<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kisan.ai - Revolutionizing Farm Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #4CAF50;
            --light-green: #8BC34A;
            --pale-green: #E8F5E9;
            --dark-green: #2E7D32;
            --white: #ffffff;
        }
        
        body {
            background: linear-gradient(135deg, var(--pale-green), var(--white));
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-content {
            text-align: center;
            padding: 4rem 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .logo-text {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, var(--primary-green), var(--dark-green));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2rem;
            letter-spacing: -1px;
        }

        .tagline {
            font-size: 1.8rem;
            font-weight: 300;
            color: var(--dark-green);
            margin-bottom: 3rem;
            line-height: 1.4;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }

        .highlight-text {
            font-size: 2.2rem;
            font-weight: 600;
            margin: 3rem 0;
            color: var(--primary-green);
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .feature-text {
            font-size: 1.4rem;
            color: #444;
            margin: 1.5rem 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
        }

        .feature-text i {
            color: var(--primary-green);
            font-size: 1.8rem;
        }

        .start-button {
            display: inline-block;
            padding: 1.2rem 3.5rem;
            font-size: 1.4rem;
            font-weight: 600;
            text-transform: uppercase;
            background: linear-gradient(45deg, var(--primary-green), var(--light-green));
            color: var(--white);
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-top: 3rem;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        }

        .start-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
            color: var(--white);
        }

        @media (max-width: 768px) {
            .logo-text {
                font-size: 2.5rem;
            }
            .tagline {
                font-size: 1.4rem;
            }
            .highlight-text {
                font-size: 1.8rem;
            }
            .feature-text {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
    <div class="hero-content">
        <h1 class="logo-text">Kisan.ai</h1>
        <p class="tagline">Empowering farmers with cutting-edge technology for smarter agriculture</p>
        
        <h2 class="highlight-text">Transforming Farming</h2>
        
        <div class="feature-text">
            <i class="fas fa-robot"></i>
            AI-Powered Crop Recommendations
        </div>
        <div class="feature-text">
            <i class="fas fa-chart-line"></i>
            Real-time Market Insights
        </div>
        <div class="feature-text">
            <i class="fas fa-box"></i>
            Inventory Tracking System
        </div>
        <div class="feature-text">
            <i class="fas fa-store"></i>
            Farmer's Marketplace
        </div>

        <a href="register.php" class="start-button">Begin Your Journey</a>
    </div>
</body>
</html>
