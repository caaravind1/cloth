<?php
session_start();

// Remove product from the cart
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]); // Remove the product
    }

    // Redirect back to the cart page
    header("Location: cart.php");
    exit();
}
?>
