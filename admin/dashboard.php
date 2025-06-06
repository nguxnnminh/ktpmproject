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

// Dữ liệu biểu đồ
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

// Toggle hiển thị vé đã đặt
$showBookingDetails = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_bookings'])) {
    $showBookingDetails = !isset($_POST['toggle_bookings_hidden']) || $_POST['toggle_bookings_hidden'] !== '1';
}
?>

<style>
.container {
    padding: 30px;
}

.ticket-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 30px;
}

.ticket-card {
    flex: 1 1 calc(25% - 20px);
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    padding: 20px;
    text-align: center;
    min-width: 220px;
}

.ticket-card h3 {
    margin-bottom: 10px;
}

.ticket-card .btn {
    background-color: #FFD700;
    color: black;
    font-weight: bold;
    padding: 12px 24px;
    display: flex; /* đổi inline-block thành flex */
    align-items: center; /* căn giữa theo chiều dọc */
    justify-content: center; /* căn giữa theo chiều ngang */
    text-decoration: none;
    border-radius: 8px;
    margin-top: 10px;
    width: 100%;
    box-sizing: border-box;
    transition: background-color 0.2s ease;
    min-height: 48px;
    line-height: 1.2rem;
}

.ticket-card .btn:hover {
    background-color: #e6be00;
}

.booking-details table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.booking-details th, .booking-details td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: center;
}

.booking-details th {
    background-color: #007bff;
    color: white;
}

.section-title {
    color: #b30000;
    font-size: 1.6rem;
    margin: 30px 0 10px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title::after {
    content: "";
    display: block;
    height: 4px;
    width: 40px;
    background: #FFD700;
    margin-left: 10px;
    border-radius: 2px;
}
</style>

<div class="container">
    <h2 class="section-title">📊 Trang quản trị hệ thống</h2>
    <p>Xin chào <strong><?= htmlspecialchars($_SESSION['user']['username']) ?></strong> (Admin)</p>

    <div class="ticket-list">
        <div class="ticket-card">
            <h3>🎬 Phim</h3>
            <p>Tổng số: <?= count($movies) ?></p>
            <a href="manage_movies.php" class="btn">Quản lý phim</a>
        </div>

        <div class="ticket-card">
            <h3>🕒 Suất chiếu</h3>
            <p>Tổng số: <?= count($showtimes) ?></p>
            <a href="manage_showtimes.php" class="btn">Quản lý suất chiếu</a>
        </div>

        <div class="ticket-card">
            <h3>🎟 Vé đã đặt</h3>
            <p>Tổng số: <?= count($bookings) ?></p>
            <form method="POST" style="display: inline;">
                <input type="hidden" name="toggle_bookings_hidden" value="<?= $showBookingDetails ? '1' : '0' ?>">
                <button type="submit" name="toggle_bookings" class="btn">
                    <?= $showBookingDetails ? 'Ẩn chi tiết' : 'Xem chi tiết' ?>
                </button>
            </form>
        </div>

        <div class="ticket-card">
            <h3>👥 Khách hàng</h3>
            <p>Tổng số: <?= $userCount ?></p>
        </div>
    </div>

    <h2 class="section-title">📈 Thống kê suất chiếu theo phim</h2>
    <div style="max-width: 1200px; margin: 0 auto; height: 400px;">
        <?php if (empty($labels) || empty($data)): ?>
            <p style="color: #dc3545; font-weight: bold; text-align: center;">Không có dữ liệu</p>
        <?php else: ?>
            <canvas id="showtimesChart" height="400"></canvas>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('showtimesChart').getContext('2d');
                const chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($labels, JSON_UNESCAPED_UNICODE) ?>,
                        datasets: [{
                            label: 'Số suất chiếu',
                            data: <?= json_encode($data) ?>,
                            backgroundColor: '#dc3545',
                            borderColor: '#c82333',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
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
                                    maxRotation: 0,
                                    minRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: 15
                                }
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>
    </div>

    <?php if ($showBookingDetails && !empty($bookings)): ?>
        <h2 class="section-title">📋 Chi tiết vé đã đặt</h2>
        <div class="booking-details">
            <table>
                <thead>
                    <tr>
                        <th>Phim</th>
                        <th>Thời gian chiếu</th>
                        <th>Phòng chiếu</th>
                        <th>Ghế đã đặt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <?php
                        $showtime = array_values(array_filter($showtimes, fn($s) => $s['id'] === $booking['showtime_id']))[0] ?? null;
                        if ($showtime) {
                            $movie = array_values(array_filter($movies, fn($m) => $m['id'] === $showtime['movie_id']))[0] ?? ['title' => 'Không xác định'];
                            $datetimeObj = new DateTime($showtime['datetime']);
                            $formattedDatetime = $datetimeObj->format('d/m/Y H:i');
                        } else {
                            $movie = ['title' => 'Không xác định'];
                            $formattedDatetime = 'Không xác định';
                        }
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($movie['title']) ?></td>
                            <td><?= htmlspecialchars($formattedDatetime) ?></td>
                            <td><?= htmlspecialchars($showtime['room'] ?? 'Không xác định') ?></td>
                            <td><?= htmlspecialchars(implode(', ', $booking['seats'] ?? ['Không xác định'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
