<?php
session_start();
include '../../config/conn.php';
include '../../config/auth-check.php';

checkAuth('admin');

$id_user = $_SESSION['id_user'];
$query = mysqli_query($conn, "SELECT alat.*, kategori.nama_kategori FROM alat
                                              JOIN kategori ON alat.id_kategori = kategori.id_kategori");

$qUser = mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_user'");
$data = mysqli_fetch_assoc($qUser);
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | Equipment</title>
    <link rel="stylesheet" href="../../src/output.css" />
  </head>
  <body class="flex gap-6 min-h-screen w-full py-8 px-14">
    <?php
      include '../components/sidebar.php'
?>

    <main class="right-dashboard-section">
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
      <nav class="navbar">
        <p>Alat Management</p>
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
        <section class="search-section">
          <div class="top-equipment-section">
            <h3>Alat Management</h3>
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
              Add Alat
            </button>
          </div>
          <div class="bottom-equipment-section">
            <div class="flex-1 relative">
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
                class="absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none opacity-50"
              >
                <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                <path d="M21 21l-6 -6" />
              </svg>
              <input
                type="text"
                id="searchInput"
                placeholder="Search alat..."
                class="pl-12 w-full"
              />
            </div>
            <select name="categoryFilter" id="categoryFilter">
              <option value="">All Categories</option>
              <?php
                $kategoriQuery = mysqli_query($conn, "SELECT DISTINCT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori");
                while ($kat = mysqli_fetch_assoc($kategoriQuery)) {
                    echo '<option value="'.$kat['id_kategori'].'">'.$kat['nama_kategori'].'</option>';
                }
              ?>
            </select>
          </div>
        </section>
        <section class="equipment-table-section">
          <table class="crud-table">
            <thead>
              <tr>
                <td class="table-header">Nama Alat</td>
                <td class="table-header">Kategori</td>
                <td class="table-header">Harga Barang</td>
                <td class="table-header">Harga Sewa per Hari</td>
                <td class="table-header">Stok</td>
                <td class="table-header">Action</td>
              </tr>
            </thead>
            <tbody>
              <?php
          $no = 1;
while ($data = mysqli_fetch_assoc($query)) :
    ?>
              <tr data-id-kategori="<?= $data['id_kategori']; ?>">
                <td><?= $data['nama_alat']; ?> </td>
                <td><?= $data['nama_kategori'] ?></td>
                <td><?= $data['harga_barang'] ?></td>
                <td><?= $data['harga_sewa'] ?></td>
                <td><?= $data['stok'] ?></td>
                <td class="button-wrapper">
                  <a
                    href="#"
                    class="edit-button"
                    data-id="<?= $data['id_alat']; ?>"
                    data-nama-alat="<?= $data['nama_alat']; ?>"
                    data-id-kategori="<?= $data['id_kategori']; ?>"
                    data-harga-barang="<?= $data['harga_barang']; ?>"
                    data-harga-sewa="<?= $data['harga_sewa']; ?>"
                    data-stok="<?= $data['stok']; ?>"
                  >
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
                      <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                      <path d="M13.5 6.5l4 4" />
                    </svg>
                  </a>
                  <a href="proses/proses-delete-alat.php?id=<?= $data['id_alat']; ?>" 
                     class="delete-button" 
                     onclick="return confirm('Yakin hapus alat ini?')">
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
                      <path d="M4 7l16 0" />
                      <path d="M10 11l0 6" />
                      <path d="M14 11l0 6" />
                      <path
                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"
                      />
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
        <h3 id="modalTitle">Add Alat</h3>
        <button id="closeBtn">
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
            <path d="M18 6l-12 12"/>
            <path d="M6 6l12 12"/>
          </svg>
        </button>
      </div>
      <form id="userForm" method="POST" action="proses/proses-add-alat.php">
        <input type="hidden" id="id_alat" name="id_alat">

        <div class="form">
          <label>Nama Alat</label>
          <input name="nama_alat" id="nama_alat" type="text" placeholder="Masukkan nama alat">
        </div>

        <div class="form">
          <label>Kategori</label>
          <select name="id_kategori" id="id_kategori">
            <option value="" disabled hidden selected>Pilih kategori</option>
            <?php
    $kategoriQuery = mysqli_query($conn, "SELECT * FROM kategori");
while ($kat = mysqli_fetch_assoc($kategoriQuery)) {
    echo '<option value="'.$kat['id_kategori'].'">'.$kat['nama_kategori'].'</option>';
}
?>
          </select>
        </div>

        <div class="form">
          <label>Harga Barang</label>
          <input name="harga_barang" id="harga_barang" type="number" placeholder="Masukkan harga barang" min="0">
        </div>

        <div class="form">
          <label>Harga Sewa per Hari</label>
          <input name="harga_sewa" id="harga_sewa" type="number" placeholder="Masukkan harga sewa per hari" min="0">
        </div>

        <div class="form">
          <label></label>Stok</label>
          <input name="stok" id="stok" type="number" placeholder="Masukkan jumlah stok" min="0">
        </div>

        <div class="button-group">
          <button class="cancel-btn" id="closeBtn" type="button">Cancel</button>
          <button class="simpan-btn" type="submit">Simpan</button>
        </div>
      </form>
    </div>

    <script src="./script.js"></script>
    
    <script>
      // Search dan Filter Functionality
      document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const categoryFilter = document.getElementById('categoryFilter');
        const tableRows = document.querySelectorAll('tbody tr');

        function filterTable() {
          const searchTerm = searchInput.value.toLowerCase();
          const selectedCategory = categoryFilter.value;

          tableRows.forEach(row => {
            // Get data dari row
            const namaAlat = row.cells[0].textContent.toLowerCase();
            const kategori = row.getAttribute('data-kategori') || row.cells[1].textContent;
            const idKategori = row.getAttribute('data-id-kategori');

            // Cek search term
            const matchesSearch = namaAlat.includes(searchTerm);

            // Cek category filter
            const matchesCategory = !selectedCategory || idKategori === selectedCategory;

            // Show/hide row
            if (matchesSearch && matchesCategory) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        }

        // Event listeners
        searchInput.addEventListener('input', filterTable);
        categoryFilter.addEventListener('change', filterTable);
      });
    </script>
  </body>
</html>
