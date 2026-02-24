<?php
session_start();
include "../../../config/conn.php";
include "../../../config/auth-check.php";
include "../../../config/logging.php";

// Proteksi
checkAuth('petugas');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

$id_peminjaman = isset($_POST['id_peminjaman']) ? (int)$_POST['id_peminjaman'] : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';
$id_user = $_SESSION['id_user'];

// Validasi
if (!$id_peminjaman || empty($status)) {
    echo json_encode(['success' => false, 'message' => 'Parameter tidak lengkap']);
    exit();
}

$valid_statuses = ['Disetujui', 'Ditolak'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
    exit();
}

// Cek peminjaman ada atau tidak dan statusnya masih Menunggu
$check_query = $conn->prepare("SELECT id_peminjaman, status FROM peminjaman WHERE id_peminjaman = ?");
$check_query->bind_param("i", $id_peminjaman);
$check_query->execute();
$check_result = $check_query->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Peminjaman tidak ditemukan']);
    exit();
}

$peminjaman = $check_result->fetch_assoc();
if ($peminjaman['status'] !== 'Menunggu') {
    echo json_encode(['success' => false, 'message' => 'Status peminjaman sudah diubah sebelumnya']);
    exit();
}

// Update status
$update_query = $conn->prepare("UPDATE peminjaman SET status = ? WHERE id_peminjaman = ?");
$update_query->bind_param("si", $status, $id_peminjaman);

if ($update_query->execute()) {
    // Log aktivitas
    $aktivitas = "Mengubah status peminjaman menjadi $status";
    logAktivitas($conn, $id_user, $aktivitas, 'peminjaman', $id_peminjaman);
    
    echo json_encode([
        'success' => true, 
        'message' => "Peminjaman berhasil " . ($status === 'Disetujui' ? 'disetujui' : 'ditolak')
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status']);
}
?>
