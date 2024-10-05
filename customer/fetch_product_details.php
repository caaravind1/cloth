<?php
session_start();
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

// Check if product ID is provided
if (isset($_GET['id'])) {
    $productId = $_GET['id'];
    $sql = "SELECT name, price, description, image FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($name, $price, $description, $image);
    $stmt->fetch();
    
    // Display product details
    echo '<div class="text-center">';
    echo '<img src="../uploads/' . htmlspecialchars($image) . '" alt="' . htmlspecialchars($name) . '" class="product-img" style="max-height: 250px; border-radius: 20px;">';
    echo '<h5>' . htmlspecialchars($name) . '</h5>';
    echo '<p class="price">$' . htmlspecialchars($price) . '</p>';
    echo '<p>' . htmlspecialchars($description) . '</p>';
    echo '<a href="add_to_cart.php?id=' . $productId . '" class="btn btn-primary">Add to Cart</a>';
    echo '</div>';
}

$conn->close();
?>
