<?php
session_start();
include 'db_connect.php';

// Google Client Configuration
$clientID = '1037427370758-vu656ogqoh3jckejva39vn6ljuk5pimk.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost:8888/kisan/register.php';

// Initialize Google Client
require_once __DIR__ . '/vendor/autoload.php';
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Handle Google Registration
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);
        $google_oauth = new Google\Service\Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Check if user exists
        $sql = "SELECT * FROM Users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $google_account_info->email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // User exists - log them in directly
            $user = $result->fetch_assoc();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['region'] = $user['region'];
            header("Location: dashboard.php");
            exit();
        } else {
            // Create new user
            $sql = "INSERT INTO Users (name, email) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $google_account_info->name, $google_account_info->email);
            $stmt->execute();
            $_SESSION['user_id'] = $conn->insert_id;
            $_SESSION['name'] = $google_account_info->name;
            $_SESSION['email'] = $google_account_info->email;
            header("Location: dashboard.php");
            exit();
        }
    }
}

// Regular Registration Process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $region = mysqli_real_escape_string($conn, $_POST['region']);

    $sql = "INSERT INTO Users (name, email, password, phone_number, region) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $name, $email, $password, $phone, $region);
    
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['region'] = $region;
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #2d3748;
            line-height: 1.6;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 500px;
            margin: auto;
        }
        .btn-primary {
            background: linear-gradient(45deg, #0d6efd, #0099ff);
            border: none;
            padding: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 1.1rem;
            text-transform: uppercase;
        }
        .btn-google {
            background-color: #fff;
            color: #333;
            border: 2px solid #e2e8f0;
            padding: 14px;
            font-weight: 600;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .btn-google:hover {
            background-color: #f8f9fa;
        }
        .form-control {
            padding: 14px;
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
            border-color: #0d6efd;
        }
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0;
        }
        .divider::before, .divider::after {
            content: '';
            flex: 1;
            border-bottom: 2px solid #e2e8f0;
        }
        .divider span {
            padding: 0 16px;
            color: #4a5568;
            font-size: 1rem;
            font-weight: 500;
        }
        h2 {
            font-size: 2.25rem;
            color: #1a202c;
            letter-spacing: -0.5px;
        }
        .text-primary {
            color: #0d6efd !important;
            font-weight: 600;
        }
        p {
            font-size: 1rem;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="register-container p-5 fade-in">
            <h2 class="text-center mb-4 fw-bold">Create Account</h2>
            
            <a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-google w-100 mb-4">
                <img src="https://www.google.com/favicon.ico" alt="Google" width="20">
                Continue with Google
            </a>
            
            <div class="divider">
                <span>or register with email</span>
            </div>

            <form action="register.php" method="POST">
                <div class="mb-3">
                    <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <input type="email" name="email" class="form-control" placeholder="Email address" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
                <div class="mb-3">
                    <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
                </div>
                <div class="mb-4">
                    <input type="text" name="region" class="form-control" placeholder="Region" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-4">Create Account</button>
            </form>
            
            <p class="text-center mb-0">Already have an account? <a href="login.php" class="text-primary">Sign In</a></p>
        </div>
    </div>
</body>
</html>
