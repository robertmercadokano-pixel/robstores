<?php
include 'db_connect.php';

$search = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;

$query = "SELECT * FROM products WHERE name LIKE '%$search%' LIMIT $limit";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $name = htmlspecialchars($row['name']);
        $price = number_format($row['price'], 2);
        $image = htmlspecialchars($row['image']);
        $product_id = $row['product_id'];
        echo "
        <div class='product'>
            <img src='images/$image' alt='$name'>
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
    echo "<p style='grid-column:1/-1;text-align:center;color:#555;'>No products found.</p>";
}
?>
