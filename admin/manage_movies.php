<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

$conn = connectOracle();
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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container">
    <h1>🎬 Quản lý Phim</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Thao tác thành công!</div>
    <?php endif; ?>

    <!-- Thêm phim -->
    <div class="admin-section">
        <h2>Thêm phim mới</h2>
        <form method="POST" class="user-form">
            <div class="form-row">
                <input type="text" name="MaPhim" placeholder="Mã phim (VD: P01)" required>
                <input type="text" name="TenPhim" placeholder="Tên phim" required>
                <input type="text" name="TheLoai" placeholder="Thể loại">
                <input type="number" name="ThoiLuong" placeholder="Thời lượng (phút)" required>
            </div>
            <div class="form-row">
                <textarea name="MoTa" rows="3" placeholder="Mô tả phim" style="width:100%"></textarea>
                <select name="TrangThai" required>
                    <option value="dang_chieu">Đang chiếu</option>
                    <option value="sap_chieu">Sắp chiếu</option>
                    <option value="ngung_chieu">Ngừng chiếu</option>
                </select>
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
                        <td><?= $movie['THOILUONG'] ?> phút</td>
                        <td><?= $movie['TRANGTHAI'] ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Xoá phim này?');" style="display:inline;">
                                <input type="hidden" name="MaPhim" value="<?= $movie['MAPHIM'] ?>">
                                <button type="submit" name="delete_movie" class="btn-delete">Xoá</button>
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
