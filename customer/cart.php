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
$sql = "SELECT c.id AS cart_id, c.quantity, p.id AS product_id, p.name, p.price, p.image FROM cart c
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
            display: flex; /* Use flexbox for alignment */
            justify-content: space-between; /* Space out elements */
            align-items: center; /* Center items vertically */
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

        .profile-icon {
            display: flex;
            align-items: center;
            position: relative;
            margin-left: 15px; /* Adjust left margin for alignment */
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

        .remove-icon {
            cursor: pointer; /* Change cursor to pointer for hover */
            width: 40px; /* Set icon width to 40px */
            height: 40px; /* Set icon height to 40px */
            margin-left: 20px; /* Space between product details and remove icon */
        }

        .quantity-control {
            display: flex;
            align-items: center; /* Center items vertically */
            margin-left: 20px; /* Space between product details and quantity control */
        }

        .quantity-button {
            background-color: #007bff;
            border: none;
            color: white;
            border-radius: 5px; /* Rounded corners */
            padding: 5px 10px; /* Padding for buttons */
            cursor: pointer; /* Change cursor to pointer for hover */
            margin: 0 5px; /* Space between buttons */
        }

        .quantity-display {
            padding: 5px 10px; /* Padding for quantity display */
            border: 1px solid #007bff;
            border-radius: 5px; /* Rounded corners */
            min-width: 30px; /* Minimum width for the display */
            text-align: center; /* Center text */
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
            <?php while ($item = $result->fetch_assoc()): ?>
                <div class="cart-item">
                    <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    <div style="flex-grow: 1;"> <!-- Make this div grow to take available space -->
                        <h5><?php echo htmlspecialchars($item['name']); ?></h5>
                        <p>Price: $<?php echo htmlspecialchars($item['price']); ?></p>
                        <p>Quantity: 
                            <div class="quantity-control">
                                <button class="quantity-button" onclick="changeQuantity(<?php echo $item['product_id']; ?>, -1)">-</button>
                                <span class="quantity-display" id="quantity-<?php echo $item['product_id']; ?>"><?php echo intval($item['quantity']); ?></span>
                                <button class="quantity-button" onclick="changeQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                            </div>
                        </p>
                    </div>
                    <img src="../uploads/remove.jpg" alt="Remove" class="remove-icon" onclick="removeItem(<?php echo $item['cart_id']; ?>)">
                </div>
            <?php endwhile; ?>
            <div class="text-center">
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function removeItem(cartId) {
            if (confirm("Are you sure you want to remove this item from the cart?")) {
                window.location.href = "remove_from_cart.php?id=" + cartId; // Redirect to remove item script
            }
        }

        function changeQuantity(productId, change) {
            const quantityElement = document.getElementById('quantity-' + productId);
            let currentQuantity = parseInt(quantityElement.innerText);

            currentQuantity += change;

            if (currentQuantity < 1) {
                alert("Quantity cannot be less than 1.");
                return;
            }

            // Here, you'd ideally send an AJAX request to update the quantity in the database
            // For now, just update the display
            quantityElement.innerText = currentQuantity;
        }
    </script>

</body>
</html>
