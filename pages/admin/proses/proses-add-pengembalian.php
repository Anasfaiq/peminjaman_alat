<?php
  session_start();
  include '../../../config/conn.php';
  include '../../../config/logging.php';
  include '../../../config/payment-helper.php';

  // cek apakah form disubmit
  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pengembalian.php");
    exit;
  }

  // ambil data
  $id_peminjaman = $_POST['id_peminjaman'] ?? '';
  $tanggal_kembali = $_POST['tanggal_kembali'] ?? '';
  $kondisi_kembali = $_POST['kondisi_kembali'] ?? 'Baik';

  // validasi kosong
  if ($id_peminjaman === '' || $tanggal_kembali === '') {
    $_SESSION['error'] = "Semua field wajib diisi!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // validasi kondisi
  $allowedKondisi = ['Baik', 'Rusak'];
  if (!in_array($kondisi_kembali, $allowedKondisi)) {
    $_SESSION['error'] = "Kondisi tidak valid!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // cek peminjaman ada atau tidak
  $cekPeminjaman = mysqli_prepare($conn, "SELECT id_peminjaman, tanggal_kembali_rencana FROM peminjaman WHERE id_peminjaman = ?");
  mysqli_stmt_bind_param($cekPeminjaman, "i", $id_peminjaman);
  mysqli_stmt_execute($cekPeminjaman);
  $result_cek = mysqli_stmt_get_result($cekPeminjaman);

  if (mysqli_num_rows($result_cek) === 0) {
    $_SESSION['error'] = "Peminjaman tidak ditemukan!";
    header("Location: ../pengembalian.php");
    exit;
  }

  $row_cek = mysqli_fetch_assoc($result_cek);
  $tanggal_kembali_rencana = $row_cek['tanggal_kembali_rencana'];

  // cek pengembalian sudah ada atau belum
  $cekPengembalian = mysqli_prepare($conn, "SELECT id_pengembalian FROM pengembalian WHERE id_peminjaman = ?");
  mysqli_stmt_bind_param($cekPengembalian, "i", $id_peminjaman);
  mysqli_stmt_execute($cekPengembalian);
  mysqli_stmt_store_result($cekPengembalian);

  if (mysqli_stmt_num_rows($cekPengembalian) > 0) {
    $_SESSION['error'] = "Pengembalian untuk peminjaman ini sudah ada!";
    header("Location: ../pengembalian.php");
    exit;
  }

  // Ambil data detail peminjaman untuk menghitung denda
  $query_detail = "SELECT dp.id_alat, dp.jumlah, a.harga_sewa, a.harga_barang
                   FROM detail_peminjaman dp
                   JOIN alat a ON dp.id_alat = a.id_alat
                   WHERE dp.id_peminjaman = ?";
  
  $stmt_detail = mysqli_prepare($conn, $query_detail);
  mysqli_stmt_bind_param($stmt_detail, "i", $id_peminjaman);
  mysqli_stmt_execute($stmt_detail);
  $result_detail = mysqli_stmt_get_result($stmt_detail);

  if (mysqli_num_rows($result_detail) === 0) {
    $_SESSION['error'] = "Detail peminjaman tidak ditemukan!";
    header("Location: ../pengembalian.php");
    exit;
  }

  $detail = mysqli_fetch_assoc($result_detail);
  $id_alat = $detail['id_alat'];
  $jumlah = $detail['jumlah'];
  $harga_sewa = $detail['harga_sewa'];
  $harga_barang = $detail['harga_barang'];

  mysqli_begin_transaction($conn);

  try {
    // Insert ke tabel pengembalian
    $query = mysqli_prepare(
      $conn,
      "INSERT INTO pengembalian (id_peminjaman, tanggal_kembali, kondisi_kembali, denda, created_at) VALUES (?, ?, ?, ?, NOW())"
    );
    mysqli_stmt_bind_param($query, "issi", $id_peminjaman, $tanggal_kembali, $kondisi_kembali, $total_denda);

    // Hitung denda terlebih dahulu
    $total_denda = 0;

    // Denda rusak (jika kondisi rusak)
    $denda_rusak = 0;
    if ($kondisi_kembali == 'Rusak') {
      $denda_rusak = hitungDendaRusak($harga_barang) * $jumlah;
      $total_denda += $denda_rusak;
    }

    // Denda keterlambatan
    $denda_telat_result = hitungDendaTelat($tanggal_kembali_rencana, $harga_sewa, $tanggal_kembali);
    $denda_telat = 0;
    if ($denda_telat_result['hari_terlambat'] > 0) {
      $denda_telat = $denda_telat_result['denda'] * $jumlah;
      $total_denda += $denda_telat;
    }

    // Fix parameter binding
    $total_denda_final = $total_denda;
    mysqli_stmt_bind_param($query, "issi", $id_peminjaman, $tanggal_kembali, $kondisi_kembali, $total_denda_final);

    if (!mysqli_stmt_execute($query)) {
      throw new Exception("Gagal mencatat pengembalian: " . mysqli_error($conn));
    }

    $id_pengembalian = mysqli_insert_id($conn);

    // Catat denda rusak jika ada
    if ($denda_rusak > 0) {
      catatDenda($id_peminjaman, 'Rusak', $denda_rusak,
                 'Denda barang rusak: 50% dari harga barang (' . formatRupiah($harga_barang) . ')',
                 $id_pengembalian, $conn);
    }

    // Catat denda keterlambatan jika ada
    if ($denda_telat_result['hari_terlambat'] > 0) {
      catatDenda($id_peminjaman, 'Telat', $denda_telat,
                 'Denda keterlambatan: ' . $denda_telat_result['hari_terlambat'] . ' hari × ' . formatRupiah($harga_sewa) . '/hari',
                 $id_pengembalian, $conn);
    }

    // Kembalikan stok alat
    $query_stok = mysqli_prepare($conn, "UPDATE alat SET stok = stok + ? WHERE id_alat = ?");
    mysqli_stmt_bind_param($query_stok, "ii", $jumlah, $id_alat);
    
    if (!mysqli_stmt_execute($query_stok)) {
      throw new Exception("Gagal mengupdate stok");
    }

    mysqli_commit($conn);
    logAktivitas($conn, $_SESSION['id_user'], "Menambah pengembalian baru (Denda: " . formatRupiah($total_denda) . ")", "pengembalian", $id_pengembalian);
    $_SESSION['success'] = "Pengembalian berhasil dicatat! Denda: " . formatRupiah($total_denda);

  } catch (Exception $e) {
    mysqli_rollback($conn);
    $_SESSION['error'] = $e->getMessage();
  }

  header("Location: ../pengembalian.php");
  exit;
?>
