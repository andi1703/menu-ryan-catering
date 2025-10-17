<script type="text/javascript">
  (function() {
    'use strict';

    if (typeof jQuery === 'undefined' || window.kantinInitialized) return;
    window.kantinInitialized = true;

    var base_url = '<?= base_url() ?>';

    $(document).ready(function() {
      initButtonHandlers();
      initFormHandlers();
      loadKantinData();
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

    // Pindahkan ke global agar bisa dipanggil dari HTML
    window.tambah_kantin = function() {
      resetForm();
      $('.modal-title').text('Tambah Kantin');
      $('#form-modal-kantin').modal('show');
      setTimeout(function() {
        $('#nama_kantin').focus();
      }, 500);
    };

    function initFormHandlers() {
      $('#formKantin').on('submit', function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
          url: base_url + 'Back_Kantin/save_data',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              $('#form-modal-kantin').modal('hide');
              showSuccess(res.message);
              loadKantinData();
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

    function loadKantinData() {
      $.ajax({
        url: base_url + 'Back_Kantin/get_data_kantin?_=' + new Date().getTime(),
        type: 'GET',
        dataType: 'json',
        success: renderKantinTable,
        error: function() {
          showError('Gagal memuat data kantin!');
        }
      });
    }

    function renderKantinTable(result) {
      var html = '';
      var no = 1;
      if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().clear().destroy();
      }
      if (result.show_data && result.show_data.length > 0) {
        result.show_data.forEach(function(item) {
          html += buildKantinRow(item, no++);
        });
        $('#show_data_kantin').html(html);
        setTimeout(function() {
          initDataTable();
        }, 100);
      } else {
        html = buildEmptyRow();
        $('#show_data_kantin').html(html);
        // JANGAN panggil initDataTable jika data kosong!
      }
    }

    // PERBAIKI DI SINI: pastikan jumlah <td> = jumlah <th> (misal 6)
    function buildKantinRow(item, no) {
      return `
    <tr>
      <td class="text-center">${no}</td>
      <td>${escapeHtml(item.nama_kantin)}</td>
      <td>${escapeHtml(item.customer_nama)}</td>
      <td>${escapeHtml(item.alamat)}</td> <!-- GANTI INI -->
      <td class="text-center">
        <button class="btn btn-warning btn-sm btn-edit" data-id="${item.id_kantin}" type="button">
          <i class="fas fa-edit"></i> Edit
        </button>
        <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id_kantin}" type="button">
          <i class="fas fa-trash"></i> Hapus
        </button>
      </td>
    </tr>
  `;
    }

    function buildEmptyRow() {
      return `
    <tr>
      <td colspan="6" class="text-center py-5">
        <div class="text-muted">
          <i class="fas fa-store fa-3x mb-3"></i>
          <h5>Tidak ada data</h5>
          <p>Belum ada kantin yang ditambahkan</p>
          <button type="button" class="btn btn-primary btn-sm" onclick="tambah_kantin()">
            <i class="ri-add-circle-line me-1"></i>Tambah Kantin Pertama
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
        url: base_url + 'Back_Kantin/get_kantin_by_id',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#formKantin')[0].reset();
            $('#formKantin input[name="stat"]').val('edit');
            $('#formKantin input[name="id"]').val(res.data.id_kantin);
            $('#nama_kantin').val(res.data.nama_kantin);
            $('#id_customer').val(res.data.id_customer);
            $('#alamat_lokasi').val(res.data.alamat);
            $('.modal-title').text('Edit Kantin: ' + res.data.nama_kantin);
            $('#form-modal-kantin').modal('show');
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
          title: 'Hapus Kantin?',
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
        if (confirm('Hapus kantin?\nData yang dihapus tidak dapat dikembalikan!')) {
          deleteData(id, button);
        }
      }
    }

    function deleteData(id, button) {
      var originalHtml = button.html();
      button.prop('disabled', true).html('<i class="ri-loader-2-line spin"></i>');
      $.ajax({
        url: base_url + 'Back_Kantin/delete_data',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success') {
            showSuccess(response.message || 'Data berhasil dihapus');
            setTimeout(loadKantinData, 500);
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
      $('#formKantin')[0].reset();
      $('#formKantin input[name="stat"]').val('add');
      $('#formKantin input[name="id"]').val('');
      $('#formKantin').data('submitting', false);
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