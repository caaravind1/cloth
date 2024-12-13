<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Create connection
$conn = new mysqli("localhost", "root", "", "cloth");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user's orders
$sql = "SELECT o.id, o.total_amount, o.shipping_address, o.payment_method, o.status 
        FROM orders o 
        WHERE o.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$order_result = $stmt->get_result();

$conn->close();

$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : ''; // Safely check if 'full_name' exists
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Orders</title>
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

        .profile-name {
            color: #ffffff; /* White for name */
            font-size: 14px;
            margin-right: 10px; /* Space between name and dropdown */
        }

        .dropdown {
            position: absolute;
            display: none; /* Initially hidden */
            background-color: #1f1f1f;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            min-width: 160px; 
            border-radius: 5px; /* Rounded corners */
            top: 50px; /* Move dropdown below the profile icon */
            right: 0; /* Align dropdown to the right of the profile icon */
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            text-align: left;
            transition: background-color 0.3s; /* Smooth background transition */
        }

        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .container {
            margin-top: 20px;
            border-radius: 20px;
            background-color: #1f1f1f; /* Darker container background */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 800px; /* Increased width for larger displays */
            margin: 50px auto;
        }

        .order-item {
            padding: 10px;
            border: 1px solid #333;
            background-color: #3a3a3c;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 20px;
        }
        body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
}

.footer {
    background-color: #000;
    color: #fff;
    padding: 20px 0;
}

.footer-container {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.footer-section {
    margin-bottom: 20px;
    min-width: 200px;
}

.footer-section h4 {
    margin-bottom: 10px;
}

.footer-section ul {
    list-style: none;
    padding: 0;
}

.footer-section ul li {
    margin-bottom: 5px;
}

.footer-section ul li a {
    color: #fff;
    text-decoration: none;
}

.footer-section ul li a:hover {
    text-decoration: underline;
}

.social-media {
    text-align: center;
    margin-top: 20px;
}

.social-media p {
    margin-bottom: 10px;
}

.social-media a img {
    width: 24px;
    margin: 0 10px;
}

.footer-bottom {
    text-align: center;
    margin-top: 20px;
    font-size: 12px;
}

.footer-bottom a {
    color: #fff;
    text-decoration: none;
}

.footer-bottom a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>

<div class="header">
    <h1>Your Orders</h1>
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
    <?php if ($order_result->num_rows == 0): ?>
        <p>You have not placed any orders yet.</p>
    <?php else: ?>
        <h2 class="text-center">Your Orders</h2>
        <?php while ($order = $order_result->fetch_assoc()): ?>
            <div class="order-item">
                <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
                <p><strong>Total Amount:</strong> $<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
                <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                <!-- You can add a button to view more details about the order here if necessary -->
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
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
