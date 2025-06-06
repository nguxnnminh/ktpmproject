<?php
include '../includes/admin_header.php';
include '../includes/data.php';

$showtimes = loadData('../data/showtimes.json');
$bookings = loadData('../data/bookings.json');
$movies = loadData('../data/movies.json');

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movie_id = intval($_POST['movie_id']);
    $datetime = trim($_POST['datetime']);
    $room = trim($_POST['room']);

    if (empty($movie_id) || empty($datetime) || empty($room)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $newShowtime = [
            "id" => count($showtimes) + 1,
            "movie_id" => $movie_id,
            "datetime" => $datetime, // vẫn lưu theo định dạng 'Y-m-d\TH:i'
            "room" => $room
        ];
        $showtimes[] = $newShowtime;
        file_put_contents('../data/showtimes.json', json_encode($showtimes, JSON_PRETTY_PRINT));
        $success = true;
    }
}

if (isset($_GET['delete'])) {
    $showtimeId = intval($_GET['delete']);
    $canDelete = true;
    $showtimeDetails = '';

    foreach ($showtimes as $showtime) {
        if ($showtime['id'] === $showtimeId) {
            $showtimeDetails = "Suất chiếu vào " . $showtime['datetime'] . " tại " . $showtime['room'];
            break;
        }
    }

    foreach ($bookings as $booking) {
        if ($booking['showtime_id'] === $showtimeId) {
            $canDelete = false;
            $errors[] = "Không thể xóa suất chiếu '$showtimeDetails' vì có vé đã đặt.";
            break;
        }
    }

    if ($canDelete) {
        $showtimes = array_filter($showtimes, fn($s) => $s['id'] !== $showtimeId);
        $showtimes = array_values($showtimes);
        file_put_contents('../data/showtimes.json', json_encode($showtimes, JSON_PRETTY_PRINT));
        $success = true;
    }
}
?>

<div class="container">
    <h2>🕒 Quản lý suất chiếu</h2>

    <!-- Form thêm suất chiếu -->
    <h3>Thêm suất chiếu mới</h3>
    <?php if ($success): ?>
        <p style="color: green;">🎉 Thao tác thành công!</p>
    <?php endif; ?>
    <?php if (!empty($errors)): ?>
        <div style="color: red;">
            <?php foreach ($errors as $e): ?>
                <p>⚠️ <?= htmlspecialchars($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="POST">
        <label>Phim:</label><br>
        <select name="movie_id" required>
            <?php foreach ($movies as $movie): ?>
                <option value="<?= $movie['id'] ?>"><?= htmlspecialchars($movie['title']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Thời gian:</label><br>
        <input type="datetime-local" name="datetime" required><br><br>

        <label>Phòng chiếu:</label><br>
        <input type="text" name="room" required><br><br>

        <button type="submit" class="btn">Thêm suất chiếu</button>
    </form>

    <!-- Danh sách suất chiếu -->
    <h3>Danh sách suất chiếu</h3>
    <?php if (empty($showtimes)): ?>
        <p>Chưa có suất chiếu nào.</p>
    <?php else: ?>
        <div class="showtime-list">
            <?php foreach ($showtimes as $showtime): ?>
                <?php
                $movie = array_filter($movies, fn($m) => $m['id'] === $showtime['movie_id']);
                $movie = array_values($movie)[0] ?? ['title' => 'Không xác định'];
                $dt = DateTime::createFromFormat('Y-m-d\TH:i', $showtime['datetime']);
                $formattedDatetime = $dt ? $dt->format('d/m/Y H:i') : htmlspecialchars($showtime['datetime']);
                ?>
                <div class="showtime-card">
                    <div class="showtime-content">
                        <p><strong>Phim:</strong> <?= htmlspecialchars($movie['title']) ?></p>
                        <p><strong>Thời gian:</strong> <?= $formattedDatetime ?></p>
                        <p><strong>Phòng chiếu:</strong> <?= htmlspecialchars($showtime['room']) ?></p>
                    </div>
                    <div class="showtime-actions">
                        <a href="?delete=<?= $showtime['id'] ?>" class="btn-delete" onclick="return confirm('Bạn có chắc chắn muốn xóa suất chiếu này?')">Xóa</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
