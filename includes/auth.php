<?php
// auth.php - Kiểm tra đăng nhập và phân quyền người dùng

session_start();

/**
 * Kiểm tra nếu người dùng chưa đăng nhập thì chuyển về login
 */
function checkLogin() {
    if (!isset($_SESSION['MaND'])) {
        header("Location: ../login.php");
        exit;
    }
}

/**
 * Kiểm tra nếu người dùng không có vai trò phù hợp thì chặn truy cập
 * @param string $expectedRole Vai trò yêu cầu: 'admin', 'nhanvien', 'khachhang'
 */
function checkRole($expectedRole) {
    checkLogin();
    if (!isset($_SESSION['VaiTro']) || $_SESSION['VaiTro'] !== $expectedRole) {
        header("Location: ../unauthorized.php"); // hoặc index.php nếu chưa có trang riêng
        exit;
    }
}

/**
 * Kiểm tra nếu vai trò nằm trong danh sách cho phép
 * @param array $allowedRoles Mảng các vai trò cho phép
 */
function checkRoles($allowedRoles = []) {
    checkLogin();
    if (!in_array($_SESSION['VaiTro'], $allowedRoles)) {
        header("Location: ../unauthorized.php");
        exit;
    }
}

/**
 * Lấy vai trò người dùng hiện tại (admin, nhanvien, khachhang)
 * @return string|false
 */
function currentRole() {
    return $_SESSION['VaiTro'] ?? false;
}

/**
 * Lấy ID người dùng hiện tại
 * @return string|false
 */
function currentUserId() {
    return $_SESSION['MaND'] ?? false;
}
?>
