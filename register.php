<?php
session_start();
include 'db_connect.php';

if (isset($_POST['register'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $firstname = $conn->real_escape_string($_POST['firstname']);
    $middlename = $conn->real_escape_string($_POST['middlename']);
    $lastname = $conn->real_escape_string($_POST['lastname']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $region = $conn->real_escape_string($_POST['region']);
    $province = $conn->real_escape_string($_POST['province']);
    $municipality = $conn->real_escape_string($_POST['municipality']);
    $barangay = $conn->real_escape_string($_POST['barangay']);
    $street = $conn->real_escape_string($_POST['street']);
    $house_no = $conn->real_escape_string($_POST['house_no']);
    $zip = $conn->real_escape_string($_POST['zip']);
    $birthday = $conn->real_escape_string($_POST['birthday']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        $chk = $conn->query("SELECT user_id FROM users WHERE username='$username' OR email='$email'");
        if ($chk->num_rows > 0) {
            $error = "Username or email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username,password,email,firstname,middlename,lastname,gender,region_id,province_id,municipality_id,barangay_id,street,house_no,zip,birthday) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->bind_param('sssssssiiiissss',$username,$hashed,$email,$firstname,$middlename,$lastname,$gender,$region,$province,$municipality,$barangay,$street,$house_no,$zip,$birthday);
            if ($stmt->execute()) {
                $_SESSION['success'] = 'Account created successfully!';
                header('Location: login.php');
                exit();
            } else {
                $error = "Database error: ".$conn->error;
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register - Robs Bagstore</title>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
body{font-family:sans-serif;background:#111;color:#fff;margin:0;padding:0;display:flex;justify-content:center;align-items:flex-start;min-height:100vh;overflow-y:auto}
.register-container{background:rgba(255,255,255,0.06);padding:28px;border-radius:12px;backdrop-filter:blur(8px);width:95%;max-width:900px;margin:36px 0;box-sizing:border-box}
h2{text-align:center;margin:0 0 6px}.subtitle{text-align:center;color:#bbb;margin:0 0 18px}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.section-title{color:#ff9933;font-weight:600;border-bottom:1px solid rgba(255,153,51,0.12);padding-bottom:6px;margin-top:12px}
input,select{width:100%;padding:10px;border-radius:8px;border:none;background:rgba(255,255,255,0.95);color:#000;font-size:14px;box-sizing:border-box}
button{width:100%;padding:12px;border-radius:8px;border:none;background:#ff6600;color:#fff;font-weight:600;cursor:pointer;margin-top:12px}
.error{background:rgba(255,0,0,0.12);color:#ff9aa2;padding:10px;border-radius:6px;text-align:center;margin-bottom:12px}
@media (max-width: 700px){.form-grid{grid-template-columns:1fr}.register-container{padding:20px}}
</style>
</head>
<body>

<div class="register-container">
<h2>Create Your Account</h2>
<p class="subtitle">Join Robs Bagstore to shop the latest styles</p>
<?php if(isset($error)) echo "<div class='error'>".htmlspecialchars($error)."</div>"; ?>

<form method="post" id="regForm">
<div class="section-title">Account</div>
<div class="form-grid">
<input name="username" placeholder="Username" required>
<input name="email" type="email" placeholder="Email" required>
<input name="password" type="password" placeholder="Password" required>
<input name="confirm_password" type="password" placeholder="Confirm Password" required>
</div>

<div class="section-title">Personal</div>
<div class="form-grid">
<input name="firstname" placeholder="First Name" required>
<input name="middlename" placeholder="Middle Name">
<input name="lastname" placeholder="Last Name" required>
<select name="gender" required>
<option value="" disabled selected>Gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
<option value="Other">Other</option>
</select>
<input type="date" name="birthday" required>
<div></div>
</div>

<div class="section-title">Address</div>
<div class="form-grid">
<select id="region" name="region" required><option value="" disabled selected>Select Region</option></select>
<select id="province" name="province" required><option value="" disabled selected>Select Province</option></select>
<select id="municipality" name="municipality" required><option value="" disabled selected>Select Municipality</option></select>
<select id="barangay" name="barangay" required><option value="" disabled selected>Select Barangay</option></select>
<input name="street" placeholder="Street" required>
<input name="house_no" placeholder="House No." required>
<input name="zip" placeholder="ZIP Code" required>
</div>

<div><button type="submit" name="register">Create Account</button></div>
</form>
</div>

<script>
let regions=[], provinces=[], municipalities=[], barangays=[];

$(document).ready(function() {
  // Load JSON files
  $.getJSON('refregion.json', data=>{ regions=data.RECORDS; populateRegions(); });
  $.getJSON('refprovince.json', data=>{ provinces=data.RECORDS; });
  $.getJSON('refcitymun.json', data=>{ municipalities=data.RECORDS; });
  $.getJSON('refbrgy.json', data=>{ barangays=data.RECORDS; });

  function populateRegions(){
    let regionSelect=$('#region'); regionSelect.html('<option value="" disabled selected>Select Region</option>');
    regions.forEach(r=>regionSelect.append(`<option value="${r.regCode}">${r.regDesc}</option>`));
  }

  $('#region').on('change',function(){
    let region_code=$(this).val();
    let filtered=provinces.filter(p=>p.regCode===region_code);
    let provinceSelect=$('#province'); provinceSelect.html('<option value="" disabled selected>Select Province</option>');
    filtered.forEach(p=>provinceSelect.append(`<option value="${p.provCode}">${p.provDesc}</option>`));
    $('#municipality').html('<option value="" disabled selected>Select Municipality</option>');
    $('#barangay').html('<option value="" disabled selected>Select Barangay</option>');
  });

  $('#province').on('change',function(){
    let code=$(this).val();
    let filtered=municipalities.filter(m=>m.provCode===code);
    let municipalitySelect=$('#municipality'); municipalitySelect.html('<option value="" disabled selected>Select Municipality</option>');
    filtered.forEach(m=>municipalitySelect.append(`<option value="${m.citymunCode}">${m.citymunDesc}</option>`));
    $('#barangay').html('<option value="" disabled selected>Select Barangay</option>');
  });

  $('#municipality').on('change',function(){
    let code=$(this).val();
    let filtered=barangays.filter(b=>b.citymunCode===code);
    let brgySelect=$('#barangay'); brgySelect.html('<option value="" disabled selected>Select Barangay</option>');
    filtered.forEach(b=>brgySelect.append(`<option value="${b.brgyCode}">${b.brgyDesc}</option>`));
  });
});
</script>

</body>
</html>
