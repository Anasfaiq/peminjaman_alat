<?php
session_start();
include "../../config/conn.php";
include "../../config/auth-check.php";
include "../../config/logging.php";

// Proteksi: hanya petugas yang bisa akses
checkAuth('petugas');

$id_user = $_SESSION['id_user'];
$user_data = getUserById($conn, $id_user);

// Get filter dari URL
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Query monitoring pengembalian
if($filter_status === 'all') {
    $query = "SELECT 
        pg.id_pengembalian,
        pg.id_peminjaman,
        pg.tanggal_kembali,
        pg.kondisi_kembali,
        pg.denda,
        u.nama,
        p.tanggal_pinjam,
        p.tanggal_kembali_rencana
        FROM pengembalian pg
        JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman
        JOIN users u ON p.id_user = u.id_user
        ORDER BY pg.tanggal_kembali DESC";
    $result = $conn->query($query);
} elseif($filter_status === 'terlambat') {
    $query = "SELECT 
        pg.id_pengembalian,
        pg.id_peminjaman,
        pg.tanggal_kembali,
        pg.kondisi_kembali,
        pg.denda,
        u.nama,
        p.tanggal_pinjam,
        p.tanggal_kembali_rencana
        FROM pengembalian pg
        JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman
        JOIN users u ON p.id_user = u.id_user
        WHERE pg.tanggal_kembali > p.tanggal_kembali_rencana
        ORDER BY pg.tanggal_kembali DESC";
    $result = $conn->query($query);
} elseif($filter_status === 'rusak') {
    $query = "SELECT 
        pg.id_pengembalian,
        pg.id_peminjaman,
        pg.tanggal_kembali,
        pg.kondisi_kembali,
        pg.denda,
        u.nama,
        p.tanggal_pinjam,
        p.tanggal_kembali_rencana
        FROM pengembalian pg
        JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman
        JOIN users u ON p.id_user = u.id_user
        WHERE pg.kondisi_kembali = 'Rusak'
        ORDER BY pg.tanggal_kembali DESC";
    $result = $conn->query($query);
}

$pengembalian_list = $result->fetch_all(MYSQLI_ASSOC);

