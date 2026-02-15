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
  $id_peminjaman = $_POST['id_peminjaman'] ?? '';
  $tanggal_kembali = $_POST['tanggal_kembali'] ?? '';
  $kondisi_kembali = $_POST['kondisi_kembali'] ?? 'Baik';
  $denda = $_POST['denda'] ?? 0;

  // validasi kosong
  if ($id_peminjaman === '' || $tanggal_kembali === '') {
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

  // cek peminjaman ada atau tidak
  $cekPeminjaman = mysqli_prepare($conn, "SELECT id_peminjaman FROM peminjaman WHERE id_peminjaman = ?");
  mysqli_stmt_bind_param($cekPeminjaman, "i", $id_peminjaman);
  mysqli_stmt_execute($cekPeminjaman);
  mysqli_stmt_store_result($cekPeminjaman);

  if (mysqli_stmt_num_rows($cekPeminjaman) === 0) {
    $_SESSION['error'] = "Peminjaman tidak ditemukan!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // cek pengembalian sudah ada atau belum
  $cekPengembalian = mysqli_prepare($conn, "SELECT id_pengembalian FROM pengembalian WHERE id_peminjaman = ?");
  mysqli_stmt_bind_param($cekPengembalian, "i", $id_peminjaman);
  mysqli_stmt_execute($cekPengembalian);
  mysqli_stmt_store_result($cekPengembalian);

  if (mysqli_stmt_num_rows($cekPengembalian) > 0) {
    $_SESSION['error'] = "Pengembalian untuk peminjaman ini sudah ada!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // simpan ke database
  $query = mysqli_prepare(
    $conn,
    "INSERT INTO pengembalian (id_peminjaman, tanggal_kembali, kondisi_kembali, denda) VALUES (?, ?, ?, ?)"
  );
  mysqli_stmt_bind_param(
    $query,
    "issi",
    $id_peminjaman,
    $tanggal_kembali,
    $kondisi_kembali,
    $denda
  );

  if (mysqli_stmt_execute($query)) {
    $id_pengembalian = mysqli_insert_id($conn);
    logAktivitas($conn, $_SESSION['id_user'], "Menambah pengembalian baru", "pengembalian", $id_pengembalian);
    $_SESSION['success'] = "Pengembalian berhasil dicatat!";
  } else {
    $_SESSION['error'] = "Gagal mencatat pengembalian!";
  }

  header("Location: ../pengembalian.php");
  exit;
?>
