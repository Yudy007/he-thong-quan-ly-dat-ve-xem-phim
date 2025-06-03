<?php
session_start();

// Xóa toàn bộ session
$_SESSION = array();

// Hủy session
session_destroy();

// Chuyển hướng về trang login
header('Location: login.php');
exit;
?>