<?php
include '../includes/admin_header.php';
include '../includes/data.php';

$movies = loadData('../data/movies.json');
$showtimes = loadData('../data/showtimes.json');

// Xử lý thêm suất chiếu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_showtime'])) {
    $movie_id = intval($_POST['movie_id']);
    $datetime = trim($_POST['datetime']);
    $room = trim($_POST['room']);

    $newId = empty($showtimes) ? 1 : max(array_column($showtimes, 'id')) + 1;
    $showtimes[] = [
        "id" => $newId,
        "movie_id" => $movie_id,
        "datetime" => $datetime,
        "room" => $room
    ];
    file_put_contents('../data/showtimes.json', json_encode($showtimes, JSON_PRETTY_PRINT));
    header("Location: manage_showtimes.php");
    exit;
}

// Xử lý xóa suất chiếu
if (isset($_GET['delete_id'])) {
    $deleteId = intval($_GET['delete_id']);
    $showtimes = array_filter($showtimes, fn($s) => $s['id'] !== $deleteId);
    file_put_contents('../data/showtimes.json', json_encode(array_values($showtimes), JSON_PRETTY_PRINT));
    header("Location: manage_showtimes.php");
    exit;
}

$movieMap = [];
foreach ($movies as $m) {
    $movieMap[$m['id']] = $m['title'];
}
?>

<div class="container">
    <h2>🕒 Quản lý suất chiếu</h2>

    <!-- Form thêm suất chiếu -->
    <h3>Thêm suất chiếu mới</h3>
    <form method="POST">
        <label>Phim:</label><br>
        <select name="movie_id" required>
            <?php foreach ($movies as $m): ?>
                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['title']) ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Thời gian (YYYY-MM-DD HH:MM):</label><br>
        <input type="text" name="datetime" placeholder="2025-06-01 18:00" required><br><br>

        <label>Phòng chiếu:</label><br>
        <input type="text" name="room" placeholder="Phòng 1" required><br><br>

        <button type="submit" name="add_showtime" class="btn">Thêm suất chiếu</button>
    </form>

    <!-- Danh sách suất chiếu -->
    <h3>Danh sách suất chiếu</h3>
    <div class="showtime-list">
        <?php foreach ($showtimes as $s): ?>
            <div class="showtime-card">
                <p><strong>Phim:</strong> <?= htmlspecialchars($movieMap[$s['movie_id']] ?? 'Không rõ') ?></p>
                <p><strong>Thời gian:</strong> <?= htmlspecialchars($s['datetime']) ?></p>
                <p><strong>Phòng chiếu:</strong> <?= htmlspecialchars($s['room']) ?></p>
                <a href="?delete_id=<?= $s['id'] ?>" class="btn" style="background: #dc3545;" onclick="return confirm('Bạn có chắc muốn xóa suất chiếu này?')">Xóa</a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>