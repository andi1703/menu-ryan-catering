-- Update tabel bahan untuk menambahkan kolom keterangan
-- File: sql/update_bahan_add_keterangan.sql

USE menu_ryan_catering;

-- Tambahkan kolom keterangan ke tabel bahan
ALTER TABLE bahan ADD COLUMN keterangan TEXT NULL AFTER harga_sekarang;

-- Tambahkan kolom updated_at jika belum ada
ALTER TABLE bahan ADD COLUMN updated_at TIMESTAMP NULL DEFAULT NULL AFTER created_at;

-- Update existing records to have updated_at same as created_at
UPDATE bahan SET updated_at = created_at WHERE updated_at IS NULL;

-- Show structure
DESCRIBE bahan;

-- Test query untuk memastikan kolom sudah ada
SELECT COUNT(*) as total_bahan FROM bahan;

COMMIT;