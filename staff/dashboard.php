<?php
require_once '../includes/auth.php';
checkRole('nhanvien');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển Nhân viên</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Bảng điều khiển Nhân viên</h1>
        <p>Xin chào, <?= $_SESSION['hoTen'] ?>! Dưới đây là các suất chiếu hôm nay.</p>
        
        <div class="today-shows">
            <h2>Suất chiếu hôm nay (<?= date('d/m/Y') ?>)</h2>
            
            <div class="show-grid">
                <?php foreach (getTodaySchedules() as $schedule): ?>
                <div class="show-card">
                    <h3><?= $schedule['TenPhim'] ?></h3>
                    <p>Phòng: <?= $schedule['MaPhong'] ?></p>
                    <p>Thời gian: <?= date('H:i', strtotime($schedule['ThoiGianBatDau'])) ?></p>
                    <p>Ghế trống: <?= $schedule['GheTrong'] ?>/<?= $schedule['TongGhe'] ?></p>
                    
                    <div class="show-actions">
                        <a href="ticket_checker.php?schedule_id=<?= $schedule['MaSuat'] ?>" 
                           class="btn">Kiểm tra vé</a>
                        <a href="seat_adjust.php?schedule_id=<?= $schedule['MaSuat'] ?>" 
                           class="btn">Điều chỉnh ghế</a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>