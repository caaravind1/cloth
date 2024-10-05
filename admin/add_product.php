<?php
session_start();

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

// Fetch categories from the database for the dropdown
$sql = "SELECT category_id, category_name FROM categories";
$category_result = $conn->query($sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];

    // Handle image upload
    $target_dir = "../uploads/"; // Target directory for images
    $image = basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $image; // Full path to save the image
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a valid image or fake image
    $check = getimagesize($_FILES["image"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size (limit: 5MB)
    if ($_FILES["image"]["size"] > 5000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    } else {
        // if everything is ok, try to upload file
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Insert the product into the database
            $sql = "INSERT INTO products (name, price, description, image, category_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sdssi", $name, $price, $description, $image, $category_id);

            if ($stmt->execute()) {
                echo "Product added successfully!";
            } else {
                echo "Error: " . $stmt->error;
            }

            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7; /* Light background */
            color: #333333; /* Darker text for readability */
        }

        .container {
            margin-top: 50px;
            border-radius: 10px;
            background-color: #ffffff; /* White container for a clean look */
            padding: 20px;
            max-width: 800px;
            margin: 50px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); /* Soft shadow */
        }

        h1 {
            font-size: 2rem;
            color: #333333; /* Dark heading */
            font-weight: 600;
            margin-bottom: 30px;
        }

        label {
            font-weight: bold;
            color: #555555; /* Slightly muted label color */
        }

        .form-control {
            border-radius: 5px;
            border: 1px solid #ced4da;
            padding: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: bold;
            text-transform: uppercase;
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: #ffffff;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 1rem;
            text-transform: uppercase;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="container">
    <h1 class="text-center">Add Product</h1>

    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" step="0.01" class="form-control" name="price" id="price" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" name="description" id="description" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="category_id">Category</label>
            <select class="form-control" name="category_id" id="category_id" required>
                <option value="" disabled selected>Select Category</option>
                <?php if ($category_result->num_rows > 0): ?>
                    <?php while ($row = $category_result->fetch_assoc()): ?>
                        <option value="<?php echo $row['category_id']; ?>"><?php echo $row['category_name']; ?></option>
                    <?php endwhile; ?>
                <?php else: ?>
                    <option value="">No categories available</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="image">Product Image</label>
            <input type="file" class="form-control-file" name="image" id="image" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Add Product</button>
    </form>

    <br>
    <a href="admin_home.php" class="btn btn-secondary btn-block">Back to Admin Home</a>
</div>

</body>
</html>
