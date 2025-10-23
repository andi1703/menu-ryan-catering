<script type="text/javascript">
  // ANTI-DUPLICATE GUARD
  if (typeof window.bahanModuleLoaded !== 'undefined') {
    console.log('Bahan module already loaded, skipping...');
  } else {
    window.bahanModuleLoaded = true;

    // Global variables
    var base_url = $('#base_url').val() || '<?= base_url() ?>';
    var satuanList = [];
    var dataTable = null;
    var isTableInitialized = false;

    // PERBAIKAN: Hide table immediately before DOM ready
    $(function() {
      // Show loading state immediately
      //$('#bahan-table').hide();
      //$('#bahan-table-container').append('<div id="table-loading" class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Memuat data...</p></div>');

      // Initialize after DOM is fully ready
      setTimeout(function() {
        initBahanModule();
      }, 10);
    });

    function initBahanModule() {
      // Prevent multiple initialization
      if (isTableInitialized) {
        console.log('Table already initialized');
        return;
      }

      try {
        loadSatuanData();
        initFormHandlers();
        initPriceCalculation();
        initDataTableOnce();
      } catch (error) {
        console.error('Init error:', error);
        // Show table if error occurs
        $('#table-loading').remove();
        $('#bahan-table').show();
      }
    }

    function initDataTableOnce() {
      // Double-check prevention
      if (isTableInitialized) return;

      const tableElement = $('#bahan-table');
      if (!tableElement.length) {
        console.log('Table element not found');
        $('#table-loading').remove();
        return;
      }

      try {
        // Destroy any existing DataTable instance
        if ($.fn.DataTable.isDataTable('#bahan-table')) {
          console.log('Destroying existing DataTable');
          tableElement.DataTable().destroy();
        }

        // Mark as initializing
        isTableInitialized = true;

        // Initialize DataTable dengan auto numbering
        dataTable = tableElement.DataTable({
          responsive: true,
          pageLength: 10,
          lengthMenu: [
            [10, 25, 50, 100],
            [10, 25, 50, 100]
          ],
          order: [
            [1, 'asc']
          ], // Sort by Nama Bahan
          columnDefs: [{
            targets: [0, 6], // No dan Aksi
            orderable: false,
            searchable: false
          }],
          language: {
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Data tidak ditemukan",
            "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
            "infoEmpty": "Tidak ada data tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "search": "Cari:",
            "paginate": {
              "first": "Pertama",
              "last": "Terakhir",
              "next": "Selanjutnya",
              "previous": "Sebelumnya"
            }
          },
          // PERBAIKAN: Auto numbering yang benar
          drawCallback: function() {
            var api = this.api();
            var pageInfo = api.page.info();

            // Auto numbering berdasarkan pagination
            api.column(0, {
              page: 'current'
            }).nodes().each(function(cell, i) {
              cell.innerHTML = pageInfo.start + i + 1;
            });
          },
          // TAMBAHAN: Callback saat selesai init
          initComplete: function() {
            // Remove loading and show table
            $('#table-loading').remove();
            $('#bahan-table').fadeIn(300);
            console.log('DataTable initialized successfully');
          }
        });

      } catch (error) {
        console.error('DataTable initialization failed:', error);
        isTableInitialized = false;
        // Show table if initialization fails
        $('#table-loading').remove();
        $('#bahan-table').show();
      }
    }

    function loadSatuanData() {
      $.ajax({
        url: base_url + 'Back_Bahan/get_satuan_list',
        type: 'GET',
        dataType: 'json',
        cache: true,
        success: function(response) {
          if (response && response.status === 'success') {
            satuanList = response.data;
          }
        },
        error: function() {
          satuanList = [{
              id_satuan: 1,
              nama_satuan: 'Kg'
            },
            {
              id_satuan: 2,
              nama_satuan: 'Gram'
            },
            {
              id_satuan: 3,
              nama_satuan: 'Liter'
            },
            {
              id_satuan: 4,
              nama_satuan: 'Pcs'
            }
          ];
        }
      });
    }

    function getSatuanOptions(selectedId = '') {
      let options = '<option value="">Pilih Satuan</option>';
      satuanList.forEach(function(satuan) {
        const selected = satuan.id_satuan == selectedId ? 'selected' : '';
        const satuanName = $('<div>').text(satuan.nama_satuan).html();
        options += `<option value="${satuan.id_satuan}" ${selected}>${satuanName}</option>`;
      });
      return options;
    }

    function calculatePriceDifference() {
      try {
        const hargaAwal = parseFloat($('#harga_awal').val()) || 0;
        const hargaSekarang = parseFloat($('#harga_sekarang').val()) || 0;
        const diffAlert = $('#price-difference');
        const diffText = $('#price-diff-text');

        if (hargaAwal > 0 && hargaSekarang > 0) {
          if (hargaSekarang > hargaAwal) {
            const persen = ((hargaSekarang - hargaAwal) / hargaAwal) * 100;
            diffAlert.removeClass('alert-warning alert-success').addClass('alert-danger').show();
            diffText.html(`<i class="fas fa-arrow-up me-1"></i>Harga naik ${persen.toFixed(1)}% dari harga awal`);
          } else if (hargaSekarang < hargaAwal) {
            const persen = ((hargaAwal - hargaSekarang) / hargaAwal) * 100;
            diffAlert.removeClass('alert-warning alert-danger').addClass('alert-success').show();
            diffText.html(`<i class="fas fa-arrow-down me-1"></i>Harga turun ${persen.toFixed(1)}% dari harga awal`);
          } else {
            diffAlert.removeClass('alert-danger alert-success').addClass('alert-warning').show();
            diffText.html(`<i class="fas fa-equals me-1"></i>Harga sama dengan harga awal`);
          }
        } else {
          diffAlert.hide();
        }
      } catch (error) {
        // Silent error handling
      }
    }

    function initPriceCalculation() {
      $(document).off('input.bahanPrice').on('input.bahanPrice', '#harga_awal, #harga_sekarang', function() {
        calculatePriceDifference();
      });
    }

    function tambah_bahan() {
      if ($('#form_bahan').length) {
        $('#form_bahan')[0].reset();
        $('#bahan_id').val('');
        $('#form_stat').val('add');
        $('#price-difference').hide();
      }

      $('#modalBahanLabel').text('Tambah Bahan Baku');
      $('#form-modal-bahan').modal('show');
    }

    function edit_bahan(id) {
      if (!id) {
        Swal.fire('Error!', 'ID bahan tidak valid', 'error');
        return;
      }

      const editButton = $(`button[onclick="edit_bahan(${id})"]`);
      const originalButtonText = editButton.html();

      editButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');

      $.ajax({
        url: base_url + 'Back_Bahan/get_bahan_by_id',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(response) {
          editButton.prop('disabled', false).html(originalButtonText);

          if (response && response.status === 'success' && response.data) {
            const data = response.data;

            $('#bahan_id').val(data.id_bahan);
            $('#nama_bahan').val(data.nama_bahan);
            $('#id_satuan').val(data.id_satuan);
            $('#harga_awal').val(data.harga_awal);
            $('#harga_sekarang').val(data.harga_sekarang);
            $('#form_stat').val('edit');

            calculatePriceDifference();

            $('#modalBahanLabel').text('Edit Bahan Baku');
            $('#form-modal-bahan').modal('show');
          } else {
            Swal.fire('Error!', response.message || 'Data bahan tidak ditemukan', 'error');
          }
        },
        error: function(xhr, status, error) {
          editButton.prop('disabled', false).html(originalButtonText);
          Swal.fire('Error!', 'Gagal memuat data bahan dari server', 'error');
        }
      });
    }

    function hapus_bahan(id) {
      if (!id || id === '' || id === null || id === undefined) {
        Swal.fire('Error!', 'ID bahan tidak valid', 'error');
        return;
      }

      if (isNaN(id) || parseInt(id) <= 0) {
        Swal.fire('Error!', 'ID bahan harus berupa angka yang valid', 'error');
        return;
      }

      $.ajax({
        url: base_url + 'Back_Bahan/get_bahan_by_id',
        type: 'POST',
        data: {
          id: id
        },
        dataType: 'json',
        success: function(checkResponse) {
          if (checkResponse.status !== 'success') {
            Swal.fire('Error!', 'Data bahan tidak ditemukan atau sudah dihapus', 'error');
            return;
          }

          const bahanData = checkResponse.data;
          const namaEscaped = $('<div>').text(bahanData.nama_bahan).html();
          const satuanEscaped = $('<div>').text(bahanData.nama_satuan).html();

          Swal.fire({
            title: 'Konfirmasi Penghapusan',
            html: `
              <div class="text-start">
                <p>Apakah Anda yakin ingin menghapus bahan berikut?</p>
                <div class="alert alert-warning">
                  <strong>Nama Bahan:</strong> ${namaEscaped}<br>
                  <strong>Satuan:</strong> ${satuanEscaped}<br>
                  <strong>Harga Sekarang:</strong> Rp ${parseInt(bahanData.harga_sekarang).toLocaleString('id-ID')}
                </div>
                <p class="text-danger"><small><i class="fas fa-exclamation-triangle me-1"></i>Data yang sudah dihapus tidak dapat dikembalikan.</small></p>
              </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            width: '500px'
          }).then((result) => {
            if (result.isConfirmed) {
              Swal.fire({
                title: 'Menghapus...',
                text: `Menghapus bahan "${bahanData.nama_bahan}"...`,
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
              });

              $.ajax({
                url: base_url + 'Back_Bahan/delete_data',
                type: 'POST',
                data: {
                  id: parseInt(id)
                },
                dataType: 'json',
                success: function(response) {
                  if (response && response.status === 'success') {
                    Swal.fire({
                      title: 'Berhasil!',
                      text: response.message,
                      icon: 'success',
                      timer: 1500,
                      timerProgressBar: true,
                      showConfirmButton: false
                    }).then(() => {
                      window.location.reload();
                    });
                  } else {
                    Swal.fire('Error!', response.message || 'Gagal menghapus data', 'error');
                  }
                },
                error: function(xhr, status, error) {
                  let errorMsg = 'Terjadi kesalahan saat menghapus data';
                  if (xhr.status === 500) {
                    errorMsg = 'Server error - periksa log aplikasi';
                  } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                  }
                  Swal.fire('Error!', errorMsg, 'error');
                }
              });
            }
          });
        },
        error: function(xhr, status, error) {
          Swal.fire('Error!', 'Gagal memverifikasi data bahan', 'error');
        }
      });
    }

    function initFormHandlers() {
      $(document).off('.bahanForm').on('submit.bahanForm', '#form_bahan', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Sedang Menyimpan...');

        const formData = new FormData();
        const idValue = $('#bahan_id').val() || '';
        const statValue = $('#form_stat').val() || '';
        const namaValue = $('#nama_bahan').val() || '';
        const satuanValue = $('#id_satuan').val() || '';
        const hargaAwalValue = $('#harga_awal').val() || '';
        const hargaSekarangValue = $('#harga_sekarang').val() || '';

        if (idValue) formData.append('id', idValue);
        formData.append('stat', statValue);
        formData.append('nama_bahan', namaValue);
        formData.append('id_satuan', satuanValue);
        formData.append('harga_awal', hargaAwalValue);
        formData.append('harga_sekarang', hargaSekarangValue);

        if (statValue === 'edit' && !idValue) {
          Swal.fire('Error!', 'ID bahan tidak ditemukan untuk edit', 'error');
          submitBtn.prop('disabled', false).html(originalText);
          return;
        }

        $.ajax({
          url: base_url + 'Back_Bahan/save_data',
          type: 'POST',
          data: formData,
          processData: false,
          contentType: false,
          dataType: 'json',
          success: function(response) {
            if (response && response.status === 'success') {
              batalForm();
              Swal.fire('Berhasil!', response.message, 'success').then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire('Error!', response.message || 'Gagal menyimpan data', 'error');
            }
          },
          error: function() {
            Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data!', 'error');
          },
          complete: function() {
            submitBtn.prop('disabled', false).html(originalText);
          }
        });
      });

      $(document).off('.batalBahan').on('click.batalBahan', '#btn-batal-bahan', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        batalForm();
      });

      $(document).off('.closeBahan').on('click.closeBahan', '.btn-close', function(e) {
        if ($(this).closest('#form-modal-bahan').length) {
          e.preventDefault();
          e.stopImmediatePropagation();
          batalForm();
        }
      });
    }

    function batalForm() {
      try {
        if ($('#form_bahan').length) {
          $('#form_bahan')[0].reset();
          $('#bahan_id').val('');
          $('#form_stat').val('add');
        }

        $('#price-difference').hide();
        $('#form-modal-bahan').modal('hide');
      } catch (error) {
        $('#form-modal-bahan').hide();
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
      }
    }

    // Global function assignments
    window.tambah_bahan = tambah_bahan;
    window.edit_bahan = edit_bahan;
    window.hapus_bahan = hapus_bahan;
    window.calculatePriceDifference = calculatePriceDifference;
  }
</script>