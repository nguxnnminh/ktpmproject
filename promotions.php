<?php
include 'includes/header.php';
?>

<div class="container">
    <h2>🎁 Khuyến mãi</h2>
    <div class="showtime-list">
        <div class="showtime-card">
            <p><strong>Khuyến mãi 1:</strong> Giảm 20% cho vé đôi vào thứ 4!</p>
            <p><strong>Thời gian áp dụng:</strong> 01/06/2025 - 07/06/2025</p>
            <a href="#" class="btn">Xem chi tiết</a>
        </div>
        <div class="showtime-card">
            <p><strong>Khuyến mãi 2:</strong> Mua 1 tặng 1 vào thứ 6 hàng tuần!</p>
            <p><strong>Thời gian áp dụng:</strong> 06/06/2025 - 13/06/2025</p>
            <a href="#" class="btn">Xem chi tiết</a>
        </div>
    </div>
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