<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: admin_login.php");
    exit();
}
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
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 50px;
        }

        .card {
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #007bff;
            color: #ffffff;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Admin Dashboard</h1>

        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Category</div>
                    <div class="card-body">
                        <a href="manage_categories.php" class="btn btn-primary btn-block">View Categories</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Product</div>
                    <div class="card-body">
                        <a href="manage_products.php" class="btn btn-primary btn-block">View Products</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Customer</div>
                    <div class="card-body">
                        <a href="manage_customers.php" class="btn btn-primary btn-block">View Customers</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Staff</div>
                    <div class="card-body">
                        <a href="manage_staff.php" class="btn btn-primary btn-block">View Staff</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Sales</div>
                    <div class="card-body">
                        <a href="sales_reports.php" class="btn btn-primary btn-block">View Sales Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
