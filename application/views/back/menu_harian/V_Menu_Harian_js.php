(function() {
'use strict';

if (typeof jQuery === 'undefined' || window.menuHarianInitialized) return;
window.menuHarianInitialized = true;

var base_url = $('#ajax_url').val();
var kondimenList = [];
var menuList = [];
var menuHarianTable = null;

// Render radio kantin
function renderKantinRadio(kantinList) {
var html = '';
if (Array.isArray(kantinList) && kantinList.length > 0) {
kantinList.forEach(function(kantin) {
html += "<div class='form-check'>" +
  "<input class='form-check-input' type='radio' name='id_kantin' id='kantin_" + kantin.id_kantin + "' value='" + kantin.id_kantin + "' required>" +
  "<label class='form-check-label' for='kantin_" + kantin.id_kantin + "'>" + kantin.nama_kantin + "</label>" +
  "</div>";
});
}
$('#kantin-radio-group').html(html);
}

$(document).ready(function() {
// ✅ LOAD DATA SAAT HALAMAN SIAP
loadMenuHarianData();
loadCustomerOptions();
loadKantinRadioOptions();
loadMenuList(); // ✅ PINDAHKAN KE SINI!

// ✅ EVENT HANDLER CUSTOMER CHANGE
$('#id_customer').on('change', function() {
var id_customer = $(this).val();
console.log('[menu_harian] Customer changed:', id_customer);
loadKantinRadioOptions(id_customer);
});

window.tambah_menu_harian = function() {
kondimenList = [];
$('#form-menu-harian')[0].reset();
$('#id_menu_harian').val('');
renderKondimenTable();
var modal = new bootstrap.Modal(document.getElementById('form-modal-menu-harian'));
modal.show();
$('#modalMenuHarianLabel').text('Tambah Menu Harian');
};

$(document).on('hidden.bs.modal', '#form-modal-menu-harian', function() {
// Tidak perlu reload di sini
});

$('#btn-tambah-kondimen').on('click', function() {
console.log('[menu_harian] Tambah kondimen clicked, menuList.length:', menuList.length);
if (menuList.length === 0) {
console.log('[menu_harian] menuList empty, loading first...');
loadMenuList(function() {
tambahKondimenRow();
});
} else {
tambahKondimenRow();
}
});

$('#form-menu-harian').on('submit', function(e) {
e.preventDefault();
if (kondimenList.length === 0) {
showError('Menu kondimen wajib diisi!');
return;
}
for (var k of kondimenList) {
if (!k.id_komponen || !k.qty) {
showError('Semua kondimen dan qty harus diisi!');
return;
}
}

var formData = new FormData(this);
formData.append('kondimen', JSON.stringify(kondimenList));

var $submitButton = $(this).find('button[type="submit"]');
$submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

$.ajax({
url: base_url + '/save',
type: 'POST',
data: formData,
processData: false,
contentType: false,
dataType: 'json',
success: function(res) {
if (res.status === 'success') {
$('#form-modal-menu-harian').modal('hide');
showSuccess(res.msg || 'Menu harian berhasil disimpan!');
loadMenuHarianData();
} else {
showError(res.msg || 'Gagal menyimpan data!');
}
},
error: function(jqXHR, textStatus, errorThrown) {
console.error("AJAX Error: ", textStatus, errorThrown, jqXHR.responseText);
showError('Terjadi kesalahan saat menyimpan data! Cek console log.');
},
complete: function() {
$submitButton.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan');
}
});
});

$(document).on('click', '.btn-edit', function() {
var id = $(this).data('id');
window.edit_menu_harian(id);
});

window.edit_menu_harian = function(id) {
$.get(base_url + '/get_by_id/' + id, function(response) {
if (response.status === 'success') {
var data = response.data;
var kondimen = response.kondimen || [];

$('#id_menu_harian').val(data.id_menu_harian);
$('#tanggal').val(data.tanggal);
$('#shift').val(data.shift);
$('#jenis_menu').val(data.jenis_menu);
$('#id_customer').val(data.id_customer);
$('#id_kantin').val(data.id_kantin);
$('#nama_menu').val(data.nama_menu);
$('#total_orderan_perkantin').val(data.total_orderan_perkantin);

kondimenList = kondimen.map(function(k) {
return {
id_komponen: k.id_komponen,
kategori: k.kategori_kondimen,
qty: k.qty_kondimen
};
});
renderKondimenTable();

$('#form-modal-menu-harian').modal('show');
$('#modalMenuHarianLabel').text('Edit Menu Harian');
} else {
showError(response.msg || 'Data tidak ditemukan!');
}
}, 'json').fail(function() {
showError('Gagal mengambil data!');
});
};

$(document).on('click', '.btn-delete', function() {
var id = $(this).data('id');
window.delete_menu_harian(id);
});

window.delete_menu_harian = function(id) {
if (typeof Swal !== 'undefined') {
Swal.fire({
title: 'Hapus Menu Harian?',
text: 'Data yang dihapus (termasuk kondimennya) tidak dapat dikembalikan!',
icon: 'warning',
showCancelButton: true,
confirmButtonColor: '#d33',
cancelButtonColor: '#3085d6',
confirmButtonText: 'Ya, Hapus!',
cancelButtonText: 'Batal',
reverseButtons: true
}).then((result) => {
if (result.isConfirmed) {
ajaxDeleteMenuHarian(id);
}
});
} else {
if (confirm('Yakin ingin menghapus menu harian ini?')) {
ajaxDeleteMenuHarian(id);
}
}
};

function ajaxDeleteMenuHarian(id) {
$.ajax({
url: base_url + '/delete/' + id,
type: 'POST',
dataType: 'json',
success: function(res) {
if (res.status === 'success') {
showSuccess(res.message || 'Menu harian dihapus!');
loadMenuHarianData();
} else {
showError(res.message || 'Gagal menghapus data');
}
},
error: function() {
showError('Gagal menghapus data!');
}
});
}

function loadCustomerOptions() {
$.get(base_url + '/get_customers', function(data) {
var options = '<option value="">-- Pilih Customer --</option>';
if (data && data.length > 0) {
$.each(data, function(_, customer) {
options += `<option value="${customer.id_customer}">${customer.nama_customer}</option>`;
});
}
$('#id_customer').html(options);
}, 'json');
}

function loadKantinRadioOptions(id_customer) {
var url = id_customer ? (base_url + '/get_kantin_by_customer') : (base_url + '/get_kantins');
var dataAjax = id_customer ? {
id_customer: id_customer
} : {};
$.ajax({
url: url,
type: id_customer ? 'POST' : 'GET',
data: dataAjax,
dataType: 'json',
success: function(kantinList) {
renderKantinRadio(kantinList);
}
});
}

function loadMenuHarianData() {
if ($.fn.DataTable && $.fn.DataTable.isDataTable('#menu-harian-table')) {
$('#menu-harian-table').DataTable().destroy();
}
$('#menu-harian-table tbody').html(buildLoadingRow());
$('#menu-harian-table').show();

$.ajax({
url: base_url + '/ajax_list',
type: 'GET',
dataType: 'json',
success: renderMenuHarianTable,
error: function() {
showError('Gagal memuat data menu harian!');
}
});
}

function renderMenuHarianTable(result) {
var html = '';
var no = 1;

if (result && result.show_data && Array.isArray(result.show_data) && result.show_data.length > 0) {
result.show_data.forEach(function(item) {
html += buildTableRow(item, no++);
});
} else {
html = buildEmptyRow();
}

if ($.fn.DataTable && $.fn.DataTable.isDataTable('#menu-harian-table')) {
$('#menu-harian-table').DataTable().destroy();
}
$('#menu-harian-table tbody').html(html);
initDataTable();
}

function buildTableRow(item, no) {
function getShiftBadge(shift) {
var badgeClass = '';
var badgeText = shift.charAt(0).toUpperCase() + shift.slice(1);
switch (shift) {
case 'lunch':
badgeClass = 'badge bg-success text-white';
break;
case 'dinner':
badgeClass = 'badge bg-primary text-white';
break;
case 'supper':
badgeClass = 'badge bg-danger text-white';
break;
default:
badgeClass = 'badge bg-secondary text-white';
}
return `<span class="${badgeClass}">${badgeText}</span>`;
}

function getKategoriBadge(kategori) {
var badgeClass = 'badge bg-secondary text-white';
switch (kategori.toLowerCase()) {
case 'lauk utama':
badgeClass = 'badge bg-primary text-white';
break;
case 'pendamping kering':
badgeClass = 'badge bg-warning text-dark';
break;
case 'pendamping basah':
badgeClass = 'badge bg-info text-white';
break;
case 'sayur':
badgeClass = 'badge bg-success text-white';
break;
case 'buah':
badgeClass = 'badge bg-danger text-white';
break;
case 'sambal':
badgeClass = 'badge bg-dark text-white';
break;
case 'nasi':
badgeClass = 'badge bg-secondary text-white';
break;
case 'sayuran berkuah':
badgeClass = 'badge bg-success text-white';
break;
case 'tumisan':
badgeClass = 'badge bg-info text-white';
break;
default:
badgeClass = 'badge bg-danger text-white';
}
return `<span class="${badgeClass}">${kategori}</span>`;
}

var kondimen = item.kondimen ? item.kondimen : '-';
if (kondimen !== '-') {
var kondimenItems = kondimen.split(', ');
kondimen = kondimenItems.map(function(str, idx) {
var match = str.match(/^(.*?) \((.*?)\) \((.*?)\)$/);
if (match) {
var nama = match[1];
var kategori = match[2];
var qty = match[3];
return `${idx + 1}. ${nama} ${getKategoriBadge(kategori)} (${qty})`;
} else {
return `${idx + 1}. ${str}`;
}
}).join('<br>');
}
return `
<tr>
  <td class="text-center">${no}</td>
  <td>${item.tanggal}</td>
  <td class="text-center">${getShiftBadge(item.shift)}</td>
  <td>${item.nama_customer}</td>
  <td>${item.nama_kantin}</td>
  <td>${item.jenis_menu}</td>
  <td>${item.nama_menu}</td>
  <td>${kondimen}</td>
  <td class="text-end">${item.total_orderan_perkantin}</td>
  <td class="text-center">
    <div class="btn-group" role="group" aria-label="Actions">
      <button type="button" class="btn btn-warning btn-sm" onclick="edit_menu_harian(${item.id_menu_harian})" title="Edit">
        <i class="fas fa-edit"></i>
      </button>
      <button type="button" class="btn btn-danger btn-sm" onclick="delete_menu_harian(${item.id_menu_harian})" title="Hapus">
        <i class="fas fa-trash"></i>
      </button>
  </td>
</tr>
`;
}

function buildLoadingRow() {
return `
<tr>
  <td class="text-center" colspan="9">
    <div class="text-muted p-3">
      <i class="fas fa-spinner fa-spin fa-2x mb-1"></i>
      <div>Memuat data...</div>
    </div>
  </td>
</tr>
`;
}

function buildEmptyRow() {
return `
<tr>
  <td class="text-center" colspan="9">
    <div class="text-muted p-3">
      <i class="fas fa-utensils fa-2x mb-1"></i>
      <div>Tidak ada data menu harian</div>
    </div>
  </td>
</tr>
`;
}

function initDataTable() {
if ($.fn.DataTable) {
if ($.fn.DataTable.isDataTable('#menu-harian-table')) {
$('#menu-harian-table').DataTable().destroy();
}
$('#menu-harian-table').DataTable({
responsive: true,
autoWidth: false,
order: [],
language: {
search: 'Cari:',
lengthMenu: 'Tampilkan _MENU_ data',
zeroRecords: 'Tidak ada data yang ditemukan',
info: 'Menampilkan _START_ s/d _END_ dari _TOTAL_ data',
infoEmpty: 'Tidak ada data tersedia',
infoFiltered: '(disaring dari _MAX_ total data)',
paginate: {
first: 'Pertama',
last: 'Terakhir',
next: '›',
previous: '‹'
}
}
});
}
}

function tambahKondimenRow() {
var existingIds = kondimenList.map(function(kondimen) {
return kondimen.id_komponen;
});

var newId = '';

if (existingIds.includes(newId) && newId !== '') {
showError('Menu kondimen sudah ada di tabel!');
return;
}

kondimenList.push({
id_komponen: newId,
kategori: '',
qty: ''
});
renderKondimenTable();
}

// ✅ FUNGSI RENDER KONDIMEN TABLE YANG DIPERBAIKI
function renderKondimenTable() {
console.log('[menu_harian] renderKondimenTable called, kondimenList:', kondimenList);

if (menuList.length === 0) {
console.log('[menu_harian] menuList empty, loading...');
loadMenuList(function() {
renderKondimenTable();
});
return;
}

console.log('[menu_harian] Rendering with', menuList.length, 'menu items');

var tbody = '';
kondimenList.forEach(function(kondimen, idx) {
var options = '<option value="">-- Pilih Kondimen --</option>';
menuList.forEach(function(menu) {
var selected = menu.id_komponen == kondimen.id_komponen ? 'selected' : '';
options += `<option value="${menu.id_komponen}" ${selected}>${menu.menu_nama}</option>`;
});
tbody += `<tr>
  <td class="text-center">${idx + 1}</td>
  <td>
    <select class="form-control kondimen-nama" data-idx="${idx}">
      ${options}
    </select>
  </td>
  <td>
    <input type="text" class="form-control kondimen-kategori" data-idx="${idx}" value="${kondimen.kategori || ''}" readonly>
  </td>
  <td>
    <input type="number" class="form-control kondimen-qty" data-idx="${idx}" value="${kondimen.qty || ''}" required min="1" step="1">
  </td>
  <td class="text-center">
    <button type="button" class="btn btn-danger btn-sm" onclick="hapusKondimenRow(${idx})">
      <i class="fas fa-trash"></i>
    </button>
  </td>
</tr>`;
});
$('#table-kondimen-menu tbody').html(tbody);

// Aktifkan Select2
$('.kondimen-nama').select2({
width: '100%',
dropdownParent: $('#form-modal-menu-harian'),
placeholder: '-- Pilih Kondimen --'
});

// ✅ HAPUS EVENT LAMA DULU (PENTING!)
$('.kondimen-nama').off('change').on('change', function() {
var idx = $(this).data('idx');
var id_komponen = $(this).val();

console.log('[menu_harian] Kondimen changed at index', idx, '-> id_komponen:', id_komponen);

// Cek duplikat
var isDuplicate = kondimenList.some(function(kondimen, i) {
return kondimen.id_komponen === id_komponen && i !== idx && id_komponen !== '';
});

if (isDuplicate) {
showError('Menu kondimen sudah ada di tabel!');
$(this).val('').trigger('change.select2');
kondimenList[idx].id_komponen = '';
kondimenList[idx].kategori = '';
$('.kondimen-kategori[data-idx="' + idx + '"]').val('');
return;
}

kondimenList[idx].id_komponen = id_komponen;

// ✅ AJAX AMBIL KATEGORI
if (id_komponen) {
console.log('[menu_harian] Fetching kategori for id_komponen:', id_komponen);
$.ajax({
url: base_url + '/get_kategori_by_menu',
type: 'POST',
data: { id_komponen: id_komponen },
dataType: 'json',
success: function(data) {
console.log('[menu_harian] Kategori response:', data);
if (data && data.nama_kategori) {
kondimenList[idx].kategori = data.nama_kategori;
$('.kondimen-kategori[data-idx="' + idx + '"]').val(data.nama_kategori);
} else {
showError('Kategori tidak ditemukan!');
}
},
error: function(xhr, status, error) {
console.error('[menu_harian] Error fetching kategori:', error, xhr.responseText);
showError('Gagal memuat kategori menu!');
}
});
} else {
kondimenList[idx].kategori = '';
$('.kondimen-kategori[data-idx="' + idx + '"]').val('');
}
});

// Event handler qty
$('.kondimen-qty').off('input').on('input', function() {
var idx = $(this).data('idx');
kondimenList[idx].qty = $(this).val();
console.log('[menu_harian] Qty changed at index', idx, '->', $(this).val());
});

// ✅ AUTO-TRIGGER CHANGE UNTUK KONDIMEN YANG SUDAH ADA DATA (SAAT EDIT)
kondimenList.forEach(function(kondimen, idx) {
if (kondimen.id_komponen) {
console.log('[menu_harian] Auto-triggering change for existing kondimen at index', idx);
setTimeout(function() {
$('.kondimen-nama[data-idx="' + idx + '"]').trigger('change');
}, 100); // Delay 100ms untuk pastikan Select2 sudah ready
}
});
}

window.hapusKondimenRow = function(idx) {
console.log('[menu_harian] Hapus kondimen at index', idx);
kondimenList.splice(idx, 1);
renderKondimenTable();
};

function showSuccess(message) {
if (typeof Swal !== 'undefined') {
Swal.fire({
title: 'Berhasil!',
text: message,
icon: 'success',
timer: 2000,
showConfirmButton: false,
toast: true,
position: 'top-end'
});
} else {
alert('✅ ' + message);
}
}

function showError(message) {
if (typeof Swal !== 'undefined') {
Swal.fire({
title: 'Gagal!',
text: message,
icon: 'error',
timer: 3000,
showConfirmButton: false,
toast: true,
position: 'top-end'
});
} else {
alert('❌ ' + message);
}
}

function loadMenuList(callback) {
console.log('[menu_harian] loadMenuList -> URL:', base_url + '/get_menu_list');
$.ajax({
url: base_url + '/get_menu_list',
type: 'GET',
dataType: 'json',
success: function(data) {
console.log('[menu_harian] get_menu_list response:', data);
menuList = data;
console.log('[menu_harian] menuList loaded with', menuList.length, 'items');
if (typeof callback === 'function') callback();
},
error: function(xhr, status, error) {
console.error('[menu_harian] Error loading menu list:', error, xhr.responseText);
showError('Gagal memuat daftar menu!');
}
});
}

// Load menu saat halaman siap
loadMenuList();

loadKantinRadioOptions();

$('#id_customer').on('change', function() {
var id_customer = $(this).val();
loadKantinRadioOptions(id_customer);
});
}); // ✅ PENUTUP $(document).ready()

})(); // ✅ PENUTUP IIFE