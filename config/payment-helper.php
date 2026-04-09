<?php
/**
 * Helper Functions untuk Sistem Pembayaran dan Denda
 * File: config/payment-helper.php
 */

/**
 * Hitung denda keterlambatan berdasarkan jumlah hari dan harga sewa
 * 
 * @param string $tanggal_kembali_rencana Format: YYYY-MM-DD
 * @param int $harga_sewa Harga sewa harian
 * @param string $tanggal_kembali_aktual Format: YYYY-MM-DD (default: hari ini)
 * @return array ['hari_terlambat' => int, 'denda' => int]
 */
function hitungDendaTelat($tanggal_kembali_rencana, $harga_sewa, $tanggal_kembali_aktual = null) {
    if ($tanggal_kembali_aktual === null) {
        $tanggal_kembali_aktual = date('Y-m-d');
    }

    $date_rencana = new DateTime($tanggal_kembali_rencana);
    $date_aktual = new DateTime($tanggal_kembali_aktual);
    
    // Hitung selisih hari
    $interval = $date_aktual->diff($date_rencana);
    $hari_terlambat = $interval->days;
    
    // Jika terdapat keterlambatan (denda hanya untuk keterlambatan positif)
    if ($date_aktual > $date_rencana) {
        $denda = $hari_terlambat * $harga_sewa;
        return [
            'hari_terlambat' => $hari_terlambat,
            'denda' => $denda,
            'tipe' => 'Telat'
        ];
    }
    
    return [
        'hari_terlambat' => 0,
        'denda' => 0,
        'tipe' => 'Telat'
    ];
}

/**
 * Hitung denda apabila barang rusak (50% dari harga barang)
 * 
 * @param int $harga_barang Harga barang asli
 * @return int Jumlah denda
 */
function hitungDendaRusak($harga_barang) {
    return (int)($harga_barang * 0.5);
}

/**
 * Hitung total biaya peminjaman (sewa + denda jika ada)
 * 
 * @param int $id_peminjaman ID peminjaman
 * @param mysqli $conn Database connection
 * @return array ['biaya_sewa' => int, 'denda_rusak' => int, 'denda_telat' => int, 'total' => int]
 */
function hitungTotalBiayaPeminjaman($id_peminjaman, $conn) {
    // Ambil data peminjaman
    $query_pinjam = "SELECT p.tanggal_pinjam, p.tanggal_kembali_rencana, dp.id_alat, dp.jumlah 
                     FROM peminjaman p 
                     JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman 
                     WHERE p.id_peminjaman = '$id_peminjaman'";
    
    $result_pinjam = mysqli_query($conn, $query_pinjam);
    
    $biaya_sewa = 0;
    $denda_rusak = 0;
    $denda_telat = 0;
    
    // Hitung biaya sewa
    while ($row = mysqli_fetch_assoc($result_pinjam)) {
        $query_alat = "SELECT harga_sewa FROM alat WHERE id_alat = '{$row['id_alat']}'";
        $result_alat = mysqli_query($conn, $query_alat);
        $alat = mysqli_fetch_assoc($result_alat);
        
        // Hitung jumlah hari peminjaman
        $date_pinjam = new DateTime($row['tanggal_pinjam']);
        $date_kembali = new DateTime($row['tanggal_kembali_rencana']);
        $interval = $date_pinjam->diff($date_kembali);
        $hari_pinjam = $interval->days + 1; // +1 karena menghitung inklusif
        
        $biaya_sewa += ($alat['harga_sewa'] * $hari_pinjam * $row['jumlah']);
    }
    
    // Hitung denda jika ada pengembalian
    $query_pengembalian = "SELECT pk.id_pengembalian, pk.kondisi_kembali, p.tanggal_kembali_rencana, pk.tanggal_kembali, 
                           dp.id_alat, dp.jumlah
                           FROM pengembalian pk
                           JOIN peminjaman p ON pk.id_peminjaman = p.id_peminjaman
                           JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
                           WHERE pk.id_peminjaman = '$id_peminjaman'";
    
    $result_pengembalian = mysqli_query($conn, $query_pengembalian);
    
    if (mysqli_num_rows($result_pengembalian) > 0) {
        while ($row = mysqli_fetch_assoc($result_pengembalian)) {
            // Denda rusak
            if ($row['kondisi_kembali'] === 'Rusak') {
                $query_harga = "SELECT harga_barang FROM alat WHERE id_alat = '{$row['id_alat']}'";
                $result_harga = mysqli_query($conn, $query_harga);
                $harga = mysqli_fetch_assoc($result_harga);
                $denda_rusak += (hitungDendaRusak($harga['harga_barang']) * $row['jumlah']);
            }
            
            // Denda telat
            $denda_telat_item = hitungDendaTelat($row['tanggal_kembali_rencana'], 
                                                  $row['harga_sewa'] ?? 0, 
                                                  $row['tanggal_kembali']);
            $denda_telat += ($denda_telat_item['denda'] * $row['jumlah']);
        }
    }
    
    $total = $biaya_sewa + $denda_rusak + $denda_telat;
    
    return [
        'biaya_sewa' => $biaya_sewa,
        'denda_rusak' => $denda_rusak,
        'denda_telat' => $denda_telat,
        'total' => $total
    ];
}

