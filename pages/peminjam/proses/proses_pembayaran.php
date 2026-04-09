<?php
session_start();
include '../../../config/conn.php';
include '../../../config/payment-helper.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ../daftar_alat.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pinjaman_user.php');
    exit;
}

$id_peminjaman = (int)$_POST['id_peminjaman'];
$total_pembayaran = (int)$_POST['total_pembayaran'];
$catatan = isset($_POST['catatan']) ? $_POST['catatan'] : '';
$id_user = $_SESSION['id_user'];

// Validasi peminjaman milik user
$query_cek = "SELECT id_peminjaman FROM peminjaman WHERE id_peminjaman = '$id_peminjaman' AND id_user = '$id_user'";
$result_cek = mysqli_query($conn, $query_cek);

if (mysqli_num_rows($result_cek) == 0) {
    header('Location: ../pinjaman_user.php?error=Peminjaman tidak ditemukan');
    exit;
}

mysqli_begin_transaction($conn);

try {
    // Cek apakah pembayaran sewa sudah dilunasi
    $query_pembayaran = "SELECT * FROM pembayaran WHERE id_peminjaman = '$id_peminjaman'";
    $result_pembayaran = mysqli_query($conn, $query_pembayaran);
    $pembayaran = mysqli_fetch_assoc($result_pembayaran);

    if ($pembayaran && $pembayaran['status_pembayaran'] == 'Belum Dibayar') {
        // Lunasi pembayaran sewa
        $query_update = "UPDATE pembayaran 
                         SET status_pembayaran = 'Lunas', tanggal_pembayaran = NOW() 
                         WHERE id_pembayaran = '{$pembayaran['id_pembayaran']}'";
        
        if (!mysqli_query($conn, $query_update)) {
            throw new Exception('Gagal mengupdate status pembayaran sewa');
        }

        // Update status pembayaran di tabel peminjaman
        $query_update_pinjam = "UPDATE peminjaman 
                                SET status_pembayaran = 'Lunas' 
                                WHERE id_peminjaman = '$id_peminjaman'";
        
        if (!mysqli_query($conn, $query_update_pinjam)) {
            throw new Exception('Gagal mengupdate status peminjaman');
        }
    }

    // Lunasi semua denda yang belum dibayar
    $query_denda = "SELECT * FROM beban_denda 
                    WHERE id_peminjaman = '$id_peminjaman' AND status_pembayaran_denda = 'Belum Dibayar'";
    $result_denda = mysqli_query($conn, $query_denda);

    while ($denda = mysqli_fetch_assoc($result_denda)) {
        $query_update_denda = "UPDATE beban_denda 
                               SET status_pembayaran_denda = 'Lunas', tanggal_pembayaran_denda = NOW() 
                               WHERE id_denda = '{$denda['id_denda']}'";
        
        if (!mysqli_query($conn, $query_update_denda)) {
            throw new Exception('Gagal mengupdate status denda');
        }
    }

    // Catat aktivitas
    $catatan_escape = mysqli_real_escape_string($conn, $catatan);
    $query_log = "INSERT INTO log_aktivitas (id_user, aktivitas, tabel, id_referensi) 
                  VALUES ('$id_user', 'Melakukan pembayaran peminjaman sebesar " . formatRupiah($total_pembayaran) . "', 'peminjaman', '$id_peminjaman')";
    
    mysqli_query($conn, $query_log);

    mysqli_commit($conn);
    header('Location: ../pembayaran.php?id_peminjaman=' . $id_peminjaman . '&success=1');
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    header('Location: ../pembayaran.php?id_peminjaman=' . $id_peminjaman . '&error=' . urlencode($e->getMessage()));
    exit;
}
?>
