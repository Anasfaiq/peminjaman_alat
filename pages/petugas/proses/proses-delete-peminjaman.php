<?php
session_start();
include '../../../config/conn.php';
include '../../../config/logging.php';
include '../../../config/auth-check.php';

// Proteksi: hanya petugas yang bisa akses
checkAuth('petugas');

header('Content-Type: application/json');

if (!isset($_POST['id_peminjaman'])) {
    echo json_encode(['success' => false, 'message' => 'ID peminjaman tidak valid']);
    exit;
}

$id_peminjaman = intval($_POST['id_peminjaman']);
$id_user = $_SESSION['id_user'];

// Cek peminjaman ada atau tidak
$cekPeminjaman = $conn->prepare("SELECT id_peminjaman, status FROM peminjaman WHERE id_peminjaman = ?");
$cekPeminjaman->bind_param("i", $id_peminjaman);
$cekPeminjaman->execute();
$resultCek = $cekPeminjaman->get_result();

if ($resultCek->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Peminjaman tidak ditemukan']);
    exit;
}

$peminjamanData = $resultCek->fetch_assoc();

// Hanya bisa menghapus peminjaman yang ditolak
if ($peminjamanData['status'] !== 'Ditolak') {
    echo json_encode(['success' => false, 'message' => 'Hanya peminjaman yang ditolak yang bisa dihapus']);
    exit;
}

// Mulai transaksi
$conn->begin_transaction();

try {
    // Dapatkan detail peminjaman untuk mengembalikan stok
    $getDetail = $conn->prepare("SELECT id_alat, jumlah FROM detail_peminjaman WHERE id_peminjaman = ?");
    $getDetail->bind_param("i", $id_peminjaman);
    $getDetail->execute();
    $resultDetail = $getDetail->get_result();

    // Kembalikan stok alat
    while ($detail = $resultDetail->fetch_assoc()) {
        $updateStok = $conn->prepare("UPDATE alat SET stok = stok + ? WHERE id_alat = ?");
        $updateStok->bind_param("ii", $detail['jumlah'], $detail['id_alat']);
        
        if (!$updateStok->execute()) {
            throw new Exception("Gagal mengembalikan stok alat");
        }
    }

    // Hapus detail peminjaman
    $deleteDetail = $conn->prepare("DELETE FROM detail_peminjaman WHERE id_peminjaman = ?");
    $deleteDetail->bind_param("i", $id_peminjaman);
    
    if (!$deleteDetail->execute()) {
        throw new Exception("Gagal menghapus detail peminjaman");
    }

    // Hapus peminjaman
    $deletePeminjaman = $conn->prepare("DELETE FROM peminjaman WHERE id_peminjaman = ?");
    $deletePeminjaman->bind_param("i", $id_peminjaman);
    
    if (!$deletePeminjaman->execute()) {
        throw new Exception("Gagal menghapus peminjaman");
    }

    // Log aktivitas
    logAktivitas($conn, $id_user, "Menghapus peminjaman ID: $id_peminjaman dengan status Ditolak", 'peminjaman', $id_peminjaman);

    // Commit transaksi
    $conn->commit();

    echo json_encode(['success' => true, 'message' => 'Peminjaman berhasil dihapus']);
} catch (Exception $e) {
    // Rollback transaksi jika ada error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
