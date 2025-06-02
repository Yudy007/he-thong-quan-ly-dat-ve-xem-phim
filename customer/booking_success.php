<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');

if (!isset($_GET['schedule_id'])) {
    header('Location: home.php');
    exit;
}

$scheduleId = $_GET['schedule_id'];
$lastBooking = getLastBooking($_SESSION['MaND'], $scheduleId);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt vé thành công</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <div class="success-message">
            <h2>🎉 Đặt vé thành công!</h2>
            <p>Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi</p>
        </div>
        
        <div class="ticket-details">
            <h3>Thông tin vé</h3>
            <p><strong>Mã vé:</strong> <?= $lastBooking['MaVe'] ?></p>
            <p><strong>Phim:</strong> <?= $lastBooking['TenPhim'] ?></p>
            <p><strong>Suất chiếu:</strong> <?= date('d/m/Y H:i', strtotime($lastBooking['ThoiGianBatDau'])) ?></p>
            <p><strong>Phòng:</strong> <?= $lastBooking['MaPhong'] ?></p>
            <p><strong>Ghế:</strong> <?= implode(', ', $lastBooking['DanhSachGhe']) ?></p>
            <p><strong>Tổng tiền:</strong> <?= number_format($lastBooking['TongTien'], 0, ',', '.') ?> VNĐ</p>
        </div>
        
        <div class="actions">
            <a href="my_tickets.php" class="btn">Xem vé của tôi</a>
            <a href="home.php" class="btn">Về trang chủ</a>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>