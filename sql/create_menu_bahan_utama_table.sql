CREATE TABLE `menu_bahan_utama` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `menu_id` INT UNSIGNED NOT NULL,
  `bahan_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_menu_bahan_menu` (`menu_id`),
  KEY `idx_menu_bahan_bahan` (`bahan_id`),
  CONSTRAINT `fk_menu_bahan_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu`(`id_komponen`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_menu_bahan_bahan` FOREIGN KEY (`bahan_id`) REFERENCES `bahan`(`id_bahan`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
