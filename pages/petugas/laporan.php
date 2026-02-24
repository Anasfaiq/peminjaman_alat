<?php
session_start();
include "../../config/conn.php";
include "../../config/auth-check.php";
include "../../config/logging.php";

// Proteksi: hanya petugas yang bisa akses
checkAuth('petugas');

$id_user = $_SESSION['id_user'];
$user_data = getUserById($conn, $id_user);

// Get filter bulan & tahun
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Validasi
$bulan = (int)$bulan;
$tahun = (int)$tahun;

if($bulan < 1 || $bulan > 12) $bulan = (int)date('m');
if($tahun < 2020 || $tahun > 2099) $tahun = (int)date('Y');

// Format bulan untuk query
$start_date = "$tahun-$bulan-01";
$end_date = date('Y-m-t', strtotime($start_date));

// Query data laporan
$laporan_query = "SELECT 
  COUNT(DISTINCT p.id_peminjaman) as total_peminjaman,
  SUM(CASE WHEN p.status='Disetujui' THEN 1 ELSE 0 END) as disetujui,
  SUM(CASE WHEN p.status='Ditolak' THEN 1 ELSE 0 END) as ditolak,
  COUNT(DISTINCT pg.id_pengembalian) as total_pengembalian,
  SUM(CASE WHEN pg.tanggal_kembali > p.tanggal_kembali_rencana THEN 1 ELSE 0 END) as terlambat,
  SUM(CASE WHEN pg.kondisi_kembali='Rusak' THEN 1 ELSE 0 END) as rusak,
  SUM(pg.denda) as total_denda
  FROM peminjaman p
  LEFT JOIN pengembalian pg ON p.id_peminjaman = pg.id_peminjaman
  WHERE DATE(p.created_at) >= ? AND DATE(p.created_at) <= ?";

$stmt = $conn->prepare($laporan_query);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$laporan_data = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | Laporan</title>
    <link rel="stylesheet" href="../../src/output.css" />
    <style>
      @media print {
        body {
          background: white;
        }
        .navbar, #userBtn, .user-dropdown-wrapper, button[onclick*="print"] {
          display: none !important;
        }
      }
    </style>
  </head>
  <body class="flex gap-6 min-h-screen w-full py-8 px-14">
    <?php include '../components/sidebar_petugas.php' ?>

    <main class="right-dashboard-section">
      <nav class="navbar">
        <p>Laporan</p>
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
        <!-- Filter Bulan & Tahun -->
        <div class="report-filter-section">
          <div>
            <label class="filter-label">Bulan:</label>
            <select id="bulan" onchange="filterLaporan()" class="filter-select">
              <?php for($i=1; $i<=12; $i++): ?>
                <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == $i ? 'selected' : '' ?>>
                  <?= strftime('%B', mktime(0, 0, 0, $i, 1)) === false ? date('F', mktime(0, 0, 0, $i, 1)) : strftime('%B', mktime(0, 0, 0, $i, 1)) ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
          <div>
            <label class="filter-label">Tahun:</label>
            <select id="tahun" onchange="filterLaporan()" class="filter-select">
              <?php for($y=2020; $y<=2099; $y++): ?>
                <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <button onclick="window.print()" class="print-button">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1 -2 -2v-5a2 2 0 0 1 2 -2h16a2 2 0 0 1 2 2v5a2 2 0 0 1 -2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> Cetak
          </button>
        </div>

        <!-- Laporan Summary -->
        <div class="report-header">
          <h2 class="report-title">
            Laporan Peminjaman Alat - 
            <?php 
            $bulan_text = date('F', mktime(0, 0, 0, $bulan, 1));
            echo "$bulan_text $tahun";
            ?>
          </h2>

          <div class="report-stats-grid">
            <div class="dashboard-card">
              <div class="card-text">
                <h3>Total Peminjaman</h3>
                <p><?= $laporan_data['total_peminjaman'] ?: 0 ?></p>
              </div>
              <div class="icon-wrapper icon-indigo">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h -2"></path><rect x="9" y="3" width="6" height="4" rx="2"></rect><path d="M9 12h6"></path><path d="M9 16h6"></path></svg>
              </div>
            </div>

            <div class="dashboard-card">
              <div class="card-text">
                <h3>Disetujui</h3>
                <p><?= $laporan_data['disetujui'] ?: 0 ?></p>
              </div>
              <div class="icon-wrapper icon-emerald">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg>
              </div>
            </div>

            <div class="dashboard-card">
              <div class="card-text">
                <h3>Ditolak</h3>
                <p><?= $laporan_data['ditolak'] ?: 0 ?></p>
              </div>
              <div class="icon-wrapper icon-rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M10 10l4 4m0 -4l-4 4"></path></svg>
              </div>
            </div>

            <div class="dashboard-card">
              <div class="card-text">
                <h3>Total Pengembalian</h3>
                <p><?= $laporan_data['total_pengembalian'] ?: 0 ?></p>
              </div>
              <div class="icon-wrapper icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 1 0 0 18a9 8 0 0 0 9 -8c0 -4.3 -2 -8 -5 -10"></path><path d="M12 9v6l4 2"></path></svg>
              </div>
            </div>

            <div class="dashboard-card">
              <div class="card-text">
                <h3>Terlambat</h3>
                <p><?= $laporan_data['terlambat'] ?: 0 ?></p>
              </div>
              <div class="icon-wrapper icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a9 9 0 1 0 0 18a9 8 0 0 0 9 -8c0 -4.3 -2 -8 -5 -10"></path><path d="M12 9v6l4 2"></path></svg>
              </div>
            </div>

            <div class="dashboard-card">
              <div class="card-text">
                <h3>Rusak</h3>
                <p><?= $laporan_data['rusak'] ?: 0 ?></p>
              </div>
              <div class="icon-wrapper icon-rose">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4v2"></path><path d="M12 3c-4.97 0 -9 4.03 -9 9s4.03 9 9 9s9 -4.03 9 -9s-4.03 -9 -9 -9"></path></svg>
              </div>
            </div>

            <div class="dashboard-card">
              <div class="card-text">
                <h3>Total Denda</h3>
                <p>Rp<?= number_format($laporan_data['total_denda'] ?: 0, 0, ',', '.') ?></p>
              </div>
              <div class="icon-wrapper icon-indigo">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"></circle><path d="M12 9v6m-3 0h6"></path></svg>
              </div>
            </div>
          </div>

          <div class="report-footer">
            <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
            <p>Oleh: <?= htmlspecialchars($user_data['nama']) ?></p>
          </div>
        </div>
      </div>
    </main>
  </body>

  <script>
    function filterLaporan() {
      const bulan = document.getElementById('bulan').value;
      const tahun = document.getElementById('tahun').value;
      window.location.href = `?bulan=${bulan}&tahun=${tahun}`;
    }
  </script>

  <script src="../../script.js"></script>
</html>
