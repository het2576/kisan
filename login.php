<?php
session_start();
include 'db_connect.php';

// Google Client Configuration
$clientID = '1037427370758-vu656ogqoh3jckejva39vn6ljuk5pimk.apps.googleusercontent.com';
$clientSecret = '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';

// Define allowed redirect URIs - must exactly match those configured in Google Console
$allowedRedirectUris = [
    'http://localhost:8888/kisan/login.php',
    'http://localhost/kisan/login.php'
];

// Get current URL without any query parameters
$currentUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . strtok($_SERVER["REQUEST_URI"], '?');

// Use current URL if it's in allowed list, otherwise use first allowed URI
$redirectUri = in_array($currentUrl, $allowedRedirectUris) ? $currentUrl : $allowedRedirectUris[0];

// Initialize Google Client
require_once __DIR__ . '/vendor/autoload.php';
$client = new Google\Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// Handle Google Login
if (isset($_GET['code'])) {
    try {
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
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['region'] = $user['region'];
            } else {
                // Create new user
                $sql = "INSERT INTO Users (name, email) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $google_account_info->name, $google_account_info->email);
                $stmt->execute();
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['name'] = $google_account_info->name;
            }
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Google authentication error: " . $token['error'] . "');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Authentication error: " . $e->getMessage() . "');</script>";
    }
}

// Regular Login Process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM Users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['region'] = $user['region'];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password!');</script>";
        }
    } else {
        echo "<script>alert('User not found!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kisan.ai</title>
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
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 450px;
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
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="login-container p-5 fade-in">
                    <h2 class="text-center mb-4 fw-bold">Welcome Back</h2>
                    <form action="login.php" method="POST">
                        <div class="mb-4">
                            <input type="email" name="email" class="form-control" placeholder="Email address" required>
                        </div>
                        <div class="mb-4">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mb-4">Sign In</button>
                    </form>
                    
                    <div class="divider">
                        <span>or continue with</span>
                    </div>
                    
                    <a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-google w-100 mb-4">
                        <i class="bi bi-google fs-5"></i>
                        <span>Sign in with Google</span>
                    </a>
                    
                    <p class="text-center mb-0">
                        Don't have an account? 
                        <a href="register.php" class="text-primary text-decoration-none ms-1">Sign up</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
