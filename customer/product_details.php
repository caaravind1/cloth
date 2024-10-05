<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product details
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "SELECT id, name, price, description, image FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if product exists
    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Product not found.");
    }

    // Fetch reviews for the product
    $review_sql = "SELECT r.rating, r.comment, u.full_name 
                   FROM reviews r 
                   JOIN users u ON r.user_id = u.id 
                   WHERE r.product_id = ?";
    $review_stmt = $conn->prepare($review_sql);
    $review_stmt->bind_param("i", $product_id);
    $review_stmt->execute();
    $reviews_result = $review_stmt->get_result();

    $stmt->close();
} else {
    die("Invalid product ID.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212; /* Dark background */
            color: #ffffff; /* Light text */
        }

        .container {
            margin-top: 50px;
            background-color: #1f1f1f; /* Darker container */
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5); /* Soft shadow */
        }

        .product-img {
            max-width: 100%;
            height: auto; /* Maintain aspect ratio */
            object-fit: cover; /* Cover the area while maintaining aspect ratio */
            border-radius: 10px; /* Rounded corners */
            margin: 0 auto; /* Center the image */
            display: block; /* Make it a block element */
        }

        .price {
            font-size: 1.5rem;
            color: #00ccff; /* Bright color for price */
            font-weight: 600; /* Semi-bold for the price */
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px; /* Oval shape */
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

        .review-section {
            margin-top: 30px;
            border-top: 1px solid #444; /* Top border for separation */
            padding-top: 20px;
        }

        .review {
            margin-bottom: 20px;
            background-color: #2c2c2e; /* Darker background for reviews */
            padding: 15px;
            border-radius: 8px;
        }

        .footer {
            background-color: #1f1f1f; /* Dark footer */
            padding: 15px;
            text-align: center;
            margin-top: 20px;
            color: #cccccc; /* Light gray for footer text */
        }

        .login-signup {
            position: absolute;
            top: 20px;
            right: 20px;
        }
    </style>
</head>
<body>

<div class="login-signup">
    <a href="customer_register.php" class="btn btn-primary">Login / Sign Up</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="cart.php" class="btn btn-primary" title="Cart">
            <img src="../uploads/cart.jpg" alt="Cart" style="width: 30px; height: 30px;"/> <!-- Cart Image -->
        </a>
    <?php else: ?>
        <a href="customer_login.php" class="btn btn-primary" title="Cart">
            <img src="../uploads/cart.jpg" alt="Cart" style="width: 30px; height: 30px;"/> <!-- Cart Image -->
        </a>
    <?php endif; ?>
</div>

<div class="container">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
    <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
    <p><?php echo htmlspecialchars($product['description']); ?></p>

    <button class="btn btn-primary" onclick="alert('Added to cart!')">Add to Cart</button>
    <br><br>
    <a href="browse_products.php" class="btn btn-secondary">Back to Products</a>

    <div class="review-section">
        <h3>Customer Reviews</h3>
        <?php if ($reviews_result->num_rows > 0): ?>
            <?php while ($review = $reviews_result->fetch_assoc()): ?>
                <div class="review">
                    <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                    <p>Rating: <?php echo htmlspecialchars($review['rating']); ?> ★</p>
                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to review!</p>
        <?php endif; ?>
    </div>
</div>

<div class="footer">
    <p>&copy; 2024 Your Company Name. All Rights Reserved.</p>
</div>

</body>
</html>
