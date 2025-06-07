<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
checkRole('admin');

$message = '';
$error = '';

// Xử lý thêm phòng mới
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_room'])) {
    $data = [
        'MaPhong' => $_POST['ma_phong'],
        'TenPhong' => $_POST['ten_phong'],
        'SoLuongGhe' => $_POST['so_luong_ghe']
    ];
    
    if (addRoom($data)) {
        $message = "Thêm phòng chiếu thành công!";
    } else {
        $error = "Có lỗi xảy ra khi thêm phòng chiếu.";
    }
}

// Xử lý thêm ghế
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_seat'])) {
    $data = [
        'MaGhe' => $_POST['ma_ghe'],
        'MaPhong' => $_POST['ma_phong_ghe'],
        'SoGhe' => $_POST['so_ghe'],
        'LoaiGhe' => $_POST['loai_ghe']
    ];
    
    if (addSeat($data)) {
        $message = "Thêm ghế thành công!";
    } else {
        $error = "Có lỗi xảy ra khi thêm ghế.";
    }
}

$rooms = getRooms();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Phòng chiếu & Ghế</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .room-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin: 20px 0; }
        .room-card { border: 1px solid #ddd; padding: 15px; border-radius: 8px; background: #f9f9f9; }
        .seat-grid { display: grid; grid-template-columns: repeat(10, 1fr); gap: 5px; margin: 10px 0; }
        .seat { width: 30px; height: 30px; border: 1px solid #ccc; display: flex; align-items: center; justify-content: center; font-size: 12px; }
        .seat.vip { background: #ffd700; }
        .seat.thuong { background: #e0e0e0; }
        .form-section { background: #f5f5f5; padding: 20px; margin: 20px 0; border-radius: 8px; }
        .form-row { display: flex; gap: 15px; margin: 10px 0; }
        .form-group { flex: 1; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .alert.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .alert.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .btn { padding: 8px 16px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container">
        <h1>🏢 Quản lý Phòng chiếu & Ghế</h1>
        
        <?php if ($message): ?>
            <div class="alert success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>
        
        <!-- Form thêm phòng mới -->
        <div class="form-section">
            <h2>➕ Thêm phòng chiếu mới</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã phòng:</label>
                        <input type="text" name="ma_phong" required placeholder="VD: P001">
                    </div>
                    <div class="form-group">
                        <label>Tên phòng:</label>
                        <input type="text" name="ten_phong" required placeholder="VD: Phòng VIP 1">
                    </div>
                    <div class="form-group">
                        <label>Số lượng ghế:</label>
                        <input type="number" name="so_luong_ghe" required min="1" max="200" placeholder="VD: 100">
                    </div>
                    <div class="form-group">
                        <button type="submit" name="add_room" class="btn">Thêm phòng</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Danh sách phòng hiện có -->
        <h2>📋 Danh sách phòng chiếu</h2>
        <div class="room-grid">
            <?php foreach ($rooms as $room): ?>
                <div class="room-card">
                    <h3><?= htmlspecialchars($room['TENPHONG']) ?> (<?= htmlspecialchars($room['MAPHONG']) ?>)</h3>
                    <p>Sức chứa: <?= $room['SOLUONGGHE'] ?> ghế</p>
                    
                    <?php $seats = getSeatsByRoom($room['MAPHONG']); ?>
                    <p>Ghế đã tạo: <?= count($seats) ?>/<?= $room['SOLUONGGHE'] ?></p>
                    
                    <?php if (count($seats) > 0): ?>
                        <div class="seat-grid">
                            <?php foreach ($seats as $seat): ?>
                                <div class="seat <?= $seat['LOAIGHE'] ?>" title="Ghế <?= $seat['SOGHE'] ?> - <?= $seat['LOAIGHE'] ?>">
                                    <?= $seat['SOGHE'] ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <button onclick="showAddSeatForm('<?= $room['MAPHONG'] ?>')" class="btn">Thêm ghế</button>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Form thêm ghế (ẩn ban đầu) -->
        <div id="add-seat-form" class="form-section" style="display: none;">
            <h2>➕ Thêm ghế mới</h2>
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label>Mã ghế:</label>
                        <input type="text" name="ma_ghe" required placeholder="VD: G001">
                    </div>
                    <div class="form-group">
                        <label>Phòng:</label>
                        <select name="ma_phong_ghe" id="ma_phong_ghe" required>
                            <option value="">Chọn phòng</option>
                            <?php foreach ($rooms as $room): ?>
                                <option value="<?= $room['MAPHONG'] ?>"><?= $room['TENPHONG'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Số ghế:</label>
                        <input type="text" name="so_ghe" required placeholder="VD: A1, B2">
                    </div>
                    <div class="form-group">
                        <label>Loại ghế:</label>
                        <select name="loai_ghe" required>
                            <option value="thuong">Thường</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" name="add_seat" class="btn">Thêm ghế</button>
                        <button type="button" onclick="hideAddSeatForm()" class="btn btn-danger">Hủy</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function showAddSeatForm(roomId) {
            document.getElementById('add-seat-form').style.display = 'block';
            document.getElementById('ma_phong_ghe').value = roomId;
            document.getElementById('add-seat-form').scrollIntoView();
        }
        
        function hideAddSeatForm() {
            document.getElementById('add-seat-form').style.display = 'none';
        }
    </script>
    
    <?php include '../includes/footer.php'; ?>
</body>
</html>
