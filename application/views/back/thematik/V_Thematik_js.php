<script type="text/javascript">
  (function() {
    'use strict';

    // CHECK JQUERY
    if (typeof jQuery === 'undefined') {
      return;
    }

    // PREVENT MULTIPLE INIT
    if (window.thematicInitialized) {
      return;
    }
    window.thematicInitialized = true;

    $(document).ready(function() {
      initSidebar();
      initOtherFunctions();
    });

    // ===== SIDEBAR FUNCTIONS =====
    function initSidebar() {
      $(document).on('click', '#vertical-menu-btn', function(e) {
        e.preventDefault();
        toggleSidebar();
      });

      $(window).on('resize', function() {
        handleResize();
      });

      $(document).on('click', function(e) {
        if ($(window).width() < 992) {
          if (!$(e.target).closest('.vertical-menu, #vertical-menu-btn').length) {
            closeSidebar();
          }
        }
      });

      initSidebarState();
    }

    function toggleSidebar() {
      var $body = $('body');
      var windowWidth = $(window).width();

      if (windowWidth >= 992) {
        $body.toggleClass('sidebar-collapsed');
        var isCollapsed = $body.hasClass('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', isCollapsed);

        setTimeout(function() {
          if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').DataTable().columns.adjust().responsive.recalc();
          }
        }, 350);
      } else {
        $body.toggleClass('sidebar-open');
      }
    }

    function closeSidebar() {
      $('body').removeClass('sidebar-open');
    }

    function initSidebarState() {
      var $body = $('body');
      var windowWidth = $(window).width();

      $body.removeClass('sidebar-collapsed sidebar-open');

      if (windowWidth >= 992) {
        var isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
          $body.addClass('sidebar-collapsed');
        }
      }
    }

    function handleResize() {
      var $body = $('body');
      var windowWidth = $(window).width();

      if (windowWidth >= 992) {
        $body.removeClass('sidebar-open');
        var isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
          $body.addClass('sidebar-collapsed');
        } else {
          $body.removeClass('sidebar-collapsed');
        }
      } else {
        $body.removeClass('sidebar-collapsed');
      }

      setTimeout(function() {
        if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
          $('#datatable').DataTable().columns.adjust().responsive.recalc();
        }
      }, 100);
    }

    // ===== MAIN FUNCTIONS =====
    function initOtherFunctions() {
      initModals();
      initFormHandlers();
      initButtonHandlers();
      loadData();
    }

    function initModals() {
      $('#modal_form').on('hidden.bs.modal', function() {
        resetForm();
      });
    }

    function initFormHandlers() {
      $('#form_thematic').on('submit', function(e) {
        e.preventDefault();
        if (!$(this).data('submitting')) {
          submitForm();
        }
      });
    }

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

      $(document).on('click', '.btn-tambah-thematic', function(e) {
        e.preventDefault();
        tambah_data();
      });

      window.tambah_data = function() {
        resetForm();
        $('.modal-title').text('Tambah Thematic');
        $('#modal_form').modal('show');
        setTimeout(function() {
          $('#thematik_nama').focus();
        }, 500);
      };
    }

    // ===== DATA FUNCTIONS =====
    function loadData() {
      $.ajax({
        type: 'GET',
        url: "<?php echo base_url('Back_Thematik/get_data_thematik'); ?>",
        dataType: 'json',
        success: function(result) {
          renderDataTable(result);
        },
        error: function() {
          showError('Gagal memuat data. Silakan refresh halaman.');
        }
      });
    }

    function renderDataTable(result) {
      var html = '';
      var no = 1;

      if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').DataTable().clear().destroy();
      }

      if (result && result.length > 0) {
        result.forEach(function(item) {
          html += buildTableRow(item, no++);
        });
      } else {
        html = buildEmptyRow();
      }

      $('#show_data_thematic').html(html);

      setTimeout(function() {
        initDataTable();
      }, 100);
    }

    function buildTableRow(item, no) {
      var deskripsi = item.thematik_deskripsi && item.thematik_deskripsi.trim() !== '' ?
        escapeHtml(item.thematik_deskripsi) :
        '<em class="text-muted">Tidak ada deskripsi</em>';

      return `
          <tr>
            <td class="text-center col-no">${no}</td>
            <td class="col-nama">
              <div class="thematic-name">${escapeHtml(item.thematik_nama)}</div>
            </td>
            <td class="col-deskripsi">${deskripsi}</td>
            <td class="text-center col-aksi">
              <div class="btn-group btn-group-sm" role="group">
                <button class="btn btn-warning btn-edit" data-id="${item.id_thematik}" type="button" title="Edit">
                  <i class="fas fa-edit text-white"></i>
                </button>
                <button class="btn btn-danger btn-delete" data-id="${item.id_thematik}" type="button" title="Hapus">
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
                <td colspan="4" class="text-center py-5">
                    <div class="text-muted">
                        <i class="ri-earth-line fa-3x mb-3"></i>
                        <h5>Tidak ada data</h5>
                        <p>Belum ada thematic yang ditambahkan</p>
                        <button type="button" class="btn btn-primary btn-sm" onclick="tambah_data()">
                            <i class="ri-add-circle-line me-1"></i>Tambah Thematic Pertama
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
            [1, 'asc']
          ],
          columnDefs: [{
              targets: [0, 3],
              orderable: false,
              searchable: false,
              className: 'text-center'
            },
            {
              targets: 0,
              width: '60px',
              className: 'text-center'
            },
            {
              targets: 1,
              width: '200px',
              className: 'text-left'
            },
            {
              targets: 2,
              width: 'auto',
              className: 'text-left'
            },
            {
              targets: 3,
              width: '180px',
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
          createdRow: function(row, data, dataIndex) {
            $(row).find('td').each(function() {
              $(this).css({
                'white-space': 'normal',
                'word-wrap': 'break-word',
                'word-break': 'break-word'
              });
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
        url: "<?php echo base_url('Back_Thematik/get_data_thematik_by_id/'); ?>" + id,
        type: 'GET',
        dataType: 'json',
        success: function(result) {
          if (result) {
            $('#method').val('edit');
            $('#id_thematik').val(result.id_thematik);
            $('#thematik_nama').val(result.thematik_nama);
            $('#thematik_deskripsi').val(result.thematik_deskripsi);
            $('.modal-title').text('Edit Thematic: ' + result.thematik_nama);
            $('#modal_form').modal('show');
            setTimeout(function() {
              $('#thematik_nama').focus();
            }, 500);
          } else {
            showError('Gagal memuat data edit');
          }
        },
        error: function() {
          showError('Gagal memuat data edit');
        }
      });
    }

    function submitForm() {
      var $form = $('#form_thematic');
      var $submitBtn = $('#btn_save');
      var method = $('#method').val();
      var url = '';

      if (method === 'tambah') {
        url = "<?php echo base_url('Back_Thematik/tambah_data_thematik'); ?>";
      } else {
        url = "<?php echo base_url('Back_Thematik/edit_data_thematik'); ?>";
      }

      $form.data('submitting', true);
      $submitBtn.prop('disabled', true).html('<i class="ri-loader-2-line spin me-2"></i>Menyimpan...');

      $.ajax({
        url: url,
        type: 'POST',
        data: $form.serialize(),
        dataType: 'json',
        success: function(result) {
          if (result.status) {
            $('#modal_form').modal('hide');
            showSuccess(result.message || 'Data berhasil disimpan');
            setTimeout(loadData, 500);
          } else {
            showError(result.message || 'Gagal menyimpan data');

            if (result.errors) {
              $.each(result.errors, function(field, message) {
                $('#' + field).addClass('is-invalid');
                $('#' + field).next('.invalid-feedback').text(message);
              });
            }
          }
        },
        error: function() {
          showError('Terjadi kesalahan pada server');
        },
        complete: function() {
          $form.data('submitting', false);
          $submitBtn.prop('disabled', false).html('<i class="ri-save-line me-2"></i>Simpan');
        }
      });
    }

    function confirmDelete(id, button) {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Hapus Thematic?',
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
        if (confirm('Hapus thematic?\nData yang dihapus tidak dapat dikembalikan!')) {
          deleteData(id, button);
        }
      }
    }

    function deleteData(id, button) {
      var originalHtml = button.html();
      button.prop('disabled', true).html('<i class="ri-loader-2-line spin"></i>');

      $.ajax({
        url: "<?php echo base_url('Back_Thematik/hapus_data_thematik'); ?>",
        type: 'POST',
        data: {
          id_thematik: id
        },
        dataType: 'json',
        success: function(response) {
          if (response.status) {
            showSuccess(response.message || 'Data berhasil dihapus');
            setTimeout(loadData, 500);
          } else {
            showError(response.message || 'Gagal menghapus data');
            button.prop('disabled', false).html(originalHtml);
          }
        },
        error: function(xhr, status, error) {
          showError('Terjadi kesalahan pada server');
          button.prop('disabled', false).html(originalHtml);
        }
      });
    }

    function resetForm() {
      $('#form_thematic')[0].reset();
      $('#method').val('tambah');
      $('#id_thematik').val('');
      $('#form_thematic').data('submitting', false);
      $('.form-control').removeClass('is-invalid');
      $('.invalid-feedback').text('');
      $('#modal_form').find(':focus').blur();
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