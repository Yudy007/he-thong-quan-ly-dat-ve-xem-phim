<?php
// Script để tạo tài khoản admin đầu tiên
require_once 'includes/db_connect.php';

echo "<h1>🔧 Tạo tài khoản Admin đầu tiên</h1>";

try {
    $conn = connectOracle();
    
    // Kiểm tra xem đã có admin chưa
    $check_sql = "SELECT COUNT(*) as count FROM NguoiDung WHERE VaiTro = 'admin'";
    $check_stmt = oci_parse($conn, $check_sql);
    oci_execute($check_stmt);
    $result = oci_fetch_assoc($check_stmt);
    
    if ($result['COUNT'] > 0) {
        echo "<p style='color: orange;'>⚠️ Đã có tài khoản admin trong hệ thống.</p>";
        
        // Hiển thị danh sách admin hiện có
        $list_sql = "SELECT MaND, TenDangNhap, HoTen FROM NguoiDung WHERE VaiTro = 'admin'";
        $list_stmt = oci_parse($conn, $list_sql);
        oci_execute($list_stmt);
        
        echo "<h3>📋 Danh sách Admin hiện có:</h3>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'><th>Mã ND</th><th>Tên đăng nhập</th><th>Họ tên</th></tr>";
        
        while ($admin = oci_fetch_assoc($list_stmt)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['MAND']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['TENDANGNHAP']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['HOTEN']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
    } else {
        // Tạo admin mới
        $admin_data = [
            'MaND' => 'ADMIN001',
            'TenDangNhap' => 'admin',
            'MatKhau' => 'admin123',
            'HoTen' => 'Quản trị viên hệ thống',
            'VaiTro' => 'admin'
        ];
        
        $insert_sql = "INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro) 
                       VALUES (:mand, :tendn, :matkhau, :hoten, :vaitro)";
        
        $insert_stmt = oci_parse($conn, $insert_sql);
        oci_bind_by_name($insert_stmt, ":mand", $admin_data['MaND']);
        oci_bind_by_name($insert_stmt, ":tendn", $admin_data['TenDangNhap']);
        oci_bind_by_name($insert_stmt, ":matkhau", $admin_data['MatKhau']);
        oci_bind_by_name($insert_stmt, ":hoten", $admin_data['HoTen']);
        oci_bind_by_name($insert_stmt, ":vaitro", $admin_data['VaiTro']);
        
        if (oci_execute($insert_stmt)) {
            echo "<p style='color: green;'>✅ Tạo tài khoản admin thành công!</p>";
            echo "<div style='background: #e7f3ff; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
            echo "<h3>🔑 Thông tin đăng nhập:</h3>";
            echo "<p><strong>Tên đăng nhập:</strong> admin</p>";
            echo "<p><strong>Mật khẩu:</strong> admin123</p>";
            echo "<p><strong>Vai trò:</strong> Admin</p>";
            echo "</div>";
        } else {
            $error = oci_error($insert_stmt);
            echo "<p style='color: red;'>❌ Lỗi tạo admin: " . $error['message'] . "</p>";
        }
    }
    
    oci_close($conn);
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Lỗi kết nối database: " . $e->getMessage() . "</p>";
}

// Tạo thêm dữ liệu mẫu nếu cần
echo "<h2>🎬 Tạo dữ liệu mẫu</h2>";

if (isset($_POST['create_sample_data'])) {
    try {
        $conn = connectOracle();
        
        // Tạo phòng chiếu mẫu
        $rooms = [
            ['P001', 'Phòng VIP 1', 50],
            ['P002', 'Phòng thường 1', 80],
            ['P003', 'Phòng thường 2', 80]
        ];
        
        foreach ($rooms as $room) {
            $room_sql = "INSERT INTO PhongChieu (MaPhong, TenPhong, SoLuongGhe) VALUES (:mp, :tp, :slg)";
            $room_stmt = oci_parse($conn, $room_sql);
            oci_bind_by_name($room_stmt, ":mp", $room[0]);
            oci_bind_by_name($room_stmt, ":tp", $room[1]);
            oci_bind_by_name($room_stmt, ":slg", $room[2]);
            @oci_execute($room_stmt); // @ để bỏ qua lỗi nếu đã tồn tại
        }
        
        // Tạo ghế mẫu cho phòng P001
        for ($i = 1; $i <= 50; $i++) {
            $seat_id = "G" . str_pad($i, 3, "0", STR_PAD_LEFT);
            $seat_num = chr(65 + floor(($i-1)/10)) . (($i-1)%10 + 1); // A1, A2, ..., B1, B2, ...
            $seat_type = ($i <= 20) ? 'vip' : 'thuong';
            
            $seat_sql = "INSERT INTO Ghe (MaGhe, MaPhong, SoGhe, LoaiGhe) VALUES (:mg, :mp, :sg, :lg)";
            $seat_stmt = oci_parse($conn, $seat_sql);
            oci_bind_by_name($seat_stmt, ":mg", $seat_id);
            oci_bind_by_name($seat_stmt, ":mp", "P001");
            oci_bind_by_name($seat_stmt, ":sg", $seat_num);
            oci_bind_by_name($seat_stmt, ":lg", $seat_type);
            @oci_execute($seat_stmt);
        }
        
        // Tạo phim mẫu
        $movies = [
            ['PHIM001', 'Avengers: Endgame', 'Hành động', 181, 'Cuộc chiến cuối cùng của các siêu anh hùng', 'dang_chieu'],
            ['PHIM002', 'Spider-Man: No Way Home', 'Hành động', 148, 'Người nhện đối mặt với đa vũ trụ', 'dang_chieu'],
            ['PHIM003', 'The Batman', 'Hành động', 176, 'Phiên bản mới của Người Dơi', 'dang_chieu']
        ];
        
        foreach ($movies as $movie) {
            $movie_sql = "INSERT INTO Phim (MaPhim, TenPhim, TheLoai, ThoiLuong, MoTa, TrangThai) VALUES (:mp, :tp, :tl, :tg, :mt, :tt)";
            $movie_stmt = oci_parse($conn, $movie_sql);
            oci_bind_by_name($movie_stmt, ":mp", $movie[0]);
            oci_bind_by_name($movie_stmt, ":tp", $movie[1]);
            oci_bind_by_name($movie_stmt, ":tl", $movie[2]);
            oci_bind_by_name($movie_stmt, ":tg", $movie[3]);
            oci_bind_by_name($movie_stmt, ":mt", $movie[4]);
            oci_bind_by_name($movie_stmt, ":tt", $movie[5]);
            @oci_execute($movie_stmt);
        }
        
        echo "<p style='color: green;'>✅ Tạo dữ liệu mẫu thành công!</p>";
        oci_close($conn);
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Lỗi tạo dữ liệu mẫu: " . $e->getMessage() . "</p>";
    }
}

echo "<form method='POST'>";
echo "<button type='submit' name='create_sample_data' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;'>🎬 Tạo dữ liệu mẫu</button>";
echo "</form>";

echo "<h2>🚀 Bước tiếp theo</h2>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<ol>";
echo "<li><a href='login.php' style='color: #007bff;'>Đăng nhập với tài khoản admin</a></li>";
echo "<li>Truy cập Admin Dashboard để quản lý hệ thống</li>";
echo "<li>Tạo thêm phòng chiếu, ghế, phim, suất chiếu</li>";
echo "<li>Tạo tài khoản nhân viên</li>";
echo "<li>Test với tài khoản khách hàng</li>";
echo "</ol>";
echo "</div>";

echo "<p style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🏠 Trang chủ</a>";
echo "<a href='test_system.php' style='background: #17a2b8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin: 0 10px;'>🧪 Test hệ thống</a>";
echo "</p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1 { color: #333; border-bottom: 3px solid #007bff; padding-bottom: 10px; }
h2 { color: #666; margin-top: 30px; }
table { margin: 20px 0; }
th, td { padding: 10px; text-align: left; }
a { color: #007bff; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
