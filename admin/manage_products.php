<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: admin_login.php");
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

// Handle delete product
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    // Fetch the product image path to delete the file
    $image_query = "SELECT image FROM products WHERE id = ?";
    $stmt = $conn->prepare($image_query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // Delete the product from the database
    $delete_sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        // Delete the image file from the uploads directory
        if (!empty($image) && file_exists("../uploads/" . $image)) {
            unlink("../uploads/" . $image);
        }
        header("Location: manage_products.php");
        exit();
    }
    $stmt->close();
}

// Fetch all products from the database
$sql = "SELECT p.id, p.name, p.price, p.description, p.image, c.category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.category_id";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
        }

        .container {
            margin-top: 50px;
        }

        .btn-primary, .btn-danger, .btn-secondary {
            border-radius: 50px;
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
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        table {
            background-color: #ffffff;
            color: #333;
            border-radius: 15px;
        }

        table th, table td {
            border-bottom: 1px solid #ddd;
        }

        img {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Manage Products</h2>
        <a href="add_product.php" class="btn btn-secondary mb-3">Add New Product</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>"></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                <a href="manage_products.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this product?');">Remove</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No products found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <button onclick="window.location.href='admin_home.php';" class="btn btn-secondary">Back to Admin Home</button>
    </div>
</body>
</html>
