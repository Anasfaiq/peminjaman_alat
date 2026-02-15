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

  // cegah hapus diri sendiri
  if ($id == $_SESSION['id_user']) {
    die("Tidak bisa menghapus akun sendiri");
  }

  $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id_user = ?");
  mysqli_stmt_bind_param($stmt, "i", $id);
  
  if (mysqli_stmt_execute($stmt)) {
    logAktivitas($conn, $_SESSION['id_user'], "Menghapus user", "users", $id);
    $_SESSION['success'] = "User berhasil dihapus!";
  } else {
    $_SESSION['error'] = "Gagal menghapus user!";
  }

  header("Location: ../user.php");
  exit;
