<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Create database connection
    $conn = new mysqli("localhost", "root", "", "cloth");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve form data
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];

    // Fetch cart items for the current user
    $sql = "SELECT c.product_id, c.quantity, p.price 
            FROM cart c 
            JOIN products p ON c.product_id = p.id 
            WHERE c.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_result = $stmt->get_result();

    if ($cart_result->num_rows == 0) {
        echo "Your cart is empty.";
        exit();
    }

    // Calculate total amount
    $total_amount = 0;
    $order_items = [];

    while ($row = $cart_result->fetch_assoc()) {
        $product_id = $row['product_id'];
        $quantity = $row['quantity'];
        $price = $row['price'];
        $item_total = $quantity * $price;

        $total_amount += $item_total;

        // Store items for later insertion into order_items
        $order_items[] = [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'price' => $price
        ];
    }

    // Insert the order into the 'orders' table
    $order_sql = "INSERT INTO orders (user_id, total_amount, status, shipping_address, payment_method) 
                  VALUES (?, ?, 'Pending', ?, ?)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->bind_param("idss", $user_id, $total_amount, $shipping_address, $payment_method);
    $order_stmt->execute();

    // Get the inserted order's ID
    $order_id = $conn->insert_id;

    // Insert order items into the 'order_items' table
    $item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                 VALUES (?, ?, ?, ?)";
    $item_stmt = $conn->prepare($item_sql);

    foreach ($order_items as $item) {
        $item_stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $item_stmt->execute();
    }

    // Clear the user's cart after the order is placed
    $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
    $clear_cart_stmt = $conn->prepare($clear_cart_sql);
    $clear_cart_stmt->bind_param("i", $user_id);
    $clear_cart_stmt->execute();

    $conn->close();

    // Redirect to the order confirmation page with the order ID
    header("Location: confirm_order.php?order_id=$order_id");
    exit();
} else {
    echo "Invalid request.";
}
?>
