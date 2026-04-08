<?php
  session_start();
  include '../../../config/conn.php';

  if (!isset($_SESSION['id_user'])) {
      header('Location: ../pinjaman_user.php');
      exit;
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ../pinjaman_user.php');
      exit;
  }

  $id_user        = $_SESSION['id_user'];
  $id_peminjaman  = (int) $_POST['id_peminjaman'];
  $tanggal_kembali = $_POST['tanggal_kembali'];
  $kondisi_kembali = $_POST['kondisi_kembali'];

  /* validasi — pastikan peminjaman ini milik user yang login */
  $cek = mysqli_query($conn, "
    SELECT p.id_peminjaman, p.tanggal_kembali_rencana,
           dp.id_alat, dp.jumlah
    FROM peminjaman p
    JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
    WHERE p.id_peminjaman = '$id_peminjaman'
      AND p.id_user = '$id_user'
      AND p.status = 'Disetujui'
  ");

  if (mysqli_num_rows($cek) === 0) {
      header('Location: ../pinjaman_user.php?error=Peminjaman tidak ditemukan');
      exit;
  }

  $pinjaman = mysqli_fetch_assoc($cek);
  $id_alat  = $pinjaman['id_alat'];
  $jumlah   = $pinjaman['jumlah'];

  /* hitung denda jika terlambat — Rp5.000 per hari */
  $hari_terlambat = max(0, (int) ((strtotime($tanggal_kembali) - strtotime($pinjaman['tanggal_kembali_rencana'])) / 86400));
  $denda = $hari_terlambat * 5000;

  /* mulai transaksi */
  mysqli_begin_transaction($conn);

  try {
      /* insert ke tabel pengembalian */
      $sql_kembali = "INSERT INTO pengembalian (id_peminjaman, tanggal_kembali, kondisi_kembali, denda, created_at)
                      VALUES ('$id_peminjaman', '$tanggal_kembali', '$kondisi_kembali', '$denda', NOW())";

      if (!mysqli_query($conn, $sql_kembali)) {
          throw new Exception('Gagal mencatat pengembalian');
      }

      /* kembalikan stok alat */
      $sql_stok = "UPDATE alat SET stok = stok + '$jumlah' WHERE id_alat = '$id_alat'";

      if (!mysqli_query($conn, $sql_stok)) {
          throw new Exception('Gagal mengupdate stok');
      }

      mysqli_commit($conn);
      header('Location: ../pinjaman_user.php?success=1');
      exit;

  } catch (Exception $e) {
      mysqli_rollback($conn);
      header('Location: ../pinjaman_user.php?error=' . urlencode($e->getMessage()));
      exit;
  }
?>