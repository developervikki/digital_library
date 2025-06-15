<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

include '../config/db.php';

$current_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['seat_number']) && isset($_POST['action'])) {
        $seat_number = intval($_POST['seat_number']);
        $action = $_POST['action'];

        if ($action === 'free') {
            $conn->query("UPDATE seats SET status = 'available', student_id = NULL, joined_at = NULL WHERE seat_number = $seat_number");
        } elseif ($action === 'book') {
            $conn->query("UPDATE seats SET status = 'booked' WHERE seat_number = $seat_number");
        }
    }
    if (isset($_POST['update_payment']) && isset($_POST['seat_number']) && isset($_POST['fees_paid']) && isset($_POST['month']) && isset($_POST['year'])) {
        $seat_number = intval($_POST['seat_number']);
        $fees_paid = intval($_POST['fees_paid']);
        $month = intval($_POST['month']);
        $year = intval($_POST['year']);

        $check = $conn->query("SELECT id FROM payments WHERE seat_number = $seat_number AND month = $month AND year = $year");
        if ($check->num_rows > 0) {
            $conn->query("UPDATE payments SET paid = $fees_paid, updated_at = NOW() WHERE seat_number = $seat_number AND month = $month AND year = $year");
        } else {
            $conn->query("INSERT INTO payments (seat_number, month, year, paid) VALUES ($seat_number, $month, $year, $fees_paid)");
        }
    }
}

$sql = "SELECT seats.seat_number, seats.status, seats.joined_at, users.name, users.email,
        (SELECT paid FROM payments WHERE payments.seat_number = seats.seat_number AND payments.month = $current_month AND payments.year = $current_year) AS fees_paid
        FROM seats 
        LEFT JOIN users ON seats.student_id = users.id";

if ($filter === 'paid') {
    $sql .= " WHERE (SELECT paid FROM payments WHERE payments.seat_number = seats.seat_number AND payments.month = $current_month AND payments.year = $current_year) = 1";
} elseif ($filter === 'unpaid') {
    $sql .= " WHERE (SELECT paid FROM payments WHERE payments.seat_number = seats.seat_number AND payments.month = $current_month AND payments.year = $current_year) != 1 OR (SELECT paid FROM payments WHERE payments.seat_number = seats.seat_number AND payments.month = $current_month AND payments.year = $current_year) IS NULL";
}

$sql .= " ORDER BY seats.seat_number";
$result = $conn->query($sql);

$total_seats = $conn->query("SELECT COUNT(*) AS total FROM seats")->fetch_assoc()['total'];
$booked_seats = $conn->query("SELECT COUNT(*) AS booked FROM seats WHERE status = 'booked'")->fetch_assoc()['booked'];
$available_seats = $conn->query("SELECT COUNT(*) AS available FROM seats WHERE status = 'available'")->fetch_assoc()['available'];
$paid_seats = $conn->query("SELECT COUNT(*) AS paid FROM payments WHERE month = $current_month AND year = $current_year AND paid = 1")->fetch_assoc()['paid'];

