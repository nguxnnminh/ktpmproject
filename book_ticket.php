<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    $_SESSION['login_message'] = "⚠️ Bạn cần đăng nhập để tiếp tục đặt vé.";
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/data.php';

$movieId = isset($_GET['movie_id']) ? intval($_GET['movie_id']) : 0;
$movies = loadData('data/movies.json');
$showtimes = loadData('data/showtimes.json');

// Tìm phim
$selectedMovie = null;
foreach ($movies as $movie) {
    if ($movie['id'] === $movieId) {
        $selectedMovie = $movie;
        break;
    }
}

if (!$selectedMovie) {
    echo "<div class='container'><h2>Phim không tồn tại.</h2></div>";
    include 'includes/footer.php';
    exit;
}
?>

<div class="container">
    <h2>Đặt vé: <?= htmlspecialchars($selectedMovie['title']) ?></h2>
    <div class="movie-detail">
        <img src="<?= htmlspecialchars($selectedMovie['image']) ?>" alt="<?= htmlspecialchars($selectedMovie['title']) ?>">
        <h3><?= htmlspecialchars($selectedMovie['title']) ?></h3>
        <p><strong>Thể loại:</strong> <?= htmlspecialchars($selectedMovie['genre']) ?></p>
        <p><strong>Thời lượng:</strong> <?= htmlspecialchars($selectedMovie['duration']) ?></p>
    </div>

    <h3>Chọn suất chiếu</h3>
    <div class="showtime-list">
        <?php
        $movieShowtimes = array_filter($showtimes, function($showtime) use ($movieId) {
            return $showtime['movie_id'] == $movieId;
        });

        if (empty($movieShowtimes)) {
            echo "<p>Chưa có suất chiếu cho phim này.</p>";
        } else {
            foreach ($movieShowtimes as $showtime) {
                $isPast = (new DateTime($showtime['datetime'], new DateTimeZone('+0700'))) < (new DateTime('now', new DateTimeZone('+0700')));
                ?>
                <div class="showtime-card">
                    <p><strong>Thời gian:</strong> <?= htmlspecialchars($showtime['datetime']) ?> <?php if ($isPast): ?> (Đã qua)<?php endif; ?></p>
                    <p><strong>Phòng chiếu:</strong> <?= htmlspecialchars($showtime['room']) ?></p>
                    <?php if (!$isPast): ?>
                        <a href="booking.php?showtime_id=<?= $showtime['id'] ?>" class="btn">Chọn suất</a>
                    <?php else: ?>
                        <p style="color: gray;">Đã hết thời gian đặt vé</p>
                    <?php endif; ?>
                </div>
                <?php
            }
        }
        ?>
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