<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: admin_login.php");
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

// Handle delete staff
if (isset($_GET['delete'])) {
    $staff_id = $_GET['delete'];
    $delete_sql = "DELETE FROM users WHERE id = ? AND role_id = 3"; 
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $staff_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_staff.php");
    exit();
}

// Fetch staff members
$sql = "SELECT id, full_name, email, created_at FROM users WHERE role_id = 3";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff</title>
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

        .header {
            background-color: #1f1f1f;
            padding: 15px 20px;
            border-bottom: 1px solid #333;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
        }

        .sticky-nav {
            position: sticky;
            top: 0;
            background-color: #1f1f1f;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
        }

        .sticky-nav a {
            color: #00ccff;
            text-decoration: none;
            padding: 10px 20px;
        }

        .profile-icon {
            display: flex;
            align-items: center;
            position: relative;
            cursor: pointer;
        }

        .profile-icon img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .dropdown {
            position: absolute;
            display: none;
            background-color: #1f1f1f;
            min-width: 160px;
            border-radius: 5px;
            top: 40px;
            right: 0;
            z-index: 1;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .container {
            margin-top: 50px;
            background-color: rgba(31, 31, 31, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        }

        table {
            background-color: #1f1f1f;
            color: #ffffff;
            border-radius: 10px;
        }

        table th, table td {
            border-bottom: 1px solid #444;
            color: #ffffff;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        h2, th, td {
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Manage Staff</h1>
</div>

<div class="sticky-nav">
    <a href="admin_home.php">Home</a>
    <div class="profile-icon" id="profileDropdown">
        <img src="../uploads/profile.jpg" alt="Profile">
        <span><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
        <div class="dropdown" id="dropdownMenu">
            <div class="dropdown-content">
                <a href="manage_categories.php">Manage Categories</a>
                <a href="manage_products.php">Manage Products</a>
                <a href="manage_staff.php">Manage Staff</a>
                <a href="manage_customers.php">Manage Customers</a>
                <a href="sales_reports.php">Sales Reports</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <h2 class="text-center">Staff Members</h2>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Staff ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a href="edit_staff.php?id=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                            <a href="manage_staff.php?delete=<?php echo $row['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this staff member?');">Remove</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No staff members found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='admin_home.php';" class="btn btn-secondary">Back to Admin Home</button>
</div>

<script>
    document.getElementById('profileDropdown').addEventListener('click', function () {
        const dropdown = document.getElementById('dropdownMenu');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    });

    document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('dropdownMenu');
        if (!document.getElementById('profileDropdown').contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });
</script>

</body>
</html>
