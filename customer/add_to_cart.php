<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    
    // Check if user_id is set in session
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        
        // Insert product into the cart
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $quantity = 1; // Default quantity

        // Bind and execute
        $stmt->bind_param("iii", $user_id, $product_id, $quantity);
        if ($stmt->execute()) {
            $_SESSION['cart_message'] = "Product added to cart!";
        } else {
            $_SESSION['cart_message'] = "Failed to add product to cart.";
        }
        $stmt->close();
    } else {
        $_SESSION['cart_message'] = "Please log in to add items to your cart.";
    }

    header("Location: browse_products.php");
    exit();
}

$conn->close();
?>
