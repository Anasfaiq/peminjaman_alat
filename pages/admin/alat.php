<?php
  session_start();
  include '../../config/conn.php';

  if (!isset($_SESSION['id_user'])) {
    die("Belum Login!");
  }

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
    <aside class="left-dashboard-section">
      <nav class="sidebar">
        <div>
          <h2>Peminjaman Alat</h2>
          <p>Admin Panel</p>
        </div>
        <ul>
          <li>
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
          <li>
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
              <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
              <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
              <path d="M16 3.13a4 4 0 0 1 0 7.75" />
              <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
            </svg>
            <a href="user.php">User Management</a>
          </li>
          <li>
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
              <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
              <path d="M12 12l8 -4.5" />
              <path d="M12 12l0 9" />
              <path d="M12 12l-8 -4.5" />
              <path d="M16 5.25l-8 4.5" />
            </svg>
            <a href="#">Alat Management</a>
          </li>
          <li>
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
                d="M9 3h3l2 2h5a2 2 0 0 1 2 2v7a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2"
              />
              <path
                d="M17 16v2a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-9a2 2 0 0 1 2 -2h2"
              />
            </svg>
            <a href="kategori.php">Kategori Management</a>
          </li>
          <li>
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
            <a href="data_peminjaman.php">Data Peminjaman</a>
          </li>
          <li>
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
            <a href="pengembalian.php">Pengembalian</a>
          </li>
          <li>
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
            <a href="log_aktivitas.php">Log Aktivitas</a>
          </li>
        </ul>
      </nav>
    </aside>

    <main class="right-dashboard-section">
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
            <a href="#" class="dropdown-item logout">
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
            <button>
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
                placeholder="Search alat..."
                class="pl-12 w-full"
              />
            </div>
            <select name="" id="">
              <option value="">All</option>
              <option value="">Computers</option>
              <option value="">Projectors</option>
              <option value="">Cameras</option>
              <option value="">Audio</option>
              <option value="">Tablets</option>
              <option value="">Drones</option>
              <option value="">Printers</option>
            </select>
          </div>
        </section>
        <section class="equipment-table-section">
          <table class="crud-table">
            <thead>
              <tr>
                <td class="table-header">Nama Alat</td>
                <td class="table-header">Kategori</td>
                <td class="table-header">Stok</td>
                <td class="table-header">Status</td>
                <td class="table-header">Action</td>
              </tr>
            </thead>
            <tbody>
              <?php
                $no = 1;
                while ($data = mysqli_fetch_assoc($query)) :
                  $status = $data['kondisi']; // ambil dari DB
                  if ($status == "Baik") {
                    $statusColor = "bg-green-200 text-green-800";
                  } elseif ($status == "Rusak Ringan") {
                    $statusColor = "bg-yellow-200 text-yellow-800";
                  } elseif ($status == "Rusak Berat") {
                    $statusColor = "bg-red-200 text-red-800";
                  } else {
                    $statusColor = "gray";
                  }
              ?>
              <tr>
                <td><?= $data['nama_alat']; ?> </td>
                <td><?= $data['nama_kategori'] ?></td>
                <td><?= $data['stok'] ?></td>
                <td class="equipment-status">
                  <span class="<?= $statusColor ?>"><?= $data['kondisi'] ?></span>
                </td>
                <td class="button-wrapper">
                  <a href="#" class="edit-button">
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
                        d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4"
                      />
                      <path d="M13.5 6.5l4 4" />
                    </svg>
                  </a>
                  <a href="#" class="delete-button">
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
    <script src="./script.js"></script>
  </body>
</html>
