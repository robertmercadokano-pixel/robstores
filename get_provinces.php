<?php
include 'db_connect.php';
if (isset($_POST['region_id'])) {
$region_id = intval($_POST['region_id']);
$res = $conn->query("SELECT id, name FROM provinces WHERE region_id = $region_id ORDER BY name ASC");
echo '<option value="" disabled selected>Select Province</option>';
while ($r = $res->fetch_assoc()) {
echo "<option value='".$r['id']."'>".htmlspecialchars($r['name'])."</option>";
}
}
?>