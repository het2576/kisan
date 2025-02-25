<?php
session_start();
require_once 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['error' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];

// Get unread notification count
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$unread_count = $result->fetch_assoc()['count'];

// Get latest notifications
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;

$stmt = $conn->prepare("
    SELECT id, type, title, message, created_at, is_read 
    FROM notifications 
    WHERE user_id = ? 
    ORDER BY created_at DESC 
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $row['created_at'] = date('Y-m-d H:i:s', strtotime($row['created_at']));
    $notifications[] = $row;
}

echo json_encode([
    'unread_count' => $unread_count,
    'notifications' => $notifications
]);
?> 