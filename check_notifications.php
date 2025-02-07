<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    exit(json_encode(['success' => false, 'error' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];

try {
    // First, check if there are any notifications
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'];

    // If no notifications exist, add some test notifications
    if ($count == 0) {
        $notifications = [
            [
                'type' => 'inventory',
                'title' => 'Low Stock Alert',
                'message' => 'Your wheat seeds inventory is running low. Consider restocking soon.'
            ],
            [
                'type' => 'market',
                'title' => 'Price Update',
                'message' => 'Cotton prices have increased by 5% in your region.'
            ],
            [
                'type' => 'weather',
                'title' => 'Weather Alert',
                'message' => 'Heavy rainfall expected in your region tomorrow. Plan your activities accordingly.'
            ]
        ];

        foreach ($notifications as $notification) {
            $stmt = $conn->prepare("INSERT INTO notifications (user_id, type, title, message, is_read, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("isss", $user_id, $notification['type'], $notification['title'], $notification['message']);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        }
    }

    // Check for inventory expiry
    $stmt = $conn->prepare("
        SELECT * FROM Inventory 
        WHERE user_id = ? 
        AND expiration_date <= DATE_ADD(CURRENT_DATE, INTERVAL 30 DAY) 
        AND expiration_date > CURRENT_DATE
        AND item_id NOT IN (
            SELECT reference_id 
            FROM notifications 
            WHERE user_id = ? 
            AND type = 'inventory' 
            AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
        )
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ii", $user_id, $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();

    while ($item = $result->fetch_assoc()) {
        $days_left = floor((strtotime($item['expiration_date']) - time()) / (60 * 60 * 24));
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, type, title, message, reference_id) 
            VALUES (?, 'inventory', ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $title = "Inventory Expiry Alert";
        $message = "{$item['item_name']} will expire in {$days_left} days";
        $stmt->bind_param("issi", $user_id, $title, $message, $item['item_id']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    }

    // Check for market price updates
    $stmt = $conn->prepare("
        SELECT * FROM MarketPrices 
        WHERE date >= DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)
        AND price_id NOT IN (
            SELECT reference_id 
            FROM notifications 
            WHERE user_id = ? 
            AND type = 'market' 
            AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
        )
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();

    while ($price = $result->fetch_assoc()) {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, type, title, message, reference_id) 
            VALUES (?, 'market', ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $title = "Market Price Update";
        $message = "Price update for {$price['crop_name']}: ₹{$price['price']}/kg";
        $stmt->bind_param("issi", $user_id, $title, $message, $price['price_id']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    }

    // Check for weather alerts
    $stmt = $conn->prepare("
        SELECT * FROM WeatherData 
        WHERE forecast_date = CURRENT_DATE
        AND weather_id NOT IN (
            SELECT reference_id 
            FROM notifications 
            WHERE user_id = ? 
            AND type = 'weather' 
            AND created_at >= DATE_SUB(CURRENT_DATE, INTERVAL 1 DAY)
        )
    ");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();

    while ($weather = $result->fetch_assoc()) {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, type, title, message, reference_id) 
            VALUES (?, 'weather', ?, ?, ?)
        ");
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $title = "Weather Alert";
        $message = "Temperature: {$weather['temperature']}°C, Rainfall: {$weather['rainfall']}mm expected today";
        $stmt->bind_param("issi", $user_id, $title, $message, $weather['weather_id']);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
    }

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Check Notifications Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // Close the database connection
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?> 