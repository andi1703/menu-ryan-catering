<!DOCTYPE html>
<html lang="en">

<head>
  <?php $this->load->view('back_partial/title-meta'); ?>
  <link href="<?php echo base_url('assets_back/libs/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">
  <style>
    /* Tambahkan styling responsif jika perlu */
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
                  <div class="col-md-3">
                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars($this->input->get('tanggal')) ?>" placeholder="Tanggal">
                  </div>
                  <div class="col-md-3">
                    <select name="id_customer" class="form-control">
                      <option value="">- Pilih Customer -</option>
                      <?php foreach ($customerList as $c) : ?>
                        <option value="<?= $c['id_customer'] ?>" <?= $this->input->get('id_customer') == $c['id_customer'] ? 'selected' : '' ?>>
                          <?= htmlspecialchars($c['nama_customer']) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="id_kantin" class="form-control">
                      <option value="">- Pilih Kantin -</option>
                      <?php foreach ($kantinList as $k) : ?>
                        <option value="<?= $k ?>" <?= $this->input->get('id_kantin') == $k ? 'selected' : '' ?>>
                          <?= htmlspecialchars($k) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select name="shift" class="form-control">
                      <option value="">- Pilih Shift -</option>
                      <option value="lunch" <?= $this->input->get('shift') == 'lunch' ? 'selected' : '' ?>>Lunch</option>
                      <option value="dinner" <?= $this->input->get('shift') == 'dinner' ? 'selected' : '' ?>>Dinner</option>
                      <option value="supper" <?= $this->input->get('shift') == 'supper' ? 'selected' : '' ?>>Supper</option>
                    </select>
                  </div>
                  <div class="col-md-12 mt-2">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="<?= base_url('menu-harian-report') ?>" class="btn btn-secondary">Reset</a>
                  </div>
                </div>
              </form>
            </div>
          </div>

          <!-- Main Content Table -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title mb-0">Report Daily Menu</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle w-100" id="report-daily-menu-table">
                      <thead class="table-dark">
                        <tr>
                          <th>Menu Kondimen</th>
                          <th>Kategori</th>
                          <?php foreach ($kantinList as $kantin) : ?>
                            <th><?= htmlspecialchars($kantin) ?></th>
                          <?php endforeach; ?>
                          <th>Total Orderan</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($pivot as $row) : ?>
                          <tr>
                            <td><?= htmlspecialchars($row['menu_kondimen']) ?></td>
                            <td><?= htmlspecialchars($row['kategori']) ?></td>
                            <?php foreach ($kantinList as $kantin) : ?>
                              <td><?= isset($row['qty_per_kantin'][$kantin]) ? htmlspecialchars($row['qty_per_kantin'][$kantin]) : 0 ?></td>
                            <?php endforeach; ?>
                            <td><?= htmlspecialchars($row['total']) ?></td>
                          </tr>
                        <?php endforeach; ?>
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

  <!-- JS Bootstrap & DataTables -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/metismenu/metisMenu.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/simplebar/simplebar.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/node-waves/waves.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive/js/dataTables.responsive.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/js/app.js'); ?>"></script>
  <script>
    $(document).ready(function() {
      $('#report-daily-menu-table').DataTable({
        responsive: true,
        paging: true,
        searching: true,
        ordering: true
      });
    });
  </script>
</body>

</html>