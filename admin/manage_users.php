<?php
// File: /admin/manage_users.php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/db_connect.php';

// Kiểm tra quyền admin
checkRole('admin');

// Xử lý các thao tác
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $data = [
            'ten_dang_nhap' => $_POST['ten_dang_nhap'],
            'mat_khau' => $_POST['mat_khau'],
            'ho_ten' => $_POST['ho_ten'],
            'vai_tro' => $_POST['vai_tro'],
            'email' => $_POST['email'],
            'sdt' => $_POST['sdt']
        ];
        
        if (insertUser($data)) {
            $_SESSION['success'] = "Thêm người dùng thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi thêm người dùng.";
        }
    } 
    elseif (isset($_POST['update_user'])) {
        $data = [
            'ma_nd' => $_POST['ma_nd'],
            'ten_dang_nhap' => $_POST['ten_dang_nhap'],
            'ho_ten' => $_POST['ho_ten'],
            'vai_tro' => $_POST['vai_tro'],
            'email' => $_POST['email'],
            'sdt' => $_POST['sdt']
        ];
        
        // Chỉ cập nhật mật khẩu nếu có nhập
        if (!empty($_POST['mat_khau'])) {
            $data['mat_khau'] = $_POST['mat_khau'];
        }
        
        if (updateUser($data)) {
            $_SESSION['success'] = "Cập nhật người dùng thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi cập nhật người dùng.";
        }
    }
    
    // Tránh resubmit form khi refresh
    header("Location: manage_users.php");
    exit();
}

// Xử lý xóa người dùng
if (isset($_GET['delete'])) {
    $ma_nd = $_GET['delete'];
    
    // Kiểm tra không cho xóa admin
    $user = getUserById($ma_nd);
    if ($user && $user['VAI_TRO'] != 'admin') {
        if (deleteUser($ma_nd)) {
            $_SESSION['success'] = "Xóa người dùng thành công!";
        } else {
            $_SESSION['error'] = "Có lỗi xảy ra khi xóa người dùng.";
        }
    } else {
        $_SESSION['error'] = "Không thể xóa tài khoản admin!";
    }
    
    header("Location: manage_users.php");
    exit();
}

// Lấy danh sách người dùng
$users = getUsers();

include_once '../includes/header.php';
?>

<div class="container">
    <h1 class="mb-4">Quản lý Người Dùng</h1>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-user-plus me-2"></i>Thêm người dùng mới
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="ten_dang_nhap" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="ten_dang_nhap" name="ten_dang_nhap" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="mat_khau" class="form-label">Mật khẩu</label>
                        <input type="password" class="form-control" id="mat_khau" name="mat_khau" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="ho_ten" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="ho_ten" name="ho_ten" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="vai_tro" class="form-label">Vai trò</label>
                        <select class="form-select" id="vai_tro" name="vai_tro" required>
                            <option value="admin">Admin</option>
                            <option value="nhanvien">Nhân viên</option>
                            <option value="khachhang" selected>Khách hàng</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="sdt" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="sdt" name="sdt">
                    </div>
                </div>
                <button type="submit" name="add_user" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i>Thêm người dùng
                </button>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header bg-info text-white">
            <i class="fas fa-users me-2"></i>Danh sách người dùng
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Mã ND</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ tên</th>
                            <th>Vai trò</th>
                            <th>Email</th>
                            <th>SĐT</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['MA_ND']) ?></td>
                            <td><?= htmlspecialchars($user['TEN_DANG_NHAP']) ?></td>
                            <td><?= htmlspecialchars($user['HO_TEN']) ?></td>
                            <td>
                                <span class="badge 
                                    <?= $user['VAI_TRO'] == 'admin' ? 'bg-danger' : 
                                      ($user['VAI_TRO'] == 'nhanvien' ? 'bg-warning text-dark' : 'bg-success') ?>">
                                    <?= ucfirst($user['VAI_TRO']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($user['EMAIL'] ?? '') ?></td>
                            <td><?= htmlspecialchars($user['SDT'] ?? '') ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning edit-user" 
                                    data-userid="<?= $user['MA_ND'] ?>"
                                    data-username="<?= htmlspecialchars($user['TEN_DANG_NHAP']) ?>"
                                    data-fullname="<?= htmlspecialchars($user['HO_TEN']) ?>"
                                    data-role="<?= $user['VAI_TRO'] ?>"
                                    data-email="<?= htmlspecialchars($user['EMAIL'] ?? '') ?>"
                                    data-phone="<?= htmlspecialchars($user['SDT'] ?? '') ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($user['VAI_TRO'] != 'admin'): ?>
                                <a href="manage_users.php?delete=<?= $user['MA_ND'] ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Bạn chắc chắn muốn xóa người dùng này?')">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal chỉnh sửa người dùng -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa người dùng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="ma_nd" id="edit_ma_nd">
                    <div class="mb-3">
                        <label for="edit_ten_dang_nhap" class="form-label">Tên đăng nhập</label>
                        <input type="text" class="form-control" id="edit_ten_dang_nhap" name="ten_dang_nhap" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_mat_khau" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                        <input type="password" class="form-control" id="edit_mat_khau" name="mat_khau">
                    </div>
                    <div class="mb-3">
                        <label for="edit_ho_ten" class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="edit_ho_ten" name="ho_ten" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_vai_tro" class="form-label">Vai trò</label>
                        <select class="form-select" id="edit_vai_tro" name="vai_tro" required>
                            <option value="admin">Admin</option>
                            <option value="nhanvien">Nhân viên</option>
                            <option value="khachhang">Khách hàng</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="edit_sdt" class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="edit_sdt" name="sdt">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" name="update_user" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>

<script>
// Xử lý modal chỉnh sửa người dùng
document.querySelectorAll('.edit-user').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.dataset.userid;
        const username = this.dataset.username;
        const fullname = this.dataset.fullname;
        const role = this.dataset.role;
        const email = this.dataset.email;
        const phone = this.dataset.phone;
        
        document.getElementById('edit_ma_nd').value = userId;
        document.getElementById('edit_ten_dang_nhap').value = username;
        document.getElementById('edit_ho_ten').value = fullname;
        document.getElementById('edit_vai_tro').value = role;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_sdt').value = phone;
        
        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    });
});
</script>