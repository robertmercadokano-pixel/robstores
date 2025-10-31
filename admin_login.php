<?php
session_start();
include 'db_connect.php';

$error = '';

// --- LOGIN ---
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $username = $conn->real_escape_string($username);

    $sql = "SELECT * FROM users WHERE username='$username' AND role='admin' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Admin not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
<style>
body { font-family:sans-serif; background:#f4f4f4; display:flex; justify-content:center; align-items:center; height:100vh; }
.login-box { background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 10px rgba(0,0,0,0.3); width:350px; text-align:center; }
input[type=text], input[type=password] { width:100%; padding:10px; margin:10px 0; border-radius:6px; border:1px solid #ccc; }
button { width:100%; padding:10px; border:none; border-radius:6px; background:#1a1a1a; color:#fff; font-weight:bold; cursor:pointer; transition:0.3s; margin-top:10px; }
button:hover { background:#333; }
.error { background:#f8d7da; color:#721c24; padding:10px; border-radius:6px; margin-bottom:10px; }
.create-btn { background:#007bff; }
.create-btn:hover { background:#0056b3; }
.show-password { cursor:pointer; font-size:0.9em; color:#007bff; margin-top:-10px; display:block; text-align:right; }
</style>
</head>
<body>

<div class="login-box">
<h2>Admin Login</h2>
<?php if($error) echo "<div class='error'>$error</div>"; ?>
<form method="POST">
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" id="login_password" required>
    <span class="show-password" onclick="togglePassword('login_password')">Show/Hide Password</span>
    <button type="submit" name="login">Login</button>
</form>
<!-- Button to go to Create Account page -->
<form method="GET" action="create_admin_account.php">
    <button type="submit" class="create-btn">Create Admin Account</button>
</form>
</div>

<script>
function togglePassword(id) {
    var x = document.getElementById(id);
    if (x.type === "password") x.type = "text";
    else x.type = "password";
}
</script>

</body>
</html>
