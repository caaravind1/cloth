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

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: customer_login.php'); // Redirect to login if not logged in
    exit();
}

// Fetch current user details from the database
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT full_name, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    // Update user details in the database
    $update_sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $full_name, $email, $user_id);

    if ($update_stmt->execute()) {
        $_SESSION['full_name'] = $full_name; // Update the session variable
        header('Location: profile.php'); // Redirect to profile.php after successful update
        exit(); // Ensure the script stops here
    } else {
        $error_message = "Error updating profile: " . $conn->error;
    }
}

// Close the database connection
$conn->close();

// Check if user is logged in
$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
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
            min-width: 160px; 
            border-radius: 5px; /* Rounded corners */
            top: 40px; /* Move dropdown below the profile icon */
            right: 0; /* Align dropdown to the right of the profile icon */
            z-index: 1;
        }
        .dropdown-content {
            display: block; /* Make dropdown block element */
        }
        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block; /* Display as block for vertical stacking */
        }
        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1); /* Hover effect */
        }
        .container {
            margin-top: 20px;
            background-color: #1f1f1f; /* Darker container */
            border-radius: 10px;
            padding: 30px;
            max-width: 500px; /* Set max width for the container */
            margin: 50px auto; /* Center the container */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5); /* Soft shadow */
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px; /* Oval shape */
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Update Profile</h1>
</div>

<div class="sticky-nav">
    <a href="browse_products.php">Home</a>
    <div class="profile-icon" id="profileDropdown">
        <img src="../uploads/profile.jpg" alt="Profile">
        <span class="profile-name"><?php echo htmlspecialchars($user_name); ?></span>
        <div class="dropdown" id="dropdownMenu">
            <div class="dropdown-content">
                <a href="profile.php">View Profile</a>
                <a href="orders.php">My Orders</a>
                <a href="cart.php">Cart</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <h2 class="text-center">User Details</h2>

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
