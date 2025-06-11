<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/db_connect.php';
checkRole('nhanvien');

// Xử lý dữ liệu đầu vào
$scheduleId = filter_input(INPUT_GET, 'schedule_id', FILTER_SANITIZE_STRING);
$seats = [];
$schedule = null;
$error = null;

if ($scheduleId) {
    try {
        $schedule = getScheduleDetails($scheduleId);
        if (!$schedule) {
            $error = "Suất chiếu không tồn tại";
        } else {
            $seats = getSeatsForSchedule($scheduleId);
        }
    } catch (Exception $e) {
        $error = "Lỗi khi tải dữ liệu: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_seats'])) {
    if (!$scheduleId) {
        $error = "Thiếu thông tin suất chiếu";
    } else {
        try {
            foreach ($_POST['seats'] as $seatId => $status) {
                updateSeatStatus($seatId, $status, $scheduleId);
            }
            $_SESSION['success'] = "Cập nhật trạng thái ghế thành công!";
            header("Location: seat_adjust.php?schedule_id=$scheduleId");
            exit;
        } catch (Exception $e) {
            $error = "Lỗi khi cập nhật: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Điều chỉnh ghế - <?= htmlspecialchars($schedule['TENPHIM'] ?? '') ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .seat-map-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .screen-display {
            background: #333;
            color: white;
            text-align: center;
            padding: 10px;
            margin: 0 0 30px;
            font-weight: bold;
            border-radius: 5px;
        }
        
        .seat-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 12px;
            margin: 30px 0;
        }
        
        .seat-item {
            position: relative;
            padding: 12px 5px;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: bold;
        }
        
        .seat-item.available { background: #e8f5e9; color: #2e7d32; }
        .seat-item.occupied { background: #ffebee; color: #c62828; }
        .seat-item.vip { background: #fff8e1; color: #f57f17; }
        .seat-item.broken { background: #efebe9; color: #6d4c41; text-decoration: line-through; }
        .seat-item.selected { box-shadow: 0 0 0 3px #2196F3; }
        
        .seat-item input[type="radio"] {
            position: absolute;
            opacity: 0;
        }
        
        .status-legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        
        .legend-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .color-box {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 3px;
        }
        
        .color-box.available { background: #e8f5e9; border: 1px solid #2e7d32; }
        .color-box.occupied { background: #ffebee; border: 1px solid #c62828; }
        .color-box.vip { background: #fff8e1; border: 1px solid #f57f17; }
        .color-box.broken { background: #efebe9; border: 1px solid #6d4c41; }
        
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 10px 20px;
            background: #2196F3;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #0d8bf2;
        }
        
        .btn-back {
            background: #757575;
            margin-right: 10px;
        }
        
        .btn-back:hover {
            background: #616161;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>🪑 Điều chỉnh trạng thái ghế</h1>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert success"><?= $_SESSION['success'] ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (!$scheduleId): ?>
            <div class="alert warning">Vui lòng chọn suất chiếu từ trang trước</div>
            <div class="form-actions">
                <a href="dashboard.php" class="btn btn-back">← Quay lại</a>
            </div>
        <?php elseif (!$schedule): ?>
            <div class="alert error">Không tìm thấy thông tin suất chiếu</div>
            <div class="form-actions">
                <a href="dashboard.php" class="btn btn-back">← Quay lại</a>
            </div>
        <?php else: ?>
            <div class="schedule-info">
                <h2>🎬 <?= htmlspecialchars($schedule['TENPHIM']) ?></h2>
                <p><strong>Phòng:</strong> <?= htmlspecialchars($schedule['TENPHONG']) ?> | 
                   <strong>Thời gian:</strong> <?= date('d/m/Y H:i', strtotime($schedule['THOIGIANBATDAU'])) ?></p>
            </div>
            
            <div class="screen-display">MÀN HÌNH</div>
            
            <form method="POST" class="seat-status-form">
                <div class="seat-grid">
                    <?php foreach ($seats as $seat): 
                        $status = $seat['TRANGTHAI'] ?? 'available';
                        $isVip = ($seat['LOAIGHE'] ?? '') === 'vip';
                    ?>
                        <div class="seat-item <?= $status ?> <?= $isVip ? 'vip' : '' ?>"
                             data-seat-id="<?= htmlspecialchars($seat['MAGHE']) ?>">
                            <input type="radio" name="seats[<?= htmlspecialchars($seat['MAGHE']) ?>]" 
                                   id="seat-<?= htmlspecialchars($seat['MAGHE']) ?>-available" 
                                   value="available" <?= $status === 'available' ? 'checked' : '' ?>>
                            <input type="radio" name="seats[<?= htmlspecialchars($seat['MAGHE']) ?>]" 
                                   id="seat-<?= htmlspecialchars($seat['MAGHE']) ?>-occupied" 
                                   value="occupied" <?= $status === 'occupied' ? 'checked' : '' ?>>
                            <input type="radio" name="seats[<?= htmlspecialchars($seat['MAGHE']) ?>]" 
                                   id="seat-<?= htmlspecialchars($seat['MAGHE']) ?>-broken" 
                                   value="broken" <?= $status === 'broken' ? 'checked' : '' ?>>
                            
                            <label for="seat-<?= htmlspecialchars($seat['MAGHE']) ?>-available">
                                <?= htmlspecialchars($seat['SOGHE']) ?>
                                <?= $isVip ? '⭐' : '' ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="status-legend">
                    <div class="legend-item">
                        <span class="color-box available"></span>
                        <span>Trống</span>
                    </div>
                    <div class="legend-item">
                        <span class="color-box occupied"></span>
                        <span>Đã bán</span>
                    </div>
                    <div class="legend-item">
                        <span class="color-box vip"></span>
                        <span>VIP</span>
                    </div>
                    <div class="legend-item">
                        <span class="color-box broken"></span>
                        <span>Hỏng</span>
                    </div>
                </div>
                
                <div class="form-actions">
                    <a href="dashboard.php" class="btn btn-back">← Quay lại</a>
                    <button type="submit" name="update_seats" class="btn">💾 Lưu thay đổi</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý click chọn ghế
        document.querySelectorAll('.seat-item').forEach(seat => {
            seat.addEventListener('click', function(e) {
                if (e.target.tagName === 'INPUT') return;
                
                const radios = this.querySelectorAll('input[type="radio"]');
                const currentChecked = Array.from(radios).findIndex(r => r.checked);
                const nextIndex = (currentChecked + 1) % radios.length;
                
                radios[nextIndex].checked = true;
                
                // Cập nhật giao diện
                this.className = 'seat-item ' + radios[nextIndex].value;
                if (this.classList.contains('vip')) {
                    this.classList.add('vip');
                }
            });
        });
        
        // Hiển thị thông tin khi hover ghế
        document.querySelectorAll('.seat-item').forEach(seat => {
            seat.addEventListener('mouseenter', function() {
                const seatId = this.getAttribute('data-seat-id');
                // Có thể thêm tooltip hiển thị thông tin chi tiết
            });
        });
    });
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>