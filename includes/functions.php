<?php
require_once 'db_connect.php';

/** =========================
 * QUẢN LÝ NGƯỜI DÙNG
 * ========================= */

function loginUser($username, $password) {
    $conn = connectOracle();
    $stmt = oci_parse($conn, "SELECT * FROM NGUOIDUNG WHERE TENDANGNHAP = :username AND MATKHAU = :password");
    oci_bind_by_name($stmt, ':username', $username);
    oci_bind_by_name($stmt, ':password', $password);
    oci_execute($stmt);
    return oci_fetch_assoc($stmt) ?: false;
}

function registerUser($data) {
    $conn = connectOracle();
    $stmt = oci_parse($conn, 
        "INSERT INTO NGUOIDUNG (MAND, TENDANGNHAP, MATKHAU, HOTEN, VAITRO, EMAIL, SDT)
         VALUES (:ma_nd, :username, :password, :fullname, 'khachhang', :email, :sdt)");
    
    $data['ma_nd'] = uniqid("ND");
    $data['password'] = md5($data['MAT_KHAU']); // Sửa: lấy từ MAT_KHAU
    
    oci_bind_by_name($stmt, ':ma_nd', $data['ma_nd']);
    oci_bind_by_name($stmt, ':username', $data['TENDANGNHAP']);
    oci_bind_by_name($stmt, ':password', $data['password']);
    oci_bind_by_name($stmt, ':fullname', $data['HOTEN']);
    oci_bind_by_name($stmt, ':email', $data['EMAIL']);
    oci_bind_by_name($stmt, ':sdt', $data['SDT']);
    
    $result = oci_execute($stmt);
    if ($result) {
        oci_commit($conn); // THÊM COMMIT
    }
    oci_close($conn);
    return $result;
}

function getUsers() {
    return fetchAll("SELECT * FROM NGUOIDUNG ORDER BY VAITRO, MAND");
}

function getUserById($ma_nd) {
    return fetchSingle("SELECT * FROM NGUOIDUNG WHERE MAND = :ma_nd", [':ma_nd' => $ma_nd]);
}

function updateUser($data) {
    $passwordClause = !empty($data['MATKHAU']) ? "MATKHAU = :mat_khau," : "";
    
    $sql = "UPDATE NGUOIDUNG SET 
                TENDANGNHAP = :ten_dang_nhap,
                $passwordClause
                HOTEN = :ho_ten,
                VAITRO = :vai_tro,
                EMAIL = :email,
                SDT = :sdt
            WHERE MAND = :ma_nd";
    
    $params = [
        ':ma_nd' => $data['MAND'],
        ':ten_dang_nhap' => $data['TENDANGNHAP'],
        ':ho_ten' => $data['HOTEN'],
        ':vai_tro' => $data['VAITRO'],
        ':email' => $data['EMAIL'],
        ':sdt' => $data['SDT']
    ];
    
    if (!empty($data['MATKHAU'])) {
        $params[':mat_khau'] = md5($data['MATKHAU']);
    }
    
    return executeQuery($sql, $params);
}

function deleteUser($ma_nd) {
    return executeQuery("DELETE FROM NGUOIDUNG WHERE MAND = :ma_nd", [':ma_nd' => $ma_nd]);
}

/** =========================
 * QUẢN LÝ PHIM
 * ========================= */

function getAllMovies($status = null) {
    $sql = "SELECT * FROM PHIM";
    $params = [];
    
    if ($status) {
        $sql .= " WHERE TRANGTHAI = :status";
        $params[':status'] = $status;
    }
    
    $sql .= " ORDER BY TENPHIM";
    return fetchAll($sql, $params);
}

function getTenPhimById($maPhim) {
    $result = fetchSingle("SELECT TENPHIM FROM PHIM WHERE MAPHIM = :maPhim", [':maPhim' => $maPhim]);
    return $result['TENPHIM'] ?? null;
}

function insertMovie($data) {
    return executeQuery(
        "INSERT INTO PHIM (MAPHIM, TENPHIM, THELOAI, THOILUONG, MOTA, TRANGTHAI)
         VALUES (:ma, :ten, :tl, :thoiluong, :mota, :tt)", 
        [
            ':ma' => $data['MaPhim'],
            ':ten' => $data['TenPhim'],
            ':tl' => $data['TheLoai'],
            ':thoiluong' => $data['ThoiLuong'],
            ':mota' => $data['MoTa'],
            ':tt' => $data['TrangThai']
        ]
    );
}

function updateMovie($data) {
    return executeQuery(
        "UPDATE PHIM SET TENPHIM = :ten, THELOAI = :tl, THOILUONG = :thoiluong, 
         MOTA = :mota, TRANGTHAI = :tt WHERE MAPHIM = :ma",
        [
            ':ma' => $data['MaPhim'],
            ':ten' => $data['TenPhim'],
            ':tl' => $data['TheLoai'],
            ':thoiluong' => $data['ThoiLuong'],
            ':mota' => $data['MoTa'],
            ':tt' => $data['TrangThai']
        ]
    );
}

function deleteMovie($maPhim) {
    return executeQuery("DELETE FROM PHIM WHERE MAPHIM = :ma", [':ma' => $maPhim]);
}

/** =========================
 * QUẢN LÝ SUẤT CHIẾU
 * ========================= */

function getSchedules($movieId = null) {
    $sql = "SELECT sc.*, p.TENPHIM, pc.TENPHONG FROM SUATCHIEU sc
            JOIN PHIM p ON sc.MAPHIM = p.MAPHIM
            JOIN PHONGCHIEU pc ON sc.MAPHONG = pc.MAPHONG";
    
    $params = [];
    if ($movieId) {
        $sql .= " WHERE sc.MAPHIM = :maphim";
        $params[':maphim'] = $movieId;
    }
    
    $sql .= " ORDER BY sc.THOIGIANBATDAU";
    return fetchAll($sql, $params);
}

function insertSchedule($data) {
    return executeQuery(
        "INSERT INTO SUATCHIEU (MASUAT, MAPHIM, MAPHONG, THOIGIANBATDAU, THOIGIANKETTHUC, GIAVE)
         VALUES (:MaSuat, :MaPhim, :MaPhong, TO_DATE(:BatDau, 'YYYY-MM-DD\"T\"HH24:MI'), 
                TO_DATE(:KetThuc, 'YYYY-MM-DD\"T\"HH24:MI'), :GiaVe)",
        [
            ':MaSuat' => $data['MaSuat'],
            ':MaPhim' => $data['MaPhim'],
            ':MaPhong' => $data['MaPhong'],
            ':BatDau' => $data['ThoiGianBatDau'],
            ':KetThuc' => $data['ThoiGianKetThuc'],
            ':GiaVe' => $data['GiaVe']
        ]
    );
}

function deleteSchedule($maSuat) {
    return executeQuery("DELETE FROM SUATCHIEU WHERE MASUAT = :MaSuat", [':MaSuat' => $maSuat]);
}

/** =========================
 * QUẢN LÝ PHÒNG CHIẾU & GHẾ
 * ========================= */

function getRooms() {
    return fetchAll("SELECT * FROM PHONGCHIEU ORDER BY MAPHONG");
}

function addRoom($data) {
    return executeQuery(
        "INSERT INTO PHONGCHIEU (MAPHONG, TENPHONG, SOLUONGGHE) 
         VALUES (:maphong, :tenphong, :soluongghe)",
        [
            ':maphong' => $data['MaPhong'],
            ':tenphong' => $data['TenPhong'],
            ':soluongghe' => $data['SoLuongGhe']
        ]
    );
}

function getSeatsByRoom($roomId) {
    return fetchAll(
        "SELECT * FROM GHE WHERE MAPHONG = :maphong ORDER BY SOGHE",
        [':maphong' => $roomId]
    );
}

function getAvailableSeats($scheduleId) {
    return fetchAll(
        "SELECT g.* FROM GHE g
         JOIN PHONGCHIEU pc ON g.MAPHONG = pc.MAPHONG
         JOIN SUATCHIEU sc ON sc.MAPHONG = pc.MAPHONG
         WHERE sc.MASUAT = :masuat
           AND g.MAGHE NOT IN (
               SELECT MAGHE FROM VE 
               WHERE MASUAT = :masuat AND TRANGTHAI IN ('da_dat', 'da_kiem_tra')
           )",
        [':masuat' => $scheduleId]
    );
}

/** =========================
 * QUẢN LÝ VÉ
 * ========================= */

function insertVe($maSuat, $maGhe, $maNguoiDung) {
    return executeQuery(
        "INSERT INTO VE (MAVE, MASUAT, MAGHE, MANGUOIDUNG, THOIGIANDAT, TRANGTHAI)
         VALUES (:mave, :masuat, :maghe, :mand, SYSDATE, 'da_dat')",
        [
            ':mave' => uniqid("V"),
            ':masuat' => $maSuat,
            ':maghe' => $maGhe,
            ':mand' => $maNguoiDung
        ]
    );
}

function getMyTickets($maNguoiDung) {
    return fetchAll(
        "SELECT v.MAVE, p.TENPHIM, sc.THOIGIANBATDAU, sc.THOIGIANKETTHUC, 
                g.SOGHE, g.LOAIGHE, pc.TENPHONG, sc.GIAVE, v.TRANGTHAI
         FROM VE v
         JOIN SUATCHIEU sc ON v.MASUAT = sc.MASUAT
         JOIN PHIM p ON sc.MAPHIM = p.MAPHIM
         JOIN GHE g ON v.MAGHE = g.MAGHE
         JOIN PHONGCHIEU pc ON g.MAPHONG = pc.MAPHONG
         WHERE v.MANGUOIDUNG = :mand
         ORDER BY sc.THOIGIANBATDAU DESC",
        [':mand' => $maNguoiDung]
    );
}

function checkTicket($maVe) {
    $conn = connectOracle();
    
    // Kiểm tra vé
    $stmt = oci_parse($conn, "SELECT TRANGTHAI FROM VE WHERE MAVE = :mave");
    oci_bind_by_name($stmt, ":mave", $maVe);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    
    if (!$row) {
        oci_close($conn);
        return "❌ Mã vé không tồn tại.";
    }
    
    if ($row['TRANGTHAI'] === 'da_kiem_tra') {
        oci_close($conn);
        return "⚠️ Vé đã được kiểm tra trước đó.";
    }
    
    // Cập nhật trạng thái
    $update = oci_parse($conn, "UPDATE VE SET TRANGTHAI = 'da_kiem_tra' WHERE MAVE = :mave");
    oci_bind_by_name($update, ":mave", $maVe);
    $result = oci_execute($update);
    
    oci_close($conn);
    return $result ? "✅ Vé hợp lệ – Cho phép vào rạp." : "❌ Có lỗi xảy ra khi cập nhật vé.";
}

/** =========================
 * THỐNG KÊ
 * ========================= */

function getStats() {
    return fetchAll(
        "SELECT p.TENPHIM, COUNT(*) AS SOVE, SUM(sc.GIAVE) AS DOANHTHU
         FROM VE v
         JOIN SUATCHIEU sc ON v.MASUAT = sc.MASUAT
         JOIN PHIM p ON sc.MAPHIM = p.MAPHIM
         WHERE v.TRANGTHAI IN ('da_dat', 'da_kiem_tra')
         GROUP BY p.TENPHIM
         ORDER BY SOVE DESC"
    );
}

function getAdminStats() {
    $conn = connectOracle();
    
    $stats = [
        'total_movies' => fetchSingleValue("SELECT COUNT(*) FROM PHIM", $conn),
        'total_tickets' => fetchSingleValue(
            "SELECT COUNT(*) FROM VE WHERE TRANGTHAI IN ('da_dat', 'da_kiem_tra')", 
            $conn
        ),
        'monthly_revenue' => fetchSingleValue(
            "SELECT SUM(sc.GIAVE) FROM VE v 
             JOIN SUATCHIEU sc ON v.MASUAT = sc.MASUAT 
             WHERE EXTRACT(MONTH FROM v.THOIGIANDAT) = EXTRACT(MONTH FROM SYSDATE)
               AND EXTRACT(YEAR FROM v.THOIGIANDAT) = EXTRACT(YEAR FROM SYSDATE)
               AND v.TRANGTHAI IN ('da_dat', 'da_kiem_tra')", 
            $conn
        ) ?? 0,
        'total_users' => fetchSingleValue("SELECT COUNT(*) FROM NGUOIDUNG", $conn)
    ];
    
    oci_close($conn);
    return $stats;
}

function getRecentActivities($limit = 10) {
    return fetchAll(
        "SELECT v.THOIGIANDAT AS THOIGIAN, nd.HOTEN, 'Đặt vé' AS HOATDONG, p.TENPHIM AS CHITIET
         FROM VE v
         JOIN NGUOIDUNG nd ON v.MANGUOIDUNG = nd.MAND
         JOIN SUATCHIEU sc ON v.MASUAT = sc.MASUAT
         JOIN PHIM p ON sc.MAPHIM = p.MAPHIM
         ORDER BY v.THOIGIANDAT DESC
         FETCH FIRST :limit ROWS ONLY",
        [':limit' => $limit]
    );
}

/** =========================
 * HÀM HỖ TRỢ
 * ========================= */

function fetchAll($sql, $params = []) {
    $conn = connectOracle();
    $stmt = oci_parse($conn, $sql);
    
    foreach ($params as $key => $value) {
        oci_bind_by_name($stmt, $key, $value);
    }
    
    oci_execute($stmt);
    $results = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $results[] = $row;
    }
    
    oci_close($conn);
    return $results;
}

