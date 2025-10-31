<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "robs_bagstore_db"; // your database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Database connected successfully"; // (You can test by enabling this)
?>
