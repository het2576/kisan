<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $toolName = $_POST['tool_name'];
    $userId = $_SESSION['user_id'];

    $sql = "INSERT INTO Tools (user_id, tool_name) VALUES ('$userId', '$toolName')";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Tool added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$sql = "SELECT * FROM Tools WHERE user_id = '{$_SESSION['user_id']}'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tools - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Automated Tool Crafting</h1>
        <div class="row">
            <div class="col-md-6 glassmorphism p-4 fade-in">
                <h3>Add New Tool</h3>
                <form action="tools.php" method="POST">
                    <div class="mb-3">
                        <input type="text" name="tool_name" class="form-control" placeholder="Tool Name" required>
                    </div>
                    <button type="submit" class="btn btn-light w-100">Add Tool</button>
                </form>
            </div>
            <div class="col-md-6 glassmorphism p-4 fade-in">
                <h3>Your Tools</h3>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tool</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['tool_name']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>