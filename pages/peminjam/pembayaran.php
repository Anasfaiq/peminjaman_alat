<?php
session_start();
include '../../config/conn.php';
include '../../config/auth-check.php';
include '../../config/payment-helper.php';

checkAuth('peminjam');

if (!isset($_GET['id_peminjaman'])) {
    header('Location: ../pinjaman_user.php?error=ID peminjaman tidak ditemukan');
    exit;
}

$id_peminjaman = (int)$_GET['id_peminjaman'];
$id_user = $_SESSION['id_user'];

// Ambil data peminjaman
$query = "SELECT p.*, u.nama, u.username 
          FROM peminjaman p 
          JOIN users u ON p.id_user = u.id_user 
          WHERE p.id_peminjaman = '$id_peminjaman' AND p.id_user = '$id_user'";

$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header('Location: ../pinjaman_user.php?error=Peminjaman tidak ditemukan');
    exit;
}

$peminjaman = mysqli_fetch_assoc($result);

// Ambil detail peminjaman
$query_detail = "SELECT dp.*, a.nama_alat, a.harga_sewa 
                 FROM detail_peminjaman dp 
                 JOIN alat a ON dp.id_alat = a.id_alat 
                 WHERE dp.id_peminjaman = '$id_peminjaman'";

$result_detail = mysqli_query($conn, $query_detail);
$detail_peminjaman = [];
while ($row = mysqli_fetch_assoc($result_detail)) {
    $detail_peminjaman[] = $row;
}

// Ambil info pembayaran
$pembayaran = infoPembayaran($id_peminjaman, $conn);

// Ambil denda jika ada
$denda_list = ambilDendaPeminjaman($id_peminjaman, $conn);

