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

  <link href="<?php echo base_url(); ?>assets_back/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- SWEETALERT2 CSS -->
  <link href="<?php echo base_url(); ?>assets_back/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">

  <!-- CUSTOM CSS UNTUK WORD WRAP -->
  <style>
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      border-radius: 0.375rem;
    }

    #datatable {
      width: 100% !important;
    }

    .table thead th,
    .table tbody td {
      vertical-align: middle;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
      background-color: #fff;
      border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }

    .table-action-buttons {
      display: flex;
      gap: 6px;
      justify-content: center;
      flex-wrap: wrap;
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
                    <table id="datatable" class="table table-striped table-hover align-middle w-100">
                      <thead class="table-dark">
                        <tr>
                          <th class="text-center">No</th>
                          <th>Nama Kategori</th>
                          <th>Deskripsi</th>
                          <th class="text-center">Aksi</th>
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