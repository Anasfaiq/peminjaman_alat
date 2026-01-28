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
          <p>Sign in to your account</p>
          <form action="config/register.php" method="POST">
            <div class="input-group">
              <input
                type="text"
                id="username"
                class="peer"
                name="nama"
                placeholder=" "
                required
              />
              <label for="username">Nama</label>
            </div>
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
            <div class="input-group">
              <select
                id="role"
                class="peer"
                name="role"
                placeholder="Pilih Role"
                required
              >
                <option value="" disabled selected hidden>Pilih Role</option>
                <option value="admin">Admin</option>
                <option value="petugas">Petugas</option>
                <option value="peminjam">Peminjam</option>
              </select>
            </div>
            <button type="submit" name="register" class="btn">Sign In</button>
          </form>
        </div>
      </div>
    </main>

    <script src="script.js"></script>
  </body>
</html>
