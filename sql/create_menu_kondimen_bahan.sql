-- Table to store manual bahan detail per kondimen (menu harian + komponen)
CREATE TABLE IF NOT EXISTS `menu_kondimen_bahan` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_menu_harian` INT UNSIGNED NOT NULL,
  `id_komponen` INT UNSIGNED NOT NULL,
  `bahan_nama` VARCHAR(255) NOT NULL,
  `qty` DECIMAL(12,3) NOT NULL DEFAULT 0,
  `satuan` VARCHAR(50) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_menu_kondimen` (`id_menu_harian`, `id_komponen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
