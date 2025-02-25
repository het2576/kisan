<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

// Get language from session, default to English if not set
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'en';

// Define translations with error handling
$translations = [
    'en' => [
        'inventory_management' => 'Inventory Management',
        'add_new_item' => 'Add New Item',
        'item_name' => 'Item Name',
        'quantity' => 'Quantity', 
        'expiration_date' => 'Expiration Date',
        'notes' => 'Notes',
        'add_item' => 'Add Item',
        'your_inventory' => 'Your Inventory',
        'expiring_soon' => 'Expiring Soon',
        'expired' => 'Expired',
        'item' => 'Item',
        'actions' => 'Actions',
        'confirm_delete' => 'Are you sure you want to delete this item?',
        'item_added' => 'Item added successfully!',
        'item_deleted' => 'Item deleted successfully!',
        'error_deleting' => 'Error deleting item: ',
        'days_remaining' => 'Days remaining: ',
        'expired_on' => 'Expired on: ',
        'edit' => 'Edit',
        'update' => 'Update',
        'cancel' => 'Cancel',
        'search' => 'Search items...',
        'sort_by' => 'Sort by',
        'filter' => 'Filter',
        'export' => 'Export to CSV',
        'print' => 'Print Inventory',
        'dashboard' => 'Dashboard'
    ],
    'hi' => [
        'inventory_management' => 'इन्वेंटरी प्रबंधन',
        'add_new_item' => 'नई वस्तु जोड़ें',
        'item_name' => 'वस्तु का नाम',
        'quantity' => 'मात्रा',
        'expiration_date' => 'समाप्ति तिथि',
        'notes' => 'नोट्स',
        'add_item' => 'वस्तु जोड़ें',
        'your_inventory' => 'आपकी इन्वेंटरी',
        'expiring_soon' => 'जल्द समाप्त होने वाला',
        'expired' => 'समाप्त हो गया',
        'item' => 'वस्तु',
        'actions' => 'कार्रवाई',
        'confirm_delete' => 'क्या आप इस वस्तु को हटाना चाहते हैं?',
        'item_added' => 'वस्तु सफलतापूर्वक जोड़ी गई!',
        'item_deleted' => 'वस्तु सफलतापूर्वक हटा दी गई!',
        'error_deleting' => 'वस्तु हटाने में त्रुटि: ',
        'days_remaining' => 'शेष दिन: ',
        'expired_on' => 'समाप्ति तिथि: ',
        'dashboard' => 'डैशबोर्ड'
    ],
    'gu' => [
        'inventory_management' => 'ઇન્વેન્ટરી મેનેજમેન્ટ',
        'add_new_item' => 'નવી વસ્તુ ઉમેરો',
        'item_name' => 'વસ્તુનું નામ',
        'quantity' => 'જથ્થો',
        'expiration_date' => 'સમાપ્તિ તારીખ',
        'notes' => 'નોંધ',
        'add_item' => 'વસ્તુ ઉમેરો',
        'your_inventory' => 'તમારી ઇન્વેન્ટરી',
        'expiring_soon' => 'જલદી સમાપ્ત થશે',
        'expired' => 'સમાપ્ત થઇ ગયેલ છે',
        'item' => 'વસ્તુ',
        'actions' => 'ક્રિયાઓ',
        'confirm_delete' => 'શું તમે આ વસ્તુ કાઢી નાખવા માંગો છો?',
        'item_added' => 'વસ્તુ સફળતાપૂર્વક ઉમેરાઈ!',
        'item_deleted' => 'વસ્તુ સફળતાપૂર્વક દૂર કરવામાં આવી!',
        'error_deleting' => 'વસ્તુ દૂર કરવામાં ભૂલ: ',
        'days_remaining' => 'બાકી રહેલા દિવસો: ',
        'expired_on' => 'સમાપ્તિ તારીખ: ',
        'dashboard' => 'ડેશબોર્ડ'
    ]
];

