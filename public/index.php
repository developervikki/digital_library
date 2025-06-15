<?php
include '../config/db.php';
session_start();

// Join with users table to fetch student name
$sql = "SELECT s.*, u.name AS student_name 
        FROM seats s 
        LEFT JOIN users u ON s.student_id = u.id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Library Seat Availability</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    canvas#bg {
      position: fixed;
      z-index: -1;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: #0f172a;
    }
  </style>
</head>
<body class="min-h-screen bg-gray-900 text-white overflow-x-hidden">
  <canvas id="bg"></canvas>

  <!-- 🧭 NAVBAR -->
  <header class="bg-gray-800 bg-opacity-90 sticky top-0 z-20 shadow-md">
    <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
      <div class="text-xl font-bold text-indigo-400">📘 MyLibrary</div>
      <div>
        <?php if (isset($_SESSION['student_id'])) { ?>
          <a href="logout.php" class="text-sm text-red-400 hover:underline">Logout</a>
        <?php } else { ?>
          <a href="register.php" class="text-sm text-blue-400 hover:underline mr-4">Register</a>
          <a href="login.php" class="text-sm text-green-400 hover:underline">Login</a>
        <?php } ?>
      </div>
    </div>
  </header>

  <!-- 💺 MAIN CONTENT -->
  <div class="max-w-6xl mx-auto px-4 py-10 relative z-10">
    <h1 class="text-4xl font-bold text-center text-indigo-400 mb-10">📚 Library Seat Availability</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <?php while ($row = $result->fetch_assoc()) { ?>
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 flex flex-col space-y-4 hover:shadow-2xl transition">
          <h3 class="text-xl font-semibold text-indigo-300">Seat <?= htmlspecialchars($row['seat_number']) ?></h3>

          <?php if ($row['status'] === 'available') { ?>
            <span class="text-sm font-medium text-green-400 bg-green-900 bg-opacity-30 px-3 py-1 rounded-full">Available</span>
            <a href="book_seat.php?seat=<?= urlencode($row['seat_number']) ?>"
               class="mt-2 bg-indigo-500 hover:bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium">
              Book Now
            </a>
          <?php } else { ?>
            <span class="text-sm font-medium text-red-400 bg-red-900 bg-opacity-30 px-3 py-1 rounded-full">Booked</span>
            <div class="text-sm text-gray-300 mt-2">
              👤 <strong><?= htmlspecialchars($row['student_name'] ?? 'N/A') ?></strong><br>
              🕒 <strong>Joined:</strong> <?= $row['joined_at'] ? date('d M Y, h:i A', strtotime($row['joined_at'])) : 'Unknown' ?>
            </div>
            <span class="text-sm text-gray-400 mt-1">Currently Unavailable</span>
          <?php } ?>
        </div>
      <?php } ?>
    </div>
  </div>

  <!-- 🌌 Canvas Animation -->
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
      for (let i = 0; i < 80; i++) {
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
