<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';

  // cek apakah form disubmit
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../alat.php");
    exit;
  }

  // ambil data
  $nama_alat = trim($_POST['nama_alat'] ?? '');
  $id_kategori = $_POST['id_kategori'] ?? '';
  $kondisi = $_POST['kondisi'] ?? 'Baik';
  $stok = $_POST['stok'] ?? '';

  // validasi kosong
  if ($nama_alat === '' || $id_kategori === '' || $stok === '') {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: ../alat.php");
    exit;
  }

  // validasi kondisi
  $allowedKondisi = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
  if (!in_array($kondisi, $allowedKondisi)) {
    $_SESSION['error'] = "Kondisi tidak valid!";
    header("Location: ../alat.php");
    exit;
  }

  // validasi stok adalah angka
  if (!is_numeric($stok) || $stok < 0) {
    $_SESSION['error'] = "Stok harus berupa angka positif!";
    header("Location: ../alat.php");
    exit;
  }

  // cek kategori ada atau tidak
  $cekKat = mysqli_prepare($conn, "SELECT id_kategori FROM kategori WHERE id_kategori = ?");
  mysqli_stmt_bind_param($cekKat, "i", $id_kategori);
  mysqli_stmt_execute($cekKat);
  mysqli_stmt_store_result($cekKat);

  if (mysqli_stmt_num_rows($cekKat) === 0) {
    $_SESSION['error'] = "Kategori tidak ditemukan!";
    header("Location: ../alat.php");
    exit;
  }

  // simpan ke database
  $query = mysqli_prepare(
    $conn,
    "INSERT INTO alat (nama_alat, id_kategori, kondisi, stok) VALUES (?, ?, ?, ?)"
  );
  mysqli_stmt_bind_param(
    $query,
    "sisi",
    $nama_alat,
    $id_kategori,
    $kondisi,
    $stok
  );

  if (mysqli_stmt_execute($query)) {
    $id_alat = mysqli_insert_id($conn);
    logAktivitas($conn, $_SESSION['id_user'], "Menambah alat baru: $nama_alat", "alat", $id_alat);
    $_SESSION['success'] = "Alat berhasil ditambahkan!";
  } else {
    $_SESSION['error'] = "Gagal menambahkan alat!";
  }

  header("Location: ../alat.php");
  exit;
?>
