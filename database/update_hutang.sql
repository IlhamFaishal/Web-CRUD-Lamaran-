
-- Table Pembelian (Purchases)
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

-- Table Saldo Awal Hutang (Beginning Balance per Year)
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

-- Table Pembayaran Hutang (Debt Payments)
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
