-- Unified Database Schema for POS Besi & Kayu
-- Combined from multiple files for easier deployment

-- ==========================================
-- 1. USERS TABLE
-- ==========================================
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'kasir') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 2. MASTER DATA TABLES (Suppliers & Prefixes)
-- ==========================================
CREATE TABLE `kode_supplier` (
  `id_kode_supplier` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_supplier` VARCHAR(50) NOT NULL,
  `nama_supplier` VARCHAR(200) NOT NULL,
  `alamat` TEXT,
  `telepon` VARCHAR(20),
  `email` VARCHAR(100),
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kode_supplier`),
  UNIQUE KEY `kode_supplier` (`kode_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `master_kode_barang` (
  `id_master_kode` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_prefix` VARCHAR(10) NOT NULL,
  `nama_prefix` VARCHAR(100) NOT NULL,
  `deskripsi` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_master_kode`),
  UNIQUE KEY `kode_prefix` (`kode_prefix`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 3. INVENTORY TABLES (Categories & Items)
-- ==========================================
CREATE TABLE `kategori` (
  `id_kategori` INT(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kategori`),
  UNIQUE KEY `nama_kategori` (`nama_kategori`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `barang` (
  `id_barang` INT(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` VARCHAR(50) NOT NULL,
  `nama_barang` VARCHAR(200) NOT NULL,
  `kategori_id` INT(11) NOT NULL,
  `supplier_id` INT(11) DEFAULT NULL, -- Added from update
  `satuan` VARCHAR(20) NOT NULL, 
  `harga_jual` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `stok` INT(11) NOT NULL DEFAULT 0,
  `gambar` VARCHAR(255) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1, -- Added from update
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_barang`),
  KEY `kategori_id` (`kategori_id`),
  KEY `supplier_id` (`supplier_id`),
  -- Note: Original unique(kode_barang) removed in favor of (kode_barang, supplier_id)
  CONSTRAINT `unique_kode_supplier` UNIQUE (`kode_barang`, `supplier_id`),
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE,
  CONSTRAINT `fk_barang_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `kode_supplier` (`id_kode_supplier`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 4. TRANSACTION TABLES (Sales)
-- ==========================================
CREATE TABLE `transaksi` (
  `id_transaksi` INT(11) NOT NULL AUTO_INCREMENT,
  `no_transaksi` VARCHAR(50) NOT NULL,
  `tanggal` DATETIME NOT NULL,
  `total_harga` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `bayar` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `kembalian` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_transaksi`),
  UNIQUE KEY `no_transaksi` (`no_transaksi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `transaksi_detail` (
  `id_detail` INT(11) NOT NULL AUTO_INCREMENT,
  `transaksi_id` INT(11) NOT NULL,
  `barang_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL,
  `harga_satuan` DECIMAL(15,2) NOT NULL,
  `subtotal` DECIMAL(15,2) NOT NULL,
  PRIMARY KEY (`id_detail`),
  KEY `transaksi_id` (`transaksi_id`),
  KEY `barang_id` (`barang_id`),
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id_barang`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 5. DEBT & PURCHASE TABLES
-- ==========================================
CREATE TABLE IF NOT EXISTS `pembelian` (
  `id_pembelian` INT(11) NOT NULL AUTO_INCREMENT,
  `no_faktur` VARCHAR(50) NOT NULL,
  `tanggal` DATE NOT NULL,
  `supplier_id` INT(11) NOT NULL,
  `barang_id` INT(11) NOT NULL,
  `qty` INT(11) NOT NULL DEFAULT 0,
  `harga_satuan` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `dpp` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `ppn` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `total_harga` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pembelian`),
  KEY `supplier_id` (`supplier_id`),
  KEY `barang_id` (`barang_id`),
  CONSTRAINT `fk_pembelian_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `kode_supplier` (`id_kode_supplier`) ON DELETE CASCADE,
  CONSTRAINT `fk_pembelian_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `saldo_awal_hutang` (
  `id_saldo` INT(11) NOT NULL AUTO_INCREMENT,
  `supplier_id` INT(11) NOT NULL,
  `tahun` INT(4) NOT NULL,
  `saldo_awal` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_saldo`),
  KEY `supplier_id` (`supplier_id`),
  UNIQUE KEY `idx_saldo_supplier_tahun` (`supplier_id`, `tahun`),
  CONSTRAINT `fk_saldo_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `kode_supplier` (`id_kode_supplier`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `pembayaran_hutang` (
  `id_bayar` INT(11) NOT NULL AUTO_INCREMENT,
  `tanggal` DATE NOT NULL,
  `supplier_id` INT(11) NOT NULL,
  `jumlah_bayar` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_bayar`),
  KEY `supplier_id` (`supplier_id`),
  CONSTRAINT `fk_bayar_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `kode_supplier` (`id_kode_supplier`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 6. SEED DATA
-- ==========================================

-- Admin User (password: admin123)
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('admin', '$2y$10$b1tulQfnJtGocZFczeQhKyjV0Z7XEbaLOF.WbE9z', 'admin');

-- Suppliers
INSERT INTO `kode_supplier` (`kode_supplier`, `nama_supplier`, `alamat`, `telepon`, `email`, `keterangan`) VALUES
('SUP001', 'PT Besi Jaya', 'Jl. Industri No. 123, Jakarta', '021-12345678', 'info@besijaya.com', 'Supplier besi berkualitas'),
('SUP002', 'CV Kayu Makmur', 'Jl. Raya Bogor KM 45, Bogor', '0251-987654', 'kayu@makmur.co.id', 'Supplier kayu terpercaya'),
('SUP003', 'UD Logam Sejahtera', 'Jl. Gatot Subroto No. 88, Bandung', '022-5556677', 'logam@sejahtera.com', 'Supplier logam dan besi');

-- Item Prefix Codes
INSERT INTO `master_kode_barang` (`kode_prefix`, `nama_prefix`, `deskripsi`) VALUES
('BS', 'Besi', 'Prefix untuk produk besi'),
('KY', 'Kayu', 'Prefix untuk produk kayu'),
('LG', 'Logam', 'Prefix untuk produk logam'),
('AL', 'Aluminium', 'Prefix untuk produk aluminium'),
('ST', 'Stainless', 'Prefix untuk produk stainless steel');
