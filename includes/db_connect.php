<?php
function connectOracle() {
    $db_user = 'qlvexemphim';
    $db_pass = 'phim123';
    $db_host = 'localhost:1521/ORCL';
    
    $conn = oci_connect($db_user, $db_pass, $db_host, 'AL32UTF8');
    
    if (!$conn) {
        $e = oci_error();
        error_log("Oracle Connect Error: " . $e['message']);
        throw new Exception("Database connection failed");
    }
    
    return $conn;
}
?>