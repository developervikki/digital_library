<?php
include '../config/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm']);

    $check = $conn->query("SELECT * FROM admin WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Admin already exists.";
    } elseif ($password != $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("INSERT INTO admin (email, password) VALUES ('$email', '$hash')");
        $success = "Admin registered. <a href='index.php'>Login</a>";
    }
}
?>
<link rel="stylesheet" href="../assets/css/style.css">
<div class="container">
    <h2>Register New Admin</h2>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <?php if (isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
    <form method="POST">
        Email: <input type="email" name="email" required><br><br>
        Password: <input type="password" name="password" required><br><br>
        Confirm Password: <input type="password" name="confirm" required><br><br>
        <button type="submit">Register</button>
    </form>
</div>
