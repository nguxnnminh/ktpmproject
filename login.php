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
        if ($user['username'] === $username && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $found = true;

            $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : null;
            unset($_SESSION['redirect_after_login']);
            unset($_SESSION['login_message']);

            if ($user['role'] === 'admin') {
                header("Location: admin/dashboard.php");
                exit;
            } else {
                header("Location: " . ($redirect ?: 'index.php'));
                exit;
            }
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
