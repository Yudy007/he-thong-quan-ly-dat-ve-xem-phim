function getTicketDetails($ticketCode) {
    return fetchSingle(
        "SELECT v.MAVE, p.TENPHIM, sc.THOIGIANBATDAU, sc.GIAVE, 
                pc.TENPHONG, g.SOGHE, g.LOAIGHE, v.TRANGTHAI,
                TO_CHAR(sc.THOIGIANBATDAU, 'DD/MM/YYYY HH24:MI') AS NGAYCHIEU_FORMATTED,
                nd.HOTEN AS TENKHACHHANG
         FROM VE v
         JOIN SUATCHIEU sc ON v.MASUAT = sc.MASUAT
         JOIN PHIM p ON sc.MAPHIM = p.MAPHIM
         JOIN GHE g ON v.MAGHE = g.MAGHE
         JOIN PHONGCHIEU pc ON g.MAPHONG = pc.MAPHONG
         JOIN NGUOIDUNG nd ON v.MANGUOIDUNG = nd.MAND
         WHERE v.MAVE = :mave",
        [':mave' => $ticketCode]
    );
}