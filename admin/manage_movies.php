<?php
// File: /admin/manage_movies.php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

$movies = getMovies(); // Hàm lấy danh sách phim từ Oracle

include_once '../includes/header.php';

// Xử lý thêm/sửa/xóa phim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_movie'])) {
        $data = [
            'ten_phim' => $_POST['ten_phim'],
            'the_loai' => $_POST['the_loai'],
            'thoi_luong' => $_POST['thoi_luong'],
            'mo_ta' => $_POST['mo_ta']
        ];
        insertMovie($data);
    } 
    elseif (isset($_POST['update_movie'])) {
        $data = [
            'ma_phim' => $_POST['ma_phim'],
            'ten_phim' => $_POST['ten_phim'],
            'the_loai' => $_POST['the_loai'],
            'thoi_luong' => $_POST['thoi_luong'],
            'mo_ta' => $_POST['mo_ta']
        ];
        updateMovie($data);
    }
    elseif (isset($_GET['delete'])) {
        deleteMovie($_GET['delete']);
    }
    header("Location: manage_movies.php");
}
?>

<div class="container">
    <h2>Quản lý Phim</h2>
    
    <!-- Form thêm phim -->
    <div class="form-section">
        <h3>Thêm phim mới</h3>
        <form method="POST">
            <input type="text" name="ten_phim" placeholder="Tên phim" required>
            <input type="text" name="the_loai" placeholder="Thể loại" required>
            <input type="number" name="thoi_luong" placeholder="Thời lượng (phút)" required>
            <textarea name="mo_ta" placeholder="Mô tả phim"></textarea>
            <button type="submit" name="add_movie">Thêm phim</button>
        </form>
    </div>
    
    <!-- Danh sách phim -->
    <table class="data-table">
        <thead>
            <tr>
                <th>Mã phim</th>
                <th>Tên phim</th>
                <th>Thể loại</th>
                <th>Thời lượng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movies as $movie): ?>
            <tr>
                <td><?= $movie['MA_PHIM'] ?></td>
                <td><?= $movie['TEN_PHIM'] ?></td>
                <td><?= $movie['THE_LOAI'] ?></td>
                <td><?= $movie['THOI_LUONG'] ?> phút</td>
                <td class="actions">
                    <a href="edit_movie.php?id=<?= $movie['MA_PHIM'] ?>" class="btn-edit">Sửa</a>
                    <a href="?delete=<?= $movie['MA_PHIM'] ?>" class="btn-delete">Xóa</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include_once '../includes/footer.php'; ?>