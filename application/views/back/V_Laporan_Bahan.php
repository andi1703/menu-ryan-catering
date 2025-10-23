<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->helper('url');
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>

  <!-- DataTables -->
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

  <!-- <style>
    .table thead th {
      background-color: #f8f9fa;
      color: #212529;
      font-weight: 600;
      border-bottom: 2px solid #dee2e6;
    }

    .summary-card {
      border-left: 4px solid #007bff;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-export {
      margin-left: 5px;
    }

    .select2-container--default .select2-selection--multiple {
      border: 1px solid #ced4da;
      border-radius: 0.375rem;
    }

    .input-form-table {
      font-size: 14px;
    }

    .input-form-table th {
      background-color: #f8f9fa;
      font-weight: 600;
      text-align: center;
      vertical-align: middle;
    }

    .input-form-table td {
      vertical-align: middle;
    }

    .bahan-select {
      min-width: 200px;
    }

    .form-control:focus {
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .card-header {
      border-bottom: 1px solid #e3ebf0;
    }

    .required {
      color: #dc3545;
    }
  </style> -->
</head>

<body data-sidebar="dark">
  <div id="layout-wrapper">
    <?php
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    $this->load->view('back_partial/head-css');
    ?>

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">
          <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
              <i class="fas fa-chart-bar"></i> <?= $title ?>
            </h1>
          </div>

          <!-- Form Input Manual Bahan -->
          <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-plus-circle"></i> Input Manual Bahan Baku
              </h6>
              <button type="button" class="btn btn-sm btn-success" id="toggleInputForm">
                <i class="fas fa-chevron-down" id="toggleIcon"></i> Tampilkan Form
              </button>
            </div>
            <div class="card-body" id="inputFormBody" style="display: none;">
              <form id="manualInputForm">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="input_tanggal">Tanggal Kebutuhan <span class="text-danger">*</span></label>
                      <input type="date" class="form-control" id="input_tanggal" name="input_tanggal" value="<?= date('Y-m-d') ?>" required>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="input_shift">Shift <span class="text-danger">*</span></label>
                      <select class="form-control" id="input_shift" name="input_shift" required>
                        <option value="">Pilih Shift</option>
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                      </select>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <label for="input_keterangan">Keterangan</label>
                      <input type="text" class="form-control" id="input_keterangan" name="input_keterangan" placeholder="Contoh: Event Khusus">
                    </div>
                  </div>
                </div>

                <!-- Tabel Input Bahan -->
                <div class="table-responsive">
                  <table class="table table-bordered" id="bahanInputTable">
                    <thead class="thead-light">
                      <tr>
                        <th width="5%">No</th>
                        <th width="30%">Nama Bahan <span class="text-danger">*</span></th>
                        <th width="15%">Jumlah <span class="text-danger">*</span></th>
                        <th width="15%">Satuan</th>
                        <th width="15%">Harga per Unit</th>
                        <th width="15%">Total Harga</th>
                        <th width="5%">Aksi</th>
                      </tr>
                    </thead>
                    <tbody id="bahanInputBody">
                      <tr id="row_1">
                        <td class="text-center">1</td>
                        <td>
                          <select class="form-control bahan-select" name="bahan_id[]" data-row="1" required>
                            <option value="">Pilih Bahan</option>
                            <?php if (isset($bahan_list)) : ?>
                              <?php foreach ($bahan_list as $bahan) : ?>
                                <option value="<?= $bahan['id_bahan'] ?>" data-satuan="<?= $bahan['nama_satuan'] ?>" data-harga="<?= $bahan['harga_sekarang'] ?>">
                                  <?= $bahan['nama_bahan'] ?>
                                </option>
                              <?php endforeach; ?>
                            <?php endif; ?>
                          </select>
                        </td>
                        <td>
                          <input type="number" class="form-control jumlah-input" name="jumlah[]" data-row="1" min="0.1" step="0.1" required>
                        </td>
                        <td>
                          <input type="text" class="form-control satuan-display" name="satuan[]" readonly>
                        </td>
                        <td>
                          <input type="number" class="form-control harga-input" name="harga_per_unit[]" data-row="1" min="0" step="0.01">
                        </td>
                        <td>
                          <input type="number" class="form-control total-harga" name="total_harga[]" readonly>
                        </td>
                        <td class="text-center">
                          <button type="button" class="btn btn-sm btn-danger remove-row" onclick="removeRow(1)">
                            <i class="fas fa-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                    <tfoot>
                      <tr class="table-info">
                        <td colspan="5" class="text-right font-weight-bold">TOTAL KESELURUHAN:</td>
                        <td><strong id="grandTotal">Rp 0</strong></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-primary" id="addRowBtn">
                            <i class="fas fa-plus"></i>
                          </button>
                        </td>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="row mt-3">
                  <div class="col-12">
                    <button type="submit" class="btn btn-success">
                      <i class="fas fa-save"></i> Simpan Data Bahan
                    </button>
                    <button type="button" class="btn btn-secondary" id="resetFormBtn">
                      <i class="fas fa-undo"></i> Reset Form
                    </button>
                    <button type="button" class="btn btn-info" id="loadTemplateBtn">
                      <i class="fas fa-file-import"></i> Load Template
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Filter Card -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> Filter Laporan
              </h6>
            </div>
            <div class="card-body">
              <form id="filterForm">
                <div class="row">
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="tanggal_mulai">Tanggal Mulai</label>
                      <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="tanggal_selesai">Tanggal Selesai</label>
                      <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" required>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="menu_ids">Pilih Menu (Opsional)</label>
                      <select class="form-control" id="menu_ids" name="menu_ids[]" multiple>
                        <?php foreach ($menus as $menu) : ?>
                          <option value="<?= $menu['id_menu'] ?>"><?= $menu['nama_menu'] ?></option>
                        <?php endforeach; ?>
                      </select>
                      <small class="text-muted">Kosongkan untuk semua menu</small>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="form-group">
                      <label for="porsi_total">Total Porsi</label>
                      <input type="number" class="form-control" id="porsi_total" name="porsi_total" value="1" min="1" required>
                      <small class="text-muted">Kalikan kebutuhan bahan dengan porsi ini</small>
                    </div>
                  </div>
                </div>
                <div class="row">
                  <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-search"></i> Generate Laporan
                    </button>
                    <button type="button" class="btn btn-success" id="exportExcelBtn" style="display: none;">
                      <i class="fas fa-file-excel"></i> Export Excel
                    </button>
                    <button type="button" class="btn btn-danger" id="exportPdfBtn" style="display: none;">
                      <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Summary Cards -->
          <div id="summaryCards" style="display: none;">
            <div class="row">
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                          Total Bahan
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBahan">0</div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-boxes fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                          Total Menu
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalMenu">0</div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-utensils fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                          Total Porsi
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalPorsi">0</div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-calculator fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-warning shadow h-100 py-2">
                  <div class="card-body">
                    <div class="row no-gutters align-items-center">
                      <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                          Total Biaya
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalBiaya">Rp 0</div>
                      </div>
                      <div class="col-auto">
                        <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Report Table -->
          <div class="card shadow mb-4" id="reportCard" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-table"></i> Kebutuhan Bahan Baku
              </h6>
              <div class="float-right">
                <small class="text-muted" id="reportPeriode"></small>
              </div>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover" id="laporanTable" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr>
                      <th width="5%">No</th>
                      <th width="30%">Nama Bahan</th>
                      <th width="15%">Satuan</th>
                      <th width="15%">Kebutuhan Total</th>
                      <th width="15%">Harga Satuan</th>
                      <th width="20%">Total Biaya</th>
                    </tr>
                  </thead>
                  <tbody id="laporanTableBody">
                    <!-- Data akan diisi via JavaScript -->
                  </tbody>
                  <tfoot>
                    <tr class="font-weight-bold bg-light">
                      <td colspan="5" class="text-right">TOTAL KESELURUHAN:</td>
                      <td id="grandTotal">Rp 0</td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>

          <!-- Summary per Menu -->
          <div class="card shadow mb-4" id="summaryMenuCard" style="display: none;">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-success">
                <i class="fas fa-chart-pie"></i> Ringkasan per Menu
              </h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-hover" id="summaryMenuTable" width="100%" cellspacing="0">
                  <thead class="thead-light">
                    <tr>
                      <th width="5%">No</th>
                      <th width="40%">Nama Menu</th>
                      <th width="15%">Porsi</th>
                      <th width="20%">Total Bahan</th>
                      <th width="20%">Total Biaya Bahan</th>
                    </tr>
                  </thead>
                  <tbody id="summaryMenuTableBody">
                    <!-- Data akan diisi via JavaScript -->
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Loading Modal -->
        <div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
          <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
              <div class="modal-body text-center">
                <div class="spinner-border text-primary" role="status">
                  <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-3">Sedang memproses laporan...</p>
              </div>
            </div>
          </div>
        </div>

        <script>
          $(document).ready(function() {
            // Set default date (last 30 days)
            const today = new Date();
            const thirtyDaysAgo = new Date(today.getTime() - (30 * 24 * 60 * 60 * 1000));

            $('#tanggal_selesai').val(today.toISOString().split('T')[0]);
            $('#tanggal_mulai').val(thirtyDaysAgo.toISOString().split('T')[0]);

            // Initialize select2 for menu selection
            $('#menu_ids').select2({
              placeholder: 'Pilih menu (kosongkan untuk semua)',
              allowClear: true,
              width: '100%'
            });

            // Form submit handler
            $('#filterForm').on('submit', function(e) {
              e.preventDefault();
              generateLaporan();
            });

            // Export button handlers
            $('#exportExcelBtn').on('click', function() {
              exportToExcel();
            });

            $('#exportPdfBtn').on('click', function() {
              exportToPdf();
            });

            function generateLaporan() {
              const formData = {
                tanggal_mulai: $('#tanggal_mulai').val(),
                tanggal_selesai: $('#tanggal_selesai').val(),
                menu_ids: $('#menu_ids').val() || [],
                porsi_total: $('#porsi_total').val()
              };

              // Show loading
              $('#loadingModal').modal('show');

              // Get main report data
              $.ajax({
                url: '<?= base_url('Back_Laporan_Bahan/get_laporan') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                  if (response.status === 'success') {
                    populateMainReport(response.data, response.summary);
                    generateSummaryPerMenu(formData);
                  } else {
                    Swal.fire('Error', response.message, 'error');
                    $('#loadingModal').modal('hide');
                  }
                },
                error: function(xhr, status, error) {
                  console.error('Error:', error);
                  Swal.fire('Error', 'Terjadi kesalahan saat memuat laporan', 'error');
                  $('#loadingModal').modal('hide');
                }
              });
            }

            function populateMainReport(data, summary) {
              // Update summary cards
              $('#totalBahan').text(summary.total_bahan);
              $('#totalMenu').text(summary.total_menu);
              $('#totalPorsi').text(summary.porsi);
              $('#reportPeriode').text('Periode: ' + summary.periode);

              // Calculate and display total biaya
              let grandTotal = 0;
              let tableHtml = '';

              data.forEach(function(item, index) {
                const totalBiaya = parseFloat(item.total_biaya) || 0;
                grandTotal += totalBiaya;

                tableHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama_bahan}</td>
                    <td>${item.nama_satuan}</td>
                    <td>${parseFloat(item.total_kebutuhan).toLocaleString('id-ID')}</td>
                    <td>Rp ${parseInt(item.harga_sekarang).toLocaleString('id-ID')}</td>
                    <td>Rp ${totalBiaya.toLocaleString('id-ID')}</td>
                </tr>
            `;
              });

              $('#laporanTableBody').html(tableHtml);
              $('#grandTotal').text('Rp ' + grandTotal.toLocaleString('id-ID'));
              $('#totalBiaya').text('Rp ' + grandTotal.toLocaleString('id-ID'));

              // Show cards and tables
              $('#summaryCards').show();
              $('#reportCard').show();
              $('#exportExcelBtn, #exportPdfBtn').show();

              // Initialize DataTable if not already initialized
              if (!$.fn.DataTable.isDataTable('#laporanTable')) {
                $('#laporanTable').DataTable({
                  responsive: true,
                  pageLength: 25,
                  order: [
                    [1, "asc"]
                  ],
                  language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                  }
                });
              }
            }

            function generateSummaryPerMenu(formData) {
              $.ajax({
                url: '<?= base_url('Back_Laporan_Bahan/get_summary_per_menu') ?>',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                  if (response.status === 'success') {
                    populateSummaryMenu(response.data);
                  }
                  $('#loadingModal').modal('hide');
                },
                error: function(xhr, status, error) {
                  console.error('Error:', error);
                  $('#loadingModal').modal('hide');
                }
              });
            }

            function populateSummaryMenu(data) {
              let tableHtml = '';

              data.forEach(function(item, index) {
                const totalBiaya = parseFloat(item.total_biaya_bahan) || 0;

                tableHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nama_menu}</td>
                    <td>${item.porsi} porsi</td>
                    <td>${item.total_bahan} bahan</td>
                    <td>Rp ${totalBiaya.toLocaleString('id-ID')}</td>
                </tr>
            `;
              });

              $('#summaryMenuTableBody').html(tableHtml);
              $('#summaryMenuCard').show();

              // Initialize DataTable for summary
              if (!$.fn.DataTable.isDataTable('#summaryMenuTable')) {
                $('#summaryMenuTable').DataTable({
                  responsive: true,
                  pageLength: 10,
                  order: [
                    [1, "asc"]
                  ],
                  language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                  }
                });
              }
            }

            function exportToExcel() {
              const params = new URLSearchParams({
                tanggal_mulai: $('#tanggal_mulai').val(),
                tanggal_selesai: $('#tanggal_selesai').val(),
                menu_ids: ($('#menu_ids').val() || []).join(','),
                porsi_total: $('#porsi_total').val()
              });

              window.open('<?= base_url('Back_Laporan_Bahan/export_excel') ?>?' + params.toString(), '_blank');
            }

            function exportToPdf() {
              const params = new URLSearchParams({
                tanggal_mulai: $('#tanggal_mulai').val(),
                tanggal_selesai: $('#tanggal_selesai').val(),
                menu_ids: ($('#menu_ids').val() || []).join(','),
                porsi_total: $('#porsi_total').val()
              });

              window.open('<?= base_url('Back_Laporan_Bahan/export_pdf') ?>?' + params.toString(), '_blank');
            }
          });

          <
          /div> < /
          div >
            <?php $this->load->view('back_partial/footer'); ?> <
            /div> < /
          div >

            <
            !--JAVASCRIPT-- >
            <
            script src = "<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>" >
        </script>
        <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>

        <!-- DataTables -->
        <script src="<?php echo base_url('assets_back/libs/dat
  atables.net/js/jquery.dataTables.min.js'); ?>"></script>
        <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>

        <!-- SweetAlert2 -->
        <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>

        <!-- Select2 JS -->
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!-- App js -->
        <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>

        <!-- Manual Input Form JavaScript -->
        <script>
          $(document).ready(function() {
            // ===== FORM INPUT MANUAL FUNCTIONS =====
            let rowCounter = 1;

            // Toggle form visibility
            $('#toggleInputForm').click(function() {
              const body = $('#inputFormBody');
              const icon = $('#toggleIcon');
              const button = $(this);

              if (body.is(':visible')) {
                body.slideUp();
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
                button.html('<i class="fas fa-chevron-down"></i> Tampilkan Form');
              } else {
                body.slideDown();
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
                button.html('<i class="fas fa-chevron-up"></i> Sembunyikan Form');
              }
            });

            // Add new row
            $('#addRowBtn').click(function() {
              rowCounter++;
              const newRow = `
          <tr id="row_${rowCounter}">
            <td class="text-center">${rowCounter}</td>
            <td>
              <select class="form-control bahan-select" name="bahan_id[]" data-row="${rowCounter}" required>
                <option value="">Pilih Bahan</option>
                <?php if (isset($bahan_list)) : ?>
                  <?php foreach ($bahan_list as $bahan) : ?>
                    <option value="<?= $bahan['id_bahan'] ?>" 
                            data-satuan="<?= $bahan['nama_satuan'] ?>" 
                            data-harga="<?= $bahan['harga_sekarang'] ?>">
                      <?= $bahan['nama_bahan'] ?>
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </td>
            <td>
              <input type="number" class="form-control jumlah-input" name="jumlah[]" data-row="${rowCounter}" min="0.1" step="0.1" required>
            </td>
            <td>
              <input type="text" class="form-control satuan-display" name="satuan[]" readonly>
            </td>
            <td>
              <input type="number" class="form-control harga-input" name="harga_per_unit[]" data-row="${rowCounter}" min="0" step="0.01">
            </td>
            <td>
              <input type="number" class="form-control total-harga" name="total_harga[]" readonly>
            </td>
            <td class="text-center">
              <button type="button" class="btn btn-sm btn-danger remove-row" onclick="removeRow(${rowCounter})">
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        `;
              $('#bahanInputBody').append(newRow);
              updateRowNumbers();
            });

            // Update row numbers
            function updateRowNumbers() {
              $('#bahanInputBody tr').each(function(index) {
                $(this).find('td:first').text(index + 1);
              });
            }

            // Handle bahan selection change
            $(document).on('change', '.bahan-select', function() {
              const selected = $(this).find(':selected');
              const row = $(this).closest('tr');

              const satuan = selected.data('satuan') || '';
              const harga = selected.data('harga') || 0;

              row.find('.satuan-display').val(satuan);
              row.find('.harga-input').val(harga);

              calculateRowTotal(row);
            });

            // Handle quantity or price change
            $(document).on('input', '.jumlah-input, .harga-input', function() {
              const row = $(this).closest('tr');
              calculateRowTotal(row);
            });

            // Calculate row total
            function calculateRowTotal(row) {
              const jumlah = parseFloat(row.find('.jumlah-input').val()) || 0;
              const harga = parseFloat(row.find('.harga-input').val()) || 0;
              const total = jumlah * harga;

              row.find('.total-harga').val(total.toFixed(2));
              calculateGrandTotal();
            }

            // Calculate grand total
            function calculateGrandTotal() {
              let grandTotal = 0;
              $('.total-harga').each(function() {
                grandTotal += parseFloat($(this).val()) || 0;
              });
              $('#grandTotal').text('Rp ' + grandTotal.toLocaleString('id-ID'));
            }

            // Global function for removing rows (referenced in onclick)
            window.removeRow = function(rowId) {
              if ($('#bahanInputBody tr').length > 1) {
                $(`#row_${rowId}`).remove();
                updateRowNumbers();
                calculateGrandTotal();
              } else {
                Swal.fire('Peringatan', 'Minimal harus ada satu bahan', 'warning');
              }
            };

            // Reset form
            $('#resetFormBtn').click(function() {
              Swal.fire({
                title: 'Konfirmasi Reset',
                text: 'Apakah Anda yakin ingin mereset semua data input?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Reset!',
                cancelButtonText: 'Batal'
              }).then((result) => {
                if (result.isConfirmed) {
                  $('#manualInputForm')[0].reset();
                  $('#bahanInputBody').html(`
              <tr id="row_1">
                <td class="text-center">1</td>
                <td>
                  <select class="form-control bahan-select" name="bahan_id[]" data-row="1" required>
                    <option value="">Pilih Bahan</option>
                    <?php if (isset($bahan_list)) : ?>
                      <?php foreach ($bahan_list as $bahan) : ?>
                        <option value="<?= $bahan['id_bahan'] ?>" 
                                data-satuan="<?= $bahan['nama_satuan'] ?>" 
                                data-harga="<?= $bahan['harga_sekarang'] ?>">
                          <?= $bahan['nama_bahan'] ?>
                        </option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </td>
                <td>
                  <input type="number" class="form-control jumlah-input" name="jumlah[]" data-row="1" min="0.1" step="0.1" required>
                </td>
                <td>
                  <input type="text" class="form-control satuan-display" name="satuan[]" readonly>
                </td>
                <td>
                  <input type="number" class="form-control harga-input" name="harga_per_unit[]" data-row="1" min="0" step="0.01">
                </td>
                <td>
                  <input type="number" class="form-control total-harga" name="total_harga[]" readonly>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-sm btn-danger remove-row" onclick="removeRow(1)">
                    <i class="fas fa-trash"></i>
                  </button>
                </td>
              </tr>
            `);
                  rowCounter = 1;
                  calculateGrandTotal();
                  Swal.fire('Berhasil!', 'Form telah direset', 'success');
                }
              });
            });

            // Submit manual input form
            $('#manualInputForm').submit(function(e) {
              e.preventDefault();

              // Validate form
              const bahanData = [];
              let isValid = true;

              $('#bahanInputBody tr').each(function() {
                const bahanId = $(this).find('.bahan-select').val();
                const jumlah = $(this).find('.jumlah-input').val();
                const harga = $(this).find('.harga-input').val();

                if (bahanId && jumlah && parseFloat(jumlah) > 0) {
                  bahanData.push({
                    id_bahan: bahanId,
                    jumlah: parseFloat(jumlah),
                    harga_per_unit: parseFloat(harga) || 0,
                    satuan: $(this).find('.satuan-display').val()
                  });
                } else if (bahanId || jumlah) {
                  isValid = false;
                }
              });

              if (!isValid || bahanData.length === 0) {
                Swal.fire('Peringatan', 'Pastikan semua bahan yang dipilih memiliki jumlah yang valid', 'warning');
                return;
              }

              // Show loading
              Swal.fire({
                title: 'Menyimpan Data...',
                allowOutsideClick: false,
                didOpen: () => {
                  Swal.showLoading();
                }
              });

              // Submit data
              $.ajax({
                url: '<?= base_url('laporan-bahan/save_manual_input') ?>',
                type: 'POST',
                data: {
                  tanggal: $('#input_tanggal').val(),
                  shift: $('#input_shift').val(),
                  keterangan: $('#input_keterangan').val(),
                  bahan_data: bahanData
                },
                dataType: 'json',
                success: function(response) {
                  Swal.close();
                  if (response.status === 'success') {
                    Swal.fire('Berhasil!', response.message, 'success').then(() => {
                      $('#resetFormBtn').click(); // Reset form after success
                    });
                  } else {
                    Swal.fire('Error!', response.message, 'error');
                  }
                },
                error: function(xhr, status, error) {
                  Swal.close();
                  Swal.fire('Error!', 'Terjadi kesalahan saat menyimpan data: ' + error, 'error');
                }
              });
            });

            // Load template function
            $('#loadTemplateBtn').click(function() {
              $.ajax({
                url: '<?= base_url('laporan-bahan/get_template') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                  if (response.status === 'success' && response.data.length > 0) {
                    // Clear current data
                    $('#resetFormBtn').click();

                    // Load template data
                    setTimeout(() => {
                      response.data.forEach((item, index) => {
                        if (index > 0) {
                          $('#addRowBtn').click(); // Add new row if needed
                        }

                        const row = $(`#bahanInputBody tr:eq(${index})`);
                        row.find('.bahan-select').val(item.id_bahan).trigger('change');
                        row.find('.jumlah-input').val(item.jumlah_default);
                        setTimeout(() => calculateRowTotal(row), 100);
                      });

                      Swal.fire('Berhasil!', 'Template berhasil dimuat', 'success');
                    }, 500);
                  } else {
                    Swal.fire('Info', 'Tidak ada template yang tersedia', 'info');
                  }
                },
                error: function() {
                  Swal.fire('Error!', 'Gagal memuat template', 'error');
                }
              });
            });

            // Initialize form
            calculateGrandTotal();
          });
        </script>

</body>

</html>