$monthly_data = [];
for ($m = 1; $m <= 12; $m++) {
    $res = $conn->query("SELECT COUNT(*) AS count FROM payments WHERE month = $m AND year = $current_year AND paid = 1");
    $monthly_data[] = $res->fetch_assoc()['count'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Seat Dashboard </title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        canvas#bg {
            position: fixed;
            z-index: -1;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: #f0f4ff;
        }
    </style>
</head>
<!--<body class="min-h-screen text-gray-800 relative overflow-hidden">-->
<body class="min-h-screen text-gray-800 relative overflow-y-auto overflow-x-hidden">

<canvas id="bg"></canvas>

<nav class="bg-white shadow-md py-4 px-6 mb-6 z-10 relative">
    <div class="flex justify-between items-center">
        <h1 class="text-xl sm:text-2xl font-bold text-blue-700">📚 Digital Library Admin Pannel</h1>
        <div class="flex items-center space-x-4 text-sm sm:text-base">
            <span class="text-gray-700">👤 <?= $_SESSION['admin'] ?></span>
            <a href="logout.php" class="bg-red-500 text-white px-2 sm:px-3 py-1 rounded hover:bg-red-600">Logout</a>
        </div>
    </div>
</nav>

<div class="max-w-7xl mx-auto p-4 sm:p-6 relative z-10">
    <div class="mb-4 space-x-2 sm:space-x-4 text-sm sm:text-base">
        <a href="?filter=all" class="px-2 sm:px-3 py-1 sm:py-2 rounded <?= $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-200' ?>">All</a>
        <a href="?filter=paid" class="px-2 sm:px-3 py-1 sm:py-2 rounded <?= $filter === 'paid' ? 'bg-green-600 text-white' : 'bg-gray-200' ?>">Paid</a>
        <a href="?filter=unpaid" class="px-2 sm:px-3 py-1 sm:py-2 rounded <?= $filter === 'unpaid' ? 'bg-yellow-600 text-white' : 'bg-gray-200' ?>">Unpaid</a>
    </div>

    <!-- Seat Stats Chart -->
    <div class="bg-white p-4 sm:p-6 rounded shadow mb-8">
        <h2 class="text-lg sm:text-xl font-semibold mb-4">📊 Seat Summary Chart</h2>
        <div class="relative h-64">
            <canvas id="seatChart"></canvas>
        </div>
    </div>

    <!-- Monthly Payment Trend Chart -->
    <div class="bg-white p-4 sm:p-6 rounded shadow mb-8">
        <h2 class="text-lg sm:text-xl font-semibold mb-4">📈 Monthly Payment Trend</h2>
        <div class="relative h-64">
            <canvas id="paymentTrend"></canvas>
        </div>
    </div>

    <div class="overflow-x-auto bg-white p-4 sm:p-6 rounded shadow">
        <h2 class="text-lg sm:text-xl font-semibold mb-4">All Seats</h2>
        <table class="min-w-full text-sm sm:text-base border">
            <thead class="bg-gray-100 text-gray-700">
                <tr>
                    <th class="px-2 sm:px-4 py-2 border">Seat</th>
                    <th class="px-2 sm:px-4 py-2 border">Status</th>
                    <th class="px-2 sm:px-4 py-2 border">Student</th>
                    <th class="px-2 sm:px-4 py-2 border">Email</th>
                    <th class="px-2 sm:px-4 py-2 border">Fees Paid</th>
                    <th class="px-2 sm:px-4 py-2 border">Joined</th>
                    <th class="px-2 sm:px-4 py-2 border">Actions</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-2 sm:px-4 py-2"><?= $row['seat_number'] ?></td>
                    <td class="px-2 sm:px-4 py-2">
                        <span class="<?= $row['status'] == 'booked' ? 'text-red-600' : 'text-green-600' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td class="px-2 sm:px-4 py-2"><?= $row['name'] ?? '---' ?></td>
                    <td class="px-2 sm:px-4 py-2"><?= $row['email'] ?? '---' ?></td>
                    <td class="px-2 sm:px-4 py-2">
                        <?= ($row['fees_paid'] == 1) ? '✅ Paid' : '❌ Unpaid' ?>
                    </td>
                    <td class="px-2 sm:px-4 py-2"><?= $row['joined_at'] ?? '---' ?></td>
                    <td class="px-2 sm:px-4 py-2 space-x-1">
                        <form method="post" class="inline-block">
                            <input type="hidden" name="seat_number" value="<?= $row['seat_number'] ?>">
                            <input type="hidden" name="action" value="<?= $row['status'] == 'booked' ? 'free' : 'book' ?>">
                            <button type="submit" class="text-white px-2 py-1 rounded <?= $row['status'] == 'booked' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' ?>">
                                <?= $row['status'] == 'booked' ? 'Free' : 'Book' ?>
                            </button>
                        </form>
                        <form method="post" class="inline-block">
                            <input type="hidden" name="seat_number" value="<?= $row['seat_number'] ?>">
                            <input type="hidden" name="update_payment" value="1">
                            <input type="hidden" name="month" value="<?= $current_month ?>">
                            <input type="hidden" name="year" value="<?= $current_year ?>">
                            <input type="hidden" name="fees_paid" value="<?= ($row['fees_paid'] == 1) ? 0 : 1 ?>">
                            <button type="submit" class="text-white px-2 py-1 rounded <?= $row['fees_paid'] == 1 ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-purple-500 hover:bg-purple-600' ?>">
                                <?= $row['fees_paid'] == 1 ? 'Mark Unpaid' : 'Mark Paid' ?>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const seatChart = document.getElementById('seatChart').getContext('2d');
    new Chart(seatChart, {
        type: 'bar',
        data: {
            labels: ['Total Seats', 'Booked', 'Available', 'Paid (<?= date('F', mktime(0, 0, 0, $current_month, 10)) ?>)'],
            datasets: [{
                label: 'Seat Statistics',
                data: [<?= $total_seats ?>, <?= $booked_seats ?>, <?= $available_seats ?>, <?= $paid_seats ?>],
                backgroundColor: ['#3b82f6', '#facc15', '#10b981', '#a855f7'],
                borderColor: ['#1e40af', '#ca8a04', '#065f46', '#6b21a8'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    const paymentTrendCtx = document.getElementById('paymentTrend').getContext('2d');
    new Chart(paymentTrendCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Paid Seats',
                data: <?= json_encode($monthly_data) ?>,
                borderColor: '#10b981',
                backgroundColor: 'transparent',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.4,
                borderDash: [5, 5]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: true
                }
            }
        }
    });
</script>
</body>
</html>
