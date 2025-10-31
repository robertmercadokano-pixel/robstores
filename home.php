<?php
session_start();
include 'db_connect.php';

// If not logged in, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Home - Robs Bagstore</title>
<link rel="stylesheet" href="style.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body {
    margin:0;
    font-family:sans-serif;
    background: url('images/bg.png') no-repeat center center fixed;
    background-size: cover;
}
.header {
    display:flex;
    flex-wrap:wrap;
    justify-content:space-between;
    align-items:center;
    background: rgba(26,26,26,0.9);
    color:#fff;
    padding:12px 20px;
    position:sticky;
    top:0;
    z-index:100;
    border-bottom:2px solid #333;
}
.header h2 {
    margin:0;
    font-size:18px;
}
.header input[type=text] {
    padding:6px 10px;
    border-radius:8px;
    border:none;
    flex:1;
    max-width:300px;
    margin:5px 10px;
}
.header .menu a {
    color:#fff;
    text-decoration:none;
    margin-left:12px;
    transition:0.3s;
}
.header .menu a:hover {
    color:#ffcc00;
}
.product-container {
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(220px,1fr));
    gap:20px;
    padding:20px;
}
.product {
    background: rgba(255,255,255,0.95);
    border-radius:12px;
    padding:10px;
    box-shadow:0 2px 6px rgba(0,0,0,0.2);
    text-align:center;
}
.product img {
    width:100%;
    height:200px;
    object-fit:cover;
    border-radius:8px;
}
.product h3 {
    margin:8px 0;
    font-size:16px;
    color:#222;
}
.product p {
    margin:4px 0;
    color:#1a1a1a;
    font-weight:bold;
}
.product form {
    margin-top:8px;
    display:flex;
    flex-direction:column;
    gap:6px;
}
.product input[type=number] {
    width:60px;
    margin:0 auto;
    padding:4px;
    border-radius:6px;
    border:1px solid #ccc;
}
.product button {
    padding:6px;
    border:none;
    border-radius:8px;
    background:#1a1a1a;
    color:#fff;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}
.product button:hover {
    background:#333;
}
@media(max-width:600px){
    .header {
        flex-direction:column;
        align-items:flex-start;
    }
    .header input[type=text]{
        max-width:100%;
        margin:10px 0;
    }
    .product-container{
        grid-template-columns:1fr;
    }
}
</style>
</head>
<body>

<div class="header">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?> 👋</h2>
    <input type="text" id="search" placeholder="Search bags...">
    <div class="menu">
        <a href="cart.php">🛒 Cart</a>
        <a href="profile.php">👤 Profile</a>
        <a href="logout.php">🚪 Logout</a>
    </div>
</div>

<div class="product-container">
<?php
$query = "SELECT * FROM products LIMIT 20";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $price = number_format($row['price'], 2);
        $imagePath = htmlspecialchars($row['image']); // Full path or relative path from DB
        $product_id = $row['product_id'];

        echo "
        <div class='product'>
            <img src='$imagePath' alt='$name'>
            <h3>$name</h3>
            <p>₱$price</p>
            <form method='POST' action='add_to_cart.php'>
                <input type='hidden' name='product_id' value='$product_id'>
                <input type='hidden' name='product_name' value='$name'>
                <input type='hidden' name='product_price' value='{$row['price']}'>
                <input type='number' name='quantity' value='1' min='1'>
                <button type='submit' name='add_to_cart'>Add to Cart</button>
            </form>
        </div>
        ";
    }
} else {
    echo "<p style='grid-column:1/-1;text-align:center;color:#fff;'>No products found.</p>";
}
?>
</div>

<script>
$(document).ready(function(){
    $('#search').on('input', function(){
        let query = $(this).val().trim();
        $.ajax({
            url: 'search_products.php',
            method: 'GET',
            data: {q: query, limit: 20},
            success: function(data){
                $('.product-container').html(data);
            }
        });
    });
});
</script>

</body>
</html>
