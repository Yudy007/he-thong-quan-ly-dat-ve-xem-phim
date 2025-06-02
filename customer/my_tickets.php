<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');

$tickets = getMyTickets($_SESSION['MaND']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Vé của tôi</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h2>Vé đã đặt</h2>
        
        <?php if (empty($tickets)): ?>
            <div class="empty-state">
                <p>Bạn chưa đặt vé nào</p>
                <a href="home.php" class="btn">Xem phim đang chiếu</a>
            </div>
        <?php else: ?>
            <div class="tickets-list">
                <?php foreach ($tickets as $ticket): ?>
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <span class="ticket-id">#<?= $ticket['MaVe'] ?></span>
                            <span class="ticket-status <?= $ticket['TrangThai'] ?>">
                                <?= getStatusText($ticket['TrangThai']) ?>
                            </span>
                        </div>
                        
                        <div class="ticket-body">
                            <h3><?= $ticket['TenPhim'] ?></h3>
                            <p><?= date('d/m/Y H:i', strtotime($ticket['ThoiGianBatDau'])) ?></p>
                            <p>Phòng <?= $ticket['MaPhong'] ?> - Ghế <?= implode(', ', $ticket['DanhSachGhe']) ?></p>
                            <p class="ticket-price"><?= number_format($ticket['TongTien'], 0, ',', '.') ?> VNĐ</p>
                        </div>
                        
                        <div class="ticket-qr">
                            <img src="../assets/images/qr-codes/<?= $ticket['MaVe'] ?>.png" alt="QR Code">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>