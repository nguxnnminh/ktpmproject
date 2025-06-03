<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>CGV Booking</title>
    <link rel="stylesheet" href="/movie-booking/assets/style.css">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="navbar">
    <h2>🎬 CGV Booking</h2>
    <ul class="menu">
        <li class="menu-item"><a href="index.php">Trang chủ</a></li>
        <li class="menu-item"><a href="all_movies.php">Phim</a></li>
        <li class="menu-item"><a href="promotions.php">Khuyến mãi</a></li>

        <?php if (!isset($_SESSION['user'])): ?>
            <li class="menu-item"><a href="register.php">Đăng ký</a></li>
            <li class="menu-item"><a href="login.php">Đăng nhập</a></li>
        <?php elseif ($_SESSION['user']['role'] === 'admin'): ?>
            <li class="menu-item"><a href="admin/dashboard.php">Quản lý phim</a></li>
            <li class="menu-item"><a href="admin/manage_showtimes.php">Quản lý suất chiếu</a></li>
            <li class="menu-item"><a href="profile.php">Xin chào, <?= htmlspecialchars($_SESSION['user']['username']) ?></a></li>
            <li class="menu-item"><a href="logout.php">Đăng xuất</a></li>
        <?php else: ?>
            <li class="menu-item"><a href="tickets.php">Vé đã đặt</a></li>
            <li class="menu-item"><a href="profile.php">Xin chào, <?= htmlspecialchars($_SESSION['user']['username']) ?></a></li>
            <li class="menu-item"><a href="logout.php">Đăng xuất</a></li>
        <?php endif; ?>
    </ul>
</div>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">