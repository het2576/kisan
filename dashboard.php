<?php
session_start(); // Start the session

// Redirect to login if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Glassmorphism Effect */
        .glassmorphism {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Custom Styles */
        body {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            color: #fff;
            font-family: 'Poppins', sans-serif;
        }

        .card {
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <!-- Welcome Message -->
        <h1 class="text-center mb-4 fade-in">Welcome, <?php echo $_SESSION['name']; ?>!</h1>

        <!-- Dashboard Cards -->
        <div class="row fade-in">
            <!-- Inventory Card -->
            <div class="col-md-4 mb-4">
                <div class="card glassmorphism p-4 text-center">
                    <h3>📦 Inventory</h3>
                    <p>Manage your farm inventory efficiently.</p>
                    <a href="inventory.php" class="btn btn-light">Manage</a>
                </div>
            </div>

            <!-- Market Insights Card -->
            <div class="col-md-4 mb-4">
                <div class="card glassmorphism p-4 text-center">
                    <h3>📈 Market Insights</h3>
                    <p>Get real-time market prices and trends.</p>
                    <a href="market.php" class="btn btn-light">View</a>
                </div>
            </div>

            <!-- Weather Card -->
            <div class="col-md-4 mb-4">
                <div class="card glassmorphism p-4 text-center">
                    <h3>🌦️ Weather</h3>
                    <p>Check weather forecasts for better planning.</p>
                    <a href="weather.php" class="btn btn-light">Check</a>
                </div>
            </div>
        </div>

        <!-- Second Row of Cards -->
        <div class="row fade-in">
            <!-- Tools Card -->
            <div class="col-md-4 mb-4">
                <div class="card glassmorphism p-4 text-center">
                    <h3>🛠️ Tools</h3>
                    <p>Manage and craft farm tools.</p>
                    <a href="tools.php" class="btn btn-light">Manage</a>
                </div>
            </div>

            <!-- AI Farming Assistant Card -->
            <div class="col-md-4 mb-4">
                <div class="card glassmorphism p-4 text-center">
                    <h3>🤖 AI Assistant</h3>
                    <p>Get farming recommendations from AI.</p>
                    <a href="ai_assistant.php" class="btn btn-light">Ask AI</a>
                </div>
            </div>

            <!-- Logout Card -->
            <div class="col-md-4 mb-4">
                <div class="card glassmorphism p-4 text-center">
                    <h3>🚪 Logout</h3>
                    <p>Log out from your account.</p>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>