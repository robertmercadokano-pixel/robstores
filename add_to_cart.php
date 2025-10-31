<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";

if (isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $quantity = $_POST['quantity'];

    // Check if product already exists in cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + $quantity;

        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=?");
        $update->bind_param("ii", $new_quantity, $row['cart_id']);
        $update->execute();
        $message = "{$product_name} quantity updated in your cart!";
    } else {
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, product_price, quantity) VALUES (?, ?, ?, ?, ?)");
        $insert->bind_param("iisdi", $user_id, $product_id, $product_name, $product_price, $quantity);
        $insert->execute();
        $message = "{$product_name} added to your cart!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart Update - Robs Bagstore</title>
<link rel="stylesheet" href="style.css">
<style>
body {
    font-family: 'Arial', sans-serif;
    background: #f8f8f8;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin:0;
    padding:20px;
}

.cart-update-card {
    background:#fff;
    border-radius:15px;
    box-shadow:0 8px 30px rgba(0,0,0,0.1);
    width:400px;
    max-width:95%;
    text-align:center;
    padding:25px 20px;
    display:flex;
    flex-direction:column;
    align-items:center;
}

.cart-update-card h2 {
    font-size:24px;
    font-weight:600;
    margin-bottom:15px;
    color:#222;
}

.cart-update-card p {
    font-size:16px;
    color:#555;
    margin-bottom:25px;
}

.cart-update-card .buttons {
    display:flex;
    gap:10px;
    width:100%;
    flex-wrap:wrap;
    justify-content:center;
}

.cart-update-card .buttons a {
    flex:1;
    text-align:center;
    padding:12px 0;
    border-radius:10px;
    background:#ff3b30;
    color:#fff;
    font-weight:600;
    transition:0.3s;
}

.cart-update-card .buttons a:hover {
    background:#e63228;
}

/* Optional product image */
.cart-update-card .product-img {
    width:100px;
    height:100px;
    border-radius:12px;
    object-fit:cover;
    margin-bottom:15px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}
</style>
</head>
<body>

<div class="cart-update-card">
    <!-- Optional: product image -->
    <img src="product_images/<?php echo isset($product_id)?$product_id:'placeholder'; ?>.jpg" alt="Product Image" class="product-img">
    <h2>Cart Update</h2>
    <p><?php echo htmlspecialchars($message); ?></p>
    <div class="buttons">
        <a href="home.php">Continue Shopping</a>
        <a href="cart.php">View Cart</a>
    </div>
</div>

</body>
</html>
