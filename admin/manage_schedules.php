<?php
require_once '../includes/auth.php';
checkRole('admin');

$schedules = getAllSchedules();
$movies = getActiveMovies();
$rooms = getAllRooms();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'MaPhim' => $_POST['MaPhim'],
        'MaPhong' => $_POST['MaPhong'],
        'ThoiGianBatDau' => $_POST['ThoiGianBatDau'],
        'GiaVe' => $_POST['GiaVe']
    ];
    
    if (isset($_POST['add_schedule'])) {
        insertSchedule($data);
    } elseif (isset($_POST['update_schedule'])) {
        $data['MaSuat'] = $_POST['MaSuat'];
        updateSchedule($data);
    } elseif (isset($_POST['delete_schedule'])) {
        deleteSchedule($_POST['MaSuat']);
    }
    
    header('Location: manage_schedules.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Suất chiếu</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/scripts.js"></script>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Quản lý Suất chiếu</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Thao tác thành công!</div>
        <?php endif; ?>
        
        <div class="admin-section">
            <h2>Thêm suất chiếu mới</h2>
            <form method="POST" class="schedule-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Phim *</label>
                        <select name="MaPhim" required>
                            <?php foreach ($movies as $movie): ?>
                                <option value="<?= $movie['MaPhim'] ?>">
                                    <?= $movie['TenPhim'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Phòng chiếu *</label>
                        <select name="MaPhong" required>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['MaPhong'] ?>">
                                    Phòng <?= $room['MaPhong'] ?> (<?= $room['SoGhe'] ?> ghế)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Thời gian bắt đầu *</label>
                        <input type="datetime-local" name="ThoiGianBatDau" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Giá vé (VNĐ) *</label>
                        <input type="number" name="GiaVe" min="0" step="1000" required>
                    </div>
                </div>
                
                <button type="submit" name="add_schedule" class="btn">Thêm suất chiếu</button>
            </form>
        </div>
        
        <div class="admin-section">
            <h2>Lịch chiếu hiện tại</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã suất</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Thời gian</th>
                        <th>Giá vé</th>
                        <th>Ghế trống</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($schedules as $schedule): ?>
                    <tr>
                        <td><?= $schedule['MaSuat'] ?></td>
                        <td><?= $schedule['TenPhim'] ?></td>
                        <td>Phòng <?= $schedule['MaPhong'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($schedule['ThoiGianBatDau'])) ?></td>
                        <td><?= number_format($schedule['GiaVe'], 0, ',', '.') ?> VNĐ</td>
                        <td><?= $schedule['GheTrong'] ?>/<?= $schedule['TongGhe'] ?></td>
                        <td>
                            <a href="#" class="btn-edit" onclick="editSchedule(<?= $schedule['MaSuat'] ?>)">Sửa</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="MaSuat" value="<?= $schedule['MaSuat'] ?>">
                                <button type="submit" name="delete_schedule" class="btn-delete" 
                                        onclick="return confirm('Xoá suất chiếu này?');">Xoá</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <script>
    function editSchedule(id) {
        // AJAX lấy thông tin suất chiếu
        // Hiển thị modal chỉnh sửa
    }
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>