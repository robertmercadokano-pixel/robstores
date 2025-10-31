<?php
session_start();
include 'db_connect.php';

// Protect page: only admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// --- Add Product ---
if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $file = $_FILES['image'];

    if ($file['error'] === 0) {
        $targetDir = "images/";
        $filename = basename($file['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $stmt = $conn->prepare("INSERT INTO products (name, price, image) VALUES (?, ?, ?)");
            $stmt->bind_param("sds", $name, $price, $targetFile);
            $stmt->execute();
        }
    }
}

// --- Update Product ---
if (isset($_POST['update_product'])) {
    $id = intval($_POST['product_id']);
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE products SET name=?, price=? WHERE product_id=?");
    $stmt->bind_param("sdi", $name, $price, $id);
    $stmt->execute();

    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $file = $_FILES['image'];
        $targetDir = "images/";
        $filename = basename($file['name']);
        $targetFile = $targetDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $res = $conn->query("SELECT image FROM products WHERE product_id=$id");
            if ($res && $res->num_rows > 0) {
                $row = $res->fetch_assoc();
                if (file_exists($row['image'])) unlink($row['image']);
            }
            $stmt = $conn->prepare("UPDATE products SET image=? WHERE product_id=?");
            $stmt->bind_param("si", $targetFile, $id);
            $stmt->execute();
        }
    }
}

// --- Delete Product ---
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $conn->query("SELECT image FROM products WHERE product_id=$id");
    if ($res && $res->num_rows > 0) {
        $row = $res->fetch_assoc();
        if (file_exists($row['image'])) unlink($row['image']);
    }
    $conn->query("DELETE FROM products WHERE product_id=$id");
}

