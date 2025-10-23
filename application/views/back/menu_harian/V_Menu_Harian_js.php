<script type="text/javascript">
  (function() {
    'use strict';

    if (typeof jQuery === 'undefined' || window.menuHarianInitialized) return;
    window.menuHarianInitialized = true;

    var base_url = $('#base_url').val();
    var kondimenList = [];
    var menuList = [];
    var menuHarianTable = null;

    $(document).ready(function() {
      loadMenuHarianData();
      loadDropdownData();

      // Tambah menu harian
      window.tambah_menu_harian = function() {
        kondimenList = [];
        renderKondimenTable();
        $('#form-menu-harian')[0].reset();
        $('#form-modal-menu-harian').modal('show');
        $('#modalMenuHarianLabel').text('Tambah Menu Harian');
      };

      // Reload data otomatis setelah modal tertutup
      $(document).on('hidden.bs.modal', '#form-modal-menu-harian', function() {
        loadMenuHarianData();
      });

      // Tambah kondimen
      $('#btn-tambah-kondimen').on('click', function() {
        if (menuList.length === 0) {
          loadMenuList(function() {
            tambahKondimenRow();
          });
        } else {
          tambahKondimenRow();
        }
      });

      // Submit form via AJAX
      $('#form-menu-harian').on('submit', function(e) {
        e.preventDefault();
        if (kondimenList.length === 0) {
          showError('Menu kondimen wajib diisi!');
          return;
        }
        var formData = new FormData(this);
        formData.append('kondimen', JSON.stringify(kondimenList));
        $.ajax({
          url: base_url + 'menu-harian/save',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              $('#form-modal-menu-harian').modal('hide');
              showSuccess('Menu harian berhasil disimpan!');
            } else {
              showError(res.msg || 'Gagal menyimpan data!');
            }
          },
          error: function() {
            showError('Terjadi kesalahan saat menyimpan data!');
          }
        });
      });

      // Edit menu harian
      $(document).on('click', '.btn-edit', function() {
        var id = $(this).data('id');
        window.edit_menu_harian(id);
      });

      window.edit_menu_harian = function(id) {
        $.ajax({
          url: base_url + 'menu-harian/get_by_id/' + id,
          type: 'GET',
          dataType: 'json',
          success: function(data) {
            $('#id_menu_harian').val(data.id_menu_harian);
            $('#tanggal').val(data.tanggal);
            $('#shift').val(data.shift);
            $('#jenis_menu').val(data.jenis_menu);
            $('#id_customer').val(data.id_customer);
            $('#id_kantin').val(data.id_kantin);
            $('#nama_menu').val(data.nama_menu);
            $('#total_menu_perkantin').val(data.total_menu_perkantin);
            kondimenList = data.kondimen || [];
            renderKondimenTable();
            $('#form-modal-menu-harian').modal('show');
            $('#modalMenuHarianLabel').text('Edit Menu Harian');
          },
          error: function() {
            showError('Gagal mengambil data menu harian!');
          }
        });
      };

      // Delete menu harian
      $(document).on('click', '.btn-delete', function() {
        var id = $(this).data('id');
        window.delete_menu_harian(id);
      });

      window.delete_menu_harian = function(id) {
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Hapus Menu Harian?',
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
          url: base_url + 'menu-harian/delete/' + id,
          type: 'POST',
          dataType: 'json',
          success: function(res) {
            showSuccess(res.message || 'Menu harian dihapus!');
            if (res.status === 'success') {
              loadMenuHarianData();
            }
          },
          error: function() {
            showError('Gagal menghapus data!');
          }
        });
      }

      // Dropdown data
      function loadDropdownData() {
        loadCustomerOptions();
        loadKantinOptions();
        loadMenuList();
      }

      function loadCustomerOptions() {
        $.get(base_url + 'menu-harian/get_customers', function(data) {
          var options = '<option value="">Pilih Customer</option>';
          $.each(data, function(_, customer) {
            options += `<option value="${customer.id_customer}">${customer.nama_customer}</option>`;
          });
          $('#id_customer').html(options);
        }, 'json');
      }

      function loadKantinOptions() {
        $.get(base_url + 'menu-harian/get_kantins', function(data) {
          var options = '<option value="">Pilih Kantin</option>';
          $.each(data, function(_, kantin) {
            options += `<option value="${kantin.id_kantin}">${kantin.nama_kantin}</option>`;
          });
          $('#id_kantin').html(options);
        }, 'json');
      }

      function loadMenuList(callback) {
        $.get(base_url + 'menu-harian/get_menu_list', function(data) {
          menuList = data;
          if (typeof callback === 'function') callback();
        }, 'json');
      }

      // Show data
      function loadMenuHarianData() {
        $.ajax({
          url: base_url + 'menu-harian',
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
        if (result.show_data && result.show_data.length > 0) {
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
        var jenisMenu = item.jenis_menu ? item.jenis_menu : '-';
        var shift = item.shift ? item.shift : '-';
        var customer = item.nama_customer ? item.nama_customer : '-';
        var kantin = item.nama_kantin ? item.nama_kantin : '-';
        var namaMenu = item.nama_menu ? item.nama_menu : '-';
        var totalMenu = item.total_menu_perkantin ? item.total_menu_perkantin : '-';
        return `
        <tr>
          <td class="text-center">${no}</td>
          <td>${item.tanggal}</td>
          <td>${shift}</td>
          <td>${customer}</td>
          <td>${kantin}</td>
          <td>${jenisMenu}</td>
          <td>${namaMenu}</td>
          <td class="text-end">${totalMenu}</td>
          <td class="text-center">
            <button class="btn btn-warning btn-sm btn-edit" data-id="${item.id_menu_harian}" type="button">
              <i class="fas fa-edit"></i> Edit
            </button>
            <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id_menu_harian}" type="button">
              <i class="fas fa-trash"></i> Hapus
            </button>
          </td>
        </tr>
      `;
      }

      function buildEmptyRow() {
        return `
        <tr>
          <td class="text-center" colspan="9">
            <div class="text-muted">
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

      // Kondimen logic
      function getMenuOptions(selectedId = '') {
        var options = '<option value="">-- Pilih Kondimen --</option>';
        menuList.forEach(function(menu) {
          var selected = menu.id_komponen == selectedId ? 'selected' : '';
          options += `<option value="${menu.id_komponen}" ${selected}>${menu.menu_nama}</option>`;
        });
        return options;
      }

      function tambahKondimenRow() {
        kondimenList.push({
          id_komponen: '',
          qty: ''
        });
        renderKondimenTable();
      }

      function renderKondimenTable() {
        var tbody = '';
        kondimenList.forEach(function(kondimen, idx) {
          tbody += `<tr>
          <td class="text-center">${idx + 1}</td>
          <td>
            <select class="form-control kondimen-nama" data-idx="${idx}">
              ${getMenuOptions(kondimen.id_komponen)}
            </select>
          </td>
          <td>
            <input type="number" class="form-control kondimen-qty" data-idx="${idx}" value="${kondimen.qty}" required>
          </td>
          <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="hapusKondimenRow(${idx})"><i class="fas fa-trash"></i></button>
          </td>
        </tr>`;
        });
        $('#table-kondimen-menu tbody').html(tbody);

        $('.kondimen-nama').on('change', function() {
          var idx = $(this).data('idx');
          kondimenList[idx].id_komponen = $(this).val();
        });
        $('.kondimen-qty').on('input', function() {
          var idx = $(this).data('idx');
          kondimenList[idx].qty = $(this).val();
        });
      }

      window.hapusKondimenRow = function(idx) {
        kondimenList.splice(idx, 1);
        renderKondimenTable();
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
    });
  })();
</script>