// Statistik
$total_pengembalian = $conn->query("SELECT COUNT(*) as total FROM pengembalian")->fetch_assoc()['total'];
$total_terlambat = $conn->query("SELECT COUNT(*) as total FROM pengembalian pg JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman WHERE pg.tanggal_kembali > p.tanggal_kembali_rencana")->fetch_assoc()['total'];
$total_rusak = $conn->query("SELECT COUNT(*) as total FROM pengembalian WHERE kondisi_kembali='Rusak'")->fetch_assoc()['total'];
$total_denda = $conn->query("SELECT SUM(denda) as total FROM pengembalian")->fetch_assoc()['total'] ?: 0;
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | Monitoring Pengembalian</title>
    <link rel="stylesheet" href="../../src/output.css" />
  </head>
  <body class="flex gap-6 min-h-screen w-full py-8 px-14">
    <?php include '../components/sidebar_petugas.php' ?>

    <main class="right-dashboard-section">
      <nav class="navbar">
        <p>Monitoring Pengembalian</p>
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

      <div class="main-card">
        <!-- Statistics Cards -->
        <div class="dashboard-cards-petugas gap-4 mb-6">
          <div class="dashboard-card">
            <div class="card-text">
              <h3>Total Pengembalian</h3>
              <p><?= $total_pengembalian ?></p>
            </div>
            <div class="icon-wrapper icon-blue">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="28"
                height="28"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M9 14l-4 -4l4 -4" />
                <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
              </svg>
            </div>
          </div>

          <div class="dashboard-card">
            <div class="card-text">
              <h3>Terlambat</h3>
              <p><?= $total_terlambat ?></p>
            </div>
            <div class="icon-wrapper icon-amber">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="28"
                height="28"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M20.986 12.502a9 9 0 1 0 -5.973 7.98" />
                <path d="M12 7v5l3 3" />
                <path d="M19 16v3" />
                <path d="M19 22v.01" />
              </svg>
            </div>
          </div>

          <div class="dashboard-card">
            <div class="card-text">
              <h3>Rusak</h3>
              <p><?= $total_rusak ?></p>
            </div>
            <div class="icon-wrapper icon-rose">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="28"
                height="28"
                viewBox="0 0 24 24"
                fill="currentColor"
              >
                <path d="M12 1.67c.955 0 1.845 .467 2.39 1.247l.105 .16l8.114 13.548a2.914 2.914 0 0 1 -2.307 4.363l-.195 .008h-16.225a2.914 2.914 0 0 1 -2.582 -4.2l.099 -.185l8.11 -13.538a2.914 2.914 0 0 1 2.491 -1.403zm.01 13.33l-.127 .007a1 1 0 0 0 0 1.986l.117 .007l.127 -.007a1 1 0 0 0 0 -1.986l-.117 -.007zm-.01 -7a1 1 0 0 0 -.993 .883l-.007 .117v4l.007 .117a1 1 0 0 0 1.986 0l.007 -.117v-4l-.007 -.117a1 1 0 0 0 -.993 -.883z" />
              </svg>
            </div>
          </div>

          <div class="dashboard-card">
            <div class="card-text">
              <h3>Total Denda</h3>
              <p>Rp<?= number_format($total_denda, 0, ',', '.') ?></p>
            </div>
            <div class="icon-wrapper icon-indigo">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="28"
                height="28"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="1.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
                <path d="M14 8h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5m2 0v1.5m0 -9v1.5" />
              </svg>
            </div>
          </div>
        </div>

        <!-- Filter Tab -->
        <div class="filter-container">
          <a href="?status=all" class="<?= $filter_status === 'all' ? 'filter-tab-pending' : 'filter-tab-inactive' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h -2"></path><rect x="9" y="3" width="6" height="4" rx="2"></rect><path d="M9 12h6"></path><path d="M9 16h6"></path></svg> Semua
          </a>
          <a href="?status=terlambat" class="<?= $filter_status === 'terlambat' ? 'filter-tab-late' : 'filter-tab-inactive' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 1 0 0 18a9 8 0 0 0 9 -8c0 -4.3 -2 -8 -5 -10"></path><path d="M12 9v6l4 2"></path></svg> Terlambat
          </a>
          <a href="?status=rusak" class="<?= $filter_status === 'rusak' ? 'filter-tab-damaged' : 'filter-tab-inactive' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4v2"></path><path d="M12 3c-4.97 0 -9 4.03 -9 9s4.03 9 9 9s9 -4.03 9 -9s-4.03 -9 -9 -9"></path></svg> Rusak
          </a>
        </div>

        <!-- Tabel Pengembalian -->
        <section class="equipment-table-section">
          <table class="crud-table">
            <thead>
              <tr class="table-header">
                <th scope="col">ID Pengembalian</th>
                <th scope="col">Peminjam</th>
                <th scope="col">Tanggal Rencana</th>
                <th scope="col">Tanggal Kembali</th>
                <th scope="col">Kondisi</th>
                <th scope="col">Denda</th>
                <th scope="col">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if(count($pengembalian_list) > 0): ?>
                <?php foreach($pengembalian_list as $item): 
                  $terlambat = strtotime($item['tanggal_kembali']) > strtotime($item['tanggal_kembali_rencana']);
                  $status_text = $terlambat ? 'Terlambat' : 'Tepat Waktu';
                  $status_color = $terlambat ? '#f59e0b' : '#22c55e';
                ?>
                  <tr>
                    <td>#<?= $item['id_pengembalian'] ?></td>
                    <td><?= htmlspecialchars($item['nama']) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['tanggal_kembali_rencana'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['tanggal_kembali'])) ?></td>
                    <td>
                      <span class=\"<?= $item['kondisi_kembali'] === 'Rusak' ? 'status-rejected' : 'status-approved' ?>\">
                        <?= $item['kondisi_kembali'] ?>
                      </span>
                    </td>
                    <td><?= $item['denda'] > 0 ? 'Rp' . number_format($item['denda'], 0, ',', '.') : '-' ?></td>
                    <td>
                      <span class=\"<?php echo $terlambat ? 'status-pending' : 'status-approved'; ?>\">
                        <?= $status_text ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="table-cell-empty">
                    Tidak ada data pengembalian
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </section>
      </div>
    </main>
  </body>

  <script src="script.js"></script>
</html>
