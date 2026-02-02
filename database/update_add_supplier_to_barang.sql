-- Add supplier_id column to barang table
ALTER TABLE `barang` 
ADD COLUMN `supplier_id` INT(11) DEFAULT NULL AFTER `kategori_id`,
ADD KEY `supplier_id` (`supplier_id`),
ADD CONSTRAINT `fk_barang_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `kode_supplier` (`id_kode_supplier`) ON DELETE SET NULL;

-- Update existing barang to have a default supplier (optional, adjust as needed)
-- UPDATE `barang` SET `supplier_id` = 1 WHERE `supplier_id` IS NULL;
