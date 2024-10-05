<?php
session_start();

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Assuming login validation here (replace this with actual validation)
    // For example, verify the email and password from the database
    $user_id = 1; // Example user ID from database
    $full_name = "John Doe"; // Example full name from database

    if ($user_id) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['full_name'] = $full_name;

        // Redirect to the page they originally wanted to access
        if (isset($_SESSION['redirect_to'])) {
            header('Location: ' . $_SESSION['redirect_to']);
            unset($_SESSION['redirect_to']);
            exit();
        } else {
            header('Location: browse_products.php'); // Default after login
            exit();
        }
    } else {
        // Invalid login credentials
        echo "Invalid email or password.";
    }
}

if (!isset($_SESSION['user_id']) && isset($_SERVER['HTTP_REFERER'])) {
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
