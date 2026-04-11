<?php
session_start();
include '../../../config/conn.php';
include '../../../config/payment-helper.php';
include '../../../config/logging.php';

if (!isset($_SESSION['id_user'])) {
  header('Location: ../pinjaman_user.php');
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../pinjaman_user.php');
  exit;
}

$id_user         = $_SESSION['id_user'];
$id_peminjaman   = (int) $_POST['id_peminjaman'];
$tanggal_kembali = $_POST['tanggal_kembali'];
$kondisi_kembali = $_POST['kondisi_kembali'];

// Validasi: peminjaman milik user & status Disetujui
$cek = mysqli_query($conn, "
  SELECT p.id_peminjaman, p.tanggal_kembali_rencana,
          dp.id_alat, dp.jumlah,
          a.harga_sewa, a.harga_barang, a.nama_alat
  FROM peminjaman p
  JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
  JOIN alat a ON dp.id_alat = a.id_alat
  WHERE p.id_peminjaman = '$id_peminjaman'
    AND p.id_user = '$id_user'
    AND p.status = 'Disetujui'
");

if (mysqli_num_rows($cek) === 0) {
  header('Location: ../pinjaman_user.php?error=' . urlencode('Peminjaman tidak ditemukan atau sudah tidak aktif'));
  exit;
}

// Validasi: belum pernah ajukan pengembalian
$cek_existing = mysqli_query($conn, "
  SELECT id_pengembalian FROM pengembalian WHERE id_peminjaman = '$id_peminjaman'
");
if (mysqli_num_rows($cek_existing) > 0) {
  header('Location: ../pinjaman_user.php?error=' . urlencode('Pengembalian sudah pernah diajukan'));
  exit;
}

// Kumpulkan semua item peminjaman
$items = [];
while ($row = mysqli_fetch_assoc($cek)) {
  $items[] = $row;
}

// Ambil tanggal_kembali_rencana dari item pertama (sama untuk semua item)
$tanggal_kembali_rencana = $items[0]['tanggal_kembali_rencana'];

// Mulai transaksi
mysqli_begin_transaction($conn);

try {
  // 1. Insert record pengembalian (kolom denda diisi 0, denda dicatat di beban_denda)
  $tanggal_kembali_esc = mysqli_real_escape_string($conn, $tanggal_kembali);
  $kondisi_kembali_esc = mysqli_real_escape_string($conn, $kondisi_kembali);

  $sql_kembali = "INSERT INTO pengembalian 
                      (id_peminjaman, tanggal_kembali, kondisi_kembali, denda, created_at)
                  VALUES 
                      ('$id_peminjaman', '$tanggal_kembali_esc', '$kondisi_kembali_esc', 0, NOW())";

  if (!mysqli_query($conn, $sql_kembali)) {
    throw new Exception('Gagal mencatat pengembalian');
  }

  $id_pengembalian = mysqli_insert_id($conn);

  // 2. Hitung & catat denda per item alat
  foreach ($items as $item) {

      // Denda Keterlambatan
      // hitungDendaTelat() pakai harga_sewa sebagai denda per hari per unit
      $hasil_telat = hitungDendaTelat(
          $tanggal_kembali_rencana,
          $item['harga_sewa'],
          $tanggal_kembali
      );

      if ($hasil_telat['denda'] > 0) {
          // Kalikan jumlah unit
          $jumlah_denda_telat = $hasil_telat['denda'] * $item['jumlah'];
          $ket_telat = "Terlambat {$hasil_telat['hari_terlambat']} hari"
                      . " × {$item['jumlah']} unit {$item['nama_alat']}"
                      . " (Rp " . number_format($item['harga_sewa'], 0, ',', '.') . "/hari/unit)";

          $id_denda = catatDenda(
              $id_peminjaman,
              'Telat',
              $jumlah_denda_telat,
              $ket_telat,
              $id_pengembalian,
              $conn
          );

          if ($id_denda < 0) {
              throw new Exception('Gagal mencatat denda keterlambatan');
          }
      }

      // Denda Kerusakan
      // Rusak Ringan = 25% harga barang, Rusak Berat = 50% harga barang
      if (in_array($kondisi_kembali, ['Rusak Ringan', 'Rusak Berat'])) {
          $persen    = ($kondisi_kembali === 'Rusak Berat') ? 0.5 : 0.25;
          $label_persen = ($kondisi_kembali === 'Rusak Berat') ? '50%' : '25%';

          $jumlah_denda_rusak = (int)($item['harga_barang'] * $persen * $item['jumlah']);
          $ket_rusak = "{$item['nama_alat']} dikembalikan kondisi {$kondisi_kembali}"
                      . " ({$label_persen} × {$item['jumlah']} unit)";

          $id_denda = catatDenda(
              $id_peminjaman,
              'Rusak',
              $jumlah_denda_rusak,
              $ket_rusak,
              $id_pengembalian,
              $conn
          );

          if ($id_denda < 0) {
              throw new Exception('Gagal mencatat denda kerusakan');
          }
      }

      // ── Kembalikan stok alat ─────────────────────────────────────────────
      $sql_stok = "UPDATE alat 
                    SET stok = stok + {$item['jumlah']} 
                    WHERE id_alat = '{$item['id_alat']}'";

      if (!mysqli_query($conn, $sql_stok)) {
          throw new Exception('Gagal mengupdate stok');
      }
  }

  // 3. Catat log aktivitas
  logAktivitas($conn, $id_user, 'Mengajukan pengembalian alat', 'pengembalian', $id_pengembalian);

  mysqli_commit($conn);

  // Redirect ke halaman pembayaran supaya user langsung bayar
  header('Location: ../pembayaran.php?id_peminjaman=' . $id_peminjaman . '&from=pengembalian');
  exit;

} catch (Exception $e) {
  mysqli_rollback($conn);
  header('Location: ../pinjaman_user.php?error=' . urlencode($e->getMessage()));
  exit;
}
?>