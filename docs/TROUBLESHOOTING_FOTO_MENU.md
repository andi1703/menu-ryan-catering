# TROUBLESHOOTING - Foto Menu Tidak Muncul

## Masalah yang Diperbaiki
Foto menu tidak muncul di tabel setelah disimpan

## Penyebab
1. Field `foto_menu` tidak dimasukkan ke array `$grouped` di controller
2. Field `foto_menu` tidak di-preserve saat loop grouping data
3. Path foto tidak konsisten

## Solusi yang Diterapkan

### 1. Controller (Back_Menu_Harian.php) - Method ajax_list()

**Perubahan 1: Tambahkan foto_menu ke array grouped**
```php
if (!isset($grouped[$key])) {
  $grouped[$key] = [
    'tanggal' => $item['tanggal'],
    'shift' => $item['shift'],
    'nama_customer' => $item['nama_customer'],
    'jenis_menu' => $item['jenis_menu'],
    'nama_menu' => $item['nama_menu'],
    'remark' => $item['remark'],
    'foto_menu' => $item['foto_menu'] ?? null,  // ← DITAMBAHKAN
    'ids' => [],
    'kantins' => [],
    'kondimen_data' => [],
    'total_orderan' => 0,
    'created_at' => $item['created_at'] ?? null,
    'updated_at' => $item['updated_at'] ?? null
  ];
}
```

**Perubahan 2: Preserve foto_menu saat looping**
```php
// Preserve foto_menu jika belum ada
if (empty($grouped[$key]['foto_menu']) && !empty($item['foto_menu'])) {
  $grouped[$key]['foto_menu'] = $item['foto_menu'];
}
```

### 2. JavaScript (V_Menu_Harian_js.php)

**Perubahan 1: Perbaiki path foto di buildTableRow()**
```javascript
var fotoMenu = '';
if (item.foto_menu && item.foto_menu !== '' && item.foto_menu !== null) {
  var basePath = base_url.split('/back_menu_harian')[0];
  var fotoPath = basePath + '/file/products/menukondimen/' + item.foto_menu;
  fotoMenu = `<a href="${fotoPath}" target="_blank" title="Lihat Foto Menu">
                <img src="${fotoPath}" alt="Foto Menu" class="img-thumbnail" 
                     style="max-height: 60px; max-width: 80px; object-fit: cover; cursor: pointer;" 
                     onerror="this.parentElement.innerHTML='<span class=\\'text-muted small\\'>Foto Error</span>';">
              </a>`;
} else {
  fotoMenu = '<span class="text-muted small">-</span>';
}
```

**Perubahan 2: Perbaiki path foto di edit_menu_harian()**
```javascript
if (res.data.foto_menu && res.data.foto_menu !== '' && res.data.foto_menu !== null) {
  var basePath = base_url.split('/back_menu_harian')[0];
  var fotoPath = basePath + '/file/products/menukondimen/' + res.data.foto_menu;
  $('#preview-foto-menu img').attr('src', fotoPath);
  $('#preview-foto-menu').show();
  $('#foto_menu_existing').val(res.data.foto_menu);
}
```

**Perubahan 3: Tambah console.log untuk debugging**
```javascript
function renderMenuHarianTable(result) {
  console.log('Data received in renderMenuHarianTable:', result);
  
  result.show_data.forEach(function(item) {
    console.log('Item foto_menu:', item.foto_menu);
    html += buildTableRow(item, no++);
  });
}
```

## Cara Test

### 1. Buka Browser Console (F12)
Setelah refresh halaman Menu Harian, cek console:
- Harus muncul log: `Data received in renderMenuHarianTable:`
- Harus muncul log: `Item foto_menu:` untuk setiap item
- Jika foto_menu bernilai `null` atau `undefined`, berarti data tidak tersimpan

### 2. Cek Network Tab
- Buka Network tab di browser console
- Cari request ke `ajax_list`
- Klik dan lihat Response
- Pastikan ada field `foto_menu` di setiap item di `show_data`

### 3. Cek File Upload
Pastikan file foto benar-benar tersimpan:
```
c:\laragon\www\menu-ryan-catering\file\products\menukondimen\
```

### 4. Cek Database
Query untuk cek apakah foto tersimpan:
```sql
SELECT id_menu_harian, nama_menu, foto_menu 
FROM menu_harian 
WHERE foto_menu IS NOT NULL 
ORDER BY id_menu_harian DESC;
```

## Path Foto yang Benar

Jika base_url adalah: `http://localhost/menu-ryan-catering/back_menu_harian`

Maka path foto akan menjadi:
```
http://localhost/menu-ryan-catering/file/products/menukondimen/menu_1234567890_1234.jpg
```

## Troubleshooting Lanjutan

### Jika foto masih tidak muncul:

1. **Cek Permission Folder**
   ```bash
   chmod 755 c:\laragon\www\menu-ryan-catering\file\products\menukondimen\
   chmod 644 c:\laragon\www\menu-ryan-catering\file\products\menukondimen\*.jpg
   ```

2. **Cek .htaccess**
   Pastikan file `.htaccess` ada di folder menukondimen dengan isi:
   ```apache
   <FilesMatch "\.(jpg|jpeg|png|gif)$">
       Allow from all
   </FilesMatch>
   ```

3. **Cek Console Error**
   - Buka browser console (F12)
   - Klik tab Console
   - Lihat apakah ada error 404 atau 403 saat load gambar

4. **Test Direct Access**
   Copy path foto dari HTML source, paste di browser:
   ```
   http://localhost/menu-ryan-catering/file/products/menukondimen/menu_1234567890_1234.jpg
   ```
   Jika error 404: File tidak ada di folder
   Jika error 403: Permission issue

5. **Cek base_url**
   Tambahkan console.log di buildTableRow:
   ```javascript
   console.log('base_url:', base_url);
   console.log('basePath:', basePath);
   console.log('fotoPath:', fotoPath);
   ```

## Hasil yang Diharapkan

Setelah perbaikan:
- ✅ Foto muncul di tabel dengan thumbnail 60x80px
- ✅ Foto bisa diklik untuk melihat full size di tab baru
- ✅ Saat edit, foto existing muncul di preview
- ✅ Jika foto error/tidak ada, muncul tanda "-"
- ✅ Console.log menunjukkan data foto_menu terkirim
