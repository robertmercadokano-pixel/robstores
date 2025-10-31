<?php
session_start();
include 'db_connect.php';

$error = '';
$success = '';

if (isset($_POST['create_account'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already exists!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = 'admin';

            $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $role);
            if ($stmt->execute()) {
                $success = "Admin account created successfully!";
            } else {
                $error = "Failed to create account!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Create Admin Account</title>
<style>
body { font-family:sans-serif; background:#f4f4f4; display:flex; justify-content:center; align-items:center; height:100vh; }
.container { background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.3); width:350px; text-align:center; }
input[type=text], input[type=password] { width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #ccc; }
button { width:100%; padding:10px; border:none; border-radius:6px; background:#1a1a1a; color:#fff; font-weight:bold; cursor:pointer; transition:0.3s; }
button:hover { background:#333; }
.error { background:#f8d7da; color:#721c24; padding:10px; border-radius:6px; margin-bottom:10px; }
.success { background:#d4edda; color:#155724; padding:10px; border-radius:6px; margin-bottom:10px; }
.show-password { cursor:pointer; font-size:0.9em; color:#007bff; margin-top:-10px; display:block; text-align:right; }
</style>
</head>
<body>

<div class="container">
<h2>Create Admin Account</h2>
<?php if($error) echo "<div class='error'>$error</div>"; ?>
<?php if($success) echo "<div class='success'>$success</div>"; ?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" id="password" required>
    <input type="password" name="confirm_password" placeholder="Confirm Password" id="confirm_password" required>
    <span class="show-password" onclick="togglePassword('password')">Show/Hide Password</span>
    <span class="show-password" onclick="togglePassword('confirm_password')">Show/Hide Confirm</span>
    <button type="submit" name="create_account">Create Account</button>
</form>
<p><a href="admin_login.php">Back to Login</a></p>
</div>

<script>
function togglePassword(id) {
    var x = document.getElementById(id);
    x.type = (x.type === "password") ? "text" : "password";
}
</script>

</body>
</html>
