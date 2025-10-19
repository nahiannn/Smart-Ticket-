<?php
session_start();
require_once '../db_connect.php';

// Redirect to admin login if not authenticated
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: admin_login.php');
    exit();
}

// --- QUERY 1: FETCH ALL MOVIE BOOKINGS ---
$movie_stmt = $conn->query("
    SELECT
        b.transaction_id, u.name AS user_name, m.title AS movie_title,
        t.name AS theater_name, s.show_time, SUM(b.total_amount) AS total_paid,
        GROUP_CONCAT(b.seat_number ORDER BY b.seat_number SEPARATOR ', ') AS seats,
        b.booking_time
    FROM bookings AS b
    JOIN users AS u ON b.user_id = u.id
    JOIN movie_showtimes AS s ON b.showtime_id = s.showtime_id
    JOIN movies AS m ON s.movie_id = m.movie_id
    JOIN theaters AS t ON s.theater_id = t.theater_id
    GROUP BY b.transaction_id
    ORDER BY b.booking_time DESC
");
$movie_bookings = $movie_stmt->fetchAll(PDO::FETCH_ASSOC);


// --- QUERY 2: FETCH ALL PARK BOOKINGS ---
$park_stmt = $conn->query("
    SELECT
        pb.transaction_id, u.name AS user_name, p.name AS park_name,
        pb.package_type, pb.quantity, pb.total_amount, pb.booking_time
    FROM park_bookings AS pb
    JOIN users AS u ON pb.user_id = u.id
    JOIN parks AS p ON pb.park_id = p.park_id
    ORDER BY pb.booking_time DESC
");
$park_bookings = $park_stmt->fetchAll(PDO::FETCH_ASSOC);


// --- QUERY 3: FETCH ALL MUSEUM BOOKINGS ---
$museum_stmt = $conn->query("
    SELECT
        mb.booking_id, u.name AS user_name, m.name AS museum_name,
        mb.quantity, mb.total_amount, mb.booking_time
    FROM museum_bookings AS mb
    JOIN users AS u ON mb.user_id = u.id
    JOIN museums AS m ON mb.museum_id = m.museum_id
    ORDER BY mb.booking_time DESC
");
$museum_bookings = $museum_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View All Bookings | Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .sidebar {
            width: 220px;
            background: linear-gradient(135deg, #ff4c60, #4ecdc4);
            color: white;
            height: 100vh;
            padding-top: 20px;
            position: fixed;
        }
        .sidebar h2 { text-align: center; margin-bottom: 20px; color: white; }
        .sidebar a { display: block; color: white; padding: 12px 20px; text-decoration: none; }
        .sidebar a:hover, .sidebar a.active { background-color: #575757; }
        .main-content { margin-left: 240px; padding: 20px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">🏠 Dashboard</a>
    <a href="view_users.php">👥 View Users</a>
    <a href="a_contact.php">📄 Contact Message</a>
    <a href="add_movie.php?type=movie">➕ Add Movie</a>
    <a href="add_theater.php">🏛 Add Theater</a>
    <a href="add_museum.php">🖼 Add Museum</a>
    <a href="add_park.php">🌳 Add Park</a>
    <a href="view_bookings.php" class="active">📄 All Bookings</a>
    <a href="admin_login.php">🚪 Logout</a>
</div>

<div class="main-content">
    <div class="container-fluid">
        <h1 class="my-4">All System Bookings</h1>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>

        <h2 class="mt-5 mb-3">🎬 All Movie Bookings</h2>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>User</th>
                            <th>Movie</th>
                            <th>Theater</th>
                            <th>Seats</th>
                            <th>Total Paid</th>
                            <th>Booking Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($movie_bookings)): ?>
                            <tr><td colspan="6" class="text-center">No movie bookings found.</td></tr>
                        <?php else: foreach ($movie_bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                <td><?= htmlspecialchars($booking['movie_title']) ?></td>
                                <td><?= htmlspecialchars($booking['theater_name']) ?></td>
                                <td><?= htmlspecialchars($booking['seats']) ?></td>
                                <td>৳<?= number_format($booking['total_paid'], 2) ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($booking['booking_time'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h2 class="mt-5 mb-3">🌳 All Park Bookings</h2>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-success">
                        <tr>
                            <th>User</th>
                            <th>Park</th>
                            <th>Package</th>
                            <th>Quantity</th>
                            <th>Total Paid</th>
                            <th>Booking Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($park_bookings)): ?>
                            <tr><td colspan="6" class="text-center">No park bookings found.</td></tr>
                        <?php else: foreach ($park_bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                <td><?= htmlspecialchars($booking['park_name']) ?></td>
                                <td><?= htmlspecialchars($booking['package_type']) ?></td>
                                <td><?= htmlspecialchars($booking['quantity']) ?></td>
                                <td>৳<?= number_format($booking['total_amount'], 2) ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($booking['booking_time'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h2 class="mt-5 mb-3">🖼️ All Museum Bookings</h2>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped table-hover">
                    <thead class="table-warning">
                        <tr>
                            <th>User</th>
                            <th>Museum</th>
                            <th>Quantity</th>
                            <th>Total Paid</th>
                            <th>Booking Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($museum_bookings)): ?>
                            <tr><td colspan="5" class="text-center">No museum bookings found.</td></tr>
                        <?php else: foreach ($museum_bookings as $booking): ?>
                            <tr>
                                <td><?= htmlspecialchars($booking['user_name']) ?></td>
                                <td><?= htmlspecialchars($booking['museum_name']) ?></td>
                                <td><?= htmlspecialchars($booking['quantity']) ?></td>
                                <td>৳<?= number_format($booking['total_amount'], 2) ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($booking['booking_time'])) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

</body>
</html>