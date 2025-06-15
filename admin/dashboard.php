<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

include '../config/db.php';

$current_month = isset($_GET['month']) ? intval($_GET['month']) : date('n');
$current_year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

// Handle seat status update
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
        LEFT JOIN users ON seats.student_id = users.id 
        ORDER BY seats.seat_number";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Seat Management</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-6 px-4">
        <h2 class="text-3xl font-bold mb-6">🎓 Admin Seat Dashboard - <?= date('F', mktime(0, 0, 0, $current_month, 10)) ?> <?= $current_year ?></h2>

        <form method="GET" class="mb-6 flex gap-2">
            <select name="month" class="border px-3 py-2 rounded">
                <?php for ($m = 1; $m <= 12; $m++) { ?>
                    <option value="<?= $m ?>" <?= $m == $current_month ? 'selected' : '' ?>><?= date('F', mktime(0, 0, 0, $m, 10)) ?></option>
                <?php } ?>
            </select>
            <select name="year" class="border px-3 py-2 rounded">
                <?php for ($y = 2023; $y <= date('Y') + 1; $y++) { ?>
                    <option value="<?= $y ?>" <?= $y == $current_year ? 'selected' : '' ?>><?= $y ?></option>
                <?php } ?>
            </select>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Load</button>
        </form>

        <table class="w-full table-auto border bg-white shadow-md rounded">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-4 py-3">Seat No</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Student</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Booking Date</th>
                    <th class="px-4 py-3"><?= date('F', mktime(0, 0, 0, $current_month, 10)) ?> Fees</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()) { ?>
                <tr class="border-t text-center hover:bg-gray-50">
                    <td class="py-2 font-semibold text-blue-800"><?= htmlspecialchars($row['seat_number']) ?></td>
                    <td class="py-2 text-sm">
                        <span class="px-2 py-1 rounded <?= $row['status'] === 'booked' ? 'bg-yellow-200 text-yellow-800' : 'bg-green-200 text-green-800' ?>">
                            <?= ucfirst($row['status']) ?>
                        </span>
                    </td>
                    <td class="py-2"><?= htmlspecialchars($row['name'] ?? '-') ?></td>
                    <td class="py-2"><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                    <td class="py-2"><?= $row['joined_at'] ?? '-' ?></td>
                    <td class="py-2">
                        <form method="POST" class="inline">
                            <input type="hidden" name="seat_number" value="<?= $row['seat_number'] ?>">
                            <input type="hidden" name="month" value="<?= $current_month ?>">
                            <input type="hidden" name="year" value="<?= $current_year ?>">
                            <input type="hidden" name="update_payment" value="1">
                            <select name="fees_paid" class="border rounded px-2 py-1">
                                <option value="1" <?= $row['fees_paid'] == 1 ? 'selected' : '' ?>>Paid</option>
                                <option value="0" <?= $row['fees_paid'] != 1 ? 'selected' : '' ?>>Unpaid</option>
                            </select>
                            <button type="submit" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Update</button>
                        </form>
                    </td>
                    <td class="py-2">
                        <?php if ($row['status'] === 'booked') { ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="seat_number" value="<?= $row['seat_number'] ?>">
                                <input type="hidden" name="action" value="free">
                                <button class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Mark as Free</button>
                            </form>
                        <?php } else { ?>
                            <span class="text-green-600 font-semibold">Available</span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
