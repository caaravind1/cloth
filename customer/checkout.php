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

// Fetch items in the cart
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image 
        FROM cart c 
        JOIN products p ON c.product_id = p.id 
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();

$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
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

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #333;
            border-radius: 10px;
            background-color: #3a3a3c;
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

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 25px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        /* Dark mode styling for text area and select dropdown */
        .form-control {
            background-color: #333;
            color: #ffffff;
            border: 1px solid #555;
        }

        .form-control:focus {
            background-color: #444;
            color: #ffffff;
            border-color: #00ccff;
            box-shadow: none;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Checkout</h1>
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
    <?php if ($result->num_rows == 0): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <h2 class="text-center">Items in Your Cart</h2>
        <?php 
        $totalAmount = 0;
        while ($item = $result->fetch_assoc()):
            $itemTotal = $item['price'] * $item['quantity'];
            $totalAmount += $itemTotal;
        ?>
            <div class="cart-item">
                <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                <div style="flex-grow: 1;">
                    <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                    <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                    <p>Quantity: <?php echo intval($item['quantity']); ?></p>
                </div>
            </div>
        <?php endwhile; ?>

        <div class="total">
            Total: $<?php echo number_format($totalAmount, 2); ?>
        </div>

        <form method="POST" action="process_order.php">
            <div class="form-group">
                <label for="shipping_address">Shipping Address</label>
                <textarea class="form-control" id="shipping_address" name="shipping_address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="payment_method">Payment Method</label>
                <select class="form-control" id="payment_method" name="payment_method" required>
                    <option value="Debit Card/Credit Card">Debit Card/Credit Card</option>
                    <option value="Online Payment">Online Payment</option>
                    <option value="Cash on Delivery">Cash on Delivery</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Complete Order</button>
        </form>
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

</body>
</html>
