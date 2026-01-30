# POS Toko Besi & Kayu

Aplikasi Point of Sale (POS) sederhana untuk toko penjualan barang besi dan kayu, dibangun dengan PHP Native (tanpa framework), MySQL, dan CSS sederhana.

## Fitur Utama
- **Login Multi-Role**: Admin dan Kasir (User default hanya Admin).
- **Master Data**: CRUD Kategori dan Barang dengan validasi stok & harga.
- **Transaksi POS**: Keranjang belanja, checkout, hitung kembalian, cetak struk.
- **Laporan**: Grafik penjualan harian/bulanan/tahunan.
- **Export**: Download laporan dalam bentuk CSV, Excel (.xls), dan PDF.

## Teknologi
- PHP Native (>= 7.4)
- MySQL / MariaDB
- PDO Driver (Prepared Statements)
- HTML5 / CSS3 (Custom Style)
- Chart.js (CDN)

## Instalasi (XAMPP)

1. **Extract / Clone**
   - Simpan folder `pos-besi-kayu` ke dalam folder `htdocs` di XAMPP Anda.
   - Contoh path: `C:\xampp\htdocs\pos-besi-kayu`

2. **Setup Database**
   - Bukalah phpMyAdmin (`http://localhost/phpmyadmin`).
   - Buat database baru dengan nama `pos_besi_kayu`.
   - Pilih database tersebut, lalu pilih menu **Import**.
   - Import file `database/schema.sql`.
   - Import file `database/seed.sql` untuk data awal.

3. **Konfigurasi**
   - Buka file `config/config.php`.
   - Pastikan database credentials sesuai (default: user `root`, pass kosong).
   - Sesuaikan `BASE_URL` jika nama folder Anda berbeda. Default:
     ```php
     define('BASE_URL', '/pos-besi-kayu/public');
     ```

4. **Menjalankan Project**
   - Buka browser dan akses: `http://localhost/pos-besi-kayu/public`
   - Anda akan diarahkan ke halaman Login.

## Akun Default
- **Username**: `admin`
- **Password**: `admin123`

## Struktur Folder
```
/pos-besi-kayu
├── app/               # (Reserved)
├── assets/            # CSS, Images
├── config/            # Konfigurasi Database & App
├── controllers/       # Logika Aplikasi (Controller)
├── database/          # SQL Schema & Seeds
├── helpers/           # Fungsi Bantuan (Auth, Util)
├── models/            # Interaksi Database
├── public/            # Entry Point (index.php)
├── routes/            # Routing Sederhana
├── views/             # Tampilan HTML (View)
└── README.md
```

## Troubleshooting
- Jika URL tidak bekerja (404), pastikan `mod_rewrite` aktif di Apache dan file `.htaccess` di folder `public` terbaca.
- Jika error database, cek kembali `config/config.php`.
