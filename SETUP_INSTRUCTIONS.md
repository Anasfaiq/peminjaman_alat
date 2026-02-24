# Setup Instructions - Role Petugas

## Langkah-langkah Implementasi

### 1. Update Tabel Users (Database)

Pastikan kolom `role` sudah ada di tabel `users` dengan tipe `enum`:

```sql
ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'petugas', 'peminjam') DEFAULT 'peminjam';
```

Jika kolom belum ada, tambahkan:

```sql
ALTER TABLE users ADD COLUMN role ENUM('admin', 'petugas', 'peminjam') DEFAULT 'peminjam' AFTER password;
```

Insert sample data petugas untuk testing:

```sql
INSERT INTO users (nama, username, password, role)
VALUES ('Petugas Alat 1', 'petugas1', '123456', 'petugas');
```

---

### 2. Update File Login (`config/auth.php` atau `index.php`)

Ketika user berhasil login, set session role:

**SEBELUM:**

```php
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "db_peminjaman");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        $_SESSION['id_user'] = $user['id_user'];
        // ... redirect
    }
}
?>
```

**SESUDAH (tambahkan role):**

```php
<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "db_peminjaman");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    $user = mysqli_fetch_assoc($query);

    if ($user) {
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['role'] = $user['role'];  // ← TAMBAH INI

        // Redirect sesuai role
        if ($user['role'] === 'admin') {
            header("Location: pages/admin/dashboard.php");
        } elseif ($user['role'] === 'petugas') {
            header("Location: pages/petugas/dashboard.php");  // ← TAMBAH REDIRECT INI
        } elseif ($user['role'] === 'peminjam') {
            header("Location: pages/peminjam/dashboard.php");
        }
        exit();
    } else {
        $error = "Username atau password salah";
    }
}
?>
```

---

### 3. Test Flow

#### Test 1: Login sebagai Admin

1. Buka `http://localhost/peminjaman_alat/index.php`
2. Login dengan user yang punya role='admin'
3. Harus redirect ke `/pages/admin/dashboard.php`
4. Sidebar menampilkan semua menu

#### Test 2: Login sebagai Petugas

1. Buka `http://localhost/peminjaman_alat/index.php`
2. Login dengan user yang punya role='petugas'
3. Harus redirect ke `/pages/petugas/dashboard.php`
4. Sidebar hanya menampilkan:
   - Dashboard
   - Persetujuan Peminjaman
   - Monitoring Pengembalian
   - Laporan

#### Test 3: Akses Tanpa Login

1. Buka langsung `http://localhost/peminjaman_alat/pages/petugas/dashboard.php` tanpa login
2. Harus redirect ke login page

#### Test 4: Admin Akses Petugas Page

1. Login sebagai admin
2. Coba akses `http://localhost/peminjaman_alat/pages/petugas/dashboard.php`
3. Harus diredirect ke login karena role tidak sesuai

---

### 4. File-file yang Sudah Dibuat

✓ **New Files:**

- `pages/petugas/dashboard.php`
- `pages/petugas/persetujuan_peminjaman.php`
- `pages/petugas/monitoring_pengembalian.php`
- `pages/petugas/laporan.php`
- `pages/petugas/proses/proses-update-status-peminjaman.php`
- `pages/petugas/proses/proses-get-detail-peminjaman.php`
- `pages/components/sidebar_petugas.php`
- `config/auth-check.php`

✓ **Documentation:**

- `PETUGAS_GUIDE.md`
- `SETUP_INSTRUCTIONS.md` (file ini)

---

### 5. Struktur Query & Flow

#### Approval Flow

```
User klik "Persetujuan Peminjaman"
  ↓
Load list peminjaman status='Menunggu'
  ↓
User click "Lihat Detail" pada satu peminjaman
  ↓
AJAX call ke proses-get-detail-peminjaman.php
  ↓
Return JSON array detail alat
  ↓
Modal muncul dengan detail
  ↓
User click "Setujui" atau "Tolak"
  ↓
AJAX call ke proses-update-status-peminjaman.php (POST)
  ↓
Update peminjaman.status di database
  ↓
Auto log aktivitas ke log_aktivitas
  ↓
Return JSON success/fail
  ↓
JavaScript reload page
  ↓
Status terupdate di tabel
```

