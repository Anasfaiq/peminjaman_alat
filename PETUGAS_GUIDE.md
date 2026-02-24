# Panduan Implementasi Role "Petugas"

## Struktur Folder

```
pages/
├── admin/                          # Halaman Admin
├── petugas/                        # Halaman Petugas (NEW)
│   ├── dashboard.php               # Dashboard petugas
│   ├── persetujuan_peminjaman.php  # Halaman approval
│   ├── monitoring_pengembalian.php # Halaman monitoring return
│   ├── laporan.php                 # Halaman laporan
│   └── proses/
│       ├── proses-update-status-peminjaman.php      # Update status
│       └── proses-get-detail-peminjaman.php         # Get detail
├── components/
│   ├── sidebar.php                 # Sidebar admin
│   └── sidebar_petugas.php         # Sidebar petugas (NEW)
└── peminjam/                       # (Untuk fase berikutnya)
```

## Fitur Petugas

### 1. Dashboard (`dashboard.php`)

- **Statistik Real-time:**
  - Peminjaman menunggu persetujuan
  - Peminjaman aktif (sudah disetujui, belum dikembalikan)
  - Pengembalian hari ini
  - Total peminjaman ditolak

- **Quick Links:**
  - Persetujuan Peminjaman
  - Monitoring Pengembalian
  - Cetak Laporan

- **Recent Pending:**
  - Tabel 5 peminjaman terakhir yang menunggu

### 2. Persetujuan Peminjaman (`persetujuan_peminjaman.php`)

- **Filter Status:**
  - Menunggu (peminjaman yang perlu disetujui)
  - Disetujui
  - Ditolak

- **Action:**
  - Lihat detail alat yang dipinjam
  - Setujui atau tolak peminjaman
  - Automatic logging aktivitas

### 3. Monitoring Pengembalian (`monitoring_pengembalian.php`)

- **Statistik:**
  - Total pengembalian
  - Pengembalian terlambat
  - Alat yang rusak
  - Total denda

- **Filter:**
  - Semua pengembalian
  - Terlambat
  - Rusak

### 4. Laporan (`laporan.php`)

- **Filter:**
  - Pilih bulan dan tahun
  - Data otomatis diupdate

- **Statistik Laporan:**
  - Total peminjaman (bulan terpilih)
  - Peminjaman disetujui
  - Peminjaman ditolak
  - Data pengembalian, terlambat, rusak
  - Total denda

- **Fitur Print:**
  - Klik tombol "Cetak" untuk print laporan

## Proteksi & Autentikasi

### 1. Session-based Auth (`config/auth-check.php`)

```php
// Import di setiap halaman petugas
include "../../config/auth-check.php";

// Gunakan untuk proteksi
checkAuth('petugas');
```

### 2. Helper Functions

```php
checkAuth($role)        // Cek login & role
isPetugas()            // Boolean check
isAdmin()              // Boolean check
isPeminjam()           // Boolean check
getUserById($conn, $id) // Get user data
```

### 3. Redirect Flow

- User akses halaman petugas TANPA login → redirect ke login
- User login sebagai ADMIN/PEMINJAM → redirect ke login

## Database Queries

### Query yang Digunakan

**1. Dashboard - Peminjaman Menunggu:**

```sql
SELECT COUNT(*) as total FROM peminjaman WHERE status='Menunggu'
```

**2. Dashboard - Peminjaman Aktif:**

```sql
SELECT COUNT(*) as total FROM peminjaman p
WHERE p.status='Disetujui'
AND p.id_peminjaman NOT IN (SELECT id_peminjaman FROM pengembalian)
```

**3. Persetujuan - List Peminjaman:**

```sql
SELECT p.*, u.nama, COUNT(d.id_detail) as jumlah_alat
FROM peminjaman p
JOIN users u ON p.id_user = u.id_user
LEFT JOIN detail_peminjaman d ON p.id_peminjaman = d.id_peminjaman
WHERE p.status = ?
GROUP BY p.id_peminjaman
```

**4. Monitoring - Pengembalian Terlambat:**

```sql
SELECT * FROM pengembalian pg
JOIN peminjaman p ON pg.id_peminjaman = p.id_peminjaman
WHERE pg.tanggal_kembali > p.tanggal_kembali_rencana
```

## Proses AJAX

### proses-update-status-peminjaman.php

- **Method:** POST
- **Parameters:**
  - `id_peminjaman` (int)
  - `status` (string: 'Disetujui' atau 'Ditolak')
- **Response:** JSON
  - `success` (boolean)
  - `message` (string)

### proses-get-detail-peminjaman.php