function fetchSingle($sql, $params = []) {
    $results = fetchAll($sql, $params);
    return $results[0] ?? null;
}

function fetchSingleValue($sql, $conn = null) {
    $shouldClose = ($conn === null);
    $conn = $conn ?: connectOracle();
    
    $stmt = oci_parse($conn, $sql);
    oci_execute($stmt);
    $row = oci_fetch_assoc($stmt);
    
    if ($shouldClose) {
        oci_close($conn);
    }
    
    return $row ? reset($row) : null;
}

function executeQuery($sql, $params = []) {
    $conn = connectOracle();
    $stmt = oci_parse($conn, $sql);
    
    foreach ($params as $key => $value) {
        oci_bind_by_name($stmt, $key, $value);
    }
    
    $result = oci_execute($stmt);
    if ($result) {
        oci_commit($conn);
    }
    oci_close($conn);
    return $result;
}

function getScheduleDetails($scheduleId) {
    $conn = connectOracle();
    $stmt = oci_parse($conn, 
        "SELECT sc.*, p.TENPHIM, pc.TENPHONG 
         FROM SUATCHIEU sc
         JOIN PHIM p ON sc.MAPHIM = p.MAPHIM
         JOIN PHONGCHIEU pc ON sc.MAPHONG = pc.MAPHONG
         WHERE sc.MASUAT = :masuat");
    oci_bind_by_name($stmt, ':masuat', $scheduleId);
    oci_execute($stmt);
    return oci_fetch_assoc($stmt);
}

function getSeatsForSchedule($scheduleId) {
    $conn = connectOracle();
    $stmt = oci_parse($conn,
        "SELECT g.*, 
                CASE WHEN v.MAVE IS NOT NULL THEN 'occupied' ELSE 'available' END AS TRANGTHAI
         FROM GHE g
         LEFT JOIN VE v ON g.MAGHE = v.MAGHE AND v.MASUAT = :masuat
         WHERE g.MAPHONG = (SELECT MAPHONG FROM SUATCHIEU WHERE MASUAT = :masuat)
         ORDER BY g.SOGHE");
    oci_bind_by_name($stmt, ':masuat', $scheduleId);
    oci_execute($stmt);
    
    $seats = [];
    while ($row = oci_fetch_assoc($stmt)) {
        $seats[] = $row;
    }
    return $seats;
}

function updateSeatStatus($seatId, $status, $scheduleId) {
    $conn = connectOracle();
    
    // Nếu đánh dấu là occupied, tạo vé mới
    if ($status === 'occupied') {
        $stmt = oci_parse($conn,
            "INSERT INTO VE (MAVE, MASUAT, MAGHE, THOIGIANDAT, TRANGTHAI)
             VALUES (:mave, :masuat, :maghe, SYSDATE, 'da_dat')");
        $maVe = uniqid("V");
        oci_bind_by_name($stmt, ':mave', $maVe);
        oci_bind_by_name($stmt, ':masuat', $scheduleId);
        oci_bind_by_name($stmt, ':maghe', $seatId);
        oci_execute($stmt);
    } 
    // Nếu chuyển từ occupied sang trạng thái khác, xóa vé
    else {
        $stmt = oci_parse($conn,
            "DELETE FROM VE WHERE MAGHE = :maghe AND MASUAT = :masuat");
        oci_bind_by_name($stmt, ':maghe', $seatId);
        oci_bind_by_name($stmt, ':masuat', $scheduleId);
        oci_execute($stmt);
    }
    
    return true;
}