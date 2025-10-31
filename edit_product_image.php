<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle image upload
if (isset($_POST['update_image'])) {
    $product_id = intval($_POST['product_id']);
    $file = $_FILES['product_image'];

    if ($file['error'] === 0) {
        $targetDir = "images/"; // Folder to store uploaded images
        $filename = basename($file['name']);
        $targetFile = $targetDir . $filename;

        // Move uploaded file to images folder
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            // Update database
            $stmt = $conn->prepare("UPDATE products SET image = ? WHERE product_id = ?");
            $stmt->bind_param("si", $targetFile, $product_id);
            if ($stmt->execute()) {
                $success = "Image updated successfully!";
            } else {
                $error = "Database update failed.";
            }
        } else {
            $error = "Failed to move uploaded file.";
        }
    } else {
        $error = "No file uploaded or upload error.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Product Image - Admin</title>
<style>
body { font-family: sans-serif; padding:20px; background:#f4f4f4; }
.container { max-width:600px; margin:auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.2);}
h2 { text-align:center; }
form { display:flex; flex-direction:column; gap:10px; }
input[type=file] { padding:6px; }
button { padding:8px; background:#1a1a1a; color:#fff; border:none; border-radius:6px; cursor:pointer; }
button:hover { background:#333; }
img { max-width:200px; margin-top:10px; border-radius:6px; }
.message { padding:10px; border-radius:6px; margin-bottom:10px; }
.success { background:#d4edda; color:#155724; }
.error { background:#f8d7da; color:#721c24; }
</style>
</head>
<body>

<div class="container">
<h2>Edit Product Image</h2>

<?php
if (isset($success)) echo "<div class='message success'>$success</div>";
if (isset($error)) echo "<div class='message error'>$error</div>";
?>

<form method="POST" enctype="multipart/form-data">
    <label>Select Product:</label>
    <select name="product_id" required>
        <option value="">--Choose Product--</option>
        <?php
        $res = $conn->query("SELECT product_id, name, image FROM products");
        while ($row = $res->fetch_assoc()) {
            $imgPreview = htmlspecialchars($row['image']);
            echo "<option value='{$row['product_id']}' data-image='$imgPreview'>{$row['name']}</option>";
        }
        ?>
    </select>

    <label>Choose New Image:</label>
    <input type="file" name="product_image" accept="image/*" required>

    <button type="submit" name="update_image">Update Image</button>
</form>

<div id="preview"></div>

<script>
// Show image preview when a product is selected
const select = document.querySelector('select[name="product_id"]');
const preview = document.getElementById('preview');

select.addEventListener('change', function() {
    const imgPath = this.selectedOptions[0].dataset.image;
    if (imgPath) {
        preview.innerHTML = "<strong>Current Image:</strong><br><img src='" + imgPath + "'>";
    } else {
        preview.innerHTML = "";
    }
});
</script>

</div>

</body>
</html>
