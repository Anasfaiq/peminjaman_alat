<?php
session_start();
include "../../config/conn.php";
include "../../config/auth-check.php";
include "../../config/logging.php";

checkAuth('admin');

$id_user = $_SESSION['id_user'];
$sql = mysqli_query($conn, "SELECT nama FROM users WHERE id_user='$id_user'");
$data = mysqli_fetch_assoc($sql);

//logic buat ngitung total user
$user_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_user = mysqli_fetch_assoc($user_query)['total'];

// logic buat ngitung total equipment
$eq_query = mysqli_query($conn, "SELECT SUM(stok) as total_stok FROM alat");
$total_alat = mysqli_fetch_assoc($eq_query)['total_stok'];

// logic buat ngitung active borrows
$br_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM peminjaman
                                                 WHERE status='Disetujui' AND id_peminjaman
                                                 NOT IN (SELECT id_peminjaman FROM pengembalian)");
$active_borrow = mysqli_fetch_assoc($br_query)['total'];

// ngambil recent log aktivitas
$recent_logs = getRecentLogs($conn, 10);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | Dashboard</title>
    <link rel="stylesheet" href="../../src/output.css" />
  </head>
  <body class="flex gap-6 min-h-screen w-full py-8 px-14">
    <?php
      if (isset($_SESSION['success'])) {
        echo '<div class="login-success-container" id="successNotif">
                <div class="login-success-alert">
                  <div class="login-success-alert-content">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    '.$_SESSION['success'].'
                  </div>
                  <button id="closeSuccessBtn" class="success-close-btn" type="button">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <line x1="18" y1="6" x2="6" y2="18"/>
                      <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                  </button>
                </div>
              </div>';
        unset($_SESSION['success']);
      }
    ?>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const closeBtn = document.getElementById('closeSuccessBtn');
        const container = document.getElementById('successNotif');
        if (closeBtn && container) {
          closeBtn.addEventListener('click', function() {
            container.remove();
          });
        }
      });
    </script>
    <?php
      include '../components/sidebar.php'
?>

    <main class="right-dashboard-section">
      <nav class="navbar">
        <p>Dashboard</p>
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
              <p><?=  $data['nama']; ?></p>
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

      <section class="dashboard-content">
        <section class="dashboard-cards">
          <div class="dashboard-card">
            <span class="card-text">
              <h3>Total User</h3>
              <p><?= $total_user ?></p>
            </span>
            <span class="total-equipment-icon icon-wrapper">
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
            </span>
          </div>

          <div class="dashboard-card">
            <span class="card-text">
              <h3>Total Equipment</h3>
              <p><?= $total_alat ?></p>
            </span>
            <span class="active-borrows-icon icon-wrapper">
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
            </span>
          </div>

          <div class="dashboard-card">
            <span class="card-text">
              <h3>Active Borrows</h3>
              <p><?= $active_borrow ?></p>
            </span>
            <span class="active-borrow-icon icon-wrapper">
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
                <path d="M9 5h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2h-2"/>
                <path d="M9 3m0 2a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v0a2 2 0 0 1 -2 2h-2a2 2 0 0 1 -2 -2z"/>
                <path d="M9 12l.01 0"/>
                <path d="M13 12l2 0"/>
                <path d="M9 16l.01 0"/>
                <path d="M13 16l2 0"/>
              </svg>
            </span>
          </div>
        </section>

        <section class="recent-activity-card">
          <h3>Recent Activity</h3>
          <?php
        if ($recent_logs) {
            while ($log = mysqli_fetch_assoc($recent_logs)) {
                $waktuFormatted = formatWaktu($log['waktu']);
                echo '<div class="activity-wrapper">';
                echo '  <div class="profile"></div>';
                echo '  <div class="activity">';
                echo '    <p>';
                echo '      <span class="username">'.$log['nama_user'].'</span>';
                echo '      <span class="user-activity">'.$log['aktivitas'].'</span>';
                echo '    </p>';
                echo '    <p class="activity-time">'.$waktuFormatted.'</p>';
                echo '  </div>';
                echo '</div>';
            }
        } else {
            echo '<p class="text-center py-4 text-gray-500">Tidak ada aktivitas</p>';
        }
?>
        </section>
      </section>
    </main>
    <script src="./script.js"></script>
  </body>
</html>
