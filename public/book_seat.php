<?php
include '../config/db.php';
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

$seat = $_GET['seat'];
$student_id = $_SESSION['student_id'];

$sql = "UPDATE seats 
        SET status = 'booked', student_id = $student_id, joined_at = NOW() 
        WHERE seat_number = '$seat' AND status = 'available'";

if ($conn->query($sql)) {
    header("Location: index.php");
} else {
    echo "❌ Booking failed.";
}
