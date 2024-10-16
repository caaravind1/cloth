<?php
session_start();

$conn = new mysqli("localhost", "root", "", "cloth");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch product details
if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT id, name, price, description, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();

    // Fetch reviews for the product
    $review_stmt = $conn->prepare("SELECT r.rating, r.comment, u.full_name 
                                   FROM reviews r 
                                   JOIN users u ON r.user_id = u.id 
                                   WHERE r.product_id = ?");
    $review_stmt->bind_param("i", $product_id);
    $review_stmt->execute();
    $reviews_result = $review_stmt->get_result();
} else {
    die("Invalid product ID.");
}

// Handle Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $quantity = 1;
        $cart_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $cart_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $cart_stmt->execute();
    } else {
        header('Location: customer_login.php');
        exit();
    }
}

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_review'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    $review_stmt = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
    $review_stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
    $review_stmt->execute();
    header("Location: product_details.php?id=$product_id"); // Refresh page
    exit();
}

$conn->close();

$cart_access = isset($_SESSION['user_id']);
$user_name = $cart_access && isset($_SESSION['full_name']) ? $_SESSION['full_name'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Product Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background-image: url('../uploads/mainbg.jpg'); background-size: cover; min-height: 100vh; color: #fff; }
        .header, .sticky-nav { background-color: #1f1f1f; padding: 15px; text-align: center; border-bottom: 1px solid #333; }
        .sticky-nav { position: sticky; top: 0; display: flex; justify-content: space-between; align-items: center; z-index: 1000; }
        .sticky-nav a { color: #00ccff; padding: 10px 20px; text-decoration: none; }
        .sticky-nav a:hover { background-color: rgba(255, 255, 255, 0.1); }
        .profile-icon { display: flex; align-items: center; cursor: pointer; }
        .profile-icon img { width: 30px; height: 30px; border-radius: 50%; margin-right: 5px; }
        .dropdown { position: absolute; display: none; background-color: #1f1f1f; min-width: 160px; border-radius: 5px; top: 50px; right: 0; z-index: 1; }
        .dropdown-content a { color: white; padding: 12px 16px; text-decoration: none; display: block; }
        .dropdown-content a:hover { background-color: rgba(255, 255, 255, 0.1); }
        .container { margin-top: 50px; background-color: #1f1f1f; border-radius: 10px; padding: 20px; max-width: 600px; }
        .product-img { max-width: 100%; height: auto; border-radius: 10px; }
        .price { font-size: 1.5rem; color: #00ccff; }
        .review { background-color: #2c2c2e; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        /* Dark mode styling for text area and select dropdown */
        .form-control {
            background-color: #333;
            color: #ffffff;
            border: 1px solid #555;
        }
        .form-control:focus {
            background-color: #444;
            color: #ffffff;
            border-color: #00ccff;
            box-shadow: none;
        }
    </style>
</head>
<body>

<div class="header">
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
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
    <img src="../uploads/<?php echo htmlspecialchars($product['image']); ?>" class="product-img" alt="<?php echo htmlspecialchars($product['name']); ?>">
    <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
    <p><?php echo htmlspecialchars($product['description']); ?></p>

    <form method="POST">
        <button type="submit" name="add_to_cart" class="btn btn-primary">Add to Cart</button>
    </form>

    <div class="review-section">
        <h3>Customer Reviews</h3>
        <?php if ($reviews_result->num_rows > 0): ?>
            <?php while ($review = $reviews_result->fetch_assoc()): ?>
                <div class="review">
                    <strong><?php echo htmlspecialchars($review['full_name']); ?></strong>
                    <p>Rating: <?php echo htmlspecialchars($review['rating']); ?> ★</p>
                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No reviews yet. Be the first to review!</p>
        <?php endif; ?>

        <?php if ($cart_access): ?>
            <form method="POST" class="mt-4">
                <div class="form-group">
                    <label for="rating">Rating (1-5):</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="1">1 ★</option>
                        <option value="2">2 ★</option>
                        <option value="3">3 ★</option>
                        <option value="4">4 ★</option>
                        <option value="5">5 ★</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Your Review:</label>
                    <textarea name="comment" id="comment" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" name="post_review" class="btn btn-primary">Post Review</button>
            </form>
        <?php endif; ?>
    </div>
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
