<?php
function connectOracle() {
    $conn = oci_connect('qlvexemphim', 'phim123', '//localhost:1521/ORCL');
    if (!$conn) {
        $e = oci_error();
        echo "Oracle Connect Error: " . $e['message'];
        exit;
    }
    return $conn;
}
?>
