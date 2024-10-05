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

// Fetch product details
$product_id = $_GET['id'];
$sql = "SELECT name, price, description, image, category_id FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($name, $price, $description, $image, $category_id);
$stmt->fetch();
$stmt->close();

// Fetch categories for the dropdown
$categories = [];
$sql = "SELECT category_id, category_name FROM categories";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_name = $_POST['name'];
    $new_price = $_POST['price'];
    $new_description = $_POST['description'];
    $new_category_id = $_POST['category_id'];
    $new_image = $image;

    // Handle image upload if a new image is provided
    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate and move the uploaded file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $new_image = $target_file;
        }
    }

    $update_sql = "UPDATE products SET name = ?, price = ?, description = ?, image = ?, category_id = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sdssii", $new_name, $new_price, $new_description, $new_image, $new_category_id, $product_id);

    if ($stmt->execute()) {
        echo "Product updated successfully!";
        header("Location: manage_products.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
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
        <h2>Edit Product</h2>
        <form method="POST" action="edit_product.php?id=<?php echo $product_id; ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo $name; ?>" required>
            </div>
            <div class="form-group">
                <label for="price">Price:</label>
                <input type="text" name="price" id="price" class="form-control" value="<?php echo $price; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" class="form-control" required><?php echo $description; ?></textarea>
            </div>
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select name="category_id" id="category_id" class="form-control" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>" <?php if ($category['category_id'] == $category_id) echo 'selected'; ?>>
                            <?php echo $category['category_name']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" name="image" id="image" class="form-control">
                <img src="<?php echo $image; ?>" alt="Current Image" style="max-width: 100px; margin-top: 10px;">
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
        <button onclick="window.location.href='manage_products.php';" class="btn btn-secondary mt-3">Back to Manage Products</button>
    </div>
</body>
</html>
