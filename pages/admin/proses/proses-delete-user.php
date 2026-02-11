<?php
  session_start();
  include '../../../config/conn.php';

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
  mysqli_stmt_execute($stmt);

  header("Location: ../user.php");
  exit;
