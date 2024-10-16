<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    exit("Unauthorized access");
}

$conn = new mysqli("localhost", "root", "", "cloth");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cart_id = $_POST['cart_id'];
$quantity = $_POST['quantity'];

$sql = "UPDATE cart SET quantity = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $quantity, $cart_id);
$stmt->execute();

$stmt->close();
$conn->close();
?>