// Function to safely get translation
function getTranslation($lang, $key) {
    global $translations;
    if (isset($translations[$lang][$key])) {
        return $translations[$lang][$key];
    }
    // Fallback to English if translation not found
    if (isset($translations['en'][$key])) {
        return $translations['en'][$key];
    }
    // Return key if no translation found
    return $key;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $itemName = mysqli_real_escape_string($conn, $_POST['item_name']);
            $quantity = (int)$_POST['quantity'];
            $expDate = $_POST['expiration_date'];
            $notes = mysqli_real_escape_string($conn, $_POST['notes']);
            $userId = $_SESSION['user_id'];

            $sql = "INSERT INTO Inventory (user_id, item_name, quantity, expiration_date, notes) 
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isiss", $userId, $itemName, $quantity, $expDate, $notes);

            if ($stmt->execute()) {
                echo "<script>alert('" . getTranslation($lang, 'item_added') . "'); window.location.href='inventory.php';</script>";
            } else {
                echo "<script>alert('Error: " . $stmt->error . "');</script>";
            }
        } elseif ($_POST['action'] == 'delete' && isset($_POST['item_id'])) {
            $itemId = (int)$_POST['item_id'];
            $userId = $_SESSION['user_id'];
            
            $sql = "DELETE FROM Inventory WHERE item_id = ? AND user_id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param("ii", $itemId, $userId);
                if ($stmt->execute()) {
                    echo "<script>alert('" . getTranslation($lang, 'item_deleted') . "'); window.location.href='inventory.php';</script>";
                } else {
                    echo "<script>alert('" . getTranslation($lang, 'error_deleting') . $stmt->error . "');</script>";
                }
            }
        } elseif ($_POST['action'] == 'update' && isset($_POST['item_id'])) {
            $itemId = (int)$_POST['item_id'];
            $itemName = mysqli_real_escape_string($conn, $_POST['item_name']);
            $quantity = (int)$_POST['quantity'];
            $expDate = $_POST['expiration_date'];
            $notes = mysqli_real_escape_string($conn, $_POST['notes']);
            $userId = $_SESSION['user_id'];

            $sql = "UPDATE Inventory SET item_name=?, quantity=?, expiration_date=?, notes=? 
                    WHERE item_id=? AND user_id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sissii", $itemName, $quantity, $expDate, $notes, $itemId, $userId);
            
            if ($stmt->execute()) {
                echo "<script>window.location.href='inventory.php';</script>";
            } else {
                echo "<script>alert('Error updating item: " . $stmt->error . "');</script>";
            }
        }
    }
}

// Handle export functionality
if (isset($_POST['export'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="inventory.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, array('Item Name', 'Quantity', 'Expiration Date', 'Notes'));
    
    $sql = "SELECT * FROM Inventory WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, array(
            $row['item_name'],
            $row['quantity'],
            $row['expiration_date'],
            $row['notes']
        ));
    }
    fclose($output);
    exit();
}

// Handle search, sort and filter
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'expiration_date';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$sql = "SELECT * FROM Inventory WHERE user_id = ?";

if ($search) {
    $sql .= " AND (item_name LIKE ? OR notes LIKE ?)";
}

if ($filter === 'expiring') {
    $sql .= " AND expiration_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)";
} elseif ($filter === 'expired') {
    $sql .= " AND expiration_date < CURDATE()";
}

// Updated sort logic to handle multiple sort options
switch($sort) {
    case 'name':
        $sql .= " ORDER BY item_name ASC";
        break;
    case 'quantity':
        $sql .= " ORDER BY quantity DESC";
        break;
    case 'expiration_date':
        $sql .= " ORDER BY expiration_date ASC";
        break;
    default:
        $sql .= " ORDER BY expiration_date ASC";
}

$stmt = $conn->prepare($sql);

