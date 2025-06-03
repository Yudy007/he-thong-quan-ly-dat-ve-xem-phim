// functions.php

// Cho manage_schedules.php
function getSchedulesWithDetails() {
    // Truy vấn lấy suất chiếu kèm thông tin phim và phòng
    // SELECT s.*, p.TEN_PHIM, r.TEN_PHONG, r.SO_LUONG_GHE, 
    //        (r.SO_LUONG_GHE - COUNT(v.MA_VE)) AS GHE_TRONG
    // FROM SUATCHIEU s
    // JOIN PHIM p ON s.MA_PHIM = p.MA_PHIM
    // JOIN PHONGCHIEU r ON s.MA_PHONG = r.MA_PHONG
    // LEFT JOIN VE v ON s.MA_SUAT = v.MA_SUAT
    // GROUP BY s.MA_SUAT, s.MA_PHIM, s.MA_PHONG, ...
}

function getActiveMovies() {
    // SELECT * FROM PHIM WHERE TRANG_THAI = 'dang_chieu'
}

function getRooms() {
    // SELECT * FROM PHONGCHIEU
}

function insertSchedule($data) {
    // INSERT INTO SUATCHIEU (MA_PHIM, MA_PHONG, THOI_GIAN, GIA_VE)
    // VALUES (:ma_phim, :ma_phong, TO_DATE(:ngay_gio, 'YYYY-MM-DD HH24:MI'), :gia_ve)
}

// Cho manage_users.php
function getUsers() {
    // SELECT * FROM NGUOIDUNG ORDER BY VAI_TRO, MA_ND
}

function insertUser($data) {
    // INSERT INTO NGUOIDUNG (TEN_DANG_NHAP, MAT_KHAU, HO_TEN, VAI_TRO, EMAIL, SDT)
    // VALUES (:ten_dang_nhap, :mat_khau, :ho_ten, :vai_tro, :email, :sdt)
}

function updateUser($data) {
    // Cập nhật có hoặc không cập nhật mật khẩu
    if (isset($data['mat_khau'])) {
        // UPDATE NGUOIDUNG SET ... MAT_KHAU = :mat_khau ...
    } else {
        // UPDATE NGUOIDUNG SET ... (không cập nhật mật khẩu)
    }
}