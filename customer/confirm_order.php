<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ensure order_id is present in the query string
if (!isset($_GET['order_id'])) {
    echo "Order not found.";
    exit();
}

$order_id = $_GET['order_id'];

// Create connection
$conn = new mysqli("localhost", "root", "", "cloth");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch order and user details
$sql = "SELECT o.id, o.total_amount, o.shipping_address, o.payment_method, o.status, 
               u.full_name, u.email 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.id = ? AND o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    echo "Order not found.";
    exit();
}

$order = $order_result->fetch_assoc();

// Fetch order items
$item_sql = "SELECT oi.quantity, oi.price, p.name, p.image 
             FROM order_items oi 
             JOIN products p ON oi.product_id = p.id 
             WHERE oi.order_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();

$conn->close();

$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
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
        }

        .sticky-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .profile-icon {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
        }

        .profile-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .dropdown {
            position: absolute;
            display: none;
            background-color: #1f1f1f;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            min-width: 160px;
            border-radius: 5px;
            top: 50px;
            right: 0;
            z-index: 1;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .container {
            margin-top: 20px;
            border-radius: 20px;
            background-color: #1f1f1f;
            padding: 20px;
            max-width: 800px;
            margin: 50px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .cart-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #333;
            background-color: #3a3a3c;
            border-radius: 10px;
        }

        .cart-item img {
            max-width: 80px;
            margin-right: 20px;
            border-radius: 10px;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Order Confirmation</h1>
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
    <h2 class="text-center">Thank you for your order!</h2>
    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['full_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['email']); ?></p>
    <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
    <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

    <h3 class="text-center">Order Summary</h3>

    <?php while ($item = $item_result->fetch_assoc()): ?>
        <div class="cart-item">
            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
            <div>
                <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                <p>Quantity: <?php echo intval($item['quantity']); ?></p>
                <p>Price: $<?php echo number_format($item['price'], 2); ?></p>
            </div>
        </div>
    <?php endwhile; ?>

    <div class="total">
        Total Amount: $<?php echo number_format($order['total_amount'], 2); ?>
    </div>

    <!-- Button to proceed to payment -->
    <div class="text-center">
        <form action="dummy_payment.php" method="post">
            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
            <button type="submit" class="btn btn-primary">Proceed to Payment</button>
        </form>
    </div>
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
<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4>My Account</h4>
            <ul>
                <li><a href="#">Order Status</a></li>
                <li><a href="#">Sign In/Register</a></li>
                <li><a href="#">Returns</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Shop</h4>
            <ul>
                <li><a href="#">Women</a></li>
                <li><a href="#">Men</a></li>
                <li><a href="#">Kids</a></li>
                <li><a href="#">Sale</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Help</h4>
            <ul>
                <li><a href="#">FAQs</a></li>
                <li><a href="#">Return Policy</a></li>
                <li><a href="#">Size Guide</a></li>
                <li><a href="#">Contact Us</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Company</h4>
            <ul>
                <li><a href="#">About Us</a></li>
                <li><a href="#">Careers</a></li>
                <li><a href="#">Store Locator</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>Customer Service</h4>
            <p>Email: support@clothingstore.com</p>
            <p>Phone: 000-800-919-1686</p>
            <p>Hours: Mon-Fri 9:00 AM - 7:00 PM</p>
        </div>
    </div>

    <div class="social-media">
        <p>Follow Us</p>
        <a href="#"><img src="../uploads/facebook.png" alt="Facebook"></a>
        <a href="#"><img src="../uploads/instagram.png" alt="Instagram"></a>
        <a href="#"><img src="../uploads/twitter.png" alt="Twitter"></a>
    </div>

    <div class="footer-bottom">
        <p>&copy; 2024 Clothing Store. All rights reserved.</p>
        <p><a href="#">Privacy Policy</a></p>
    </div>
</footer>
</body>
</html>
