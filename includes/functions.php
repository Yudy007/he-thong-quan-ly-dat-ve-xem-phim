// functions.php
function getStats() {
    // Truy vấn thống kê từ Oracle
    // Trả về mảng: total_movies, total_tickets, etc.
}

function getMovies() {
    // SELECT * FROM PHIM
}

function insertMovie($data) {
    // INSERT INTO PHIM (...) VALUES (...)
}

function getReport($type) {
    // Truy vấn báo cáo theo loại
    // Sử dụng GROUP BY, SUM() cho thống kê
}

function checkTicket($ticket_code) {
    // SELECT + UPDATE bảng VE
    // Trả về mảng thông tin vé hoặc lỗi
}

function getTodaySchedules() {
    // SELECT * FROM SUATCHIEU WHERE NGAY_CHIEU = SYSDATE
}

function getSeatStatus($schedule_id) {
    // SELECT trạng thái ghế cho suất chiếu
}

function updateSeatStatus($seat_id, $status) {
    // UPDATE bảng GHE SET TRANG_THAI = ...
}