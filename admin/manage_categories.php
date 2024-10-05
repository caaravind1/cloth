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

// Handle delete category
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];
    $delete_sql = "DELETE FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_categories.php");
    exit();
}

// Fetch categories
$sql = "SELECT category_id, category_name FROM categories";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
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
        <h2>Manage Categories</h2>
        <a href="add_category.php" class="btn btn-secondary mb-3">Add New Category</a>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Category ID</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['category_id']; ?></td>
                    <td><?php echo $row['category_name']; ?></td>
                    <td>
                        <a href="edit_category.php?id=<?php echo $row['category_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="manage_categories.php?delete=<?php echo $row['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Remove</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <button onclick="window.location.href='admin_home.php';" class="btn btn-secondary">Back to Admin Home</button>
    </div>
</body>
</html>
