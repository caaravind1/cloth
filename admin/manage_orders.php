<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "cloth");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle staff assignment
if (isset($_POST['assign'])) {
    $order_id = $_POST['order_id'];
    $staff_id = $_POST['staff_id'];

    // Update the assigned staff for the order
    $update_sql = "UPDATE orders SET assigned_staff_id = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ii", $staff_id, $order_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch total stats
$total_orders_query = "SELECT COUNT(*) AS total_orders FROM orders";
$total_orders = $conn->query($total_orders_query)->fetch_assoc()['total_orders'];

$total_products_query = "SELECT COUNT(*) AS total_products FROM products";
$total_products = $conn->query($total_products_query)->fetch_assoc()['total_products'];

$total_customers_query = "SELECT COUNT(*) AS total_customers FROM users WHERE role_id = 1";
$total_customers = $conn->query($total_customers_query)->fetch_assoc()['total_customers'];

// Fetch all orders
$sql = "SELECT o.id, u.full_name AS customer_name, o.total_amount, o.payment_method, o.shipping_address, o.order_date, o.status, s.full_name AS staff_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        LEFT JOIN users s ON o.assigned_staff_id = s.id"; // Fetch staff names if assigned
$result = $conn->query($sql);

// Fetch staff members for assignment
$staff_sql = "SELECT id, full_name FROM users WHERE role_id = 3"; // Assuming staff role ID is 3
$staff_result = $conn->query($staff_sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
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

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Manage Orders</h2>
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
                <th>Assigned Staff</th>
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
                        <td><?php echo htmlspecialchars($row['staff_name']); ?></td>
                        <td>
                            <?php if ($row['status'] != 'Delivered' && $row['staff_name'] === null): ?>
                                <form method="POST" action="manage_orders.php" style="display: inline;">
                                    <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                    <select name="staff_id" required>
                                        <option value="">Assign Staff</option>
                                        <?php 
                                        // Reset the staff result pointer for each order
                                        $staff_result->data_seek(0); 
                                        while ($staff = $staff_result->fetch_assoc()): ?>
                                            <option value="<?php echo $staff['id']; ?>"><?php echo $staff['full_name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="submit" name="assign" class="btn btn-primary btn-sm">Assign</button>
                                </form>
                            <?php else: ?>
                                <span class="text-success">Assigned</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='admin_home.php';" class="btn btn-secondary">Back to Admin Home</button>
</div>

</body>
</html>
