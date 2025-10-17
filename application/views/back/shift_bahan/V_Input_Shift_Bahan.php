<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <title><?= $title ?> | Ryan Catering System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Sistem Manajemen Ryan Catering" name="description" />
  <meta content="Ryan Catering" name="author" />

  <!-- App CSS -->
  <link href="<?php echo base_url('assets_back/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <style>
    .input-table {
      font-size: 12px;
    }

    .input-table th {
      background-color: #f8f9fa;
      text-align: center;
      vertical-align: middle;
      padding: 8px 4px;
      border: 1px solid #dee2e6;
    }

    .input-table td {
      padding: 4px;
      border: 1px solid #dee2e6;
      text-align: center;
    }

    .bahan-name {
      text-align: left !important;
      font-weight: 500;
    }

    .quantity-input {
      width: 60px;
      height: 30px;
      border: 1px solid #ccc;
      text-align: center;
      font-size: 11px;
    }

    .divisi-header {
      writing-mode: vertical-rl;
      text-orientation: mixed;
      background-color: #e3f2fd !important;
    }

    .kategori-subheader {
      font-size: 10px;
      background-color: #f5f5f5 !important;
    }

    .total-column {
      background-color: #fff3cd !important;
      font-weight: bold;
    }

    .sticky-header {
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .table-container {
      max-height: 70vh;
      overflow-y: auto;
    }
  </style>
</head>

<body data-topbar="dark" data-layout="vertical">
  <div id="layout-wrapper">

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">

          <!-- Page Title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-0 font-size-18">
                    <i class="fas fa-edit me-2 text-primary"></i>Input Data Bahan Baku Per Shift
                  </h4>
                  <p class="mb-0 text-muted">Masukkan kebutuhan bahan baku untuk <?= date('l, d F Y', strtotime($tanggal_shift)) ?></p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo base_url('shift-bahan'); ?>">Data Shift Bahan</a></li>
                    <li class="breadcrumb-item active">Input Data</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Control Panel -->
          <div class="row mb-4">
            <div class="col-12">
              <div class="card">
                <div class="card-body">
                  <form id="shiftForm" method="post">
                    <div class="row align-items-end">
                      <div class="col-md-3">
                        <label for="tanggal_shift" class="form-label">Tanggal Shift</label>
                        <input type="date" class="form-control" id="tanggal_shift" name="tanggal_shift" value="<?= $tanggal_shift ?>" required>
                      </div>
                      <div class="col-md-2">
                        <label for="shift_type" class="form-label">Tipe Shift</label>
                        <select class="form-select" id="shift_type" name="shift_type" required>
                          <option value="lunch">Lunch</option>
                          <option value="dinner">Dinner</option>
                          <option value="breakfast">Breakfast</option>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <button type="button" class="btn btn-info me-2" onclick="loadTemplate()">
                          <i class="fas fa-file-import me-1"></i>Load Template
                        </button>
                        <button type="button" class="btn btn-warning me-2" onclick="clearAll()">
                          <i class="fas fa-eraser me-1"></i>Clear All
                        </button>
                      </div>
                      <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-success me-2" onclick="saveData()">
                          <i class="fas fa-save me-1"></i>Simpan Data
                        </button>
                        <a href="<?= base_url('shift-bahan') ?>" class="btn btn-secondary">
                          <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Input Table -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header bg-primary text-white">
                  <h5 class="mb-0">
                    <i class="fas fa-table me-2"></i>LIST RINCIAN BAHAN BAKU (SHIFT LUNCH)
                  </h5>
                  <p class="mb-0"><?= date('l, F j, Y', strtotime($tanggal_shift)) ?></p>
                </div>
                <div class="card-body p-0">
                  <div class="table-container">
                    <table class="table table-bordered input-table mb-0">
                      <thead class="sticky-header">
                        <tr>
                          <th rowspan="3" style="width: 40px;">NO</th>
                          <th rowspan="3" style="width: 200px;">BAHAN UTAMA</th>

                          <!-- Divisi Headers -->
                          <?php foreach ($divisi_list as $divisi) : ?>
                            <th class="divisi-header" colspan="<?= count($kategori_list) ?>"><?= $divisi['kode_divisi'] ?></th>
                          <?php endforeach; ?>

                          <th rowspan="3" style="width: 80px;" class="total-column">TOTAL</th>
                        </tr>
                        <tr>
                          <!-- Kategori Subheaders untuk setiap divisi -->
                          <?php foreach ($divisi_list as $divisi) : ?>
                            <?php foreach ($kategori_list as $kategori) : ?>
                              <th class="kategori-subheader" style="width: 50px;"><?= $kategori['kode_kategori'] ?></th>
                            <?php endforeach; ?>
                          <?php endforeach; ?>
                        </tr>
                        <tr>
                          <!-- Empty row for better spacing -->
                          <?php foreach ($divisi_list as $divisi) : ?>
                            <?php foreach ($kategori_list as $kategori) : ?>
                              <th class="kategori-subheader">-</th>
                            <?php endforeach; ?>
                          <?php endforeach; ?>
                        </tr>
                      </thead>
                      <tbody id="bahanTableBody">
                        <?php $no = 1;
                        foreach ($bahan_list as $bahan) : ?>
                          <tr data-bahan-id="<?= $bahan['id_bahan'] ?>">
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="bahan-name">
                              <strong><?= htmlspecialchars($bahan['nama_bahan']) ?></strong>
                              <br><small class="text-muted"><?= $bahan['nama_satuan'] ?></small>
                            </td>

                            <!-- Input cells untuk setiap divisi dan kategori -->
                            <?php foreach ($divisi_list as $divisi) : ?>
                              <?php foreach ($kategori_list as $kategori) : ?>
                                <td>
                                  <input type="number" class="quantity-input" min="0" step="0.1" data-bahan="<?= $bahan['id_bahan'] ?>" data-divisi="<?= $divisi['id_divisi'] ?>" data-kategori="<?= $kategori['id_shift_kategori'] ?>" onchange="calculateRowTotal(this)" placeholder="0">
                                </td>
                              <?php endforeach; ?>
                            <?php endforeach; ?>

                            <td class="total-column">
                              <span class="row-total" data-bahan="<?= $bahan['id_bahan'] ?>">0</span>
                            </td>
                          </tr>
                        <?php endforeach; ?>
                      </tbody>
                      <tfoot>
                        <tr class="table-info">
                          <td colspan="2" class="text-center"><strong>TOTAL KESELURUHAN</strong></td>

                          <!-- Column totals untuk setiap divisi dan kategori -->
                          <?php foreach ($divisi_list as $divisi) : ?>
                            <?php foreach ($kategori_list as $kategori) : ?>
                              <td class="text-center">
                                <strong class="col-total" data-divisi="<?= $divisi['id_divisi'] ?>" data-kategori="<?= $kategori['id_shift_kategori'] ?>">0</strong>
                              </td>
                            <?php endforeach; ?>
                          <?php endforeach; ?>

                          <td class="total-column text-center">
                            <strong id="grandTotal">0</strong>
                          </td>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                <div class="card-footer bg-light">
                  <div class="row align-items-center">
                    <div class="col-md-8">
                      <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Masukkan jumlah kebutuhan bahan untuk setiap divisi dan kategori. Sistem akan menghitung total otomatis.
                      </small>
                    </div>
                    <div class="col-md-4 text-end">
                      <button type="button" class="btn btn-primary btn-lg" onclick="saveData()">
                        <i class="fas fa-save me-2"></i>Simpan Semua Data
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div> <!-- container-fluid -->
      </div> <!-- page-content -->
    </div> <!-- main-content -->

  </div> <!-- layout-wrapper -->

  <!-- Loading Modal -->
  <div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body text-center">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <h5>Menyimpan Data...</h5>
          <p class="text-muted">Mohon tunggu sebentar</p>
        </div>
      </div>
    </div>
  </div>

  <!-- JavaScript -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>

  <script>
    let bahanData = [];
    const base_url = '<?= base_url() ?>';

    $(document).ready(function() {
      // Load existing data if available
      <?php if ($existing_data) : ?>
        loadExistingData();
      <?php endif; ?>

      // Auto-save draft setiap 30 detik
      setInterval(function() {
        saveDraft();
      }, 30000);
    });

    function calculateRowTotal(input) {
      const bahanId = $(input).data('bahan');
      let rowTotal = 0;

      // Sum all inputs in this row
      $(`input[data-bahan="${bahanId}"]`).each(function() {
        const value = parseFloat($(this).val()) || 0;
        rowTotal += value;
      });

      // Update row total display
      $(`.row-total[data-bahan="${bahanId}"]`).text(rowTotal.toFixed(1));

      // Recalculate column totals
      calculateColumnTotals();
      calculateGrandTotal();
    }

    function calculateColumnTotals() {
      $('.col-total').each(function() {
        const divisi = $(this).data('divisi');
        const kategori = $(this).data('kategori');
        let colTotal = 0;

        $(`input[data-divisi="${divisi}"][data-kategori="${kategori}"]`).each(function() {
          const value = parseFloat($(this).val()) || 0;
          colTotal += value;
        });

        $(this).text(colTotal.toFixed(1));
      });
    }

    function calculateGrandTotal() {
      let grandTotal = 0;
      $('.row-total').each(function() {
        const value = parseFloat($(this).text()) || 0;
        grandTotal += value;
      });

      $('#grandTotal').text(grandTotal.toFixed(1));
    }

    function collectData() {
      bahanData = [];

      $('.quantity-input').each(function() {
        const value = parseFloat($(this).val()) || 0;

        if (value > 0) {
          bahanData.push({
            id_bahan: $(this).data('bahan'),
            id_divisi: $(this).data('divisi'),
            id_shift_kategori: $(this).data('kategori'),
            jumlah_kebutuhan: value,
            satuan: 'unit', // Default satuan
            keterangan: null
          });
        }
      });

      return bahanData;
    }

    function saveData() {
      const data = collectData();

      if (data.length === 0) {
        Swal.fire('Peringatan', 'Tidak ada data yang diinput. Masukkan minimal satu bahan.', 'warning');
        return;
      }

      // Show loading modal
      $('#loadingModal').modal('show');

      $.ajax({
        url: base_url + 'shift-bahan/save_data',
        type: 'POST',
        data: {
          tanggal_shift: $('#tanggal_shift').val(),
          shift_type: $('#shift_type').val(),
          bahan_data: data
        },
        dataType: 'json',
        success: function(response) {
          $('#loadingModal').modal('hide');

          if (response.status === 'success') {
            Swal.fire({
              title: 'Berhasil!',
              text: response.message,
              icon: 'success',
              confirmButtonText: 'OK'
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = base_url + 'shift-bahan';
              }
            });
          } else {
            Swal.fire('Error!', response.message, 'error');
          }
        },
        error: function(xhr, status, error) {
          $('#loadingModal').modal('hide');
          Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data: ' + error, 'error');
        }
      });
    }

    function saveDraft() {
      const data = collectData();
      if (data.length > 0) {
        // Save as draft silently
        $.ajax({
          url: base_url + 'shift-bahan/save_data',
          type: 'POST',
          data: {
            tanggal_shift: $('#tanggal_shift').val(),
            shift_type: $('#shift_type').val(),
            bahan_data: data
          },
          dataType: 'json',
          success: function(response) {
            if (response.status === 'success') {
              // Show subtle notification
              $('body').append('<div class="toast-container position-fixed bottom-0 end-0 p-3"><div class="toast" role="alert"><div class="toast-body">Draft disimpan otomatis</div></div></div>');
              $('.toast').toast('show');
              setTimeout(() => $('.toast-container').remove(), 3000);
            }
          }
        });
      }
    }

    function loadTemplate() {
      $.ajax({
        url: base_url + 'shift-bahan/load_template',
        type: 'GET',
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success' && response.data.length > 0) {
            // Clear current data
            $('.quantity-input').val('');

            // Load template data
            response.data.forEach(function(item) {
              const input = $(`input[data-bahan="${item.id_bahan}"][data-divisi="${item.id_divisi}"][data-kategori="${item.id_shift_kategori}"]`);
              if (input.length) {
                input.val(item.jumlah_default);
                calculateRowTotal(input[0]);
              }
            });

            Swal.fire('Berhasil!', 'Template berhasil dimuat', 'success');
          } else {
            Swal.fire('Info', 'Tidak ada template yang tersedia', 'info');
          }
        },
        error: function() {
          Swal.fire('Error!', 'Gagal memuat template', 'error');
        }
      });
    }

    function clearAll() {
      Swal.fire({
        title: 'Konfirmasi',
        text: 'Apakah Anda yakin ingin menghapus semua data input?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $('.quantity-input').val('');
          $('.row-total').text('0');
          $('.col-total').text('0');
          $('#grandTotal').text('0');
          Swal.fire('Terhapus!', 'Semua data input telah dihapus', 'success');
        }
      });
    }

    function loadExistingData() {
      // Load existing data into form
      <?php if ($existing_data) : ?>
        const existingData = <?= json_encode($existing_data['details']) ?>;

        existingData.forEach(function(item) {
          const input = $(`input[data-bahan="${item.id_bahan}"][data-divisi="${item.id_divisi}"][data-kategori="${item.id_shift_kategori}"]`);
          if (input.length) {
            input.val(item.jumlah_kebutuhan);
            calculateRowTotal(input[0]);
          }
        });
      <?php endif; ?>
    }

    // Update shift type in table header
    $('#shift_type').change(function() {
      const shiftType = $(this).val().toUpperCase();
      $('.card-header h5').html(`<i class="fas fa-table me-2"></i>LIST RINCIAN BAHAN BAKU (SHIFT ${shiftType})`);
    });
  </script>

</body>

</html>