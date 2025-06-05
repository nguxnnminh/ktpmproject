<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    $_SESSION['login_message'] = "⚠️ Bạn cần đăng nhập để xem vé đã đặt.";
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/data.php';

$showtimes = loadData('data/showtimes.json');
$movies = loadData('data/movies.json');
$bookings = loadData('data/bookings.json');

// Lọc booking theo user_id của tài khoản hiện tại
$userBookings = array_filter($bookings, function($b) {
    return isset($b['user_id']) && $b['user_id'] == $_SESSION['user']['id'];
});

$userBookings = array_values($userBookings); // Reset keys
?>

<div class="container">
    <h2>🎟️ Vé đã đặt</h2>

    <?php if (empty($userBookings)): ?>
        <p>⚠️ Bạn chưa đặt vé nào.</p>
    <?php else: ?>
        <div class="ticket-list">
            <?php foreach ($userBookings as $booking): ?>
                <?php
                $showtime = array_filter($showtimes, function($s) use ($booking) {
                    return $s['id'] == $booking['showtime_id'];
                });
                $showtime = array_values($showtime)[0];
                $movie = array_filter($movies, function($m) use ($showtime) {
                    return $m['id'] == $showtime['movie_id'];
                });
                $movie = array_values($movie)[0];
                ?>
                <div class="ticket-card">
                    <h3><?= htmlspecialchars($movie['title']) ?></h3>
                    <p>🕒 Thời gian: <?= $showtime['datetime'] ?></p>
                    <p>📍 Phòng: <?= $showtime['room'] ?></p>
                    <p>🎟️ Ghế: <?= implode(", ", $booking['seats']) ?></p>
                    <p>⏰ Thời gian đặt: <?= $booking['booking_time'] ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>