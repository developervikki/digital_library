<?php
include '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$pass', 'student')";
    if ($conn->query($sql)) {
        header("Location: login.php");
        exit();
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Student Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    canvas#bg {
      position: fixed;
      z-index: -1;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: #0f172a;
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center bg-gray-900 text-white relative overflow-hidden">
  <canvas id="bg"></canvas>

  <div class="bg-gray-800 bg-opacity-90 p-8 rounded-xl shadow-2xl w-full max-w-md z-10">
    <h2 class="text-3xl font-bold mb-6 text-center">Student Registration</h2>
    <?php if (!empty($error)) echo "<p class='text-red-400 mb-4'>$error</p>"; ?>
    <form method="post" class="space-y-4">
      <input name="name" type="text" placeholder="Full Name" required
             class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input name="email" type="email" placeholder="Email" required
             class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
      <input name="password" type="password" placeholder="Password" required
             class="w-full px-4 py-2 rounded-lg bg-gray-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
      <button type="submit"
              class="w-full bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg font-semibold transition">
        Register
      </button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-300">
      Already registered? <a href="login.php" class="text-blue-400 hover:underline">Login here</a>
    </p>
  </div>

  <!-- Canvas Animation -->
  <script>
    const canvas = document.getElementById('bg');
    const ctx = canvas.getContext('2d');
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    let particlesArray = [];
    const colors = ['#38bdf8', '#818cf8', '#f472b6', '#facc15'];

    class Particle {
      constructor() {
        this.x = Math.random() * canvas.width;
        this.y = Math.random() * canvas.height;
        this.size = Math.random() * 3 + 1;
        this.speedX = Math.random() * 2 - 1;
        this.speedY = Math.random() * 2 - 1;
        this.color = colors[Math.floor(Math.random() * colors.length)];
      }

      update() {
        this.x += this.speedX;
        this.y += this.speedY;

        if (this.x < 0 || this.x > canvas.width) this.speedX *= -1;
        if (this.y < 0 || this.y > canvas.height) this.speedY *= -1;
      }

      draw() {
        ctx.fillStyle = this.color;
        ctx.beginPath();
        ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
        ctx.fill();
      }
    }

    function initParticles() {
      particlesArray = [];
      for (let i = 0; i < 100; i++) {
        particlesArray.push(new Particle());
      }
    }

    function animate() {
      ctx.fillStyle = 'rgba(15,23,42,0.2)';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      particlesArray.forEach(p => {
        p.update();
        p.draw();
      });
      requestAnimationFrame(animate);
    }

    window.addEventListener('resize', () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
      initParticles();
    });

    initParticles();
    animate();
  </script>
</body>
</html>
