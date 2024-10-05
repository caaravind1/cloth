<?php
session_start();

// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "cloth";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user details from the database
    $sql = "SELECT id, full_name, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password (assuming password is hashed in the database)
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];

            // Redirect to the originally requested page or browse_products.php by default
            if (isset($_SESSION['redirect_to']) && strpos($_SESSION['redirect_to'], 'customer_register.php') === false) {
                header('Location: ' . $_SESSION['redirect_to']);
                unset($_SESSION['redirect_to']);
                exit();
            } else {
                header('Location: browse_products.php'); // Default after login
                exit();
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }
    
    $stmt->close();
}

$conn->close();

// Only store the redirect URL if it's not from the login or register page
if (!isset($_SESSION['user_id']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'login.php') === false && strpos($_SERVER['HTTP_REFERER'], 'customer_register.php') === false) {
    $_SESSION['redirect_to'] = $_SERVER['HTTP_REFERER']; // Store last page before login
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212;
            color: #ffffff;
        }
        .container {
            margin-top: 50px;
            background-color: #1f1f1f;
            border-radius: 10px;
            padding: 20px;
            max-width: 400px;
            margin: 50px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Customer Login</h2>
    <form method="POST">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Login</button>
    </form>
    <p class="text-center"><a href="customer_register.php">Create an account</a></p>
</div>

</body>
</html>
