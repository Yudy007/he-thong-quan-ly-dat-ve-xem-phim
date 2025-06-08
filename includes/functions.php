<?php
require_once 'db_connect.php';

/**
 * Đăng nhập người dùng
 * @param string $username
 * @param string $password
 * @return array|false
 */
function loginUser($username, $password) {
    $conn = connectOracle();
    $sql = "SELECT * FROM NguoiDung WHERE TenDangNhap = :username AND MatKhau = :password";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":username", $username);
    oci_bind_by_name($stmt, ":password", $password);
    oci_execute($stmt);
    $result = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    oci_close($conn);
    return $result ?: false;
}

/**
 * Đăng ký tài khoản người dùng mới
 * @param array $data
 * @return bool
 */
function registerUser($data) {
    $conn = connectOracle();
    $sql = "INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro) 
            VALUES (:mand, :tendn, :matkhau, :hoten, 'khachhang')";
    $stmt = oci_parse($conn, $sql);
    $data['MaND'] = uniqid("ND");
    oci_bind_by_name($stmt, ":mand", $data['MaND']);
    oci_bind_by_name($stmt, ":tendn", $data['TenDangNhap']);
    oci_bind_by_name($stmt, ":matkhau", $data['MatKhau']);
    oci_bind_by_name($stmt, ":hoten", $data['HoTen']);
    $success = oci_execute($stmt);
    oci_free_statement($stmt);
    oci_close($conn);
    return $success;
}

/**
 * Lấy danh sách phim đang chiếu
 * @return array
 */
function getAllMovies() {
    $conn = connectOracle();
    $sql = "SELECT * FROM Phim WHERE TrangThai = 'dang_chieu'";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    $movies = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $movies[] = $row;
    }
    oci_free_statement($stmt);
    oci_close($conn);
    return $movies;
}

/**
 * Lấy danh sách suất chiếu theo mã phim
 * @param string $movieId
 * @return array
 */
function getSchedules($movieId) {
    $conn = connectOracle();
    $sql = "SELECT * FROM SuatChieu WHERE MaPhim = :maphim";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":maphim", $movieId);
    oci_execute($stmt);
    $schedules = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $schedules[] = $row;
    }
    oci_free_statement($stmt);
    oci_close($conn);
    return $schedules;
}

/**
 * Lấy danh sách ghế chưa được đặt cho 1 suất chiếu
 * @param string $scheduleId
 * @return array
 */
function getAvailableSeats($scheduleId) {
    $conn = connectOracle();
    $sql = "SELECT Ghe.*
            FROM Ghe
            JOIN PhongChieu ON Ghe.MaPhong = PhongChieu.MaPhong
            JOIN SuatChieu ON SuatChieu.MaPhong = PhongChieu.MaPhong
            WHERE SuatChieu.MaSuat = :masuat
            AND Ghe.MaGhe NOT IN (
                SELECT MaGhe FROM Ve WHERE MaSuat = :masuat AND TrangThai IN ('da_dat', 'da_kiem_tra')
            )";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":masuat", $scheduleId);
    oci_execute($stmt);
    $seats = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $seats[] = $row;
    }
    oci_free_statement($stmt);
    oci_close($conn);
    return $seats;
}

/**
 * Đặt vé
 * @param string $userId
 * @param string $scheduleId
 * @param array $seatList
 * @return bool
 */
function bookTicket($userId, $scheduleId, $seatList) {
    $conn = connectOracle();
    $success = true;

    foreach ($seatList as $seatId) {
        $sql = "INSERT INTO Ve (MaVe, MaSuat, MaGhe, MaNguoiDung, ThoiGianDat, TrangThai)
                VALUES (:mave, :masuat, :maghe, :mand, SYSDATE, 'da_dat')";
        $stmt = oci_parse($conn, $sql);
        $maVe = uniqid("V");
        oci_bind_by_name($stmt, ":mave", $maVe);
        oci_bind_by_name($stmt, ":masuat", $scheduleId);
        oci_bind_by_name($stmt, ":maghe", $seatId);
        oci_bind_by_name($stmt, ":mand", $userId);
        $exec = oci_execute($stmt, OCI_NO_AUTO_COMMIT);
        if (!$exec) {
            $success = false;
            break;
        }
        oci_free_statement($stmt);
    }

    if ($success) {
        oci_commit($conn);
    } else {
        oci_rollback($conn);
    }

    oci_close($conn);
    return $success;
}

