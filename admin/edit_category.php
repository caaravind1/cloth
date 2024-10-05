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

// Check if category ID is provided
if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    // Fetch the category details
    $sql = "SELECT category_id, category_name FROM categories WHERE category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $stmt->bind_result($category_id, $category_name);
    $stmt->fetch();
    $stmt->close();
} else {
    echo "Invalid category ID.";
    exit();
}

// Handle form submission for updating category
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_category_name = $_POST['category_name'];

    $update_sql = "UPDATE categories SET category_name = ? WHERE category_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $new_category_name, $category_id);

    if ($stmt->execute()) {
        echo "Category updated successfully!";
        header("Location: manage_categories.php"); // Redirect back to manage categories
        exit();
    } else {
        echo "Error updating category: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
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
        <h2>Edit Category</h2>
        <form action="edit_category.php?id=<?php echo $category_id; ?>" method="POST">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" name="category_name" id="category_name" class="form-control" value="<?php echo htmlspecialchars($category_name); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Category</button>
        </form>
        <button onclick="window.location.href='manage_categories.php';" class="btn btn-secondary mt-3">Back to Categories</button>
    </div>
</body>
</html>
