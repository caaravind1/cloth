<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if the admin is already registered
    $sql = "SELECT id FROM users WHERE email = ? AND role_id = 2"; // Assuming role_id 2 is for Admin
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Admin already registered, redirect to login page
        header("Location: admin_login.php");
        exit();
    } else {
        // Continue with the registration process
        $fullName = $_POST['full_name'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $securityQuestion = $_POST['security_question'];
        $securityAnswer = $_POST['security_answer'];

        $sql = "INSERT INTO users (full_name, email, password, role_id, security_question, security_answer) VALUES (?, ?, ?, 2, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $fullName, $email, $password, $securityQuestion, $securityAnswer);

        if ($stmt->execute()) {
            echo "Registration successful. You can now <a href='admin_login.php'>login</a>.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 500px;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Admin Registration</h2>
        <form method="POST" action="admin_register.php">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="security_question">Security Question</label>
                <select class="form-control" id="security_question" name="security_question" required>
                    <option value="Your first pet's name?">Your first pet's name?</option>
                    <option value="Your mother's maiden name?">Your mother's maiden name?</option>
                    <option value="Your favorite teacher's name?">Your favorite teacher's name?</option>
                    <!-- Add more questions as needed -->
                </select>
            </div>
            <div class="form-group">
                <label for="security_answer">Answer</label>
                <input type="text" class="form-control" id="security_answer" name="security_answer" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="text-center mt-3">
            Already registered? <a href="admin_login.php">Login here</a>
        </p>
    </div>
</body>
</html>
