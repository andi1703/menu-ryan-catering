<script type="text/javascript">
  (function() {
      'use strict';

      if (typeof jQuery === 'undefined' || window.bahanInitialized) return;
      window.bahanInitialized = true;

      var base_url = '<?= base_url() ?>';

      $(document).ready(function() {
        initButtonHandlers();
        initFormHandlers();
        loadBahanData();
      });

      function initButtonHandlers()
    },
    error: function() {
      showError('Gagal memuat daftar bahan dari database!');
    }
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

  window.tambah_bahan = function() {
    resetForm();
    $('#modalBahanLabel').html('<i class="fas fa-carrot me-2"></i>Tambah Bahan Baku');
    $('#stat').val('add');
    $('#submitBtn').html('<i class="fas fa-save me-1"></i>Simpan Bahan');
    $('#form-modal-bahan').modal('show');
    setTimeout(function() {
      $('#nama_bahan').focus();
    }, 500);
  };

  window.edit_bahan = function(id) {
    if (!id) return;

    $.ajax({
      url: base_url + 'Back_Bahan/get_bahan_by_id',
      type: 'POST',
      data: {
        id: id
      },
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success' && res.data) {
          const data = res.data;
          $('#modalBahanLabel').html('<i class="fas fa-edit me-2"></i>Edit Bahan Baku');
          $('#stat').val('edit');
          $('#id').val(data.id_bahan);
          $('#nama_bahan').val(data.nama_bahan);
          $('#id_satuan').val(data.id_satuan);
          $('#harga_awal').val(data.harga_awal);
          $('#harga_sekarang').val(data.harga_sekarang);
          $('#keterangan').val(data.keterangan || '');
          $('#submitBtn').html('<i class="fas fa-save me-1"></i>Update Bahan');

          // Show price difference if any
          checkPriceDifference();

          $('#form-modal-bahan').modal('show');
          setTimeout(function() {
            $('#nama_bahan').focus();
          }, 500);
        } else {
          showError(res.message || 'Data bahan tidak ditemukan');
        }
      },
      error: function() {
        showError('Gagal memuat data bahan dari server');
      }
    });
  };

  window.hapus_bahan = function(id) {
    if (!id) return;

    Swal.fire({
      title: 'Konfirmasi Penghapusan',
      text: 'Apakah Anda yakin ingin menghapus bahan ini? Data yang sudah dihapus tidak dapat dikembalikan.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Hapus Sekarang!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: base_url + 'Back_Bahan/delete_data',
          type: 'POST',
          data: {
            id: id
          },
          dataType: 'json',
          success: function(res) {
            if (res.status === 'success') {
              showSuccess(res.message);
              loadBahanData();
            } else {
              showError(res.message);
            }
          },
          error: function() {
            showError('Gagal menghapus data bahan dari server');
          }
        });
      }
    });
  };

  function initFormHandlers() {
    // Form submission
    $('#formBahan').on('submit', function(e) {
      e.preventDefault();

      // Clear previous validation
      $('.is-invalid').removeClass('is-invalid');
      $('.invalid-feedback').text('');

      // Show loading on submit button
      const submitBtn = $('#submitBtn');
      const originalText = submitBtn.html();
      submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sedang Menyimpan...');

      var formData = new FormData(this);
      $.ajax({
        url: base_url + 'Back_Bahan/save_data',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(res) {
          if (res.status === 'success') {
            $('#form-modal-bahan').modal('hide');
            showSuccess(res.message);
            loadBahanData();
          } else {
            showError(res.message);
            // Show validation errors if any
            if (res.errors) {
              showValidationErrors(res.errors);
            }
          }
        },
        error: function(xhr) {
          let errorMsg = 'Terjadi kesalahan saat menyimpan data bahan!';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMsg = xhr.responseJSON.message;
          }
          showError(errorMsg);
        },
        complete: function() {
          // Reset button
          submitBtn.prop('disabled', false).html(originalText);
        }
      });
    });

    // Price difference calculator
    $('#harga_awal, #harga_sekarang').on('input', function() {
      checkPriceDifference();
    });

    // Format currency input
    $('#harga_awal, #harga_sekarang').on('blur', function() {
      formatCurrencyInput($(this));
    });

    // Character counter for keterangan
    $('#keterangan').on('input', function() {
      const maxLength = 500;
      const currentLength = $(this).val().length;
      const remaining = maxLength - currentLength;

      let counterText = `${currentLength}/${maxLength} karakter`;
      if (remaining < 50) {
        counterText = `<span class="text-warning">${counterText}</span>`;
      }
      if (remaining < 0) {
        counterText = `<span class="text-danger">${counterText}</span>`;
      }

      $(this).next('.form-text').html(counterText);
    });
  }

  function checkPriceDifference() {
    const hargaAwal = parseFloat($('#harga_awal').val()) || 0;
    const hargaSekarang = parseFloat($('#harga_sekarang').val()) || 0;
    const diffContainer = $('#price-difference');
    const diffText = $('#price-diff-text');

    if (hargaAwal > 0 && hargaSekarang > 0 && hargaAwal !== hargaSekarang) {
      const difference = hargaSekarang - hargaAwal;
      const percentage = ((difference / hargaAwal) * 100).toFixed(1);

      let message = '';
      let alertClass = 'alert-warning';

      if (difference > 0) {
        message = `Harga naik Rp ${Math.abs(difference).toLocaleString('id-ID')} (${percentage}%)`;
        alertClass = 'alert-danger';
      } else {
        message = `Harga turun Rp ${Math.abs(difference).toLocaleString('id-ID')} (${Math.abs(percentage)}%)`;
        alertClass = 'alert-success';
      }

      diffContainer.removeClass('alert-warning alert-danger alert-success').addClass(alertClass);
      diffText.text(message);
      diffContainer.show();
    } else {
      diffContainer.hide();
    }
  }

  function formatCurrencyInput($input) {
    let value = $input.val().replace(/[^\d]/g, '');
    if (value) {
      $input.val(parseInt(value));
    }
  }

  function showValidationErrors(errors) {
    $.each(errors, function(field, message) {
      const $field = $('[name="' + field + '"]');
      $field.addClass('is-invalid');
      $field.next('.invalid-feedback').text(message);
    });
  }

  function loadBahanData() {
    $.ajax({
      url: base_url + 'Back_Bahan/get_data_bahan?_=' + new Date().getTime(),
      type: 'GET',
      dataType: 'json',
      success: renderBahanTable,
      error: function() {
        showError('Gagal memuat data bahan!');
      }
    });
  }

  function renderBahanTable(result) {
    var html = '';
    var no = 1;
    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
      $('#datatable').DataTable().clear().destroy();
    }
    if (result.show_data && result.show_data.length > 0) {
      result.show_data.forEach(function(item) {
        html += buildBahanRow(item, no++);
      });
      $('#show_data_bahan').html(html);
      setTimeout(function() {
        initDataTable();
      }, 100);
    } else {
      html = buildEmptyRow();
      $('#show_data_bahan').html(html);
    }
  }

  function resetForm() {
    $('#formBahan')[0].reset();
    $('#id').val('');
    $('#stat').val('add');
    $('.is-invalid').removeClass('is-invalid');
    $('.invalid-feedback').text('');
    $('#price-difference').hide();
    $('#keterangan').next('.form-text').html('Maksimal 500 karakter');
  }

  function formatRupiah(angka) {
    angka = parseInt(angka, 10); // pastikan angka
    if (isNaN(angka)) return 'Rp 0';
    return 'Rp ' + angka.toLocaleString('id-ID');
  }

  function buildBahanRow(item, no) {
    // Calculate price status
    const hargaAwal = parseFloat(item.harga_awal) || 0;
    const hargaSekarang = parseFloat(item.harga_sekarang) || 0;
    let statusBadge = '<span class="badge bg-secondary">Sama</span>';

    if (hargaSekarang > hargaAwal) {
      const increase = ((hargaSekarang - hargaAwal) / hargaAwal * 100).toFixed(1);
      statusBadge = `<span class="badge bg-danger" title="Naik ${increase}%"><i class="fas fa-arrow-up"></i> Naik</span>`;
    } else if (hargaSekarang < hargaAwal) {
      const decrease = ((hargaAwal - hargaSekarang) / hargaAwal * 100).toFixed(1);
      statusBadge = `<span class="badge bg-success" title="Turun ${decrease}%"><i class="fas fa-arrow-down"></i> Turun</span>`;
    }

    return `
    <tr>
      <td class="text-center">${no}</td>
      <td>
        <div class="d-flex align-items-center">
          <div>
            <strong>${escapeHtml(item.nama_bahan)}</strong>
            ${item.keterangan ? `<br><small class="text-muted">${escapeHtml(item.keterangan)}</small>` : ''}
          </div>
        </div>
      </td>
      <td class="text-center">
        <span class="badge bg-info">${escapeHtml(item.nama_satuan)}</span>
      </td>
      <td class="text-end">${formatRupiah(item.harga_awal)}</td>
      <td class="text-end">
        <strong>${formatRupiah(item.harga_sekarang)}</strong>
      </td>
      <td class="text-center">${statusBadge}</td>
      <td class="text-center">
        <div class="btn-group" role="group">
          <button class="btn btn-outline-warning btn-sm btn-edit" data-id="${item.id_bahan}" type="button" title="Edit bahan">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-outline-danger btn-sm btn-delete" data-id="${item.id_bahan}" type="button" title="Hapus bahan">
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
        <td colspan="7" class="text-center py-5">
          <div class="text-muted">
            <i class="fas fa-carrot fa-3x mb-3 text-primary"></i>
            <h5>Tidak ada data bahan</h5>
            <p class="mb-3">Belum ada bahan baku yang ditambahkan ke sistem</p>
            <button type="button" class="btn btn-primary" onclick="tambah_bahan()">
              <i class="fas fa-plus me-1"></i>Tambah Bahan Pertama
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
      url: base_url + 'Back_Bahan/get_bahan_by_id',
      type: 'POST',
      data: {
        id: id
      },
      dataType: 'json',
      success: function(res) {
        if (res.status === 'success') {
          $('#formBahan')[0].reset();
          $('#formBahan input[name="stat"]').val('edit');
          $('#formBahan input[name="id"]').val(res.data.id_bahan);
          $('#nama_bahan').val(res.data.nama_bahan);
          $('#id_satuan').val(res.data.id_satuan);
          $('#harga_awal').val(res.data.harga_awal);
          $('#harga_sekarang').val(res.data.harga_sekarang);
          $('.modal-title').text('Edit Bahan: ' + res.data.nama_bahan);
          $('#form-modal-bahan').modal('show');
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
        title: 'Hapus Bahan?',
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
      if (confirm('Hapus bahan?\nData yang dihapus tidak dapat dikembalikan!')) {
        deleteData(id, button);
      }
    }
  }

  function deleteData(id, button) {
    var originalHtml = button.html();
    button.prop('disabled', true).html('<i class="ri-loader-2-line spin"></i>');
    $.ajax({
      url: base_url + 'Back_Bahan/delete_data',
      type: 'POST',
      data: {
        id: id
      },
      dataType: 'json',
      success: function(response) {
        if (response.status === 'success') {
          showSuccess(response.message || 'Data berhasil dihapus');
          setTimeout(loadBahanData, 500);
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
    $('#formBahan')[0].reset();
    $('#formBahan input[name="stat"]').val('add');
    $('#formBahan input[name="id"]').val('');
    $('#formBahan').data('submitting', false);
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