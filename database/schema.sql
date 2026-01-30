-- Database Schema for POS Besi & Kayu

CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'kasir') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `satuan` VARCHAR(20) NOT NULL, 
  `harga_jual` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `stok` INT(11) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_barang`),
  UNIQUE KEY `kode_barang` (`kode_barang`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
