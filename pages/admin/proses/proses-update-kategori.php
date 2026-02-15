<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';

  // cek apakah form disubmit
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../kategori.php");
    exit;
  }

  // ambil data
  $id_kategori = $_POST['id_kategori'] ?? '';
  $nama_kategori = trim($_POST['nama_kategori'] ?? '');
  $keterangan = trim($_POST['keterangan'] ?? '');

  // validasi kosong
  if ($id_kategori === '' || $nama_kategori === '') {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: ../kategori.php");
    exit;
  }

  // cek kategori ada atau tidak
  $cekKat = mysqli_prepare($conn, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
  mysqli_stmt_bind_param($cekKat, "i", $id_kategori);
  mysqli_stmt_execute($cekKat);
  mysqli_stmt_store_result($cekKat);

  if (mysqli_stmt_num_rows($cekKat) === 0) {
    $_SESSION['error'] = "Kategori tidak ditemukan!";
    header("Location: ../kategori.php");
    exit;
  }

  // update ke database
  $query = mysqli_prepare(
    $conn,
    "UPDATE kategori SET nama_kategori = ?, keterangan = ? WHERE id_kategori = ?"
  );
  mysqli_stmt_bind_param(
    $query,
    "ssi",
    $nama_kategori,
    $keterangan,
    $id_kategori
  );

  if (mysqli_stmt_execute($query)) {
    logAktivitas($conn, $_SESSION['id_user'], "Mengubah kategori: $nama_kategori", "kategori", $id_kategori);
    $_SESSION['success'] = "Kategori berhasil diperbarui!";
  } else {
    $_SESSION['error'] = "Gagal memperbarui kategori!";
  }

  header("Location: ../kategori.php");
  exit;
?>
