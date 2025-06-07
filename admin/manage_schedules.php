<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

$schedules = getAllSchedules();
$movies = getAllMoviesAdmin(); // Lấy tất cả phim
$rooms = getRooms();           // Lấy danh sách phòng

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>🕒 Quản lý Suất chiếu</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Thao tác thành công!</div>
    <?php endif; ?>

    <!-- Thêm suất chiếu -->
    <div class="admin-section">
        <h2>Thêm suất chiếu mới</h2>
        <form method="POST" class="user-form">
            <div class="form-row">
                <input type="text" name="MaSuat" placeholder="Mã suất (VD: SC01)" required>
                <select name="MaPhim" required>
                    <option value="">-- Chọn phim --</option>
                    <?php foreach ($movies as $phim): ?>
                        <option value="<?= $phim['MAPHIM'] ?>"><?= $phim['TENPHIM'] ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="MaPhong" required>
                    <option value="">-- Chọn phòng chiếu --</option>
                    <?php foreach ($rooms as $room): ?>
                        <option value="<?= $room['MAPHONG'] ?>"><?= $room['TENPHONG'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-row">
                <input type="datetime-local" name="ThoiGianBatDau" required>
                <input type="datetime-local" name="ThoiGianKetThuc" required>
                <input type="number" name="GiaVe" placeholder="Giá vé" required>
            </div>
            <button type="submit" name="add_schedule" class="btn">Thêm suất chiếu</button>
        </form>
    </div>

    <!-- Danh sách suất chiếu -->
    <div class="admin-section">
        <h2>Danh sách suất chiếu</h2>
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
                <?php foreach ($schedules as $sc): ?>
                    <tr>
                        <td><?= $sc['MASUAT'] ?></td>
                        <td><?= $sc['TENPHIM'] ?></td>
                        <td><?= $sc['TENPHONG'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($sc['THOIGIANBATDAU'])) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($sc['THOIGIANKETTHUC'])) ?></td>
                        <td><?= number_format($sc['GIAVE']) ?>₫</td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Xoá suất này?');" style="display:inline;">
                                <input type="hidden" name="MaSuat" value="<?= $sc['MASUAT'] ?>">
                                <button type="submit" name="delete_schedule" class="btn-delete">Xoá</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
