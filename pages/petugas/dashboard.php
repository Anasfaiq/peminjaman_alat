<?php
session_start();
include "../../config/conn.php";
include "../../config/auth-check.php";
include "../../config/logging.php";
include "../../config/payment-helper.php";

// Proteksi: hanya petugas yang bisa akses
checkAuth('petugas');

$id_user = $_SESSION['id_user'];
$user_data = getUserById($conn, $id_user);

// Hitung statistik untuk petugas
// Total peminjaman menunggu persetujuan
$pending_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status='Menunggu'");
$total_pending = mysqli_fetch_assoc($pending_query)['total'];

// Total peminjaman disetujui yang belum dikembalikan
$approved_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman p 
                                       WHERE p.status='Disetujui' 
                                       AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)");
$total_active = mysqli_fetch_assoc($approved_query)['total'];

// Total pengembalian hari ini
$today = date('Y-m-d');
$return_today_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengembalian 
                                           WHERE DATE(tanggal_kembali) = '$today'");
$total_return_today = mysqli_fetch_assoc($return_today_query)['total'];

// Total peminjaman yang ditolak
$rejected_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE status='Ditolak'");
$total_rejected = mysqli_fetch_assoc($rejected_query)['total'];

// Denda monitoring - Total belum bayar
$denda_belum_query = mysqli_query($conn, "SELECT SUM(jumlah_denda) as total FROM beban_denda WHERE status_pembayaran_denda='Belum Dibayar'");
$denda_belum_data = mysqli_fetch_assoc($denda_belum_query);
$total_denda_belum = $denda_belum_data['total'] ?? 0;

