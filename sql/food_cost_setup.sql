-- ================================================
-- DATABASE SETUP FOR FOOD COST MENU REGULAR
-- ================================================

-- 0. Tabel Satuan (jika belum ada)
CREATE TABLE IF NOT EXISTS `satuan` (
  `id_satuan` int(11) NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(50) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_satuan`),
  UNIQUE KEY `unique_nama_satuan` (`nama_satuan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default satuan data
INSERT IGNORE INTO `satuan` (`nama_satuan`, `keterangan`) VALUES 
('kg', 'Kilogram'),
('gram', 'Gram'),
('liter', 'Liter'), 
('ml', 'Mililiter'),
('pcs', 'Pieces/Buah'),
('biji', 'Per Biji'),
('buah', 'Per Buah'),
('ikat', 'Per Ikat'),
('lembar', 'Per Lembar'),
('botol', 'Per Botol');

-- 1. Tabel untuk Menu Regular Food Cost
CREATE TABLE IF NOT EXISTS `menu_regular_food_cost` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_menu` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_nama_menu` (`nama_menu`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tabel untuk Detail Bahan per Menu
CREATE TABLE IF NOT EXISTS `menu_regular_bahan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `nama_bahan` varchar(255) NOT NULL,
  `qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `satuan` varchar(50) NOT NULL,
  `harga_per_satuan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pembagian_porsi` int(11) NOT NULL DEFAULT 1,
  `urutan` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_menu_id` (`menu_id`),
  KEY `idx_nama_bahan` (`nama_bahan`),
  KEY `idx_urutan` (`urutan`),
  CONSTRAINT `fk_menu_bahan_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu_regular_food_cost` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ================================================
-- SAMPLE DATA INSERT
-- ================================================

-- Insert sample menu
INSERT INTO `menu_regular_food_cost` (`nama_menu`, `deskripsi`) VALUES 
('Nasi Gudeg Jogja', 'Menu tradisional Jogja dengan nasi putih, gudeg, ayam, dan sambal');

-- Get the last inserted menu ID
SET @menu_id = LAST_INSERT_ID();

-- Insert sample bahan for the menu
INSERT INTO `menu_regular_bahan` (`menu_id`, `nama_bahan`, `qty`, `satuan`, `harga_per_satuan`, `pembagian_porsi`, `urutan`) VALUES
(@menu_id, 'beras', 1.00, 'kg', 13900.00, 6, 1),
(@menu_id, 'nangka muda', 0.50, 'kg', 8000.00, 8, 2),
(@menu_id, 'ayam kampung', 1.20, 'kg', 33000.00, 8, 3),
(@menu_id, 'santan kelapa', 0.30, 'kg', 15000.00, 8, 4),
(@menu_id, 'gula merah', 0.20, 'kg', 18000.00, 8, 5),
(@menu_id, 'bawang merah', 0.10, 'kg', 25000.00, 8, 6),
(@menu_id, 'bawang putih', 0.05, 'kg', 35000.00, 8, 7),
(@menu_id, 'cabai rawit', 0.05, 'kg', 30000.00, 80, 8),
(@menu_id, 'krupuk udang', 1.00, 'pcs', 2000.00, 1, 9);

-- ================================================
-- CREATE ROUTES (untuk ditambahkan ke routes.php)
-- ================================================

-- Tambahkan routes berikut ke application/config/routes.php:
/*
// ===== FOOD COST ROUTES =====
$route['food-cost'] = 'Back_Food_Cost/index';
$route['food-cost/get_data'] = 'Back_Food_Cost/get_data';
$route['food-cost/get_by_id'] = 'Back_Food_Cost/get_by_id';
$route['food-cost/save_data'] = 'Back_Food_Cost/save_data';
$route['food-cost/delete_data'] = 'Back_Food_Cost/delete_data';
$route['food-cost/calculate_cost'] = 'Back_Food_Cost/calculate_cost';
$route['food-cost/get_stats'] = 'Back_Food_Cost/get_stats';
*/

-- ================================================
-- PERHITUNGAN FOOD COST EXAMPLE
-- ================================================

/*
CONTOH PERHITUNGAN BERDASARKAN DATA SAMPLE:

1. BAHAN MENTAH (1 PORSI):
   - Beras: (1 × 13,900) ÷ 6 = Rp 2,316.67
   - Nangka muda: (0.5 × 8,000) ÷ 8 = Rp 500.00
   - Ayam kampung: (1.2 × 33,000) ÷ 8 = Rp 4,950.00
   - Santan kelapa: (0.3 × 15,000) ÷ 8 = Rp 562.50
   - Gula merah: (0.2 × 18,000) ÷ 8 = Rp 450.00
   - Bawang merah: (0.1 × 25,000) ÷ 8 = Rp 312.50
   - Bawang putih: (0.05 × 35,000) ÷ 8 = Rp 218.75
   - Cabai rawit: (0.05 × 30,000) ÷ 80 = Rp 18.75
   - Krupuk udang: (1 × 2,000) ÷ 1 = Rp 2,000.00
   
   TOTAL BAHAN MENTAH = Rp 11,328.17

2. BIAYA PRODUKSI (20%):
   Rp 11,328.17 × 20% = Rp 2,265.63

3. FOOD COST:
   Rp 11,328.17 + Rp 2,265.63 = Rp 13,593.80
*/