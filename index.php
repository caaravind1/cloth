<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Index Page</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background:url('uploads/index.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            
        }

        .container {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        h2 {
            font-size: 28px;
            margin-bottom: 40px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .btn {
            background-color: rgba(255, 255, 255, 0.3);
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 16px;
            margin: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .btn img {
            width: 50px; /* Adjust the size of the image */
            height: 50px; /* Adjust the size of the image */
            margin-bottom: 8px;
        }

        .btn:hover {
            background-color: #ffffff;
            color: #2193b0;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.3);
        }

        .btn:focus {
            outline: none;
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Continue as:</h2>
        <a href="customer/browse_products.php" class="btn">
            <img src="uploads/customer.png" alt="Customer">
            Customer
        </a>
        <a href="admin/admin_login.php" class="btn">
            <img src="uploads/admin.png" alt="Admin">
            Admin
        </a>
        <a href="staff/staff_login.php" class="btn">
            <img src="uploads/staff.png" alt="Staff">
            Staff
        </a>
    </div>
</body>
</html>
