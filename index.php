<?php
include 'includes/header.php';
include 'includes/data.php';

$movies = loadData('data/movies.json');

// Sắp xếp phim theo số vé đặt (giảm dần)
usort($movies, function($a, $b) {
    return ($b['tickets_booked'] ?? 0) - ($a['tickets_booked'] ?? 0);
});

// Chỉ lấy 3 phim đầu
$hotMovies = array_slice($movies, 0, 3);
?>

<!-- Banner -->
<div class="banner">
    <div class="banner-content">
        <h1>Khám phá thế giới điện ảnh tại CGV</h1>
        <p>Xem phim hot nhất với trải nghiệm tuyệt vời!</p>
        <a href="all_movies.php" class="btn">Xem ngay</a>
    </div>
</div>

<!-- Danh sách phim hot -->
<div class="container">
    <h2>Phim Hot</h2>
    <div class="movie-list">
        <?php foreach ($hotMovies as $movie): ?>
            <div class="movie-card">
                <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                <div class="movie-content">
                    <h3><?= htmlspecialchars($movie['title']) ?></h3>
                    <p><strong>Thể loại:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
                    <p><strong>Thời lượng:</strong> <?= htmlspecialchars($movie['duration']) ?></p>
                    <a href="all_movies.php?id=<?= $movie['id'] ?>" class="btn movie-btn">Xem chi tiết</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<a id="back-to-top" href="#" title="Back to Top">↑</a>

<?php include 'includes/footer.php'; ?>

<script>
    window.onscroll = function() {
        let btn = document.getElementById("back-to-top");
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            btn.style.display = "flex";
        } else {
            btn.style.display = "none";
        }
    };

    document.getElementById("back-to-top").addEventListener("click", function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>