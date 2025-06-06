<?php
include 'includes/header.php';  // Gọi menu + session
include 'includes/data.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if ($password !== $confirm) {
        $errors[] = "Mật khẩu nhập lại không khớp.";
    }

    $users = loadData('data/users.json');

    foreach ($users as $u) {
        if ($u['username'] === $username) {
            $errors[] = "Tên đăng nhập đã tồn tại.";
            break;
        }
    }

    if (empty($errors)) {
        // Tạo user_id mới (tăng từ số lớn nhất hiện có)
        $maxId = 0;
        foreach ($users as $u) {
            if (isset($u['user_id']) && $u['user_id'] > $maxId) {
                $maxId = $u['user_id'];
            }
        }
        $newUserId = $maxId + 1;

        // Thêm user mới với user_id và mật khẩu đã mã hóa
        $users[] = [
            "user_id" => $newUserId,
            "username" => $username,
            "password" => password_hash($password, PASSWORD_DEFAULT), // Mã hóa mật khẩu
            "role" => "user"
        ];
        saveData('data/users.json', $users); // Sử dụng saveData thay vì file_put_contents
        $success = true;
    }
}
?>

<div class="container">
    <h2>📝 Đăng ký tài khoản</h2>

    <?php if ($success): ?>
        <p style="color: green;">🎉 Đăng ký thành công! <a href="login.php">Đăng nhập ngay</a>.</p>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div style="color: red;">
                <?php foreach ($errors as $e): ?>
                    <p>⚠️ <?= htmlspecialchars($e) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label>Tên đăng nhập:</label><br>
            <input type="text" name="username" required><br><br>

            <label>Mật khẩu:</label><br>
            <input type="password" name="password" required><br><br>

            <label>Nhập lại mật khẩu:</label><br>
            <input type="password" name="confirm" required><br><br>

            <button type="submit" class="btn">Đăng ký</button>
        </form>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>