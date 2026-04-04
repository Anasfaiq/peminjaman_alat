<?php
session_start();
include "../../config/conn.php";
include "../../config/auth-check.php";
include "../../config/logging.php";

// Proteksi: hanya petugas yang bisa akses
checkAuth('petugas');

$id_user = $_SESSION['id_user'];
$user_data = getUserById($conn, $id_user);

// Get filter status dari URL
$filter_status = isset($_GET['status']) ? $_GET['status'] : 'Menunggu';

// Validasi filter_status
$valid_statuses = ['Menunggu', 'Disetujui', 'Ditolak'];
if (!in_array($filter_status, $valid_statuses)) {
    $filter_status = 'Menunggu';
}

// Query peminjaman sesuai filter
$query = "SELECT p.id_peminjaman, u.nama, u.id_user, p.tanggal_pinjam, 
         p.tanggal_kembali_rencana, p.status, p.created_at,
         COUNT(d.id_detail) as jumlah_alat
         FROM peminjaman p
         JOIN users u ON p.id_user = u.id_user
         LEFT JOIN detail_peminjaman d ON p.id_peminjaman = d.id_peminjaman
         WHERE p.status = ?
         GROUP BY p.id_peminjaman
         ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $filter_status);
$stmt->execute();
$result = $stmt->get_result();
$peminjaman_list = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | Persetujuan Peminjaman</title>
    <link rel="stylesheet" href="../../src/output.css" />
  </head>
  <body class="flex gap-6 min-h-screen w-full py-8 px-14">
    <?php include '../components/sidebar_petugas.php' ?>

    <main class="right-dashboard-section">
      <nav class="navbar">
        <p>Persetujuan Peminjaman</p>
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
        <!-- Filter Tabs -->
        <div class="filter-container">
          <a href="?status=Menunggu" class="<?= $filter_status === 'Menunggu' ? 'filter-tab-pending' : 'filter-tab-inactive' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2V7a2 2 0 0 0 -2 -2h -2"></path><rect x="9" y="3" width="6" height="4" rx="2"></rect><path d="M9 12h6"></path><path d="M9 16h6"></path></svg> Menunggu (<?php $c = $conn->query("SELECT COUNT(*) as c FROM peminjaman WHERE status='Menunggu'"); $r = $c->fetch_assoc(); echo $r['c']; ?>)
          </a>
          <a href="?status=Disetujui" class="<?= $filter_status === 'Disetujui' ? 'filter-tab-approved' : 'filter-tab-inactive' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Disetujui (<?php $c = $conn->query("SELECT COUNT(*) as c FROM peminjaman WHERE status='Disetujui'"); $r = $c->fetch_assoc(); echo $r['c']; ?>)
          </a>
          <a href="?status=Ditolak" class="<?= $filter_status === 'Ditolak' ? 'filter-tab-rejected' : 'filter-tab-inactive' ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M10 10l4 4m0 -4l-4 4"></path></svg> Ditolak (<?php $c = $conn->query("SELECT COUNT(*) as c FROM peminjaman WHERE status='Ditolak'"); $r = $c->fetch_assoc(); echo $r['c']; ?>)
          </a>
        </div>

        <section class="equipment-table-section">
          <table class="crud-table">
            <thead>
              <tr class="table-header">
                <th scope="col">ID Peminjaman</th>
                <th scope="col">Peminjam</th>
                <th scope="col">Tanggal Pinjam</th>
                <th scope="col">Rencana Kembali</th>
                <th scope="col">Jml Alat</th>
                <th scope="col">Status</th>
                <th scope="col">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php if(count($peminjaman_list) > 0): ?>
                <?php foreach($peminjaman_list as $item): ?>
                  <tr>
                    <td>#<?= $item['id_peminjaman'] ?></td>
                    <td><?= htmlspecialchars($item['nama']) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['tanggal_pinjam'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($item['tanggal_kembali_rencana'])) ?></td>
                    <td class="text-center"><?= $item['jumlah_alat'] ?></td>
                    <td class="text-center">
                      <span class="<?php
                        if($item['status'] === 'Menunggu') {
                            echo 'status-pending';
                        } elseif($item['status'] === 'Disetujui') {
                            echo 'status-approved';
                        } else {
                            echo 'status-rejected';
                        }
                      ?>">
                        <?= $item['status'] ?>
                      </span>
                    </td>
                    <td class="text-center">
                      <button onclick="openDetailModal(<?= $item['id_peminjaman'] ?>, '<?= htmlspecialchars($item['nama']) ?>', '<?= $item['status'] ?>')" 
                              class="detail-link">
                        Lihat Detail
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="7" class="table-cell-empty">
                    Tidak ada data peminjaman dengan status <?= $filter_status ?>
                  </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </section>
      </div>
    </main>
  </body>

  <!-- Modal untuk detail peminjaman -->
  <div id="detailModal" class="backdrop hidden"></div>
  <div id="detailModalContent" class="modal hidden w-96">
    <div class="modal-header">
      <h3>Detail Peminjaman</h3>
      <button onclick="closeDetailModal()" class="text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg p-2 transition-colors">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.75"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M18 6l-12 12" />
          <path d="M6 6l12 12" />
        </svg>
      </button>
    </div>
    <form id="detailForm">
      <div class="modal-content-form">
        <div class="modal-field">
          <label class="modal-field-label">Peminjam:</label>
          <p id="peminjamName" class="modal-field-display"></p>
        </div>

        <div id="detailAlatContainer" class="modal-field">
          <label class="modal-field-label">Detail Alat:</label>
          <div id="detailAlatList" class="modal-field-list"></div>
        </div>

        <div class="modal-actions">
          <button type="button" id="btnSetujui" onclick="updateStatusPeminjaman(currentPeminjamanId, 'Disetujui')" 
                  class="simpan-btn-petugas">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"></path></svg> Setujui
          </button>
          <button type="button" id="btnTolak" onclick="updateStatusPeminjaman(currentPeminjamanId, 'Ditolak')" 
                  class="cancel-btn">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"></path><path d="M10 10l4 4m0 -4l-4 4"></path></svg> Tolak
          </button>
          <button type="button" id="btnBatalkan" onclick="updateStatusPeminjaman(currentPeminjamanId, 'Menunggu')" 
                  class="edit-btn-petugas hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 0 1 9 -9a9.75 9.75 0 0 1 6.74 2.74L21 8"></path><path d="M21 3v5h-5"></path><path d="M21 12a9 9 0 0 1 -9 9a9.75 9.75 0 0 1 -6.74 -2.74L3 16"></path><path d="M3 21v-5h5"></path></svg> Batalkan Peminjaman
          </button>
          <button type="button" id="btnHapus" onclick="if(confirm('Yakin ingin menghapus peminjaman yang ditolak ini?')) deletePeminjaman(currentPeminjamanId)" 
                  class="cancel-btn hidden">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7l16 0"></path><path d="M10 11l0 6"></path><path d="M14 11l0 6"></path><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path><path d="M9 7V4a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path></svg> Hapus Peminjaman
          </button>
        </div>
      </div>
    </form>
  </div>

  <script src="script.js"></script>
</html>