/**
 * Lấy danh sách vé đã đặt của người dùng
 * @param string $userId
 * @return array
 */
function getMyTickets($userId) {
    $conn = connectOracle();
    $sql = "SELECT Ve.*, Phim.TenPhim, SuatChieu.ThoiGianBatDau, Ghe.SoGhe
            FROM Ve
            JOIN SuatChieu ON Ve.MaSuat = SuatChieu.MaSuat
            JOIN Phim ON SuatChieu.MaPhim = Phim.MaPhim
            JOIN Ghe ON Ve.MaGhe = Ghe.MaGhe
            WHERE Ve.MaNguoiDung = :mand";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":mand", $userId);
    oci_execute($stmt);
    $tickets = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $tickets[] = $row;
    }
    oci_free_statement($stmt);
    oci_close($conn);
    return $tickets;
}

/**
 * Kiểm tra và xác nhận vé
 * @param string $ticketCode
 * @return string
 */
function checkTicket($ticketCode) {
    $conn = connectOracle();
    $sql = "SELECT TrangThai FROM Ve WHERE MaVe = :mave";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":mave", $ticketCode);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);

    if (!$row) {
        oci_close($conn);
        return "Vé không tồn tại.";
    }

    if ($row['TRANGTHAI'] === 'da_kiem_tra') {
        oci_close($conn);
        return "Vé đã được kiểm tra trước đó.";
    }

    $update = oci_parse($conn, "UPDATE Ve SET TrangThai = 'da_kiem_tra' WHERE MaVe = :mave");
    oci_bind_by_name($update, ":mave", $ticketCode);
    oci_execute($update);
    oci_free_statement($update);
    oci_close($conn);
    return "Vé hợp lệ – cho phép vào rạp.";
}

/**
 * Thống kê số vé theo phim
 * @return array
 */
function getStats() {
    $conn = connectOracle();
    $sql = "SELECT Phim.TenPhim, COUNT(*) AS SoVe, SUM(SuatChieu.GiaVe) AS DoanhThu
            FROM Ve
            JOIN SuatChieu ON Ve.MaSuat = SuatChieu.MaSuat
            JOIN Phim ON SuatChieu.MaPhim = Phim.MaPhim
            GROUP BY Phim.TenPhim";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    $stats = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $stats[] = $row;
    }
    oci_free_statement($stmt);
    oci_close($conn);
    return $stats;
}

// manage_users.php

/**
 * Lấy danh sách tất cả người dùng
 */
function getUsers() {
    $conn = connectOracle();
    $query = "SELECT MAND, TENDANGNHAP, HOTEN, VAITRO, EMAIL, SDT 
              FROM NGUOIDUNG 
              ORDER BY VAITRO, MAND";
    
    $stid = oci_parse($conn, $query);
    oci_execute($stid);
    
    $users = [];
    while ($row = oci_fetch_assoc($stid)) {
        $users[] = $row;
    }
    
    oci_free_statement($stid);
    oci_close($conn);
    
    return $users;
}

function getMovieNameById($movieId) {
    $conn = connectOracle();
    $sql = "SELECT TenPhim FROM Phim WHERE MaPhim = :id";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":id", $movieId);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    oci_free_statement($stmt);
    oci_close($conn);
    return $row['TENPHIM'] ?? null;
}

/**
 * Lấy thông tin người dùng bằng ID
 */
function getUserById($ma_nd) {
    $conn = connectOracle();
    $query = "SELECT MA_ND, TEN_DANG_NHAP, HO_TEN, VAI_TRO, EMAIL, SDT 
              FROM NGUOIDUNG 
              WHERE MA_ND = :ma_nd";
    
    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ':ma_nd', $ma_nd);
    oci_execute($stid);
    
    $user = oci_fetch_assoc($stid);
    
    oci_free_statement($stid);
    oci_close($conn);
    
    return $user;
}

/**
 * Thêm người dùng mới
 */
