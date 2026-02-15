<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';

  if (!isset($_SESSION['id_user'])) {
    die("Belum login");
  }

  $id = $_GET['id'] ?? '';

  if ($id === '') {
    die("ID tidak valid");
  }

  // cek peminjaman ada atau tidak
  $cekPeminjaman = mysqli_prepare($conn, "SELECT id_peminjaman FROM peminjaman WHERE id_peminjaman = ?");
  mysqli_stmt_bind_param($cekPeminjaman, "i", $id);
  mysqli_stmt_execute($cekPeminjaman);
  mysqli_stmt_store_result($cekPeminjaman);

  if (mysqli_stmt_num_rows($cekPeminjaman) === 0) {
    $_SESSION['error'] = "Peminjaman tidak ditemukan!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // mulai transaksi untuk hapus peminjaman dan detailnya
  mysqli_begin_transaction($conn);

  try {
    // dapatkan detail peminjaman untuk mengembalikan stok
    $getDetail = mysqli_prepare($conn, "SELECT id_alat, jumlah FROM detail_peminjaman WHERE id_peminjaman = ?");
    mysqli_stmt_bind_param($getDetail, "i", $id);
    mysqli_stmt_execute($getDetail);
    $resultDetail = mysqli_stmt_get_result($getDetail);

    // kembalikan stok alat
    while ($detail = mysqli_fetch_assoc($resultDetail)) {
      $updateStok = mysqli_prepare($conn, "UPDATE alat SET stok = stok + ? WHERE id_alat = ?");
      mysqli_stmt_bind_param($updateStok, "ii", $detail['jumlah'], $detail['id_alat']);
      
      if (!mysqli_stmt_execute($updateStok)) {
        throw new Exception("Gagal mengembalikan stok alat!");
      }
    }

    // hapus detail peminjaman
    $deleteDetail = mysqli_prepare($conn, "DELETE FROM detail_peminjaman WHERE id_peminjaman = ?");
    mysqli_stmt_bind_param($deleteDetail, "i", $id);
    
    if (!mysqli_stmt_execute($deleteDetail)) {
      throw new Exception("Gagal menghapus detail peminjaman!");
    }

    // hapus peminjaman
    $stmt = mysqli_prepare($conn, "DELETE FROM peminjaman WHERE id_peminjaman = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (!mysqli_stmt_execute($stmt)) {
      throw new Exception("Gagal menghapus peminjaman!");
    }

    // commit transaksi
    mysqli_commit($conn);
    logAktivitas($conn, $_SESSION['id_user'], "Menghapus peminjaman", "peminjaman", $id);
    $_SESSION['success'] = "Peminjaman berhasil dihapus!";
  } catch (Exception $e) {
    // rollback transaksi
    mysqli_rollback($conn);
    $_SESSION['error'] = $e->getMessage();
  }

  header("Location: ../data_peminjaman.php");
  exit;
?>
