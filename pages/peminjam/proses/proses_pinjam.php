<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/payment-helper.php';

  if (!isset($_SESSION['id_user'])) {
      header('Location: ../daftar_alat.php');
      exit;
  }

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
      header('Location: ../daftar_alat.php');
      exit;
  }

  $id_user            = $_SESSION['id_user'];
  $id_alat            = (int) $_POST['id_alat'];
  $tanggal_pinjam     = $_POST['tanggal_pinjam'];
  $tanggal_kembali    = $_POST['tanggal_kembali_rencana'];
  $jumlah             = (int) $_POST['jumlah'];

  /* validasi input */
  if ($jumlah < 1 || empty($tanggal_pinjam) || empty($tanggal_kembali)) {
      header('Location: ../daftar_alat.php?error=Input tidak valid');
      exit;
  }

  if ($tanggal_kembali <= $tanggal_pinjam) {
      header('Location: ../daftar_alat.php?error=Tanggal kembali harus setelah tanggal pinjam');
      exit;
  }

  /* cek stok tersedia */
  $cek = mysqli_query($conn, "SELECT stok, harga_sewa FROM alat WHERE id_alat='$id_alat'");
  $alat = mysqli_fetch_assoc($cek);

  if (!$alat || $alat['stok'] < $jumlah) {
      header('Location: ../daftar_alat.php?error=Stok tidak mencukupi');
      exit;
  }

  /* mulai transaksi */
  mysqli_begin_transaction($conn);

  try {
      /* hitung biaya sewa */
      $date_pinjam = new DateTime($tanggal_pinjam);
      $date_kembali = new DateTime($tanggal_kembali);
      $interval = $date_pinjam->diff($date_kembali);
      $hari_pinjam = $interval->days + 1; // +1 karena menghitung inklusif
      
      $total_biaya = ($alat['harga_sewa'] * $hari_pinjam * $jumlah);

      /* insert ke tabel peminjaman */
      $sql_pinjam = "INSERT INTO peminjaman (id_user, tanggal_pinjam, tanggal_kembali_rencana, status, total_biaya, status_pembayaran, created_at)
                     VALUES ('$id_user', '$tanggal_pinjam', '$tanggal_kembali', 'Menunggu', '$total_biaya', 'Belum Dibayar', NOW())";

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

      /* buat record pembayaran */
      if (!buatPembayaran($id_peminjaman, $id_user, $total_biaya, 'Biaya sewa peminjaman alat', $conn)) {
          throw new Exception('Gagal membuat record pembayaran');
      }

      /* kurangi stok alat */
      $sql_stok = "UPDATE alat SET stok = stok - '$jumlah' WHERE id_alat='$id_alat'";

      if (!mysqli_query($conn, $sql_stok)) {
          throw new Exception('Gagal mengupdate stok');
      }

      mysqli_commit($conn);
      header('Location: ../pinjaman_user.php?success=1');
      exit;

  } catch (Exception $e) {
      mysqli_rollback($conn);
      header('Location: ../daftar_alat.php?error=' . urlencode($e->getMessage()));
      exit;
  }
?>