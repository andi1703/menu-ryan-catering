<script type="text/javascript">
  (function() {
    'use strict';

    if (typeof jQuery === 'undefined' || window.menuInitialized) return;
    window.menuInitialized = true;

    var base_url = $('#base_url').val();
    var dropdownData = {
      categories: [],
      thematik: [],
      bahanUtama: []
    };

    // Badge color configuration by category
    var badgeConfig = {
      "nasi": {
        class: "badge-nasi",
        color: "#ffc107"
      },
      "lauk utama": {
        class: "badge-lauk-utama",
        color: "#dc3545"
      },
      "lauk_utama": {
        class: "badge-lauk-utama",
        color: "#dc3545"
      },
      "pendamping basah": {
        class: "badge-pendamping-basah",
        color: "#28a745"
      },
      "pendamping_basah": {
        class: "badge-pendamping-basah",
        color: "#28a745"
      },
      "pendamping kering": {
        class: "badge-pendamping-kering",
        color: "#17a2b8"
      },
      "pendamping_kering": {
        class: "badge-pendamping-kering",
        color: "#17a2b8"
      },
      "sayuran berkuah": {
        class: "badge-sayuran-berkuah",
        color: "#20c997"
      },
      "sayuran_berkuah": {
        class: "badge-sayuran-berkuah",
        color: "#20c997"
      },
      "buah": {
        class: "badge-buah",
        color: "#e83e8c"
      },
      "sambal": {
        class: "badge-sambal",
        color: "#fd7e14"
      },
      "tumisan": {
        class: "badge-tumisan",
        color: "#6f42c1"
      },
      "kerupuk": {
        class: "badge-pendamping-kering",
        color: "#17a2b8"
      },
      "sayur": {
        class: "badge-sayuran-berkuah",
        color: "#20c997"
      }
    };

    // Custom Multi-Select State
    var bahanUtamaState = {
      selected: [],
      isOpen: false,
      allOptions: []
    };

    $(document).ready(function() {
      loadData();
      loadDropdownData();
      initBahanUtamaMultiSelect();

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
            dropdownData.thematik = result.thematik || [];
            dropdownData.bahanUtama = result.bahan || [];
            bahanUtamaState.allOptions = result.bahan || [];
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

      var thematikOptions = '<option value="">-- Pilih Thematik --</option>';
      dropdownData.thematik.forEach(function(thematik) {
        thematikOptions += `<option value="${thematik.id_thematik}">${escapeHtml(thematik.thematik_nama)}</option>`;
      });
      $('#id_thematik').html(thematikOptions);

      renderBahanOptions();
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
      $('#datatable').show();

      // Inisialisasi ulang DataTable
      initDataTable();
    }

    function buildTableRow(item, no) {
      var imageHtml = item.menu_gambar ?
        `<img src="${base_url}file/products/menu/${item.menu_gambar}" class="img-fluid img-thumbnail" style="width:50px;height:50px;object-fit:cover;cursor:pointer;" alt="Menu Image" onclick="previewImageMenu('${item.menu_gambar}', '${escapeHtml(item.menu_nama)}')">` :
        '<div class="no-image">No Image</div>';

      var kategori = buildCategoryBadge(item.nama_kategori);

      var thematik = item.thematik_nama ?
        `<span>${escapeHtml(item.thematik_nama)}</span>` :
        '<span class="text-muted fst-italic">Tidak ada thematik</span>';

      var bahanUtama = '<span class="text-muted fst-italic">Tidak ada bahan utama</span>';
      if (Array.isArray(item.bahan_utama) && item.bahan_utama.length) {
        var bahanTextNumbered = item.bahan_utama.map(function(nama, idx) {
          return (idx + 1) + '. ' + escapeHtml(nama);
        }).join(', ');
        bahanUtama = bahanTextNumbered;
      } else if (item.nama_bahan_utama) {
        bahanUtama = '1. ' + escapeHtml(item.nama_bahan_utama);
      }

      // Siapkan konten deskripsi (hanya teks) dan tombol deskripsi untuk kolom Aksi
      var deskripsi = '<span class="text-muted fst-italic">Tidak ada deskripsi</span>';
      var deskripsiButtonHtml = '';
      var escapedName = escapeHtml(item.menu_nama).replace(/'/g, '\\&#039;').replace(/"/g, '&quot;');
      var imageParam = item.menu_gambar ? encodeURIComponent(item.menu_gambar) : '';
      var bahanUtamaParam = '';
      if (Array.isArray(item.bahan_utama) && item.bahan_utama.length > 0) {
        bahanUtamaParam = encodeURIComponent(JSON.stringify(item.bahan_utama));
      }

      if (item.menu_deskripsi && item.menu_deskripsi.trim() !== '') {
        var shortDesc = item.menu_deskripsi.length > 50 ? item.menu_deskripsi.substring(0, 50) + '...' : item.menu_deskripsi;
        var escapedDesc = escapeHtml(item.menu_deskripsi).replace(/'/g, '\\&#039;').replace(/"/g, '&quot;').replace(/\n/g, '\\n').replace(/\r/g, '\\r');

        deskripsi = `<small class="text-muted">${escapeHtml(shortDesc)}</small>`;
        deskripsiButtonHtml = `<button type="button" class="btn btn-sm btn-info btn-view-desc" onclick="viewDeskripsi('${item.id_komponen}', '${escapedName}', '${escapedDesc}', '${imageParam}', '${bahanUtamaParam}')" title="Lihat Deskripsi Lengkap"><i class="ri-eye-line"></i></button>`;
      }

      var statusClass = item.status_aktif == 1 ? 'badge bg-success' : 'badge bg-secondary';
      var statusText = item.status_aktif == 1 ? 'Aktif' : 'Tidak Aktif';
      var statusHtml = `<span class="${statusClass}">${statusText}</span>`;


      return `
        <tr>
          <td class="text-center fw-bold">${no}</td>
          <td class="text-center">${imageHtml}</td>
          <td class="fw-semibold">${escapeHtml(item.menu_nama)}</td>
          <td>${kategori}</td>
          <td>${thematik}</td>
          <td>${bahanUtama}</td>
          <td>${deskripsi}</td>
          <td class="text-center">${statusHtml}</td>
          <td class="text-center">
            <div class="btn-group btn-group-sm" role="group">
              ${deskripsiButtonHtml}
              <button class="btn btn-warning btn-edit" data-id="${item.id_komponen}" type="button" title="Edit">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-danger btn-delete" data-id="${item.id_komponen}" type="button" title="Hapus">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `;
    }


    function buildEmptyRow() {
      return `
        <tr>
          <td colspan="9" class="text-center text-muted py-3">
            <i class="fas fa-utensils fa-2x mb-2"></i>
            <div>Tidak ada data menu</div>
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

    // Build category badge HTML using the config above
    function buildCategoryBadge(namaKategori) {
      if (!namaKategori) {
        return '<span class="text-muted fst-italic">Tidak ada kategori</span>';
      }

      var key = String(namaKategori).trim().toLowerCase();
      var keyUnderscore = key.replace(/\s+/g, '_');
      var cfg = badgeConfig[key] || badgeConfig[keyUnderscore];

      if (cfg) {
        return `<span class="badge ${cfg.class}" style="background-color:${cfg.color}; color:#fff;">${escapeHtml(namaKategori)}</span>`;
      }
      // fallback to default style
      return `<span class="badge bg-info">${escapeHtml(namaKategori)}</span>`;
    }

    // ========== CUSTOM MULTI-SELECT FOR BAHAN UTAMA ==========
    function initBahanUtamaMultiSelect() {
      const wrapper = $('#bahanUtamaSelect');
      const selectBox = $('#bahanSelectBox');
      const dropdownMenu = $('#bahanDropdownMenu');
      const tagsContainer = $('#bahanTagsContainer');
      const optionsList = $('#bahanOptionsList');
      const searchInput = $('#bahanSearchInput');
      const placeholder = $('#bahanPlaceholder');
      const clearBtn = $('#bahanClearBtn');
      const divider = $('.control-divider');

      // Toggle Dropdown
      selectBox.on('click', function(e) {
        if ($(e.target).closest('.tag-close').length || $(e.target).closest('#bahanClearBtn').length) {
          return;
        }
        bahanUtamaState.isOpen = !bahanUtamaState.isOpen;
        updateDropdownState();
        if (bahanUtamaState.isOpen) {
          searchInput.focus();
        }
      });

      // Close when clicking outside
      $(document).on('click', function(e) {
        if (!wrapper[0].contains(e.target)) {
          bahanUtamaState.isOpen = false;
          updateDropdownState();
        }
      });

      // Search Logic
      searchInput.on('input', function() {
        renderBahanOptions($(this).val());
      });

      // Clear All
      clearBtn.on('click', function(e) {
        e.stopPropagation();
        bahanUtamaState.selected = [];
        renderBahanTags();
        renderBahanOptions();
        updateHiddenInput();
      });

      function updateDropdownState() {
        if (bahanUtamaState.isOpen) {
          dropdownMenu.addClass('show');
          selectBox.addClass('active');
        } else {
          dropdownMenu.removeClass('show');
          selectBox.removeClass('active');
          searchInput.val('');
          renderBahanOptions();
        }
      }
    }

    function renderBahanTags() {
      const tagsContainer = $('#bahanTagsContainer');
      const placeholder = $('#bahanPlaceholder');
      const clearBtn = $('#bahanClearBtn');
      const divider = $('.control-divider');

      tagsContainer.empty();

      if (bahanUtamaState.selected.length === 0) {
        tagsContainer.append(placeholder);
        placeholder.show();
        clearBtn.hide();
        divider.hide();
      } else {
        placeholder.hide();
        clearBtn.show();
        divider.show();

        bahanUtamaState.selected.forEach(function(id) {
          const item = bahanUtamaState.allOptions.find(b => b.id_bahan == id);
          if (!item) return;

          const tag = $('<div class=\"tag-badge\"></div>');
          const closeIcon = $('<i class=\"bi bi-x tag-close\"></i>').on('click', function(e) {
            e.stopPropagation();
            removeTag(id);
          });
          const span = $('<span></span>').text(item.nama_bahan);

          tag.append(closeIcon).append(span);
          tagsContainer.append(tag);
        });
      }
      updateHiddenInput();
    }

    function renderBahanOptions(filterText = '') {
      const optionsList = $('#bahanOptionsList');
      optionsList.empty();

      const filtered = bahanUtamaState.allOptions.filter(function(b) {
        return b.nama_bahan.toLowerCase().includes(filterText.toLowerCase());
      });

      if (filtered.length === 0) {
        optionsList.append('<li style=\"padding:10px; color:#666; text-align:center;\">Tidak ada hasil</li>');
        return;
      }

      filtered.forEach(function(item) {
        const isSelected = bahanUtamaState.selected.includes(item.id_bahan);
        const li = $('<li class=\"option-item\"></li>');

        if (isSelected) {
          li.addClass('selected');
        }

        const span = $('<span></span>').text(item.nama_bahan);
        li.append(span);

        if (isSelected) {
          li.append('<i class=\"bi bi-check-lg float-end\"></i>');
        }

        li.on('click', function() {
          toggleBahanSelection(item.id_bahan);
        });

        optionsList.append(li);
      });
    }

    function toggleBahanSelection(id) {
      const index = bahanUtamaState.selected.indexOf(id);
      if (index > -1) {
        bahanUtamaState.selected.splice(index, 1);
      } else {
        bahanUtamaState.selected.push(id);
      }
      renderBahanTags();
      renderBahanOptions($('#bahanSearchInput').val());
      $('#bahanSearchInput').focus();
    }

    function removeTag(id) {
      const index = bahanUtamaState.selected.indexOf(id);
      if (index > -1) {
        bahanUtamaState.selected.splice(index, 1);
      }
      renderBahanTags();
      renderBahanOptions();
    }

    function updateHiddenInput() {
      $('#id_bahan_utama').val(bahanUtamaState.selected.join(','));
    }

    function setBahanUtamaValues(values) {
      bahanUtamaState.selected = values || [];
      renderBahanTags();
      renderBahanOptions();
    }

    function clearBahanUtamaValues() {
      bahanUtamaState.selected = [];
      renderBahanTags();
      renderBahanOptions();
    }

    function updateBahanPlaceholder() {
      // No longer needed with custom implementation
    }


    // Submit form via AJAX (add/edit)
    $('#form-data').on('submit', function(e) {
      e.preventDefault();

      // Update hidden input before submit
      updateHiddenInput();

      var formData = new FormData(this);

      // Add selected bahan utama to FormData
      if (bahanUtamaState.selected.length > 0) {
        formData.delete('id_bahan_utama[]');
        bahanUtamaState.selected.forEach(function(id) {
          formData.append('id_bahan_utama[]', id);
        });
      }

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
      $('#id_kategori').val('');
      $('#id_thematik').val('');
      clearBahanUtamaValues();
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
            $('#id_kategori').val(res.data.id_kategori);
            $('#id_thematik').val(res.data.id_thematik);

            if (res.data.bahan_utama_ids && res.data.bahan_utama_ids.length) {
              setBahanUtamaValues(res.data.bahan_utama_ids);
            } else {
              clearBahanUtamaValues();
            }

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

    // Fungsi untuk view deskripsi dalam modal
    window.viewDeskripsi = function(idMenu, namaMenu, deskripsi, imageFile, bahanUtama) {
      // Decode HTML entities dan unescape newlines
      var tempDiv = document.createElement('div');
      tempDiv.innerHTML = namaMenu;
      var decodedName = tempDiv.textContent || tempDiv.innerText || '';

      tempDiv.innerHTML = deskripsi;
      var decodedDesc = tempDiv.textContent || tempDiv.innerText || '';
      decodedDesc = decodedDesc.replace(/\\n/g, '\n').replace(/\\r/g, '\r');

      $('#previewMenuName').text(decodedName);
      $('#previewDeskripsiContent').text(decodedDesc);

      // Handle Bahan Utama (tampilkan sebagai teks dipisah koma)
      var decodedBahanUtama = bahanUtama ? decodeURIComponent(bahanUtama) : '';
      if (decodedBahanUtama && decodedBahanUtama !== 'null' && decodedBahanUtama.trim() !== '') {
        try {
          var bahanArray = JSON.parse(decodedBahanUtama);
          if (Array.isArray(bahanArray) && bahanArray.length > 0) {
            var $container = $('#previewBahanUtama');
            var $grid = $('<div class="bahan-grid"></div>');
            for (var i = 0; i < bahanArray.length; i += 5) {
              var startNum = i + 1;
              var $list = $('<ol class="mb-0 ps-3 bahan-list" start="' + startNum + '"></ol>');
              bahanArray.slice(i, i + 5).forEach(function(bahan) {
                $('<li></li>').text(bahan).appendTo($list);
              });
              $grid.append($list);
            }
            $container.empty().append($grid);
            $('#bahanUtamaSection').show();
          } else {
            $('#bahanUtamaSection').hide();
          }
        } catch (e) {
          console.error('Error parsing bahan utama:', e);
          $('#bahanUtamaSection').hide();
        }
      } else {
        $('#bahanUtamaSection').hide();
      }

      var decodedImage = imageFile ? decodeURIComponent(imageFile) : '';
      var imagePath = decodedImage ? base_url + 'file/products/menu/' + decodedImage : '';
      var $imageElement = $('#previewMenuImage');
      var $placeholder = $('#previewMenuImagePlaceholder');

      if (decodedImage) {
        $imageElement.attr('src', imagePath);
        $imageElement.attr('alt', 'Gambar ' + decodedName);
        $imageElement.show();
        $placeholder.hide();
      } else {
        $imageElement.attr('src', '');
        $imageElement.hide();
        $placeholder.show();
      }

      $('#modalPreviewDeskripsi').modal('show');
    };


  })();
</script>