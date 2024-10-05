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
            margin: 0;
            padding: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            position: fixed;
            height: 100%;
            padding-top: 20px;
        }
        .sidebar a {
            color: #ffffff;
            padding: 15px;
            text-decoration: none;
            display: block;
            transition: background 0.3s;
        }
        .sidebar a:hover {
            background-color: #007bff;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .table-wrapper {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            margin-bottom: 15px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="add_category.php">Manage Categories</a>
    <a href="add_product.php">Manage Products</a>
    <!-- Add more sidebar links as needed -->
</div>

<div class="content">
    <!-- Content will be injected here depending on the link clicked -->
</div>

</body>
</html>

