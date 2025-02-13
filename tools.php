<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_tool'])) {
        $toolName = $_POST['tool_name'];
        $userId = $_SESSION['user_id'];

        $sql = "INSERT INTO Tools (user_id, tool_name) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $userId, $toolName);
        
        if ($stmt->execute()) {
            echo "<script>alert('Tool added successfully!');</script>";
        } else {
            echo "<script>alert('Error: " . $conn->error . "');</script>";
        }
    } else if (isset($_POST['update_status'])) {
        $toolId = $_POST['tool_id'];
        $newStatus = $_POST['status'];
        
        $sql = "UPDATE Tools SET status = ? WHERE tool_id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $newStatus, $toolId, $_SESSION['user_id']);
        $stmt->execute();
    }
}

$sql = "SELECT * FROM Tools WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

// Define translations
$translations = [
    'en' => [
        'automated_tool_crafting' => 'Automated Tool Crafting',
        'add_new_tool' => 'Add New Tool',
        'enter_tool_name' => 'Enter tool name',
        'add_tool' => 'Add Tool',
        'your_tools' => 'Your Tools',
        'tool_name' => 'Tool Name',
        'status' => 'Status',
        'actions' => 'Actions',
        'no_tools' => 'No tools added yet. Add your first tool!',
        'toggle_status' => 'Toggle Status',
        'dashboard' => 'Dashboard'
    ],
    'gu' => [
        'automated_tool_crafting' => 'સ્વચાલિત સાધન ક્રાફ્ટિંગ',
        'add_new_tool' => 'નવું સાધન ઉમેરો',
        'enter_tool_name' => 'સાધનનું નામ દાખલ કરો',
        'add_tool' => 'સાધન ઉમેરો',
        'your_tools' => 'તમારા સાધનો',
        'tool_name' => 'સાધનનું નામ',
        'status' => 'સ્થિતિ',
        'actions' => 'ક્રિયાઓ',
        'no_tools' => 'હજુ સુધી કોઈ સાધનો ઉમેર્યા નથી. તમારું પ્રથમ સાધન ઉમેરો!',
        'toggle_status' => 'સ્થિતિ બદલો',
        'dashboard' => 'ડેશબોર્ડ'
    ]
];

$lang = $_SESSION['lang'] ?? 'en';
$t = $translations[$lang];
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: linear-gradient(135deg, #ffffff 0%, #e6f3ff 100%);
            color: #0d6efd;
            line-height: 1.6;
        }
        .glassmorphism {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid #0d6efd;
        }
        .btn-custom {
            background: #0d6efd;
            color: white;
            border: none;
            transition: transform 0.2s;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            color: white;
            background: #0b5ed7;
        }
        .table {
            color: #0d6efd;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.9em;
        }
        .status-available {
            background-color: #e7f5ff;
            color: #0d6efd;
        }
        .status-in_use {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Back to Dashboard Button -->
    <div class="container mt-3">
        <a href="dashboard.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i><?php echo $t['dashboard']; ?>
        </a>
    </div>

    <div class="container py-5">
        <h1 class="text-center mb-4 text-primary">
            <i class="bi bi-tools"></i> 
            <?php echo $t['automated_tool_crafting']; ?>
        </h1>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="glassmorphism p-4 fade-in h-100">
                    <h3 class="mb-4 text-primary"><i class="bi bi-plus-circle"></i> <?php echo $t['add_new_tool']; ?></h3>
                    <form action="tools.php" method="POST">
                        <div class="mb-3">
                            <input type="text" name="tool_name" class="form-control form-control-lg" 
                                   placeholder="<?php echo $t['enter_tool_name']; ?>" required>
                        </div>
                        <button type="submit" name="add_tool" class="btn btn-custom btn-lg w-100">
                            <i class="bi bi-plus-lg"></i> <?php echo $t['add_tool']; ?>
                        </button>
                    </form>
                </div>
            </div>
            <div class="col-md-6">
                <div class="glassmorphism p-4 fade-in h-100">
                    <h3 class="mb-4 text-primary"><i class="bi bi-list-check"></i> <?php echo $t['your_tools']; ?></h3>
                    <?php if ($result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th><?php echo $t['tool_name']; ?></th>
                                    <th><?php echo $t['status']; ?></th>
                                    <th><?php echo $t['actions']; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <i class="bi bi-wrench"></i>
                                        <?php echo htmlspecialchars($row['tool_name']); ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $row['status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <form action="tools.php" method="POST" class="d-inline">
                                            <input type="hidden" name="tool_id" value="<?php echo $row['tool_id']; ?>">
                                            <input type="hidden" name="status" 
                                                   value="<?php echo $row['status'] === 'available' ? 'in_use' : 'available'; ?>">
                                            <button type="submit" name="update_status" class="btn btn-sm btn-custom">
                                                <?php echo $t['toggle_status']; ?>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center p-4">
                        <i class="bi bi-emoji-neutral fs-1"></i>
                        <p class="mt-3"><?php echo $t['no_tools']; ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
