# FITUR FOTO MENU - MENU HARIAN

## PERUBAHAN YANG DILAKUKAN

### 1. DATABASE
**Query SQL yang harus dijalankan di HeidiSQL:**
```sql
ALTER TABLE `menu_harian` 
ADD COLUMN `foto_menu` VARCHAR(255) NULL DEFAULT NULL AFTER `remark`;
```

File SQL tersimpan di: `sql/add_foto_menu_to_menu_harian.sql`

### 2. FOLDER PENYIMPANAN
- **Path**: `file/products/menukondimen/`
- Folder sudah dibuat otomatis
- File `.htaccess` sudah ditambahkan untuk keamanan

### 3. FILE YANG DIMODIFIKASI

#### A. Form Input (V_Menu_Harian_form.php)
- ✅ Menambahkan input file upload untuk foto menu
- ✅ Preview foto sebelum upload
- ✅ Validasi format: JPG, JPEG, PNG
- ✅ Validasi ukuran maksimal: 2MB
- ✅ Hidden field untuk menyimpan foto existing saat edit

#### B. View Tabel (V_Menu_Harian.php)
- ✅ Menambahkan kolom header "Foto Menu" di tabel
- ✅ Mengubah width kolom Remark dari 22% menjadi 15%
- ✅ Menambahkan kolom Foto Menu dengan width 7%

#### C. JavaScript (V_Menu_Harian_js.php)
- ✅ Menambahkan preview foto saat memilih file
- ✅ Validasi ukuran file (max 2MB)
- ✅ Validasi tipe file (JPG, JPEG, PNG)
- ✅ Menampilkan foto di tabel dengan thumbnail (60x80px)
- ✅ Foto bisa diklik untuk melihat ukuran penuh
- ✅ Reset preview saat tambah menu baru
- ✅ Menampilkan foto existing saat edit menu
- ✅ Update colspan untuk loading dan empty rows

#### D. Controller (Back_Menu_Harian.php)
- ✅ Handle upload file foto dengan CodeIgniter upload library
- ✅ Validasi format dan ukuran file
- ✅ Generate nama file unique: `menu_[timestamp]_[random].jpg`
- ✅ Buat folder otomatis jika belum ada
- ✅ Simpan nama file ke database
- ✅ Support edit: keep foto lama jika tidak upload foto baru

## CARA MENGGUNAKAN

### 1. Jalankan Query SQL
Buka HeidiSQL dan jalankan query di file `sql/add_foto_menu_to_menu_harian.sql`

### 2. Menambah Menu Harian dengan Foto
1. Klik tombol "Tambah Menu Harian"
2. Isi semua field yang diperlukan
3. Di bagian bawah form, ada field "Foto Menu"
4. Klik tombol "Choose File" dan pilih foto
5. Preview foto akan muncul otomatis
6. Klik "Simpan"

### 3. Edit Menu Harian
1. Klik tombol Edit pada menu yang ingin diubah
2. Foto existing akan ditampilkan (jika ada)
3. Untuk mengganti foto, pilih file baru
4. Untuk tetap menggunakan foto lama, jangan pilih file baru
5. Klik "Simpan"

### 4. Melihat Foto di Tabel
- Foto akan ditampilkan sebagai thumbnail di kolom "Foto Menu"
- Ukuran thumbnail: max 60px tinggi, 80px lebar
- Klik foto untuk melihat ukuran penuh di tab baru
- Jika tidak ada foto, akan tampil tanda "-"

## SPESIFIKASI TEKNIS

### Upload Configuration
- **Upload Path**: `./file/products/menukondimen/`
- **Allowed Types**: jpg, jpeg, png
- **Max Size**: 2048 KB (2MB)
- **File Naming**: `menu_[timestamp]_[random].[ext]`
- **Auto Create Directory**: Yes

### Validasi Client-Side (JavaScript)
```javascript
- Format: image/jpeg, image/jpg, image/png
- Max Size: 2MB
- Preview: Auto preview sebelum upload
```

### Validasi Server-Side (PHP)
```php
- allowed_types: 'jpg|jpeg|png'
- max_size: 2048 (KB)
- Error handling dengan pesan user-friendly
```

## TROUBLESHOOTING

### Jika upload gagal:
1. Pastikan folder `file/products/menukondimen/` ada dan writable (chmod 755)
2. Periksa PHP upload_max_filesize dan post_max_size di php.ini
3. Pastikan file tidak lebih dari 2MB
4. Pastikan format file adalah JPG, JPEG, atau PNG

### Jika foto tidak muncul:
1. Periksa path foto di browser console
2. Pastikan file ada di folder `file/products/menukondimen/`
3. Periksa permission folder (755) dan file (644)
4. Clear browser cache

## FITUR TAMBAHAN PADA KONDIMEN
- ✅ Nomor urut sudah digabung dengan nama kondimen (1. Nama Kondimen)
- ✅ Kolom penomoran terpisah sudah dihapus
- ✅ Tampilan lebih compact dan informatif
