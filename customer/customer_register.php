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
    $fullName = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $securityQuestion = $_POST['security_question'];
    $securityAnswer = $_POST['security_answer'];

    // Basic validation
    if ($password !== $confirmPassword) {
        echo "Passwords do not match!";
        exit();
    }

    // Check if the email already exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "An account with this email already exists!";
        $stmt->close();
        $conn->close();
        exit();
    }

    // Hash the password and security answer
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $hashedSecurityAnswer = password_hash($securityAnswer, PASSWORD_DEFAULT);

    // SQL query to insert data
    $sql = "INSERT INTO users (full_name, email, password, security_question, security_answer, role_id) 
            VALUES (?, ?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $fullName, $email, $hashedPassword, $securityQuestion, $hashedSecurityAnswer);

    if ($stmt->execute()) {
        echo "Registration successful!";
        header("Location: customer_login.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Customer Registration</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #121212; /* Dark background */
            color: #ffffff; /* Light text */
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #1f1f1f; /* Darker container background */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.1);
        }
        h2 {
            color: #ffffff; /* White for headings */
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
            color: #cccccc; /* Light gray for labels */
        }
        .form-control {
            border-radius: 4px;
            border: 1px solid #cccccc; /* Light gray border */
            padding: 10px;
            font-size: 16px;
            background-color: #2c2c2e; /* Darker input background */
            color: #ffffff; /* White text */
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
            background-color: #0056b3; /* Darker blue on hover */
        }
        /* Sub-box for Security Answer */
        #security_answer_group {
            display: none;
            margin-top: 15px;
        }
    </style>
    <script>
        function toggleSecurityAnswer() {
            var question = document.getElementById("security_question").value;
            var answerGroup = document.getElementById("security_answer_group");

            if (question) {
                answerGroup.style.display = "block";
            } else {
                answerGroup.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Customer Registration</h2>
        <form action="customer_register.php" method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="security_question">Security Question</label>
                <select name="security_question" id="security_question" class="form-control" onchange="toggleSecurityAnswer()" required>
                    <option value="">Select a security question</option>
                    <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                    <option value="What was the name of your first pet?">What was the name of your first pet?</option>
                    <option value="What was your first car?">What was your first car?</option>
                    <option value="What elementary school did you attend?">What elementary school did you attend?</option>
                    <option value="What is the name of the town where you were born?">What is the name of the town where you were born?</option>
                </select>
            </div>
            <div id="security_answer_group" class="form-group">
                <label for="security_answer">Answer</label>
                <input type="text" name="security_answer" id="security_answer" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        <p class="text-center mt-3">
            Already have an account? <a href="customer_login.php" style="color: #007bff;">Login here</a>
        </p>
    </div>
</body>
</html>
