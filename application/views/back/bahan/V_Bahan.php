<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>
  <!-- DataTables & CSS -->
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">
</head>

<body data-topbar="dark" data-layout="vertical">
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
                  <h4 class="mb-0 font-size-18">Data Master Bahan Baku</h4>
                  <p class="mb-0 text-muted">Kelola dan pantau semua bahan baku untuk produksi menu</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Data Master</a></li>
                    <li class="breadcrumb-item active">Bahan Baku</li>
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
                      <h4 class="card-title mb-0">
                        <i class="fas fa-carrot me-2 text-primary"></i>Daftar Bahan Baku
                      </h4>
                      <p class="text-muted mb-0">Kelola semua bahan baku untuk produksi menu catering</p>
                    </div>
                    <div class="col-auto">
                      <button type="button" class="btn btn-primary btn-lg" onclick="tambah_bahan()">
                        <i class="fas fa-plus me-2"></i>Tambah Bahan Baru
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="datatable" class="table table-bordered">
                      <thead>
                        <tr>
                          <th width="5%">No</th>
                          <th width="25%">Nama Bahan</th>
                          <th width="10%">Satuan</th>
                          <th width="15%">Harga Awal</th>
                          <th width="15%">Harga Sekarang</th>
                          <th width="10%">Status</th>
                          <th width="20%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="show_data_bahan">
                        <?php
                        $no = 1;
                        foreach ($bahan_list as $row) {
                        ?>
                          <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $row['nama_bahan'] ?></td>
                            <td><?= $row['nama_satuan'] ?></td>
                            <td><?= 'Rp ' . number_format($row['harga_awal'], 0, ',', '.') ?></td>
                            <td><?= 'Rp ' . number_format($row['harga_sekarang'], 0, ',', '.') ?></td>
                            <td>
                              <button class="btn btn-sm btn-warning" onclick="edit_bahan(<?= $row['id_bahan'] ?>)">
                                <i class="ri-pencil-line"></i> Edit
                              </button>
                              <button class="btn btn-sm btn-danger" onclick="hapus_bahan(<?= $row['id_bahan'] ?>)">
                                <i class="ri-delete-bin-line"></i> Hapus
                              </button>
                            </td>
                          </tr>
                        <?php } ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Modal Form Bahan -->
          <?php $this->load->view('back/bahan/V_Bahan_form', ['satuan_list' => $satuan_list]); ?>
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
  <?php $this->load->view('back/bahan/V_Bahan_js'); ?>
</body>

</html>