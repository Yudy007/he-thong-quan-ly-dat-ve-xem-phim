<?php
require_once '../includes/auth.php';
checkRole('admin');

$users = getAllUsers();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'TenDangNhap' => $_POST['TenDangNhap'],
        'MatKhau' => password_hash($_POST['MatKhau'], PASSWORD_DEFAULT),
        'HoTen' => $_POST['HoTen'],
        'VaiTro' => $_POST['VaiTro']
    ];
    
    if (isset($_POST['add_user'])) {
        insertUser($data);
    } elseif (isset($_POST['update_user'])) {
        $data['MaND'] = $_POST['MaND'];
        updateUser($data);
    } elseif (isset($_POST['delete_user'])) {
        deleteUser($_POST['MaND']);
    }
    
    header('Location: manage_users.php?success=1');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Người dùng</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>Quản lý Tài khoản Người dùng</h1>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Thao tác thành công!</div>
        <?php endif; ?>
        
        <div class="admin-section">
            <h2>Thêm tài khoản mới</h2>
            <form method="POST" class="user-form">
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên đăng nhập *</label>
                        <input type="text" name="TenDangNhap" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mật khẩu *</label>
                        <input type="password" name="MatKhau" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Họ tên *</label>
                        <input type="text" name="HoTen" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Vai trò *</label>
                        <select name="VaiTro" required>
                            <option value="khachhang">Khách hàng</option>
                            <option value="nhanvien">Nhân viên</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="add_user" class="btn">Thêm người dùng</button>
            </form>
        </div>
        
        <div class="admin-section">
            <h2>Danh sách người dùng</h2>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên đăng nhập</th>
                        <th>Họ tên</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= $user['MaND'] ?></td>
                        <td><?= $user['TenDangNhap'] ?></td>
                        <td><?= $user['HoTen'] ?></td>
                        <td>
                            <span class="role-badge <?= $user['VaiTro'] ?>">
                                <?= getUserRoleText($user['VaiTro']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['NgayTao'])) ?></td>
                        <td>
                            <a href="edit_user.php?id=<?= $user['MaND'] ?>" class="btn-edit">Sửa</a>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="MaND" value="<?= $user['MaND'] ?>">
                                <button type="submit" name="delete_user" class="btn-delete" 
                                        onclick="return confirm('Xoá người dùng này?');">Xoá</button>
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