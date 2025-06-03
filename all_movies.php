<?php
include 'includes/header.php';
include 'includes/data.php';

$movies = loadData('data/movies.json');

// Kiểm tra nếu có ID phim được truyền qua URL để hiển thị chi tiết
$selectedMovie = null;
if (isset($_GET['id'])) {
    $movieId = (int)$_GET['id'];
    foreach ($movies as $movie) {
        if ($movie['id'] === $movieId) {
            $selectedMovie = $movie;
            break;
        }
    }
}
?>

<?php if ($selectedMovie): ?>
    <!-- Hiển thị chi tiết phim nếu có ID -->
    <div class="container">
        <h2>Chi tiết phim</h2>
        <div class="movie-detail">
            <img src="<?= htmlspecialchars($selectedMovie['image']) ?>" alt="<?= htmlspecialchars($selectedMovie['title']) ?>">
            <h3><?= htmlspecialchars($selectedMovie['title']) ?></h3>
            <p><strong>Thể loại:</strong> <?= htmlspecialchars($selectedMovie['genre']) ?></p>
            <p><strong>Thời lượng:</strong> <?= htmlspecialchars($selectedMovie['duration']) ?></p>
            <a href="book_ticket.php?movie_id=<?= $selectedMovie['id'] ?>" class="btn">Đặt vé</a>
        </div>
    </div>
<?php else: ?>
    <!-- Hiển thị danh sách tất cả phim -->
    <div class="container">
        <h2>Tất cả phim</h2>
        <div class="movie-list">
            <?php foreach ($movies as $movie): ?>
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
<?php endif; ?>

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