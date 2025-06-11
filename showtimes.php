<?php
include 'includes/header.php';
include 'includes/data.php';

$showtimes = loadData('data/showtimes.json');
$movies = loadData('data/movies.json');

// Tạo mảng ánh xạ movie_id -> title
$movieMap = [];
foreach ($movies as $movie) {
    $movieMap[$movie['id']] = $movie['title'];
}

// Debug: Kiểm tra dữ liệu
if (empty($showtimes)) {
    echo "<p style='color: red;'>Không tải được dữ liệu suất chiếu từ showtimes.json hoặc file rỗng.</p>";
} elseif (empty($movies)) {
    echo "<p style='color: red;'>Không tải được dữ liệu phim từ movies.json hoặc file rỗng.</p>";
}
?>

<div class="container">
    <h2>🕒 Lịch chiếu tất cả các phim</h2>

    <?php if (empty($showtimes) || empty($movies)): ?>
        <p>Không có dữ liệu để hiển thị lịch chiếu.</p>
    <?php else: ?>
        <div class="showtime-list">
            <?php foreach ($showtimes as $showtime): ?>
                <?php
                $movieTitle = $movieMap[$showtime['movie_id']] ?? 'Không rõ';
                $isPast = (new DateTime($showtime['datetime'], new DateTimeZone('+0700'))) < (new DateTime('now', new DateTimeZone('+0700')));
                ?>
                <div class="showtime-card">
                    <p><strong>Phim:</strong> <?= htmlspecialchars($movieTitle) ?></p>
                    <p><strong>Thời gian:</strong> <?= htmlspecialchars($showtime['datetime']) ?> <?php if ($isPast): ?> (Đã qua)<?php endif; ?></p>
                    <p><strong>Rạp:</strong> <?= htmlspecialchars($showtime['room']) ?></p>
                    <?php if (!$isPast): ?>
                        <a href="booking.php?showtime_id=<?= $showtime['id'] ?>" class="btn">Đặt vé</a>
                    <?php else: ?>
                        <p style="color: gray;">Đã hết thời gian đặt vé</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>