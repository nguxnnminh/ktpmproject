<?php
include 'includes/header.php';
include 'includes/data.php';

$bookings = loadData('data/bookings.json');
$showtimes = loadData('data/showtimes.json');
$movies = loadData('data/movies.json');

// Tạo bản đồ ID => đối tượng
$movieMap = [];
foreach ($movies as $m) {
    $movieMap[$m['id']] = $m['title'];
}

$showtimeMap = [];
foreach ($showtimes as $s) {
    $showtimeMap[$s['id']] = $s;
}
?>

<div class="container">
    <h2>🎫 Vé đã đặt</h2>

    <?php if (empty($bookings)): ?>
        <p>Bạn chưa đặt vé nào.</p>
    <?php else: ?>
        <div class="ticket-list">
            <?php foreach ($bookings as $b): 
                $showtime = $showtimeMap[$b['showtime_id']] ?? null;
                if (!$showtime) continue;

                $movieTitle = $movieMap[$showtime['movie_id']] ?? 'Không rõ phim';
            ?>
                <div class="ticket-card">
                    <h3><?= htmlspecialchars($movieTitle) ?></h3>
                    <p><strong>🕒 Thời gian:</strong> <?= htmlspecialchars($showtime['datetime']) ?></p>
                    <p><strong>📍 Phòng:</strong> <?= htmlspecialchars($showtime['room']) ?></p>
                    <p><strong>🎟️ Ghế:</strong> <?= implode(', ', $b['seats']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
