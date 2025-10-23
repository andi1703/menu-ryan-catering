<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>

  <!-- DataTables -->
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  BOOTSTRAP CSS
  <link href="<?php echo base_url(); ?>assets_back/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- SWEETALERT2 CSS -->
  <link href="<?php echo base_url(); ?>assets_back/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">

  <!-- CUSTOM CSS UNTUK WORD WRAP -->
  <style>
    /* TABLE FIXES */
    #datatable {
      table-layout: fixed;
      width: 100% !important;
    }

    #datatable th,
    #datatable td {
      word-wrap: break-word;
      word-break: break-word;
      overflow-wrap: break-word;
      white-space: normal;
      vertical-align: top;
    }

    /* COLUMN WIDTHS */
    #datatable th:nth-child(1),
    #datatable td:nth-child(1) {
      width: 8%;
      text-align: center;
    }

    #datatable th:nth-child(2),
    #datatable td:nth-child(2) {
      width: 25%;
    }

    #datatable th:nth-child(3),
    #datatable td:nth-child(3) {
      width: 52%;
    }

    #datatable th:nth-child(4),
    #datatable td:nth-child(4) {
      width: 15%;
      text-align: center;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {

      #datatable th:nth-child(2),
      #datatable td:nth-child(2) {
        width: 30%;
      }

      #datatable th:nth-child(3),
      #datatable td:nth-child(3) {
        width: 45%;
      }
    }

    .table-action-buttons {
      display: flex;
      gap: 3px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .btn-action {
      padding: 4px 8px;
      font-size: 11px;
      border-radius: 3px;
    }

    .table th {
      background-color: #f8f9fa;
      font-weight: 600;
      color: #495057;
      border-bottom: 2px solid #dee2e6;
      padding: 12px 8px;
    }

    .table td {
      padding: 10px 8px;
      border-bottom: 1px solid #dee2e6;
      line-height: 1.4;
    }

    .table tbody tr:hover {
      background-color: #f8f9fa;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: 1px solid #e3e6f0;
    }

    .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #e3e6f0;
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
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-0 font-size-18">Kategori Menu</h4>
                  <p class="mb-0 text-muted">Kelola kategori untuk komponen menu catering</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                    <li class="breadcrumb-item active">Kategori Menu</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Main Content -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col">
                      <h4 class="card-title mb-0">Daftar Kategori Menu</h4>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary" onclick="tambah_data()">
                        <i class="ri-add-circle-line me-2"></i>Tambah Kategori
                      </button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="table-responsive">
                    <table id="datatable" class="table table-hover table-bordered dt-responsive nowrap w-100">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Kategori</th>
                          <th>Deskripsi</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="show_data_kategori">
                        <!-- Data akan dimuat via AJAX -->
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

  <!-- Include Modal Form -->
  <?php $this->load->view('back/KategoriKomponenMenu/V_Kategori_Menu_form'); ?>

  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url(); ?>assets_back/libs/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/metismenu/metisMenu.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/simplebar/simplebar.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/node-waves/waves.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/sweetalert2/sweetalert2.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/js/app.js"></script>

  <?php $this->load->view('back/KategoriKomponenMenu/V_Kategori_Menu_js'); ?>

</body>

</html>