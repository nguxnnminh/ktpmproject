<?php
include '../includes/admin_header.php';
include '../includes/data.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $duration = trim($_POST['duration']);
    $image = trim($_POST['image']);

    if (empty($title) || empty($genre) || empty($duration) || empty($image)) {
        $errors[] = "Vui lòng điền đầy đủ thông tin.";
    } else {
        $movies = loadData('../data/movies.json');
        $newId = empty($movies) ? 1 : max(array_column($movies, 'id')) + 1;
        $movies[] = [
            "id" => $newId,
            "title" => $title,
            "genre" => $genre,
            "duration" => $duration,
            "image" => $image
        ];
        file_put_contents('../data/movies.json', json_encode($movies, JSON_PRETTY_PRINT));
        $success = true;
    }
}
?>

<div class="container">
    <h2>🎬 Thêm phim mới</h2>

    <?php if ($success): ?>
        <p style="color: green;">🎉 Thêm phim thành công! <a href="dashboard.php">Quay lại Dashboard</a></p>
    <?php else: ?>
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

            <label>Thời lượng (phút):</label><br>
            <input type="text" name="duration" required><br><br>

            <label>Đường dẫn hình ảnh:</label><br>
            <input type="text" name="image" placeholder="assets/image.jpg" required><br><br>

            <button type="submit" class="btn">Thêm phim</button>
        </form>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>