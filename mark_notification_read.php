<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];

// Get notification ID from POST request
$notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : null;

if ($notification_id === null) {
    exit(json_encode(['error' => 'Notification ID is required']));
}

// Mark notification as read
$stmt = $conn->prepare("
    UPDATE notifications 
    SET is_read = 1 
    WHERE id = ? AND user_id = ?
");
$stmt->bind_param("ii", $notification_id, $user_id);
$success = $stmt->execute();

if ($success) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to mark notification as read']);
}
?> 