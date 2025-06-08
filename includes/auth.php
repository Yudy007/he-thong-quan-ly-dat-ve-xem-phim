<?php
// auth.php - Kiểm tra đăng nhập và phân quyền người dùng

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400, // 1 ngày
        'cookie_secure' => true,    // Chỉ gửi cookie qua HTTPS
        'cookie_httponly' => true,  // Chống truy cập cookie bằng JS
        'use_strict_mode' => true   // Bảo mật session ID
    ]);
}

// Danh sách vai trò hợp lệ
define('VALID_ROLES', ['admin', 'nhanvien', 'khachhang']);

/**
 * Kiểm tra trạng thái đăng nhập
 * @throws RuntimeException Nếu session không an toàn
 */
function checkLogin() {
    // Kiểm tra session fixation
    if (!isset($_SESSION['initiated'])) {
        session_regenerate_id(true);
        $_SESSION['initiated'] = true;
    }

    // Kiểm tra session hijacking
    if (!isset($_SESSION['user_agent'])) {
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
    } elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
        session_destroy();
        throw new RuntimeException('Session không hợp lệ');
    }

    if (!isset($_SESSION['MaND'])) {
        header("Location: ../login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

/**
 * Kiểm tra vai trò người dùng
 * @param string|array $expectedRole Vai trò hoặc mảng vai trò cho phép
 * @param bool $strict True nếu yêu cầu khớp chính xác
 */
function checkRole($expectedRole, bool $strict = true) {
    checkLogin();
    
    if (!isset($_SESSION['VaiTro'])) {
        header("Location: ../logout.php?reason=invalid_role");
        exit;
    }

    $currentRole = $_SESSION['VaiTro'];
    
    // Kiểm tra vai trò hợp lệ
    if (!in_array($currentRole, VALID_ROLES, true)) {
        header("Location: ../logout.php?reason=invalid_role");
        exit;
    }

    // Xử lý cả mảng vai trò
    if (is_array($expectedRole)) {
        if (!in_array($currentRole, $expectedRole, true)) {
            header("Location: ../unauthorized.php");
            exit;
        }
    } 
    // Xử lý vai trò đơn lẻ
    elseif ($strict ? ($currentRole !== $expectedRole) : ($currentRole != $expectedRole)) {
        header("Location: ../unauthorized.php");
        exit;
    }
}

/**
 * Lấy thông tin người dùng hiện tại
 * @return array [
 *     'id' => string,
 *     'role' => string,
 *     'username' => string,
 *     'last_active' => int
 * ]
 */
function currentUser(): array {
    checkLogin();
    return [
        'id' => $_SESSION['MaND'] ?? null,
        'role' => $_SESSION['VaiTro'] ?? null,
        'username' => $_SESSION['TenDangNhap'] ?? null,
        'last_active' => $_SESSION['last_active'] ?? null
    ];
}

/**
 * Ghi nhận hoạt động cuối cùng
 */
function updateLastActivity() {
    if (isset($_SESSION['MaND'])) {
        $_SESSION['last_active'] = time();
    }
}

// Tự động cập nhật thời gian hoạt động
updateLastActivity();

// Kiểm tra timeout (30 phút không hoạt động)
if (isset($_SESSION['last_active']) && (time() - $_SESSION['last_active'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: ../login.php?reason=timeout");
    exit;
}