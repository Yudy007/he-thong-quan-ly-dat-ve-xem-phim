function getTicketDetails($ticketCode) {
    $conn = connectOracle();
    $stmt = oci_parse($conn,
        "SELECT v.MAVE, p.TENPHIM, sc.THOIGIANBATDAU, sc.GIAVE, 
                pc.TENPHONG, g.SOGHE, g.LOAIGHE
         FROM VE v
         JOIN SUATCHIEU sc ON v.MASUAT = sc.MASUAT
         JOIN PHIM p ON sc.MAPHIM = p.MAPHIM
         JOIN GHE g ON v.MAGHE = g.MAGHE
         JOIN PHONGCHIEU pc ON g.MAPHONG = pc.MAPHONG
         WHERE v.MAVE = :mave");
    oci_bind_by_name($stmt, ':mave', $ticketCode);
    oci_execute($stmt);
    return oci_fetch_assoc($stmt);
}