<?php
session_start();
include 'db_connect.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Insert into user_activity
            $action = "Logged in";
            $stmt2 = $conn->prepare("INSERT INTO user_activity (user_id, action) VALUES (?, ?)");
            $stmt2->bind_param("is", $user['user_id'], $action);
            $stmt2->execute();

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: home.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "No account found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Robs Bagstore</title>
<style>
body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg,#000,#333); color:white; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; }
.login-container { background: rgba(255,255,255,0.1); padding:40px; border-radius:15px; backdrop-filter:blur(10px); box-shadow:0 0 15px rgba(0,0,0,0.5); width:360px; text-align:center; }
.login-container h2 { margin-bottom:10px; color:#f9f9f9; }
.login-container p { color:#ccc; font-size:14px; margin-bottom:25px; }
input { width:85%; padding:12px; margin:8px 0; border:none; border-radius:8px; outline:none; background:rgba(255,255,255,0.85); color:#000; font-size:14px; }
button { width:90%; padding:12px; margin-top:10px; border:none; background:#ff6600; color:white; font-size:16px; border-radius:8px; cursor:pointer; font-weight:bold; }
button:hover { background:#ff8533; transform:scale(1.03); }
a { color:#ff6600; text-decoration:none; font-weight:500; }
a:hover { text-decoration:underline; }
.error { background: rgba(255,0,0,0.15); color:#ff8080; padding:10px; border-radius:5px; margin-bottom:10px; }
.footer-text { margin-top:15px; color:#ccc; font-size:14px; }
</style>
</head>
<body>
<div class="login-container">
<h2>Welcome Back 👋</h2>
<p>Login to continue shopping at <b>Robs Bagstore</b></p>

<?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

<form method="POST">
<input type="email" name="email" placeholder="Email Address" required><br>
<input type="password" name="password" placeholder="Password" required><br>
<button type="submit" name="login">Login</button>
</form>

<p class="footer-text">Don't have an account? <a href="register.php">Sign Up</a></p>
</div>
</body>
</html>
