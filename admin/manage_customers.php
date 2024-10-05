<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete customer
if (isset($_GET['delete'])) {
    $customer_id = $_GET['delete'];
    $delete_sql = "DELETE FROM users WHERE id = ? AND role_id = 1"; // Ensures only customers are deleted
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $customer_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_customers.php");
    exit();
}

// Fetch customers
$sql = "SELECT id, full_name, email, created_at FROM users WHERE role_id = 1"; // Fetch only customers
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .container {
            margin-top: 50px;
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

        .btn-secondary {
            margin-bottom: 20px;
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
        <h2>Manage Customers</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="manage_customers.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this customer?');">Remove</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button onclick="window.location.href='admin_home.php';" class="btn btn-secondary">Back to Admin Home</button>
    </div>
</body>
</html>
