<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';

  // cek apakah form disubmit
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // ambil data
  $id_peminjaman = $_POST['id_peminjaman'] ?? '';
  $status = $_POST['status'] ?? 'Menunggu';

  // validasi kosong
  if ($id_peminjaman === '') {
    $_SESSION['error'] = "ID peminjaman tidak valid!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // validasi status
  $allowedStatus = ['Menunggu', 'Disetujui', 'Ditolak'];
  if (!in_array($status, $allowedStatus)) {
    $_SESSION['error'] = "Status tidak valid!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // cek peminjaman ada atau tidak
  $cekPeminjaman = mysqli_prepare($conn, "SELECT id_peminjaman FROM peminjaman WHERE id_peminjaman = ?");
  mysqli_stmt_bind_param($cekPeminjaman, "i", $id_peminjaman);
  mysqli_stmt_execute($cekPeminjaman);
  mysqli_stmt_store_result($cekPeminjaman);

  if (mysqli_stmt_num_rows($cekPeminjaman) === 0) {
    $_SESSION['error'] = "Peminjaman tidak ditemukan!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // update status peminjaman
  $query = mysqli_prepare(
    $conn,
    "UPDATE peminjaman SET status = ? WHERE id_peminjaman = ?"
  );
  mysqli_stmt_bind_param(
    $query,
    "si",
    $status,
    $id_peminjaman
  );

  if (mysqli_stmt_execute($query)) {
    logAktivitas($conn, $_SESSION['id_user'], "Mengubah status peminjaman", "peminjaman", $id_peminjaman);
    $_SESSION['success'] = "Peminjaman berhasil diperbarui!";
  } else {
    $_SESSION['error'] = "Gagal memperbarui peminjaman!";
  }

  header("Location: ../data_peminjaman.php");
  exit;
?>
