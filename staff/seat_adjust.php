<?php
// File: /staff/seat_adjust.php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('staff');

$schedules = getTodaySchedules(); // Hàm lấy lịch chiếu trong ngày
$schedule_id = $_GET['schedule_id'] ?? null;
$seats = $schedule_id ? getSeatStatus($schedule_id) : [];

// Xử lý cập nhật trạng thái ghế
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_seat'])) {
    updateSeatStatus(
        $_POST['seat_id'],
        $_POST['new_status']
    );
    header("Location: ?schedule_id=$schedule_id");
}

include_once '../includes/header.php';
?>

<div class="container">
    <h2>Quản lý Ghế</h2>
    
    <div class="schedule-selector">
        <label>Chọn suất chiếu:</label>
        <select id="scheduleSelect">
            <option value="">-- Chọn suất --</option>
            <?php foreach ($schedules as $schedule): ?>
            <option value="<?= $schedule['MA_SUAT'] ?>" 
                <?= $schedule_id == $schedule['MA_SUAT'] ? 'selected' : '' ?>>
                <?= $schedule['TEN_PHIM'] ?> - <?= $schedule['GIO_CHIEU'] ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($schedule_id && !empty($seats)): ?>
    <div class="seat-map">
        <h3>Sơ đồ ghế - Phòng <?= $seats[0]['PHONG'] ?></h3>
        
        <div class="screen">MÀN HÌNH</div>
        
        <div class="seats-grid">
            <?php foreach ($seats as $seat): ?>
            <div class="seat <?= $seat['TRANG_THAI'] ?>"
                 data-seat-id="<?= $seat['MA_GHE'] ?>"
                 data-status="<?= $seat['TRANG_THAI'] ?>">
                <?= $seat['SO_GHE'] ?>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="seat-legend">
            <span><div class="legend available"></div> Trống</span>
            <span><div class="legend booked"></div> Đã đặt</span>
            <span><div class="legend broken"></div> Hỏng</span>
            <span><div class="legend selected"></div> Đang chọn</span>
        </div>
    </div>
    
    <form id="seatUpdateForm" method="POST" style="display:none">
        <input type="hidden" name="seat_id" id="inputSeatId">
        <input type="hidden" name="new_status" id="inputNewStatus">
        <button type="submit" name="update_seat">Cập nhật</button>
    </form>
    <?php endif; ?>
</div>

<script>
// Chọn suất chiếu
document.getElementById('scheduleSelect').addEventListener('change', function() {
    if (this.value) {
        window.location = 'seat_adjust.php?schedule_id=' + this.value;
    }
});

// Xử lý chọn ghế
document.querySelectorAll('.seat').forEach(seat => {
    seat.addEventListener('click', function() {
        const currentStatus = this.dataset.status;
        const seatId = this.dataset.seatId;
        
        // Hiển thị modal cập nhật
        const newStatus = prompt(`Cập nhật trạng thái ghế ${this.textContent}:\n\n1. Trống\n2. Đã đặt\n3. Hỏng`, currentStatus);
        
        if (newStatus && newStatus !== currentStatus) {
            document.getElementById('inputSeatId').value = seatId;
            document.getElementById('inputNewStatus').value = newStatus;
            document.getElementById('seatUpdateForm').submit();
        }
    });
});
</script>

<?php include_once '../includes/footer.php'; ?>