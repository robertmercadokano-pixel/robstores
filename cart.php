<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Update quantities
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty));
        $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=? AND user_id=?");
        $stmt->bind_param("iii", $qty, $cart_id, $user_id);
        $stmt->execute();
    }
}

// Remove item
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
}

// Fetch cart items
$stmt = $conn->prepare("SELECT * FROM cart WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Your Cart - Robs Bagstore</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f8f8f8;
    margin: 0;
    padding: 0;
}
.container {
    max-width: 600px;
    margin: 20px auto;
    padding-bottom: 80px; /* space for sticky footer */
}
h2 {
    text-align: center;
    margin: 20px 0;
    color: #222;
}
.cart-item {
    display: flex;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.08);
    margin-bottom: 15px;
    padding: 12px;
    align-items: center;
}
.cart-item img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 10px;
    margin-right: 12px;
}
.item-details {
    flex:1;
    display:flex;
    flex-direction:column;
}
.item-name {
    font-weight:600;
    font-size:16px;
    margin-bottom:5px;
}
.item-price {
    color:#ff3b30;
    font-weight:700;
    margin-bottom:6px;
}
.item-quantity {
    display:flex;
    align-items:center;
    gap:6px;
}
.item-quantity input {
    width:50px;
    padding:4px;
    text-align:center;
    border-radius:6px;
    border:1px solid #ccc;
}
.remove-btn {
    color:#ff3b30;
    font-weight:600;
    cursor:pointer;
    margin-top:6px;
    font-size:14px;
}
.cart-footer {
    position: fixed;
    bottom:0;
    left:0;
    width:100%;
    max-width:600px;
    background:#fff;
    border-top:1px solid #ddd;
    box-shadow:0 -3px 10px rgba(0,0,0,0.05);
    padding:12px 20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-sizing:border-box;
}
.cart-footer .total {
    font-weight:700;
    font-size:18px;
    color:#222;
}
.cart-footer a {
    background:#ff3b30;
    color:#fff;
    padding:10px 18px;
    border-radius:10px;
    font-weight:600;
    text-decoration:none;
    transition:0.3s;
}
.cart-footer a:hover {
    background:#e63228;
}
.empty-cart {
    text-align:center;
    margin-top:50px;
    color:#555;
    font-weight:600;
}
@media(max-width:500px){
    .cart-item {flex-direction:column; align-items:flex-start;}
    .cart-item img {margin-bottom:8px;}
    .cart-footer {flex-direction:column; gap:10px;}
}
</style>
</head>
<body>

<div class="container">
<h2>Your Cart</h2>
<form method="POST">
<?php if($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()):
        $subtotal = $row['product_price'] * $row['quantity'];
        $total += $subtotal;
    ?>
    <div class="cart-item">
        <img src="product_images/<?php echo $row['product_id']; ?>.jpg" alt="<?php echo htmlspecialchars($row['product_name']); ?>">
        <div class="item-details">
            <div class="item-name"><?php echo htmlspecialchars($row['product_name']); ?></div>
            <div class="item-price">₱<?php echo number_format($row['product_price'],2); ?></div>
            <div class="item-quantity">
                <label>Qty:</label>
                <input type="number" name="quantity[<?php echo $row['cart_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1">
                <a class="remove-btn" href="cart.php?remove=<?php echo $row['cart_id']; ?>">Remove</a>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
    <div class="cart-footer">
        <div class="total">Total: ₱<?php echo number_format($total,2); ?></div>
        <a href="checkout.php">Checkout</a>
    </div>
    <button type="submit" name="update_cart" style="display:none;"></button>
<?php else: ?>
    <div class="empty-cart">
        Your cart is empty.<br>
        <a href="home.php" style="color:#ff3b30;">Continue Shopping</a>
    </div>
<?php endif; ?>
</form>
</div>

</body>
</html>
