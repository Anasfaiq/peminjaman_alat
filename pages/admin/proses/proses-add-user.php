<?php
  session_start();
  include '../../../config/conn.php';

  // cek apakah form disubmit
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
  }

  // ambil data
  $nama     = trim($_POST['nama'] ?? '');
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $role     = $_POST['role'] ?? '';

  // validasi kosong
  if ($nama === '' || $username === '' || $password === '' || $role === '') {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: index.php");
    exit;
  }

  // validasi role
  $allowedRole = ['admin', 'petugas', 'peminjam'];
  if (!in_array($role, $allowedRole)) {
    $_SESSION['error'] = "Role tidak valid!";
    header("Location: index.php");
    exit;
  }

  // cek username sudah ada atau belum
  $cek = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username = ?");
  mysqli_stmt_bind_param($cek, "s", $username);
  mysqli_stmt_execute($cek);
  mysqli_stmt_store_result($cek);

  if (mysqli_stmt_num_rows($cek) > 0) {
    $_SESSION['error'] = "Username sudah digunakan!";
    header("Location: index.php");
    exit;
  }

  // hash password
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  // simpan ke database
  $query = mysqli_prepare(
    $conn,
    "INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)"
  );
  mysqli_stmt_bind_param(
    $query,
    "ssss",
    $nama,
    $username,
    $hashedPassword,
    $role
  );

  if (mysqli_stmt_execute($query)) {
    $_SESSION['success'] = "User berhasil ditambahkan!";
  } else {
    $_SESSION['error'] = "Gagal menambahkan user!";
  }

  header("Location: ../user.php");
  exit;
