ALTER TABLE `menu`
  ADD COLUMN `id_bahan_utama` INT NULL AFTER `id_thematik`,
  ADD INDEX `idx_menu_id_bahan_utama` (`id_bahan_utama`),
  ADD CONSTRAINT `fk_menu_bahan_utama`
    FOREIGN KEY (`id_bahan_utama`) REFERENCES `bahan`(`id_bahan`)
    ON UPDATE CASCADE
    ON DELETE SET NULL;
