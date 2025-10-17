# 📊 SISTEM MANAJEMEN FOOD COST MENU REGULAR

## 🎯 **Overview**

Sistem manajemen input untuk menghitung food cost menu regular dengan rumus perhitungan yang akurat sesuai dengan kebutuhan bisnis catering.

## 📋 **Features**

### ✅ **Input Management**

- **Multi-Bahan Input**: Tambah bahan dengan dinamis
- **Real-time Calculation**: Perhitungan otomatis saat input
- **Flexible Satuan**: Support berbagai satuan (kg, biji, pcs, dll)
- **Validation**: Validasi input untuk memastikan data akurat

### 📊 **Calculation System**

```
RUMUS PERHITUNGAN:
1. Harga Bahan Mentah (1 porsi) = (Qty × Harga per Satuan) ÷ Pembagian Porsi
2. Total Bahan Mentah = Σ semua harga bahan mentah (1 porsi)
3. Biaya Produksi = Total Bahan Mentah × 20%
4. Food Cost = Total Bahan Mentah + Biaya Produksi
```

### 📈 **Dashboard & Analytics**

- **Summary Statistics**: Total menu, rata-rata food cost
- **Real-time Updates**: Data terbaru setiap saat
- **Visual Indicators**: Badge dan color coding untuk clarity

## 🛠 **Technical Implementation**

### **Database Structure**

```sql
-- Tabel Menu
menu_regular_food_cost:
- id (Primary Key)
- nama_menu
- deskripsi
- created_at, updated_at

-- Tabel Bahan Detail
menu_regular_bahan:
- id (Primary Key)
- menu_id (Foreign Key)
- nama_bahan
- qty (decimal)
- satuan
- harga_per_satuan (decimal)
- pembagian_porsi (int)
- urutan
```

### **File Structure**

```
application/
├── controllers/
│   └── Back_Food_Cost.php          # Main controller
├── models/
│   └── M_Food_Cost.php             # Data model
├── views/back/food_cost/
│   ├── V_Food_Cost.php             # Main view
│   └── V_Food_Cost_js.php          # JavaScript logic
└── config/
    └── routes.php                  # Route configuration
```

## 🚀 **Usage Guide**

### **1. Akses Sistem**

```
URL: http://domain.com/food-cost
```

### **2. Tambah Menu Baru**

1. Klik tombol **"Tambah Menu"**
2. Isi **Nama Menu** dan **Deskripsi**
3. Tambah bahan dengan klik **"Tambah Bahan"**
4. Untuk setiap bahan, isi:
   - **Nama Bahan** (contoh: beras, ayam, dll)
   - **Qty** (jumlah: 1, 1.2, 0.5)
   - **Satuan** (kg, biji, pcs)
   - **Harga per Satuan** (dalam Rupiah)
   - **Pembagian Porsi** (berapa porsi dihasilkan)

### **3. Real-time Calculation**

Sistem akan menghitung otomatis:

- **Harga per Porsi** untuk setiap bahan
- **Total Bahan Mentah**
- **Biaya Produksi (20%)**
- **Food Cost Final**

### **4. Manage Data**

- **Edit**: Klik icon edit untuk mengubah data
- **Delete**: Klik icon hapus untuk menghapus menu
- **View Detail**: Lihat breakdown lengkap perhitungan

## 📊 **Example Calculation**

### **Sample Data: Nasi Gudeg Jogja**

```
Bahan Input:
1. Beras: 1 kg × Rp13,900 ÷ 6 porsi = Rp2,316.67/porsi
2. Ayam: 1.2 kg × Rp33,000 ÷ 8 porsi = Rp4,950.00/porsi
3. Nangka: 0.5 kg × Rp8,000 ÷ 8 porsi = Rp500.00/porsi
... dst

HASIL PERHITUNGAN:
├── Total Bahan Mentah: Rp11,328.17
├── Biaya Produksi (20%): Rp2,265.63
└── FOOD COST: Rp13,593.80
```

## 🔧 **Installation Steps**

### **1. Database Setup**

```sql
-- Jalankan script SQL
mysql -u username -p database_name < sql/food_cost_setup.sql
```

### **2. File Installation**

- Copy semua file ke folder CodeIgniter
- Pastikan URL helper sudah ter-load di autoload.php

### **3. Routes Configuration**

Routes sudah otomatis ditambahkan ke `routes.php`

### **4. Dependencies**

- **jQuery**: Ajax operations
- **Bootstrap**: UI framework
- **SweetAlert2**: Notifications
- **DataTables**: Table management (optional)

## 🎨 **UI/UX Features**

### **Modern Interface**

- **Responsive Design**: Mobile-friendly
- **Real-time Preview**: Lihat perhitungan langsung
- **Color Coding**: Visual indicators untuk berbagai status
- **Modal Forms**: Clean input experience

### **User Experience**

- **Auto-calculation**: Tidak perlu manual hitung
- **Dynamic Rows**: Tambah/hapus bahan dengan mudah
- **Validation**: Error handling yang informatif
- **Loading States**: Progress indicators

## 📱 **Mobile Responsive**

- ✅ Tablet friendly
- ✅ Mobile optimized
- ✅ Touch-friendly buttons
- ✅ Readable typography

## 🔒 **Security Features**

- **Input Validation**: Server-side validation
- **SQL Injection Protection**: Prepared statements
- **XSS Protection**: HTML escaping
- **CSRF Protection**: CodeIgniter built-in

## 📈 **Performance Optimization**

- **AJAX Loading**: No page refresh
- **Efficient Queries**: Optimized database calls
- **Minimal Dependencies**: Lightweight implementation
- **Caching**: Static asset caching

## 🚀 **Future Enhancements**

- [ ] Export to Excel/PDF
- [ ] Bulk import bahan
- [ ] Recipe management
- [ ] Cost comparison analysis
- [ ] Integration with inventory
- [ ] Profit margin calculator

## 📞 **Support**

Untuk bantuan teknis atau pertanyaan, silakan hubungi developer atau buat issue di repository.

---

**Developed with ❤️ for Ryan Catering Management System**
