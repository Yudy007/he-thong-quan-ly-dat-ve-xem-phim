<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/db_connect.php';
checkRole('admin');
$base_url = '/he-thong-quan-ly-dat-ve-xem-phim';
$schedules = getSchedules();
$movies = getAllMovies();
$rooms = getRooms();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_schedule'])) {
            $data = [
                'MaSuat' => $_POST['MaSuat'],
                'MaPhim' => $_POST['MaPhim'],
                'MaPhong' => $_POST['MaPhong'],
                'ThoiGianBatDau' => $_POST['ThoiGianBatDau'],
                'ThoiGianKetThuc' => $_POST['ThoiGianKetThuc'],
                'GiaVe' => $_POST['GiaVe']
            ];
            insertSchedule($data);
        } elseif (isset($_POST['delete_schedule'])) {
            deleteSchedule($_POST['MaSuat']);
        }

        $_SESSION['success'] = 'Thao tác thành công!';
        header('Location: manage_schedules.php');
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        header('Location: manage_schedules.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Suất chiếu</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>🕒 Quản lý Suất chiếu</h1>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert error"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Form thêm suất chiếu -->
    <div class="admin-section">
        <h2>Thêm suất chiếu mới</h2>
        <form method="POST" id="scheduleForm">
            <div class="form-grid">
                <div class="form-group">
                    <label>Mã suất</label>
                    <input type="text" name="MaSuat" pattern="SC\d{3}" title="VD: SC001" required>
                </div>
                <div class="form-group">
                    <label>Phim</label>
                    <select name="MaPhim" required>
                        <option value="">-- Chọn phim --</option>
                        <?php foreach ($movies as $phim): ?>
                            <option value="<?= htmlspecialchars($phim['MAPHIM']) ?>">
                                <?= htmlspecialchars($phim['TENPHIM']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Phòng chiếu</label>
                    <select name="MaPhong" required>
                        <option value="">-- Chọn phòng --</option>
                        <?php foreach ($rooms as $room): ?>
                            <option value="<?= htmlspecialchars($room['MAPHONG']) ?>">
                                <?= htmlspecialchars($room['TENPHONG']) ?> (<?= $room['SOLUONGGHE'] ?> ghế)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Thời gian bắt đầu</label>
                    <input type="datetime-local" name="ThoiGianBatDau" required>
                </div>
                <div class="form-group">
                    <label>Thời gian kết thúc</label>
                    <input type="datetime-local" name="ThoiGianKetThuc" required>
                </div>
                <div class="form-group">
                    <label>Giá vé (VNĐ)</label>
                    <input type="number" name="GiaVe" min="50000" step="10000" required>
                </div>
            </div>
            <button type="submit" name="add_schedule" class="btn">Thêm suất chiếu</button>
        </form>
    </div>

    <!-- Danh sách suất chiếu -->
    <div class="admin-section">
        <h2>Danh sách suất chiếu</h2>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Bắt đầu</th>
                        <th>Kết thúc</th>
                        <th>Giá vé</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $sc): 
                        $now = time();
                        $startTime = strtotime($sc['THOIGIANBATDAU']);
                        $isPast = ($now > $startTime);
                    ?>
                        <tr class="<?= $isPast ? 'inactive' : '' ?>">
                            <td><?= htmlspecialchars($sc['MASUAT']) ?></td>
                            <td><?= htmlspecialchars($sc['TENPHIM']) ?></td>
                            <td><?= htmlspecialchars($sc['TENPHONG']) ?></td>
                            <td class="time-cell">
                                <?= date('d/m/Y H:i', $startTime) ?>
                                <?= ($isPast) ? '<span class="conflict-warning"> (Đã qua)</span>' : '' ?>
                            </td>
                            <td class="time-cell"><?= date('d/m/Y H:i', strtotime($sc['THOIGIANKETTHUC'])) ?></td>
                            <td><?= number_format($sc['GIAVE']) ?>₫</td>
                            <td>
                                <?php if (!$isPast): ?>
                                    <form method="POST" onsubmit="return confirm('Xoá suất chiếu này?');">
                                        <input type="hidden" name="MaSuat" value="<?= htmlspecialchars($sc['MASUAT']) ?>">
                                        <button type="submit" name="delete_schedule" class="btn-delete">Xoá</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn-disabled" disabled>Đã qua</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script>
// Validate thời gian
document.getElementById('scheduleForm').addEventListener('submit', function(e) {
    const startInput = document.getElementsByName('ThoiGianBatDau')[0];
    const endInput = document.getElementsByName('ThoiGianKetThuc')[0];
    
    const start = new Date(startInput.value);
    const end = new Date(endInput.value);
    
    if (start >= end) {
        alert('Thời gian kết thúc phải sau thời gian bắt đầu!');
        e.preventDefault();
        return false;
    }
    
    // Kiểm tra thời gian trong quá khứ
    const now = new Date();
    if (start < now) {
        alert('Không thể tạo suất chiếu trong quá khứ!');
        e.preventDefault();
        return false;
    }
    
    return true;
});
</script>
</body>
</html>