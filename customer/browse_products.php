<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "cloth";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories for the horizontal bar with images
$category_sql = "SELECT category_id, category_name FROM categories";
$categories_result = $conn->query($category_sql);

// Fetch products from the database based on selected category
$sql = "SELECT id, name, price, description, image FROM products";
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    $sql .= " WHERE category_id = ?";
}

$stmt = $conn->prepare($sql);
if (isset($category_id)) {
    $stmt->bind_param("i", $category_id);
}
$stmt->execute();
$result = $stmt->get_result();

// Close the database connection
$conn->close();

// Check if the user is logged in
$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';

// Check for success message
$cart_message = isset($_SESSION['cart_message']) ? $_SESSION['cart_message'] : '';
unset($_SESSION['cart_message']); // Clear the message after displaying
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212; /* Dark background */
            color: #ffffff; /* Light text */
        }

        .header {
            background-color: #1f1f1f; /* Header background */
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid #333; /* Light border for separation */
        }

        .header h1 {
            margin: 0;
            color: #ffffff; /* White for heading */
        }

        .sticky-nav {
            position: sticky;
            top: 0;
            background-color: #1f1f1f;
            z-index: 1000;
            border-bottom: 1px solid #333;
            padding: 10px 0;
        }

        .sticky-nav a {
            color: #00ccff; /* Bright color for links */
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
        }

        .sticky-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Light hover effect */
        }

        .login-signup {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .home-button {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .profile-icon {
            display: flex;
            align-items: center;
        }

        .profile-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .container {
            margin-top: 20px;
            border-radius: 20px;
            background-color: #1f1f1f; /* Darker container background */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 1200px; /* Increased width for larger displays */
            margin: 50px auto;
        }

        .category-bar {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            border-bottom: 1px solid #333; /* Light border for separation */
            margin-bottom: 20px;
        }

        .category-bar a {
            color: #00ccff; /* Bright color for links */
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px; /* Rounded links */
            transition: background-color 0.3s;
        }

        .category-bar a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Light hover effect */
        }

        .category-bar img {
            width: 50px; /* Set fixed width for category images */
            height: auto; /* Maintain aspect ratio */
            border-radius: 10px; /* Rounded corners */
            margin: 0 10px; /* Spacing between images */
        }

        .card {
            margin-bottom: 30px;
            border: none;
            border-radius: 20px; /* Curved corners for the product container */
            background-color: #3a3a3c; /* Dark card background */
            height: auto; /* Allow container to adjust to the image size */
            display: flex;
            flex-direction: column;
            align-items: center; /* Center items horizontally */
            justify-content: flex-start;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Hover effect */
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .product-img {
            width: 100%; 
            height: auto;
            max-height: 250px; 
            object-fit: contain; 
            border-radius: 20px; 
            margin-bottom: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 25px; 
            padding: 10px 30px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin: 5px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .services {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
        }

        .service-card {
            background-color: #3a3a3c;
            border-radius: 20px;
            padding: 20px;
            width: calc(33.333% - 20px); 
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .service-card h3 {
            color: #00ccff;
        }

        .service-card p {
            color: #cccccc;
        }

        @media (max-width: 768px) {
            .service-card {
                width: 100%;
            }
        }

        .row {
            justify-content: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Welcome to Our Online Store</h1>
    </div>

    <div class="sticky-nav">
        <a href="browse_products.php">Home</a>
        <div class="login-signup">
            <?php if ($cart_access): ?>
                <div class="profile-icon">
                    <img src="../uploads/profile.jpg" alt="Profile">
                    <a href="profile.php"><?php echo htmlspecialchars($user_name); ?></a>
                </div>
                <a href="cart.php">Cart</a>
            <?php else: ?>
                <a href="customer_register.php">Login / Sign Up</a>
                <a href="customer_login.php">Cart</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <div class="description">
            <p>Explore a wide range of products across various categories. Shop the latest trends and styles at unbeatable prices!</p>
        </div>

        <h1 class="text-center">Browse Our Products</h1>

        <!-- Horizontal Category Bar -->
        <div class="category-bar">
            <?php if ($categories_result->num_rows > 0): ?>
                <?php while ($category = $categories_result->fetch_assoc()): ?>
                    <a href="browse_products.php?category_id=<?php echo $category['category_id']; ?>">
                        <img src="../uploads/category/<?php echo strtolower($category['category_name']); ?>.jpg" alt="<?php echo htmlspecialchars($category['category_name']); ?>" />
                        <?php echo htmlspecialchars($category['category_name']); ?>
                    </a>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>

        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card" onclick="window.location.href='product_details.php?id=<?php echo $row['id']; ?>'">
                            <div class="card-header"><?php echo htmlspecialchars($row['name']); ?></div>
                            <div class="card-body">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="product-img">
                                <?php else: ?>
                                    <img src="../uploads/default.jpg" alt="Default Image" class="product-img">
                                <?php endif; ?>
                                <p class="price">$<?php echo htmlspecialchars($row['price']); ?></p>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <?php if ($cart_access): ?>
                                    <a href="add_to_cart.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Add to Cart</a>
                                <?php else: ?>
                                    <a href="customer_login.php" class="btn btn-primary">Add to Cart</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No products found.</p>
            <?php endif; ?>
        </div>

        <!-- Services Section -->
        <div class="services">
            <div class="service-card">
                <h3>Affordable Prices</h3>
                <p>We offer competitive prices for the best quality products.</p>
            </div>
            <div class="service-card">
                <h3>Fast Delivery</h3>
                <p>Get your products delivered to your doorstep in no time.</p>
            </div>
            <div class="service-card">
                <h3>Quality Products</h3>
                <p>We provide only the highest quality products for our customers.</p>
            </div>
        </div>

    </div>

</body>
</html>
