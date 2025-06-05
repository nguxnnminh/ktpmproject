<?php
session_start();

if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    $_SESSION['login_message'] = "⚠️ Bạn cần đăng nhập để xem thông tin cá nhân.";
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/data.php';

// Giả định dữ liệu người dùng từ session
$user = $_SESSION['user'];

// Kiểm tra trạng thái chỉnh sửa (mặc định là chế độ xem)
$editMode = isset($_GET['edit']) && $_GET['edit'] === 'true';

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $user['name'] = $_POST['name'] ?? $user['name'];
    $user['phone'] = $_POST['phone'] ?? $user['phone'];
    $user['cccd'] = $_POST['cccd'] ?? $user['cccd'];
    $user['dob'] = $_POST['dob'] ?? $user['dob'];
    $user['address'] = $_POST['address'] ?? $user['address'];

    // Lấy danh sách người dùng từ users.json
    $users = loadData('data/users.json');
    foreach ($users as &$u) {
        if ($u['username'] === $user['username']) {
            // Giữ nguyên password từ bản ghi gốc
            $user['password'] = $u['password'];
            $u = $user; // Cập nhật toàn bộ thông tin
            break;
        }
    }

    // Cập nhật session
    $_SESSION['user'] = $user;
    // Ghi lại vào file users.json
    file_put_contents('data/users.json', json_encode($users, JSON_PRETTY_PRINT));

    // Chuyển về chế độ xem sau khi lưu
    header('Location: profile.php');
    exit;
}
?>

<!-- Phần HTML giữ nguyên -->
<div class="container">
    <h2>👤 Thông tin cá nhân</h2>

    <?php if ($editMode): ?>
        <!-- Chế độ chỉnh sửa -->
        <form method="POST" class="profile-form">
            <label for="name">Tên:</label>
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($user['name'] ?? '') ?>" required>

            <label for="phone">Số điện thoại:</label>
            <input type="tel" name="phone" id="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" pattern="[0-9]{10}" placeholder="10 chữ số" required>

            <label for="cccd">CCCD:</label>
            <input type="text" name="cccd" id="cccd" value="<?= htmlspecialchars($user['cccd'] ?? '') ?>" pattern="[0-9]{9,12}" placeholder="9-12 chữ số" required>

            <label for="dob">Ngày sinh:</label>
            <input type="date" name="dob" id="dob" value="<?= htmlspecialchars($user['dob'] ?? '') ?>" required>

            <label for="address">Địa chỉ:</label>
            <textarea name="address" id="address" required><?= htmlspecialchars($user['address'] ?? '') ?></textarea>

            <button type="submit" class="btn">Lưu thay đổi</button>
            <a href="profile.php" class="btn btn-cancel">Hủy</a>
        </form>
    <?php else: ?>
        <!-- Chế độ xem -->
        <div class="profile-view">
            <p><strong>Họ và Tên:</strong> <?= htmlspecialchars($user['name'] ?? 'Chưa cập nhật') ?></p>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($user['phone'] ?? 'Chưa cập nhật') ?></p>
            <p><strong>CCCD:</strong> <?= htmlspecialchars($user['cccd'] ?? 'Chưa cập nhật') ?></p>
            <p><strong>Ngày sinh:</strong> <?= htmlspecialchars($user['dob'] ?? 'Chưa cập nhật') ?></p>
            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($user['address'] ?? 'Chưa cập nhật') ?></p>
            <a href="profile.php?edit=true" class="btn">Chỉnh sửa</a>
        </div>
    <?php endif; ?>
</div>

<a id="back-to-top" href="#" title="Back to Top">↑</a>

<?php include 'includes/footer.php'; ?>

<script>
    window.onscroll = function() {
        let btn = document.getElementById("back-to-top");
        if (document.body.scrollTop > 300 || document.documentElement.scrollTop > 300) {
            btn.style.display = "flex";
        } else {
            btn.style.display = "none";
        }
    };

    document.getElementById("back-to-top").addEventListener("click", function(e) {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
</script>