<?php
session_start();
include 'includes/data.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $users = loadData('data/users.json');
    $found = false;

    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === $password) {
            // Lấy toàn bộ thông tin từ bản ghi gốc
            $_SESSION['user'] = $user;
            $found = true;

            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                unset($_SESSION['login_message']);
                header("Location: $redirect");
                exit;
            }

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        }
    }

    if (!$found) {
        $errors[] = "Sai tên đăng nhập hoặc mật khẩu.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container">
    <h2>🔐 Đăng nhập</h2>

    <?php if (isset($_SESSION['login_message'])): ?>
        <p style="color: orange; font-weight: bold;"><?= $_SESSION['login_message'] ?></p>
        <?php unset($_SESSION['login_message']); ?>
    <?php endif; ?>

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

        <button type="submit" class="btn">Đăng nhập</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>