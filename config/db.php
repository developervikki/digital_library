<?php
$conn = new mysqli("localhost", "root", "", "digital_library");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
