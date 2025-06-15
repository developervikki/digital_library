<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>StudyZone - Digital Library Platform</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" type="image/png" href="assets/favicon.png">
</head>
<body class="bg-gray-50 text-gray-800">

  <!-- Navbar -->
  <nav class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
      <h1 class="text-2xl font-bold text-indigo-600">📚 StudyZone</h1>
      <div class="space-x-4">
        <a href="#features" class="text-gray-700 hover:text-indigo-600">Features</a>
        <!--<a href="public/book_seat.php" class="text-gray-700 hover:text-indigo-600">Seats</a>-->
        <a href="public/book_seat.php" class="text-gray-700 hover:text-indigo-600">Join</a>
        <a href="admin/login.php" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Admin Login</a>
      </div>
    </div>
  </nav>

  <!-- Hero Section -->
  <section class="bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 text-white text-center py-20">
    <h2 class="text-4xl font-bold mb-4">A Place Where Focus Lives</h2>
    <p class="text-lg mb-6">Reserve your seat, leave distractions behind, and boost your study productivity.</p>
    <a href="public/index.php" class="bg-white text-indigo-600 font-semibold px-6 py-3 rounded-full hover:bg-gray-100 transition">Book Your Seat Now</a>
  </section>

  <!-- Features Section -->
  <section id="features" class="max-w-6xl mx-auto py-12 px-6 text-center">
    <h3 class="text-3xl font-bold mb-6">Why Join StudyZone?</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
      <div class="bg-white p-6 rounded shadow hover:shadow-lg transition">
        <h4 class="text-xl font-semibold mb-2">📶 Distraction-Free Zone</h4>
        <p>A quiet and disciplined environment designed to help you focus on your study goals.</p>
      </div>
      <div class="bg-white p-6 rounded shadow hover:shadow-lg transition">
        <h4 class="text-xl font-semibold mb-2">💺 Seat Reservation</h4>
        <p>Real-time seat availability and seamless booking experience.</p>
      </div>
      <div class="bg-white p-6 rounded shadow hover:shadow-lg transition">
        <h4 class="text-xl font-semibold mb-2">👩‍🎓 Student Community</h4>
        <p>Join a group of students who are serious about their learning and growth.</p>
      </div>
    </div>
  </section>

  <!-- Seat Availability Section -->
  <section id="seats" class="max-w-6xl mx-auto py-12 px-6">
    <h3 class="text-2xl font-bold mb-4 text-center">Available Study Seats</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      
      <!-- Example Seat Card -->
      <div class="bg-white p-4 rounded-lg shadow hover:shadow-lg transition">
        <h4 class="text-xl font-bold mb-2">Seat A1</h4>
        <p class="text-sm text-green-600 font-semibold">Available</p>
        <button class="mt-4 w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700">Book Now</button>
      </div>

      <!-- Repeat with PHP or JS for more seats -->

    </div>
  </section>

  <!-- Call to Action Join Section -->
  <section id="join" class="bg-indigo-100 py-16 text-center">
    <h3 class="text-3xl font-bold mb-4">Ready to Start Studying?</h3>
    <p class="text-lg mb-6 text-gray-700">Get your study routine on track. Join StudyZone now!</p>
    <a href="register.php" class="bg-indigo-600 text-white px-6 py-3 rounded-full hover:bg-indigo-700 transition">Join Now</a>
  </section>

  <!-- Footer -->
  <footer class="bg-gray-800 text-white text-center py-4">
    &copy; 2025 StudyZone. Built with ❤ using PHP, Tailwind CSS & MySQL.
  </footer>

</body>
</html>
