# Tutorial Hosting ke Infinity Free

Panduan lengkap untuk meng-hosting aplikasi POS Besi & Kayu ke Infinity Free.

---

## 📋 Persiapan

### 1. Kompres Project Menjadi ZIP

**Di Windows:**
1. Buka folder `pos-besi-kayu` (folder yang berisi `public`, `assets`, `controllers`, dll)
2. Pilih **SEMUA** file dan folder (jangan pilih folder induk, tapi isinya)
3. Klik kanan → **Kirim ke** → **Folder terkompresi (zip)**
4. Beri nama `website.zip`

**Struktur yang benar saat di-ZIP:**
```
website.zip
├── public/
├── assets/
├── controllers/
├── models/
├── views/
├── config/
├── helpers/
├── database/
├── .htaccess
└── README.md
```

---

## 🌐 Membuat Akun Infinity Free

1. Buka [https://app.infinityfree.com/signup](https://app.infinityfree.com/signup)
2. Daftar dengan email aktif
3. Verifikasi email Anda
4. Login ke Control Panel

---

## 🗄️ Setup Database

### Langkah 1: Buat Database
1. Di Control Panel Infinity Free, klik **MySQL Databases**
2. Klik **Create Database**
3. Isi nama database (contoh: `db_pos_besi_kayu`)
4. Klik **Create**
5. **CATAT** informasi berikut (akan muncul di halaman):
   - `DB_HOST` (misal: `sql308.infinityfree.com`)
   - `DB_USER` (misal: `if0_41029338`)
   - `DB_PASS` (password yang Anda set)
   - `DB_NAME` (misal: `if0_41029338_db_pos_besi_kayu`)

### Langkah 2: Import Database
1. Di halaman **MySQL Databases**, klik **Manage** atau **phpMyAdmin**
2. Login dengan kredensial database Anda
3. Pilih database yang baru dibuat (di sidebar kiri)
4. Klik tab **SQL**
5. **Import Schema:** 
   - Buka file lokal `database/schema.sql` dengan Notepad
   - Copy semua isinya
   - Paste ke tab SQL di phpMyAdmin
   - Klik **Go**
6. **Import Seed Data:**
   - Buka file lokal `database/seed.sql` dengan Notepad
   - Copy semua isinya
   - Paste ke tab SQL di phpMyAdmin
   - Klik **Go**

> ⚠️ **Penting:** Jika muncul error "user admin doesn't exist", abaikan untuk sementara, kita akan fix nanti.

---

## 📤 Upload Files

### Langkah 1: Akses File Manager
1. Di Control Panel, klik **File Manager**
2. Navigasi ke folder `htdocs`

### Langkah 2: Upload ZIP
1. Klik tombol **Upload** (icon upload di toolbar)
2. Pilih file `website.zip` yang sudah disiapkan
3. Tunggu hingga upload 100% selesai
4. Tutup jendela upload

### Langkah 3: Extract ZIP
1. Kembali ke File Manager → folder `htdocs`
2. Klik kanan file `website.zip`
3. Pilih **Extract**
4. Tunggu hingga proses extract selesai
5. **Hapus** file `website.zip` (opsional, untuk menghemat space)

### Langkah 4: Verifikasi Struktur
Pastikan di dalam `htdocs` terdapat:
```
htdocs/
├── public/
├── assets/
├── controllers/
├── models/
├── views/
├── config/
├── helpers/
├── database/
├── .htaccess
└── README.md
```

> ❌ **SALAH:** Jangan sampai ada nested folder seperti `htdocs/pos-besi-kayu/public/`

---

## ⚙️ Konfigurasi Database

### Edit file `config/config.php`

1. Di File Manager, navigasi ke `htdocs/config/`
2. Klik kanan file `config.php` → **Edit**
3. Ganti bagian database (baris 3-7) dengan kredensial Infinity Free Anda:

```php
<?php

// Konfigurasi Database (INFINITY FREE)
define('DB_HOST', 'sql308.infinityfree.com');        // Ganti dengan DB_HOST Anda
define('DB_USER', 'if0_41029338');                   // Ganti dengan DB_USER Anda
define('DB_PASS', 'PasswordAndaDisini');             // Ganti dengan DB_PASS Anda
define('DB_NAME', 'if0_41029338_db_pos_besi_kayu');  // Ganti dengan DB_NAME Anda
```

4. **PENTING:** Pastikan `<?php` tetap ada di baris paling atas
5. Klik **Save Changes**

---

## 🔐 Reset Password Admin (Troubleshooting)

Jika login gagal dengan `admin` / `admin123`, kemungkinan password hash tidak kompatibel dengan versi PHP hosting.

### Solusi: Reset via phpMyAdmin

1. Buka **phpMyAdmin** dari Control Panel
2. Pilih database Anda
3. Klik tab **SQL**
4. Jalankan query berikut:

```sql
-- Hapus user admin lama
DELETE FROM users WHERE username = 'admin';

-- Buat user admin baru dengan password yang di-generate langsung di server
INSERT INTO users (username, password, role) 
VALUES ('admin', MD5('admin123'), 'admin');
```

> ⚠️ **Catatan Keamanan:** MD5 lebih lemah dari bcrypt, tapi lebih kompatibel di shared hosting. Ubah password setelah berhasil login pertama kali.

### Alternatif: Buat Script Reset

1. Buat file baru di `htdocs` dengan nama `reset_admin.php`
2. Isi dengan:

```php
<?php
require_once __DIR__ . '/config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $newPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = :pass WHERE username = 'admin'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':pass' => $newPassword]);
    
    echo "✅ Password reset berhasil! Gunakan: admin / admin123";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

3. Akses `namadomainanda.com/reset_admin.php` di browser
4. Setelah muncul "berhasil", **HAPUS FILE INI** untuk keamanan
5. Coba login

---

## 🧪 Testing Website

1. Buka browser, akses domain Anda (contoh: `webcrudilham.infinityfreeapp.com`)
2. Seharusnya muncul halaman **Login POS**
3. Masukkan kredensial:
   - Username: `admin`
   - Password: `admin123`
4. Jika berhasil, Anda akan masuk ke Dashboard

### Checklist Testing:
- [ ] Halaman Login tampil dengan CSS yang benar
- [ ] Login berhasil dengan `admin` / `admin123`
- [ ] Halaman Dashboard/Kategori muncul
- [ ] Menu Sidebar bisa diklik
- [ ] Gambar produk (jika ada) tampil
- [ ] Fitur CRUD (Create, Read, Update, Delete) berfungsi
- [ ] Transaksi POS bisa dilakukan
- [ ] Laporan tampil dengan grafik

---

## 🐛 Troubleshooting Umum

### 1. Halaman Blank / Error 500
**Penyebab:** Error PHP atau file `config.php` salah.

**Solusi:**
- Pastikan `config.php` tidak ada syntax error
- Cek kredensial database sudah benar
- Aktifkan error reporting dengan menambahkan di `config.php`:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

### 2. CSS Tidak Tampil / Halaman Polos
**Penyebab:** File `.htaccess` salah atau folder `assets` tidak ter-upload.

**Solusi:**
- Pastikan file `.htaccess` ada di folder `htdocs`
- Isi `.htaccess` harus seperti ini:
  ```apache
  <IfModule mod_rewrite.c>
      RewriteEngine On
      RewriteRule ^assets/ - [L]
      RewriteRule ^$ public/ [L]
      RewriteRule (.*) public/$1 [L]
  </IfModule>
  ```

### 3. Error 404 Not Found
**Penyebab:** Struktur folder salah atau `.htaccess` tidak berfungsi.

**Solusi:**
- Pastikan struktur folder benar (lihat bagian "Upload Files")
- Pastikan `mod_rewrite` aktif (biasanya sudah default di Infinity Free)

### 4. Database Connection Failed
**Penyebab:** Kredensial database salah.

**Solusi:**
- Periksa kembali `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME` di `config.php`
- Pastikan database sudah dibuat di Control Panel
- **Jangan gunakan `localhost`** sebagai `DB_HOST`

### 5. Login Gagal Terus
**Penyebab:** Password hash tidak kompatibel atau user tidak ada di database.

**Solusi:**
- Cek tabel `users` di phpMyAdmin, pastikan ada user `admin`
- Gunakan script reset password (lihat bagian "Reset Password Admin")

### 6. Upload Gambar Gagal
**Penyebab:** Folder `assets/uploads/products/` tidak ada atau tidak writable.

**Solusi:**
- Buat folder `assets/uploads/products/` via File Manager
- Set permission folder menjadi `755` (klik kanan → Permission/Chmod)

---

## 📌 Catatan Penting

### Batasan Infinity Free:
- ⏱️ **Execution Time:** Max 30 detik per request
- 💾 **File Upload:** Max 10MB per file
- 🗄️ **Database Size:** Max 400MB
- 🚀 **Bandwidth:** Unlimited (dengan fair use policy)
- ⚡ **PHP Version:** Biasanya 7.4 atau 8.0 (tergantung server)

### Tips Performa:
1. **Optimasi Gambar:** Compress gambar sebelum upload (max 500KB per gambar)
2. **Gunakan CDN:** Untuk Chart.js sudah menggunakan CDN, jangan ubah
3. **Batasi Query:** Gunakan LIMIT pada query database untuk data besar

### Keamanan:
1. **Ganti Password Default:** Setelah login pertama kali, ganti password admin
2. **Hapus File Sensitif:** Hapus `database/schema.sql` dan `database/seed.sql` dari hosting
3. **Tutup Error Display:** Setelah testing selesai, matikan error display di production:
   ```php
   ini_set('display_errors', 0);
   error_reporting(0);
   ```

---

## 📞 Bantuan Lebih Lanjut

- **Forum Infinity Free:** [https://forum.infinityfree.com/](https://forum.infinityfree.com/)
- **Knowledge Base:** [https://infinityfree.com/support/](https://infinityfree.com/support/)
- **GitHub Repository:** (link ke repo Anda jika sudah di-push)

---

**Selamat! 🎉** Jika semua langkah sudah diikuti dengan benar, aplikasi POS Anda seharusnya sudah live dan dapat diakses dari mana saja.
