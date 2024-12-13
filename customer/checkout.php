<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Database connection
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

// Initialize total amount
$totalAmount = 0;
$cartItems = [];

while ($item = $result->fetch_assoc()) {
    $itemTotal = $item['price'] * $item['quantity'];
    $totalAmount += $itemTotal;
    $cartItems[] = $item;
}

$conn->close();

$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "cloth");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Insert a new order with total amount
    $order_sql = "INSERT INTO orders (user_id, shipping_address, payment_method, total_amount, status) VALUES (?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($order_sql);
    $stmt->bind_param("issd", $user_id, $shipping_address, $payment_method, $totalAmount);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        $order_id = $stmt->insert_id; // Get the order ID
        $stmt->close();

        // Empty the user's cart after successful checkout
        $delete_cart_sql = "DELETE FROM cart WHERE user_id = ?";
        $delete_stmt = $conn->prepare($delete_cart_sql);
        $delete_stmt->bind_param("i", $user_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        // Close the database connection
        $conn->close();

        // Redirect to the correct payment page
        if ($payment_method === 'Debit Card/Credit Card') {
            header("Location: card_payment.php?order_id=$order_id");
        } else {
            header("Location: dummy_payment.php?order_id=$order_id");
        }
        exit();
    } else {
        echo "Error inserting order: " . $stmt->error;
    }
}
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
            color: #ffffff;
            margin: 0;
            min-height: 100vh;
        }

        .header {
            background-color: #1f1f1f;
            padding: 15px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
        }

        .container {
            margin-top: 30px;
            background-color: rgba(31, 31, 31, 0.9);
            padding: 20px;
            border-radius: 10px;
            max-width: 800px;
            margin: 50px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #2c2c2e;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        .cart-item img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }

        .cart-item-details {
            flex: 1;
            margin-left: 15px;
        }

        .cart-item h5 {
            margin: 0;
            color: #00ccff;
        }

        .quantity-control {
            display: flex;
            align-items: center;
        }

        .quantity-btn {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            margin: 0 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .quantity-display {
            min-width: 30px;
            text-align: center;
        }

        .total {
            font-weight: bold;
            font-size: 1.5rem;
            margin-top: 20px;
            text-align: right;
        }

        .btn-primary {
            background-color: #007bff;
            border-radius: 20px;
            padding: 10px 20px;
            margin-top: 15px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .form-control {
            background-color: #2c2c2e;
            color: #ffffff;
            border: 1px solid #444;
        }

        .form-control:focus {
            background-color: #3a3a3c;
            border-color: #00ccff;
            box-shadow: none;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Checkout</h1>
</div>

<div class="container">
    <?php if (empty($cartItems)): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <h2>Items in Your Cart</h2>
        <div id="cart-items">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>" data-price="<?php echo $item['price']; ?>">
                    <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div class="cart-item-details">
                        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p>Price: $<span class="item-price"><?php echo htmlspecialchars($item['price']); ?></span></p>
                        <div class="quantity-control">
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">-</button>
                            <span class="quantity-display"><?php echo intval($item['quantity']); ?></span>
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">+</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="total">
            Total: $<span id="total-amount"><?php echo number_format($totalAmount, 2); ?></span>
        </div>

        <form id="checkout-form" method="POST">
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
function updateQuantity(cartId, change) {
    const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
    const quantityDisplay = cartItem.querySelector('.quantity-display');
    const itemPrice = parseFloat(cartItem.getAttribute('data-price'));
    let currentQuantity = parseInt(quantityDisplay.textContent);

    currentQuantity += change;
    if (currentQuantity < 1) return;

    quantityDisplay.textContent = currentQuantity;
    const newItemTotal = itemPrice * currentQuantity;

    let totalAmount = Array.from(document.querySelectorAll('.cart-item'))
        .reduce((sum, item) => sum + parseFloat(item.getAttribute('data-price')) * parseInt(item.querySelector('.quantity-display').textContent), 0);

    document.getElementById('total-amount').textContent = totalAmount.toFixed(2);
}
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