function insertUser($data) {
    $conn = connectOracle();
    
    // Mã hóa mật khẩu (trong thực tế nên sử dụng password_hash())
    $hashed_password = md5($data['mat_khau']);
    
    $query = "INSERT INTO NGUOIDUNG (
                TEN_DANG_NHAP, 
                MAT_KHAU, 
                HO_TEN, 
                VAI_TRO, 
                EMAIL, 
                SDT
              ) VALUES (
                :ten_dang_nhap, 
                :mat_khau, 
                :ho_ten, 
                :vai_tro, 
                :email, 
                :sdt
              )";
    
    $stid = oci_parse($conn, $query);
    
    oci_bind_by_name($stid, ':ten_dang_nhap', $data['ten_dang_nhap']);
    oci_bind_by_name($stid, ':mat_khau', $hashed_password);
    oci_bind_by_name($stid, ':ho_ten', $data['ho_ten']);
    oci_bind_by_name($stid, ':vai_tro', $data['vai_tro']);
    oci_bind_by_name($stid, ':email', $data['email']);
    oci_bind_by_name($stid, ':sdt', $data['sdt']);
    
    $result = oci_execute($stid);
    
    oci_free_statement($stid);
    oci_close($conn);
    
    return $result;
}

/**
 * Cập nhật thông tin người dùng
 */
function updateUser($data) {
    $conn = connectOracle();
    
    // Xây dựng câu lệnh SQL tùy thuộc vào việc có cập nhật mật khẩu hay không
    $password_clause = '';
    if (isset($data['mat_khau']) && !empty($data['mat_khau'])) {
        $hashed_password = md5($data['mat_khau']);
        $password_clause = "MAT_KHAU = :mat_khau,";
    }
    
    $query = "UPDATE NGUOIDUNG SET
                TEN_DANG_NHAP = :ten_dang_nhap,
                {$password_clause}
                HO_TEN = :ho_ten,
                VAI_TRO = :vai_tro,
                EMAIL = :email,
                SDT = :sdt
              WHERE MA_ND = :ma_nd";
    
    $stid = oci_parse($conn, $query);
    
    oci_bind_by_name($stid, ':ma_nd', $data['ma_nd']);
    oci_bind_by_name($stid, ':ten_dang_nhap', $data['ten_dang_nhap']);
    oci_bind_by_name($stid, ':ho_ten', $data['ho_ten']);
    oci_bind_by_name($stid, ':vai_tro', $data['vai_tro']);
    oci_bind_by_name($stid, ':email', $data['email']);
    oci_bind_by_name($stid, ':sdt', $data['sdt']);
    
    if (isset($hashed_password)) {
        oci_bind_by_name($stid, ':mat_khau', $hashed_password);
    }
    
    $result = oci_execute($stid);
    
    oci_free_statement($stid);
    oci_close($conn);
    
    return $result;
}

/**
 * Xóa người dùng
 */
function deleteUser($ma_nd) {
    $conn = connectOracle();

    $query = "DELETE FROM NGUOIDUNG WHERE MA_ND = :ma_nd";

    $stid = oci_parse($conn, $query);
    oci_bind_by_name($stid, ':ma_nd', $ma_nd);

    $result = oci_execute($stid);

    oci_free_statement($stid);
    oci_close($conn);

    return $result;
}

// ===== THÊM CÁC HÀM MỚI CHO HỆ THỐNG =====

/**
 * Lấy thống kê cho admin dashboard
 */
function getAdminStats() {
    $conn = connectOracle();

    // Tổng số phim
    $stmt1 = oci_parse($conn, "SELECT COUNT(*) as total FROM Phim");
    oci_execute($stmt1);
    $total_movies = oci_fetch_assoc($stmt1)['TOTAL'];

    // Tổng số vé đã bán
    $stmt2 = oci_parse($conn, "SELECT COUNT(*) as total FROM Ve WHERE TrangThai IN ('da_dat', 'da_kiem_tra')");
    oci_execute($stmt2);
    $total_tickets = oci_fetch_assoc($stmt2)['TOTAL'];

    // Doanh thu tháng này
    $stmt3 = oci_parse($conn, "SELECT SUM(sc.GiaVe) as revenue FROM Ve v JOIN SuatChieu sc ON v.MaSuat = sc.MaSuat WHERE EXTRACT(MONTH FROM v.ThoiGianDat) = EXTRACT(MONTH FROM SYSDATE) AND EXTRACT(YEAR FROM v.ThoiGianDat) = EXTRACT(YEAR FROM SYSDATE)");
    oci_execute($stmt3);
    $monthly_revenue = oci_fetch_assoc($stmt3)['REVENUE'] ?? 0;

    // Tổng số người dùng
    $stmt4 = oci_parse($conn, "SELECT COUNT(*) as total FROM NguoiDung");
    oci_execute($stmt4);
    $total_users = oci_fetch_assoc($stmt4)['TOTAL'];

    oci_close($conn);

    return [
        'total_movies' => $total_movies,
        'total_tickets' => $total_tickets,
        'monthly_revenue' => $monthly_revenue,
        'total_users' => $total_users
    ];
}

