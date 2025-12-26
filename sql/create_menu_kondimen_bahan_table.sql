-- Tabel untuk menyimpan detail bahan per menu kondimen
-- Relasi: menu_harian + id_komponen -> bahan list
CREATE TABLE IF NOT EXISTS `menu_kondimen_bahan` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `id_menu_harian` INT(10) NOT NULL COMMENT 'FK ke menu_harian',
  `id_komponen` INT(10) NOT NULL COMMENT 'FK ke menu.id_komponen',
  `bahan_nama` VARCHAR(255) NOT NULL COMMENT 'Nama bahan',
  `qty` DECIMAL(10,2) NOT NULL DEFAULT 0 COMMENT 'Quantity bahan',
  `satuan` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Satuan (kg/gr/pcs)',
  `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_menu_harian` (`id_menu_harian`),
  INDEX `idx_komponen` (`id_komponen`),
  INDEX `idx_menu_komponen` (`id_menu_harian`, `id_komponen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Detail bahan per kondimen pada menu harian untuk vegetable calculator';
