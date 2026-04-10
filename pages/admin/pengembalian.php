<?php
session_start();
include '../../config/conn.php';
include '../../config/auth-check.php';

checkAuth('admin');

$id_user = $_SESSION['id_user'];

$query = mysqli_query(
    $conn,
    "SELECT pengembalian.*, users.nama as nama_peminjam, alat.nama_alat, peminjaman.tanggal_pinjam, peminjaman.tanggal_kembali_rencana, detail_peminjaman.jumlah AS jumlah FROM pengembalian
            JOIN peminjaman ON pengembalian.id_peminjaman = peminjaman.id_peminjaman
            JOIN users ON peminjaman.id_user = users.id_user
            JOIN detail_peminjaman ON peminjaman.id_peminjaman = detail_peminjaman.id_peminjaman
            JOIN alat ON detail_peminjaman.id_alat = alat.id_alat"
);

// ngambil nama user
$nama = mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_user'");
$data = mysqli_fetch_assoc($nama);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | Pengembalian</title>
    <link rel="stylesheet" href="../../src/output.css" />
  </head>
  <body class="flex gap-6 min-h-screen w-full py-8 px-14">
    <?php
      include '../components/sidebar.php'
    ?>

    <main class="right-dashboard-section">
      <nav class="navbar">
        <p>Data Pengembalian</p>
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
              <p><?= $data['nama'] ?></p>
              <span>Administrator</span>
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
            <a href="#" class="dropdown-item">
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
              Profile
            </a>
            <a href="#" class="dropdown-item">
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
                  d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"
                />
                <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
              </svg>
              Settings
            </a>
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
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert-container" id="alertNotif">
                    <div class="alert alert-success">
                      <div class="alert-content">
                        <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        '.$_SESSION['success'].'
                      </div>
                      <button id="closeAlertBtn" class="alert-close-btn" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                          <line x1="18" y1="6" x2="6" y2="18"/>
                          <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                      </button>
                    </div>
                  </div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert-container" id="alertNotif">
                    <div class="alert alert-error">
                      <div class="alert-content">
                        <svg class="alert-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"/>
                          <line x1="12" y1="8" x2="12" y2="12"/>
                          <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        '.$_SESSION['error'].'
                      </div>
                      <button id="closeAlertBtn" class="alert-close-btn" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;">
                          <line x1="18" y1="6" x2="6" y2="18"/>
                          <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                      </button>
                    </div>
                  </div>';
            unset($_SESSION['error']);
        }
        ?>
        <script>
          document.addEventListener('DOMContentLoaded', function() {
            const closeBtn = document.getElementById('closeAlertBtn');
            const container = document.getElementById('alertNotif');
            if (closeBtn && container) {
              closeBtn.addEventListener('click', function() {
                container.remove();
              });
            }
          });
        </script>
        <section class="search-section">
          <div class="top-equipment-section">
            <h3>Data Pengembalian</h3>
            <button id="addBtn">
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
                <path d="M12 5l0 14" />
                <path d="M5 12l14 0" />
              </svg>
              Add Pengembalian
            </button>
          </div>
        </section>
        <section class="equipment-table-section">
          <table class="crud-table">
            <thead>
              <tr>
                <td class="table-header">Borrower Name</td>
                <td class="table-header">Alat </td>
                <td class="table-header">Tanggal Pinjam</td>
                <td class="table-header">Tanggal Kembali Rencana</td>
                <td class="table-header">Tanggal Kembali</td>
                <td class="table-header">Kondisi</td>
                <td class="table-header">Action</td>
              </tr>
            </thead>
            <tbody>
              <?php
              $no = 1;
              while ($data = mysqli_fetch_assoc($query)) :
                  $kondisi_kembali = $data['kondisi_kembali'];
                  if ($kondisi_kembali == "Baik") {
                      $statusColor = "bg-green-200 text-green-800";
                  } elseif ($kondisi_kembali == "Rusak") {
                      $statusColor = "bg-red-200 text-red-800";
                  } else {
                      $statusColor = "bg-gray-200 text-gray-200";
                  }
              ?>
              <tr>
                <td><?= $data['nama_peminjam'] ?></td>
                <td><?= $data['nama_alat'] ?></td>
                <td><?= $data['tanggal_pinjam'] ?></td>
                <td><?= $data['tanggal_kembali_rencana'] ?></td>
                <td><?= $data['tanggal_kembali'] ?></td>
                <td class="equipment-status">
                  <span class="<?= $statusColor ?>"><?= $data['kondisi_kembali'] ?></span>
                </td>
                <td class="button-wrapper">
                  <a href="#" 
                     data-id="<?= $data['id_pengembalian']; ?>"
                     data-id-peminjaman="<?= $data['id_peminjaman']; ?>"
                     data-tanggal-kembali="<?= $data['tanggal_kembali']; ?>"
                     data-kondisi-kembali="<?= $data['kondisi_kembali']; ?>"
                     data-denda="<?= $data['denda']; ?>"
                     class="edit-button">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"/>
                      <path d="M13.5 6.5l4 4" />
                    </svg>
                  </a>
                  <a href="proses/proses-delete-pengembalian.php?id=<?= $data['id_pengembalian']; ?>" 
                     class="delete-button" 
                     onclick="return confirm('Yakin hapus pengembalian ini?')">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M4 7l16 0" />
                      <path d="M10 11l0 6" />
                      <path d="M14 11l0 6" />
                      <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                      <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                    </svg>
                  </a>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </section>
      </div>
    </main>
    <!-- backdrop -->
    <div id="modalBackdrop" class="backdrop hidden"></div>

    <!-- modal -->
    <div class="modal hidden" id="modal">
      <div class="modal-header">
        <h3 id="modalTitle">Add Pengembalian</h3>
        <button id="closeBtnX" type="button">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M18 6l-12 12"/>
            <path d="M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <form id="userForm" method="POST" action="proses/proses-add-pengembalian.php">
        <input type="hidden" id="id_pengembalian" name="id_pengembalian">
        <div class="form">
          <label>Peminjaman</label>
          <select name="id_peminjaman" id="id_peminjaman_select">
            <option value="" disabled hidden selected>Pilih peminjaman</option>
            <?php
            $peminjamanQuery = mysqli_query($conn, "SELECT p.id_peminjaman, u.nama, a.nama_alat FROM peminjaman p JOIN users u ON p.id_user = u.id_user JOIN detail_peminjaman dp ON p.id_peminjaman = dp.id_peminjaman JOIN alat a ON dp.id_alat = a.id_alat GROUP BY p.id_peminjaman");
            while ($pmj = mysqli_fetch_assoc($peminjamanQuery)) {
                echo '<option value="'.$pmj['id_peminjaman'].'">'.$pmj['nama'].' - '.$pmj['nama_alat'].'</option>';
            }
            ?>
          </select>
        </div>
        <div class="form">
          <label>Tanggal Kembali</label>
          <input name="tanggal_kembali" id="tanggal_kembali" type="date">
        </div>
        <div class="form">
          <label>Kondisi Saat Kembali</label>
          <select name="kondisi_kembali" id="kondisi_kembali">
            <option value="" disabled hidden selected>Pilih kondisi</option>
            <option value="Baik">Baik</option>
            <option value="Rusak">Rusak</option>
          </select>
        </div>
        <div class="form">
          <label>Denda</label>
          <input name="denda" id="denda" type="number" placeholder="Masukkan nominal denda" min="0">
        </div>
        <div class="button-group">
          <button class="cancel-btn" id="closeBtn" type="button">Cancel</button>
          <button class="simpan-btn" type="submit">Simpan</button>
        </div>
      </form>
    </div>
    <script src="./script.js"></script>
  </body>
</html>
