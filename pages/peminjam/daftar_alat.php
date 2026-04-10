<?php
  session_start();
  include '../../config/conn.php';
  include '../../config/auth-check.php';

  checkAuth('peminjam');

  $id_user = $_SESSION['id_user'];
  $sql_user = mysqli_query($conn, "SELECT nama FROM users WHERE id_user='$id_user'");
  $data = mysqli_fetch_assoc($sql_user);

  $sql_alat = mysqli_query($conn, "
    SELECT a.*, k.nama_kategori 
    FROM alat a
    JOIN kategori k ON a.id_kategori = k.id_kategori
    ORDER BY a.nama_alat ASC
  ");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Peminjaman Alat | Daftar Alat Peminjam</title>
  <link rel="stylesheet" href="../../src/output.css">
</head>
<body class="db-body">
  <!-- Navbar -->
  <nav class="navbar-peminjam">
    <section class="left-header-peminjam">
      <p>Peminjaman Alat</p>
      <ul>
        <li>
          <a href="dashboard.php">Dashboard</a>
        </li>
        <li class="active">
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

  <!-- main content -->
  <main class="dashboard-content-peminjam">

    <!-- alert -->
    <?php if (isset($_GET['success'])): ?>
      <div class="alert alert-success">Peminjaman berhasil diajukan! Menunggu persetujuan petugas.</div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-error"><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <!-- header card -->
    <section class="header-card-peminjam">
      <h3>Daftar Alat</h3>
      <p>Browse and borrow available equipment</p>
    </section>

    <!-- list item -->
    <section class="list-item-cards">
      <?php while ($alat = mysqli_fetch_assoc($sql_alat)): ?>
      <div class="list-item-card">
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
        <div class="card-body">
          <h3 class="title"><?= htmlspecialchars($alat['nama_alat']) ?></h3>
          <div class="meta">
            <span class="category"><?= htmlspecialchars($alat['nama_kategori']) ?></span>
            <?php if ($alat['stok'] > 0): ?>
              <span class="stock badge-ok"><?= $alat['stok'] ?> tersedia</span>
            <?php else: ?>
              <span class="stock badge-late">Habis</span>
            <?php endif; ?>
          </div>
          <p class="description">Kondisi: <?= $alat['kondisi'] ?></p>
        </div>
        <div class="card-footer">
          <?php if ($alat['stok'] > 0): ?>
            <button
              onclick="openModal(<?= $alat['id_alat'] ?>, '<?= htmlspecialchars($alat['nama_alat'], ENT_QUOTES) ?>', <?= $alat['stok'] ?>)"
            >Pinjam</button>
          <?php else: ?>
            <button disabled>Tidak Tersedia</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </section>
  </main>

  <!-- backdrop -->
  <div id="backdrop" class="backdrop hidden" onclick="closeModal()"></div>

  <!-- modal pinjam -->
  <div id="modal-pinjam" class="modal hidden">
    <div class="modal-header">
      <h3 id="modal-title">Pinjam Alat</h3>
      <button onclick="closeModal()">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
          fill="none" stroke="currentColor" stroke-width="1.5"
          stroke-linecap="round" stroke-linejoin="round">
          <path d="M18 6l-12 12" /><path d="M6 6l12 12" />
        </svg>
      </button>
    </div>
    <form action="proses/proses_pinjam.php" method="POST">
      <input type="hidden" name="id_alat" id="input-id-alat">

      <div class="form">
        <label>Tanggal Pinjam</label>
        <input type="date" name="tanggal_pinjam" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="form">
        <label>Tanggal Kembali Rencana</label>
        <input type="date" name="tanggal_kembali_rencana" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
      </div>

      <div class="form">
        <label>Jumlah</label>
        <input type="number" name="jumlah" id="input-jumlah" min="1" value="1" required>
      </div>

      <div class="button-group">
        <button type="button" class="cancel-btn" onclick="closeModal()">Batal</button>
        <button type="submit" class="simpan-btn">Ajukan Pinjam</button>
      </div>
    </form>
  </div>

  <script>
    function openModal(idAlat, namaAlat, stok) {
      document.getElementById('modal-title').textContent = 'Pinjam ' + namaAlat;
      document.getElementById('input-id-alat').value = idAlat;
      document.getElementById('input-jumlah').max = stok;
      document.getElementById('modal-pinjam').classList.remove('hidden');
      document.getElementById('backdrop').classList.remove('hidden');
    }

    function closeModal() {
      document.getElementById('modal-pinjam').classList.add('hidden');
      document.getElementById('backdrop').classList.add('hidden');
    }
  </script>
</body>
</html>