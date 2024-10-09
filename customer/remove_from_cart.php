<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$cart_id = $_GET['id'];

// Create connection
$conn = new mysqli("localhost", "root", "", "cloth");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Remove item from the cart
$sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $cart_id, $user_id);
$stmt->execute();
$stmt->close();

$conn->close();

// Redirect back to the cart page with a message
$_SESSION['cart_message'] = "Item removed from cart.";
header('Location: cart.php');
exit();
?>
