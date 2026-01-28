<?php
session_start();
include 'conn.php';
if (isset($_POST['register'])) {
  $nama = $_POST['nama'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $role = $_POST['role'];

  $hash = password_hash($password, PASSWORD_DEFAULT);
  $sql = "INSERT INTO users(nama, username, password, role) VALUES('$nama','$username', '$hash','$role')";
  mysqli_query($conn, $sql);
  
  header("Location: ../index.php");
  exit();
}