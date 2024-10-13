<?php
// Database connection parameters
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "cloth";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $securityQuestion = $_POST['security_question'];
    $securityAnswer = $_POST['security_answer'];

    // Prepare and execute SQL query to fetch user data
    $sql = "SELECT security_question, security_answer FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedQuestion, $storedAnswer);
        $stmt->fetch();

        // Verify security question and answer
        if ($securityQuestion === $storedQuestion && password_verify($securityAnswer, $storedAnswer)) {
            // Redirect to password reset page
            header("Location: reset_password.php?email=" . urlencode($email));
            exit();
        } else {
            $error = "Incorrect security question or answer.";
        }
    } else {
        $error = "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
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
        <h2 class="text-center">Forgot Password</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="security_question">Security Question</label>
                <select name="security_question" id="security_question" class="form-control" required>
                  <option value="">Select a security question</option>
                    <option value="What is your mother's maiden name?">What is your mother's maiden name?</option>
                    <option value="What was the name of your first pet?">What was the name of your first pet?</option>
                    <option value="What was your first car?">What was your first car?</option>
                    <option value="What elementary school did you attend?">What elementary school did you attend?</option>
                    <option value="What is the name of the town where you were born?">What is the name of the town where you were born?</option>
                    <!-- Add other questions here if needed -->
                </select>
            </div>
            <div class="form-group">
                <label for="security_answer">Security Answer</label>
                <input type="text" name="security_answer" id="security_answer" class="form-control" required>
            </div>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
</body>
</html>


