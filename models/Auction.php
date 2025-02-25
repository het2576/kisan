<?php
require_once __DIR__ . '/../includes/init.php';

class Auction {
    private $conn;
    private $table = 'auctions';

    // Properties matching your database
    public $auction_id;
    public $seller_id;
    public $title;
    public $description;
    public $image_url;
    public $starting_price;
    public $min_increment;
    public $start_time;
    public $end_time;
    public $status;
    public $created_at;
    public $category_id;

    public function __construct() {
        global $conn;
        if (!$conn) {
            throw new Exception("Database connection not available");
        }
        $this->conn = $conn;
    }

    // Create new auction
    public function create($data) {
        try {
            // Validate required fields
            $required_fields = ['title', 'description', 'starting_bid', 'current_bid', 
                              'min_increment', 'end_time', 'image_url', 'seller_name'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field])) {
                    error_log("Missing required field: $field");
                    return false;
                }
            }

            // Make sure seller_id exists in session
            if (!isset($_SESSION['user']['id'])) {
                error_log("No seller_id found in session");
                return false;
            }

            $query = "INSERT INTO " . $this->table . " 
                (seller_id, title, description, starting_bid, current_bid, 
                 min_increment, end_time, image_url, seller_name, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')";

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                return false;
            }

            $seller_id = $_SESSION['user']['id'];
            $stmt->bind_param("issdddss",
                $seller_id,
                $data['title'],
                $data['description'],
                $data['starting_bid'],
                $data['current_bid'],
                $data['min_increment'],
                $data['end_time'],
                $data['image_url'],
                $data['seller_name']
            );

            $result = $stmt->execute();
            if (!$result) {
                error_log("Execute failed: " . $stmt->error);
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Auction Creation Error: " . $e->getMessage());
            return false;
        }
    }

    // Get all active auctions
    public function getActive() {
        $query = "SELECT a.*, u.name as seller_name, c.name as category_name,
                    COALESCE((SELECT MAX(amount) FROM bids WHERE auction_id = a.auction_id), a.starting_price) as current_bid
                 FROM " . $this->table . " a
                 LEFT JOIN users u ON a.seller_id = u.user_id
                 LEFT JOIN auction_categories c ON a.category_id = c.id
                 WHERE a.status = 'active'
                 AND a.end_time > NOW()
                 ORDER BY a.created_at DESC";

        try {
            $result = $this->conn->query($query);
            if (!$result) {
                throw new Exception("Query failed: " . $this->conn->error);
            }
            return $result;
        } catch (Exception $e) {
            error_log("Error getting active auctions: " . $e->getMessage());
            echo "<!-- Debug: " . $e->getMessage() . " -->";
            return false;
        }
    }

    // Get auction by ID
    public function getById($auction_id) {
        $query = "SELECT a.*, u.name as seller_name,
                    COALESCE((SELECT MAX(amount) FROM bids WHERE auction_id = a.auction_id), a.starting_price) as current_bid
                 FROM " . $this->table . " a
                 LEFT JOIN users u ON a.seller_id = u.user_id
                 WHERE a.auction_id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("i", $auction_id);
            $stmt->execute();
            return $stmt->get_result();
        } catch (Exception $e) {
            error_log("Error getting auction: " . $e->getMessage());
            return false;
        }
    }

    // Update auction status
    public function updateStatus($status) {
        $query = "UPDATE " . $this->table . "
                 SET status = ?
                 WHERE auction_id = ?";

        try {
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("si", $status, $this->auction_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating auction status: " . $e->getMessage());
            return false;
        }
    }

    // Get active auctions with filters
    public function getActiveWithFilters($category = '', $sort = 'ending_soon', $search = '') {
        $query = "SELECT a.*, u.name as seller_name, c.name as category_name,
                    COALESCE((SELECT MAX(amount) FROM bids WHERE auction_id = a.auction_id), a.starting_price) as current_bid
                 FROM " . $this->table . " a
                 LEFT JOIN users u ON a.seller_id = u.user_id
                 LEFT JOIN auction_categories c ON a.category_id = c.id
                 WHERE a.status = 'active'
                 AND a.end_time > NOW()";

        // Add category filter
        if ($category) {
            $query .= " AND a.category_id = " . intval($category);
        }

        // Add search filter
        if ($search) {
            $search = $this->conn->real_escape_string($search);
            $query .= " AND (a.title LIKE '%$search%' OR a.description LIKE '%$search%')";
        }

        // Add sorting
        switch ($sort) {
            case 'price_low':
                $query .= " ORDER BY current_bid ASC";
                break;
            case 'price_high':
                $query .= " ORDER BY current_bid DESC";
                break;
            case 'newest':
                $query .= " ORDER BY a.created_at DESC";
                break;
            default: // ending_soon
                $query .= " ORDER BY a.end_time ASC";
        }

        try {
            return $this->conn->query($query);
        } catch (Exception $e) {
            error_log("Error getting filtered auctions: " . $e->getMessage());
            return false;
        }
    }

    // Get auction statistics
    public function getActiveCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE status = 'active' AND end_time > NOW()";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    public function getTotalBids() {
        $query = "SELECT COUNT(*) as count FROM bids";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    public function getSuccessfulAuctions() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                 WHERE status = 'completed'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    // Get categories
    public function getCategories() {
        try {
            // First verify the table exists
            $tableCheck = $this->conn->query("
                SELECT 1 
                FROM information_schema.tables 
                WHERE table_schema = '" . DB_NAME . "' 
                AND table_name = 'auction_categories'
            ");

            if ($tableCheck->num_rows === 0) {
                // Table doesn't exist, try to create it
                $setupUrl = "../database/direct_setup.php";
                echo "Auction tables not found. Please <a href='$setupUrl' target='_blank'>set up the database</a> first.";
                return [];
            }

            // Now try to get categories
            $query = "SELECT * FROM auction_categories ORDER BY name";
            $result = $this->conn->query($query);
            
            if (!$result) {
                throw new Exception("Error fetching categories: " . $this->conn->error);
            }
            
            return $result->fetch_all(MYSQLI_ASSOC);
        } catch (Exception $e) {
            error_log("Error in getCategories: " . $e->getMessage());
            return [];
        }
    }

    public function placeBid($auction_id, $bidder_id, $bidder_name, $bid_amount) {
        $stmt = $this->conn->prepare("
            UPDATE auctions 
            SET current_bid = ?, 
                current_bidder_id = ?,
                current_bidder_name = ?
            WHERE auction_id = ? 
            AND current_bid < ?
            AND end_time > NOW()
        ");
        
        $stmt->bind_param("disid", $bid_amount, $bidder_id, $bidder_name, $auction_id, $bid_amount);
        return $stmt->execute();
    }

    public function getLastError() {
        return $this->conn->error;
    }

    public function saveAuctionMetadata($auction_id, $metadata) {
        try {
            $stmt = $this->conn->prepare("
                INSERT INTO auction_metadata 
                (auction_id, shipping_info, payment_terms, quality_grade, 
                 certification, harvest_date, storage_conditions)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param("issssss",
                $auction_id,
                $metadata['shipping_info'],
                $metadata['payment_terms'],
                $metadata['quality_grade'],
                $metadata['certification'],
                $metadata['harvest_date'],
                $metadata['storage_conditions']
            );
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error saving auction metadata: " . $e->getMessage());
            return false;
        }
    }
} 