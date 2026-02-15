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

  // cek alat ada atau tidak
  $cekAlat = mysqli_prepare($conn, "SELECT id_alat FROM alat WHERE id_alat = ?");
  mysqli_stmt_bind_param($cekAlat, "i", $id);
  mysqli_stmt_execute($cekAlat);
  mysqli_stmt_store_result($cekAlat);

  if (mysqli_stmt_num_rows($cekAlat) === 0) {
    $_SESSION['error'] = "Alat tidak ditemukan!";
    header("Location: ../alat.php");
    exit;
  }

  $stmt = mysqli_prepare($conn, "DELETE FROM alat WHERE id_alat = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  
  if (mysqli_stmt_execute($stmt)) {
    logAktivitas($conn, $_SESSION['id_user'], "Menghapus alat", "alat", $id);
    $_SESSION['success'] = "Alat berhasil dihapus!";
  } else {
    $_SESSION['error'] = "Gagal menghapus alat!";
  }

  header("Location: ../alat.php");
  exit;
?>
