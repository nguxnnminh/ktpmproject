<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
ob_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>CGV Quản trị</title>
    <!-- Đường dẫn tuyệt đối -->
    <link rel="stylesheet" href="/movie-booking/assets/style.css">
    <!-- Đường dẫn tương đối -->
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

<div class="navbar">
    <h2>🎬 CGV Quản trị</h2>
    <ul class="menu">
        <li class="menu-item"><a href="dashboard.php">📊 Thống kê</a></li>
        <li class="menu-item"><a href="manage_movies.php">🎬 Quản lý phim</a></li>
        <li class="menu-item"><a href="manage_showtimes.php">🕒 Quản lý suất chiếu</a></li>
        <li class="menu-item"><strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong></li>
        <li class="menu-item"><a href="../logout.php">🚪 Đăng xuất</a></li>
    </ul>
</div>