---

### 6. Troubleshooting

**Error: "Belum Login" ketika buka halaman petugas**

- Solusi: Pastikan sudah login terlebih dahulu
- Cek: `$_SESSION['id_user']` ada tidak

**Error: "Akses Ditolak" ketika buka halaman petugas**

- Solusi: Login dengan user yang punya role='petugas'
- Cek: `$_SESSION['role']` value-nya

**Modal tidak muncul saat klik "Lihat Detail"**

- Cek: Browser console (F12)
- Pastikan `script.js` sudah di-load
- Pastikan proses-get-detail-peminjaman.php response valid JSON

**Update status tidak bekerja**

- Cek: Network tab di Browser Developer Tools
- Pastikan POST ke proses-update-status-peminjaman.php berhasil
- Cek: Database sudah terupdate atau tidak

**Sidebar tidak menampilkan menu dengan benar**

- Pastikan menggunakan `sidebar_petugas.php` di halaman petugas
- Jangan menggunakan `sidebar.php` (itu untuk admin)

---

### 7. File yang Perlu Dimodifikasi

#### `config/logout.php`

Pastikan sudah ada handler logout yang bersih:

```php
<?php
session_start();
session_destroy();
header("Location: ../../index.php");
exit();
?>
```

#### `script.js`

Pastikan sudah ada fungsi untuk dropdown menu. Jika belum, tambahkan:

```javascript
document.addEventListener("DOMContentLoaded", function () {
  const userBtn = document.getElementById("userBtn");
  const userDropdownMenu = document.querySelector(".user-dropdown-menu");

  if (userBtn) {
    userBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      userDropdownMenu?.classList.toggle("hidden");
      userBtn.classList.toggle("active");
    });
  }

  document.addEventListener("click", function () {
    userDropdownMenu?.classList.add("hidden");
    userBtn?.classList.remove("active");
  });
});
```

---

### 8. Security Notes

1. **Never Trust Client-Side Role Check**
   - Selalu validasi role di server-side
   - Gunakan `checkAuth('petugas')` di setiap halaman

2. **SQL Injection Prevention**
   - Gunakan prepared statements (sudah diimplementasikan)
   - Htmlspecialchars() untuk output

3. **Session Hijacking**
   - Regenerate session ID setelah login
   - Pastikan session timeout di setting

4. **CSRF Protection** (Optional)
   - Pertimbangkan tambah CSRF token untuk form

---

### 9. Fitur Bawaan yang Terproteksi

✓ **Semua halaman petugas sudah terproteksi:**

- Check session login
- Check role = 'petugas'
- Redirect jika tidak sesuai

✓ **Setiap proses juga terproteksi:**

- `proses-update-status-peminjaman.php` → checkAuth('petugas')
- `proses-get-detail-peminjaman.php` → checkAuth('petugas')

---

### 10. Next Steps (Opsional)

Untuk fase berikutnya, buat role "peminjam" di:

- `pages/peminjam/dashboard.php`
- `pages/peminjam/buat_peminjaman.php`
- Dll

---

## Checklist Implementasi

- [ ] Update tabel users (add role enum)
- [ ] Insert user dengan role='petugas' untuk testing
- [ ] Update file login untuk set `$_SESSION['role']`
- [ ] Test login sebagai admin → ok
- [ ] Test login sebagai petugas → ok
- [ ] Test akses petugas page tanpa login → redirect ok
- [ ] Test akses petugas page sebagai admin → redirect ok
- [ ] Test approval flow (approve/reject)
- [ ] Test monitoring pengembalian filter
- [ ] Test laporan bulan/tahun
- [ ] Test print laporan
- [ ] Test logout → session cleared
- [ ] Test log aktivitas tercatat
- [ ] Review code untuk security issues
- [ ] Deploy ke production

---

**Last Updated:** 2026-02-17  
**Status:** Ready for Implementation
