<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Peminjamana Alat | Login</title>
    <link rel="stylesheet" href="src/output.css" />
  </head>
  <body>
    <main class="login-container">
      <?php
        session_start();
        if (isset($_SESSION['error'])) {
            echo '<div class="login-error-container">
                    <div class="login-error-alert">
                      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                      </svg>
                      '.$_SESSION['error'].'
                    </div>
                  </div>';
            unset($_SESSION['error']);
        }
      ?>
      <div id="card" class="card">
        <div class="left">
          <h2>Sign In</h2>
          <p>Sign in to your account</p>
          <form action="config/auth.php" method="POST">
            <div class="input-group">
              <input
                type="text"
                id="username"
                class="peer"
                name="username"
                placeholder=" "
                required
              />
              <label for="username">Username</label>
            </div>
            <div class="input-group">
              <input
                type="password"
                id="password"
                class="peer"
                name="password"
                placeholder=" "
                required
              />
              <label for="password">Password</label>
            </div>
            <button type="submit" name="login" class="btn">Sign In</button>
          </form>
        </div>

        <div class="right">
          <h2 id="signUpHeader">Welcome Back!</h2>
          <p id="signUpText">
            To keep connected with us please login with your personal info
          </p>
          <button id="btnSignUp" class="btn">Sign Up</button>
        </div>

        <div class="right-form">
          <h2>Sign Up</h2>
          <p>Daftar sebagai Peminjam Alat</p>
          <form action="config/register.php" method="POST">
            <div class="input-group">
              <input
                type="text"
                id="signupNama"
                class="peer"
                name="nama"
                placeholder=" "
                required
              />
              <label for="signupNama">Nama</label>
            </div>
            <div class="input-group">
              <input
                type="text"
                id="signupUsername"
                class="peer"
                name="username"
                placeholder=" "
                required
              />
              <label for="signupUsername">Username</label>
            </div>
            <div class="input-group">
              <input
                type="password"
                id="signupPassword"
                class="peer"
                name="password"
                placeholder=" "
                required
              />
              <label for="signupPassword">Password</label>
            </div>
            <input type="hidden" name="role" value="peminjam" />
            <button type="submit" name="register" class="btn">Daftar</button>
          </form>
        </div>
      </div>
    </main>

    <script src="script.js"></script>
  </body>
</html>
