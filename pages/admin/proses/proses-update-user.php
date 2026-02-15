<?php
session_start();
include '../../../config/conn.php';
include '../../../config/logging.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header("Location: ../user.php");
  exit;
}

$id_user = $_POST['id_user'] ?? '';
$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';

// validasi kosong
if ($id_user === '' || $nama === '' || $username === '' || $role === '') {
  $_SESSION['error'] = "Semua field wajib diisi!";
  header("Location: ../user.php");
  exit;
}

// validasi role
$allowedRole = ['admin', 'petugas', 'peminjam'];
if (!in_array($role, $allowedRole)) {
  $_SESSION['error'] = "Role tidak valid!";
  header("Location: ../user.php");
  exit;
}

// cek user ada atau tidak
$cekUser = mysqli_prepare($conn, "SELECT id_user FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($cekUser, "i", $id_user);
mysqli_stmt_execute($cekUser);
mysqli_stmt_store_result($cekUser);

if (mysqli_stmt_num_rows($cekUser) === 0) {
  $_SESSION['error'] = "User tidak ditemukan!";
  header("Location: ../user.php");
  exit;
}

if ($password !== '') {
  $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
  $sql = "UPDATE users SET nama=?, username=?, password=?, role=? WHERE id_user=?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "ssssi", $nama, $username, $hashedPassword, $role, $id_user);
} else {
  $sql = "UPDATE users SET nama=?, username=?, role=? WHERE id_user=?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "sssi", $nama, $username, $role, $id_user);
}

if (mysqli_stmt_execute($stmt)) {
  logAktivitas($conn, $_SESSION['id_user'], "Mengubah user: $nama ($role)", "users", $id_user);
  $_SESSION['success'] = "User berhasil diperbarui!";
} else {
  $_SESSION['error'] = "Gagal memperbarui user!";
}

header("Location: ../user.php");
exit;
?>
