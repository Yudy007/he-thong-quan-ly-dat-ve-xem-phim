<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/db_connect.php'; // Thêm dòng này
checkRole('admin');
$base_url = '/he-thong-quan-ly-dat-ve-xem-phim';
$movies = getAllMovies();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'MaPhim' => $_POST['MaPhim'],
        'TenPhim' => $_POST['TenPhim'],
        'TheLoai' => $_POST['TheLoai'],
        'ThoiLuong' => $_POST['ThoiLuong'],
        'MoTa' => $_POST['MoTa'],
        'TrangThai' => $_POST['TrangThai']
    ];

    if (isset($_POST['add_movie'])) {
        insertMovie($data);
    } elseif (isset($_POST['delete_movie'])) {
        deleteMovie($_POST['MaPhim']);
    } elseif (isset($_POST['update_movie'])) {
        updateMovie($data);
    }

    header('Location: manage_movies.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phim</title>
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>🎬 Quản lý Phim</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Thao tác thành công!</div>
    <?php endif; ?>

    <!-- Form thêm phim -->
    <div class="admin-section">
        <h2>Thêm phim mới</h2>
        <form method="POST" class="movie-form">
            <div class="form-grid">
                <div class="form-group">
                    <label>Mã phim</label>
                    <input type="text" name="MaPhim" placeholder="VD: P001" required 
                           pattern="[A-Za-z0-9]+" title="Chỉ chấp nhận chữ và số">
                </div>
                <div class="form-group">
                    <label>Tên phim</label>
                    <input type="text" name="TenPhim" required>
                </div>
                <div class="form-group">
                    <label>Thể loại</label>
                    <input type="text" name="TheLoai" required>
                </div>
                <div class="form-group">
                    <label>Thời lượng (phút)</label>
                    <input type="number" name="ThoiLuong" min="1" required>
                </div>
                <div class="form-group span-2">
                    <label>Mô tả</label>
                    <textarea name="MoTa" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Trạng thái</label>
                    <select name="TrangThai" required>
                        <option value="dang_chieu">Đang chiếu</option>
                        <option value="sap_chieu">Sắp chiếu</option>
                        <option value="ngung_chieu">Ngừng chiếu</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="add_movie" class="btn">Thêm phim</button>
        </form>
    </div>

    <!-- Danh sách phim -->
    <div class="admin-section">
        <h2>Danh sách phim</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Tên phim</th>
                    <th>Thể loại</th>
                    <th>Thời lượng</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($movies as $movie): ?>
                    <tr>
                        <td><?= htmlspecialchars($movie['MAPHIM']) ?></td>
                        <td><?= htmlspecialchars($movie['TENPHIM']) ?></td>
                        <td><?= htmlspecialchars($movie['THELOAI']) ?></td>
                        <td><?= (int)$movie['THOILUONG'] ?> phút</td>
                        <td class="status-<?= $movie['TRANGTHAI'] ?>">
                            <?= match($movie['TRANGTHAI']) {
                                'dang_chieu' => 'Đang chiếu',
                                'sap_chieu' => 'Sắp chiếu',
                                'ngung_chieu' => 'Ngừng chiếu',
                                default => $movie['TRANGTHAI']
                            } ?>
                        </td>
                        <td class="action-buttons">
                            <form method="POST" onsubmit="return confirm('Xóa phim này?');">
                                <input type="hidden" name="MaPhim" value="<?= $movie['MAPHIM'] ?>">
                                <button type="submit" name="delete_movie" class="btn-delete">Xóa</button>
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