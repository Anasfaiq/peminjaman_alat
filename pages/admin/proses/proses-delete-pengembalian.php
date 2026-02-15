<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';

  if (!isset($_SESSION['id_user'])) {
    die("Belum login");
  }

  $id = $_GET['id'] ?? '';

  if ($id === '') {
    die("ID tidak valid");
  }

  // cek pengembalian ada atau tidak
  $cekPengembalian = mysqli_prepare($conn, "SELECT id_pengembalian FROM pengembalian WHERE id_pengembalian = ?");
  mysqli_stmt_bind_param($cekPengembalian, "i", $id);
  mysqli_stmt_execute($cekPengembalian);
  mysqli_stmt_store_result($cekPengembalian);

  if (mysqli_stmt_num_rows($cekPengembalian) === 0) {
    $_SESSION['error'] = "Pengembalian tidak ditemukan!";
    header("Location: ../pengembalian.php");
    exit;
  }

  $stmt = mysqli_prepare($conn, "DELETE FROM pengembalian WHERE id_pengembalian = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  
  if (mysqli_stmt_execute($stmt)) {
    logAktivitas($conn, $_SESSION['id_user'], "Menghapus pengembalian", "pengembalian", $id);
    $_SESSION['success'] = "Pengembalian berhasil dihapus!";
  } else {
    $_SESSION['error'] = "Gagal menghapus pengembalian!";
  }

  header("Location: ../pengembalian.php");
  exit;
?>
