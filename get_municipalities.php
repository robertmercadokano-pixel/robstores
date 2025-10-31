<?php
include 'db_connect.php';
if (isset($_POST['province_id'])) {
$province_id = intval($_POST['province_id']);
$res = $conn->query("SELECT id, name FROM municipalities WHERE province_id = $province_id ORDER BY name ASC");
echo '<option value="" disabled selected>Select Municipality</option>';
while ($r = $res->fetch_assoc()) {
echo "<option value='".$r['id']."'>".htmlspecialchars($r['name'])."</option>";
}
}
?>