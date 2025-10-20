<script type="text/javascript">
  (function() {
    'use strict';

    if (typeof jQuery === 'undefined' || window.menuInitialized) return;
    window.menuInitialized = true;

    var base_url = '<?= base_url() ?>';
    var dropdownData = {
      categories: [],
      thematics: []
    };

    $(document).ready(function() {
      loadDropdownData();
      loadData();
    });

    // ===== LOAD DROPDOWN DATA =====
    function loadDropdownData() {
      $.ajax({
        type: 'POST',
        url: base_url + 'menu/get_dropdown_data',
        dataType: 'json',
        success: function(result) {
          if (result.status === 'success') {
            dropdownData.categories = result.kategori || [];
            dropdownData.thematics = result.thematik || [];
            populateDropdowns();
          }
        },
        error: function(xhr) {
          console.error('Error loading dropdown:', xhr.responseText);
        }
      });
    }

    function populateDropdowns() {
      // Populate Categories
      var categoryOptions = '<option value="">-- Pilih Kategori --</option>';
      dropdownData.categories.forEach(function(category) {
        categoryOptions += `<option value="${category.id_kategori}">${escapeHtml(category.nama_kategori)}</option>`;
      });
      $('#id_kategori').html(categoryOptions);

      // Populate Thematics
      var thematicOptions = '<option value="">-- Pilih Thematic --</option>';
      dropdownData.thematics.forEach(function(thematic) {
        thematicOptions += `<option value="${thematic.id_thematik}">${escapeHtml(thematic.thematik_nama)}</option>`;
      });
      $('#id_thematik').html(thematicOptions);
    }

    // ===== LOAD DATA =====
    function loadData() {
      $.ajax({
        url: base_url + 'menu/tampil',
        type: 'GET',
        dataType: 'json',
        success: function(result) {
          renderDataTable(result);
        },
        error: function(xhr) {
          console.error('Error loading data:', xhr.responseText);
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

      if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().destroy();
      }

      $('#show_data_menu').html(html);
      initDataTable();
    }

    function buildTableRow(item, no) {
      var imageHtml = item.menu_gambar ?
        `<img src="${base_url}file/products/menu/${item.menu_gambar}" class="img-fluid img-thumbnail" style="width:50px;height:50px;object-fit:cover;cursor:pointer;" alt="Menu Image" onclick="previewImageMenu('${item.menu_gambar}', '${escapeHtml(item.menu_nama)}')">` :
        '<div class="no-image" style="width: 50px; height: 50px; background: #f8f9fa; border: 1px dashed #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 4px; font-size: 10px; color: #6c757d;">No Image</div>';

      var kategori = item.kategori_nama ? escapeHtml(item.kategori_nama) : '<em class="text-muted">-</em>';
      var thematik = item.thematik_nama ? escapeHtml(item.thematik_nama) : '<em class="text-muted">-</em>';
      var deskripsi = item.menu_deskripsi ?
        escapeHtml(item.menu_deskripsi).substring(0, 100) + '...' :
        '<em class="text-muted">-</em>';

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
          <td class="text-center">${statusHtml}</td>
          <td class="text-center">
            <button class="btn btn-warning btn-sm" onclick="editData(${item.id_menu})">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-danger btn-sm" onclick="deleteData(${item.id_menu})">
              <i class="fas fa-trash"></i> Hapus
            </button>
          </td>
        </tr>
      `;
    }

    function buildEmptyRow() {
      return `
        <tr>
          <td colspan="8" class="text-center">
            <div class="text-muted py-5">
              <i class="fas fa-utensils fa-3x mb-3"></i>
              <h5>Tidak ada data menu</h5>
            </div>
          </td>
        </tr>
      `;
    }

    function initDataTable() {
      if ($.fn.DataTable) {
        $('#datatable').DataTable({
          responsive: true,
          autoWidth: false,
          order: [
            [2, 'asc']
          ],
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

    // ===== TAMBAH DATA =====
    window.tambah_data = function() {
      $('#modal-form').modal('show');
      $('#modalLabel').html('<i class="ri-add-circle-line me-2"></i>Tambah Menu');
      $('#form-menu')[0].reset();
      $('#id_menu').val('');
      $('#status_aktif').val('1');
      $('#image-preview').hide();
      $('#preview-img').attr('src', '');
      loadDropdownData();
    };

    // ===== EDIT DATA =====
    window.editData = function(id) {
      $.ajax({
        url: base_url + 'menu/edit_data',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            var data = res.data;

            $('#id_komponen').val(data.id_komponen);
            $('#menu_nama').val(data.menu_nama);
            $('#menu_deskripsi').val(data.menu_deskripsi);
            $('#menu_harga').val(data.harga_menu);
            $('#status_aktif').val(data.status_aktif);
            $('#stat').val('edit');

            loadDropdownData();
            setTimeout(function() {
              $('#id_kategori').val(data.id_kategori_menu);
              $('#id_thematik').val(data.id_thematik);
            }, 500);

            if (data.menu_gambar) {
              $('#image-preview').show();
              $('#preview-img').attr('src', base_url + 'file/products/menu/' + data.menu_gambar);
            } else {
              $('#image-preview').hide();
            }

            $('#modal-form').modal('show');
            $('#modalLabel').html('<i class="ri-edit-line me-2"></i>Edit Menu');
          } else {
            showError('Data menu tidak ditemukan!');
          }
        },
        error: function(xhr) {
          console.error('Error:', xhr.responseText);
          showError('Gagal mengambil data menu!');
        }
      });
    };

    // ===== DELETE DATA =====
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

    function ajaxDeleteMenu(id) {
      $.ajax({
        url: base_url + 'menu/delete_data',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            showSuccess(res.message);
            loadData();
          } else {
            showError(res.message);
          }
        },
        error: function() {
          showError('Gagal menghapus data!');
        }
      });
    }

    // ===== FORM SUBMIT =====
    $('#form-menu').on('submit', function(e) {
      e.preventDefault();
      var formData = new FormData(this);

      $.ajax({
        url: base_url + 'menu/save_data',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function() {
          $('#btn-simpan').prop('disabled', true)
            .html('<i class="spinner-border spinner-border-sm me-1"></i>Menyimpan...');
        },
        success: function(res) {
          if (res.status === 'success') {
            $('#modal-form').modal('hide');
            showSuccess(res.message);
            loadData();
          } else {
            showError(res.message);
          }
        },
        complete: function() {
          $('#btn-simpan').prop('disabled', false)
            .html('<i class="ri-save-line me-1"></i>Simpan');
        },
        error: function(xhr) {
          console.error('Error:', xhr.responseText);
          showError('Terjadi kesalahan saat menyimpan data!');
          $('#btn-simpan').prop('disabled', false)
            .html('<i class="ri-save-line me-1"></i>Simpan');
        }
      });
    });

    // ===== IMAGE PREVIEW =====
    $('#menu_gambar').on('change', function() {
      let file = this.files[0];
      if (file) {
        let reader = new FileReader();
        reader.onload = function(e) {
          $('#preview-img').attr('src', e.target.result);
          $('#image-preview').show();
        }
        reader.readAsDataURL(file);
      }
    });

    window.removeImagePreview = function() {
      $('#menu_gambar').val('');
      $('#preview-img').attr('src', '');
      $('#image-preview').hide();
    };

    window.previewImageMenu = function(imageName, menuName) {
      var imageUrl = base_url + 'file/products/menu/' + imageName;
      $('#staticBackdropLabel').text('Preview Image: ' + menuName);
      $('#xpreview_image').html('<img src="' + imageUrl + '" class="img-fluid" style="max-width:100%;">');
      $('#form-modal-preview-image').modal('show');
    };

    // ===== MODAL EVENTS =====
    $(document).on('hidden.bs.modal', '#modal-form', function() {
      $('#form-menu')[0].reset();
      $('#image-preview').hide();
    });

    $(document).on('hide.bs.modal', '.modal', function() {
      $(this).find(':focus').blur();
    });

    // ===== HELPER FUNCTIONS =====
    function escapeHtml(text) {
      var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      };
      return text ? String(text).replace(/[&<>"']/g, function(m) {
        return map[m];
      }) : '';
    }

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

  })();
</script>