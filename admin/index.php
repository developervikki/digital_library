<?php
session_start();
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];
    $res = $conn->query("SELECT * FROM admin WHERE email='$email'");
    if ($res->num_rows === 1) {
        $admin = $res->fetch_assoc();
        if (password_verify($pass, $admin['password'])) {
            $_SESSION['admin'] = $admin['email'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Wrong Password";
        }
    } else {
        $error = "No admin found";
    }
}
?>
<link rel="stylesheet" href="../assets/css/style.css">
<div class='container'>
    <h2>Admin Login</h2>
    <?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        <button type="submit">Login</button>
    </form>
    <p><a href="register.php">New Admin? Register here</a></p>
</div>
