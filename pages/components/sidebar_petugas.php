<?php
// Deteksi halaman aktif
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="left-dashboard-section">
  <nav class="sidebar">
  <div>
    <h2>Peminjaman Alat</h2>
    <p>Petugas</p>
  </div>
  <ul>
    <li <?php echo ($current_page === 'dashboard.php') ? 'class="active"' : ''; ?>>
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
        d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1"
        />
        <path
        d="M5 16h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1"
        />
        <path
        d="M15 12h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1"
        />
        <path
        d="M15 4h4a1 1 0 0 1 1 1v2a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-2a1 1 0 0 1 1 -1"
        />
    </svg>
    <a href="dashboard.php">Dashboard</a>
    </li>
    <li <?php echo ($current_page === 'persetujuan_peminjaman.php') ? 'class="active"' : ''; ?>>
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
      <path d="M14 3v4a1 1 0 0 0 1 1h4" />
      <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
      <path d="M9 15l2 2l4 -4" />
    </svg>
    <a href="persetujuan_peminjaman.php">Persetujuan</a>
    </li>
    <li <?php echo ($current_page === 'monitoring_pengembalian.php') ? 'class="active"' : ''; ?>>
    <svg
      xmlns="http://www.w3.org/2000/svg"
      width="24"
      height="24"
      viewBox="0 0 24 24"
      fill="none"
      stroke="currentColor"
      stroke-width="1"
      stroke-linecap="round"
      stroke-linejoin="round"
    >
      <path d="M12 21l-8 -4.5v-9l8 -4.5l8 4.5v4.5" />
      <path d="M12 12l8 -4.5" />
      <path d="M12 12v9" />
      <path d="M12 12l-8 -4.5" />
      <path d="M22 18h-7" />
      <path d="M18 15l-3 3l3 3" />
    </svg>
    <a href="monitoring_pengembalian.php">Pengembalian</a>
    </li>
    <li <?php echo ($current_page === 'laporan.php') ? 'class="active"' : ''; ?>>
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
        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
        <path
        d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"
        />
        <path d="M9 9l1 0" />
        <path d="M9 13l6 0" />
        <path d="M9 17l6 0" />
    </svg>
    <a href="laporan.php">Laporan</a>
    </li>
  </ul>
  </nav>
</aside>
