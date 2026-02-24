<?php
session_start();
include "../../config/conn.php";
include "../../config/auth-check.php";
include "../../config/logging.php";

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
        <div class="dashboard-cards">
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

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg p-6 shadow-sm flex gap-4 flex-wrap">
          <a href="persetujuan_peminjaman.php" class="quick-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Persetujuan Peminjaman
          </a>
          <a href="monitoring_pengembalian.php" class="quick-link-purple">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 1 0 0 18a9 8 0 0 0 9 -8c0 -4.3 -2 -8 -5 -10"></path><path d="M12 9v6l4 2"></path></svg> Monitoring Pengembalian
          </a>
          <a href="laporan.php" class="quick-link-cyan">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h -2"></path><rect x="9" y="3" width="6" height="4" rx="2"></rect><path d="M9 12h6"></path><path d="M9 16h6"></path></svg> Laporan
          </a>
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
      </div>
    </main>
  </body>

  <script src="../../script.js"></script>
</html>
