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

// Fetch total stats
$total_orders_query = "SELECT COUNT(*) AS total_orders FROM orders";
$total_orders = $conn->query($total_orders_query)->fetch_assoc()['total_orders'];

$total_products_query = "SELECT COUNT(*) AS total_products FROM products";
$total_products = $conn->query($total_products_query)->fetch_assoc()['total_products'];

$total_customers_query = "SELECT COUNT(*) AS total_customers FROM users WHERE role_id = 1";
$total_customers = $conn->query($total_customers_query)->fetch_assoc()['total_customers'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            overflow-x: hidden;
        }

        .header {
            background-color: #1f1f1f;
            padding: 15px 20px;
            border-bottom: 1px solid #333;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
        }

        .sidebar {
            background-color: rgba(31, 31, 31, 0.8); /* Semi-transparent background */
            padding: 15px;
            display: flex;
            flex-direction: column;
            position: relative; /* Change to relative to place it below header */
            margin-top: 0; /* No margin to stick to header */
        }

        .sidebar a {
            color: #00ccff;
            text-decoration: none;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .container {
            margin-top: 20px; /* Adjust margin for spacing below the sidebar */
        }

        .card {
            margin-bottom: 20px;
            background-color: #1f1f1f;
            border-radius: 10px;
            color: #fff;
        }

        .card-header {
            background-color: #007bff;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Admin Dashboard</h1>
</div>

<div class="sidebar">
    <a href="admin_home.php">Home</a>
    <a href="manage_categories.php">Manage Categories</a>
    <a href="manage_products.php">Manage Products</a>
    <a href="manage_orders.php">Manage Orders</a>
    <a href="manage_staff.php">Manage Staff</a>
    <a href="manage_customers.php">Manage Customers</a>
    <a href="sales_reports.php">Sales Reports</a>
</div>

<div class="container">
    <h2 class="text-center">Admin Dashboard Overview</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Total Orders</div>
                <div class="card-body">
                    <h3 class="text-center"><?php echo $total_orders; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Total Products</div>
                <div class="card-body">
                    <h3 class="text-center"><?php echo $total_products; ?></h3>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Total Customers</div>
                <div class="card-body">
                    <h3 class="text-center"><?php echo $total_customers; ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