// Hitung total yang harus dibayar
$total_pembayaran = $peminjaman['total_biaya'];
foreach ($denda_list as $denda) {
    if ($denda['status_pembayaran_denda'] == 'Belum Dibayar') {
        $total_pembayaran += $denda['jumlah_denda'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Peminjaman</title>
    <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="payment-body">
    <div class="payment-container">
        <!-- Header -->
        <div class="payment-header">
            <a href="pinjaman_user.php" class="payment-back-link">← Kembali ke Daftar Peminjaman</a>
            <h1 class="payment-title">Pembayaran Peminjaman</h1>
            <p class="payment-subtitle">ID Peminjaman: #<?php echo $id_peminjaman; ?></p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="payment-alert info">
                ✓ Pembayaran berhasil dicatat. Terima kasih telah membayar!
            </div>
        <?php endif; ?>

        <!-- Detail Peminjaman -->
        <div class="payment-card">
            <h3>Detail Peminjaman</h3>
            
            <?php foreach ($detail_peminjaman as $item): ?>
                <div class="payment-item">
                    <div>
                        <strong><?php echo $item['nama_alat']; ?></strong>
                        <div class="payment-item-detail">
                            Jumlah: <?php echo $item['jumlah']; ?> | Harga Sewa: <?php echo formatRupiah($item['harga_sewa']); ?>/hari
                        </div>
                    </div>
                    <div class="payment-text-right">
                        <?php 
                            $date_pinjam = new DateTime($peminjaman['tanggal_pinjam']);
                            $date_kembali = new DateTime($peminjaman['tanggal_kembali_rencana']);
                            $interval = $date_pinjam->diff($date_kembali);
                            $hari = $interval->days + 1;
                            $harga = $item['harga_sewa'] * $hari * $item['jumlah'];
                        ?>
                        <strong><?php echo formatRupiah($harga); ?></strong>
                        <div class="payment-text-muted">×<?php echo $hari; ?> hari</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Ringkasan Pembayaran -->
        <div class="payment-card">
            <h3>Ringkasan Pembayaran</h3>
            
            <div class="payment-item">
                <span>Biaya Sewa</span>
                <strong class="payment-biaya-sewa"><?php echo formatRupiah($peminjaman['total_biaya']); ?></strong>
            </div>

            <?php
            $total_denda_rusak = 0;
            $total_denda_telat = 0;
            $denda_rusak_bayar = 0;
            $denda_telat_bayar = 0;

            foreach ($denda_list as $denda):
                if ($denda['tipe_denda'] == 'Rusak') {
                    $total_denda_rusak += $denda['jumlah_denda'];
                    if ($denda['status_pembayaran_denda'] == 'Belum Dibayar') {
                        $denda_rusak_bayar += $denda['jumlah_denda'];
                    }
                } else {
                    $total_denda_telat += $denda['jumlah_denda'];
                    if ($denda['status_pembayaran_denda'] == 'Belum Dibayar') {
                        $denda_telat_bayar += $denda['jumlah_denda'];
                    }
                }
            endforeach;

            if ($denda_rusak_bayar > 0):
            ?>
                <div class="payment-item">
                    <span>Denda Barang Rusak</span>
                    <strong class="payment-denda-rusak"><?php echo formatRupiah($denda_rusak_bayar); ?></strong>
                </div>
            <?php endif; ?>

            <?php if ($denda_telat_bayar > 0): ?>
                <div class="payment-item">
                    <span>Denda Keterlambatan</span>
                    <strong class="payment-denda-telat"><?php echo formatRupiah($denda_telat_bayar); ?></strong>
                </div>
            <?php endif; ?>

            <div class="payment-item total">
                <span>Total Pembayaran</span>
                <strong><?php echo formatRupiah($total_pembayaran); ?></strong>
            </div>
        </div>

        <!-- Status Pembayaran -->
        <div class="payment-card">
            <h3 class="payment-title">Status Pembayaran</h3>
            
            <div class="payment-item">
                <span>Pembayaran Sewa</span>
                <span class="badge <?php echo ($peminjaman['status_pembayaran'] == 'Lunas') ? 'lunas' : 'belum'; ?>">
                    <?php echo ($peminjaman['status_pembayaran'] == 'Lunas') ? 'Lunas' : 'Belum Dibayar'; ?>
                </span>
            </div>

            <?php if ($denda_rusak_bayar > 0 || $denda_telat_bayar > 0): ?>
                <div class="payment-item">
                    <span>Denda</span>
                    <span class="badge belum">Belum Dibayar</span>
                </div>
            <?php endif; ?>

            <div class="payment-note">
                <p>
                    <strong>Catatan:</strong> Ini adalah sistem pembayaran sekolah. Pembayaran dicatat secara otomatis ketika Anda mengklik tombol "Bayar Sekarang".
                </p>
            </div>
        </div>

        <!-- Form Pembayaran -->
        <div class="payment-card">
            <h3 class="payment-title">Proses Pembayaran</h3>

            <?php if ($peminjaman['status_pembayaran'] == 'Belum Dibayar' || $denda_rusak_bayar > 0 || $denda_telat_bayar > 0): ?>
                <form action="proses/proses_pembayaran.php" method="POST">
                    <input type="hidden" name="id_peminjaman" value="<?php echo $id_peminjaman; ?>">
                    <input type="hidden" name="total_pembayaran" value="<?php echo $total_pembayaran; ?>">
                    
                    <div class="payment-form-group">
                        <label for="catatan" class="payment-form-label">Catatan (Opsional)</label>
                        <textarea name="catatan" id="catatan" class="payment-form-input" rows="3" placeholder="Tambahkan catatan pembayaran Anda..."></textarea>
                    </div>

                    <button type="submit" class="btn-bayar">
                        Bayar Sekarang - <?php echo formatRupiah($total_pembayaran); ?>
                    </button>
                </form>
            <?php else: ?>
                <p class="payment-success-text">
                    ✓ Semua pembayaran telah lunas!
                </p>
            <?php endif; ?>
        </div>

        <!-- Riwayat Denda (jika ada) -->
        <?php if (count($denda_list) > 0): ?>
            <div class="payment-card">
                <h3 class="payment-title">Riwayat Denda</h3>
                
                <table class="payment-table">
                    <thead>
                        <tr>
                            <th>Tipe</th>
                            <th>Keterangan</th>
                            <th class="text-right">Jumlah</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($denda_list as $denda): ?>
                            <tr>
                                <td>
                                    <span class="badge <?php echo ($denda['tipe_denda'] == 'Rusak') ? 'belum' : 'tetap'; ?>">
                                        <?php echo $denda['tipe_denda']; ?>
                                    </span>
                                </td>
                                <td><?php echo $denda['keterangan']; ?></td>
                                <td class="text-right">
                                    <strong><?php echo formatRupiah($denda['jumlah_denda']); ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?php echo ($denda['status_pembayaran_denda'] == 'Lunas') ? 'lunas' : 'belum'; ?>">
                                        <?php echo ($denda['status_pembayaran_denda'] == 'Lunas') ? 'Lunas' : 'Belum'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