/**
 * Lấy hoạt động gần đây
 */
function getRecentActivities() {
    $conn = connectOracle();
    $sql = "SELECT v.ThoiGianDat as ThoiGian, nd.HoTen, 'Đặt vé' as HoatDong, p.TenPhim as ChiTiet
            FROM Ve v
            JOIN NguoiDung nd ON v.MaNguoiDung = nd.MaND
            JOIN SuatChieu sc ON v.MaSuat = sc.MaSuat
            JOIN Phim p ON sc.MaPhim = p.MaPhim
            ORDER BY v.ThoiGianDat DESC
            FETCH FIRST 10 ROWS ONLY";

    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $activities = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $activities[] = $row;
    }

    oci_close($conn);
    return $activities;
}

/**
 * Lấy suất chiếu hôm nay cho nhân viên
 */
function getTodaySchedules() {
    $conn = connectOracle();
    $sql = "SELECT sc.*, p.TenPhim, pc.TenPhong,
                   (SELECT COUNT(*) FROM Ghe WHERE MaPhong = sc.MaPhong) as TongGhe,
                   (SELECT COUNT(*) FROM Ghe g WHERE g.MaPhong = sc.MaPhong
                    AND g.MaGhe NOT IN (SELECT MaGhe FROM Ve WHERE MaSuat = sc.MaSuat AND TrangThai IN ('da_dat', 'da_kiem_tra'))) as GheTrong
            FROM SuatChieu sc
            JOIN Phim p ON sc.MaPhim = p.MaPhim
            JOIN PhongChieu pc ON sc.MaPhong = pc.MaPhong
            WHERE TRUNC(sc.ThoiGianBatDau) = TRUNC(SYSDATE)
            ORDER BY sc.ThoiGianBatDau";

    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $schedules = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $schedules[] = $row;
    }

    oci_close($conn);
    return $schedules;
}

/**
 * Lấy danh sách phòng chiếu
 */
function getRooms() {
    $conn = connectOracle();
    $sql = "SELECT * FROM PhongChieu ORDER BY MaPhong";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $rooms = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $rooms[] = $row;
    }

    oci_close($conn);
    return $rooms;
}

/**
 * Thêm phòng chiếu mới
 */
function addRoom($data) {
    $conn = connectOracle();
    $sql = "INSERT INTO PhongChieu (MaPhong, TenPhong, SoLuongGhe) VALUES (:maphong, :tenphong, :soluongghe)";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ":maphong", $data['MaPhong']);
    oci_bind_by_name($stmt, ":tenphong", $data['TenPhong']);
    oci_bind_by_name($stmt, ":soluongghe", $data['SoLuongGhe']);

    $result = oci_execute($stmt);
    oci_close($conn);
    return $result;
}

/**
 * Lấy danh sách ghế theo phòng
 */
function getSeatsByRoom($roomId) {
    $conn = connectOracle();
    $sql = "SELECT * FROM Ghe WHERE MaPhong = :maphong ORDER BY SoGhe";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":maphong", $roomId);
    oci_execute($stmt);

    $seats = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $seats[] = $row;
    }

    oci_close($conn);
    return $seats;
}

/**
 * Thêm ghế mới
 */
