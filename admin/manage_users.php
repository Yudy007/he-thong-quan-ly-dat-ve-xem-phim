<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

$users = getUsers();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = [
        'ten_dang_nhap' => $_POST['TenDangNhap'],
        'mat_khau' => $_POST['MatKhau'],
        'ho_ten' => $_POST['HoTen'],
        'vai_tro' => $_POST['VaiTro'],
        'email' => $_POST['Email'],
        'sdt' => $_POST['SDT']
    ];

    if (isset($_POST['add_user'])) {
        insertUser($data);
    } elseif (isset($_POST['update_user'])) {
        $data['ma_nd'] = $_POST['MaND'];
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
    <h1>👥 Quản lý Tài khoản Người dùng</h1>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert success">Thao tác thành công!</div>
    <?php endif; ?>

    <!-- Form thêm người dùng -->
    <div class="admin-section">
        <h2>Thêm người dùng</h2>
        <form method="POST" class="user-form">
            <div class="form-row">
                <input type="text" name="TenDangNhap" placeholder="Tên đăng nhập" required>
                <input type="password" name="MatKhau" placeholder="Mật khẩu" required>
                <input type="text" name="HoTen" placeholder="Họ tên" required>
            </div>
            <div class="form-row">
                <select name="VaiTro" required>
                    <option value="khachhang">Khách hàng</option>
                    <option value="nhanvien">Nhân viên</option>
                    <option value="admin">Quản trị viên</option>
                </select>
                <input type="email" name="Email" placeholder="Email">
                <input type="text" name="SDT" placeholder="Số điện thoại">
            </div>
            <button type="submit" name="add_user" class="btn">Thêm người dùng</button>
        </form>
    </div>

    <!-- Danh sách người dùng -->
    <div class="admin-section">
        <h2>Danh sách người dùng</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tài khoản</th>
                    <th>Họ tên</th>
                    <th>Vai trò</th>
                    <th>Email</th>
                    <th>SDT</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['MA_ND']) ?></td>
                        <td><?= htmlspecialchars($user['TEN_DANG_NHAP']) ?></td>
                        <td><?= htmlspecialchars($user['HO_TEN']) ?></td>
                        <td><?= $user['VAI_TRO'] ?></td>
                        <td><?= htmlspecialchars($user['EMAIL']) ?></td>
                        <td><?= htmlspecialchars($user['SDT']) ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Xoá người dùng này?');" style="display:inline;">
                                <input type="hidden" name="MaND" value="<?= $user['MA_ND'] ?>">
                                <button type="submit" name="delete_user" class="btn-delete">Xoá</button>
                            </form>
                            <!-- Có thể tách sửa sang edit_user.php nếu cần -->
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
