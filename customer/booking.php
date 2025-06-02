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
    $selectedSeats = $_POST['seats'];
    $userId = $_SESSION['MaND'];
    
    if (bookTicket($userId, $scheduleId, $selectedSeats)) {
        header("Location: booking_success.php?schedule_id=$scheduleId");
        exit;
    } else {
        $error = "Có lỗi xảy ra khi đặt vé. Vui lòng thử lại";
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
        
        <?php if (isset($error)): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <h3>Chọn ghế</h3>
            <div class="seat-map">
                <?php foreach ($seats as $seat): ?>
                    <div class="seat">
                        <input type="checkbox" name="seats[]" value="<?= $seat['MaGhe'] ?>" 
                               id="seat-<?= $seat['MaGhe'] ?>">
                        <label for="seat-<?= $seat['MaGhe'] ?>">
                            <?= $seat['TenGhe'] ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <button type="submit" class="btn">Xác nhận đặt vé</button>
        </form>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>