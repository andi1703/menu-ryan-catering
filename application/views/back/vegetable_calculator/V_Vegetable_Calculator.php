<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">

  <style>
    /* =============================================
       MAIN TABLE STYLING
       ============================================= */
    #calc-sessions-table {
      font-size: 0.85rem;
      border-collapse: collapse;
    }

    #calc-sessions-table thead th {
      background-color: #343a40;
      color: white;
      font-weight: 600;
      vertical-align: middle;
      text-align: center;
      border: 1px solid #23282d;
      padding: 10px 6px;
      position: sticky;
      top: 0;
      z-index: 10;
      letter-spacing: 0.3px;
      font-size: 0.8rem;
    }

    #calc-sessions-table tbody td {
      vertical-align: middle;
      border: 1px solid #dee2e6;
      padding: 8px 6px;
    }

    #calc-sessions-table tbody tr:hover {
      background-color: #f8f9fa;
      transition: background-color 0.2s ease;
    }

    /* =============================================
       SHIFT BADGES
       ============================================= */
    .shift-badge {
      display: inline-block;
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: 700;
      font-size: 0.65rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .shift-lunch {
      background-color: #20c997;
      color: white;
    }

    .shift-dinner {
      background-color: #6f42c1;
      color: white;
    }

    .shift-supper {
      background-color: #e83e8c;
      color: white;
    }

    /* =============================================
       ACTION BUTTONS
       ============================================= */
    .btn-action {
      padding: 4px 6px;
      font-size: 0.7rem;
      margin: 0 1px;
      border-radius: 3px;
    }

    .btn-action i {
      font-size: 0.8rem;
    }

    /* =============================================
       RESPONSIVE
       ============================================= */
    @media (max-width: 768px) {
      #calc-sessions-table {
        font-size: 0.7rem;
      }

      .btn-action {
        font-size: 0.65rem;
        padding: 3px 5px;
      }
    }

    /* =============================================
       MODAL TABLE (EXCEL-LIKE) HEADER TO DARK NAVY
       ============================================= */
    #calc_excel_table thead th {
      background-color: #1f2d3d !important;
      /* dark navy */
      color: #ffffff !important;
      border: 1px solid #15202b !important;
      font-weight: 600;
      vertical-align: middle;
      text-align: left;
      padding: 10px 8px;
    }

    /* Nested detail table header */
    #calc_excel_table .nested-table thead th {
      background-color: #1f2d3d !important;
      color: #ffffff !important;
      border: 1px solid #15202b !important;
      font-weight: 600;
    }

    /* =============================================
       UX POLISH: CLEANER LAYOUT + BETTER READABILITY
       ============================================= */
    #calcModal .modal-header {
      border-bottom: 0;
      padding-bottom: 0.25rem;
    }

    #calcModal .modal-body {
      padding-top: 0.25rem;
    }

    /* Filter row spacing */
    #calcModal .form-label {
      font-size: 0.8rem;
      color: #6c757d;
      margin-bottom: 0.25rem;
    }

    /* Info note */
    #calcModal .alert-info {
      background: #f1f8ff;
      border: 1px solid #d6eaff;
      color: #285e8e;
      font-size: 0.85rem;
    }

    /* Main excel-like table */
    #calc_excel_table {
      font-size: 0.9rem;
    }

    #calc_excel_table tbody td {
      vertical-align: middle;
      background-color: #fff;
    }

    #calc_excel_table tbody tr:nth-child(even) td {
      background: #fcfcfd;
    }

    /* Yield input width */
    #calc_excel_table .w-120 {
      max-width: 120px;
      min-width: 120px;
    }

    /* Toggle & action buttons */
    #calc_excel_table .toggle-bahan {
      border-color: #0d6efd;
      color: #0d6efd;
    }

    #calc_excel_table .toggle-bahan:hover {
      background: #0d6efd;
      color: #fff;
    }

    #calc_excel_table .add-bahan {
      border-color: #20c997;
      color: #20c997;
    }

    #calc_excel_table .add-bahan:hover {
      background: #20c997;
      color: #fff;
    }

    /* Bahan header row (inside detail) */
    #calc_excel_table .bahan-container .d-flex strong {
      color: #203246;
    }

    /* Nested table body */
    #calc_excel_table .nested-table tbody td {
      padding: 6px 8px;
      background: #fff;
    }

    #calc_excel_table .nested-table .form-control,
    #calc_excel_table .nested-table .form-select,
    #calc_excel_table .nested-table .select2-selection--single {
      height: 32px !important;
      min-height: 32px !important;
      font-size: 0.85rem;
    }

    /* Select2 chip alignment */
    .select2-container .select2-selection--single .select2-selection__rendered {
      line-height: 30px;
    }

    .select2-container .select2-selection--single {
      height: 32px;
    }

    .select2-container .select2-selection__arrow {
      height: 30px;
    }
  </style>
</head>

<body data-sidebar="dark" data-layout="vertical">
  <div id="layout-wrapper">
    <?php
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    $this->load->view('back_partial/head-css');
    ?>

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">

          <!-- Page Title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between mb-3">
                <div>
                  <h4 class="mb-1 font-size-18">VEGETABLE CALCULATOR</h4>
                  <p class="mb-2 text-muted">Excel-like table: Kondimen + Bahan otomatis</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Vegetable Calculator</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                  <h5 class="mb-0">Sesi Penghitungan Sayur</h5>
                  <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#calcModal">
                    <i class="ri-add-circle-line me-1"></i>Tambahkan Penghitungan
                  </button>
                </div>

                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle w-100" id="calc-sessions-table">
                      <thead class="table-dark">
                        <tr>
                          <th class="text-center" width="5%">No</th>
                          <th width="12%">Tanggal</th>
                          <th class="text-center" width="10%">Shift</th>
                          <th width="25%">Customer</th>
                          <th class="text-center" width="10%">Jumlah Menu</th>
                          <th class="text-center" width="10%">Jumlah Bahan</th>
                          <th class="text-center" width="18%">Action</th>
                        </tr>
                      </thead>
                      <tbody id="calc-sessions-tbody">
                        <!-- Data loaded via AJAX -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <!-- Modal Form -->
  <?php $this->load->view('back/vegetable_calculator/V_Vegetable_Calculator_Form'); ?>

  <!-- JAVASCRIPT -->
  <script>
    window.BASE_URL = '<?php echo base_url(); ?>';
  </script>
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>

  <script src="https://cdn.jsdelivr.net/npm/node-waves@0.7.6/dist/waves.min.js"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>

  <!-- Select2 JS (needed for dropdown with search) -->
  <script src="<?php echo base_url('assets_back/libs/select2/js/select2.full.min.js'); ?>"></script>

  <?php $this->load->view('back/vegetable_calculator/V_Vegetable_Calculator_js'); ?>
</body>

</html>