<?php
  session_start();
  include '../../config/conn.php';

  if (!isset($_SESSION['id_user'])) {
    die("Belum Login!");
  }

  $id_user = $_SESSION['id_user'];
  $query = mysqli_query($conn, "SELECT * FROM users");

  $query2 = mysqli_query($conn, "SELECT * FROM users WHERE id_user='$id_user'");
  $data = mysqli_fetch_assoc($query2)
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjaman Alat | User</title>
    <link rel="stylesheet" href="../../src/output.css" />
  </head>
  <body class="relative flex gap-6 min-h-screen w-full py-8 px-14">
    
    <?php 
      include '../components/sidebar.php'
    ?>

    <main class="right-dashboard-section">
      <nav class="navbar">
        <p>User Management</p>
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
              <p><?= $data['nama']; ?></p>
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
            <h3>User Management</h3>
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
              Add User
            </button>
          </div>
        </section>
        <section class="equipment-table-section">
          <table class="crud-table">
            <thead>
              <tr>
                <td class="table-header">No</td>
                <td class="table-header">Name</td>
                <td class="table-header">Username</td>
                <td class="table-header">Role</td>
                <td class="table-header">Action</td>
              </tr>
            </thead>
            <tbody>
              <?php
                $no = 1;
                while($row = mysqli_fetch_assoc($query)) : 
              ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['nama']; ?></td>
                <td><?= $row['username']; ?></td>
                <td><?= $row['role']; ?></td>
                <td class="button-wrapper">
                  <a href="#"
                     data-id="<?= $row['id_user']; ?>"
                     data-nama="<?= $row['nama']; ?>"
                     data-username="<?= $row['username']; ?>"
                     data-role="<?= $row['role']; ?>"
                     class="edit-button">
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
                  <a href="proses/proses-delete-user.php?id=<?= $row['id_user']; ?>" class="delete-button" onclick="return confirm('Yakin hapus user ini?')">
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
        <h3 id="modalTitle">Add User Form</h3>
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
      <form id="userForm" method="POST" action="proses/proses-add-user.php">
        <input type="hidden" id="id_user" name="id_user">
        <div class="form">
          <label>Name</label>
          <input name="nama" id="nama" type="text" placeholder="Masukkan nama lengkap anda">
        </div>
        <div class="form">
          <label>Username</label>
          <input name="username" id="username" type="text" placeholder="Masukkan username">
        </div>
        <div class="form">
          <label>Password</label>
          <input name="password" id="password" type="password" placeholder="Masukkan password">
          <small id="passwordHint" class="hidden">
            *Kosongkan jika tidak ingin mengubah password
          </small>
        </div>
        <div class="form">
          <label>Pilih Role</label>
          <select name="role" id="role">
            <option value="" disabled hidden selected>Pilih role yang anda inginkan</option>
            <option value="admin">Admin</option>
            <option value="petugas">Petugas</option>
            <option value="peminjam">Peminjam</option>
          </select>
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
