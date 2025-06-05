<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');

// Kiểm tra xem có thông tin vé trong session không
if (!isset($_SESSION['last_booking'])) {
    header('Location: home.php');
    exit;
}

$booking = $_SESSION['last_booking'];
unset($_SESSION['last_booking']); // Xóa thông tin sau khi hiển thị
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
        <div class="success-container">
            <div class="success-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="#4CAF50" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
            </div>
            
            <h1>Đặt vé thành công!</h1>
            <p class="success-message">Cảm ơn bạn đã đặt vé tại hệ thống của chúng tôi</p>
            
            <div class="ticket-details">
                <h2>Thông tin vé</h2>
                
                <div class="ticket-info">
                    <div class="info-row">
                        <span class="info-label">Mã vé:</span>
                        <span class="info-value"><?= htmlspecialchars($booking['MaVe']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Phim:</span>
                        <span class="info-value"><?= htmlspecialchars($booking['TenPhim']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Suất chiếu:</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($booking['ThoiGianBatDau'])) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Phòng:</span>
                        <span class="info-value"><?= htmlspecialchars($booking['MaPhong']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Ghế:</span>
                        <span class="info-value"><?= htmlspecialchars(implode(', ', $booking['DanhSachGhe'])) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Tổng tiền:</span>
                        <span class="info-value price"><?= number_format($booking['TongTien'], 0, ',', '.') ?> VNĐ</span>
                    </div>
                </div>
                
                <div class="ticket-qr">
                    <img src="../assets/images/qr-codes/<?= htmlspecialchars($booking['MaVe']) ?>.png" alt="Mã QR vé">
                    <p>Quét mã QR tại rạp để nhận vé</p>
                </div>
            </div>
            
            <div class="actions">
                <a href="my_tickets.php" class="btn">Xem tất cả vé</a>
                <a href="home.php" class="btn btn-secondary">Về trang chủ</a>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>