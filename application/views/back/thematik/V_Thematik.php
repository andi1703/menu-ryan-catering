<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->helper('url');
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>

  <!-- DataTables -->
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

  <!-- Sweet Alert -->
  <link href="<?php echo base_url(); ?>assets_back/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css" />

  <!-- BOOTSTRAP CSS -->
  <link href="<?php echo base_url(); ?>assets_back/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet">

  <!-- SWEETALERT2 CSS -->
  <link href="<?php echo base_url(); ?>assets_back/libs/sweetalert2/sweetalert2.min.css" rel="stylesheet">

  <!-- CUSTOM CSS: selaraskan dengan tampilan Kategori Menu -->
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

    .table-action-buttons {
      display: flex;
      gap: 6px;
      justify-content: center;
      flex-wrap: wrap;
    }

    .card {
      box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
      border: 1px solid rgba(0, 0, 0, 0.125);
    }

    .card-header {
      background-color: #fff;
      border-bottom: 1px solid rgba(0, 0, 0, 0.125);
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

          <!-- start page title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Thematic Management</h4>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master Data</a></li>
                    <li class="breadcrumb-item active">Thematic</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>
          <!-- end page title -->

          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col-md-6">
                      <h4 class="card-title mb-0">Data Thematic</h4>
                    </div>
                    <div class="col-md-6">
                      <div class="d-flex flex-wrap align-items-center justify-content-end gap-2">
                        <button type="button" class="btn btn-primary btn-tambah-thematic">
                          <i class="ri-add-circle-line me-1"></i> Tambah Thematic
                        </button>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="card-body">
                  <div class="table-responsive">
                    <table id="datatable" class="table table-striped table-hover align-middle w-100">
                      <thead class="table-dark">
                        <tr>
                          <th class="text-center">No</th>
                          <th>Nama Thematic</th>
                          <th>Deskripsi</th>
                          <th class="text-center">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="show_data_thematic">
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

  <!-- Modal Form -->
  <div class="modal fade" id="modal_form" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalLabel">Form Thematic</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="form_thematic">
          <div class="modal-body">
            <input type="hidden" id="method" name="method" value="tambah">
            <input type="hidden" id="id_thematik" name="id_thematik">

            <div class="mb-3">
              <label for="thematik_nama" class="form-label">Nama Thematic <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="thematik_nama" name="thematik_nama" placeholder="Masukkan nama thematic" required>
              <div class="invalid-feedback"></div>
            </div>

            <div class="mb-3">
              <label for="thematik_deskripsi" class="form-label">Deskripsi</label>
              <textarea class="form-control" id="thematik_deskripsi" name="thematik_deskripsi" rows="3" placeholder="Masukkan deskripsi thematic (opsional)"></textarea>
              <div class="invalid-feedback"></div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="ri-close-line me-1"></i> Batal
            </button>
            <button type="submit" class="btn btn-primary" id="btn_save">
              <i class="ri-save-line me-1"></i> Simpan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- JAVASCRIPT -->
  <script src="<?php echo base_url(); ?>assets_back/libs/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/metismenu/metisMenu.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/simplebar/simplebar.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/node-waves/waves.min.js"></script>

  <!-- DataTables -->
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>

  <!-- Sweet Alert -->
  <script src="<?php echo base_url(); ?>assets_back/libs/sweetalert2/sweetalert2.min.js"></script>

  <script src="<?php echo base_url(); ?>assets_back/js/app.js"></script>

  <!-- Load JavaScript khusus halaman -->
  <?php $this->load->view('back/thematik/V_Thematik_js'); ?>

</body>

</html>