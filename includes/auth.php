<?php
/**
 * Kiểm tra đã đăng nhập chưa
 */
function isLoggedIn() {
    return isset($_SESSION['user']);
}

/**
 * Kiểm tra có phải admin không
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['user']['role'] === 'admin';
}

/**
 * Bắt buộc đăng nhập mới được tiếp tục
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        $_SESSION['login_message'] = "⚠️ Bạn cần đăng nhập để tiếp tục.";
        header("Location: login.php");
        exit;
    }
}

/**
 * Bắt buộc là admin mới được truy cập
 */
function requireAdmin() {
    if (!isAdmin()) {
        header("Location: ../login.php");
        exit;
    }
}
