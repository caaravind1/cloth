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
            background-color: #1f1f1f;
            padding: 15px 20px;
            text-align: center;
            border-bottom: 1px solid #333;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
        }

        .sticky-nav {
            position: sticky;
            top: 0;
            background-color: #1f1f1f;
            z-index: 1000;
            border-bottom: 1px solid #333;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .sticky-nav a {
            color: #00ccff;
            text-decoration: none;
            padding: 10px 20px;
            margin: 0 10px;
        }

        .sticky-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .profile-icon {
            display: flex;
            align-items: center;
            position: relative;
            margin-left: 15px;
            cursor: pointer;
        }

        .profile-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .profile-name {
            color: #ffffff;
            font-size: 14px;
            margin-right: 10px;
        }

        .dropdown {
            position: absolute;
            display: none;
            background-color: #1f1f1f;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            min-width: 160px;
            border-radius: 5px;
            top: 50px;
            right: 0;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .container {
            margin-top: 20px;
            border-radius: 20px;
            background-color: #1f1f1f;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 1200px;
            margin: 50px auto;
        }

        .category-bar {
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            border-bottom: 1px solid #333;
            margin-bottom: 20px;
        }

        .category-bar a {
            color: #00ccff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 50px;
            transition: background-color 0.3s;
        }

        .category-bar a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .category-bar img {
            width: 50px;
            height: auto;
            border-radius: 10px;
            margin: 0 10px;
        }

        .card {
            margin-bottom: 30px;
            border: none;
            border-radius: 20px;
            background-color: #3a3a3c;
            height: auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
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
            <?php else: ?>
                <a href="customer_register.php">Login / Sign Up</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <?php if (!empty($cart_message)): ?>
            <div class="alert alert-success"><?php echo $cart_message; ?></div>
        <?php endif; ?>

        <div class="description">
            <p>Explore a wide range of products across various categories. Shop the latest trends and styles at unbeatable prices!</p>
        </div>

        <h1 class="text-center">Browse Our Products</h1>

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
                                <!-- Add to Cart Button -->
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

    <script>
        document.addEventListener('click', function(event) {
            var profileDropdown = document.getElementById('profileDropdown');
            var dropdownMenu = document.getElementById('dropdownMenu');
            if (!profileDropdown.contains(event.target)) {
                dropdownMenu.style.display = 'none'; // Hide dropdown when clicking outside
            } else {
                dropdownMenu.style.display = 'block'; // Show dropdown when clicking on profile icon
            }
        });
    </script>

</body>
</html>
