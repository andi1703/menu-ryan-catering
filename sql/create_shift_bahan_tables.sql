-- Database schema untuk aplikasi input data bahan baku per shift
-- File: sql/create_shift_bahan_tables.sql

USE menu_ryan_catering;

-- Tabel master divisi/unit kerja
CREATE TABLE IF NOT EXISTS divisi (
    id_divisi INT(11) NOT NULL AUTO_INCREMENT,
    kode_divisi VARCHAR(10) NOT NULL,
    nama_divisi VARCHAR(50) NOT NULL,
    keterangan TEXT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_divisi),
    UNIQUE KEY uk_kode_divisi (kode_divisi)
);

-- Insert data divisi default
INSERT INTO divisi (kode_divisi, nama_divisi, keterangan) VALUES
('TMMIN', 'Toyota Motor Manufacturing Indonesia', 'Divisi utama produksi Toyota'),
('ADM', 'Administration', 'Divisi administrasi dan keuangan'),
('TPIN', 'Toyota Parts Indonesia', 'Divisi spare parts Toyota'),
('SMT', 'Surface Mount Technology', 'Divisi teknologi pemasangan permukaan'),
('AKBN', 'Astra Komponen', 'Divisi komponen Astra'),
('ATI', 'Astra Toyota Indonesia', 'Divisi Astra Toyota'),
('POSCO', 'POSCO Indonesia', 'Divisi POSCO Indonesia');

-- Tabel master kategori shift
CREATE TABLE IF NOT EXISTS shift_kategori (
    id_shift_kategori INT(11) NOT NULL AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL,
    kode_kategori VARCHAR(20) NOT NULL,
    keterangan TEXT NULL,
    urutan INT(3) DEFAULT 1,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_shift_kategori),
    UNIQUE KEY uk_kode_kategori (kode_kategori)
);

-- Insert kategori shift
INSERT INTO shift_kategori (nama_kategori, kode_kategori, urutan) VALUES
('AB', 'AB', 1),
('AT', 'AT', 2),
('CY', 'CY', 3),
('TAM', 'TAM', 4),
('TR', 'TR', 5),
('REG', 'REG', 6),
('STAFF SEHAT', 'STAFF_SEHAT', 7),
('PAGI', 'PAGI', 8),
('SORE', 'SORE', 9),
('SEHAT', 'SEHAT', 10);

-- Tabel header untuk input data bahan baku per hari
CREATE TABLE IF NOT EXISTS shift_bahan_header (
    id_header INT(11) NOT NULL AUTO_INCREMENT,
    tanggal_shift DATE NOT NULL,
    shift_type ENUM('lunch', 'dinner', 'breakfast') DEFAULT 'lunch',
    status_input ENUM('draft', 'completed', 'approved') DEFAULT 'draft',
    total_bahan INT(11) DEFAULT 0,
    created_by INT(11) NULL,
    approved_by INT(11) NULL,
    approved_at TIMESTAMP NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_header),
    UNIQUE KEY uk_tanggal_shift (tanggal_shift, shift_type),
    INDEX idx_tanggal (tanggal_shift),
    INDEX idx_status (status_input)
);

-- Tabel detail untuk input jumlah bahan per divisi dan kategori
CREATE TABLE IF NOT EXISTS shift_bahan_detail (
    id_detail INT(11) NOT NULL AUTO_INCREMENT,
    id_header INT(11) NOT NULL,
    id_bahan INT(11) NOT NULL,
    id_divisi INT(11) NOT NULL,
    id_shift_kategori INT(11) NOT NULL,
    jumlah_kebutuhan DECIMAL(10,3) DEFAULT 0,
    satuan VARCHAR(20) NULL,
    keterangan TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_detail),
    UNIQUE KEY uk_detail (id_header, id_bahan, id_divisi, id_shift_kategori),
    FOREIGN KEY (id_header) REFERENCES shift_bahan_header(id_header) ON DELETE CASCADE,
    FOREIGN KEY (id_bahan) REFERENCES bahan(id_bahan) ON DELETE CASCADE,
    FOREIGN KEY (id_divisi) REFERENCES divisi(id_divisi) ON DELETE CASCADE,
    FOREIGN KEY (id_shift_kategori) REFERENCES shift_kategori(id_shift_kategori) ON DELETE CASCADE,
    INDEX idx_header (id_header),
    INDEX idx_bahan (id_bahan),
    INDEX idx_divisi (id_divisi)
);

-- View untuk mempermudah query data lengkap
CREATE OR REPLACE VIEW v_shift_bahan_lengkap AS
SELECT 
    h.id_header,
    h.tanggal_shift,
    h.shift_type,
    h.status_input,
    d.id_detail,
    b.id_bahan,
    b.nama_bahan,
    s.nama_satuan,
    div.kode_divisi,
    div.nama_divisi,
    sk.kode_kategori,
    sk.nama_kategori,
    d.jumlah_kebutuhan,
    d.keterangan as detail_keterangan,
    h.keterangan as header_keterangan,
    d.created_at as input_date
FROM shift_bahan_header h
LEFT JOIN shift_bahan_detail d ON h.id_header = d.id_header
LEFT JOIN bahan b ON d.id_bahan = b.id_bahan
LEFT JOIN satuan s ON b.id_satuan = s.id_satuan
LEFT JOIN divisi div ON d.id_divisi = div.id_divisi
LEFT JOIN shift_kategori sk ON d.id_shift_kategori = sk.id_shift_kategori
ORDER BY h.tanggal_shift DESC, b.nama_bahan ASC, div.kode_divisi ASC, sk.urutan ASC;

-- Tabel untuk menyimpan template default bahan per divisi
CREATE TABLE IF NOT EXISTS shift_bahan_template (
    id_template INT(11) NOT NULL AUTO_INCREMENT,
    id_bahan INT(11) NOT NULL,
    id_divisi INT(11) NOT NULL,
    id_shift_kategori INT(11) NOT NULL,
    jumlah_default DECIMAL(10,3) DEFAULT 0,
    is_active ENUM('yes', 'no') DEFAULT 'yes',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id_template),
    UNIQUE KEY uk_template (id_bahan, id_divisi, id_shift_kategori),
    FOREIGN KEY (id_bahan) REFERENCES bahan(id_bahan) ON DELETE CASCADE,
    FOREIGN KEY (id_divisi) REFERENCES divisi(id_divisi) ON DELETE CASCADE,
    FOREIGN KEY (id_shift_kategori) REFERENCES shift_kategori(id_shift_kategori) ON DELETE CASCADE
);

COMMIT;

-- Test queries
SELECT 'Database shift bahan baku berhasil dibuat' as status;
SELECT COUNT(*) as total_divisi FROM divisi;
SELECT COUNT(*) as total_shift_kategori FROM shift_kategori;