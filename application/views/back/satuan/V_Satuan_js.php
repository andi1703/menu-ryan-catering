<script type="text/javascript">
  (function() {
    'use strict';

    if (typeof jQuery === 'undefined' || window.satuanInitialized) return;
    window.satuanInitialized = true;

    var base_url = '<?= base_url() ?>';

    $(document).ready(function() {
      initButtonHandlers();
      initFormHandlers();
      loadSatuanData();
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
    }

    window.tambah_satuan = function() {
      resetForm();
      $('.modal-title').text('Tambah Satuan');
      $('#form-modal-satuan').modal('show');
      setTimeout(function() {
        $('#nama_satuan').focus();
      }, 500);
    };

    function initFormHandlers() {
      $('#formSatuan').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
          url: base_url + 'Back_Satuan/save_data',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              $('#form-modal-satuan').modal('hide');
              showSuccess(res.message);
              loadSatuanData();
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

    function loadSatuanData() {
      $.ajax({
        url: base_url + 'Back_Satuan/get_data_satuan?_=' + new Date().getTime(),
        type: 'GET',
        dataType: 'json',
        success: renderSatuanTable,
        error: function() {
          showError('Gagal memuat data satuan!');
        }
      });
    }

    function renderSatuanTable(result) {
      var html = '';
      var no = 1;
      if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().clear().destroy();
      }
      if (result.show_data && result.show_data.length > 0) {
        result.show_data.forEach(function(item) {
          html += buildSatuanRow(item, no++);
        });
        $('#show_data_satuan').html(html);
        setTimeout(function() {
          initDataTable();
        }, 100);
      } else {
        html = buildEmptyRow();
        $('#show_data_satuan').html(html);
      }
    }

    function buildSatuanRow(item, no) {
      return `
    <tr>
      <td class="text-center">${no}</td>
      <td>${escapeHtml(item.nama_satuan)}</td>
      <td class="text-center">
        <button class="btn btn-warning btn-sm btn-edit" data-id="${item.id_satuan}" type="button">
          <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id_satuan}" type="button">
          <i class="fas fa-trash"></i> Hapus
        </button>
      </td>
    </tr>
  `;
    }

    function buildEmptyRow() {
      return `
    <tr>
      <td colspan="3" class="text-center py-5">
        <div class="text-muted">
          <i class="fas fa-balance-scale fa-3x mb-3"></i>
          <h5>Tidak ada data</h5>
          <p>Belum ada satuan yang ditambahkan</p>
          <button type="button" class="btn btn-primary btn-sm" onclick="tambah_satuan()">
            <i class="ri-add-circle-line me-1"></i>Tambah Satuan Pertama
          </button>
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

    function editData(id) {
      $.ajax({
        url: base_url + 'Back_Satuan/get_satuan_by_id',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#formSatuan')[0].reset();
            $('#formSatuan input[name="stat"]').val('edit');
            $('#formSatuan input[name="id"]').val(res.data.id_satuan);
            $('#nama_satuan').val(res.data.nama_satuan);
            $('.modal-title').text('Edit Satuan: ' + res.data.nama_satuan);
            $('#form-modal-satuan').modal('show');
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
          title: 'Hapus Satuan?',
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
        if (confirm('Hapus satuan?\nData yang dihapus tidak dapat dikembalikan!')) {
          deleteData(id, button);
        }
      }
    }

    function deleteData(id, button) {
      var originalHtml = button.html();
      button.prop('disabled', true).html('<i class="ri-loader-2-line spin"></i>');
      $.ajax({
        url: base_url + 'Back_Satuan/delete_data',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            showSuccess(response.message || 'Data berhasil dihapus');
            setTimeout(loadSatuanData, 500);
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
      $('#formSatuan')[0].reset();
      $('#formSatuan input[name="stat"]').val('add');
      $('#formSatuan input[name="id"]').val('');
      $('#formSatuan').data('submitting', false);
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