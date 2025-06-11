<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/db_connect.php'; // Thêm dòng này để đảm bảo kết nối DB
checkRole('nhanvien');

// Lấy thông tin người dùng hiện tại
$currentUser = currentUser();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng điều khiển Nhân viên</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .welcome-section { 
            background: linear-gradient(135deg, #28a745, #20c997); 
            color: white; 
            padding: 25px; 
            border-radius: 15px; 
            margin: 20px 0; 
            text-align: center; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .show-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin: 20px 0; 
        }
        .show-card { 
            background: white; 
            border: 1px solid #e0e0e0; 
            border-radius: 10px; 
            padding: 20px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
            transition: all 0.3s ease; 
        }
        .show-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        .show-card h3 { 
            color: #007bff; 
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        .show-actions { 
            margin-top: 15px; 
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .btn { 
            padding: 10px 15px; 
            background: #007bff; 
            color: white; 
            text-decoration: none; 
            border-radius: 5px; 
            display: inline-block; 
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 0.9em;
        }
        .btn:hover { 
            background: #0056b3; 
            transform: translateY(-2px);
        }
        .btn-success { 
            background: #28a745; 
        }
        .btn-success:hover { 
            background: #218838; 
        }
        .info-section { 
            background: #f8f9fa; 
            padding: 20px; 
            border-radius: 10px; 
            margin: 20px 0;
            border-left: 4px solid #17a2b8;
        }
        .quick-stats { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 15px; 
            margin: 20px 0; 
        }
        .stat-card { 
            background: linear-gradient(135deg, #17a2b8, #138496); 
            color: white; 
            padding: 20px; 
            border-radius: 10px; 
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .stat-number { 
            font-size: 2em; 
            font-weight: bold; 
            margin-bottom: 5px;
        }
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            color: #6c757d;
        }
        .empty-state-icon {
            font-size: 3em;
            margin-bottom: 15px;
            color: #adb5bd;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container">
        <div class="welcome-section">
            <h1>🎫 Chào mừng <?= htmlspecialchars($currentUser['HOTEN'] ?? 'Nhân viên') ?>!</h1>
            <p>Hệ thống quản lý rạp chiếu phim - Phiên nhân viên</p>
        </div>

        <!-- Thống kê nhanh -->
        <div class="quick-stats">
            <?php
            $todaySchedules = getSchedules();
            $totalShows = count($todaySchedules);
            $totalSeats = array_sum(array_column($todaySchedules, 'TONGGHE'));
            $availableSeats = array_sum(array_column($todaySchedules, 'GHETRONG'));
            $soldTickets = $totalSeats - $availableSeats;
            ?>
            <div class="stat-card">
                <div class="stat-number"><?= $totalShows ?></div>
                <div>Suất chiếu hôm nay</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $soldTickets ?></div>
                <div>Vé đã bán</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $availableSeats ?></div>
                <div>Ghế trống</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= round(($soldTickets/$totalSeats)*100, 1) ?>%</div>
                <div>Tỉ lệ lấp đầy</div>
            </div>
        </div>

        <div class="today-shows">
            <h2>📅 Lịch chiếu hôm nay (<?= date('d/m/Y') ?>)</h2>

            <?php if (!empty($todaySchedules)): ?>
                <div class="show-grid">
                    <?php foreach ($todaySchedules as $schedule): 
                        $startTime = strtotime($schedule['THOIGIANBATDAU']);
                        $endTime = strtotime($schedule['THOIGIANKETTHUC']);
                        $isNowShowing = (time() >= $startTime && time() <= $endTime);
                    ?>
                        <div class="show-card <?= $isNowShowing ? 'now-showing' : '' ?>">
                            <h3>🎬 <?= htmlspecialchars($schedule['TENPHIM']) ?></h3>
                            <p><strong>Phòng:</strong> <?= htmlspecialchars($schedule['TENPHONG']) ?></p>
                            <p><strong>Thời gian:</strong> 
                                <?= date('H:i', $startTime) ?> - <?= date('H:i', $endTime) ?>
                                <?= $isNowShowing ? '<span style="color:#dc3545;font-weight:bold;"> (Đang chiếu)</span>' : '' ?>
                            </p>
                            <div class="progress-container" style="margin: 10px 0;">
                                <div class="progress" style="height: 10px; background: #e9ecef; border-radius: 5px;">
                                    <div class="progress-bar" 
                                         style="height: 100%; width: <?= ($schedule['TONGGHE'] - $schedule['GHETRONG']) / $schedule['TONGGHE'] * 100 ?>%; 
                                         background: linear-gradient(90deg, #28a745, #20c997); 
                                         border-radius: 5px;">
                                    </div>
                                </div>
                                <small style="display: flex; justify-content: space-between; margin-top: 5px;">
                                    <span><?= $schedule['GHETRONG'] ?> ghế trống</span>
                                    <span><?= $schedule['TONGGHE'] - $schedule['GHETRONG'] ?> đã đặt</span>
                                </small>
                            </div>
                            <p><strong>Giá vé:</strong> <?= number_format($schedule['GIAVE']) ?>₫</p>

                            <div class="show-actions">
                                <a href="ticket_checker.php?schedule_id=<?= htmlspecialchars($schedule['MASUAT']) ?>" 
                                   class="btn btn-success">
                                   <span class="icon">🎫</span> Kiểm tra vé
                                </a>
                                <a href="seat_map.php?schedule_id=<?= htmlspecialchars($schedule['MASUAT']) ?>" 
                                   class="btn">
                                   <span class="icon">🗺️</span> Sơ đồ ghế
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📭</div>
                    <h3>Không có suất chiếu nào hôm nay</h3>
                    <p>Hiện không có lịch chiếu nào được lên lịch cho hôm nay.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hướng dẫn cho nhân viên -->
        <div class="info-section">
            <h3>📋 Hướng dẫn công việc</h3>
            <div class="guide-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 15px;">
                <div class="guide-item">
                    <h4>🎫 Kiểm tra vé</h4>
                    <p>Xác thực vé khách hàng bằng mã QR hoặc mã vé trước khi vào rạp</p>
                </div>
                <div class="guide-item">
                    <h4>🛎️ Hỗ trợ khách</h4>
                    <p>Hướng dẫn khách đến đúng phòng chiếu và vị trí ghế ngồi</p>
                </div>
                <div class="guide-item">
                    <h4>🔄 Báo cáo sự cố</h4>
                    <p>Thông báo ngay cho quản lý khi có vấn đề về kỹ thuật hoặc an ninh</p>
                </div>
                <div class="guide-item">
                    <h4>🧹 Vệ sinh rạp</h4>
                    <p>Đảm bảo khu vực được phân công luôn sạch sẽ và ngăn nắp</p>
                </div>
            </div>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>

    <script>
    // Cập nhật thời gian thực
    function updateCurrentTime() {
        const nowShowingCards = document.querySelectorAll('.show-card');
        const now = new Date();
        
        nowShowingCards.forEach(card => {
            const timeText = card.querySelector('strong:contains("Thời gian")')?.parentNode?.textContent;
            if (timeText) {
                // Logic kiểm tra xem suất chiếu có đang diễn ra không
                // (Có thể thêm bằng JavaScript nếu cần)
            }
        });
    }
    
    // Cập nhật mỗi phút
    setInterval(updateCurrentTime, 60000);
    </script>
</body>
</html>