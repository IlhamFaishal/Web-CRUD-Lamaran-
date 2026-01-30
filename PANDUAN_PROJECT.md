# Panduan Teknis Project POS (Untuk Interview HRD/User)

Dokumen ini dibuat secara khusus agar Anda bisa menjelaskan **secara detail dan spesifik** di mana letak codingan dan logika aplikasi ini berjalan. Panduan ini menggunakan gaya bahasa manusia (bukan robot) agar mudah diterima oleh HRD atau User non-teknis.

## 1. Teknologi yang Digunakan
*   **Bahasa**: PHP Native (Murni tanpa Framework).
*   **Database**: MySQL.
*   **Server**: Apache (XAMPP).

---

## 2. Bedah Struktur Folder (Detail)

Berikut adalah peta lokasi file-file penting yang bisa Anda tunjukkan saat presentasi:

### A. Pintu Masuk (Entry Point)
*   **Lokasi**: `public/index.php`
*   **Penjelasan**: "Pak/Bu, semua user yang masuk ke web ini pasti lewat file ini dulu. Di sini codingannya mengatur 'lalu lintas' (Router). Kalau user ketik alamat `/barang`, file ini yang akan memanggil Controller Barang."

### B. Otak Aplikasi (Controllers)
*   **Lokasi**: `controllers/`
*   **File Penting**:
    1.  `controllers/BarangController.php`: Mengurus logika Tambah, Edit, Hapus, dan Upload Gambar barang.
    2.  `controllers/PosController.php`: Mengurus logika Kasir, hitung keranjang, dan simpan transaksi.
    3.  `controllers/AuthController.php`: Mengurus Login dan Logout admin.
*   **Penjelasan**: "Di folder inilah logika bisnis berjalan. Contohnya di `BarangController.php` baris 70, ada fungsi untuk validasi upload gambar agar file yang masuk aman dan sesuai format."

### C. Dapur Data (Models)
*   **Lokasi**: `models/`
*   **File Penting**:
    1.  `models/BarangModel.php`: Isinya sintaks SQL (`SELECT`, `INSERT`, `UPDATE`) khusus tabel Barang.
    2.  `models/kategoriModel.php`: Isinya sintaks SQL untuk tabel Kategori.
*   **Penjelasan**: "Controller tidak boleh menyentuh database langsung. Jadi kalau mau ambil data barang, Controller minta tolong ke `BarangModel.php`. Ini membuat codingan aman dan terstruktur."

### D. Tampilan (Views)
*   **Lokasi**: `views/`
*   **Folder Penting**:
    1.  `views/pos/index.php`: Tampilan halaman kasir yang ada kotak-kotak produknya.
    2.  `views/barang/index.php`: Tampilan tabel daftar barang, termasuk Modal Pop-up tambah barang yang baru kita buat.
*   **Penjelasan**: "Di sini isinya HTML murni. Logika PHP yang berat tidak boleh ada di sini agar tampilannya ringan."

### E. Konfigurasi & Alat Bantu
1.  **Koneksi Database**: `config/database.php`
    *   "Saya menggunakan teknik **Singleton** di sini pak, agar koneksi ke database hanya dibuka satu kali saja per halaman, jadi servernya tidak berat."
2.  **Fungsi Bantuan**: `helpers/functions.php`
    *   "Fungsi seperti `formatRupiah()` atau `redirect()` saya kumpulkan di satu file ini biar tidak duplikat codingan di mana-mana."

---

## 3. FAQ: "Kok file `index.php` ada banyak?"

Jangan bingung Pak/Bu, memang ada banyak tapi fungsinya beda-beda:

1.  **`public/index.php` (YANG PALING UTAMA)**
    *   Ini adalah **Satpam Utama**. Semua pengunjung masuk lewat sini.
2.  **`views/pos/index.php`**
    *   Ini cuma **Tampilan** halaman Kasir.
3.  **`views/barang/index.php`**
    *   Ini cuma **Tampilan** halaman Daftar Barang.

Jadi kalau bicara "Logika/Router", yang dimaksud pasti `public/index.php`. Kalau "Tampilan", berarti yang di folder `views`.

---

## 4. Contoh Kasus: "Coba jelaskan alur Tambah Barang!"

Jika HRD meminta Anda menjelaskan satu fitur, gunakan alur ini (sambil buka file-nya):

1.  **User Klik Simpan**:
    > "Saat tombol Simpan di Modal diklik, data dikirim ke `index.php` dulu."
2.  **Router Bekerja**:
    > "Dari `index.php`, data diteruskan ke `controllers/BarangController.php` fungsi `store()`."
3.  **Proses di Controller**:
    *   Buka file `controllers/BarangController.php`.
    *   Tunjuk baris validasi (`validateRequired`).
    *   Tunjuk baris upload gambar (`move_uploaded_file`).
4.  **Simpan ke DB**:
    *   Controller memanggil `models/BarangModel.php` fungsi `create()`.
    *   Buka file `models/BarangModel.php` dan tunjuk sintaks `INSERT INTO barang...`.
5.  **Selesai**:
    > "Setelah berhasil, sistem mengembalikan user ke halaman daftar barang dengan pesan sukses."

---

## 4. Keunggulan Codingan Anda
Jika ditanya "Apa spesialnya codingan ini?", jawablah:

1.  **Struktur MVC Murni**: "Saya paham betul memisahkan tampilan (View), logika (Controller), dan data (Model). Ini standar industri."
2.  **Aman**: "Saya pakai PDO (PHP Data Objects) di `config/database.php` jadi aman dari serangan hacker (SQL Injection)."
3.  **Efisien**: "Saya pakai teknik Singleton di Database dan memecah helper function agar codingan tidak berulang-ulang (DRY - Don't Repeat Yourself)."

---

## 7. Panduan Upload ke Hosting (Saat sudah siap online)

Web ini sudah saya buat **"Hosting Ready"**. Artinya, codingannya fleksibel (tidak kaku di localhost).

**Langkah-langkah Hosting:**

1.  **Siapkan Database:**
    *   Buka **phpMyAdmin** di hosting Anda.
    *   Buat database baru (misal: `u123456_pos`).
    *   Import file `database/seed.sql` ke database tersebut.

2.  **Upload File:**
    *   Upload semua folder dan file ke **File Manager** (biasanya di dalam folder `public_html`).

3.  **Wajib Edit: `config/config.php`**
    *   Cari file `config/config.php` di hosting.
    *   Edit bagian atasnya sesuai database hosting:

    ```php
    define('DB_HOST', 'localhost'); // Biasanya tetap localhost
    define('DB_USER', 'u123456_user_hosting'); // Ganti user hosting
    define('DB_PASS', 'password_hosting_anda'); // Ganti password hosting
    define('DB_NAME', 'u123456_pos'); // Ganti nama DB hosting
    ```

    *   **PENTING**: Anda **TIDAK PERLU** mengedit `BASE_URL`. Codingan saya di baris 10-18 sudah otomatis mendeteksi nama domain Anda (misal: `tokobesi.com` atau `subdomain.toko.com`).

4.  **Cek Hasil:**
    *   Buka website Anda. InsyaAllah langsung jalan!