/**
 * Format rupiah untuk ditampilkan
 * 
 * @param int $angka Angka yang akan diformat
 * @return string Hasil format dengan Rp.
 */
function formatRupiah($angka) {
    return "Rp. " . number_format($angka, 0, ',', '.');
}

/**
 * Buat record pembayaran baru
 * 
 * @param int $id_peminjaman ID peminjaman
 * @param int $id_user ID user
 * @param int $jumlah_pembayaran Jumlah pembayaran
 * @param string $catatan Catatan pembayaran
 * @param mysqli $conn Database connection
 * @return bool
 */
function buatPembayaran($id_peminjaman, $id_user, $jumlah_pembayaran, $catatan = '', $conn) {
    $catatan = mysqli_real_escape_string($conn, $catatan);
    
    $query = "INSERT INTO pembayaran (id_peminjaman, id_user, jumlah_pembayaran, catatan) 
              VALUES ('$id_peminjaman', '$id_user', '$jumlah_pembayaran', '$catatan')";
    
    return mysqli_query($conn, $query);
}

/**
 * Update status pembayaran jadi lunas
 * 
 * @param int $id_pembayaran ID pembayaran
 * @param mysqli $conn Database connection
 * @return bool
 */
function lunasPembayaran($id_pembayaran, $conn) {
    $query = "UPDATE pembayaran 
              SET status_pembayaran = 'Lunas', tanggal_pembayaran = NOW()
              WHERE id_pembayaran = '$id_pembayaran'";
    
    return mysqli_query($conn, $query);
}

/**
 * Catat denda (rusak atau telat)
 * 
 * @param int $id_peminjaman ID peminjaman
 * @param string $tipe_denda 'Rusak' atau 'Telat'
 * @param int $jumlah_denda Jumlah denda
 * @param string $keterangan Deskripsi denda
 * @param int|null $id_pengembalian ID pengembalian (opsional)
 * @param mysqli $conn Database connection
 * @return int ID denda yang baru dibuat atau -1 jika gagal
 */
function catatDenda($id_peminjaman, $tipe_denda, $jumlah_denda, $keterangan = '', $id_pengembalian = null, $conn) {
    $keterangan = mysqli_real_escape_string($conn, $keterangan);
    $id_pengembalian_sql = $id_pengembalian ? "'$id_pengembalian'" : "NULL";
    
    $query = "INSERT INTO beban_denda (id_peminjaman, id_pengembalian, tipe_denda, jumlah_denda, keterangan)
              VALUES ('$id_peminjaman', $id_pengembalian_sql, '$tipe_denda', '$jumlah_denda', '$keterangan')";
    
    if (mysqli_query($conn, $query)) {
        return mysqli_insert_id($conn);
    }
    
    return -1;
}

/**
 * Update status pembayaran denda menjadi lunas
 * 
 * @param int $id_denda ID denda
 * @param mysqli $conn Database connection
 * @return bool
 */
function lunasDenda($id_denda, $conn) {
    $query = "UPDATE beban_denda 
              SET status_pembayaran_denda = 'Lunas', tanggal_pembayaran_denda = NOW()
              WHERE id_denda = '$id_denda'";
    
    return mysqli_query($conn, $query);
}

/**
 * Ambil informasi pembayaran peminjaman
 * 
 * @param int $id_peminjaman ID peminjaman
 * @param mysqli $conn Database connection
 * @return array|null
 */
function infoPembayaran($id_peminjaman, $conn) {
    $query = "SELECT * FROM pembayaran WHERE id_peminjaman = '$id_peminjaman'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Ambil semua denda untuk peminjaman tertentu
 * 
 * @param int $id_peminjaman ID peminjaman
 * @param mysqli $conn Database connection
 * @return array
 */
function ambilDendaPeminjaman($id_peminjaman, $conn) {
    $query = "SELECT * FROM beban_denda WHERE id_peminjaman = '$id_peminjaman' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    
    $denda_list = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $denda_list[] = $row;
    }
    
    return $denda_list;
}

/**
 * Hitung total denda yang belum dibayar
 * 
 * @param int $id_peminjaman ID peminjaman
 * @param mysqli $conn Database connection
 * @return int
 */
function totalDendaBelumBayar($id_peminjaman, $conn) {
    $query = "SELECT SUM(jumlah_denda) as total FROM beban_denda 
              WHERE id_peminjaman = '$id_peminjaman' AND status_pembayaran_denda = 'Belum Dibayar'";
    
    $result = mysqli_query($conn, $query);
    $row = mysqli_fetch_assoc($result);
    
    return (int)($row['total'] ?? 0);
}
?>
