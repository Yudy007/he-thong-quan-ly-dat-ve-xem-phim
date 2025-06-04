<?php
require_once '../includes/auth.php';
checkRole('admin');

$movies = getAllMovies();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_movie'])) {
        $data = [
            'TenPhim' => $_POST['TenPhim'],
            'TheLoai' => $_POST['TheLoai'],
            'ThoiLuong' => $_POST['ThoiLuong'],
            'MoTa' => $_POST['MoTa'],
            'TrangThai' => $_POST['TrangThai']
        ];
        
        if ($_FILES['HinhAnh']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['HinhAnh']['name'], PATHINFO_EXTENSION);
            $filename = 'movie-' . time() . '.' . $ext;
            move_uploaded_file($_FILES['HinhAnh']['tmp_name'], "../assets/images/posters/$filename");
            $data['HinhAnh'] = $filename;
        }
        
        insertMovie($data);
        header('Location: manage_movies.php?success=1');
        exit;
    }
    
    if (isset($_POST['delete_movie'])) {
        deleteMovie($_POST['MaPhim']);
        header('Location: manage_movies.php?success=1');
        exit;
    }
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
        <h1>Quản lý Danh mục Phim</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Thao tác thành công!</div>
        <?php endif; ?>
        
        <div class="admin-section">
            <h2>Thêm phim mới</h2>
            <form method="POST" enctype="multipart/form-data" class="movie-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên phim *</label>
                        <input type="text" name="TenPhim" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Thể loại *</label>
                        <input type="text" name="TheLoai" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Thời lượng (phút) *</label>
                        <input type="number" name="ThoiLuong" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Trạng thái *</label>
                        <select name="TrangThai" required>
                            <option value="dang_chieu">Đang chiếu</option>
                            <option value="sap_chieu">Sắp chiếu</option>
                            <option value="ngung_chieu">Ngừng chiếu</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="MoTa" rows="4"></textarea>
                </div>
                
                <div class="form-group">
                    <label>Poster phim</label>
                    <input type="file" name="HinhAnh" accept="image/*">
                </div>
                
                <button type="submit" name="add_movie" class="btn">Thêm phim</button>
            </form>
        </div>
        
        <div class="admin-section">
            <h2>Danh sách phim</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
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
                        <td><?= $movie['MaPhim'] ?></td>
                        <td>
                            <img src="../assets/images/posters/<?= $movie['HinhAnh'] ?>" 
                                 alt="<?= $movie['TenPhim'] ?>" class="table-poster">
                        </td>
                        <td><?= $movie['TenPhim'] ?></td>
                        <td><?= $movie['TheLoai'] ?></td>
                        <td><?= $movie['ThoiLuong'] ?> phút</td>
                        <td>
                            <span class="status-badge <?= $movie['TrangThai'] ?>">
                                <?= getMovieStatusText($movie['TrangThai']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="edit_movie.php?id=<?= $movie['MaPhim'] ?>" class="btn-edit">Sửa</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="MaPhim" value="<?= $movie['MaPhim'] ?>">
                                <button type="submit" name="delete_movie" class="btn-delete" 
                                        onclick="return confirm('Xoá phim này?');">Xoá</button>
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