// --- Update Order Status ---
if (isset($_POST['update_order'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $valid_status = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
    if (in_array($status, $valid_status)) {
        $stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
        $stmt->bind_param("si", $status, $order_id);
        $stmt->execute();
    }
}

// --- Fetch data ---
$products = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
$orders = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id = u.user_id ORDER BY order_date DESC");
$users = $conn->query("SELECT user_id, username, role FROM users ORDER BY username ASC");

// Fetch recent activity
$activity = $conn->query("SELECT a.*, u.username FROM user_activity a JOIN users u ON a.user_id = u.user_id ORDER BY timestamp DESC LIMIT 20");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<style>
body { font-family:sans-serif; background:#f4f4f4; margin:0; }
header { background:#1a1a1a; color:#fff; padding:15px; display:flex; justify-content:space-between; align-items:center; }
header a { color:#fff; text-decoration:none; margin-left:20px; }
.container { max-width:1200px; margin:20px auto; padding:10px; }
.tabs { display:flex; background:#fff; border-radius:8px; overflow:hidden; margin-bottom:20px; box-shadow:0 2px 6px rgba(0,0,0,0.2); }
.tab { flex:1; text-align:center; padding:12px; cursor:pointer; background:#eee; transition:0.3s; font-weight:bold; }
.tab.active { background:#1a1a1a; color:#fff; }
.tab-content { display:none; }
.tab-content.active { display:block; }
form { display:flex; flex-wrap:wrap; gap:10px; margin-bottom:20px; background:#fff; padding:15px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.2); }
form input, form button, form select { padding:10px; border-radius:6px; border:1px solid #ccc; }
form button { background:#1a1a1a; color:#fff; border:none; cursor:pointer; }
table { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.2); margin-bottom:30px; }
th, td { padding:12px; border-bottom:1px solid #ccc; text-align:center; }
th { background:#1a1a1a; color:#fff; }
img { width:80px; height:80px; object-fit:cover; border-radius:6px; }
a.action { padding:6px 10px; border-radius:6px; color:#fff; text-decoration:none; margin:2px; }
a.edit { background:#007bff; }
a.delete { background:#dc3545; }
h3 { margin-top:40px; }
</style>
</head>
<body>

<header>
<h2>Admin Dashboard</h2>
<div><a href="logout.php">Logout</a></div>
</header>

<div class="container">

<div class="tabs">
<div class="tab active" data-tab="products">Product List</div>
<div class="tab" data-tab="orders">Orders</div>
<div class="tab" data-tab="users">Users List</div>
<div class="tab" data-tab="activity">Recent User Activity</div>
</div>

<!-- Product List -->
<div class="tab-content active" id="products">
<h3>Add New Product</h3>
<form method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="number" name="price" step="0.01" placeholder="Price" required>
    <input type="file" name="image" accept="image/*" required>
    <button type="submit" name="add_product">Add Product</button>
</form>

<h3>Products</h3>
<table>
<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Price</th>
<th>Actions</th>
</tr>
<?php while($row = $products->fetch_assoc()): ?>
<tr>
<td><?php echo $row['product_id']; ?></td>
<td><img src="<?php echo htmlspecialchars($row['image']); ?>" alt=""></td>
<td><?php echo htmlspecialchars($row['name']); ?></td>
<td>₱<?php echo number_format($row['price'],2); ?></td>
<td>
<form method="POST" enctype="multipart/form-data" style="display:inline-block;">
<input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
<input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
<input type="number" step="0.01" name="price" value="<?php echo $row['price']; ?>" required>
<input type="file" name="image" accept="image/*">
<button type="submit" name="update_product">Update</button>
</form>
<a class="action delete" href="?delete=<?php echo $row['product_id']; ?>" onclick="return confirm('Delete this product?');">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- Orders -->
<div class="tab-content" id="orders">
<h3>Orders</h3>
<table>
<tr>
<th>Order ID</th>
<th>User</th>
<th>Total</th>
<th>Status</th>
<th>Action</th>
</tr>
<?php while($order = $orders->fetch_assoc()): ?>
<tr>
<td><?php echo $order['order_id']; ?></td>
<td><?php echo htmlspecialchars($order['username']); ?></td>
<td>₱<?php echo number_format($order['total'],2); ?></td>
<td>
<form method="POST">
<input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
<select name="status">
<option value="Pending" <?php if($order['status']=='Pending') echo 'selected'; ?>>Pending</option>
<option value="Processing" <?php if($order['status']=='Processing') echo 'selected'; ?>>Processing</option>
<option value="Shipped" <?php if($order['status']=='Shipped') echo 'selected'; ?>>Shipped</option>
<option value="Delivered" <?php if($order['status']=='Delivered') echo 'selected'; ?>>Delivered</option>
<option value="Cancelled" <?php if($order['status']=='Cancelled') echo 'selected'; ?>>Cancelled</option>
</select>
</td>
<td><button type="submit" name="update_order">Update</button></form></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- Users List -->
<div class="tab-content" id="users">
<h3>Users List</h3>
<table>
<tr>
<th>User ID</th>
<th>Username</th>
<th>Role</th>
</tr>
<?php while($user = $users->fetch_assoc()): ?>
<tr>
<td><?php echo $user['user_id']; ?></td>
<td><?php echo htmlspecialchars($user['username']); ?></td>
<td><?php echo $user['role']; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- User Activity -->
<div class="tab-content" id="activity">
<h3>Recent User Activity</h3>
<table>
<tr>
<th>User</th>
<th>Action</th>
<th>Time</th>
</tr>
<?php while($act = $activity->fetch_assoc()): ?>
<tr>
<td><?php echo htmlspecialchars($act['username']); ?></td>
<td><?php echo htmlspecialchars($act['action']); ?></td>
<td><?php echo $act['timestamp']; ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

</div>

<script>
// Tabs functionality
const tabs = document.querySelectorAll('.tab');
const contents = document.querySelectorAll('.tab-content');

tabs.forEach(tab => {
    tab.addEventListener('click', () => {
        tabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const target = tab.dataset.tab;
        contents.forEach(c => c.classList.remove('active'));
        document.getElementById(target).classList.add('active');
    });
});
</script>

</body>
</html>
