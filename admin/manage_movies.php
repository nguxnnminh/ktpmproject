<?php
include '../includes/admin_header.php';
include '../includes/data.php';

$movies = loadData('../data/movies.json');
$showtimes = loadData('../data/showtimes.json');
$bookings = loadData('../data/bookings.json');

$errors = [];
$success = false;
$deleteMessage = '';

if (isset($_GET['delete'])) {
    $movieId = intval($_GET['delete']);
    $canDelete = true;
    $movieTitle = '';

    foreach ($movies as $movie) {
        if ($movie['id'] === $movieId) {
            $movieTitle = $movie['title'];
            break;
        }
    }

    foreach ($showtimes as $showtime) {
        if ($showtime['movie_id'] === $movieId) {
            $canDelete = false;
            $errors[] = "Không thể xóa phim '$movieTitle' vì phim này đang có suất chiếu.";
            break;
        }
    }

    if ($canDelete) {
        foreach ($bookings as $booking) {
            foreach ($showtimes as $showtime) {
                if ($showtime['id'] === $booking['showtime_id'] && $showtime['movie_id'] === $movieId) {
                    $canDelete = false;
                    $errors[] = "Không thể xóa phim '$movieTitle' vì phim này có vé đã đặt.";
                    break 2;
                }
            }
        }
    }

    if ($canDelete) {
        $movies = array_filter($movies, fn($m) => $m['id'] !== $movieId);
        $movies = array_values($movies);
        file_put_contents('../data/movies.json', json_encode($movies, JSON_PRETTY_PRINT));
        $deleteMessage = "Xóa phim '$movieTitle' thành công!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $duration = trim($_POST['duration']);
    $image = trim($_POST['image']);

    if (empty($title) || empty($genre) || empty($duration) || empty($image)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $newMovie = [
            "id" => count($movies) + 1,
            "title" => $title,
            "genre" => $genre,
            "duration" => $duration,
            "image" => $image
        ];
        $movies[] = $newMovie;
        file_put_contents('../data/movies.json', json_encode($movies, JSON_PRETTY_PRINT));
        $success = true;
    }
}
?>

<div class="container">
    <h2>🎬 Quản lý phim</h2>

    <!-- Form thêm phim -->
    <h3>Thêm phim mới</h3>
    <?php if ($success): ?>
        <p style="color: green;">🎉 Thêm phim thành công!</p>
    <?php endif; ?>
    <?php if (!empty($deleteMessage)): ?>
        <p style="color: green;">🎉 <?= htmlspecialchars($deleteMessage) ?></p>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $e): ?>
                <p>⚠️ <?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label>Tiêu đề:</label><br>
        <input type="text" name="title" required><br><br>

        <label>Thể loại:</label><br>
        <input type="text" name="genre" required><br><br>

        <label>Thời lượng:</label><br>
        <input type="text" name="duration" required><br><br>

        <label>Hình ảnh (đường dẫn):</label><br>
        <input type="text" name="image" required><br><br>

        <button type="submit" class="btn">Thêm phim</button>
    </form>

    <!-- Danh sách phim -->
    <h3>Danh sách phim</h3>
    <?php if (empty($movies)): ?>
        <p>Chưa có phim nào.</p>
    <?php else: ?>
        <div class="showtime-list">
            <?php foreach ($movies as $movie): ?>
                <div class="showtime-card">
                    <div class="showtime-content">
                        <p><strong>Tiêu đề:</strong> <?= htmlspecialchars($movie['title']) ?></p>
                        <p><strong>Thể loại:</strong> <?= htmlspecialchars($movie['genre']) ?></p>
                        <p><strong>Thời lượng:</strong> <?= htmlspecialchars($movie['duration']) ?></p>
                        <p><strong>Hình ảnh:</strong> <?= htmlspecialchars($movie['image']) ?></p>
                    </div>
                    <div class="showtime-actions">
                        <a href="?delete=<?= $movie['id'] ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa phim này?')">Xóa</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>