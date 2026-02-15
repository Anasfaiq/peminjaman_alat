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

  // cek kategori ada atau tidak
  $cekKat = mysqli_prepare($conn, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
  mysqli_stmt_bind_param($cekKat, "i", $id);
  mysqli_stmt_execute($cekKat);
  mysqli_stmt_store_result($cekKat);

  if (mysqli_stmt_num_rows($cekKat) === 0) {
    $_SESSION['error'] = "Kategori tidak ditemukan!";
    header("Location: ../kategori.php");
    exit;
  }

  // cek apakah kategori masih digunakan
  $cekAlat = mysqli_prepare($conn, "SELECT id_alat FROM alat WHERE id_kategori = ?");
  mysqli_stmt_bind_param($cekAlat, "i", $id);
  mysqli_stmt_execute($cekAlat);
  mysqli_stmt_store_result($cekAlat);

  if (mysqli_stmt_num_rows($cekAlat) > 0) {
    $_SESSION['error'] = "Kategori tidak bisa dihapus karena masih digunakan oleh alat!";
    header("Location: ../kategori.php");
    exit;
  }

  $stmt = mysqli_prepare($conn, "DELETE FROM kategori WHERE id_kategori = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  
  if (mysqli_stmt_execute($stmt)) {
    logAktivitas($conn, $_SESSION['id_user'], "Menghapus kategori", "kategori", $id);
    $_SESSION['success'] = "Kategori berhasil dihapus!";
  } else {
    $_SESSION['error'] = "Gagal menghapus kategori!";
  }

  header("Location: ../kategori.php");
  exit;
?>
