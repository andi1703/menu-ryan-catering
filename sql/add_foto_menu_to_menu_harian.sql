-- Menambahkan kolom foto_menu ke tabel menu_harian
-- Path file: file/products/menukondimen/

ALTER TABLE `menu_harian` 
ADD COLUMN `foto_menu` VARCHAR(255) NULL DEFAULT NULL AFTER `remark`;

-- Keterangan:
-- Kolom foto_menu akan menyimpan nama file foto menu
-- File foto akan disimpan di folder: file/products/menukondimen/
-- Format file yang diterima: JPG, JPEG, PNG
-- Maksimal ukuran file: 2MB
