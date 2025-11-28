<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>

  <!-- PASTIKAN BOOTSTRAP 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <!-- Hapus Select2 karena tidak digunakan -->
  <!-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> -->

  <style>
    .customer-header {
      background-color: #007bff !important;
      color: white;
      font-weight: bold;
      text-align: center;
    }

    .kantin-header {
      background-color: #6c757d !important;
      color: white;
      font-weight: bold;
    }

    /* Styling Label Form */
    .form-label {
      font-weight: 500;
      margin-bottom: 0.5rem;
      color: #495057;
      font-size: 14px;
    }

    /* Styling untuk alert data tidak ditemukan */
    .alert-warning {
      border-left: 4px solid #ffc107;
    }

    .alert-warning .alert-link {
      color: #856404;
      font-weight: bold;
      text-decoration: underline;
    }

    .alert-warning .alert-link:hover {
      color: #533f03;
    }

    /* Styling Dropdown Kantin */
    .dropdown-menu {
      border: 1px solid #ced4da;
      border-radius: 0.25rem;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .dropdown-menu .form-check {
      margin-bottom: 8px;
      padding-left: 1.5rem;
    }

    .dropdown-menu .form-check:last-child {
      margin-bottom: 0;
    }

    .dropdown-menu .form-check-input {
      cursor: pointer;
      margin-top: 0.3rem;
    }

    .dropdown-menu .form-check-label {
      cursor: pointer;
      user-select: none;
      font-size: 14px;
      width: 100%;
    }

    .dropdown-menu .form-check:hover {
      background-color: #f8f9fa;
      border-radius: 4px;
    }

    /* Styling button dropdown - PERBAIKAN WARNA */
    #kantin-dropdown {
      height: 38px;
      padding: 0.375rem 0.75rem;
      font-size: 14px;
      border-color: #ced4da;
      background-color: #ffffff !important;
      color: #495057 !important;
    }

    #kantin-dropdown:hover {
      background-color: #f8f9fa !important;
      border-color: #adb5bd;
    }

    #kantin-dropdown:focus,
    #kantin-dropdown:active {
      background-color: #ffffff !important;
      border-color: #80bdff;
      box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      color: #495057 !important;
    }

    /* Text dalam button */
    #kantin-selected-count {
      color: #6c757d;
      flex: 1;
      text-align: left;
    }

    #kantin-dropdown.has-selection #kantin-selected-count {
      color: #495057;
      font-weight: 500;
    }

    /* Icon chevron */
    #kantin-dropdown .mdi-chevron-down {
      font-size: 18px;
      color: #6c757d;
      margin-left: auto;
    }

    /* Override Bootstrap default */
    .btn-outline-secondary {
      color: #495057 !important;
      background-color: #ffffff !important;
      border-color: #ced4da !important;
    }

    .btn-outline-secondary:hover {
      color: #495057 !important;
      background-color: #f8f9fa !important;
      border-color: #adb5bd !important;
    }

    .btn-outline-secondary:focus,
    .btn-outline-secondary:active,
    .btn-outline-secondary.active,
    .btn-outline-secondary.show {
      color: #495057 !important;
      background-color: #ffffff !important;
      border-color: #80bdff !important;
    }

    /* Scrollbar custom untuk dropdown */
    .dropdown-menu::-webkit-scrollbar {
      width: 8px;
    }

    .dropdown-menu::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb {
      background: #888;
      border-radius: 10px;
    }

    .dropdown-menu::-webkit-scrollbar-thumb:hover {
      background: #555;
    }

    /* Mencegah dropdown tertutup saat klik checkbox */
    .dropdown-menu {
      cursor: default;
    }

    #selectAllKantin,
    #deselectAllKantin {
      text-decoration: none;
      font-weight: 500;
    }

    #selectAllKantin:hover,
    #deselectAllKantin:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body data-sidebar="dark" data-layout="vertical">
  <div id="layout-wrapper">
    <?php
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    ?>

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">

          <!-- Page Title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-0 font-size-18">Report Daily Menu</h4>
                  <p class="mb-0 text-muted">Laporan menu harian per kantin</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item active">Report Daily Menu</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Filter Form -->
          <div class="card mb-3">
            <div class="card-body">
              <form method="get" class="mb-3">
                <div class="row g-2">
                  <!-- Filter Tanggal -->
                  <div class="col-md-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" value="<?= htmlspecialchars($this->input->get('tanggal')) ?>">
                  </div>

                  <!-- Filter Customer -->
                  <div class="col-md-3">
                    <label for="customer-select" class="form-label">Customer</label>
                    <select name="id_customer" class="form-control" id="customer-select">
                      <option value="">- Pilih Customer -</option>
                      <?php foreach ($customerList as $c) : ?>
                        <option value="<?= $c['id_customer'] ?>" <?= $this->input->get('id_customer') == $c['id_customer'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($c['nama_customer']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <!-- Filter Kantin (Dropdown Checkbox) -->
                  <div class="col-md-3">
                    <div class="form-group mb-0">
                      <label for="kantin-dropdown" class="form-label">Kantin <span class="text-danger">*</span></label>
                      <div class="dropdown w-100">
                        <!-- GANTI CLASS dari btn-outline-secondary ke btn-kantin-dropdown -->
                        <button class="btn btn-kantin-dropdown dropdown-toggle w-100 text-start d-flex justify-content-between align-items-center" type="button" id="kantin-dropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                          <span id="kantin-selected-count">- Pilih Kantin -</span>
                          <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu w-100 p-3" aria-labelledby="kantin-dropdown" style="max-height: 300px; overflow-y: auto;">
                          <!-- Header: Select/Deselect All -->
                          <div class="mb-2 pb-2 border-bottom">
                            <a href="#" id="selectAllKantin" class="text-primary me-2" style="font-size: 12px;">Pilih Semua</a> |
                            <a href="#" id="deselectAllKantin" class="text-danger ms-2" style="font-size: 12px;">Hapus Semua</a>
                          </div>

                          <!-- Checkbox List -->
                          <?php
                          // Ambil semua kantin untuk checkbox
                          $allKantins = $this->db->select('nama_kantin, id_kantin')
                            ->from('kantin')
                            ->order_by('nama_kantin', 'ASC')
                            ->get()
                            ->result_array();
                          $selectedKantins = $this->input->get('id_kantin') ? $this->input->get('id_kantin') : [];

                          if (count($allKantins) > 0) :
                            foreach ($allKantins as $k) :
                              $isChecked = in_array($k['nama_kantin'], $selectedKantins) ? 'checked' : '';
                          ?>
                              <div class="form-check">
                                <input class="form-check-input kantin-checkbox" type="checkbox" name="id_kantin[]" value="<?= htmlspecialchars($k['nama_kantin']) ?>" id="kantin_<?= $k['id_kantin'] ?>" <?= $isChecked ?>>
                                <label class="form-check-label" for="kantin_<?= $k['id_kantin'] ?>">
                                  <?= htmlspecialchars($k['nama_kantin']) ?>
                                </label>
                              </div>
                            <?php
                            endforeach;
                          else :
                            ?>
                            <small class="text-muted">Tidak ada kantin tersedia</small>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Filter Shift -->
                  <div class="col-md-3">
                    <label for="shift-select" class="form-label">Shift</label>
                    <select name="shift" id="shift-select" class="form-control">
                      <option value="">- Pilih Shift -</option>
                      <option value="lunch" <?= $this->input->get('shift') == 'lunch' ? 'selected' : '' ?>>Lunch</option>
                      <option value="dinner" <?= $this->input->get('shift') == 'dinner' ? 'selected' : '' ?>>Dinner</option>
                      <option value="supper" <?= $this->input->get('shift') == 'supper' ? 'selected' : '' ?>>Supper</option>
                    </select>
                  </div>

                  <!-- Tombol Aksi -->
                  <div class="col-md-12 mt-3">
                    <button type="submit" class="btn btn-primary">
                      <i class="mdi mdi-filter-outline me-1"></i> Filter
                    </button>
                    <a href="<?= base_url('menu-harian-report') ?>" class="btn btn-secondary">
                      <i class="mdi mdi-refresh me-1"></i> Reset
                    </a>
                    <a href="<?= base_url('menu-harian-report/generate_pdf?' . http_build_query($_GET)) ?>" class="btn btn-danger" target="_blank">
                      <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                    <!-- TOMBOL PRINT BARU -->
                    <button type="button" class="btn btn-info" onclick="printReport()">
                      <i class="fas fa-print me-1"></i> Print Laporan
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Main Content - Accordion by Customer -->
          <div class="row">
            <div class="col-12">
              <?php if (count($groupedByCustomer) > 0) : ?>
                <div class="card">
                  <div class="card-header">
                    <div class="row">
                      <div class="col-lg-6">
                        <h6 class="mb-0">
                          Report:
                          <?php if ($this->input->get('tanggal')) : ?>
                            <?= date('d M Y', strtotime($this->input->get('tanggal'))) ?>
                          <?php else : ?>
                            Semua Tanggal
                          <?php endif; ?>
                          <?php if ($this->input->get('shift')) : ?>
                            - Shift: <?= strtoupper($this->input->get('shift')) ?>
                          <?php endif; ?>
                        </h6>
                      </div>
                      <div class="col-lg-6">
                        <div class="custom-control custom-switch float-end">
                          <input type="checkbox" class="custom-control-input" id="customSwitch1">
                          <label class="custom-control-label" for="customSwitch1">Show Collapsed</label>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="card-body">
                    <div class="accordion" id="accordionCustomer">
                      <?php
                      $no = 1;
                      foreach ($groupedByCustomer as $customerId => $customerData) :
                      ?>
                        <div class="accordion-item mb-2">
                          <h2 class="accordion-header" id="heading<?= $customerId ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $customerId ?>" aria-expanded="false" aria-controls="collapse<?= $customerId ?>">
                              <strong><?= $no++ ?>. <?= htmlspecialchars($customerData['customer_name']) ?></strong>
                              <span class="badge bg-primary ms-2"><?= count($customerData['kantins']) ?> Kantin</span>
                            </button>
                          </h2>
                          <div id="collapse<?= $customerId ?>" class="accordion-collapse collapse xcollapse" aria-labelledby="heading<?= $customerId ?>" data-bs-parent="#accordionCustomer">
                            <div class="accordion-body">
                              <div class="table-responsive">
                                <table class="table table-bordered table-sm table-hover">
                                  <thead class="table-primary">
                                    <tr>
                                      <th>No</th>
                                      <th>Menu Kondimen</th>
                                      <th>Kategori</th>
                                      <?php foreach ($customerData['kantins'] as $kantin) : ?>
                                        <th><?= htmlspecialchars($kantin) ?></th>
                                      <?php endforeach; ?>
                                      <th>Total</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    <?php
                                    $menuNo = 1;
                                    foreach ($customerData['menu_data'] as $menu) :
                                    ?>
                                      <tr>
                                        <td><?= $menuNo++ ?></td>
                                        <td><?= htmlspecialchars($menu['menu_kondimen']) ?></td>
                                        <td><?= htmlspecialchars($menu['kategori']) ?></td>
                                        <?php foreach ($customerData['kantins'] as $kantin) : ?>
                                          <td class="text-center">
                                            <?= isset($menu['qty_per_kantin'][$kantin]) ? $menu['qty_per_kantin'][$kantin] : 0 ?>
                                          </td>
                                        <?php endforeach; ?>
                                        <td class="text-center"><strong><?= $menu['total'] ?></strong></td>
                                      </tr>
                                    <?php endforeach; ?>
                                  </tbody>
                                  <tfoot class="table-dark">
                                    <tr>
                                      <th colspan="3">Total</th>
                                      <?php
                                      foreach ($customerData['kantins'] as $kantin) {
                                        $totalPerKantin = 0;
                                        foreach ($customerData['menu_data'] as $menu) {
                                          if (isset($menu['qty_per_kantin'][$kantin])) {
                                            $totalPerKantin += $menu['qty_per_kantin'][$kantin];
                                          }
                                        }
                                        echo "<th class='text-center'>$totalPerKantin</th>";
                                      }

                                      // Grand total
                                      $grandTotal = 0;
                                      foreach ($customerData['menu_data'] as $menu) {
                                        $grandTotal += $menu['total'];
                                      }
                                      echo "<th class='text-center'>$grandTotal</th>";
                                      ?>
                                    </tr>
                                  </tfoot>
                                </table>
                              </div>
                            </div>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
              <?php else : ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                  <i class="mdi mdi-alert-outline me-2"></i>
                  <strong>Data Tidak Ditemukan!</strong>
                  <br>
                  Tidak ada data menu harian yang sesuai dengan filter.
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <!-- JS Bootstrap & DataTables -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>

  <!-- PASTIKAN BOOTSTRAP 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>

  <!-- Hapus Select2 karena tidak digunakan -->
  <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->

  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>

  <script>
    $(document).ready(function() {
      // Inisialisasi DataTable
      $('#report-daily-menu-table').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true
      });

      // Inisialisasi Bootstrap Dropdown secara manual
      var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
      var dropdownList = dropdownElementList.map(function(dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl, {
          autoClose: 'outside'
        })
      });

      // Mencegah dropdown tertutup saat klik di dalam dropdown
      $('.dropdown-menu').on('click', function(e) {
        e.stopPropagation();
      });

      // Cegah form submit saat enter di dalam dropdown
      $('.dropdown-menu').on('keydown', function(e) {
        if (e.keyCode === 13) {
          e.preventDefault();
        }
      });

      // Update text button dropdown saat checkbox berubah
      function updateKantinDropdownText() {
        const checkedCount = $('.kantin-checkbox:checked').length;
        const totalCount = $('.kantin-checkbox').length;
        const dropdown = $('#kantin-dropdown');
        const textSpan = $('#kantin-selected-count');

        if (checkedCount === 0) {
          textSpan.text('- Pilih Kantin -');
          dropdown.removeClass('has-selection');
        } else if (checkedCount === totalCount) {
          textSpan.text(`Semua Kantin (${checkedCount})`);
          dropdown.addClass('has-selection');
        } else {
          textSpan.text(`${checkedCount} Kantin Dipilih`);
          dropdown.addClass('has-selection');
        }
      }

      // Event saat checkbox kantin berubah
      $('.kantin-checkbox').on('change', function() {
        updateKantinDropdownText();
      });

      // Select All Kantin
      $('#selectAllKantin').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.kantin-checkbox').prop('checked', true);
        updateKantinDropdownText();
      });

      // Deselect All Kantin
      $('#deselectAllKantin').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.kantin-checkbox').prop('checked', false);
        updateKantinDropdownText();
      });

      // Update text saat halaman dimuat
      updateKantinDropdownText();

      // Auto scroll ke alert jika ada data tidak ditemukan
      <?php if (empty($pivot) && ($this->input->get('tanggal') || $this->input->get('id_customer') || $this->input->get('id_kantin') || $this->input->get('shift'))) : ?>
        if ($('.alert-warning').length > 0) {
          $('html, body').animate({
            scrollTop: $('.alert-warning').offset().top - 100
          }, 500);
        }
      <?php endif; ?>
    });
  </script>

  <script>
    function printReport() {
      // Ambil data yang sudah difilter
      var tanggal = $('#tanggal').val();
      var shift = $('#shift-select').val();
      var customer = $('#customer-select option:selected').text();
      var customerId = $('#customer-select').val();

      // Validasi minimal filter
      if (!tanggal) {
        Swal.fire({
          icon: 'warning',
          title: 'Perhatian!',
          text: 'Silakan pilih tanggal terlebih dahulu',
          confirmButtonText: 'OK'
        });
        return;
      }

      // Ambil kantin yang dipilih
      var selectedKantins = [];
      $('.kantin-checkbox:checked').each(function() {
        selectedKantins.push($(this).val());
      });

      if (selectedKantins.length === 0) {
        Swal.fire({
          icon: 'warning',
          title: 'Perhatian!',
          text: 'Silakan pilih minimal 1 kantin',
          confirmButtonText: 'OK'
        });
        return;
      }

      // Generate print window
      var printWindow = window.open('', '_blank');
      var printContent = generatePrintHTML();

      printWindow.document.write(printContent);
      printWindow.document.close();
      printWindow.focus();

      setTimeout(function() {
        printWindow.print();
      }, 500);
    }

    function generatePrintHTML() {
      var tanggal = $('#tanggal').val();
      var shift = $('#shift-select').val();
      var customer = $('#customer-select option:selected').text();

      var html = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Menu Harian - Ryan Catering</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .header .company {
            font-size: 11px;
            color: #555;
            margin-bottom: 5px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 9px;
        }
        
        .info-table td {
            padding: 2px 5px;
        }
        
        .info-table td:first-child {
            width: 120px;
            font-weight: bold;
        }
        
        .customer-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        
        .customer-title {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8px;
        }
        
        table.data-table th {
            background: #4a4a4a;
            color: white;
            padding: 4px 3px;
            border: 1px solid #333;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }
        
        table.data-table td {
            padding: 3px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        table.data-table td:nth-child(2) {
            text-align: left;
            padding-left: 5px;
        }
        
        table.data-table td:nth-child(3) {
            text-align: left;
        }
        
        table.data-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        table.data-table tbody tr:hover {
            background: #f0f0f0;
        }
        
        table.data-table tfoot {
            background: #333;
            color: white;
            font-weight: bold;
        }
        
        table.data-table tfoot td {
            border-color: #333;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .badge-primary { background: #007bff; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        
        .total-row {
            background: #fffacd !important;
            font-weight: bold;
        }
        
        @media print {
            body { margin: 0; }
            .customer-section { page-break-inside: avoid; }
            @page { margin: 8mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Menu Harian</h2>
        <div class="company">Ryan Catering</div>
    </div>
    
    <table class="info-table">
        <tr>
            <td>Tanggal Cetak:</td>
            <td>${formatDateIndo(new Date())}</td>
            <td>Tanggal:</td>
            <td>${formatDateIndo(tanggal)}</td>
        </tr>
        <tr>
            <td>Customer:</td>
            <td>${customer || 'Semua Customer'}</td>
            <td>Shift:</td>
            <td>${shift ? shift.toUpperCase() : 'Semua Shift'}</td>
        </tr>
    </table>
`;

      // Loop untuk setiap customer di accordion
      $('.accordion-item').each(function() {
        var customerName = $(this).find('.accordion-button strong').text().trim();
        var table = $(this).find('table');

        html += `
    <div class="customer-section">
        <div class="customer-title">${customerName}</div>
        <table class="data-table">
            <thead>
                <tr>
`;

        // Copy header
        table.find('thead th').each(function() {
          var thText = $(this).text().trim();
          html += `<th>${thText}</th>`;
        });

        html += `
                </tr>
            </thead>
            <tbody>
`;

        // Copy body
        table.find('tbody tr').each(function() {
          html += '<tr>';
          $(this).find('td').each(function(index) {
            var tdText = $(this).text().trim();
            if (index === 2) { // Kolom kategori
              var kategori = tdText;
              html += `<td>${getKategoriBadgeHTML(kategori)}</td>`;
            } else {
              html += `<td>${tdText}</td>`;
            }
          });
          html += '</tr>';
        });

        html += `
            </tbody>
            <tfoot>
                <tr>
`;

        // Copy footer
        table.find('tfoot th').each(function() {
          var thText = $(this).text().trim();
          html += `<td>${thText}</td>`;
        });

        html += `
                </tr>
            </tfoot>
        </table>
    </div>
`;
      });

      html += `
</body>
</html>
`;

      return html;
    }

    function getKategoriBadgeHTML(kategori) {
      var badgeClass = 'badge badge-secondary';
      var kategoriLower = kategori.toLowerCase();

      if (kategoriLower.includes('lauk utama')) {
        badgeClass = 'badge badge-primary';
      } else if (kategoriLower.includes('pendamping kering')) {
        badgeClass = 'badge badge-warning';
      } else if (kategoriLower.includes('pendamping basah')) {
        badgeClass = 'badge badge-info';
      } else if (kategoriLower.includes('sayur')) {
        badgeClass = 'badge badge-success';
      } else if (kategoriLower.includes('buah')) {
        badgeClass = 'badge badge-danger';
      } else if (kategoriLower.includes('nasi')) {
        badgeClass = 'badge badge-secondary';
      }

      return `<span class="${badgeClass}">${kategori}</span>`;
    }

    function formatDateIndo(date) {
      if (typeof date === 'string') {
        var parts = date.split('-');
        if (parts.length === 3) {
          date = new Date(parts[0], parts[1] - 1, parts[2]);
        }
      }

      var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
      ];

      var d = date.getDate();
      var m = date.getMonth();
      var y = date.getFullYear();

      return d + ' ' + bulan[m] + ' ' + y;
    }
  </script>
</body>

</html>