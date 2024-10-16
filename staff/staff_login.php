<?php
session_start();

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
    $password = $_POST['password'];

    // Prepare and execute SQL query to fetch user data
    $sql = "SELECT id, full_name, password FROM users WHERE email = ? AND role_id = 3"; // role_id 3 for staff
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $fullName, $hashedPassword);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            // Start session and store user data
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['full_name'] = $fullName;
            $_SESSION['role_id'] = 3;

            // Redirect to staff home page
            header("Location: staff_dashboard.php");
            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "No account found with that email.";
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
    <title>Staff Login</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
          body {
            font-family: 'Poppins', sans-serif;
            background-image: url('../uploads/staffbg.jpg'); /* Add a background image for the staff home */
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
            background-color: rgba(31, 31, 31, 0.9); /* Dark, semi-transparent background */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        h2 {
            color: #ffffff; /* White text for heading */
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            color: #ffffff; /* White text for labels */
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #444; /* Darker border for input */
            padding: 10px;
            font-size: 16px;
            background-color: #222; /* Dark background for inputs */
            color: #ffffff; /* White text for inputs */
        }

        .btn {
            background-color: #007bff; /* Button background */
            color: #ffffff; /* Button text color */
            border: none;
            border-radius: 4px;
            padding: 10px 15px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3; /* Darker button on hover */
        }

        .text-danger {
            color: #dc3545; /* Danger text color */
        }

        .text-center {
            color: #ffffff; /* White text for links */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Staff Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="staff_login.php" method="POST">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p class="text-center mt-3">
            Don't have an account? <a href="staff_register.php" style="color: #00ccff;">Register here</a>
        </p>
    </div>
</body>
</html>
