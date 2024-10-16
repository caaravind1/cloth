<?php
session_start();

// Check if staff is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 3) {
    header("Location: staff_login.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the total number of delivered orders for this staff member
$staff_id = $_SESSION['user_id'];
$total_delivered_query = "SELECT COUNT(*) AS total_delivered FROM orders WHERE assigned_staff_id = ? AND status = 'Delivered'";
$stmt = $conn->prepare($total_delivered_query);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$total_delivered = $stmt->get_result()->fetch_assoc()['total_delivered'];
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-image: url('../uploads/adminbg.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #ffffff;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            margin-top: 50px;
            background-color: rgba(31, 31, 31, 0.8); /* Semi-transparent background for container */
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .card {
            background-color: #1f1f1f;
            border: none;
            color: #ffffff;
        }

        .card-title {
            color: #ffffff;
        }

        .profile-icon {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .profile-icon img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        .logout {
            color: #ffffff;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<!-- Profile Icon with Logout Option -->
<div class="profile-icon">
    <img src="../uploads/profile.jpg" alt="Profile" title="Profile" onclick="document.getElementById('logoutModal').style.display='block'">
</div>

<!-- Logout Modal -->
<div id="logoutModal" class="modal" tabindex="-1" role="dialog" style="display:none;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Logout</h5>
                <button type="button" class="close" onclick="document.getElementById('logoutModal').style.display='none'">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('logoutModal').style.display='none'">Cancel</button>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h2>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Total Orders Delivered</h5>
            <h1><?php echo $total_delivered; ?></h1>
        </div>
    </div>
    <a href="staff_home.php" class="btn btn-primary">View Assigned Orders</a>
</div>

<script>
    // Close the modal when clicking outside of it
    window.onclick = function(event) {
        if (event.target == document.getElementById('logoutModal')) {
            document.getElementById('logoutModal').style.display = "none";
        }
    }
</script>

</body>
</html>
