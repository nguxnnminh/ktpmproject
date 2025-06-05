<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    $_SESSION['login_message'] = "⚠️ Bạn cần đăng nhập để tiếp tục đặt vé.";
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/data.php';

$showtimeId = isset($_GET['showtime_id']) ? intval($_GET['showtime_id']) : 0;
$showtimes = loadData('data/showtimes.json');
$movies = loadData('data/movies.json');
$bookings = loadData('data/bookings.json');

// Tìm suất chiếu
$showtime = null;
foreach ($showtimes as $s) {
    if ($s['id'] == $showtimeId) {
        $showtime = $s;
        break;
    }
}

if (!$showtime) {
    echo "<div class='container'><h2>Suất chiếu không tồn tại.</h2></div>";
    include 'includes/footer.php';
    exit;
}

// Tìm tên phim
$movieTitle = '';
foreach ($movies as $m) {
    if ($m['id'] == $showtime['movie_id']) {
        $movieTitle = $m['title'];
        break;
    }
}

// Ghế đã đặt
$bookedSeats = [];
foreach ($bookings as $b) {
    if ($b['showtime_id'] == $showtimeId) {
        $bookedSeats = array_merge($bookedSeats, $b['seats']);
    }
}

// Trong phần xử lý POST trong booking.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['seats'])) {
    $selectedSeats = $_POST['seats'];

    // Kiểm tra xem ghế đã được đặt chưa
    $alreadyBooked = array_intersect($selectedSeats, $bookedSeats);
    if (!empty($alreadyBooked)) {
        echo "<div class='container'><h2>⚠️ Một số ghế đã được đặt: " . implode(", ", $alreadyBooked) . ". Vui lòng chọn ghế khác.</h2></div>";
        echo "<div class='container'><a href='booking.php?showtime_id=$showtimeId' class='btn'>← Chọn lại ghế</a></div>";
        include 'includes/footer.php';
        exit;
    }

    // Lưu thông tin đặt vé với user_id
    $bookings[] = [
        "showtime_id" => $showtimeId,
        "seats" => $selectedSeats,
        "user_id" => $_SESSION['user']['id'], // Thêm user_id từ session
        "booking_time" => date('Y-m-d H:i:s')
    ];

    file_put_contents('data/bookings.json', json_encode($bookings, JSON_PRETTY_PRINT));
    echo "<div class='container'><h2>🎟️ Đặt vé thành công cho các ghế: " . implode(", ", $selectedSeats) . "</h2></div>";
    echo "<div class='container'><p>Phim: $movieTitle | Suất chiếu: {$showtime['datetime']} | Phòng: {$showtime['room']}</p></div>";
    echo "<div class='container'><a href='index.php' class='btn'>← Quay về trang chủ</a></div>";
    include 'includes/footer.php';
    exit;
}

// Ghế giả lập
$rows = ['A', 'B', 'C', 'D', 'E', 'F'];
$cols = range(1, 10);
?>

<div class="container">
    <h2>🎟️ Đặt vé cho: <?= htmlspecialchars($movieTitle) ?></h2>
    <p>🕒 <?= $showtime['datetime'] ?> | 📍 <?= $showtime['room'] ?></p>
    <!-- Xóa dòng này -->
    <!-- <p><strong>Đang đặt vé với tài khoản:</strong> Admin</p> -->

    <div class="seat-map-container">
        <h3>Sơ đồ ghế ngồi</h3>
        <div class="screen">Màn hình</div>
        <form method="POST" id="seatForm">
            <div class="seat-map">
                <?php foreach ($rows as $row): ?>
                    <div class="seat-row">
                        <?php foreach ($cols as $col): 
                            $seat = $row . $col;
                            $isBooked = in_array($seat, $bookedSeats);
                            $isSelected = isset($_POST['seats']) && in_array($seat, $_POST['seats']);
                            ?>
                            <label class="seat-label">
                                <input type="checkbox" name="seats[]" value="<?= $seat ?>" <?= $isBooked ? 'disabled' : '' ?> onchange="updateSelectedSeats()">
                                <span class="seat <?= $isBooked ? 'booked' : ($isSelected ? 'selected' : '') ?>"><?= $seat ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <p><strong>Ghế bạn đang chọn:</strong> <span id="selectedSeats">Chưa chọn</span></p>
            <p><small>Ghế màu xanh navy: Trống | Ghế màu xám: Đã đặt | Ghế màu vàng: Đã chọn</small></p>

            <button type="submit" class="btn">Xác nhận đặt vé</button>
        </form>
    </div>
</div>

<script>
function updateSelectedSeats() {
    const checked = document.querySelectorAll('input[name="seats[]"]:checked');
    const seatList = Array.from(checked).map(cb => cb.value);
    document.getElementById("selectedSeats").textContent = seatList.length > 0 ? seatList.join(', ') : 'Chưa chọn';
}
</script>

<?php include 'includes/footer.php'; ?>