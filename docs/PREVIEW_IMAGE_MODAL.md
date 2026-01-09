# FITUR PREVIEW FOTO MENU - MODAL POPUP

## Yang Telah Ditambahkan

### 1. File Baru
**Path**: `application/views/back/menu_harian/preview_image.php`

Modal popup untuk preview foto menu dengan fitur:
- Header dengan nama menu
- Gambar dalam ukuran penuh (responsive)
- Loading indicator saat gambar dimuat
- Style modern dengan shadow dan border-radius
- Tombol close

### 2. Modifikasi File

#### A. V_Menu_Harian.php
✅ Include file preview_image.php
✅ Tambah CSS untuk hover effect pada foto
✅ Style untuk thumbnail foto di tabel

#### B. V_Menu_Harian_js.php
✅ Ubah link foto dari `target="_blank"` ke modal popup
✅ Tambah class `btn-preview-foto` dan data attributes
✅ Event handler untuk klik foto: `$(document).on('click', '.btn-preview-foto')`
✅ Inject image dan nama menu ke modal

## Cara Kerja

1. **Di Tabel**: Foto ditampilkan sebagai thumbnail (60x80px)
2. **Hover Effect**: Saat mouse hover, foto akan zoom dan border berubah warna
3. **Klik Foto**: Modal popup muncul dengan:
   - Header: "Preview Foto Menu" + nama menu
   - Body: Gambar dalam ukuran penuh (max-width: 100%)
   - Footer: Tombol Close

## Code Flow

```javascript
// 1. User klik foto
$('.btn-preview-foto').click()

// 2. Ambil data
var fotoPath = $(this).data('foto');  // Path foto
var namaMenu = $(this).data('menu');  // Nama menu

// 3. Set ke modal
$('#preview-menu-text').text(namaMenu);
$('#xpreview_image').html('<img src="...">');

// 4. Show modal
$('#form-modal-preview-image').modal('show');
```

## Fitur UI/UX

### Hover Effect
- Scale up 1.05x saat hover
- Border berubah dari #e9ecef → #007bff
- Box shadow muncul dengan warna biru

### Modal Header
- Background primary (biru)
- Text putih
- Nama menu dengan icon utensils
- Tombol close di kanan atas

### Modal Body
- Image responsive (max-width: 100%)
- Border radius 8px
- Box shadow untuk depth
- Loading indicator saat gambar belum load

## Data Attributes

```html
<a href="javascript:void(0);" 
   class="btn-preview-foto" 
   data-foto="path/to/image.jpg" 
   data-menu="Nasi Goreng">
  <img src="..." />
</a>
```

- `data-foto`: Full path ke file gambar
- `data-menu`: Nama menu untuk ditampilkan di header

## Bootstrap Modal

Modal menggunakan:
- `modal-lg`: Modal besar untuk foto
- `modal-dialog-scrollable`: Scroll jika gambar terlalu besar
- `data-backdrop="static"`: Tidak close saat klik di luar
- Bootstrap 4 syntax

## Browser Compatibility

✅ Chrome/Edge (modern)
✅ Firefox
✅ Safari
✅ Mobile browsers (responsive)

## Testing

Untuk test fitur ini:
1. Refresh halaman Menu Harian
2. Lihat tabel dengan foto menu
3. Hover mouse ke foto → harus ada zoom effect
4. Klik foto → modal popup muncul
5. Cek header modal → harus ada nama menu
6. Cek gambar → harus full size dan responsive
7. Klik tombol Close atau X → modal tertutup

## Keuntungan vs Target Blank

### Sebelum (Target Blank)
- ❌ Buka tab baru di browser
- ❌ User harus close tab manual
- ❌ Tidak ada context nama menu
- ❌ UX kurang smooth

### Sekarang (Modal Popup)
- ✅ Preview di halaman yang sama
- ✅ Auto close dengan tombol
- ✅ Ada context nama menu di header
- ✅ UX lebih smooth dan modern
- ✅ Responsive untuk mobile
- ✅ Hover effect untuk feedback visual
