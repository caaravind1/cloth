<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cloth";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch staff details
$staff_id = $_GET['id']; // Ensure this is set and passed correctly
if (!$staff_id) {
    echo "Invalid staff ID.";
    exit();
}

$sql = "SELECT full_name, email FROM users WHERE id = ? AND role_id = 3"; // Ensure only staff members are edited
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $staff_id);
$stmt->execute();
$stmt->bind_result($full_name, $email);
$stmt->fetch();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_full_name = $_POST['full_name'];
    $new_email = $_POST['email'];

    $update_sql = "UPDATE users SET full_name = ?, email = ? WHERE id = ? AND role_id = 3";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssi", $new_full_name, $new_email, $staff_id);

    if ($stmt->execute()) {
        // Redirect after successful update to avoid re-submission on refresh
        header("Location: manage_staff.php");
        exit();
    } else {
        echo "Error updating staff: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Staff</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        .container {
            max-width: 500px;
            margin: 50px auto;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
        }
        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Staff</h2>
        <form method="POST" action="edit_staff.php?id=<?php echo $staff_id; ?>">
            <div class="form-group">
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="full_name" class="form-control" value="<?php echo htmlspecialchars($full_name); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Staff</button>
        </form>
        <button onclick="window.location.href='manage_staff.php';" class="btn btn-secondary mt-3">Back to Manage Staff</button>
    </div>
</body>
</html>
	