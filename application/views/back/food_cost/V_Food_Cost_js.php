<script>
  // ============================================
  // GLOBAL FUNCTIONS - Available Immediately
  // ============================================

  // Fungsi tambah menu - HARUS DIDEFINISIKAN DI SINI!
  window.tambah_menu = function() {
    console.log('Tambah menu clicked!');
    try {
      // Reset form
      var form = document.getElementById('form-food-cost');
      if (form) form.reset();

      // Set values
      $('#menu_id').val('');
      $('#bahan-container').empty();
      $('#modalLabel').text('Tambah Menu Food Cost');
      $('#stat').val('add');

      // Show modal
      $('#modal-food-cost').modal('show');
    } catch (e) {
      console.error('Error in tambah_menu:', e);
      alert('Error: ' + e.message);
    }
  };

  // Fungsi edit menu
  window.editMenu = function(id) {
    console.log('Edit menu:', id);
    alert('Edit menu ID: ' + id);
  };

  // Fungsi delete menu
  window.deleteMenu = function(id) {
    console.log('Delete menu:', id);
    if (confirm('Hapus menu ID: ' + id + '?')) {
      alert('Delete functionality will be implemented');
    }
  };
  $(document).ready(function() {
    console.log('=== Food Cost JS Loaded ===');
    console.log('tambah_menu function:', typeof window.tambah_menu);

    // Global variables
    let bahanCounter = 0;
    let table;

    // Bahan list dari database dengan fallback
    let bahanDatabase = [];
    try {
      bahanDatabase = <?php echo json_encode($bahan_list ?? []); ?>;
      if (!Array.isArray(bahanDatabase)) {
        bahanDatabase = [];
      }
    } catch (e) {
      console.log('Error loading bahan data:', e);
      bahanDatabase = [];
    }

    // Satuan options dengan fallback - PERBAIKAN VARIABLE NAME
    let satuanOptions = [];
    try {
      satuanOptions = <?php echo json_encode($satuan_list ?? []); ?>;
      if (!Array.isArray(satuanOptions)) {
        satuanOptions = [{
            id_satuan: 1,
            nama_satuan: 'kg'
          },
          {
            id_satuan: 2,
            nama_satuan: 'gram'
          },
          {
            id_satuan: 3,
            nama_satuan: 'liter'
          },
          {
            id_satuan: 4,
            nama_satuan: 'pcs'
          }
        ];
      }
    } catch (e) {
      console.log('Error loading satuan data:', e);
      satuanOptions = [{
          id_satuan: 1,
          nama_satuan: 'kg'
        },
        {
          id_satuan: 2,
          nama_satuan: 'gram'
        },
        {
          id_satuan: 3,
          nama_satuan: 'liter'
        },
        {
          id_satuan: 4,
          nama_satuan: 'pcs'
        }
      ];
    }

    // Initialize dengan check data
    if (bahanDatabase.length === 0) {
      console.log('⚠️ Warning: Tidak ada data bahan dari database');
    } else {
      console.log(`✅ Loaded ${bahanDatabase.length} bahan from database`);
    }

    console.log('✅ Loaded satuan options:', satuanOptions);

    // Initialize DataTable
    initializeDataTable();

    // Load initial statistics
    loadStatistics();

    // Initialize DataTable function
    function initializeDataTable() {
      table = $('#datatable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
          url: "<?php echo base_url('food-cost/get_data'); ?>",
          type: "GET",
          dataSrc: "data",
          error: function(xhr, error, code) {
            console.log('Error loading data:', error);
            $('#food-cost-data').html('<tr><td colspan="7" class="text-center">Gagal memuat data</td></tr>');
          }
        },
        columns: [{
            data: null,
            render: function(data, type, row, meta) {
              return meta.row + 1;
            },
            orderable: false,
            searchable: false,
            className: 'text-center'
          },
          {
            data: 'nama_menu',
            render: function(data, type, row) {
              return data || '-';
            }
          },
          {
            data: 'deskripsi',
            render: function(data, type, row) {
              return data || '-';
            }
          },
          {
            data: 'total_bahan',
            render: function(data, type, row) {
              return parseInt(data) || 0;
            },
            className: 'text-center'
          },
          {
            data: 'biaya_produksi',
            render: function(data, type, row) {
              return 'Rp ' + parseFloat(data || 0).toLocaleString('id-ID');
            },
            className: 'text-end'
          },
          {
            data: 'food_cost',
            render: function(data, type, row) {
              return 'Rp ' + parseFloat(data || 0).toLocaleString('id-ID');
            },
            className: 'text-end'
          },
          {
            data: null,
            render: function(data, type, row) {
              return '<div class="btn-group" role="group">' +
                '<button type="button" class="btn btn-sm btn-outline-primary" onclick="editMenu(' + row.id + ')" title="Edit">' +
                '<i class="ri-edit-line"></i>' +
                '</button>' +
                '<button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteMenu(' + row.id + ')" title="Hapus">' +
                '<i class="ri-delete-bin-line"></i>' +
                '</button>' +
                '</div>';
            },
            orderable: false,
            searchable: false,
            className: 'text-center'
          }
        ],
        responsive: true,
        language: {
          processing: "Memuat data...",
          lengthMenu: "Tampilkan _MENU_ entri",
          zeroRecords: "Tidak ada data yang ditemukan",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
          infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
          infoFiltered: "(disaring dari _MAX_ total entri)",
          search: "Cari:",
          paginate: {
            first: "Pertama",
            last: "Terakhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
          }
        }
      });
    }

    // Event handler untuk pilih bahan dari database
    $(document).on('click', '.bahan-row-database', function() {
      const bahanData = {
        id_bahan: $(this).data('id'),
        nama_bahan: $(this).data('nama'),
        nama_satuan: $(this).data('satuan'),
        id_satuan: $(this).data('id-satuan'),
        harga_awal: $(this).data('harga-awal'),
        harga_sekarang: $(this).data('harga-sekarang'),
        harga_current: $(this).data('harga-current')
      };

      // Tambah bahan row dengan data dari database
      addBahanRowFromDatabase(bahanData);

      // Tutup modal
      $('#modal-pilih-bahan').modal('hide');

      // Show success message
      Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: `Bahan "${bahanData.nama_bahan}" berhasil ditambahkan`,
        timer: 2000,
        showConfirmButton: false
      });
    });

    // Function untuk menambah bahan row dari database
    function addBahanRowFromDatabase(bahan) {
      bahanCounter++;

      const hargaDisplay = bahan.harga_sekarang > 0 ? bahan.harga_sekarang : bahan.harga_awal;

      // Hitung nomor urut
      let rowNumber = $('#bahan-container tr').length + 1;

      let html = '<tr class="bahan-row table-info" data-counter="' + bahanCounter + '">';
      html += '<input type="hidden" name="id_bahan[]" value="' + bahan.id_bahan + '">';
      html += '<input type="hidden" name="id_satuan[]" value="' + bahan.id_satuan + '">';

      // Kolom No
      html += '<td class="text-center align-middle">';
      html += '<span class="badge bg-info">' + rowNumber + '</span>';
      html += '</td>';

      // Kolom Nama Bahan
      html += '<td>';
      html += '<input type="text" class="form-control form-control-sm bg-light" name="nama_bahan[]" value="' + bahan.nama_bahan + '" readonly>';
      html += '<small class="text-muted">';
      html += '<i class="ri-database-line me-1"></i>Dari Database (ID: ' + bahan.id_bahan + ')';
      html += '</small>';
      html += '</td>';

      // Kolom Jumlah
      html += '<td>';
      html += '<input type="number" class="form-control form-control-sm qty-input" name="qty[]" step="0.01" min="0" placeholder="0" required>';
      html += '</td>';

      // Kolom Satuan
      html += '<td>';
      html += '<input type="text" class="form-control form-control-sm bg-light" value="' + bahan.nama_satuan + '" readonly>';
      html += '</td>';

      // Kolom Harga per Unit
      html += '<td>';
      html += '<input type="number" class="form-control form-control-sm harga-input" name="harga_per_satuan[]" min="0" value="' + hargaDisplay + '" required>';
      if (bahan.harga_sekarang > 0) {
        html += '<small class="text-success d-block mt-1">';
        html += '<i class="ri-price-tag-3-line me-1"></i>Harga Update';
        html += '</small>';
      } else {
        html += '<small class="text-muted d-block mt-1">';
        html += '<i class="ri-price-tag-3-line me-1"></i>Harga Default';
        html += '</small>';
      }
      html += '</td>';

      // Kolom Total Harga
      html += '<td>';
      html += '<input type="text" class="form-control form-control-sm total-bahan bg-light" readonly value="0">';
      html += '</td>';

      // Kolom Aksi
      html += '<td class="text-center align-middle">';
      html += '<button type="button" class="btn btn-danger btn-sm btn-remove-bahan" title="Hapus">';
      html += '<i class="ri-delete-bin-line"></i>';
      html += '</button>';
      html += '</td>';

      html += '</tr>';

      $('#bahan-container').append(html);
      updateRowNumbers();
      calculateTotal();
    }

    // Function untuk tambah bahan manual
    function addBahanRowManual() {
      bahanCounter++;

      // Build satuan options - PERBAIKAN MENGGUNAKAN satuanOptions
      let satuanOptionsHtml = '<option value="">Pilih Satuan</option>';
      satuanOptions.forEach(function(satuan) {
        satuanOptionsHtml += '<option value="' + satuan.id_satuan + '">' + satuan.nama_satuan + '</option>';
      });

      // Hitung nomor urut
      let rowNumber = $('#bahan-container tr').length + 1;

      let html = '<tr class="bahan-row" data-counter="' + bahanCounter + '">';
      html += '<input type="hidden" name="id_bahan[]" value="">';

      // Kolom No
      html += '<td class="text-center align-middle">';
      html += '<span class="badge bg-secondary">' + rowNumber + '</span>';
      html += '</td>';

      // Kolom Nama Bahan
      html += '<td>';
      html += '<input type="text" class="form-control form-control-sm" name="nama_bahan[]" placeholder="Pilih Bahan" required>';
      html += '</td>';

      // Kolom Jumlah
      html += '<td>';
      html += '<input type="number" class="form-control form-control-sm qty-input" name="qty[]" step="0.01" min="0" placeholder="0" required>';
      html += '</td>';

      // Kolom Satuan
      html += '<td>';
      html += '<select class="form-select form-select-sm" name="id_satuan[]" required>';
      html += satuanOptionsHtml;
      html += '</select>';
      html += '</td>';

      // Kolom Harga per Unit
      html += '<td>';
      html += '<input type="number" class="form-control form-control-sm harga-input" name="harga_per_satuan[]" min="0" placeholder="0" required>';
      html += '</td>';

      // Kolom Total Harga
      html += '<td>';
      html += '<input type="text" class="form-control form-control-sm total-bahan bg-light" readonly value="0">';
      html += '</td>';

      // Kolom Aksi
      html += '<td class="text-center align-middle">';
      html += '<button type="button" class="btn btn-danger btn-sm btn-remove-bahan" title="Hapus">';
      html += '<i class="ri-delete-bin-line"></i>';
      html += '</button>';
      html += '</td>';

      html += '</tr>';

      $('#bahan-container').append(html);
      updateRowNumbers();
      calculateTotal();
    }

    // Update row numbers after add/delete
    function updateRowNumbers() {
      $('#bahan-container .bahan-row').each(function(index) {
        $(this).find('td:first .badge').text(index + 1);
      });
    }

    // Calculate Total function - SIMPLIFIED untuk table format
    function calculateTotal() {
      let totalKeseluruhan = 0;

      $('#bahan-container .bahan-row').each(function() {
        const qty = parseFloat($(this).find('.qty-input').val()) || 0;
        const harga = parseFloat($(this).find('.harga-input').val()) || 0;

        if (qty > 0 && harga > 0) {
          const totalHarga = qty * harga;
          totalKeseluruhan += totalHarga;

          // Update total harga per row
          $(this).find('.total-bahan').val('Rp ' + totalHarga.toLocaleString('id-ID'));
        } else {
          $(this).find('.total-bahan').val('Rp 0');
        }
      });

      // Update display total keseluruhan
      $('#display-total-keseluruhan').text('Rp ' + totalKeseluruhan.toLocaleString('id-ID'));

      // Update calculation card jika masih ada
      if ($('#calc-total-bahan').length) {
        const biayaProduksi = totalKeseluruhan * 0.20;
        const foodCost = totalKeseluruhan + biayaProduksi;

        $('#calc-total-bahan').text('Rp ' + totalKeseluruhan.toLocaleString('id-ID'));
        $('#calc-biaya-produksi').text('Rp ' + biayaProduksi.toLocaleString('id-ID'));
        $('#calc-food-cost').text('Rp ' + foodCost.toLocaleString('id-ID'));
      }
    }

    // Search bahan functionality
    $('#btn-search-bahan').click(function() {
      searchBahan();
    });

    $('#search-bahan').keypress(function(e) {
      if (e.which == 13) { // Enter key
        searchBahan();
      }
    });

    function searchBahan() {
      const keyword = $('#search-bahan').val().trim();

      if (keyword.length < 2) {
        Swal.fire({
          icon: 'warning',
          title: 'Peringatan!',
          text: 'Masukkan minimal 2 karakter untuk pencarian'
        });
        return;
      }

      $.ajax({
        url: "<?php echo base_url('food-cost/search_bahan'); ?>",
        type: "POST",
        data: {
          keyword: keyword
        },
        dataType: "json",
        beforeSend: function() {
          $('#btn-search-bahan').html('<i class="ri-loader-2-line spinner-border spinner-border-sm"></i>');
        },
        success: function(response) {
          if (response.success && response.data.length > 0) {
            renderBahanDatabase(response.data);
          } else {
            $('#daftar-bahan-database').html(` <
          tr >
            <
            td colspan = "5"
          class = "text-center text-muted" >
            <
            i class = "ri-search-line me-2" > < /i>Tidak ada bahan yang sesuai dengan pencarian "${keyword}" < /
          td > <
            /tr>
          `);
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mencari bahan'
          });
        },
        complete: function() {
          $('#btn-search-bahan').html('<i class="ri-search-line"></i>');
        }
      });
    }

    // Reload bahan
    $('#btn-reload-bahan').click(function() {
      $('#search-bahan').val('');
      renderBahanDatabase(bahanDatabase);
    });

    // Render bahan database results
    function renderBahanDatabase(bahanList) {
      let html = '';

      if (bahanList && bahanList.length > 0) {
        bahanList.forEach(function(bahan, index) {
          const hargaCurrent = (bahan.harga_sekarang > 0) ? bahan.harga_sekarang : bahan.harga_awal;

          html += '<tr class="bahan-row-database cursor-pointer"';
          html += ' data-id="' + bahan.id_bahan + '"';
          html += ' data-nama="' + bahan.nama_bahan + '"';
          html += ' data-satuan="' + bahan.nama_satuan + '"';
          html += ' data-id-satuan="' + bahan.id_satuan + '"';
          html += ' data-harga-awal="' + bahan.harga_awal + '"';
          html += ' data-harga-sekarang="' + bahan.harga_sekarang + '"';
          html += ' data-harga-current="' + hargaCurrent + '">';
          html += '<td>' + (index + 1) + '</td>';
          html += '<td>';
          html += '<strong>' + bahan.nama_bahan + '</strong>';
          html += '<br><small class="text-muted">ID: ' + bahan.id_bahan + '</small>';
          html += '</td>';
          html += '<td><span class="badge bg-secondary">' + bahan.nama_satuan + '</span></td>';
          html += '<td><span class="text-muted">Rp ' + parseFloat(bahan.harga_awal).toLocaleString('id-ID') + '</span></td>';
          html += '<td>';
          html += (bahan.harga_sekarang > 0 ?
            '<strong class="text-success">Rp ' + parseFloat(bahan.harga_sekarang).toLocaleString('id-ID') + '</strong>' :
            '<span class="text-muted">-</span>');
          html += '</td>';
          html += '</tr>';
        });
      } else {
        html = '<tr>';
        html += '<td colspan="5" class="text-center text-muted">';
        html += '<i class="ri-inbox-line me-2"></i>Tidak ada data bahan';
        html += '</td>';
        html += '</tr>';
      }

      $('#daftar-bahan-database').html(html);
    }

    // Tambah Menu function
    window.tambah_menu = function() {
      resetForm();
      $('#modalLabel').text('Tambah Menu Food Cost');
      $('#stat').val('add');

      // Add initial bahan row - PERBAIKAN: tidak otomatis add row
      // User bisa pilih dari database atau manual

      $('#modal-food-cost').modal('show');
    };

    // Edit Menu function
    window.editMenu = function(id) {
      $.ajax({
        url: "<?php echo base_url('food-cost/get_by_id'); ?>",
        type: "POST",
        data: {
          id: id
        },
        dataType: "json",
        success: function(response) {
          if (response.success) {
            // Fill form data
            $('#modalLabel').text('Edit Menu Food Cost');
            $('#stat').val('edit');
            $('#menu_id').val(response.data.menu.id);
            $('#nama_menu').val(response.data.menu.nama_menu);
            $('#deskripsi').val(response.data.menu.deskripsi);

            // Clear bahan container
            $('#bahan-container').empty();
            bahanCounter = 0;

            // Add bahan rows
            if (response.data.bahan && response.data.bahan.length > 0) {
              response.data.bahan.forEach(function(bahan) {
                if (bahan.id_bahan) {
                  // From database
                  addBahanRowFromDatabase(bahan);
                  // Fill qty and harga
                  const lastRow = $('#bahan-container .bahan-row').last();
                  lastRow.find('[name="qty[]"]').val(bahan.qty);
                  if (bahan.harga_per_satuan) {
                    lastRow.find('[name="harga_per_satuan[]"]').val(bahan.harga_per_satuan);
                  }
                } else {
                  // Manual input
                  addBahanRowManual();
                  // Fill with data
                  const lastRow = $('#bahan-container .bahan-row').last();
                  lastRow.find('[name="nama_bahan[]"]').val(bahan.nama_bahan);
                  lastRow.find('[name="qty[]"]').val(bahan.qty);
                  lastRow.find('[name="harga_per_satuan[]"]').val(bahan.harga_per_satuan);
                  if (bahan.id_satuan) {
                    lastRow.find('[name="id_satuan[]"]').val(bahan.id_satuan);
                  }
                }
              });
            }

            calculateTotal();
            $('#modal-food-cost').modal('show');
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response.message || 'Gagal memuat data menu'
            });
          }
        },
        error: function() {
          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat memuat data'
          });
        }
      });
    };

    // Delete Menu function
    window.deleteMenu = function(id) {
      Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data menu dan semua bahan akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "<?php echo base_url('food-cost/delete_data'); ?>",
            type: "POST",
            data: {
              id: id
            },
            dataType: "json",
            success: function(response) {
              if (response.success) {
                table.ajax.reload(null, false);
                loadStatistics();

                Swal.fire({
                  icon: 'success',
                  title: 'Berhasil!',
                  text: 'Menu berhasil dihapus',
                  timer: 2000,
                  showConfirmButton: false
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Error!',
                  text: response.message || 'Gagal menghapus menu'
                });
              }
            },
            error: function() {
              Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Terjadi kesalahan saat menghapus data'
              });
            }
          });
        }
      });
    };

    // Reset Form function
    function resetForm() {
      $('#form-food-cost')[0].reset();
      $('#menu_id').val('');
      $('#bahan-container').empty();
      bahanCounter = 0;

      // Reset calculations
      $('#display-total-keseluruhan').text('Rp 0');
      if ($('#calc-total-bahan').length) {
        $('#calc-total-bahan').text('Rp 0');
        $('#calc-biaya-produksi').text('Rp 0');
        $('#calc-food-cost').text('Rp 0');
      }

      // Reset validation
      $('.form-control').removeClass('is-invalid');
      $('.invalid-feedback').text('');
    }

    // Load Statistics function
    function loadStatistics() {
      $.ajax({
        url: "<?php echo base_url('food-cost/get_stats'); ?>",
        type: "GET",
        dataType: "json",
        success: function(response) {
          if (response.success) {
            $('#total-menu').text(response.data.total_menu || 0);
            $('#avg-food-cost').text('Rp ' + parseFloat(response.data.avg_food_cost || 0).toLocaleString('id-ID'));
            $('#total-food-cost').text('Rp ' + parseFloat(response.data.total_food_cost || 0).toLocaleString('id-ID'));
          }
        },
        error: function() {
          console.log('Gagal memuat statistik');
        }
      });
    }

    // Event Handlers

    // Remove bahan button
    $(document).on('click', '.btn-remove-bahan', function() {
      $(this).closest('.bahan-row').remove();
      updateRowNumbers();
      calculateTotal();
    });

    // Calculate when input changes
    $(document).on('input change', '.qty-input, .harga-input', function() {
      calculateTotal();
    });

    // Form submission
    $('#form-food-cost').on('submit', function(e) {
      e.preventDefault();

      // Validate form
      if (!validateForm()) {
        return false;
      }

      // Collect bahan data
      const bahanData = [];
      $('#bahan-container .bahan-row').each(function() {
        const nama_bahan = $(this).find('[name="nama_bahan[]"]').val();
        const qty = $(this).find('[name="qty[]"]').val();
        const harga_per_satuan = $(this).find('[name="harga_per_satuan[]"]').val();
        const id_bahan = $(this).find('[name="id_bahan[]"]').val();
        const id_satuan = $(this).find('[name="id_satuan[]"]').val();

        if (nama_bahan && qty && harga_per_satuan) {
          bahanData.push({
            id_bahan: id_bahan || null,
            nama_bahan: nama_bahan,
            qty: parseFloat(qty),
            id_satuan: id_satuan || null,
            harga_per_satuan: parseFloat(harga_per_satuan)
          });
        }
      });

      if (bahanData.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Peringatan!',
          text: 'Minimal tambahkan 1 bahan untuk menu'
        });
        return false;
      }

      // Prepare form data
      const formData = {
        stat: $('#stat').val(),
        id: $('#menu_id').val(),
        nama_menu: $('#nama_menu').val(),
        deskripsi: $('#deskripsi').val(),
        bahan: bahanData
      };

      // Submit data
      $.ajax({
        url: "<?php echo base_url('food-cost/save_data'); ?>",
        type: "POST",
        data: formData,
        dataType: "json",
        beforeSend: function() {
          $('button[type="submit"]').prop('disabled', true).html('<i class="ri-loader-2-line spinner-border spinner-border-sm me-1"></i> Menyimpan...');
        },
        success: function(response) {
          if (response.success) {
            $('#modal-food-cost').modal('hide');
            table.ajax.reload(null, false);
            loadStatistics();

            Swal.fire({
              icon: 'success',
              title: 'Berhasil!',
              text: response.message || 'Data berhasil disimpan',
              timer: 2000,
              showConfirmButton: false
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error!',
              text: response.message || 'Gagal menyimpan data'
            });
          }
        },
        error: function(xhr) {
          let errorMessage = 'Terjadi kesalahan saat menyimpan data';
          if (xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
          }

          Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: errorMessage
          });
        },
        complete: function() {
          $('button[type="submit"]').prop('disabled', false).html('<i class="ri-save-line me-1"></i> Simpan');
        }
      });
    });

    // Form validation
    function validateForm() {
      let isValid = true;

      // Reset validation
      $('.form-control').removeClass('is-invalid');
      $('.invalid-feedback').remove();

      // Validate nama menu
      if (!$('#nama_menu').val().trim()) {
        $('#nama_menu').addClass('is-invalid');
        $('#nama_menu').after('<div class="invalid-feedback">Nama menu harus diisi</div>');
        isValid = false;
      }

      // Validate bahan
      let hasBahan = false;
      $('#bahan-container .bahan-row').each(function() {
        const nama_bahan = $(this).find('[name="nama_bahan[]"]').val();
        const qty = $(this).find('[name="qty[]"]').val();
        const harga_per_satuan = $(this).find('[name="harga_per_satuan[]"]').val();
        const pembagian_porsi = $(this).find('[name="pembagian_porsi[]"]').val();

        if (nama_bahan || qty || harga_per_satuan || pembagian_porsi) {
          hasBahan = true;

          // Validate individual fields
          if (!nama_bahan) {
            $(this).find('[name="nama_bahan[]"]').addClass('is-invalid');
            isValid = false;
          }
          if (!qty || parseFloat(qty) <= 0) {
            $(this).find('[name="qty[]"]').addClass('is-invalid');
            isValid = false;
          }
          if (!harga_per_satuan || parseFloat(harga_per_satuan) <= 0) {
            $(this).find('[name="harga_per_satuan[]"]').addClass('is-invalid');
            isValid = false;
          }
          if (!pembagian_porsi || parseInt(pembagian_porsi) <= 0) {
            $(this).find('[name="pembagian_porsi[]"]').addClass('is-invalid');
            isValid = false;
          }
        }
      });

      if (!hasBahan) {
        Swal.fire({
          icon: 'warning',
          title: 'Peringatan!',
          text: 'Minimal tambahkan 1 bahan untuk menu'
        });
        isValid = false;
      }

      return isValid;
    }

    // Modal close event
    $('#modal-food-cost').on('hidden.bs.modal', function() {
      resetForm();
    });

    // Expose functions globally
    window.addBahanRowFromDatabase = addBahanRowFromDatabase;
    window.addBahanRowManual = addBahanRowManual;
    window.calculateTotal = calculateTotal;
  });
</script>