<?php
  session_start();
  include '../../config/conn.php';

  if (!isset($_SESSION['id_user'])) {
      die("Belum Login!");
  }

  $id_user = $_SESSION['id_user'];
  $sql_user = mysqli_query($conn, "SELECT nama FROM users WHERE id_user='$id_user'");
  $data = mysqli_fetch_assoc($sql_user);

  /* active borrowings — status Disetujui dan belum ada pengembalian */
  $sql_aktif = mysqli_query($conn, "
    SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali_rencana, p.status,
           a.id_alat, a.nama_alat, a.kondisi,
           k.nama_kategori,
           dp.jumlah,
           DATEDIFF(NOW(), p.tanggal_kembali_rencana) AS hari_terlambat
    FROM peminjaman p
    JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
    JOIN alat a ON dp.id_alat = a.id_alat
    JOIN kategori k ON a.id_kategori = k.id_kategori
    LEFT JOIN pengembalian pb ON p.id_peminjaman = pb.id_peminjaman
    WHERE p.id_user = '$id_user'
      AND p.status = 'Disetujui'
      AND pb.id_pengembalian IS NULL
    ORDER BY p.tanggal_kembali_rencana ASC
  ");

  /* menunggu persetujuan */
  $sql_menunggu = mysqli_query($conn, "
    SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali_rencana, p.status,
           a.nama_alat,
           k.nama_kategori,
           dp.jumlah
    FROM peminjaman p
    JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
    JOIN alat a ON dp.id_alat = a.id_alat
    JOIN kategori k ON a.id_kategori = k.id_kategori
    WHERE p.id_user = '$id_user'
      AND p.status = 'Menunggu'
    ORDER BY p.created_at DESC
  ");

  /* history — sudah dikembalikan atau ditolak */
  $sql_history = mysqli_query($conn, "
    SELECT p.id_peminjaman, p.tanggal_pinjam, p.tanggal_kembali_rencana, p.status,
           a.nama_alat,
           k.nama_kategori,
           dp.jumlah,
           pb.tanggal_kembali, pb.kondisi_kembali, pb.denda
    FROM peminjaman p
    JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman
    JOIN alat a ON dp.id_alat = a.id_alat
    JOIN kategori k ON a.id_kategori = k.id_kategori
    LEFT JOIN pengembalian pb ON p.id_peminjaman = pb.id_peminjaman
    WHERE p.id_user = '$id_user'
      AND (pb.id_pengembalian IS NOT NULL OR p.status = 'Ditolak')
    ORDER BY p.created_at DESC
  ");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peminjaman Alat | Pinjaman Saya</title>
  <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="db-body">

  <!-- navbar -->
  <nav class="navbar-peminjam">
    <section class="left-header-peminjam">
      <p>Peminjaman Alat</p>
      <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="daftar_alat.php">Daftar Alat</a></li>
        <li class="active"><a href="pinjaman_user.php">Pinjaman Saya</a></li>
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

  <!-- main content -->
  <main class="dashboard-content-peminjam">

    <!-- alert -->
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">Pengembalian berhasil diajukan!</div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- header card -->
    <section class="header-card-peminjam">
      <h3>Pinjaman Saya</h3>
      <p>Manage your active borrowings and view history</p>
    </section>

    <!-- menunggu persetujuan -->
    <?php if (mysqli_num_rows($sql_menunggu) > 0): ?>
    <section class="pinjaman-cards">
      <p class="text-lg font-medium mb-3">Menunggu Persetujuan</p>
      <div class="list-item-pinjam-cards">
        <?php while ($row = mysqli_fetch_assoc($sql_menunggu)): ?>
        <div class="list-item-pinjam-card">
          <div class="pinjaman-card">
            <div class="card-header icon-amber-peminjam">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
              </svg>
            </div>
            <div class="pinjaman-content">
              <div class="upper-pinjaman-content">
                <h3><?= htmlspecialchars($row['nama_alat']) ?></h3>
                <span class="badge" style="background:#FAEEDA;color:#BA7517;">Menunggu</span>
              </div>
              <div class="deskripsi-pinjaman">
                <span><?= htmlspecialchars($row['nama_kategori']) ?></span>
                <span>Jumlah: <?= $row['jumlah'] ?></span>
              </div>
              <div class="deskripsi-pinjaman" style="margin-top:4px;">
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />
                    <path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" />
                    <path d="M8 15h2v2h-2l0 -2" />
                  </svg>
                  Pinjam: <?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?>
                </span>
                <span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                    <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />
                    <path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" />
                    <path d="M8 15h2v2h-2l0 -2" />
                  </svg>
                  Kembali: <?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])) ?>
                </span>
              </div>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- active borrowings -->
    <section class="pinjaman-cards">
      <p class="text-lg font-medium mb-3">Active Borrowings</p>
      <?php if (mysqli_num_rows($sql_aktif) === 0): ?>
        <div class="list-item-pinjam-card" style="width:100%;">
          <p class="text-gray-400 text-sm py-6 text-center">Tidak ada peminjaman aktif.</p>
        </div>
      <?php else: ?>
      <div class="list-item-pinjam-cards">
        <?php while ($row = mysqli_fetch_assoc($sql_aktif)):
          $terlambat = $row['hari_terlambat'] > 0;
        ?>
        <div class="list-item-pinjam-card">
          <div class="pinjaman-card">
            <div class="card-header icon-blue-peminjam">
              <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
                <path d="M12 12l8 -4.5" />
                <path d="M12 12l0 9" />
                <path d="M12 12l-8 -4.5" />
                <path d="M16 5.25l-8 4.5" />
              </svg>
            </div>
            <div class="pinjaman-content">
              <div class="upper-pinjaman-content">
                <h3><?= htmlspecialchars($row['nama_alat']) ?></h3>
                <?php if ($terlambat): ?>
                  <span class="badge badge-late">Terlambat <?= $row['hari_terlambat'] ?> hari</span>
                <?php else: ?>
                  <span class="badge badge-ok">Aktif</span>
                <?php endif; ?>
              </div>
              <div class="deskripsi-pinjaman">
                <span><?= htmlspecialchars($row['nama_kategori']) ?></span>
                <span>Jumlah: <?= $row['jumlah'] ?></span>
              </div>
              <div class="deskripsi-pinjaman" style="margin-top:4px;">
                <span>Pinjam: <?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?></span>
                <span <?= $terlambat ? 'class="late-text"' : '' ?>>
                  Kembali: <?= date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])) ?>
                </span>
              </div>
              <div class="pinjaman-card-footer">
                <button onclick="openModalKembali(<?= $row['id_peminjaman'] ?>, '<?= htmlspecialchars($row['nama_alat'], ENT_QUOTES) ?>')">
                  Ajukan Pengembalian
                </button>
              </div>
            </div>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <?php endif; ?>
    </section>

    <!-- history -->
    <section class="pinjaman-cards">
      <p class="text-lg font-medium mb-3">Borrowing History</p>
      <?php if (mysqli_num_rows($sql_history) === 0): ?>
        <p class="text-gray-400 text-sm">Belum ada riwayat peminjaman.</p>
      <?php else: ?>
      <div class="list-history-pinjaman">
        <?php while ($row = mysqli_fetch_assoc($sql_history)): ?>
        <div class="history-pinjaman-card">
          <div class="left-history-content">
            <div class="item-history">
              <h3><?= htmlspecialchars($row['nama_alat']) ?></h3>
              <span><?= htmlspecialchars($row['nama_kategori']) ?> &middot; Jumlah: <?= $row['jumlah'] ?></span>
            </div>
            <div class="history-date">
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2l0 -12" />
                <path d="M16 3l0 4" /><path d="M8 3l0 4" /><path d="M4 11l16 0" />
                <path d="M8 15h2v2h-2l0 -2" />
              </svg>
              <p>
                <?= date('d/m/Y', strtotime($row['tanggal_pinjam'])) ?>
                &mdash;
                <?= $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : date('d/m/Y', strtotime($row['tanggal_kembali_rencana'])) ?>
              </p>
            </div>
            <?php if ($row['denda'] > 0): ?>
              <p style="font-size:12px;color:#A32D2D;margin-top:4px;">
                Denda: Rp<?= number_format($row['denda'], 0, ',', '.') ?>
              </p>
            <?php endif; ?>
          </div>
          <div class="right-history-content">
            <?php if ($row['status'] === 'Ditolak'): ?>
              <span class="badge badge-late">Ditolak</span>
            <?php else: ?>
              <span class="badge badge-ok">Dikembalikan</span>
            <?php endif; ?>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
      <?php endif; ?>
    </section>

  </main>

  <!-- backdrop -->
  <div id="backdrop" class="backdrop hidden" onclick="closeModalKembali()"></div>

  <!-- modal ajukan pengembalian -->
  <div id="modal-kembali" class="modal hidden">
    <div class="modal-header">
      <h3 id="modal-kembali-title">Ajukan Pengembalian</h3>
      <button onclick="closeModalKembali()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="1.5"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
        </svg>
      </button>
    </div>
    <form action="proses/proses_kembali.php" method="POST">
      <input type="hidden" name="id_peminjaman" id="input-id-peminjaman">

      <div class="form">
        <label>Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form">
        <label>Kondisi Alat saat Dikembalikan</label>
        <select name="kondisi_kembali" required>
          <option value="Baik">Baik</option>
          <option value="Rusak">Rusak</option>
        </select>
      </div>

      <div class="button-group">
        <button type="button" class="cancel-btn" onclick="closeModalKembali()">Batal</button>
        <button type="submit" class="simpan-btn">Ajukan</button>
      </div>
    </form>
  </div>

  <script>
    function openModalKembali(idPeminjaman, namaAlat) {
      document.getElementById('modal-kembali-title').textContent = 'Kembalikan ' + namaAlat;
      document.getElementById('input-id-peminjaman').value = idPeminjaman;
      document.getElementById('modal-kembali').classList.remove('hidden');
      document.getElementById('backdrop').classList.remove('hidden');
    }

    function closeModalKembali() {
      document.getElementById('modal-kembali').classList.add('hidden');
      document.getElementById('backdrop').classList.add('hidden');
    }
  </script>
</body>
</html>