<?php
session_start();
include '../../../config/conn.php';
include '../../../config/payment-helper.php';
include '../../../config/logging.php';

if (!isset($_SESSION['id_user'])) {
  header('Location: ../daftar_alat.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../pinjaman_user.php');
  exit;
}

$id_peminjaman    = (int) $_POST['id_peminjaman'];
$total_pembayaran = (int) $_POST['total_pembayaran'];
$catatan          = isset($_POST['catatan']) ? trim($_POST['catatan']) : '';
$id_user          = $_SESSION['id_user'];

// Validasi: peminjaman milik user
$result_cek = mysqli_query($conn, "
  SELECT id_peminjaman FROM peminjaman 
  WHERE id_peminjaman = '$id_peminjaman' AND id_user = '$id_user'
");

if (mysqli_num_rows($result_cek) == 0) {
  header('Location: ../pinjaman_user.php?error=' . urlencode('Peminjaman tidak ditemukan'));
  exit;
}

// Validasi: pengembalian sudah diajukan dulu
// Pembayaran hanya bisa dilakukan setelah pengembalian dicatat,
// supaya denda telat sudah ter-generate dengan benar
$cek_kembali = mysqli_query($conn, "
  SELECT id_pengembalian FROM pengembalian WHERE id_peminjaman = '$id_peminjaman'
");
if (mysqli_num_rows($cek_kembali) == 0) {
  header('Location: ../pinjaman_user.php?error=' . urlencode('Ajukan pengembalian alat terlebih dahulu sebelum membayar'));
  exit;
}

mysqli_begin_transaction($conn);

try {
  // Lunasi pembayaran sewa
  $result_pembayaran = mysqli_query($conn, "
    SELECT * FROM pembayaran WHERE id_peminjaman = '$id_peminjaman'
  ");
  $pembayaran = mysqli_fetch_assoc($result_pembayaran);

  if ($pembayaran && $pembayaran['status_pembayaran'] == 'Belum Dibayar') {
    if (!mysqli_query($conn, "
      UPDATE pembayaran 
      SET status_pembayaran = 'Lunas', tanggal_pembayaran = NOW() 
      WHERE id_pembayaran = '{$pembayaran['id_pembayaran']}'
    ")) {
      throw new Exception('Gagal mengupdate status pembayaran sewa');
    }

    if (!mysqli_query($conn, "
      UPDATE peminjaman 
      SET status_pembayaran = 'Lunas' 
      WHERE id_peminjaman = '$id_peminjaman'
    ")) {
      throw new Exception('Gagal mengupdate status peminjaman');
    }
  }

  // Lunasi semua denda yang belum dibayar
  $result_denda = mysqli_query($conn, "
    SELECT * FROM beban_denda 
    WHERE id_peminjaman = '$id_peminjaman' 
      AND status_pembayaran_denda = 'Belum Dibayar'
  ");

  while ($denda = mysqli_fetch_assoc($result_denda)) {
    if (!mysqli_query($conn, "
      UPDATE beban_denda 
      SET status_pembayaran_denda = 'Lunas', tanggal_pembayaran_denda = NOW() 
      WHERE id_denda = '{$denda['id_denda']}'
    ")) {
      throw new Exception('Gagal mengupdate status denda');
    }
  }

  // Catat log aktivitas
  logAktivitas(
    $conn,
    $id_user,
    'Melakukan pembayaran sebesar ' . formatRupiah($total_pembayaran),
    'peminjaman',
    $id_peminjaman
  );

  mysqli_commit($conn);
  header('Location: ../pembayaran.php?id_peminjaman=' . $id_peminjaman . '&success=1');
  exit;

} catch (Exception $e) {
  mysqli_rollback($conn);
  header('Location: ../pembayaran.php?id_peminjaman=' . $id_peminjaman . '&error=' . urlencode($e->getMessage()));
  exit;
}
?>