- **Method:** GET
- **Parameters:**
  - `id` (int - peminjaman ID)
- **Response:** JSON Array
  - `id_detail`
  - `jumlah`
  - `nama_alat`

## User Flow

### 1. Login

```
User (browser)
  → POST /index.php (username + password)
  → Set $_SESSION['id_user']
  → Set $_SESSION['role']
  → Redirect ke /pages/petugas/dashboard.php (jika role=petugas)
```

### 2. Approval Process

```
Dashboard
  → Click "Persetujuan Peminjaman"
  → View list peminjaman status "Menunggu"
  → Click "Lihat Detail"
  → Modal muncul dengan detail alat
  → Click "Setujui" atau "Tolak"
  → AJAX POST ke proses-update-status-peminjaman.php
  → Status diupdate di database
  → Log aktivitas otomatis
  → Page reload
```

### 3. Monitoring

```
Dashboard
  → Click "Monitoring Pengembalian"
  → Filter: Semua / Terlambat / Rusak
  → View tabel pengembalian dengan status
```

### 4. Laporan

```
Dashboard
  → Click "Laporan"
  → Select bulan & tahun
  → View statistik
  → Click "Cetak" untuk print
```

## Hak Akses Petugas

### ✓ DIPERBOLEHKAN:

- ✓ Login / Logout
- ✓ Melihat dashboard
- ✓ Menyetujui peminjaman (ubah status Menunggu → Disetujui)
- ✓ Menolak peminjaman (ubah status Menunggu → Ditolak)
- ✓ Memantau pengembalian
- ✓ Melihat laporan bulanan
- ✓ Cetak laporan

### ✗ TIDAK DIPERBOLEHKAN:

- ✗ CRUD user
- ✗ CRUD kategori
- ✗ CRUD alat
- ✗ Melihat log aktivitas
- ✗ Mengubah data yang sudah disetujui/ditolak

## Integrasi dengan Sistem Existing

### 1. Sidebar Mapping

Petugas TIDAK akan melihat menu:

- User Management
- Alat Management
- Kategori Management
- Log Aktivitas

Petugas HANYA melihat:

- Dashboard
- Persetujuan Peminjaman
- Monitoring Pengembalian
- Laporan

### 2. Logging

Setiap aksi petugas (setujui/tolak) dicatat di `log_aktivitas`:

```php
logAktivitas($conn, $id_user, "Mengubah status peminjaman menjadi Disetujui", 'peminjaman', $id_peminjaman);
```

### 3. CSS Reuse

- Menggunakan CSS dari `src/output.css` (Tailwind)
- Layout dan komponen sidebar sama dengan admin
- Hanya menu sidebar yang berbeda

## Testing Checklist

- [ ] Login sebagai petugas → berhasil redirect ke `/pages/petugas/dashboard.php`
- [ ] Login sebagai admin → tetap bisa akses `/pages/admin/dashboard.php`
- [ ] Akses `/pages/petugas/dashboard.php` tanpa login → redirect ke login
- [ ] Dashboard menampilkan statistik yang benar
- [ ] Bisa melihat peminjaman menunggu
- [ ] Bisa approve/reject peminjaman
- [ ] Modal detail alat muncul saat klik "Lihat Detail"
- [ ] Monitoring pengembalian menampilkan filter
- [ ] Laporan menampilkan data sesuai bulan/tahun
- [ ] Tombol print berfungsi
- [ ] Logout berfungsi
- [ ] Log aktivitas tercatat untuk setiap aksi approve/reject

## Catatan Penting

1. **Session Role Harus Disetting di Login:**
   - Update file login (`index.php` atau `config/auth.php`) untuk menyimpan `$_SESSION['role']`
2. **Database Role di Tabel Users:**
   - Pastikan kolom `role` di tabel `users` ada
   - Values: 'admin', 'petugas', 'peminjam'

3. **Script External:**
   - Pastikan `script.js` sudah ada untuk dropdown menu

4. **Responsive Design:**
   - Dashboard menggunakan Tailwind CSS (dari output.css)
   - Sudah responsive untuk mobile

## Pengembangan Selanjutnya

1. **Peminjam Role:** Buat halaman peminjam dengan CRUD peminjaman
2. **Notifikasi:** Tambah email notifikasi saat peminjaman disetujui/ditolak
3. **Dashboard dinamis:** Tambah grafik untuk visualisasi data
4. **Export PDF:** Tambah fitur export laporan ke PDF
5. **Mobile App:** Buat mobile app untuk approval lebih mudah

---

**Dibuat untuk:** Sistem Peminjaman Alat Berbasis PHP
**Tanggal:** 2026-02-17
**Versi:** 1.0
