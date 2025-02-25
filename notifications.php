<?php
session_start();
require_once 'db_connect.php';

header('Content-Type: application/json');

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check database connection
if (!$conn) {
    exit(json_encode([
        'success' => false,
        'error' => 'Database connection failed: ' . mysqli_connect_error()
    ]));
}

if (!isset($_SESSION['user_id'])) {
    exit(json_encode(['success' => false, 'error' => 'Not logged in']));
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$lang = $_SESSION['lang'] ?? 'en';

// Only keep essential translations for UI elements
$translations = [
    'en' => [
        'no_notifications' => 'No notifications',
        'mark_all_read' => 'Mark all as read',
        'notifications' => 'Notifications'
    ],
    'hi' => [
        'no_notifications' => 'कोई सूचना नहीं',
        'mark_all_read' => 'सभी को पढ़ा हुआ मार्क करें',
        'notifications' => 'सूचनाएं'
    ],
    'gu' => [
        'no_notifications' => 'કોઈ સૂચના નથી',
        'mark_all_read' => 'બધાને વાંચેલા તરીકે માર્ક કરો',
        'notifications' => 'સૂચનાઓ'
    ]
];

try {
    switch($action) {
        case 'get_count':
            // Simplified count query
            $stmt = $conn->prepare("
                SELECT COUNT(*) as count 
                FROM notifications 
                WHERE user_id = ? 
                AND is_read = 0
                AND language = ?
            ");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("is", $user_id, $lang);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            echo json_encode(['success' => true, 'count' => $count]);
            break;

        case 'get_notifications':
            // Simplified notifications query
            $stmt = $conn->prepare("
                SELECT id, type, title, message, is_read, created_at 
                FROM notifications 
                WHERE user_id = ? 
                AND language = ?
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("is", $user_id, $lang);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            $result = $stmt->get_result();
            $notifications = [];
            
            while($row = $result->fetch_assoc()) {
                // Get icon based on notification type
                $icon = 'fa-bell';
                switch($row['type']) {
                    case 'inventory':
                        $icon = 'fa-box';
                        break;
                    case 'market':
                        $icon = 'fa-chart-line';
                        break;
                    case 'weather':
                        $icon = 'fa-cloud';
                        break;
                }
                
                $notifications[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'message' => $row['message'],
                    'type' => $row['type'],
                    'icon' => $icon,
                    'is_read' => (bool)$row['is_read'],
                    'created_at' => $row['created_at']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'notifications' => $notifications,
                'translations' => [
                    'no_notifications' => $translations[$lang]['no_notifications'],
                    'mark_all_read' => $translations[$lang]['mark_all_read'],
                    'notifications' => $translations[$lang]['notifications']
                ]
            ]);
            break;

        case 'mark_read':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $notification_id = isset($_POST['notification_id']) ? (int)$_POST['notification_id'] : 0;
            if ($notification_id <= 0) {
                throw new Exception('Invalid notification ID');
            }
            
            $stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ? AND language = ?");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("iis", $notification_id, $user_id, $lang);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            
            if ($stmt->affected_rows === 0) {
                throw new Exception('Notification not found or already read');
            }
            
            echo json_encode(['success' => true]);
            break;

        case 'mark_all_read':
            // Simplified mark all read query
            $stmt = $conn->prepare("
                UPDATE notifications 
                SET is_read = 1 
                WHERE user_id = ? 
                AND is_read = 0 
                AND language = ?
            ");
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param("is", $user_id, $lang);
            if (!$stmt->execute()) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
            echo json_encode(['success' => true, 'affected_rows' => $stmt->affected_rows]);
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log("Notification Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// Close the database connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?> 