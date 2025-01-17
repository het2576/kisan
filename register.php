<?php
session_start(); // Start the session
include 'db_connect.php'; // Include the database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $phone = $_POST['phone'];
    $region = $_POST['region'];

    // Insert user data into the database
    $sql = "INSERT INTO Users (name, email, password, phone_number, region)
            VALUES ('$name', '$email', '$password', '$phone', '$region')";

    if ($conn->query($sql) === TRUE) {
        // Fetch the newly created user ID
        $user_id = $conn->insert_id;

        // Store user data in the session
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['region'] = $region;

        // Redirect to the dashboard
        header("Location: dashboard.php");
        exit(); // Stop further execution
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6 glassmorphism p-4 fade-in">
                <h2 class="text-center mb-4">Register</h2>
                <form action="register.php" method="POST">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
                    </div>
                    <div class="mb-3">
                        <input type="text" name="region" class="form-control" placeholder="Region" required>
                    </div>
                    <button type="submit" class="btn btn-light w-100">Register</button>
                </form>
                <p class="text-center mt-3">Already have an account? <a href="login.php">Login</a></p>
            </div>
        </div>
    </div>
</body>
</html>