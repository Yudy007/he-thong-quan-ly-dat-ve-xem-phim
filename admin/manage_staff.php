<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

$message = '';
$error = '';

// Xử lý tạo tài khoản nhân viên mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_staff'])) {
    $data = [
        'TenDangNhap' => $_POST['ten_dang_nhap'],
        'MatKhau' => $_POST['mat_khau'],
        'HoTen' => $_POST['ho_ten']
    ];
    
    if (createStaffAccount($data)) {
        $message = "Tạo tài khoản nhân viên thành công!";
    } else {
        $error = "Có lỗi xảy ra khi tạo tài khoản nhân viên. Có thể tên đăng nhập đã tồn tại.";
    }
}

// Xử lý xóa tài khoản nhân viên
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_staff'])) {
    $staffId = $_POST['staff_id'];
    
    if (deleteStaffAccount($staffId)) {
        $message = "Xóa tài khoản nhân viên thành công!";
    } else {
        $error = "Có lỗi xảy ra khi xóa tài khoản nhân viên.";
    }
}

$staffList = getStaffList();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Nhân viên</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .form-section { background: #f8f9fa; padding: 25px; margin: 20px 0; border-radius: 10px; border-left: 4px solid #007bff; }
        .form-row { display: flex; gap: 15px; margin: 15px 0; align-items: end; }
        .form-group { flex: 1; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .staff-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .staff-table th { background: #007bff; color: white; padding: 15px; text-align: left; }
        .staff-table td { padding: 15px; border-bottom: 1px solid #eee; }
        .staff-table tr:hover { background: #f8f9fa; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; font-size: 14px; transition: all 0.3s; }
        .btn-primary { background: #007bff; color: white; }
        .btn-primary:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; color: white; }
        .btn-danger:hover { background: #c82333; }
        .alert { padding: 15px; margin: 15px 0; border-radius: 5px; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .stats-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin: 20px 0; }
        .stat-card { background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>👨‍💼 Quản lý Nhân viên</h1>
        
        <?php if ($message): ?>
            <div class="alert success">✅ <?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert error">❌ <?= $error ?></div>
        <?php endif; ?>
        
        <!-- Thống kê nhân viên -->
        <div class="stats-cards">
            <div class="stat-card">
                <div class="stat-number"><?= count($staffList) ?></div>
                <div>Tổng nhân viên</div>
            </div>
        </div>
        
        <!-- Form tạo tài khoản nhân viên mới -->
        <div class="form-section">
            <h2>➕ Tạo tài khoản nhân viên mới</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Tên đăng nhập:</label>
                        <input type="text" name="ten_dang_nhap" required placeholder="VD: nhanvien01">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu:</label>
                        <input type="password" name="mat_khau" required placeholder="Mật khẩu mạnh">
                    </div>
                    <div class="form-group">
                        <label>Họ và tên:</label>
                        <input type="text" name="ho_ten" required placeholder="VD: Nguyễn Văn A">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="create_staff" class="btn btn-primary">Tạo tài khoản</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Danh sách nhân viên -->
        <h2>📋 Danh sách nhân viên hiện tại</h2>
        
        <?php if (count($staffList) > 0): ?>
            <table class="staff-table">
                <thead>
                    <tr>
                        <th>Mã NV</th>
                        <th>Tên đăng nhập</th>
                        <th>Họ và tên</th>
                        <th>Vai trò</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($staffList as $staff): ?>
                    <tr>
                        <td><?= htmlspecialchars($staff['MAND']) ?></td>
                        <td><?= htmlspecialchars($staff['TENDANGNHAP']) ?></td>
                        <td><?= htmlspecialchars($staff['HOTEN']) ?></td>
                        <td><span style="background: #28a745; color: white; padding: 4px 8px; border-radius: 4px;">Nhân viên</span></td>
                        <td>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tài khoản này?')">
                                <input type="hidden" name="staff_id" value="<?= $staff['MAND'] ?>">
                                <button type="submit" name="delete_staff" class="btn btn-danger">🗑️ Xóa</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 10px;">
                <h3>Chưa có nhân viên nào</h3>
                <p>Hãy tạo tài khoản nhân viên đầu tiên bằng form ở trên.</p>
            </div>
        <?php endif; ?>
        
        <!-- Hướng dẫn -->
        <div style="background: #e7f3ff; padding: 20px; border-radius: 10px; margin: 30px 0; border-left: 4px solid #007bff;">
            <h3>📝 Hướng dẫn quản lý nhân viên:</h3>
            <ul>
                <li><strong>Tạo tài khoản:</strong> Nhập đầy đủ thông tin và tạo mật khẩu mạnh cho nhân viên</li>
                <li><strong>Quyền hạn nhân viên:</strong> Chỉ có thể kiểm tra vé và xác nhận vé, không thể quản lý hệ thống</li>
                <li><strong>Xóa tài khoản:</strong> Cẩn thận khi xóa, dữ liệu sẽ không thể khôi phục</li>
                <li><strong>Bảo mật:</strong> Nhân viên nên đổi mật khẩu sau lần đăng nhập đầu tiên</li>
            </ul>
        </div>
    </div>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
