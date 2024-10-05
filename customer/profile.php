<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php');
    exit();
}

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

// Fetch user details from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT full_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Process form submission to update details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    // Update user details in the database
    $update_sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $full_name, $email, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['full_name'] = $full_name; // Update session name if changed
        $success_message = "Profile updated successfully!";
        // Refresh user data
        $user['full_name'] = $full_name;
        $user['email'] = $email;
    } else {
        $error_message = "Error updating profile.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #121212; /* Dark background */
            color: #ffffff; /* Light text */
        }

        .container {
            margin-top: 50px;
            background-color: #1f1f1f;
            border-radius: 10px;
            padding: 20px;
            max-width: 600px;
            margin: 50px auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        .form-group label {
            color: #cccccc; /* Light label text */
        }

        .form-control {
            background-color: #333;
            border: none;
            color: #fff;
        }

        .form-control:focus {
            background-color: #444;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Profile</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
    </form>
</div>

</body>
</html>
