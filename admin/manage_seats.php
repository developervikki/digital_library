<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit();
}

// Free a seat
if (isset($_GET['free'])) {
    $seat = intval($_GET['free']);
    $conn->query("UPDATE seats SET status='available', booked_by=NULL, booking_date=NULL, fees_status='unpaid' WHERE seat_number=$seat");
    header("Location: dashboard.php");
    exit();
}

// Mark as paid
if (isset($_GET['mark_paid'])) {
    $seat = intval($_GET['mark_paid']);
    $conn->query("UPDATE seats SET fees_status='paid' WHERE seat_number=$seat");
    header("Location: dashboard.php");
    exit();
}

// Mark as unpaid
if (isset($_GET['mark_unpaid'])) {
    $seat = intval($_GET['mark_unpaid']);
    $conn->query("UPDATE seats SET fees_status='unpaid' WHERE seat_number=$seat");
    header("Location: dashboard.php");
    exit();
}
?>
