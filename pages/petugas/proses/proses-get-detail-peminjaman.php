<?php
session_start();
include "../../../config/conn.php";
include "../../../config/auth-check.php";

// Proteksi
checkAuth('petugas');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode([]);
    exit();
}

$id_peminjaman = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_peminjaman) {
    echo json_encode([]);
    exit();
}

// Get detail alat dari peminjaman
$query = $conn->prepare("
    SELECT 
        d.id_detail,
        d.jumlah,
        a.id_alat,
        a.nama_alat
    FROM detail_peminjaman d
    JOIN alat a ON d.id_alat = a.id_alat
    WHERE d.id_peminjaman = ?
");

$query->bind_param("i", $id_peminjaman);
$query->execute();
$result = $query->get_result();

$detail_list = [];
while ($row = $result->fetch_assoc()) {
    $detail_list[] = $row;
}

echo json_encode($detail_list);
?>
