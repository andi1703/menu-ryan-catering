-- Remove status_aktif column from menu table (safe for MySQL 8+)
ALTER TABLE `menu` DROP COLUMN IF EXISTS `status_aktif`;

-- For MySQL versions without IF EXISTS, use:
-- ALTER TABLE `menu` DROP COLUMN `status_aktif`;
