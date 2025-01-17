<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $itemName = $_POST['item_name'];
    $quantity = $_POST['quantity'];
    $expDate = $_POST['expiration_date'];
    $userId = $_SESSION['user_id'];

    $sql = "INSERT INTO Inventory (user_id, item_name, quantity, expiration_date)
            VALUES ('$userId', '$itemName', '$quantity', '$expDate')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Item added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}

$sql = "SELECT * FROM Inventory WHERE user_id = '{$_SESSION['user_id']}'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - Kisan.ai</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-4">Inventory Management</h1>
        <div class="row">
            <div class="col-md-6 glassmorphism p-4 fade-in">
                <h3>Add New Item</h3>
                <form action="inventory.php" method="POST">
                    <div class="mb-3">
                        <input type="text" name="item_name" class="form-control" placeholder="Item Name" required>
                    </div>
                    <div class="mb-3">
                        <input type="number" name="quantity" class="form-control" placeholder="Quantity" required>
                    </div>
                    <div class="mb-3">
                        <input type="date" name="expiration_date" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-light w-100">Add Item</button>
                </form>
            </div>
            <div class="col-md-6 glassmorphism p-4 fade-in">
                <h3>Your Inventory</h3>
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Expiration</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['item_name']; ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td><?php echo $row['expiration_date']; ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>