<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';

  // cek apakah form disubmit
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pengembalian.php");
    exit;
  }

  // ambil data
  $id_pengembalian = $_POST['id_pengembalian'] ?? '';
  $tanggal_kembali = $_POST['tanggal_kembali'] ?? '';
  $kondisi_kembali = $_POST['kondisi_kembali'] ?? 'Baik';
  $denda = $_POST['denda'] ?? 0;

  // validasi kosong
  if ($id_pengembalian === '' || $tanggal_kembali === '') {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // validasi kondisi
  $allowedKondisi = ['Baik', 'Rusak'];
  if (!in_array($kondisi_kembali, $allowedKondisi)) {
    $_SESSION['error'] = "Kondisi tidak valid!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // validasi denda adalah angka
  if (!is_numeric($denda) || $denda < 0) {
    $_SESSION['error'] = "Denda harus berupa angka positif!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // cek pengembalian ada atau tidak
  $cekPengembalian = mysqli_prepare($conn, "SELECT id_pengembalian FROM pengembalian WHERE id_pengembalian = ?");
  mysqli_stmt_bind_param($cekPengembalian, "i", $id_pengembalian);
  mysqli_stmt_execute($cekPengembalian);
  mysqli_stmt_store_result($cekPengembalian);

  if (mysqli_stmt_num_rows($cekPengembalian) === 0) {
    $_SESSION['error'] = "Pengembalian tidak ditemukan!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // update ke database
  $query = mysqli_prepare(
    $conn,
    "UPDATE pengembalian SET tanggal_kembali = ?, kondisi_kembali = ?, denda = ? WHERE id_pengembalian = ?"
  );
  mysqli_stmt_bind_param(
    $query,
    "ssii",
    $tanggal_kembali,
    $kondisi_kembali,
    $denda,
    $id_pengembalian
  );

  if (mysqli_stmt_execute($query)) {
    logAktivitas($conn, $_SESSION['id_user'], "Mengubah pengembalian", "pengembalian", $id_pengembalian);
    $_SESSION['success'] = "Pengembalian berhasil diperbarui!";
  } else {
    $_SESSION['error'] = "Gagal memperbarui pengembalian!";
  }

  header("Location: ../pengembalian.php");
  exit;
?>
