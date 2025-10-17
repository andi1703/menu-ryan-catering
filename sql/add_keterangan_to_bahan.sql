-- Migration script untuk menambahkan kolom keterangan ke tabel bahan
-- File: sql/add_keterangan_to_bahan.sql

-- Cek apakah kolom keterangan sudah ada
SELECT COUNT(*) as kolom_ada 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'bahan' 
AND COLUMN_NAME = 'keterangan';

-- Jika kolom belum ada (kolom_ada = 0), jalankan ALTER TABLE berikut:
ALTER TABLE `bahan` 
ADD COLUMN `keterangan` TEXT NULL AFTER `harga_sekarang`,
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `keterangan`;

-- Update struktur tabel untuk consistency
ALTER TABLE `bahan` 
MODIFY COLUMN `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
MODIFY COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP;

-- Contoh insert data dengan keterangan
-- INSERT INTO `bahan` (`nama_bahan`, `id_satuan`, `harga_awal`, `harga_sekarang`, `keterangan`, `created_at`) 
-- VALUES ('Wortel Organik', 1, 15000, 17000, 'Wortel segar berkualitas tinggi untuk sup dan salad', NOW());

-- Verify struktur tabel setelah migration
DESCRIBE `bahan`;