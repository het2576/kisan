<?php
class NotificationService {
    public static function notifyFollowers($auction_id, $type) {
        // Basic implementation
        try {
            global $conn;
            $stmt = $conn->prepare("
                INSERT INTO notifications (user_id, type, reference_id, message)
                SELECT user_id, ?, ?, 'New auction has been created'
                FROM user_follows
                WHERE followed_id = (SELECT seller_id FROM auctions WHERE auction_id = ?)
            ");
            $stmt->bind_param('sii', $type, $auction_id, $auction_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Notification error: " . $e->getMessage());
            return false;
        }
    }
} 