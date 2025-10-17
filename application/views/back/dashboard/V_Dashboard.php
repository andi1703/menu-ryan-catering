<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  $this->load->view('back_partial/title-meta');
  $this->load->view('back_partial/head-css');
  ?>

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <!-- DataTables CSS -->
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css" rel="stylesheet" type="text/css" />
</head>

<body data-topbar="dark" data-layout="vertical">
  <!-- Begin page -->
  <div id="layout-wrapper">

    <!-- ✅ TOPBAR & SIDEBAR - TIDAK DIUBAH -->
    <?php
    $this->load->view('back_partial/topbar');
    $this->load->view('back_partial/sidebar');
    ?>

    <!-- ✅ MAIN CONTENT -->
    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">

          <!-- ✅ PAGE TITLE -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">📊 Dashboard Menu Ryan Catering</h4>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">Dashboard</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- ✅ WELCOME MESSAGE -->
          <div class="row">
            <div class="col-12">
              <div class="card bg-primary text-white">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-md-8">
                      <h4 class="mb-3 text-white">🍽️ Selamat Datang di Dashboard Menu!</h4>
                      <p class="mb-0 text-white-50">
                        Kelola menu Indonesian, Western, Chinese, dan Japanese Food dengan mudah.
                        Pantau tren menu terpopuler dan statistik lengkap.
                      </p>
                    </div>
                    <div class="col-md-4">
                      <div class="text-md-end">
                        <i class="ri-restaurant-2-line font-size-48 text-white-50"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ✅ STATISTIK MENU -->
          <div class="row">
            <div class="col-12">
              <h5 class="mb-3">📈 Statistik Menu</h5>
            </div>
          </div>

          <div class="row">
            <!-- Total Menu -->
            <div class="col-xl-3 col-md-6 col-sm-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-primary">
                          <i class="ri-restaurant-line font-size-16"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Total Menu</h6>
                      <h4 class="mb-0"><?php echo number_format($total_menu); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Indonesian Food -->
            <div class="col-xl-3 col-md-6 col-sm-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-success">
                          <i class="ri-leaf-line font-size-16"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Indonesian</h6>
                      <h4 class="mb-0"><?php echo number_format($total_indonesian); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Western Food -->
            <div class="col-xl-3 col-md-6 col-sm-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-warning">
                          <i class="ri-cake-line font-size-16"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Western</h6>
                      <h4 class="mb-0"><?php echo number_format($total_western); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Chinese Food -->
            <div class="col-xl-3 col-md-6 col-sm-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-danger">
                          <i class="ri-bowl-line font-size-16"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Chinese</h6>
                      <h4 class="mb-0"><?php echo number_format($total_chinese); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Japanese Food -->
            <div class="col-xl-3 col-md-6 col-sm-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                      <div class="avatar-sm">
                        <span class="avatar-title rounded-circle bg-info">
                          <i class="ri-fish-line font-size-16"></i>
                        </span>
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Japanese</h6>
                      <h4 class="mb-0"><?php echo number_format($total_japanese); ?></h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ✅ TREN MENU & CHARTS -->
          <div class="row">
            <!-- Chart Kategori -->
            <div class="col-xl-6">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title mb-0">📊 Distribusi Menu per Kategori</h4>
                </div>
                <div class="card-body">
                  <canvas id="categoryChart" width="400" height="200"></canvas>
                </div>
              </div>
            </div>

            <!-- Trending Menu -->
            <div class="col-xl-6">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title mb-0">🔥 Menu Terpopuler</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>Rank</th>
                          <th>Nama Menu</th>
                          <th>Kategori</th>
                          <th>Popularitas</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($trending_menu)) : ?>
                          <?php foreach ($trending_menu as $index => $menu) : ?>
                            <tr>
                              <td>
                                <span class="badge bg-primary">#<?php echo $index + 1; ?></span>
                              </td>
                              <td>
                                <h6 class="mb-0"><?php echo $menu['menu_nama']; ?></h6>
                              </td>
                              <td>
                                <span class="badge bg-success"><?php echo $menu['nama_kategori']; ?></span>
                              </td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <div class="flex-grow-1">
                                    <div class="progress progress-sm">
                                      <div class="progress-bar bg-primary" style="width: <?php echo ($menu['popularity'] * 20); ?>%"></div>
                                    </div>
                                  </div>
                                  <div class="flex-shrink-0 ms-2">
                                    <small class="text-muted"><?php echo $menu['popularity']; ?></small>
                                  </div>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else : ?>
                          <tr>
                            <td colspan="4" class="text-center py-4">
                              <i class="ri-restaurant-line font-size-48 text-muted"></i>
                              <h6 class="mt-2">Belum ada data menu</h6>
                              <p class="text-muted">Tambahkan menu untuk melihat tren</p>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ✅ MENU TERBARU & QUICK ACTIONS -->
          <div class="row">
            <!-- Recent Menu -->
            <div class="col-xl-8">
              <div class="card">
                <div class="card-header">
                  <h4 class="card-title mb-0">🆕 Menu Terbaru</h4>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table class="table table-nowrap table-hover mb-0">
                      <thead class="table-light">
                        <tr>
                          <th>Nama Menu</th>
                          <th>Kategori</th>
                          <th>Tanggal</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($recent_menu)) : ?>
                          <?php foreach ($recent_menu as $menu) : ?>
                            <tr>
                              <td>
                                <div class="d-flex align-items-center">
                                  <h6 class="mb-0"><?php echo $menu['menu_nama']; ?></h6>
                                </div>
                              </td>
                              <td>
                                <span class="badge bg-info"><?php echo $menu['nama_kategori']; ?></span>
                              </td>
                              <td>
                                <small class="text-muted">
                                  <?php echo date('d M Y', strtotime($menu['created_at'])); ?>
                                </small>
                              </td>
                              <td>
                                <span class="badge bg-success">Active</span>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else : ?>
                          <tr>
                            <td colspan="5" class="text-center py-4">
                              <i class="ri-restaurant-line font-size-48 text-muted"></i>
                              <h6 class="mt-2">Belum ada menu</h6>
                              <p class="text-muted">Tambahkan menu pertama Anda</p>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Info Dashboard -->
            <div class="card">
              <div class="card-header">
                <h4 class="card-title mb-0">📊 Informasi Dashboard</h4>
              </div>
              <div class="card-body">
                <div class="text-center">
                  <div class="avatar-lg mx-auto">
                    <div class="avatar-title rounded-circle bg-primary">
                      <i class="ri-pie-chart-line font-size-24"></i>
                    </div>
                  </div>
                  <h5 class="mt-3 mb-1">Dashboard Menu</h5>
                  <p class="text-muted">Kelola semua menu dengan mudah</p>

                  <div class="mt-4">
                    <div class="row">
                      <div class="col-6">
                        <div class="mt-3">
                          <h5 class="font-size-16 mb-1"><?php echo number_format($total_menu); ?></h5>
                          <p class="text-muted mb-0">Total Menu</p>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="mt-3">
                          <h5 class="font-size-16 mb-1"><?php echo $this->db->count_all('kategori_menu'); ?></h5>
                          <p class="text-muted mb-0">Kategori</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- ✅ PERFORMANCE KERJA NUTRISIONIST -->
          <div class="row">
            <div class="col-12">
              <h5 class="mb-3">👩‍⚕️ Performance Kerja Nutrisionist</h5>
              <div class="card">
                <div class="card-body">
                  <div class="row text-center">
                    <div class="col-md-4">
                      <h4 class="mb-0"><?= $nutrisionist_stats['menu_input'] ?? 0 ?></h4>
                      <p class="text-muted mb-0">Menu Diinput</p>
                    </div>
                    <div class="col-md-4">
                      <h4 class="mb-0"><?= $nutrisionist_stats['menu_update'] ?? 0 ?></h4>
                      <p class="text-muted mb-0">Menu Diupdate</p>
                    </div>
                    <div class="col-md-4">
                      <h4 class="mb-0"><?= $nutrisionist_stats['activity_count'] ?? 0 ?></h4>
                      <p class="text-muted mb-0">Aktivitas Lain</p>
                    </div>
                  </div>
                  <hr>
                  <div class="row text-center">
                    <div class="col-md-4">
                      <h5 class="mb-0"><?= $nutrisionist_stats['login_count'] ?? 0 ?></h5>
                      <p class="text-muted mb-0">Jumlah Login</p>
                    </div>
                    <div class="col-md-4">
                      <h5 class="mb-0"><?= $nutrisionist_stats['total_usage_hours'] ?? 0 ?></h5>
                      <p class="text-muted mb-0">Total Waktu Penggunaan (jam)</p>
                    </div>
                    <div class="col-md-4">
                      <h5 class="mb-0"><?= $nutrisionist_stats['avg_activity_per_day'] ?? 0 ?></h5>
                      <p class="text-muted mb-0">Rata-rata Aktivitas/Hari</p>
                    </div>
                  </div>
                  <div class="mt-3 text-center">
                    <p class="text-info">Semakin aktif, semakin besar kontribusi Anda untuk pelayanan terbaik!</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- ✅ FOOTER - TIDAK DIUBAH -->
    <?php $this->load->view('back_partial/footer'); ?>
  </div>
  </div>

  <!-- Right bar overlay -->
  <div class="rightbar-overlay"></div>

  <!-- ✅ JAVASCRIPT -->
  <script src="<?php echo base_url(); ?>assets_back/libs/jquery/jquery.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/metismenu/metisMenu.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/simplebar/simplebar.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/node-waves/waves.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="<?php echo base_url(); ?>assets_back/js/app.js"></script>

  <!-- ✅ CHART SCRIPT -->
  <script>
    $(document).ready(function() {
      // ✅ CATEGORY CHART
      const ctx = document.getElementById('categoryChart').getContext('2d');
      const categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: [
            'Indonesian Food',
            'Western Food',
            'Chinese Food',
            'Japanese Food'
          ],
          datasets: [{
            data: [
              <?php echo $total_indonesian; ?>,
              <?php echo $total_western; ?>,
              <?php echo $total_chinese; ?>,
              <?php echo $total_japanese; ?>
            ],
            backgroundColor: [
              '#28a745',
              '#ffc107',
              '#dc3545',
              '#17a2b8'
            ],
            borderWidth: 2,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                usePointStyle: true,
                padding: 20
              }
            }
          }
        }
      });

      console.log('Dashboard loaded successfully');
    });
  </script>
</body>

</html>