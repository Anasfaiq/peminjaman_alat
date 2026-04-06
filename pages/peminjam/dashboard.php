<?php
  session_start();
  include '../../config/conn.php';

  if (!isset($_SESSION['id_user'])) {
      die("Belum Login!");
  }

  $id_user = $_SESSION['id_user'];
  $sql = mysqli_query($conn, "SELECT nama FROM users WHERE id_user='$id_user'");
  $data = mysqli_fetch_assoc($sql);
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
        <li class="active">Dashboard</li>
        <li>Daftar Alat</li>
        <li>Pinjaman Saya</li>
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
        <button>
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
            fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 5l0 14" />
            <path d="M5 12l14 0" />
          </svg>
          Pinjam Alat
        </button>
      </div>
    </section>

    <!-- Stat Cards -->
    <section class="dashboard-cards-peminjam">

      <!-- total dipinjam -->
      <div class="dashboard-card-peminjam">
        <div class="card-text-peminjam">
          <h3>Total Dipinjam</h3>
          <p>12</p>
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
          <p class="text-amber-peminjam">3</p>
          <small>1 terlambat dikembalikan</small>
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
          <p class="text-green-peminjam">9</p>
          <small>75% selesai</small>
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
        <button class="view-all-btn">Lihat semua</button>
      </div>

      <!-- item: on time -->
      <div class="borrow-item-row">
        <div class="borrow-item-icon" style="background:#E6F1FB;">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
            fill="none" stroke="#378ADD" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="3" width="20" height="14" rx="2"/>
            <line x1="8" y1="21" x2="16" y2="21"/>
            <line x1="12" y1="17" x2="12" y2="21"/>
          </svg>
        </div>
        <div class="borrow-item-info">
          <p class="item-name">Proyektor Epson EB-X41</p>
          <div class="item-dates">
            <span>Pinjam: 10/2/2026</span>
            <span>Kembali: 20/2/2026</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:70%; background:#1D9E75;"></div>
          </div>
        </div>
        <div class="borrow-item-right">
          <span class="badge badge-ok">On Time</span>
          <button class="action-btn">Kembalikan</button>
        </div>
      </div>

      <!-- item: on time -->
      <div class="borrow-item-row">
        <div class="borrow-item-icon" style="background:#EAF3DE;">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
            fill="none" stroke="#3B6D11" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
            <circle cx="12" cy="13" r="4"/>
          </svg>
        </div>
        <div class="borrow-item-info">
          <p class="item-name">Kamera Canon EOS 90D</p>
          <div class="item-dates">
            <span>Pinjam: 12/2/2026</span>
            <span>Kembali: 19/2/2026</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:55%; background:#1D9E75;"></div>
          </div>
        </div>
        <div class="borrow-item-right">
          <span class="badge badge-ok">On Time</span>
          <button class="action-btn">Kembalikan</button>
        </div>
      </div>

      <!-- item: late -->
      <div class="borrow-item-row borrow-item-late">
        <div class="borrow-item-icon" style="background:#FCEBEB;">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
            fill="none" stroke="#A32D2D" stroke-width="1.8"
            stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 1a3 3 0 0 0-3 3v8a3 3 0 0 0 6 0V4a3 3 0 0 0-3-3z"/>
            <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
            <line x1="12" y1="19" x2="12" y2="23"/>
            <line x1="8" y1="23" x2="16" y2="23"/>
          </svg>
        </div>
        <div class="borrow-item-info">
          <p class="item-name late-text">Mikrofon Wireless Shure</p>
          <div class="item-dates">
            <span>Pinjam: 5/2/2026</span>
            <span class="late-text" style="font-weight:500;">Kembali: 15/2/2026 — sudah lewat!</span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" style="width:100%; background:#E24B4A;"></div>
          </div>
        </div>
        <div class="borrow-item-right">
          <span class="badge badge-late">Terlambat</span>
          <button class="action-btn action-btn-late">Kembalikan</button>
        </div>
      </div>

    </section>
  </main>
</body>
</html>