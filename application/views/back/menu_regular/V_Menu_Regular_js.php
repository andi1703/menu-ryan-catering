<script type="text/javascript">
  $(document).ready(function() {
    var selectedComponents = [];
    var base_url = '<?= base_url() ?>';
    var isInitialized = false; // Flag untuk mencegah double initialization

    // Pagination variables untuk komponen
    var komponenCurrentPage = 1;
    var komponenItemsPerPage = 10;
    var allKomponenRows = []; // Akan diisi dengan semua row
    var filteredKomponenRows = []; // Untuk hasil search

    // Pagination variables untuk keranjang
    var keranjangCurrentPage = 1;
    var keranjangItemsPerPage = 5;

    // FUNGSI HELPER: Badge kategori
    function getBadgeClassByCategory(kategori) {
      var badgeClasses = {
        'Lauk Utama': 'badge-primary',
        'Nasi': 'badge-success',
        'Sayuran Berkuah': 'badge-info',
        'Pendamping Basah': 'badge-warning',
        'Pendamping Kering': 'badge-secondary',
        'Sambal': 'badge-danger',
        'Buah': 'badge-dark'
      };
      return badgeClasses[kategori] || 'badge-light';
    }

    function getCategoryBadge(kategori) {
      var badgeClass = getBadgeClassByCategory(kategori);
      return '<span class="badge ' + badgeClass + '">' + kategori + '</span>';
    }

    // Initialize komponen rows dengan flag check
    function initializeKomponenRows() {
      if (isInitialized) {
        console.log('⚠️ Already initialized, skipping...');
        return;
      }

      console.log('🚀 Initializing komponen rows...');
      allKomponenRows = $('#komponen-menu-body .komponen-row').toArray();
      filteredKomponenRows = allKomponenRows.slice();
      console.log('📊 Total komponen rows:', allKomponenRows.length);

      // Force render pagination
      renderKomponenTable();

      isInitialized = true; // Set flag
      console.log('✅ Komponen pagination initialized successfully');
    }

    // Fungsi untuk render pagination komponen
    function renderKomponenPagination(currentPage, totalItems) {
      console.log('🔄 Rendering komponen pagination - Page:', currentPage, 'Total:', totalItems);

      var totalPages = Math.ceil(totalItems / komponenItemsPerPage);
      var container = $('#komponen-pagination-container');
      var paginationList = $('#komponen-pagination-list');

      if (totalItems > komponenItemsPerPage) {
        container.show();

        // Update info
        var startItem = ((currentPage - 1) * komponenItemsPerPage) + 1;
        var endItem = Math.min(currentPage * komponenItemsPerPage, totalItems);
        $('#komponen-start').text(startItem);
        $('#komponen-end').text(endItem);
        $('#komponen-total').text(totalItems);

        // Generate pagination buttons
        var html = '';

        // Previous button
        if (currentPage > 1) {
          html += `<li class="page-item">
                    <a class="page-link komponen-page-btn" href="#" data-page="${currentPage - 1}">
                      <i class="mdi mdi-chevron-left"></i>
                    </a>
                  </li>`;
        } else {
          html += `<li class="page-item disabled">
                    <span class="page-link">
                      <i class="mdi mdi-chevron-left"></i>
                    </span>
                  </li>`;
        }

        // Page numbers
        var startPage = Math.max(1, currentPage - 1);
        var endPage = Math.min(totalPages, currentPage + 1);

        // Always show page 1
        if (startPage > 1) {
          html += `<li class="page-item">
                    <a class="page-link komponen-page-btn" href="#" data-page="1">1</a>
                  </li>`;
          if (startPage > 2) {
            html += `<li class="page-item disabled">
                      <span class="page-link">...</span>
                    </li>`;
          }
        }

        // Current range
        for (var i = startPage; i <= endPage; i++) {
          if (i === currentPage) {
            html += `<li class="page-item active">
                      <span class="page-link">${i}</span>
                    </li>`;
          } else {
            html += `<li class="page-item">
                      <a class="page-link komponen-page-btn" href="#" data-page="${i}">${i}</a>
                    </li>`;
          }
        }

        // Always show last page
        if (endPage < totalPages) {
          if (endPage < totalPages - 1) {
            html += `<li class="page-item disabled">
                      <span class="page-link">...</span>
                    </li>`;
          }
          html += `<li class="page-item">
                    <a class="page-link komponen-page-btn" href="#" data-page="${totalPages}">${totalPages}</a>
                  </li>`;
        }

        // Next button
        if (currentPage < totalPages) {
          html += `<li class="page-item">
                    <a class="page-link komponen-page-btn" href="#" data-page="${currentPage + 1}">
                      <i class="mdi mdi-chevron-right"></i>
                    </a>
                  </li>`;
        } else {
          html += `<li class="page-item disabled">
                    <span class="page-link">
                      <i class="mdi mdi-chevron-right"></i>
                    </span>
                  </li>`;
        }

        paginationList.html(html);
        console.log('✅ Komponen pagination rendered successfully');
      } else {
        // Jika <= 10 items, tetap tampilkan info tapi hide pagination buttons
        $('#komponen-start').text(1);
        $('#komponen-end').text(totalItems);
        $('#komponen-total').text(totalItems);
        paginationList.html('<li class="page-item disabled"><span class="page-link">Semua data ditampilkan</span></li>');
      }
    }

    // Fungsi untuk render table komponen dengan pagination
    function renderKomponenTable() {
      console.log('🔄 Rendering komponen table...');
      var totalItems = filteredKomponenRows.length;
      var startIndex = (komponenCurrentPage - 1) * komponenItemsPerPage;
      var endIndex = startIndex + komponenItemsPerPage;

      console.log('📋 Pagination Info - Current Page:', komponenCurrentPage, 'Start:', startIndex, 'End:', endIndex, 'Total:', totalItems);

      // Hide semua rows dulu
      $(allKomponenRows).addClass('hidden');

      // Show hanya rows untuk halaman ini
      for (var i = startIndex; i < endIndex && i < filteredKomponenRows.length; i++) {
        $(filteredKomponenRows[i]).removeClass('hidden');
      }

      // PAKSA render pagination
      renderKomponenPagination(komponenCurrentPage, totalItems);

      console.log('✅ Komponen table rendered - Showing', Math.min(komponenItemsPerPage, totalItems - startIndex), 'items');
    }

    // Event handler untuk pagination komponen
    $(document).on('click', '.komponen-page-btn', function(e) {
      e.preventDefault();
      var page = parseInt($(this).data('page'));
      console.log('🖱️ Komponen pagination clicked - Page:', page);
      if (page && page > 0) {
        komponenCurrentPage = page;
        renderKomponenTable();
      }
    });

    // Search functionality dengan pagination
    $('#search-komponen').on('keyup', function() {
      var value = $(this).val().toLowerCase();
      console.log('🔍 Search komponen:', value);

      if (value === '') {
        filteredKomponenRows = allKomponenRows.slice();
      } else {
        filteredKomponenRows = allKomponenRows.filter(function(row) {
          var text = $(row).text().toLowerCase();
          return text.indexOf(value) > -1;
        });
      }

      komponenCurrentPage = 1; // Reset ke halaman 1 saat search
      renderKomponenTable();
      console.log('🔍 Search result:', filteredKomponenRows.length, 'items');
    });

    // Fungsi untuk render pagination keranjang
    function renderKeranjangPagination(currentPage, totalItems) {
      var totalPages = Math.ceil(totalItems / keranjangItemsPerPage);
      var container = $('#keranjang-pagination-container');
      var paginationList = $('#keranjang-pagination-list');

      if (totalItems <= keranjangItemsPerPage) {
        container.hide();
        return;
      }

      container.show();

      // Update info
      var startItem = ((currentPage - 1) * keranjangItemsPerPage) + 1;
      var endItem = Math.min(currentPage * keranjangItemsPerPage, totalItems);
      $('#keranjang-start').text(startItem);
      $('#keranjang-end').text(endItem);
      $('#keranjang-total').text(totalItems);

      // Generate pagination buttons
      var html = '';

      // Previous button
      if (currentPage > 1) {
        html += `<li class="page-item">
                  <a class="page-link keranjang-page-btn" href="#" data-page="${currentPage - 1}">
                    <i class="mdi mdi-chevron-left"></i>
                  </a>
                </li>`;
      } else {
        html += `<li class="page-item disabled">
                  <span class="page-link">
                    <i class="mdi mdi-chevron-left"></i>
                  </span>
                </li>`;
      }

      // Page numbers
      var startPage = Math.max(1, currentPage - 2);
      var endPage = Math.min(totalPages, currentPage + 2);

      if (startPage > 1) {
        html += `<li class="page-item">
                  <a class="page-link keranjang-page-btn" href="#" data-page="1">1</a>
                </li>`;
        if (startPage > 2) {
          html += `<li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>`;
        }
      }

      for (var i = startPage; i <= endPage; i++) {
        if (i === currentPage) {
          html += `<li class="page-item active">
                    <span class="page-link">${i}</span>
                  </li>`;
        } else {
          html += `<li class="page-item">
                    <a class="page-link keranjang-page-btn" href="#" data-page="${i}">${i}</a>
                  </li>`;
        }
      }

      if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
          html += `<li class="page-item disabled">
                    <span class="page-link">...</span>
                  </li>`;
        }
        html += `<li class="page-item">
                  <a class="page-link keranjang-page-btn" href="#" data-page="${totalPages}">${totalPages}</a>
                </li>`;
      }

      // Next button
      if (currentPage < totalPages) {
        html += `<li class="page-item">
                  <a class="page-link keranjang-page-btn" href="#" data-page="${currentPage + 1}">
                    <i class="mdi mdi-chevron-right"></i>
                  </a>
                </li>`;
      } else {
        html += `<li class="page-item disabled">
                  <span class="page-link">
                    <i class="mdi mdi-chevron-right"></i>
                  </span>
                </li>`;
      }

      paginationList.html(html);
    }

    // Fungsi render keranjang dengan pagination
    function renderKeranjang() {
      var tbody = $('#keranjang-menu-body');
      var totalHarga = 0;

      if (selectedComponents.length === 0) {
        tbody.html(`
          <tr id="keranjang-empty">
            <td colspan="5" class="text-center text-muted py-4">
              <div>
                <i class="ri-shopping-cart-line fa-2x mb-2"></i>
                <p class="mb-0">Belum ada komponen menu dipilih</p>
              </div>
            </td>
          </tr>
        `);
        $('#keranjang-pagination-container').hide();
      } else {
        // Pagination logic
        var totalItems = selectedComponents.length;
        var startIndex = (keranjangCurrentPage - 1) * keranjangItemsPerPage;
        var endIndex = startIndex + keranjangItemsPerPage;
        var currentPageData = selectedComponents.slice(startIndex, endIndex);

        var html = '';
        currentPageData.forEach(function(item, index) {
          var actualNumber = startIndex + index + 1;
          html += `
            <tr class="highlight-new">
              <td class="text-center">${actualNumber}</td>
              <td>${item.nama}</td>
              <td>${getCategoryBadge(item.kategori)}</td>
              <td class="text-right">Rp ${item.harga.toLocaleString('id-ID')}</td>
              <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger btn-hapus-item" data-id="${item.id}" title="Hapus komponen">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </td>
            </tr>
          `;
        });

        // Hitung total harga dari semua komponen
        totalHarga = 0;
        selectedComponents.forEach(function(item) {
          totalHarga += item.harga;
        });

        tbody.html(html);

        // Render pagination
        renderKeranjangPagination(keranjangCurrentPage, totalItems);
      }

      $('#total-harga-display').text('Rp ' + totalHarga.toLocaleString('id-ID'));
      $('#harga').val(totalHarga);
      $('#jumlah-komponen').text(selectedComponents.length);
    }

    // Event handler untuk pagination keranjang
    $(document).on('click', '.keranjang-page-btn', function(e) {
      e.preventDefault();
      var page = parseInt($(this).data('page'));
      if (page && page > 0) {
        keranjangCurrentPage = page;
        renderKeranjang();
      }
    });

    // Refresh halaman setelah operasi CRUD berhasil
    function refreshPage() {
      var urlParams = new URLSearchParams(window.location.search);
      var currentPage = urlParams.get('page') || '1';
      window.location.href = window.location.pathname + '?page=' + currentPage;
    }

    // Event handler untuk checkbox komponen dengan debugging
    $(document).on('change', '.komponen-checkbox', function() {
      var id = $(this).val();
      var harga = parseInt($(this).data('harga')) || 0;
      var nama = $(this).closest('tr').find('td:eq(1)').text().trim();
      var kategori = $(this).closest('tr').find('td:eq(2)').text().trim();

      console.log('📋 Checkbox changed - ID:', id, 'Type:', typeof id, 'Checked:', $(this).is(':checked'));

      if ($(this).is(':checked')) {
        // Pastikan ID disimpan sebagai string
        var existingIndex = selectedComponents.findIndex(item => String(item.id) === String(id));
        if (existingIndex === -1) {
          selectedComponents.push({
            id: String(id), // Konversi ke string
            nama: nama,
            kategori: kategori,
            harga: harga
          });
          console.log('➕ Added component:', nama, 'ID:', String(id));
        } else {
          console.log('⚠️ Component already exists:', nama);
        }
      } else {
        // Filter dengan string comparison
        selectedComponents = selectedComponents.filter(item => String(item.id) !== String(id));
        keranjangCurrentPage = 1; // Reset ke halaman 1 saat item dihapus
        console.log('➖ Removed component:', nama, 'ID:', String(id));
      }

      // DEBUG: Log current selectedComponents
      console.log('📊 Current selectedComponents:', selectedComponents.map(item => ({
        id: item.id,
        nama: item.nama
      })));

      renderKeranjang();
    });

    // PERBAIKAN: Event handler untuk hapus item dari keranjang - TANPA KONFIRMASI
    $(document).on('click', '.btn-hapus-item', function(e) {
      e.preventDefault();
      e.stopPropagation();

      var id = $(this).data('id');
      console.log('🗑️ Delete button clicked - Component ID:', id, 'Type:', typeof id);

      // Konversi ID ke string untuk consistency
      var idString = String(id);

      // Pastikan ID valid
      if (!id) {
        console.error('❌ No ID found for delete button');
        return;
      }

      // Cari komponen dengan string comparison
      var componentToRemove = selectedComponents.find(item => String(item.id) === idString);
      if (!componentToRemove) {
        console.error('❌ Component not found in selectedComponents:', idString);
        console.error('Available IDs:', selectedComponents.map(item => String(item.id)));
        return;
      }

      console.log('🗑️ Removing component:', componentToRemove.nama, 'ID:', idString);

      // Uncheck checkbox dengan string comparison
      var checkbox = $('.komponen-checkbox[value="' + idString + '"]');
      if (checkbox.length > 0) {
        checkbox.prop('checked', false);
        console.log('✅ Checkbox unchecked for ID:', idString);
      } else {
        console.warn('⚠️ Checkbox not found for ID:', idString);
      }

      // Hapus dari array dengan string comparison
      selectedComponents = selectedComponents.filter(item => String(item.id) !== idString);
      console.log('📊 Remaining components:', selectedComponents.length);

      // Adjust current page if needed
      var totalPages = Math.ceil(selectedComponents.length / keranjangItemsPerPage);
      if (keranjangCurrentPage > totalPages && totalPages > 0) {
        keranjangCurrentPage = totalPages;
      } else if (selectedComponents.length === 0) {
        keranjangCurrentPage = 1;
      }

      // Re-render keranjang dengan animasi fade out
      $(this).closest('tr').addClass('table-danger').fadeOut(300, function() {
        renderKeranjang();
      });

      console.log('✅ Component successfully removed from cart without confirmation');
    });

    // Fungsi edit data
    function editData(id) {
      if (!id) return;
      resetForm();
      $.ajax({
        url: base_url + 'Back_Menu_Regular/get_regular_menu_by_id',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success' && response.data) {
            var data = response.data;
            $('#id').val(data.id);
            $('#nama_menu_reg').val(data.nama_menu_reg);
            $('#harga').val(data.harga);
            $('[name="stat"]').val('edit');
            selectedComponents = [];

            if (data.komponen && data.komponen.length > 0) {
              data.komponen.forEach(function(komponen) {
                var checkbox = $('.komponen-checkbox[value="' + komponen.id_komponen + '"]');
                if (checkbox.length > 0) {
                  checkbox.prop('checked', true);
                  var nama = checkbox.closest('tr').find('td:eq(1)').text().trim();
                  var kategori = checkbox.closest('tr').find('td:eq(2)').text().trim();
                  var harga = parseInt(checkbox.data('harga')) || 0;
                  selectedComponents.push({
                    id: String(komponen.id_komponen), // Konversi ke string
                    nama: nama,
                    kategori: kategori,
                    harga: harga
                  });
                }
              });
            }

            komponenCurrentPage = 1;
            keranjangCurrentPage = 1;
            renderKeranjang();
            $('.modal-title').text('Edit Menu Regular');
            $('#modal-regular-menu').modal('show');
          } else {
            showError(response.message || 'Gagal mengambil data menu');
          }
        },
        error: function(xhr, status, error) {
          showError('Terjadi kesalahan saat mengambil data: ' + error);
        }
      });
    }

    function deleteData(id) {
      if (!id) return;
      if (confirm('Apakah Anda yakin ingin menghapus menu ini?')) {
        $.ajax({
          url: base_url + 'Back_Menu_Regular/delete_data',
          type: 'POST',
          data: {
            id: id
          },
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              showSuccess(response.message);
              setTimeout(function() {
                refreshPage();
              }, 1500);
            } else {
              showError(response.message || 'Gagal menghapus data');
            }
          },
          error: function(xhr, status, error) {
            showError('Terjadi kesalahan saat menghapus data: ' + error);
          }
        });
      }
    }

    // Event handlers untuk tombol edit/delete (menu regular, bukan komponen)
    $(document).on('click', '.btn-edit', function() {
      editData($(this).data('id'));
    });

    $(document).on('click', '.btn-delete', function() {
      deleteData($(this).data('id'));
    });

    // Form submission
    $('#form-regular-menu').on('submit', function(e) {
      e.preventDefault();

      if (selectedComponents.length === 0) {
        showError('Pilih minimal 1 komponen menu');
        return false;
      }

      $('#selected-components-container').empty();
      selectedComponents.forEach(function(item) {
        $('#selected-components-container').append(
          '<input type="hidden" name="komponen_menu[]" value="' + item.id + '">'
        );
      });

      $.ajax({
        url: base_url + 'Back_Menu_Regular/save_data',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#modal-regular-menu').modal('hide');
            showSuccess(res.message);
            setTimeout(function() {
              refreshPage();
            }, 1500);
          } else {
            showError(res.message || 'Gagal menyimpan data');
          }
        },
        error: function(xhr, status, error) {
          showError('Terjadi kesalahan saat menyimpan data: ' + error);
        }
      });
    });

    // Global functions
    window.tambah_menu_regular = function() {
      resetForm();
      $('.modal-title').text('Tambah Menu Regular');
      $('#modal-regular-menu').modal('show');
    };

    function resetForm() {
      $('#form-regular-menu')[0].reset();
      $('[name="stat"]').val('add');
      $('[name="id"]').val('');
      selectedComponents = [];
      komponenCurrentPage = 1;
      keranjangCurrentPage = 1;
      filteredKomponenRows = allKomponenRows.slice();
      $('#search-komponen').val('');
      $('.komponen-checkbox').prop('checked', false);
      renderKomponenTable();
      renderKeranjang();
    }

    function showSuccess(message) {
      if (typeof Swal !== 'undefined') {
        Swal.fire('Sukses!', message, 'success');
      } else {
        alert('Sukses: ' + message);
      }
    }

    function showError(message) {
      if (typeof Swal !== 'undefined') {
        Swal.fire('Error!', message, 'error');
      } else {
        alert('Error: ' + message);
      }
    }

    // Initialize saat modal dibuka dengan flag check
    $('#modal-regular-menu').on('shown.bs.modal', function() {
      console.log('🔓 Modal opened - Checking if initialization needed...');

      // Delay untuk memastikan DOM sudah fully loaded
      setTimeout(function() {
        initializeKomponenRows(); // Akan di-skip jika sudah initialized
        renderKeranjang();
      }, 100);
    });

    console.log('✅ Menu Regular JavaScript with INSTANT delete (no confirmation) initialized successfully');
  });
</script>