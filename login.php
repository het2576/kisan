<?php
session_start();

// Include database connection
require_once 'db_connect.php';

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
                $_SESSION['phone'] = $user['phone_number'];
                $_SESSION['farm_size'] = $user['farm_size'];
                $_SESSION['main_crops'] = $user['main_crops'];
                $_SESSION['farming_type'] = $user['farming_type'];
                $_SESSION['soil_type'] = $user['soil_type'];
                $_SESSION['irrigation'] = $user['irrigation'];
            } else {
                // Create new user
                $sql = "INSERT INTO Users (name, email) VALUES (?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $google_account_info->name, $google_account_info->email);
                $stmt->execute();
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['name'] = $google_account_info->name;
                $_SESSION['phone'] = $google_account_info->phoneNumber;
                $_SESSION['region'] = $google_account_info->region;
                $_SESSION['farm_size'] = $google_account_info->farmSize;
                $_SESSION['main_crops'] = $google_account_info->mainCrops;
                $_SESSION['farming_type'] = $google_account_info->farmingType;
                $_SESSION['soil_type'] = $google_account_info->soilType;
                $_SESSION['irrigation'] = $google_account_info->irrigation;
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
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    // Sanitize inputs
    $email = $conn->real_escape_string($email);
    
    // Query to check user credentials - removed 'role' from SELECT
    $sql = "SELECT user_id, name, email, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set all session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['phone'] = $user['phone_number'];
            $_SESSION['region'] = $user['region'];
            $_SESSION['farm_size'] = $user['farm_size'];
            $_SESSION['main_crops'] = $user['main_crops'];
            $_SESSION['farming_type'] = $user['farming_type'];
            $_SESSION['soil_type'] = $user['soil_type'];
            $_SESSION['irrigation'] = $user['irrigation'];
            
            // Debug session data
            error_log("Session data after login: " . print_r($_SESSION, true));
            
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Invalid password";
        }
    } else {
        $error = "User not found";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            max-width: 450px;
            width: 100%;
            padding: 2.5rem;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-header h1 {
            font-size: 2.25rem;
            color: #1a202c;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .login-header p {
            color: #4a5568;
            opacity: 0.8;
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

        .btn-login {
            background: linear-gradient(45deg, #0d6efd, #0099ff);
            border: none;
            padding: 14px;
            font-weight: 600;
            letter-spacing: 0.5px;
            font-size: 1.1rem;
            text-transform: uppercase;
            width: 100%;
            color: white;
            border-radius: 12px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
        }

        .footer-links {
            text-align: center;
            margin-top: 1.5rem;
        }

        .footer-links a {
            color: #0d6efd;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #dc2626;
        }

        .input-group-text {
            background: transparent;
            border: 2px solid #e2e8f0;
            border-right: none;
            color: #4a5568;
        }

        .input-group .form-control {
            border-left: none;
            margin-bottom: 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #0d6efd;
        }

        @media (max-width: 768px) {
            .login-container {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Back</h1>
            <p>Login to your account</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" class="form-control" name="email" required 
                       placeholder="Enter your email">
            </div>
            
            <div class="input-group mb-4">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" class="form-control" name="password" required 
                       placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>

        <div class="footer-links">
            <p class="mb-0">Don't have an account? 
                <a href="register.php">Create Account</a>
            </p>
        </div>
    </div>
</body>
</html>
