<?php
session_start();
include '../../../config/conn.php';

$id = $_POST['id_user'];
$nama = trim($_POST['nama']);
$username = trim($_POST['username']);
$password = $_POST['password'];
$role = $_POST['role'];

$allowed = ['admin','petugas','peminjam'];
if (!in_array($role, $allowed)) {
  die("Role tidak valid");
}

if ($password !== '') {
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $sql = "UPDATE users SET nama=?, username=?, password=?, role=? WHERE id_user=?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "ssssi", $nama, $username, $hash, $role, $id);
} else {
  $sql = "UPDATE users SET nama=?, username=?, role=? WHERE id_user=?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "sssi", $nama, $username, $role, $id);
}

mysqli_stmt_execute($stmt);
header("Location: ../user.php");
exit;
