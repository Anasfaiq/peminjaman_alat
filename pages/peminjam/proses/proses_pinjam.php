<?php
  session_start();
  include '../../../config/conn.php';

  if (!isset($_SESSION['id_user'])) {
      header('Location: ../peminjam/daftar_alat.php');
      exit;
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ../peminjam/daftar_alat.php');
      exit;
  }

  $id_user            = $_SESSION['id_user'];
  $id_alat            = (int) $_POST['id_alat'];
  $tanggal_pinjam     = $_POST['tanggal_pinjam'];
  $tanggal_kembali    = $_POST['tanggal_kembali_rencana'];
  $jumlah             = (int) $_POST['jumlah'];

  /* validasi input */
  if ($jumlah < 1 || empty($tanggal_pinjam) || empty($tanggal_kembali)) {
      header('Location: ../peminjam/daftar_alat.php?error=Input tidak valid');
      exit;
  }

  if ($tanggal_kembali <= $tanggal_pinjam) {
      header('Location: ../peminjam/daftar_alat.php?error=Tanggal kembali harus setelah tanggal pinjam');
      exit;
  }

  /* cek stok tersedia */
  $cek = mysqli_query($conn, "SELECT stok FROM alat WHERE id_alat='$id_alat'");
  $alat = mysqli_fetch_assoc($cek);

  if (!$alat || $alat['stok'] < $jumlah) {
      header('Location: ../peminjam/daftar_alat.php?error=Stok tidak mencukupi');
      exit;
  }

  /* mulai transaksi */
  mysqli_begin_transaction($conn);

  try {
      /* insert ke tabel peminjaman */
      $sql_pinjam = "INSERT INTO peminjaman (id_user, tanggal_pinjam, tanggal_kembali_rencana, status, created_at)
                     VALUES ('$id_user', '$tanggal_pinjam', '$tanggal_kembali', 'Menunggu', NOW())";

      if (!mysqli_query($conn, $sql_pinjam)) {
          throw new Exception('Gagal membuat peminjaman');
      }

      $id_peminjaman = mysqli_insert_id($conn);

      /* insert ke tabel detail_peminjaman */
      $sql_detail = "INSERT INTO detail_peminjaman (id_peminjaman, id_alat, jumlah)
                     VALUES ('$id_peminjaman', '$id_alat', '$jumlah')";

      if (!mysqli_query($conn, $sql_detail)) {
          throw new Exception('Gagal menyimpan detail peminjaman');
      }

      /* kurangi stok alat */
      $sql_stok = "UPDATE alat SET stok = stok - '$jumlah' WHERE id_alat='$id_alat'";

      if (!mysqli_query($conn, $sql_stok)) {
          throw new Exception('Gagal mengupdate stok');
      }

      mysqli_commit($conn);
      header('Location: ../peminjam/daftar_alat.php?success=1');
      exit;

  } catch (Exception $e) {
      mysqli_rollback($conn);
      header('Location: ../peminjam/daftar_alat.php?error=' . urlencode($e->getMessage()));
      exit;
  }
?>