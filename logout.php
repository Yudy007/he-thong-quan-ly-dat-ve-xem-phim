<?php
session_start();

// Xóa tất cả dữ liệu session
$_SESSION = array();

// Hủy session
session_destroy();

// Chuyển hướng về trang đăng nhập
header('Location: login.php');
exit;
?>