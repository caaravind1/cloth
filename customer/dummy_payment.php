<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if order_id is present in the POST request
if (!isset($_POST['order_id'])) {
    echo "No order specified.";
    exit();
}

$order_id = $_POST['order_id'];

// Create connection
$conn = new mysqli("localhost", "root", "", "cloth");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simulate payment processing
// In a real application, you would integrate with a payment gateway here.
$payment_successful = true; // Simulating a successful payment

if ($payment_successful) {
    // Update the order status to "Paid"
    $sql = "UPDATE orders SET status = 'Paid' WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $user_id);
    if ($stmt->execute()) {
        $order_status = 'Paid';
    } else {
        echo "Failed to update order status.";
        exit();
    }
} else {
    echo "Payment failed. Please try again.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Confirmation</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('../uploads/mainbg.jpg'); 
            background-size: cover; 
            background-position: center; 
            background-repeat: no-repeat; 
            background-attachment: fixed; 
            color: #ffffff; 
            min-height: 100vh; 
            margin: 0; 
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
        }

        .sticky-nav a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Light hover effect */
        }

        .profile-icon {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer; /* Change cursor to pointer for hover */
        }

        .profile-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
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
            max-width: 800px; /* Increased width for larger displays */
            margin: 50px auto;
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
    <h1>Payment Confirmation</h1>
</div>

<div class="sticky-nav">
    <a href="browse_products.php">Home</a>
    <div class="login-signup">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="profile-icon" id="profileDropdown">
                <img src="../uploads/profile.jpg" alt="Profile">
                <span class="profile-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
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
    <h2 class="text-center">Thank You!</h2>
    <p>Your payment was successful!</p>
    <p>Your order status is now: <strong><?php echo htmlspecialchars($order_status); ?></strong></p>
    <p>You will receive a confirmation email shortly.</p>
    <div class="text-center">
        <a href="orders.php" class="btn btn-primary">View Your Orders</a>
        <a href="browse_products.php" class="btn btn-primary">Continue Shopping</a>
    </div>
</div>

<script>
    document.getElementById('profileDropdown').addEventListener('click', function() {
        const dropdown = document.getElementById('dropdownMenu');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdownMenu');
        if (!document.getElementById('profileDropdown').contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

</body>
</html>
