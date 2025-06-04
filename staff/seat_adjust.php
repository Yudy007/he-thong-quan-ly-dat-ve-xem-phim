<?php
require_once '../includes/auth.php';
checkRole('nhanvien');

$scheduleId = $_GET['schedule_id'] ?? null;
$seats = [];
$schedule = null;

if ($scheduleId) {
    $schedule = getScheduleDetails($scheduleId);
    $seats = getSeatsForSchedule($scheduleId);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_seats'])) {
    foreach ($_POST['seats'] as $seatId => $status) {
        updateSeatStatus($seatId, $status);
    }
    header("Location: seat_adjust.php?schedule_id=$scheduleId&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Điều chỉnh ghế</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
    .seat-status-form .seat-grid {
        display: grid;
        grid-template-columns: repeat(10, 1fr);
        gap: 10px;
        margin: 20px 0;
    }
    
    .seat-item {
        padding: 10px;
        border-radius: 4px;
        text-align: center;
        cursor: pointer;
    }
    
    .seat-item.available { background: #e0e0e0; }
    .seat-item.occupied { background: #f44336; color: white; }
    .seat-item.vip { background: #ffc107; }
    .seat-item.broken { background: #9e9e9e; color: white; }
    
    .seat-item input[type="radio"] {
        display: none;
    }
    
    .seat-item input[type="radio"]:checked + label {
        outline: 3px solid #2196f3;
    }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Điều chỉnh trạng thái ghế</h1>
        
        <?php if (!$scheduleId): ?>
            <div class="alert error">Vui lòng chọn suất chiếu</div>
            <a href="dashboard.php" class="btn">Quay lại bảng điều khiển</a>
        <?php else: ?>
            <?php if (isset($_GET['success'])): ?>
                <div class="alert success">Cập nhật trạng thái ghế thành công!</div>
            <?php endif; ?>
            
            <div class="schedule-info">
                <h2><?= $schedule['TenPhim'] ?></h2>
                <p>Phòng: <?= $schedule['MaPhong'] ?> | 
                   Thời gian: <?= date('d/m/Y H:i', strtotime($schedule['ThoiGianBatDau'])) ?></p>
            </div>
            
            <form method="POST" class="seat-status-form">
                <div class="seat-grid">
                    <?php foreach ($seats as $seat): ?>
                    <div class="seat-item <?= $seat['TrangThai'] ?>">
                        <input type="radio" name="seats[<?= $seat['MaGhe'] ?>]" 
                               id="seat-<?= $seat['MaGhe'] ?>-available" 
                               value="available" 
                               <?= $seat['TrangThai'] == 'available' ? 'checked' : '' ?>>
                        <label for="seat-<?= $seat['MaGhe'] ?>-available"><?= $seat['TenGhe'] ?></label>
                        
                        <input type="radio" name="seats[<?= $seat['MaGhe'] ?>]" 
                               id="seat-<?= $seat['MaGhe'] ?>-occupied" 
                               value="occupied" 
                               <?= $seat['TrangThai'] == 'occupied' ? 'checked' : '' ?>>
                        
                        <input type="radio" name="seats[<?= $seat['MaGhe'] ?>]" 
                               id="seat-<?= $seat['MaGhe'] ?>-broken" 
                               value="broken" 
                               <?= $seat['TrangThai'] == 'broken' ? 'checked' : '' ?>>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="status-legend">
                    <div><span class="color-box available"></span> Trống</div>
                    <div><span class="color-box occupied"></span> Đã bán</div>
                    <div><span class="color-box broken"></span> Hỏng</div>
                    <div><span class="color-box vip"></span> VIP</div>
                </div>
                
                <input type="hidden" name="schedule_id" value="<?= $scheduleId ?>">
                <button type="submit" name="update_seats" class="btn">Cập nhật trạng thái</button>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
    // Chuyển đổi trạng thái ghế bằng cách click
    document.querySelectorAll('.seat-item').forEach(seat => {
        seat.addEventListener('click', function(e) {
            const radios = this.querySelectorAll('input[type="radio"]');
            let currentIndex = -1;
            
            // Tìm radio hiện đang chọn
            radios.forEach((radio, index) => {
                if (radio.checked) currentIndex = index;
            });
            
            // Chuyển sang trạng thái tiếp theo
            const nextIndex = (currentIndex + 1) % radios.length;
            radios[nextIndex].checked = true;
            
            // Cập nhật class trạng thái
            this.className = 'seat-item ' + radios[nextIndex].value;
        });
    });
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>