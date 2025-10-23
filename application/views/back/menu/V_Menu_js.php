<script type="text/javascript">
  (function() {
    'use strict';

    if (typeof jQuery === 'undefined' || window.menuInitialized) return;
    window.menuInitialized = true;

    var base_url = $('#base_url').val();
    var dropdownData = {
      categories: [],
      thematik: [] // Changed from countries to thematik
    };

    $(document).ready(function() {
      loadData();
      loadDropdownData();

      // Hitung total harga saat checkbox komponen diubah
      $(document).on('change', '.komponen-checkbox', function() {
        hitungTotalHargaMenu();
      });

      function hitungTotalHargaMenu() {
        var total = 0;
        $('.komponen-checkbox:checked').each(function() {
          var harga = parseInt($(this).data('harga')) || 0;
          total += harga;
        });
        $('#total_harga_menu').val('Rp ' + total.toLocaleString('id-ID'));
      }
    });

    function loadDropdownData() {
      $.ajax({
        type: 'POST',
        url: '<?= base_url("Back_Menu/get_dropdown_data") ?>',
        dataType: 'json',
        success: function(result) {
          if (result.status === 'success') {
            dropdownData.categories = result.categories;
            dropdownData.thematik = result.thematik; // Changed from countries to thematik
            populateDropdowns();
          }
        }
      });
    }

    function populateDropdowns() {
      var categoryOptions = '<option value="">-- Pilih Kategori --</option>';
      dropdownData.categories.forEach(function(category) {
        categoryOptions += `<option value="${category.id_kategori}">${escapeHtml(category.nama_kategori)}</option>`;
      });
      $('#id_kategori').html(categoryOptions);

      var thematikOptions = '<option value="">-- Pilih Thematik --</option>'; // Changed from countryOptions
      dropdownData.thematik.forEach(function(thematik) { // Changed from countries to thematik
        thematikOptions += `<option value="${thematik.id_thematik}">${escapeHtml(thematik.thematik_nama)}</option>`; // Changed properties
      });
      $('#id_thematik').html(thematikOptions); // Changed from id_negara to id_thematik
    }

    function loadData() {
      $.ajax({
        url: '<?= base_url("Back_Menu/tampil") ?>',
        type: 'GET',
        dataType: 'json',
        success: renderDataTable,
        error: function() {
          showError('Gagal memuat data menu!');
        }
      });
    }

    function renderDataTable(result) {
      var html = '';
      var no = 1;
      if (result.show_data && result.show_data.length > 0) {
        result.show_data.forEach(function(item) {
          html += buildTableRow(item, no++);
        });
      } else {
        html = buildEmptyRow();
      }

      // Destroy DataTable sebelum isi HTML baru
      if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
      }

      $('#show_data_menu').html(html);

      // Inisialisasi ulang DataTable
      initDataTable();
    }

    function buildTableRow(item, no) {
      var imageHtml = item.menu_gambar ?
        `<img src="${base_url}file/products/menu/${item.menu_gambar}" class="img-fluid img-thumbnail" style="width:50px;height:50px;object-fit:cover;cursor:pointer;" alt="Menu Image" onclick="previewImageMenu('${item.menu_gambar}', '${escapeHtml(item.menu_nama)}')">` :
        '<div class="no-image" style="width: 50px; height: 50px; background: #f8f9fa; border: 1px dashed #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 4px; font-size: 10px; color: #6c757d;">No Image</div>';
      var kategori = item.nama_kategori ? escapeHtml(item.nama_kategori) : '<em class="text-muted">Tidak ada kategori</em>'; // Changed from kategori_nama
      var thematik = item.thematik_nama ? escapeHtml(item.thematik_nama) : '<em class="text-muted">Tidak ada thematik</em>'; // Changed from country_nama to thematik_nama
      var deskripsi = item.menu_deskripsi ?
        escapeHtml(item.menu_deskripsi).replace(/\n/g, '<br>') :
        '<em class="text-muted">Tidak ada deskripsi</em>';
      var harga = item.menu_harga ? 'Rp ' + parseInt(item.menu_harga).toLocaleString('id-ID') : '<em class="text-muted">-</em>';
      var statusClass = item.status_aktif == 1 ? 'badge bg-success' : 'badge bg-secondary';
      var statusText = item.status_aktif == 1 ? 'Aktif' : 'Tidak Aktif';
      var statusHtml = `<span class="${statusClass}">${statusText}</span>`;


      return `
   <tr>
     <td class="text-center">${no}</td>
     <td class="text-center">${imageHtml}</td>
     <td>${escapeHtml(item.menu_nama)}</td>
     <td>${kategori}</td>
     <td>${thematik}</td>
     <td>${deskripsi}</td>
     <td>${harga}</td>
     <td class="text-center">${statusHtml}</td>
     <td class="text-center col-aksi">
       <div class="table-action-buttons">
         <button class="btn btn-warning btn-sm btn-edit" data-id="${item.id_komponen}" type="button">
           <i class="fas fa-edit"></i> Edit
         </button>
         <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id_komponen}" type="button">
           <i class="fas fa-trash"></i> Hapus
         </button>
       </div>
     </td>
   </tr>
 `;
    }


    function buildEmptyRow() {
      return `
     <tr>
       <td class="text-center">-</td>
       <td class="text-center">-</td>
       <td class="text-center">-</td>s
       <td class="text-center">-</td>
       <td class="text-center">-</td>
       <td class="text-center">-</td>
       <td class="text-center">-</td>
       <td class="text-center">
         <div class="text-muted">
           <i class="fas fa-utensils fa-2x mb-1"></i>
           <div>Tidak ada data menu</div>
         </div>
       </td>
     </tr>
   `;
    }


    function initDataTable() {
      if ($.fn.DataTable) {
        if ($.fn.DataTable.isDataTable('#datatable')) {
          $('#datatable').DataTable().destroy();
        }
        $('#datatable').DataTable({
          responsive: true,
          autoWidth: false,
          // Hilangkan pengurutan kolom nomor
          order: [], // <-- Tidak ada urutan default
          language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data per halaman',
            zeroRecords: 'Tidak ada data yang ditemukan',
            info: 'Menampilkan halaman _PAGE_ dari _PAGES_',
            infoEmpty: 'Tidak ada data tersedia',
            paginate: {
              next: 'Selanjutnya',
              previous: 'Sebelumnya'
            }
          }
        });
      }
    }


    function escapeHtml(text) {
      var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text ? text.replace(/[&<>"']/g, function(m) {
        return map[m];
      }) : '';
    }


    // Submit form via AJAX (add/edit)
    $('#form-data').on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);
      $('#form-modal-menu-form').find(':focus').blur();
      $.ajax({
        url: '<?= base_url('back_menu/save_data') ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#form-modal-menu-form').modal('hide');
            showSuccess(res.message);
          } else {
            showError(res.message);
          }
        },
        error: function() {
          showError('Terjadi kesalahan saat menyimpan data!');
        }
      });
    });


    // Reload data otomatis setelah modal tertutup (add/edit)
    $(document).on('hidden.bs.modal', '#form-modal-menu-form', function() {
      loadData();
    });


    $(document).on('hide.bs.modal', '.modal', function() {
      $(this).find(':focus').blur();
    });


    window.tambah_data = function() {
      $('#form-modal-menu-form').modal('show');
      $('#modalMenuLabel').text('Tambah Menu');
      $('#stat').val('new');
      $('#id').val('');
      $('#id_komponen').val('');
      $('#menu_nama').val('');
      $('#menu_deskripsi').val('');
      $('#menu_harga').val('');
      $('#id_kategori').val('');
      $('#id_thematik').val(''); // Changed from id_negara to id_thematik
      $('#status_aktif').val('1');
      $('#menu_gambar').val('');
      $('#image-preview').hide();
      $('#preview-img').attr('src', '');
    };


    $(document).on('click', '.btn-edit', function() {
      var id = $(this).data('id');
      window.editData(id);
    });


    window.editData = function(id) {
      $.ajax({
        url: base_url + 'Back_Menu/edit_data',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#id_komponen').val(res.data.id_komponen);
            $('#menu_nama').val(res.data.menu_nama);
            $('#menu_deskripsi').val(res.data.menu_deskripsi);
            $('#menu_harga').val(res.data.menu_harga);
            $('#id_kategori').val(res.data.id_kategori);
            $('#id_thematik').val(res.data.id_thematik); // Changed from id_negara
            $('#status_aktif').val(res.data.status_aktif);
            $('#id').val(res.data.id_komponen);
            $('#stat').val('edit');
            $('#form-modal-menu-form').modal('show');
            $('#modalMenuLabel').text('Edit Menu');
            $('#menu_gambar').val('');
            if (res.data.menu_gambar) {
              $('#image-preview').show();
              $('#preview-img').attr('src', base_url + 'file/products/menu/' + res.data.menu_gambar);
            } else {
              $('#image-preview').hide();
              $('#preview-img').attr('src', '');
            }
          } else {
            showError('Data menu tidak ditemukan!');
          }
        },
        error: function() {
          showError('Gagal mengambil data menu!');
        }
      });
    };


    $(document).on('click', '.btn-delete', function() {
      var id = $(this).data('id');
      window.deleteData(id);
    });


    window.deleteData = function(id) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Hapus Menu?',
          text: 'Data yang dihapus tidak dapat dikembalikan!',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Ya, Hapus!',
          cancelButtonText: 'Batal',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            ajaxDeleteMenu(id);
          }
        });
      } else {
        if (confirm('Yakin ingin menghapus menu ini?')) {
          ajaxDeleteMenu(id);
        }
      }
    };


    // Delete data (langsung reload di AJAX success)
    function ajaxDeleteMenu(id) {
      $.ajax({
        url: base_url + 'Back_Menu/delete_data',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          showSuccess(res.message);
          if (res.status === 'success') {
            loadData(); // Data reload otomatis setelah delete
          }
        },
        error: function() {
          showError('Gagal menghapus data!');
        }
      });
    }


    window.previewImageMenu = function(imageName, menuName) {
      var imageUrl = base_url + 'file/products/menu/' + imageName;
      $('#staticBackdropLabel').text('Preview Image: ' + menuName);
      $('#xpreview_image').html('<img src="' + imageUrl + '" class="img-fluid" style="max-width:50%;">');
      $('#form-modal-preview-image').modal('show');
    };


    // Toast & error popup
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
          title: 'Error!',
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


    // Debug di awal fungsi load data
    function tampil_data() {
      $.ajax({
        url: "<?= base_url('Back_Menu/tampil') ?>",
        type: "GET",
        dataType: "JSON",
        success: function(data) {
          console.log("Data from server:", data); // Cek hasil response AJAX
          // lanjutkan proses render tabel
        }
      });
    }


  })();
</script>