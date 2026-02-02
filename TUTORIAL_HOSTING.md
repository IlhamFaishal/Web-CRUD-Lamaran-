# Panduan Hosting di InfinityFree

Berikut adalah langkah-langkah lengkap untuk mengonlinekan aplikasi POS Besi & Kayu menggunakan hosting gratis InfinityFree.

## 1. Persiapan File
Sebelum upload, pastikan file Anda siap.
1.  **Database**: Gunakan file `database/database.sql` yang sudah disatukan.
2.  **Source Code**: Siapkan seluruh folder project (kecuali `.git`). Anda bisa men-zip seluruh folder project agar mudah diupload, atau upload manual via FTP.

## 2. Daftar & Buat Akun Hosting
1.  Buka [InfinityFree.net](https://infinityfree.net) dan daftar/login.
2.  Klik **"Create Account"**.
3.  **Domain Type**: Pilih Subdomain (Gratis).
    *   Masukkan nama domain yang diinginkan (contoh: `pos-besi-kayu`).
    *   Pilih domain ext (contoh: `.rf.gd`).
4.  Lanjutkan langkahnya hingga akun terbuat.
5.  **PENTING**: Catat informasi akun Anda yang muncul (atau cek di Client Area):
    *   **Username** (contoh: `epiz_34567890`)
    *   **Password** (Password akun hosting)
    *   **MySQL Hostname** (contoh: `sql123.infinityfree.com`)

## 3. Setup Database
1.  Masuk ke **Control Panel** (Tombol hijau di Client Area), lalu Approve jika diminta.
2.  Cari menu **MySQL Databases** di bagian Databases.
3.  **Create New Database**:
    *   Masukkan nama database (contoh: `pos`). nama database akhirnya akan menjadi seperti `epiz_34567890_pos`.
    *   Klik **Create Database**.
4.  Scroll ke bawah, lihat list "Current Databases".
5.  Klik tombol **Admin** di sebelah database yang baru dibuat. Ini akan membuka **phpMyAdmin**.
6.  Di phpMyAdmin:
    *   Klik tab **Import**.
    *   Choose File: Pilih file `database/database.sql` dari komputer Anda.
    *   Klik **Go** / **Kirim**.
    *   Pastikan impor sukses (hijau).

## 4. Upload File Website
1.  Buka **Online File Manager** dari Client Area (atau gunakan FileZilla jika filenya banyak).
2.  Masuk ke folder `htdocs`.
3.  **Hapus** file `index2.html` atau `default` lainnya yang ada disana.
4.  **Upload** semua file dan folder project Anda ke dalam `htdocs`.
    *   Struktur di dalam `htdocs` harusnya langsung berisi: `app`, `config`, `controllers`, `public`, `index.php`, dll.
    *   *Jangan* masukkan dalam folder lagi (misal `htdocs/pos-besi-kayu/index.php`), nanti URL-nya jadi panjang.
    *   Pastikan file `public/index.php` atau `index.php` utama ada di root atau diatur sesuai struktur. 
    *   *Catatan*: Jika struktur project Anda memiliki `index.php` di dalam folder `public`, Anda mungkin perlu memindahkannya ke root alias `htdocs`, atau mengatur `.htaccess`. **Untuk project ini, `index.php` tampaknya ada di folder `public`**.
    
    **Rekomendasi Struktur untuk InfinityFree (Root directory):**
    Agar mudah, pindahkan isi folder `public` (file `index.php`, css, js) ke luar (ke `htdocs` langsung), dan sesuaikan path `require`. ATAU biarkan seperti ini tapi anda aka mengaksesnya via `domain.com/public`.
    
    *Solusi Terbaik:* Biarkan struktur apa adanya, lalu buat file `.htaccess` baru di dalam `htdocs` (sejajar dengan folder public) dengan isi:
    ```apache
    <IfModule mod_rewrite.c>
      RewriteEngine On
      RewriteRule ^(.*)$ public/$1 [L]
    </IfModule>
    ```
    Ini akan otomatis mengarahkan pengunjung ke folder `public`.

## 5. Konfigurasi Koneksi Database
1.  Di File Manager, buka folder `config`.
2.  Klik kanan file `config.php` -> **Edit**.
3.  Ubah bagian konfigurasi database sesuai data dari InfinityFree (Langkah 2 & 3):
    ```php
    // GANTI BAGIAN INI:
    define('DB_HOST', 'sqlxxx.infinityfree.com'); // Lihat di Client Area "MySQL Hostname"
    define('DB_USER', 'epiz_xxxxxxx');            // Lihat "MySQL Username"
    define('DB_PASS', 'password_akun_anda');      // Password hosting Anda
    define('DB_NAME', 'epiz_xxxxxxx_pos');        // Masukkan nama database LENGKAP
    ```
4.  Simpan (Save).

## 6. Selesai & Testing
Buka domain Anda di browser (contoh: `http://pos-besi-kayu.rf.gd`).
*   Coba login dengan akun admin standar (jika Anda menggunakan seed data saya: `admin` / `admin123`).
*   Cek fitur transaksi dan laporan.

### Masalah Umum (Troubleshooting)
*   **Error 500 / Blank Page**: Biasanya error PHP. Coba aktifkan `display_errors` di `config.php` menjadi `1` sementara untuk melihat errornya.
*   **Database Error**: Cek kembali Hostname, Username, Password, dan Nama Database di `config.php`. Pastikan tidak ada spasi tambahan.
*   **Gambar tidak muncul**: Pastikan nama file gambar (besar/kecil huruf) sesuai, karena hosting Linux itu Case Sensitive (File `Gambar.jpg` beda dengan `gambar.jpg`).
