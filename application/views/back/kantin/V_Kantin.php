<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>
  <!-- DataTables -->
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css'); ?>" rel="stylesheet" type="text/css" />
  <!-- BOOTSTRAP CSS -->
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <!-- SWEETALERT2 CSS -->
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">
  <style>
    /* Custom CSS jika diperlukan */
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
                  <h4 class="mb-0 font-size-18">Master Kantin</h4>
                  <p class="mb-0 text-muted">Kelola data kantin dan lokasi perusahaan</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                    <li class="breadcrumb-item active">Kantin</li>
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
                      <h4 class="card-title mb-0">Daftar Kantin</h4>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary" onclick="tambah_kantin()">
                        <i class="ri-add-circle-line me-2"></i>Tambah Kantin
                      </button>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="table-responsive">
                    <table id="datatable" class="table table-bordered">
                      <thead>
                        <tr>
                          <th>No</th>
                          <th>Nama Kantin</th>
                          <th>Customer</th>
                          <th>Alamat Lokasi</th>
                          <th>Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="show_data_kantin"></tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Modal Form Kantin -->
          <?php $this->load->view('back/kantin/V_Kantin_form'); ?>

        </div>
      </div>
      <?php $this->load->view('back_partial/footer'); ?>
    </div>
  </div>

  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-buttons/js/dataTables.buttons.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <?php $this->load->view('back/kantin/V_Kantin_js'); ?>

</body>

</html>