<?php
// profile.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'db_connect.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get language from session
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$farmer = $stmt->get_result()->fetch_assoc();

if (!$farmer) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $region = trim($_POST['region']);
    $farm_size = trim($_POST['farm_size']);
    $main_crops = trim($_POST['main_crops']);
    $farming_type = trim($_POST['farming_type']);
    $soil_type = trim($_POST['soil_type']);
    $irrigation = trim($_POST['irrigation']);

    try {
        // Update user profile
        $sql = "UPDATE users SET 
                name = ?, 
                phone_number = ?, 
                region = ?, 
                farm_size = ?, 
                main_crops = ?, 
                farming_type = ?, 
                soil_type = ?, 
                irrigation = ? 
                WHERE user_id = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssi", 
            $name, 
            $phone, 
            $region, 
            $farm_size, 
            $main_crops, 
            $farming_type, 
            $soil_type, 
            $irrigation, 
            $_SESSION['user_id']
        );

        if ($stmt->execute()) {
            // Update session variables
            $_SESSION['name'] = $name;
            $_SESSION['phone'] = $phone;
            $_SESSION['region'] = $region;
            $_SESSION['farm_size'] = $farm_size;
            $_SESSION['main_crops'] = $main_crops;
            $_SESSION['farming_type'] = $farming_type;
            $_SESSION['soil_type'] = $soil_type;
            $_SESSION['irrigation'] = $irrigation;

            // Debug session data
            error_log("Session data after update: " . print_r($_SESSION, true));
            
            $success_message = "Profile updated successfully!";
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $farmer = $stmt->get_result()->fetch_assoc();
        } else {
            throw new Exception($stmt->error);
        }
    } catch (Exception $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
        error_log("Profile update error: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Profile - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            padding: 15px;
        }

        .profile-container {
            max-width: 800px;
            margin: 15px auto;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section-title {
            color: #2d3748;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
        }

        .form-label {
            font-weight: 500;
            color: #4a5568;
            font-size: 0.95rem;
            margin-bottom: 8px;
            display: block;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            padding: 12px 15px;
            font-size: 0.95rem;
            width: 100%;
            margin-bottom: 20px;
            color: #2d3748;
        }

        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        .form-section {
            margin-bottom: 30px;
        }

        .btn-update {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-update:hover {
            background: #43A047;
            transform: translateY(-2px);
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: white;
            border: 2px solid #4CAF50;
            color: #4CAF50;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-button i {
            margin-right: 8px;
        }

        .back-button:hover {
            background: #4CAF50;
            color: white;
        }

        @media (max-width: 768px) {
            .profile-container {
                padding: 20px;
                margin: 10px;
            }

            .section-title {
                font-size: 1.2rem;
                margin-bottom: 20px;
            }

            .form-label {
                font-size: 0.9rem;
            }

            .form-control {
                font-size: 0.9rem;
                padding: 10px 12px;
            }

            .col-md-6 {
                margin-bottom: 15px;
            }
        }

        @media (max-width: 576px) {
            body {
                padding: 10px;
            }

            .profile-container {
                padding: 15px;
            }

            .back-button {
                padding: 8px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-button">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>

        <div class="profile-container">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <h2 class="section-title">Personal Information</h2>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($farmer['name']); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($farmer['phone_number'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Region</label>
                        <input type="text" class="form-control" name="region" value="<?php echo htmlspecialchars($farmer['region'] ?? ''); ?>">
                    </div>
                </div>

                <h2 class="section-title">Farming Information</h2>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Farm Size (Acres)</label>
                        <input type="text" class="form-control" name="farm_size" value="<?php echo htmlspecialchars($farmer['farm_size'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Main Crops</label>
                        <input type="text" class="form-control" name="main_crops" value="<?php echo htmlspecialchars($farmer['main_crops'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Farming Type</label>
                        <select class="form-control" name="farming_type">
                            <option value="">Select Farming Type</option>
                            <option value="organic" <?php echo ($farmer['farming_type'] ?? '') === 'organic' ? 'selected' : ''; ?>>Organic</option>
                            <option value="traditional" <?php echo ($farmer['farming_type'] ?? '') === 'traditional' ? 'selected' : ''; ?>>Traditional</option>
                            <option value="mixed" <?php echo ($farmer['farming_type'] ?? '') === 'mixed' ? 'selected' : ''; ?>>Mixed</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Soil Type</label>
                        <input type="text" class="form-control" name="soil_type" value="<?php echo htmlspecialchars($farmer['soil_type'] ?? ''); ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Irrigation Method</label>
                        <input type="text" class="form-control" name="irrigation" value="<?php echo htmlspecialchars($farmer['irrigation'] ?? ''); ?>">
                    </div>
                </div>

                <button type="submit" class="btn btn-update">
                    Update Profile
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
