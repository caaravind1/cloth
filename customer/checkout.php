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

// Fetch cart items
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
            color: #fff; 
            margin: 0; 
            min-height: 100vh;
        }
        .header { background-color: #1f1f1f; padding: 15px 20px; text-align: center; }
        .header h1 { margin: 0; color: #fff; }
        .container { margin-top: 30px; background: rgba(31, 31, 31, 0.8); padding: 20px; border-radius: 10px; }
        .cart-item { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .cart-item img { max-width: 80px; border-radius: 10px; }
        .total { font-weight: bold; font-size: 20px; margin-top: 20px; }
        .btn-primary { background-color: #007bff; border-radius: 20px; padding: 10px 20px; }
    </style>
</head>
<body>

<div class="header">
    <h1>Checkout</h1>
</div>

<div class="container">
    <?php if ($result->num_rows == 0): ?>
        <p>Your cart is empty.</p>
    <?php else: ?>
        <h2>Items in Your Cart</h2>
        <div id="cart-items">
            <?php 
            $totalAmount = 0;
            while ($item = $result->fetch_assoc()):
                $itemTotal = $item['price'] * $item['quantity'];
                $totalAmount += $itemTotal;
            ?>
                <div class="cart-item" data-cart-id="<?php echo $item['cart_id']; ?>">
                    <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div>
                        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p>Price: $<span class="item-price"><?php echo htmlspecialchars($item['price']); ?></span></p>
                        <p>
                            Quantity: 
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, -1)">-</button>
                            <span class="quantity-display"><?php echo intval($item['quantity']); ?></span>
                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $item['cart_id']; ?>, 1)">+</button>
                        </p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="total">
            Total: $<span id="total-amount"><?php echo number_format($totalAmount, 2); ?></span>
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
function updateQuantity(cartId, change) {
    const cartItem = document.querySelector(`[data-cart-id="${cartId}"]`);
    const quantityDisplay = cartItem.querySelector('.quantity-display');
    let currentQuantity = parseInt(quantityDisplay.textContent);

    const newQuantity = Math.max(1, currentQuantity + change); // Ensure quantity doesn't go below 1
    quantityDisplay.textContent = newQuantity;

    const price = parseFloat(cartItem.querySelector('.item-price').textContent);
    const itemTotal = price * newQuantity;

    updateTotalAmount();

    // Send AJAX request to update quantity in the database
    fetch('update_cart_quantity.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `cart_id=${cartId}&quantity=${newQuantity}`
    });
}

function updateTotalAmount() {
    let total = 0;
    document.querySelectorAll('.cart-item').forEach(item => {
        const price = parseFloat(item.querySelector('.item-price').textContent);
        const quantity = parseInt(item.querySelector('.quantity-display').textContent);
        total += price * quantity;
    });
    document.getElementById('total-amount').textContent = total.toFixed(2);
}
</script>

</body>
</html>
