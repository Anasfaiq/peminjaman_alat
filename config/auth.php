<?php
session_start();
include 'conn.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $nama = $_POST['nama'];
  $id_user = $_POST['id_user'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $sql = "SELECT * FROM users WHERE username='$username'";
  $result = mysqli_query($conn, $sql);
  if (mysqli_num_rows($result) == 1) {
    $data = mysqli_fetch_assoc($result);
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['id_user'] = $data['id_user'];
    $_SESSION['username'] = $data['username'];
    $_SESSION['role'] = $data['role'];

    if (password_verify($password, $data['password'])) {
      $_SESSION['nama'] = $data['nama'];
      $_SESSION['id_user'] = $data['id_user'];
      $_SESSION['username'] = $data['username'];
      $_SESSION['role'] = $data['role'];

      if ($data['role'] == 'admin') {
        header("Location: ../pages/admin/dashboard.php");
      } else if ($data['role'] == 'petugas') {
        header("Location: ../pages/petugas/dashboard.php");
      } else if ($data['role'] == 'peminjam') {
        header('Location: ../pages/peminjam/dashboard.php');
      } else {
        header("Location: ../index.php");
      }
    }

    exit();

  } else {
    echo "Invalid username or password";
  }
}
