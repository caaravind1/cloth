<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id']) && isset($_SESSION['user_id'])) {
    $product_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Insert the product into the cart
    $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $user_id, $product_id);

    if ($stmt->execute()) {
        // Set success message in session
        $_SESSION['cart_message'] = "Product successfully added to cart!";
    } else {
        // Set error message if the insertion fails
        $_SESSION['cart_message'] = "Failed to add product to cart.";
    }

    // Redirect back to browse_products.php
    header("Location: browse_products.php");
    exit();
} else {
    // Redirect to login page if the user is not logged in
    header("Location: customer_login.php");
    exit();
}
?>
