<?php
// Không gọi session_start() vì đã xử lý ở file chính

// Kiểm tra session để hiển thị menu
$menuItems = [];
if (!isset($_SESSION['user'])) {
    $menuItems = [
        ['label' => 'Đăng ký', 'href' => 'register.php'],
        ['label' => 'Đăng nhập', 'href' => 'login.php']
    ];
} elseif ($_SESSION['user']['role'] === 'admin') {
    $menuItems = [
        ['label' => 'Quản lý phim', 'href' => 'admin/dashboard.php'],
        ['label' => 'Quản lý suất chiếu', 'href' => 'admin/manage_showtimes.php'],
        ['label' => "Xin chào, " . htmlspecialchars($_SESSION['user']['username']), 'href' => 'profile.php'],
        ['label' => 'Đăng xuất', 'href' => 'logout.php']
    ];
} else {
    $menuItems = [
        ['label' => 'Vé đã đặt', 'href' => 'tickets.php'],
        ['label' => "Xin chào, " . htmlspecialchars($_SESSION['user']['username']), 'href' => 'profile.php'],
        ['label' => 'Đăng xuất', 'href' => 'logout.php']
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>CGV Booking</title>
    <link rel="stylesheet" href="/movie-booking/assets/style.css">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
</head>
<body>

<div class="navbar">
    <h2>CGV Booking</h2>
    <ul class="menu">
        <li class="menu-item"><a href="index.php">Trang chủ</a></li>
        <li class="menu-item"><a href="all_movies.php">Phim</a></li>
        <li class="menu-item"><a href="promotions.php">Khuyến mãi</a></li>
        <?php foreach ($menuItems as $item): ?>
            <li class="menu-item"><a href="<?= $item['href'] ?>"><?= $item['label'] ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