if ($search) {
    $searchParam = "%$search%";
    $stmt->bind_param("iss", $_SESSION['user_id'], $searchParam, $searchParam);
} else {
    $stmt->bind_param("i", $_SESSION['user_id']);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #2d3748;
        }
        .container {
            max-width: 1200px;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background: white;
            transition: none;
        }
        .card-header {
            background: #0d6efd;
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem 1.5rem;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .btn-primary {
            background: #0d6efd;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
        }
        .btn-danger {
            border-radius: 8px;
        }
        .table {
            margin-bottom: 0;
        }
        .table th {
            border-top: none;
            color: #4a5568;
            font-weight: 600;
        }
        .expiring-soon {
            background-color: #fff3cd;
        }
        .expired {
            background-color: #f8d7da;
        }
        .badge {
            font-size: 0.85em;
            padding: 0.5em 0.75em;
            font-weight: 500;
        }
        .expiry-info {
            font-size: 0.8em;
            color: #666;
            margin-top: 4px;
        }
        .notes-cell {
            max-width: 200px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .search-box {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .filter-controls {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .action-buttons {
            margin-bottom: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
        .edit-form {
            display: none;
        }
        .edit-form.active {
            display: block;
        }
        .item-row.editing {
            display: none;
        }
    </style>
</head>
<body>
    <!-- Back to Dashboard Button -->
    <div class="container mt-3">
        <a href="dashboard.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i><?php echo getTranslation($lang, 'dashboard'); ?>
        </a>
    </div>

    <div class="container py-5">
        <h1 class="text-center mb-5" style="color: #0d6efd;"><?php echo getTranslation($lang, 'inventory_management'); ?></h1>
        
        <!-- Search and Filter Controls -->
        <div class="row mb-4 no-print">
            <div class="col-md-6">
                <form action="" method="GET" class="search-box">
                    <input type="text" name="search" class="form-control" placeholder="<?php echo getTranslation($lang, 'search'); ?>" value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <div class="col-md-6">
                <div class="filter-controls">
                    <select name="filter" class="form-select" onchange="window.location.href='?filter='+this.value+'&sort=<?php echo $sort; ?>&search=<?php echo $search; ?>'">
                        <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Items</option>
                        <option value="expiring" <?php echo $filter === 'expiring' ? 'selected' : ''; ?>>Expiring Soon</option>
                        <option value="expired" <?php echo $filter === 'expired' ? 'selected' : ''; ?>>Expired</option>
                    </select>
                    <select name="sort" class="form-select" onchange="window.location.href='?sort='+this.value+'&filter=<?php echo $filter; ?>&search=<?php echo $search; ?>'">
                        <option value="expiration_date" <?php echo $sort === 'expiration_date' ? 'selected' : ''; ?>>Sort by Date</option>
                        <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Sort by Name</option>
                        <option value="quantity" <?php echo $sort === 'quantity' ? 'selected' : ''; ?>>Sort by Quantity</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons no-print">
            <form action="" method="POST" style="display: inline;">
                <input type="hidden" name="export" value="1">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-download"></i> <?php echo getTranslation($lang, 'export'); ?>
                </button>
            </form>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="bi bi-printer"></i> <?php echo getTranslation($lang, 'print'); ?>
            </button>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h3 class="mb-0"><?php echo getTranslation($lang, 'add_new_item'); ?></h3>
                    </div>
                    <div class="card-body">
                        <form action="inventory.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="mb-3">
                                <label class="form-label"><?php echo getTranslation($lang, 'item_name'); ?></label>
                                <input type="text" name="item_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo getTranslation($lang, 'quantity'); ?></label>
                                <input type="number" name="quantity" class="form-control" min="1" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo getTranslation($lang, 'expiration_date'); ?></label>
                                <input type="date" name="expiration_date" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><?php echo getTranslation($lang, 'notes'); ?></label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-plus-circle me-2"></i><?php echo getTranslation($lang, 'add_item'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="mb-0"><?php echo getTranslation($lang, 'your_inventory'); ?></h3>
                        <div>
                            <span class="badge bg-warning text-dark me-2"><?php echo getTranslation($lang, 'expiring_soon'); ?></span>
                            <span class="badge bg-danger"><?php echo getTranslation($lang, 'expired'); ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th><?php echo getTranslation($lang, 'item'); ?></th>
                                        <th><?php echo getTranslation($lang, 'quantity'); ?></th>
                                        <th><?php echo getTranslation($lang, 'expiration_date'); ?></th>
                                        <th><?php echo getTranslation($lang, 'notes'); ?></th>
                                        <th><?php echo getTranslation($lang, 'actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if ($result && $result->num_rows > 0):
                                        while ($row = $result->fetch_assoc()):
                                            $expDate = strtotime($row['expiration_date']);
                                            $today = strtotime('today');
                                            $diff = $expDate - $today;
                                            $daysUntilExpiry = floor($diff / (60 * 60 * 24));
                                            
                                            $rowClass = '';
                                            $expiryInfo = '';
                                            if ($daysUntilExpiry < 0) {
                                                $rowClass = 'expired';
                                                $expiryInfo = getTranslation($lang, 'expired_on') . date('d/m/Y', $expDate);
                                            } elseif ($daysUntilExpiry <= 7) {
                                                $rowClass = 'expiring-soon';
                                                $expiryInfo = getTranslation($lang, 'days_remaining') . $daysUntilExpiry;
                                            } else {
                                                $expiryInfo = getTranslation($lang, 'days_remaining') . $daysUntilExpiry;
                                            }
                                    ?>
                                    <tr class="<?php echo $rowClass; ?> item-row" id="item-<?php echo $row['item_id']; ?>">
                                        <td>
                                            <?php echo htmlspecialchars($row['item_name']); ?>
                                            <div class="expiry-info"><?php echo $expiryInfo; ?></div>
                                        </td>
                                        <td><?php echo $row['quantity']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['expiration_date'])); ?></td>
                                        <td class="notes-cell"><?php echo htmlspecialchars($row['notes'] ?? ''); ?></td>
                                        <td>
                                            <form action="inventory.php" method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('<?php echo getTranslation($lang, 'confirm_delete'); ?>')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                <button type="button" class="btn btn-primary btn-sm" onclick="toggleEdit(<?php echo $row['item_id']; ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <!-- Edit form -->
                                    <tr class="edit-form" id="edit-<?php echo $row['item_id']; ?>">
                                        <td colspan="5">
                                            <form action="inventory.php" method="POST" class="row g-3">
                                                <input type="hidden" name="action" value="update">
                                                <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                                                
                                                <div class="col-md-3">
                                                    <input type="text" name="item_name" class="form-control" value="<?php echo htmlspecialchars($row['item_name']); ?>" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <input type="number" name="quantity" class="form-control" value="<?php echo $row['quantity']; ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="date" name="expiration_date" class="form-control" value="<?php echo $row['expiration_date']; ?>" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <textarea name="notes" class="form-control"><?php echo htmlspecialchars($row['notes'] ?? ''); ?></textarea>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-success btn-sm"><?php echo getTranslation($lang, 'update'); ?></button>
                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEdit(<?php echo $row['item_id']; ?>)"><?php echo getTranslation($lang, 'cancel'); ?></button>
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php 
                                        endwhile;
                                    endif;
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function toggleEdit(itemId) {
        const itemRow = document.getElementById(`item-${itemId}`);
        const editForm = document.getElementById(`edit-${itemId}`);
        
        if (editForm.classList.contains('active')) {
            itemRow.classList.remove('editing');
            editForm.classList.remove('active');
        } else {
            itemRow.classList.add('editing');
            editForm.classList.add('active');
        }
    }
    </script>
</body>
</html>
