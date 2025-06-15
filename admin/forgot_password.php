<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: index.php"); exit(); }
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_SESSION['admin'];
    $current = $_POST['current'];
    $new = $_POST['new'];
    $confirm = $_POST['confirm'];

    $res = $conn->query("SELECT * FROM admin WHERE email='$email'");
    $admin = $res->fetch_assoc();
    if (!password_verify($current, $admin['password'])) {
        $error = "Current password is wrong.";
    } elseif ($new != $confirm) {
        $error = "New passwords do not match.";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $conn->query("UPDATE admin SET password='$hash' WHERE email='$email'");
        $success = "Password changed successfully.";
    }
}
?>
<link rel="stylesheet" href="../assets/css/style.css">
<div class="container">
    <h2>Change Password</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        Current Password: <input type="password" name="current" required><br><br>
        New Password: <input type="password" name="new" required><br><br>
        Confirm New Password: <input type="password" name="confirm" required><br><br>
        <button type="submit">Change</button>
    </form>
</div>
