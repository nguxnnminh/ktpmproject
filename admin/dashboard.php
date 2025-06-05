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

// Toggle hiển thị chi tiết vé
$showBookingDetails = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_bookings'])) {
    $showBookingDetails = !isset($_POST['toggle_bookings_hidden']) || $_POST['toggle_bookings_hidden'] !== '1';
}
?>

<div class="container">
    <h2>📊 Trang quản trị hệ thống</h2>
    <p>Xin chào <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong> (Admin)</p>

    <div class="ticket-list">
        <div class="ticket-card">
            <h3>🎬 Phim</h3>
            <p>Tổng số: <?= count($movies) ?></p>
            <a href="manage_movies.php" class="btn">➕ Thêm phim</a>
        </div>

        <div class="ticket-card">
            <h3>🕒 Suất chiếu</h3>
            <p>Tổng số: <?= count($showtimes) ?></p>
            <a href="manage_showtimes.php" class="btn">⚙️ Quản lý suất chiếu</a>
        </div>

        <div class="ticket-card">
            <h3>🎟 Vé đã đặt</h3>
            <p>Tổng số: <?= count($bookings) ?></p>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="toggle_bookings_hidden" value="<?= $showBookingDetails ? '1' : '0' ?>">
                <button type="submit" name="toggle_bookings" class="btn">Xem chi tiết</button>
            </form>
        </div>

        <div class="ticket-card">
            <h3>👥 Khách hàng</h3>
            <p>Tổng số: <?= $userCount ?></p>
        </div>
    </div>

    <!-- Thêm biểu đồ -->
    <h2>📈 Thống kê suất chiếu theo phim</h2>
    <div style="max-width: 800px; margin: 0 auto;">
        <?php if (empty($labels) || empty($data)): ?>
            <p style="color: #dc3545; font-weight: bold; text-align: center;">Không có dữ liệu</p>
        <?php else: ?>
            <canvas id="showtimeChart"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('showtimeChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($labels) ?>,
                        datasets: [{
                            label: 'Số suất chiếu',
                            data: <?= json_encode($data) ?>,
                            backgroundColor: '#dc3545',
                            borderColor: '#c82333',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Số suất chiếu'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Phim'
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    autoSkip: false
                                }
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </div>

    <!-- Hiển thị chi tiết vé đã đặt -->
    <?php if ($showBookingDetails && !empty($bookings)): ?>
        <h2>📋 Chi tiết vé đã đặt</h2>
        <div class="booking-details">
            <table border="1" style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                <thead>
                    <tr style="background-color: #007bff; color: white;">
                        <th>Phim</th>
                        <th>Thời gian chiếu</th>
                        <th>Phòng chiếu</th>
                        <th>Ghế đã đặt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <?php
                        $showtime = array_filter($showtimes, fn($s) => $s['id'] === $booking['showtime_id']);
                        $showtime = array_values($showtime)[0] ?? null;
                        if ($showtime) {
                            $movie = array_filter($movies, fn($m) => $m['id'] === $showtime['movie_id']);
                            $movie = array_values($movie)[0] ?? ['title' => 'Không xác định'];
                        } else {
                            $movie = ['title' => 'Không xác định'];
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($movie['title']) ?></td>
                            <td><?= htmlspecialchars($showtime['datetime'] ?? 'Không xác định') ?></td>
                            <td><?= htmlspecialchars($showtime['room'] ?? 'Không xác định') ?></td>
                            <td><?= htmlspecialchars(implode(', ', $booking['seats'] ?? ['Không xác định'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form method="POST" style="display: inline; margin-top: 10px;">
                <input type="hidden" name="toggle_bookings_hidden" value="1">
                <button type="submit" name="toggle_bookings" class="btn">Ẩn chi tiết</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
