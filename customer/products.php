<?php
session_start();

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

// Fetch products from the database
$sql = "SELECT id, name, price, description, image FROM products";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products</title>
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
            margin-top: 50px;
            border-radius: 20px;
            background-color: #1f1f1f; /* Darker container background */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            padding: 20px;
            max-width: 1200px; /* Increased width for larger displays */
            margin: 50px auto;
        }

        h1 {
            font-size: 2.5rem;
            color: #ffffff; /* White for headings */
            font-weight: bold;
            margin-bottom: 40px;
        }

        .card {
            margin-bottom: 30px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card-header {
            background-color: #3a3a3c;
            border-bottom: none;
            font-size: 1.25rem;
            font-weight: bold;
            color: #ffffff; /* White for card header */
            text-align: center;
            padding: 15px;
        }

        .card-body {
            padding: 20px;
        }

        .product-img {
            width: 100%; /* Make the image take the full width of the container */
            height: auto; /* Maintain aspect ratio */
            object-fit: cover; /* Ensure the image covers the area without distortion */
            border-radius: 10px; /* Rounded corners */
            margin-bottom: 20px; /* Space below the image */
            display: block; /* Ensures the image is centered */
            margin-left: auto; /* Center the image horizontally */
            margin-right: auto; /* Center the image horizontally */
        }

        p {
            font-size: 1rem;
            color: #cccccc; /* Light gray for descriptions */
        }

        .price {
            font-size: 1.2rem;
            color: #00ccff; /* Bright blue price for contrast */
            font-weight: bold; /* Bold for price */
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 25px; /* Increased for oval shape */
            padding: 10px 30px; /* Adjusted padding for a better oval shape */
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }

        .row {
            justify-content: center;
        }

        @media (max-width: 768px) {
            .product-img {
                height: 200px; /* Adjust height for smaller screens */
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Browse Our Products</h1>
        <div class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header"><?php echo htmlspecialchars($row['name']); ?></div>
                            <div class="card-body">
                                <?php if (!empty($row['image'])): ?>
                                    <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" class="product-img">
                                <?php else: ?>
                                    <img src="../uploads/default.jpg" alt="Default Image" class="product-img">
                                <?php endif; ?>
                                <p class="price">$<?php echo htmlspecialchars($row['price']); ?></p>
                                <p><?php echo htmlspecialchars($row['description']); ?></p>
                                <a href="product_details.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center">No products found.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
