<?php
// Database connection parameters
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Validate that both passwords match
    if ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match!";
    } else {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Check if the email exists in the database
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Update the password in the database
            $sql = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $hashedPassword, $email);

            if ($stmt->execute()) {
                echo "Password reset successful!";
                header("Location: customer_login.php");
                exit();
            } else {
                $error = "Error updating password: " . $stmt->error;
            }
        } else {
            $error = "Incorrect email ID.";
        }
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 10px;
            font-size: 16px;
        }

        .btn {
            background-color: #007bff;
            color: #ffffff;
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .text-danger {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Reset Password</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="reset_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
        </form>
    </div>
</body>
</html>

