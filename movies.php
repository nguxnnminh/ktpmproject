<?php
include 'includes/header.php';
include 'includes/data.php';

$movies = loadData('data/movies.json');

// Lấy ID phim từ URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$movie = null;

// Tìm phim theo ID
foreach ($movies as $m) {
    if ($m['id'] === $id) {
        $movie = $m;
        break;
    }
}

if (!$movie) {
    echo "<h2>Không tìm thấy phim.</h2>";
    include 'includes/footer.php';
    exit;
}
?>

<h2>🎞️ Thông tin phim</h2>
<div style="display: flex; gap: 20px;">
    <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>" style="width: 300px; border-radius: 8px;">
    <div>
        <h3><?= htmlspecialchars($movie['title']) ?></h3>
        <p><strong>Thể loại:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
        <p><strong>Thời lượng:</strong> <?= htmlspecialchars($movie['duration']) ?></p>
        <a href="showtimes.php?movie_id=<?= $movie['id'] ?>" style="background: #28a745; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px;">Xem suất chiếu</a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>