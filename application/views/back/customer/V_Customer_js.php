<script type="text/javascript">
  (function() {
    'use strict';

    if (typeof jQuery === 'undefined' || window.customerInitialized) return;
    window.customerInitialized = true;

    var base_url = '<?= base_url() ?>';

    $(document).ready(function() {
      initButtonHandlers();
      initFormHandlers();
      loadCustomerData();
    });

    function initButtonHandlers() {
      $(document).on('click', '.btn-edit', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (id) editData(id);
      });

      $(document).on('click', '.btn-delete', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        if (id) confirmDelete(id, $(this));
      });

      window.tambah_data = function() {
        resetForm();
        $('.modal-title').text('Tambah Customer');
        $('#form-modal-customer-form').modal('show');
        setTimeout(function() {
          $('#nama_customer').focus();
        }, 500);
      };
    }

    function initFormHandlers() {
      $('#formCustomer').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
          url: base_url + 'Back_Customer/save_data',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              $('#form-modal-customer-form').modal('hide');
              showSuccess(res.message);
              loadCustomerData();
            } else {
              showError(res.message);
            }
          },
          error: function() {
            showError('Terjadi kesalahan saat menyimpan data!');
          }
        });
      });
    }

    function loadCustomerData() {
      $.ajax({
        url: base_url + 'Back_Customer/get_data_customer?_=' + new Date().getTime(),
        type: 'GET',
        dataType: 'json',
        success: renderCustomerTable,
        error: function() {
          showError('Gagal memuat data customer!');
        }
      });
    }

    function renderCustomerTable(result) {
      var html = '';
      var no = 1;
      if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().clear().destroy();
      }
      if (result.show_data && result.show_data.length > 0) {
        result.show_data.forEach(function(item) {
          html += buildCustomerRow(item, no++);
        });
      } else {
        html = buildEmptyRow();
      }
      $('#show_data_customer').html(html);
      setTimeout(function() {
        initDataTable();
      }, 100);
    }

    function buildCustomerRow(item, no) {
      var imageHtml = item.customer_img ?
        `<img src="${base_url}file/customer/${item.customer_img}" class="img-fluid img-thumbnail" style="width:50px;height:50px;object-fit:cover;border-radius:8px;" alt="Customer Image">` :
        '<div class="no-image" style="width: 50px; height: 50px; background: #f8f9fa; border: 1px dashed #dee2e6; display: flex; align-items: center; justify-content: center; border-radius: 4px; font-size: 10px; color: #6c757d;">No Image</div>';

      var hargaMakan = item.harga_makan ? 'Rp ' + parseInt(item.harga_makan).toLocaleString('id-ID') : '-';
      var foodCostMax = item.food_cost_max ? 'Rp ' + parseInt(item.food_cost_max).toLocaleString('id-ID') : '-';

      return `
    <tr>
      <td class="text-center">${no}</td>
      <td class="text-center">${imageHtml}</td>
      <td>${escapeHtml(item.nama_customer)}</td>
      <td>${escapeHtml(item.no_hp)}</td>
      <td>${escapeHtml(item.email)}</td>
      <td>${escapeHtml(item.alamat)}</td>
      <td class="text-center">${hargaMakan}</td>
      <td class="text-center">${foodCostMax}</td>
      <td class="text-center">
        <div class="btn-group btn-group-sm" role="group">
          <button class="btn btn-warning btn-edit" data-id="${item.id_customer}" type="button" title="Edit">
            <i class="fas fa-edit text-white"></i>
          </button>
          <button class="btn btn-danger btn-delete" data-id="${item.id_customer}" type="button" title="Hapus">
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
        <td colspan="9" class="text-center py-5">
          <div class="text-muted">
            <i class="fas fa-user fa-3x mb-3"></i>
            <h5>Tidak ada data</h5>
            <p>Belum ada customer yang ditambahkan</p>
            <button type="button" class="btn btn-primary btn-sm" onclick="tambah_data()">
              <i class="ri-add-circle-line me-1"></i>Tambah Customer Pertama
            </button>
          </div>
        </td>
      </tr>
    `;
    }

    function initDataTable() {
      if ($.fn.DataTable) {
        $('#datatable').DataTable({
          responsive: false,
          autoWidth: false,
          scrollX: true,
          scrollCollapse: true,
          order: [
            [2, 'asc']
          ], // sort by Nama Customer
          columnDefs: [{
              targets: [0, 1, 8],
              orderable: false,
              searchable: false
            },
            {
              targets: 0,
              width: '60px',
              className: 'text-center'
            },
            {
              targets: 1,
              width: '80px',
              className: 'text-center'
            },
            {
              targets: 2,
              width: '220px'
            },
            {
              targets: 3,
              width: '140px'
            },
            {
              targets: 4,
              width: '220px'
            },
            {
              targets: 5,
              width: 'auto'
            },
            {
              targets: 6,
              width: '140px',
              className: 'text-center'
            },
            {
              targets: 7,
              width: '160px',
              className: 'text-center'
            },
            {
              targets: 8,
              width: '160px',
              className: 'text-center'
            }
          ],
          language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data per halaman',
            zeroRecords: 'Tidak ada data yang ditemukan',
            info: 'Menampilkan halaman _PAGE_ dari _PAGES_',
            infoEmpty: 'Tidak ada data tersedia',
            infoFiltered: '(difilter dari _MAX_ total data)',
            paginate: {
              first: 'Pertama',
              last: 'Terakhir',
              next: 'Selanjutnya',
              previous: 'Sebelumnya'
            }
          },
          createdRow: function(row) {
            $(row).find('td').css({
              'white-space': 'normal',
              'word-wrap': 'break-word',
              'word-break': 'break-word'
            });
          },
          drawCallback: function() {
            this.api().column(0, {
              search: 'applied',
              order: 'applied'
            }).nodes().each(function(cell, i) {
              cell.innerHTML = i + 1;
            });
          },
          initComplete: function() {
            this.api().columns.adjust();
          }
        });
      }
    }

    function editData(id) {
      $.ajax({
        url: base_url + 'Back_Customer/get_customer_by_id',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#formCustomer')[0].reset();
            $('#formCustomer input[name="stat"]').val('edit');
            $('#formCustomer input[name="id"]').val(res.data.id_customer);
            $('#nama_customer').val(res.data.nama_customer);
            $('#no_hp').val(res.data.no_hp);
            $('#email').val(res.data.email);
            $('#alamat').val(res.data.alamat);
            $('#status_aktif').val(res.data.status_aktif); // <-- pastikan ini ada!
            $('.modal-title').text('Edit Customer: ' + res.data.nama_customer);
            $('#form-modal-customer-form').modal('show');
            if (res.data.customer_img) {
              $('#image-preview').show();
              $('#preview-img').attr('src', base_url + 'file/customer/' + res.data.customer_img);
            } else {
              $('#image-preview').hide();
              $('#preview-img').attr('src', '');
            }
          } else {
            showError('Gagal memuat data edit');
          }
        },
        error: function() {
          showError('Gagal memuat data edit');
        }
      });
    }

    function confirmDelete(id, button) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Hapus Customer?',
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
            deleteData(id, button);
          }
        });
      } else {
        if (confirm('Hapus customer?\nData yang dihapus tidak dapat dikembalikan!')) {
          deleteData(id, button);
        }
      }
    }

    function deleteData(id, button) {
      var originalHtml = button.html();
      button.prop('disabled', true).html('<i class="ri-loader-2-line spin"></i>');
      $.ajax({
        url: base_url + 'Back_Customer/delete_data',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            showSuccess(response.message || 'Data berhasil dihapus');
            setTimeout(loadCustomerData, 500);
          } else {
            showError(response.message || 'Gagal menghapus data');
            button.prop('disabled', false).html(originalHtml);
          }
        },
        error: function() {
          showError('Terjadi kesalahan pada server');
          button.prop('disabled', false).html(originalHtml);
        }
      });
    }

    function resetForm() {
      $('#formCustomer')[0].reset();
      $('#formCustomer input[name="stat"]').val('add');
      $('#formCustomer input[name="id"]').val('');
      $('#formCustomer').data('submitting', false);
      $('#image-preview').hide();
      $('#preview-img').attr('src', '');
      $('.form-control').removeClass('is-invalid');
      $('.invalid-feedback').text('');
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

  })();
</script>