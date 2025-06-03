<?php
include '../includes/admin_header.php';
include '../includes/data.php';

$movies = loadData('../data/movies.json');
$showtimes = loadData('../data/showtimes.json');
$bookings = loadData('../data/bookings.json');
$users = loadData('../data/users.json');

$userCount = 0;
foreach ($users as $u) {
    if ($u['role'] === 'user') $userCount++;
}

// Dữ liệu cho biểu đồ
$showtimesPerMovie = [];
foreach ($movies as $m) {
    $showtimesPerMovie[$m['id']] = 0;
}
foreach ($showtimes as $s) {
    if (isset($showtimesPerMovie[$s['movie_id']])) {
        $showtimesPerMovie[$s['movie_id']]++;
    }
}
$labels = array_map(fn($m) => $m['title'], $movies);
$data = array_values($showtimesPerMovie);
?>

<div class="container">
    <h2>📊 Trang quản trị hệ thống</h2>
    <p>Xin chào <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong> (Admin)</p>

    <div class="ticket-list">
        <div class="ticket-card">
            <h3>🎬 Phim</h3>
            <p>Tổng số: <?= count($movies) ?></p>
            <a href="add_movie.php" class="btn">➕ Thêm phim</a>
        </div>

        <div class="ticket-card">
            <h3>🕒 Suất chiếu</h3>
            <p>Tổng số: <?= count($showtimes) ?></p>
            <a href="manage_showtimes.php" class="btn">⚙️ Quản lý suất chiếu</a>
        </div>

        <div class="ticket-card">
            <h3>🎟 Vé đã đặt</h3>
            <p>Tổng số: <?= count($bookings) ?></p>
        </div>

        <div class="ticket-card">
            <h3>👥 Khách hàng</h3>
            <p>Tổng số: <?= $userCount ?></p>
        </div>
    </div>

    <!-- Thêm biểu đồ -->
    <h2>📈 Thống kê suất chiếu theo phim</h2>
    <div style="max-width: 600px; margin: 0 auto;">
```chartjs
{
    "type": "bar",
    "data": {
        "labels": <?= json_encode($labels) ?>,
        "datasets": [{
            "label": "Số suất chiếu",
            "data": <?= json_encode($data) ?>,
            "backgroundColor": ["#28a745", "#007bff", "#dc3545"],
            "borderColor": ["#1e7e34", "#0056b3", "#c82333"],
            "borderWidth": 1
        }]
    },
    "options": {
        "scales": {
            "y": {
                "beginAtZero": true,
                "title": {
                    "display": true,
                    "text": "Số suất chiếu"
                }
            },
            "x": {
                "title": {
                    "display": true,
                    "text": "Phim"
                }
            }
        }
    }
}