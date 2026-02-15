<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';

  // cek apakah form disubmit
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // ambil data
  $id_user = $_POST['id_user'] ?? '';
  $tanggal_pinjam = $_POST['tanggal_pinjam'] ?? '';
  $tanggal_kembali_rencana = $_POST['tanggal_kembali_rencana'] ?? '';
  $status = $_POST['status'] ?? 'Menunggu';
  $id_alat = $_POST['id_alat'] ?? '';
  $jumlah = $_POST['jumlah'] ?? '';

  // validasi kosong
  if ($id_user === '' || $tanggal_pinjam === '' || $tanggal_kembali_rencana === '' || $id_alat === '' || $jumlah === '') {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // validasi status
  $allowedStatus = ['Menunggu', 'Disetujui', 'Ditolak'];
  if (!in_array($status, $allowedStatus)) {
    $_SESSION['error'] = "Status tidak valid!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // validasi jumlah adalah angka
  if (!is_numeric($jumlah) || $jumlah <= 0) {
    $_SESSION['error'] = "Jumlah harus berupa angka positif!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // cek user ada atau tidak
  $cekUser = mysqli_prepare($conn, "SELECT id_user FROM users WHERE id_user = ?");
  mysqli_stmt_bind_param($cekUser, "i", $id_user);
  mysqli_stmt_execute($cekUser);
  mysqli_stmt_store_result($cekUser);

  if (mysqli_stmt_num_rows($cekUser) === 0) {
    $_SESSION['error'] = "User tidak ditemukan!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // cek alat ada atau tidak
  $cekAlat = mysqli_prepare($conn, "SELECT id_alat FROM alat WHERE id_alat = ?");
  mysqli_stmt_bind_param($cekAlat, "i", $id_alat);
  mysqli_stmt_execute($cekAlat);
  mysqli_stmt_store_result($cekAlat);

  if (mysqli_stmt_num_rows($cekAlat) === 0) {
    $_SESSION['error'] = "Alat tidak ditemukan!";
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // cek stok alat
  $getStok = mysqli_prepare($conn, "SELECT stok FROM alat WHERE id_alat = ?");
  mysqli_stmt_bind_param($getStok, "i", $id_alat);
  mysqli_stmt_execute($getStok);
  $result = mysqli_stmt_get_result($getStok);
  $row = mysqli_fetch_assoc($result);

  if ($row['stok'] < $jumlah) {
    $_SESSION['error'] = "Stok alat tidak cukup! Stok tersedia: " . $row['stok'];
    header("Location: ../data_peminjaman.php");
    exit;
  }

  // mulai transaksi
  mysqli_begin_transaction($conn);

  try {
    // simpan ke table peminjaman
    $queryPeminjaman = mysqli_prepare(
      $conn,
      "INSERT INTO peminjaman (id_user, tanggal_pinjam, tanggal_kembali_rencana, status) VALUES (?, ?, ?, ?)"
    );
    mysqli_stmt_bind_param(
      $queryPeminjaman,
      "isss",
      $id_user,
      $tanggal_pinjam,
      $tanggal_kembali_rencana,
      $status
    );
    
    if (!mysqli_stmt_execute($queryPeminjaman)) {
      throw new Exception("Gagal menambahkan peminjaman!");
    }

    // dapatkan id_peminjaman yang baru
    $id_peminjaman = mysqli_insert_id($conn);

    // simpan ke table detail_peminjaman
    $queryDetail = mysqli_prepare(
      $conn,
      "INSERT INTO detail_peminjaman (id_peminjaman, id_alat, jumlah) VALUES (?, ?, ?)"
    );
    mysqli_stmt_bind_param(
      $queryDetail,
      "iii",
      $id_peminjaman,
      $id_alat,
      $jumlah
    );
    
    if (!mysqli_stmt_execute($queryDetail)) {
      throw new Exception("Gagal menambahkan detail peminjaman!");
    }

    // kurangi stok alat jika status disetujui
    if ($status === 'Disetujui') {
      $updateStok = mysqli_prepare($conn, "UPDATE alat SET stok = stok - ? WHERE id_alat = ?");
      mysqli_stmt_bind_param($updateStok, "ii", $jumlah, $id_alat);
      
      if (!mysqli_stmt_execute($updateStok)) {
        throw new Exception("Gagal mengurangi stok alat!");
      }
    }

    // commit transaksi
    mysqli_commit($conn);
    logAktivitas($conn, $_SESSION['id_user'], "Menambah peminjaman baru", "peminjaman", $id_peminjaman);
    $_SESSION['success'] = "Peminjaman berhasil ditambahkan!";
  } catch (Exception $e) {
    // rollback transaksi
    mysqli_rollback($conn);
    $_SESSION['error'] = $e->getMessage();
  }

  header("Location: ../data_peminjaman.php");
  exit;
?>
