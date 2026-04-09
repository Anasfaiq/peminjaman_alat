<?php
  session_start();
  include '../../config/conn.php';
  include '../../config/payment-helper.php';

  if (!isset($_SESSION['id_user'])) {
      die("Belum Login!");
  }

  $id_user = $_SESSION['id_user'];
  $sql = mysqli_query($conn, "SELECT nama FROM users WHERE id_user='$id_user'");
  $data = mysqli_fetch_assoc($sql);

  // Total dipinjam sepanjang waktu
  $total_pinjam_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman WHERE id_user='$id_user'");
  $total_pinjam = mysqli_fetch_assoc($total_pinjam_query)['total'];

  // Sedang dipinjam (status Disetujui dan belum dikembalikan)
  $sedang_pinjam_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman p 
                                              WHERE p.id_user='$id_user' 
                                              AND p.status='Disetujui' 
                                              AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)");
  $sedang_pinjam = mysqli_fetch_assoc($sedang_pinjam_query)['total'];

  // Sudah dikembalikan
  $sudah_kembali_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM pengembalian pg
                                              JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman
                                              WHERE p.id_user='$id_user'");
  $sudah_kembali = mysqli_fetch_assoc($sudah_kembali_query)['total'];

  // Hitung terlambat
  $terlambat_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman p
                                         WHERE p.id_user='$id_user'
                                         AND p.status='Disetujui'
                                         AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)
                                         AND p.tanggal_kembali_rencana < NOW()");
  $terlambat = mysqli_fetch_assoc($terlambat_query)['total'];

  // Query peminjaman aktif dengan detail lengkap
  $active_borrowing_query = mysqli_query($conn, "SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali_rencana,
                                               GROUP_CONCAT(a.nama_alat SEPARATOR ', ') as alat_list,
                                               COUNT(d.id_detail) as jumlah_alat,
                                               CASE 
                                                 WHEN p.tanggal_kembali_rencana < NOW() THEN 'terlambat'
                                                 ELSE 'tepat'
                                               END as status_waktu
                                               FROM peminjaman p
                                               LEFT JOIN detail_peminjaman d ON p.id_peminjaman = d.id_peminjaman
                                               LEFT JOIN alat a ON d.id_alat = a.id_alat
                                               WHERE p.id_user='$id_user'
                                               AND p.status='Disetujui'
                                               AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)
                                               GROUP BY p.id_peminjaman
                                               ORDER BY p.tanggal_pinjam DESC
                                               LIMIT 3");
  $active_borrowings = [];
  while($row = mysqli_fetch_assoc($active_borrowing_query)) {
    $active_borrowings[] = $row;
  }

  // Hitung progress
  $total_active = $sedang_pinjam > 0 ? $sedang_pinjam : 1; // avoid division by zero
  $persentase_selesai = $total_pinjam > 0 ? round(($sudah_kembali / $total_pinjam) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peminjaman Alat | Dashboard Peminjam</title>
  <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="db-body">

  <!-- Navbar -->
  <nav class="navbar-peminjam">
    <section class="left-header-peminjam">
      <p>Peminjaman Alat</p>
      <ul>
        <li class="active">
          <a href="dashboard.php">Dashboard</a>
        </li>
        <li>
          <a href="daftar_alat.php">Daftar Alat</a>
        </li>
        <li>
          <a href="pinjaman_user.php">Pinjaman Saya</a>
        </li>
      </ul>
    </section>
    <section class="right-header-peminjam">
      <div class="nav-avatar">
        <?= strtoupper(substr($data['nama'], 0, 2)) ?>
      </div>
      <span class="nav-username"><?= $data['nama'] ?></span>
      <a href="../../config/logout.php" class="nav-logout-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="1.5"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
          <path d="M9 12h12l-3 -3" />
          <path d="M18 15l3 -3" />
        </svg>
        Logout
      </a>
    </section>
  </nav>

  <!-- Main Content -->
  <main class="dashboard-content-peminjam">

    <!-- Welcome Bar -->
    <section class="welcome-bar">
      <div>
        <h2 class="welcome-title">Selamat datang, <?= $data['nama'] ?></h2>
        <p class="welcome-sub">Ini ringkasan peminjaman kamu hari ini</p>
      </div>
      <div class="add-peminjam-btn">
        <a href="daftar_alat.php">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
          </svg>
          Pinjam Alat
        </a>
      </div>
    </section>

    <!-- Stat Cards -->
    <section class="dashboard-cards-peminjam">

      <!-- total dipinjam -->
      <div class="dashboard-card-peminjam">
        <div class="card-text-peminjam">
          <h3>Total Dipinjam</h3>
          <p><?= $total_pinjam ?></p>
          <small>Sepanjang waktu</small>
        </div>
        <div class="icon-wrapper-peminjam icon-blue-peminjam">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
            <path d="M12 12l8 -4.5" />
            <path d="M12 12l0 9" />
            <path d="M12 12l-8 -4.5" />
            <path d="M16 5.25l-8 4.5" />
          </svg>
        </div>
      </div>

      <!-- sedang dipinjam -->
      <div class="dashboard-card-peminjam">
        <div class="card-text-peminjam">
          <h3>Sedang Dipinjam</h3>
          <p class="text-amber-peminjam"><?= $sedang_pinjam ?></p>
          <small><?= $terlambat > 0 ? $terlambat . ' terlambat dikembalikan' : 'Semua tepat waktu' ?></small>
        </div>
        <div class="icon-wrapper-peminjam icon-amber-peminjam">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10"/>
            <polyline points="12 6 12 12 16 14"/>
          </svg>
        </div>
      </div>

      <!-- sudah dikembalikan -->
      <div class="dashboard-card-peminjam">
        <div class="card-text-peminjam">
          <h3>Sudah Dikembalikan</h3>
          <p class="text-green-peminjam"><?= $sudah_kembali ?></p>
          <small><?= $persentase_selesai ?>% selesai</small>
        </div>
        <div class="icon-wrapper-peminjam icon-green-peminjam">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <polyline points="20 6 9 17 4 12"/>
          </svg>
        </div>
      </div>

    </section>

    <!-- Active Borrowings List -->
    <section class="card-peminjam">
      <div class="card-peminjam-header">
        <p>Peminjaman Aktif</p>
        <button class="view-all-btn" onclick="location.href='pinjaman_user.php'">Lihat semua</button>
      </div>

      <?php if(count($active_borrowings) > 0): ?>
        <?php foreach($active_borrowings as $borrow): 
          $is_late = $borrow['status_waktu'] === 'terlambat';
          $date_pinjam = new DateTime($borrow['tanggal_pinjam']);
          $date_kembali = new DateTime($borrow['tanggal_kembali_rencana']);
          $today = new DateTime();
          $interval = $today->diff($date_pinjam);
          $total_days = $interval->days + 1;
          $interval_progress = $date_kembali->diff($date_pinjam);
          $total_duration = $interval_progress->days + 1;
          $progress_pct = min(($total_days / $total_duration) * 100, 100);
        ?>
          <div class="borrow-item-row <?= $is_late ? 'borrow-item-late' : '' ?>">
            <div class="borrow-item-icon <?= $is_late ? 'borrow-item-icon-late' : 'borrow-item-icon-ok' ?>">
              <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round" class="<?= $is_late ? 'text-red-700' : 'text-blue-600' ?>">
                <?php if($is_late): ?>
                  <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
                  <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                  <line x1="12" y1="19" x2="12" y2="23"/>
                  <line x1="8" y1="23" x2="16" y2="23"/>
                <?php else: ?>
                  <rect x="2" y="3" width="20" height="14" rx="2"/>
                  <line x1="8" y1="21" x2="16" y2="21"/>
                  <line x1="12" y1="17" x2="12" y2="21"/>
                <?php endif; ?>
              </svg>
            </div>
            <div class="borrow-item-info">
              <p class="item-name <?= $is_late ? 'late-text' : '' ?>"><?= htmlspecialchars($borrow['alat_list']) ?></p>
              <div class="item-dates">
                <span>Pinjam: <?= date('d/m/Y', strtotime($borrow['tanggal_pinjam'])) ?></span>
                <?php if($is_late): ?>
                  <span class="late-text font-medium">Kembali: <?= date('d/m/Y', strtotime($borrow['tanggal_kembali_rencana'])) ?> — sudah lewat!</span>
                <?php else: ?>
                  <span>Kembali: <?= date('d/m/Y', strtotime($borrow['tanggal_kembali_rencana'])) ?></span>
                <?php endif; ?>
              </div>
              <div class="progress-bar">
                <div class="progress-fill <?= $is_late ? 'progress-fill-late' : 'progress-fill-ok' ?>" style="width:<?= $progress_pct ?>%;"></div>
              </div>
            </div>
            <div class="borrow-item-right">
              <span class="badge <?= $is_late ? 'badge-late' : 'badge-ok' ?>">
                <?= $is_late ? 'Terlambat' : 'On Time' ?>
              </span>
              <button class="action-btn <?= $is_late ? 'action-btn-late' : '' ?>" onclick="location.href='pinjaman_user.php#id-<?= $borrow['id_peminjaman'] ?>'">
                Kembalikan
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <p>Tidak ada peminjaman aktif saat ini</p>
        </div>
      <?php endif; ?>
  </main>
</body>
</html>