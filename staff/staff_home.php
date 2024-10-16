<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: staff_login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle marking an order as delivered
if (isset($_POST['deliver'])) {
    $order_id = $_POST['order_id'];

    // Update the order status to Delivered
    $update_status_sql = "UPDATE orders SET status = 'Delivered' WHERE id = ?";
    $stmt = $conn->prepare($update_status_sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch orders assigned to the staff
$staff_id = $_SESSION['user_id'];
$sql = "SELECT o.id, u.full_name AS customer_name, o.total_amount, o.payment_method, o.shipping_address, o.order_date, o.status 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.assigned_staff_id = ?"; // Fetch orders assigned to this staff member
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('../uploads/adminbg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #ffffff;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            margin-top: 50px;
            background-color: rgba(31, 31, 31, 0.8); /* Semi-transparent background for container */
            padding: 20px;
            border-radius: 10px;
        }

        .table {
            background-color: #1f1f1f; /* Background color for table */
            color: #ffffff; /* Text color for table */
        }

        .table th, .table td {
            border-bottom: 1px solid #444; /* Darker border color */
        }

        .btn-success {
            background-color: #28a745;
            border: none;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Assigned Orders</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Total Amount</th>
                <th>Payment Method</th>
                <th>Shipping Address</th>
                <th>Order Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                        <td>$<?php echo htmlspecialchars($row['total_amount']); ?></td>
                        <td><?php echo htmlspecialchars($row['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($row['shipping_address']); ?></td>
                        <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <?php if ($row['status'] != 'Delivered'): ?>
                                <form method="POST" action="staff_home.php" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <button type="submit" name="deliver" class="btn btn-success btn-sm">Mark as Delivered</button>
                                </form>
                            <?php else: ?>
                                <span class="text-success">Delivered</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">No assigned orders.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='staff_dashboard.php';" class="btn btn-secondary">Back to Staff Dashboard</button>
</div>

</body>
</html>
