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
            <path
            d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"
            />
            <path
            d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"
            />
            <path d="M9 12l.01 0" />
            <path d="M13 12l2 0" />
            <path d="M9 16l.01 0" />
            <path d="M13 16l2 0" />
        </svg>
        <a href="persetujuan_peminjaman.php">Persetujuan Peminjaman</a>
        </li>
        <li <?php echo ($current_page === 'monitoring_pengembalian.php') ? 'class="active"' : ''; ?>>
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
            <path d="M19.95 11a8 8 0 1 0 -.5 4m.5 5v-5h-5" />
        </svg>
        <a href="monitoring_pengembalian.php">Monitoring Pengembalian</a>
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
