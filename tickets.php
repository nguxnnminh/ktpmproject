<?php
session_start();
date_default_timezone_set('Asia/Ho_Chi_Minh');

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

// Lọc vé theo người dùng hiện tại
$userBookings = array_filter($bookings, function($b) {
    return isset($b['user_id']) && $b['user_id'] == $_SESSION['user']['user_id'];
});
$userBookings = array_values($userBookings); // Reset key
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
                $showtime = array_values($showtime)[0] ?? ['datetime' => 'Không xác định', 'room' => 'Không xác định'];

                // Định dạng thời gian suất chiếu
                $dt = DateTime::createFromFormat('Y-m-d\TH:i', $showtime['datetime']);
                $formattedDatetime = $dt ? $dt->format('d/m/Y H:i') : htmlspecialchars($showtime['datetime']);

                $movie = array_filter($movies, function($m) use ($showtime) {
                    return $m['id'] == $showtime['movie_id'];
                });
                $movie = array_values($movie)[0] ?? ['title' => 'Phim không tồn tại'];
                ?>
                <div class="ticket-card">
                    <h3><?= htmlspecialchars($movie['title']) ?></h3>
                    <p>🕒 Thời gian: <?= $formattedDatetime ?></p>
                    <p>📍 Rạp: <?= htmlspecialchars($showtime['room']) ?></p>
                    <p>🎟️ Ghế: <?= implode(", ", array_map('htmlspecialchars', $booking['seats'])) ?></p>
                    <p>⏰ Thời gian đặt: <?= DateTime::createFromFormat('Y-m-d H:i:s', $booking['booking_time'])->format('d/m/Y H:i') ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
