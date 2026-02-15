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
  $nama_kategori = trim($_POST['nama_kategori'] ?? '');
  $keterangan = trim($_POST['keterangan'] ?? '');

  // validasi kosong
  if ($nama_kategori === '') {
    $_SESSION['error'] = "Nama kategori wajib diisi!";
    header("Location: ../kategori.php");
    exit;
  }

  // cek kategori sudah ada atau belum
  $cek = mysqli_prepare($conn, "SELECT id_kategori FROM kategori WHERE nama_kategori = ?");
  mysqli_stmt_bind_param($cek, "s", $nama_kategori);
  mysqli_stmt_execute($cek);
  mysqli_stmt_store_result($cek);

  if (mysqli_stmt_num_rows($cek) > 0) {
    $_SESSION['error'] = "Kategori dengan nama ini sudah ada!";
    header("Location: ../kategori.php");
    exit;
  }

  // simpan ke database
  $query = mysqli_prepare(
    $conn,
    "INSERT INTO kategori (nama_kategori, keterangan) VALUES (?, ?)"
  );
  mysqli_stmt_bind_param(
    $query,
    "ss",
    $nama_kategori,
    $keterangan
  );

  if (mysqli_stmt_execute($query)) {
    $id_kategori = mysqli_insert_id($conn);
    logAktivitas($conn, $_SESSION['id_user'], "Menambah kategori baru: $nama_kategori", "kategori", $id_kategori);
    $_SESSION['success'] = "Kategori berhasil ditambahkan!";
  } else {
    $_SESSION['error'] = "Gagal menambahkan kategori!";
  }

  header("Location: ../kategori.php");
  exit;
?>
