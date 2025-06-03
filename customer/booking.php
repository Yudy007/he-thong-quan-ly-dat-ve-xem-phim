<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('khachhang');

if (!isset($_GET['schedule_id'])) {
    header('Location: home.php');
    exit;
}

$scheduleId = $_GET['schedule_id'];
$schedule = getScheduleDetails($scheduleId);
$seats = getAvailableSeats($scheduleId);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (empty($_POST['seats'])) {
        $error = "Vui lòng chọn ít nhất một ghế";
    } else {
        $selectedSeats = $_POST['seats'];
        $userId = $_SESSION['MaND'];
        
        if (bookTicket($userId, $scheduleId, $selectedSeats)) {
            header("Location: booking_success.php?schedule_id=$scheduleId");
            exit;
        } else {
            $error = "Có lỗi xảy ra khi đặt vé. Vui lòng thử lại";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đặt vé xem phim</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/scripts.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h2>Đặt vé: <?= $schedule['TenPhim'] ?></h2>
        <p>Suất chiếu: <?= date('d/m/Y H:i', strtotime($schedule['ThoiGianBatDau'])) ?></p>
        <p>Phòng: <?= $schedule['MaPhong'] ?></p>
        <p class="price-info">Giá vé: <?= number_format($schedule['GiaVe'], 0, ',', '.') ?> VNĐ/ghế</p>
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" id="booking-form">
            <h3>Chọn ghế (Tối đa 6 ghế)</h3>
            
            <?php if (empty($seats)): ?>
                <div class="alert warning">Suất chiếu này đã hết ghế trống</div>
            <?php else: ?>
                <div class="seat-map">
                    <?php foreach ($seats as $seat): ?>
                        <div class="seat">
                            <input type="checkbox" name="seats[]" value="<?= $seat['MaGhe'] ?>" 
                                   id="seat-<?= $seat['MaGhe'] ?>" class="seat-checkbox">
                            <label for="seat-<?= $seat['MaGhe'] ?>" class="seat-label">
                                <?= $seat['TenGhe'] ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="booking-summary">
                    <p>Đã chọn: <span id="selected-count">0</span> ghế</p>
                    <p>Tổng tiền: <span id="total-price">0</span> VNĐ</p>
                </div>
                
                <button type="submit" class="btn">Xác nhận đặt vé</button>
            <?php endif; ?>
        </form>
    </div>
    
    <?php include '../includes/footer.php'; ?>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const seatPrice = <?= $schedule['GiaVe'] ?>;
        const checkboxes = document.querySelectorAll('.seat-checkbox');
        const selectedCount = document.getElementById('selected-count');
        const totalPrice = document.getElementById('total-price');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const selected = document.querySelectorAll('.seat-checkbox:checked');
                selectedCount.textContent = selected.length;
                totalPrice.textContent = (selected.length * seatPrice).toLocaleString();
                
                if (selected.length > 6) {
                    alert('Bạn chỉ có thể chọn tối đa 6 ghế');
                    this.checked = false;
                    selectedCount.textContent = 6;
                    totalPrice.textContent = (6 * seatPrice).toLocaleString();
                }
            });
        });
        
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            const selected = document.querySelectorAll('.seat-checkbox:checked');
            if (selected.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một ghế');
            }
        });
    });
    </script>
</body>
</html>