function addSeat($data) {
    $conn = connectOracle();
    $sql = "INSERT INTO Ghe (MaGhe, MaPhong, SoGhe, LoaiGhe) VALUES (:maghe, :maphong, :soghe, :loaighe)";
    $stmt = oci_parse($conn, $sql);

    oci_bind_by_name($stmt, ":maghe", $data['MaGhe']);
    oci_bind_by_name($stmt, ":maphong", $data['MaPhong']);
    oci_bind_by_name($stmt, ":soghe", $data['SoGhe']);
    oci_bind_by_name($stmt, ":loaighe", $data['LoaiGhe']);

    $result = oci_execute($stmt);
    oci_close($conn);
    return $result;
}

/**
 * Tạo tài khoản nhân viên (chỉ admin mới được dùng)
 */
function createStaffAccount($data) {
    $conn = connectOracle();
    $sql = "INSERT INTO NguoiDung (MaND, TenDangNhap, MatKhau, HoTen, VaiTro)
            VALUES (:mand, :tendn, :matkhau, :hoten, 'nhanvien')";
    $stmt = oci_parse($conn, $sql);

    $data['MaND'] = uniqid("NV");
    oci_bind_by_name($stmt, ":mand", $data['MaND']);
    oci_bind_by_name($stmt, ":tendn", $data['TenDangNhap']);
    oci_bind_by_name($stmt, ":matkhau", $data['MatKhau']);
    oci_bind_by_name($stmt, ":hoten", $data['HoTen']);

    $result = oci_execute($stmt);
    oci_close($conn);
    return $result;
}

/**
 * Lấy danh sách nhân viên (chỉ admin)
 */
function getStaffList() {
    $conn = connectOracle();
    $sql = "SELECT MaND, TenDangNhap, HoTen FROM NguoiDung WHERE VaiTro = 'nhanvien' ORDER BY HoTen";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);

    $staff = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $staff[] = $row;
    }

    oci_close($conn);
    return $staff;
}

/**
 * Xóa tài khoản nhân viên (chỉ admin)
 */
function deleteStaffAccount($staffId) {
    $conn = connectOracle();
    $sql = "DELETE FROM NguoiDung WHERE MaND = :mand AND VaiTro = 'nhanvien'";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":mand", $staffId);

    $result = oci_execute($stmt);
    oci_close($conn);
    return $result;
}

function getAllSchedules() {
    $conn = connectOracle();
    $sql = "SELECT sc.*, p.TenPhim, pc.TenPhong 
            FROM SuatChieu sc 
            JOIN Phim p ON sc.MaPhim = p.MaPhim 
            JOIN PhongChieu pc ON sc.MaPhong = pc.MaPhong 
            ORDER BY sc.ThoiGianBatDau DESC";
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    $schedules = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $schedules[] = $row;
    }
    return $schedules;
}

function getAllMoviesAdmin() {
    $conn = connectOracle();
    $stmt = oci_parse($conn, "SELECT * FROM Phim ORDER BY TenPhim");
    oci_execute($stmt);
    $result = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $result[] = $row;
    }
    return $result;
}

function insertSchedule($data) {
    $conn = connectOracle();
    $sql = "INSERT INTO SuatChieu (MaSuat, MaPhim, MaPhong, ThoiGianBatDau, ThoiGianKetThuc, GiaVe)
            VALUES (:MaSuat, :MaPhim, :MaPhong, TO_DATE(:BatDau, 'YYYY-MM-DD\"T\"HH24:MI'), TO_DATE(:KetThuc, 'YYYY-MM-DD\"T\"HH24:MI'), :GiaVe)";
    $stmt = oci_parse($conn, $sql);
    oci_bind_by_name($stmt, ":MaSuat", $data['MaSuat']);
    oci_bind_by_name($stmt, ":MaPhim", $data['MaPhim']);
    oci_bind_by_name($stmt, ":MaPhong", $data['MaPhong']);
    oci_bind_by_name($stmt, ":BatDau", $data['ThoiGianBatDau']);
    oci_bind_by_name($stmt, ":KetThuc", $data['ThoiGianKetThuc']);
    oci_bind_by_name($stmt, ":GiaVe", $data['GiaVe']);
    return oci_execute($stmt);
}

function deleteSchedule($maSuat) {
    $conn = connectOracle();
    $stmt = oci_parse($conn, "DELETE FROM SuatChieu WHERE MaSuat = :MaSuat");
    oci_bind_by_name($stmt, ":MaSuat", $maSuat);
    return oci_execute($stmt);
}

?>

