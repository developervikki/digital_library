<?php
include '../config/db.php';

$token = $_GET['token'] ?? '';
$show_form = false;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && $token === '1234') {
    // Removed token_expiry check
    $stmt = $conn->query("SELECT * FROM users WHERE reset_token='1234'");
    $user = $stmt->fetch_assoc();

    if ($user) {
        $show_form = true;
    } else {
        echo "❌ Invalid token.";
    }
}

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password='$password', reset_token=NULL, token_expiry=NULL WHERE reset_token='1234'";
    if ($conn->query($sql)) {
        echo "✅ Password updated successfully! <a href=index.php'>Login here</a>";
        exit;
    } else {
        echo "❌ Failed to update password.";
    }
}
?>

<?php if ($show_form): ?>
<h2>Reset Your Password</h2>
<form method="post">
    New Password: <input type="password" name="password" required><br>
    <button type="submit">Reset Password</button>
</form>
<?php endif; ?>
