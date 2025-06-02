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
function getMovies() {
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
?>
