<?php
include 'db_connect.php';
if (isset($_POST['municipality_id'])) {
$municipality_id = intval($_POST['municipality_id']);
$res = $conn->query("SELECT id, name FROM barangays WHERE municipality_id = $municipality_id ORDER BY name ASC");
echo '<option value="" disabled selected>Select Barangay</option