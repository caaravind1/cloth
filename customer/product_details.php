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

// Check if the Add to Cart button was clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) {
        // Get user ID from session
        $user_id = $_SESSION['user_id'];
        
        // Insert product into cart
        $cart_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $cart_stmt = $conn->prepare($cart_sql);
        $quantity = 1; // Default quantity is 1
        $cart_stmt->bind_param("iii", $user_id, $product_id, $quantity);

        if ($cart_stmt->execute()) {
            $cart_success = "Product added to cart successfully!";
        } else {
            $cart_error = "Failed to add product to cart.";
        }
    } else {
        // Redirect to login if user is not logged in
        header('Location: customer_login.php');
        exit();
    }
}

$conn->close();

// Check if user is logged in
$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
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
            background-image: url('../uploads/mainbg.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            background-attachment: fixed; 
            color: #ffffff; 
            min-height: 100vh; 
            margin: 0; 
        }

        .header {
            background-color: #1f1f1f; /* Header background */
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid #333; /* Light border for separation */
        }

        .sticky-nav {
            position: sticky;
            top: 0;
            background-color: #1f1f1f;
            z-index: 1000;
            border-bottom: 1px solid #333;
            padding: 10px 0;
            display: flex; /* Use flexbox for alignment */
            justify-content: space-between; /* Space out elements */
            align-items: center; /* Center items vertically */
        }

        .sticky-nav a {
            color: #00ccff; /* Bright color for links */
            text-decoration: none;
            padding: 10px 20px;
        }

        .sticky-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Light hover effect */
        }

        .profile-icon {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer; /* Change cursor to pointer for hover */
        }

        .profile-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .dropdown {
            position: absolute;
            display: none; /* Initially hidden */
            background-color: #1f1f1f;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            min-width: 160px; 
            border-radius: 5px; /* Rounded corners */
            top: 40px; /* Move dropdown below the profile icon */
            right: 0; /* Align dropdown to the right of the profile icon */
            z-index: 1;
        }

        .dropdown-content {
            display: block; /* Make dropdown block element */
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block; /* Display as block for vertical stacking */
        }

        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Hover effect */
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
    </style>
</head>
<body>

<div class="header">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
</div>

<div class="sticky-nav">
    <a href="browse_products.php">Home</a>
    <div class="profile-icon" id="profileDropdown">
        <img src="../uploads/profile.jpg" alt="Profile">
        <span class="profile-name"><?php echo htmlspecialchars($user_name); ?></span>
        <div class="dropdown" id="dropdownMenu">
            <div class="dropdown-content">
                <a href="profile.php">View Profile</a>
                <a href="orders.php">My Orders</a>
                <a href="cart.php">Cart</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-img">
    <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
    <p><?php echo htmlspecialchars($product['description']); ?></p>

    <form method="POST" action="">
        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
    </form>
    <br>
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

<script>
    document.getElementById('profileDropdown').addEventListener('click', function() {
        const dropdown = document.getElementById('dropdownMenu');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownMenu');
        if (!document.getElementById('profileDropdown').contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

</body>
</html>
