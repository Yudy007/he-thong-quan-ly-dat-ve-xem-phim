<?php
function connectOracle() {
    $db_user = 'qlvexemphim';
    $db_pass = 'phim123';
    $db_host = 'localhost:1521/ORCL';
    
    $conn = oci_connect($db_user, $db_pass, $db_host, 'AL32UTF8'); // Thêm charset
    
    if (!$conn) {
        $e = oci_error();
        error_log("Oracle Connect Error: " . $e['message']); // Ghi log thay vì echo
        throw new Exception("Database connection failed"); // Ném exception để xử lý tập trung
    }
    
    return $conn;
}
?>