// Denda monitoring - Jumlah user dengan denda belum bayar
$denda_user_query = mysqli_query($conn, "SELECT COUNT(DISTINCT bd.id_peminjaman) as total 
                                          FROM beban_denda bd 
                                          WHERE bd.status_pembayaran_denda='Belum Dibayar'");
$denda_user_data = mysqli_fetch_assoc($denda_user_query);
$total_denda_users = $denda_user_data['total'] ?? 0;

// Data peminjaman menunggu (untuk quick view)
$pending_list_query = mysqli_query($conn, "SELECT p.id_peminjaman, u.nama, p.tanggal_pinjam, 
                                           p.tanggal_kembali_rencana, COUNT(d.id_detail) as jumlah_alat
                                           FROM peminjaman p
                                           JOIN users u ON p.id_user = u.id_user
                                           LEFT JOIN detail_peminjaman d ON p.id_peminjaman = d.id_peminjaman
                                           WHERE p.status='Menunggu'
                                           GROUP BY p.id_peminjaman
                                           ORDER BY p.created_at DESC
                                           LIMIT 5");

// Data denda - untuk display di monitoring section
$denda_monitoring_query = mysqli_query($conn, "SELECT bd.id_denda, bd.id_peminjaman, bd.tipe_denda, 
                                               bd.jumlah_denda, bd.status_pembayaran_denda, bd.keterangan,
                                               u.nama, u.username, p.tanggal_pinjam
                                               FROM beban_denda bd
                                               JOIN peminjaman p ON bd.id_peminjaman = p.id_peminjaman
                                               JOIN users u ON p.id_user = u.id_user
                                               WHERE bd.status_pembayaran_denda='Belum Dibayar'
                                               ORDER BY bd.created_at DESC
                                               LIMIT 10");
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | Dashboard Petugas</title>
    <link rel="stylesheet" href="../../src/output.css" />
  </head>
  <body class="flex gap-6 min-h-screen w-full py-8 px-14">
    <?php include '../components/sidebar_petugas.php' ?>

    <main class="right-dashboard-section">
      <nav class="navbar">
        <p>Dashboard Petugas</p>
        <div class="user-dropdown-wrapper">
          <button id="userBtn" class="user-dropdown-button">
            <div class="user-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
              </svg>
            </div>
            <div class="user-account">
              <p><?= htmlspecialchars($user_data['nama']) ?></p>
              <span>Petugas</span>
            </div>
            <svg
              class="dropdown-arrow"
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <path d="M6 9l6 6l6 -6" />
            </svg>
          </button>
          <div class="user-dropdown-menu hidden">
            <a href="../../config/logout.php" class="dropdown-item logout">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2"
                />
                <path d="M9 12h12l-3 -3" />
                <path d="M18 15l3 -3" />
              </svg>
              Logout
            </a>
          </div>
        </div>
      </nav>

      <div class="dashboard-content">
        <!-- Statistics Cards -->
        <div class="dashboard-cards-petugas">
          <div class="dashboard-card">
            <div class="card-text">
              <h3>Peminjaman Menunggu</h3>
              <p><?= $total_pending ?></p>
            </div>
            <div class="icon-wrapper icon-yellow">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h-2"></path><rect x="9" y="3" width="6" height="4" rx="2"></rect><path d="M9 12h6"></path><path d="M9 16h6"></path></svg>
            </div>
          </div>

          <div class="dashboard-card">
            <div class="card-text">
              <h3>Peminjaman Aktif</h3>
              <p><?= $total_active ?></p>
            </div>
            <div class="icon-wrapper icon-blue">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3L20 7.5v9.5L12 21l-8 -4.5v-9.5L12 3"></path><path d="M12 12L4 7.5"></path><path d="M12 12v9"></path><path d="M12 12L20 7.5"></path></svg>
            </div>
          </div>

          <div class="dashboard-card">
            <div class="card-text">
              <h3>Pengembalian Hari Ini</h3>
              <p><?= $total_return_today ?></p>
            </div>
            <div class="icon-wrapper icon-green">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 1 0 0 18a9 8 0 0 0 9 -8c0 -4.3 -2 -8 -5 -10"></path><path d="M12 9v6l4 2"></path></svg>
            </div>
          </div>

          <div class="dashboard-card">
            <div class="card-text">
              <h3>Peminjaman Ditolak</h3>
              <p><?= $total_rejected ?></p>
            </div>
            <div class="icon-wrapper icon-red">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M10 10l4 4m0 -4l-4 4"></path></svg>
            </div>
          </div>
        </div>

        <!-- Recent Pending Requests -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
          <h3 class="text-lg font-semibold mb-4">Peminjaman Menunggu Persetujuan</h3>
          
          <?php if($total_pending > 0): ?>
            <table class="crud-table w-full">
              <thead>
                <tr>
                  <th>ID Peminjaman</th>
                  <th>Peminjam</th>
                  <th>Tanggal Pinjam</th>
                  <th>Target Kembali</th>
                  <th>Jml Alat</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = mysqli_fetch_assoc($pending_list_query)): ?>
                  <tr>
                    <td><?= $row['id_peminjaman'] ?></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])) ?></td>
                    <td><?= $row['jumlah_alat'] ?> item</td>
                    <td>
                      <a href="persetujuan_peminjaman.php" class="btn-info">Lihat Detail</a>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div class="py-8 text-center text-gray-500">
              <p>Tidak ada peminjaman yang menunggu persetujuan</p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Monitoring Denda Section -->
        <div class="bg-white rounded-lg p-6 shadow-sm">
          <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
            Monitoring Denda Peminjaman
            <?php if ($total_denda_belum > 0): ?>
              <span class="badge badge-late">Belum Dibayar: <?= $total_denda_users ?></span>
            <?php endif; ?>
          </h3>
          
          <?php if($total_denda_belum > 0): ?>
            <div class="mb-4 p-4 bg-red-50 rounded-lg border border-red-200">
              <p class="text-sm text-red-700">
                <strong>Total Denda Belum Dibayar:</strong> <?= formatRupiah($total_denda_belum) ?> 
                <span class="text-gray-600">(<?= $total_denda_users ?> peminjaman)</span>
              </p>
            </div>

            <table class="crud-table w-full text-sm">
              <thead>
                <tr>
                  <th>Peminjam</th>
                  <th>Tipe Denda</th>
                  <th>Keterangan</th>
                  <th>Jumlah</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = mysqli_fetch_assoc($denda_monitoring_query)): ?>
                  <tr>
                    <td>
                      <div>
                        <strong><?= htmlspecialchars($row['nama']) ?></strong>
                        <br>
                        <small class="text-gray-500">@<?= htmlspecialchars($row['username']) ?></small>
                      </div>
                    </td>
                    <td>
                      <span class="badge <?= ($row['tipe_denda'] == 'Rusak') ? 'badge-late' : 'badge-ok'; ?>">
                        <?= htmlspecialchars($row['tipe_denda']) ?>
                      </span>
                    </td>
                    <td>
                      <small><?= htmlspecialchars(substr($row['keterangan'], 0, 40)); ?><?= (strlen($row['keterangan']) > 40) ? '...' : ''; ?></small>
                    </td>
                    <td>
                      <strong><?= formatRupiah($row['jumlah_denda']) ?></strong>
                    </td>
                    <td>
                      <span class="badge badge-late"><?= htmlspecialchars($row['status_pembayaran_denda']) ?></span>
                    </td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>

            <div class="mt-4 pt-4 border-t border-gray-200">
              <p class="text-xs text-gray-500">Catatan: Status denda akan otomatis berubah menjadi "Lunas" setelah peminjam menyelesaikan pembayaran di halaman pembayaran mereka.</p>
            </div>
          <?php else: ?>
            <div class="py-8 text-center text-gray-500">
              <p>✓ Semua denda telah dibayar!</p>
            </div>
          <?php endif; ?>
        </div>
  </body>

  <script src="script.js"></script>
</html>
