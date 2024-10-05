<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    // Redirect to login if not logged in
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
$sql = "SELECT c.quantity, p.id, p.name, p.price, p.image FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();

// Check if the user is logged in
$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : ''; // Safely check if 'full_name' exists

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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

        .container {
            margin-top: 20px;
            border-radius: 20px;
            background-color: #1f1f1f; /* Darker container background */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 1200px; /* Increased width for larger displays */
            margin: 50px auto;
        }

        .cart-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #333; /* Border for cart items */
            border-radius: 10px;
            background-color: #3a3a3c; /* Background color for cart items */
        }

        .cart-item img {
            max-width: 80px; /* Set a fixed size for the product images in the cart */
            margin-right: 20px;
            border-radius: 10px; /* Curved corners for images */
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 25px; /* Increased for oval shape */
            padding: 10px 20px; /* Adjusted padding for a better oval shape */
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Your Shopping Cart</h1>
    </div>

    <div class="sticky-nav">
        <a href="browse_products.php">Home</a>
        <div class="login-signup">
            <a href="customer_register.php">Login / Sign Up</a>
            <?php if ($cart_access): ?>
                <a href="cart.php">Cart</a>
            <?php else: ?>
                <a href="customer_login.php">Cart</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <?php if ($result->num_rows == 0): ?>
            <p>Your cart is empty.</p>
        <?php else: ?>
            <h2 class="text-center">Items in Your Cart</h2>
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="cart-item">
                    <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div>
                        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                        <p>Quantity: <?php echo intval($item['quantity']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
            <div class="text-center">
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
