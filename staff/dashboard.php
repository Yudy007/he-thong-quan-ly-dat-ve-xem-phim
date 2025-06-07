<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('nhanvien');
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển Nhân viên</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .welcome-section { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 25px; border-radius: 15px; margin: 20px 0; text-align: center; }
        .show-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .show-card { background: white; border: 1px solid #ddd; border-radius: 10px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .show-card:hover { transform: translateY(-5px); }
        .show-card h3 { color: #007bff; margin-bottom: 15px; }
        .show-actions { margin-top: 15px; }
        .show-actions .btn { margin-right: 10px; margin-bottom: 10px; }
        .btn { padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block; transition: background 0.3s; }
        .btn:hover { background: #0056b3; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }
        .info-section { background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; }
        .quick-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: linear-gradient(135deg, #17a2b8, #138496); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="welcome-section">
            <h1>🎫 Chào mừng Nhân viên <?= htmlspecialchars($_SESSION['hoTen']) ?>!</h1>
            <p>Quản lý kiểm tra vé và hỗ trợ khách hàng</p>
        </div>

        <!-- Thống kê nhanh -->
        <div class="quick-stats">
            <?php
            $todaySchedules = getTodaySchedules();
            $totalShows = count($todaySchedules);
            $totalSeats = array_sum(array_column($todaySchedules, 'TONGGHE'));
            $availableSeats = array_sum(array_column($todaySchedules, 'GHETRONG'));
            ?>
            <div class="stat-card">
                <div class="stat-number"><?= $totalShows ?></div>
                <div>Suất chiếu hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $totalSeats - $availableSeats ?></div>
                <div>Vé đã bán</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $availableSeats ?></div>
                <div>Ghế còn trống</div>
            </div>
        </div>

        <div class="today-shows">
            <h2>📅 Suất chiếu hôm nay (<?= date('d/m/Y') ?>)</h2>

            <?php if (count($todaySchedules) > 0): ?>
                <div class="show-grid">
                    <?php foreach ($todaySchedules as $schedule): ?>
                    <div class="show-card">
                        <h3>🎬 <?= htmlspecialchars($schedule['TENPHIM']) ?></h3>
                        <p><strong>Phòng:</strong> <?= htmlspecialchars($schedule['TENPHONG']) ?> (<?= htmlspecialchars($schedule['MAPHONG']) ?>)</p>
                        <p><strong>Thời gian:</strong> <?= date('H:i', strtotime($schedule['THOIGIANBATDAU'])) ?> - <?= date('H:i', strtotime($schedule['THOIGIANKETTHUC'])) ?></p>
                        <p><strong>Tình trạng ghế:</strong>
                            <span style="color: #28a745; font-weight: bold;"><?= $schedule['GHETRONG'] ?> trống</span> /
                            <span style="color: #dc3545; font-weight: bold;"><?= $schedule['TONGGHE'] - $schedule['GHETRONG'] ?> đã đặt</span> /
                            <span style="color: #007bff; font-weight: bold;"><?= $schedule['TONGGHE'] ?> tổng</span>
                        </p>
                        <p><strong>Giá vé:</strong> <?= number_format($schedule['GIAVE'], 0, ',', '.') ?> VNĐ</p>

                        <div class="show-actions">
                            <a href="ticket_checker.php?schedule_id=<?= $schedule['MASUAT'] ?>"
                               class="btn btn-success">🎫 Kiểm tra vé</a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="info-section">
                    <h3>📭 Không có suất chiếu nào hôm nay</h3>
                    <p>Hôm nay không có suất chiếu nào được lên lịch. Hãy liên hệ với quản lý để biết thêm thông tin.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hướng dẫn cho nhân viên -->
        <div class="info-section">
            <h3>📋 Hướng dẫn công việc:</h3>
            <ul>
                <li><strong>Kiểm tra vé:</strong> Sử dụng chức năng "Kiểm tra vé" để xác thực vé của khách hàng</li>
                <li><strong>Hỗ trợ khách hàng:</strong> Giúp khách hàng tìm ghế và giải đáp thắc mắc</li>
                <li><strong>Báo cáo sự cố:</strong> Thông báo ngay cho quản lý nếu có vấn đề về kỹ thuật</li>
                <li><strong>Giữ gìn trật tự:</strong> Đảm bảo rạp chiếu sạch sẽ và an toàn</li>
            </ul>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>