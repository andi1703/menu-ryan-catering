<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8" />
  <title><?= $title ?> | Ryan Catering System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta content="Sistem Manajemen Ryan Catering" name="description" />
  <meta content="Ryan Catering" name="author" />

  <!-- App CSS -->
  <link href="<?php echo base_url('assets_back/css/bootstrap.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/css/icons.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/css/app.min.css'); ?>" rel="stylesheet" type="text/css" />
  <link href="<?php echo base_url('assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css'); ?>" rel="stylesheet" />
  <link href="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.css'); ?>" rel="stylesheet">
</head>

<body data-topbar="dark" data-layout="vertical">
  <div id="layout-wrapper">

    <div class="main-content">
      <div class="page-content">
        <div class="container-fluid">

          <!-- Page Title -->
          <div class="row">
            <div class="col-12">
              <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                  <h4 class="mb-0 font-size-18">
                    <i class="fas fa-clipboard-list me-2 text-primary"></i>Data Bahan Baku Per Shift
                  </h4>
                  <p class="mb-0 text-muted">Kelola dan pantau kebutuhan bahan baku untuk setiap shift kerja</p>
                </div>
                <div class="page-title-right">
                  <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="<?php echo base_url('dashboard'); ?>">Beranda</a></li>
                    <li class="breadcrumb-item active">Data Shift Bahan</li>
                  </ol>
                </div>
              </div>
            </div>
          </div>

          <!-- Action Cards -->
          <div class="row">
            <div class="col-xl-3 col-md-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-primary rounded-circle fs-3">
                        <i class="fas fa-plus"></i>
                      </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Input Data Hari Ini</h6>
                      <p class="text-muted mb-2">Tambah data bahan shift</p>
                      <button class="btn btn-primary btn-sm" onclick="inputDataHariIni()">
                        <i class="fas fa-plus me-1"></i>Input Sekarang
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-success rounded-circle fs-3">
                        <i class="fas fa-calendar-alt"></i>
                      </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Pilih Tanggal</h6>
                      <p class="text-muted mb-2">Input data tanggal lain</p>
                      <input type="date" class="form-control form-control-sm" id="inputTanggalLain" onchange="inputDataTanggal()">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-info rounded-circle fs-3">
                        <i class="fas fa-file-excel"></i>
                      </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Export Data</h6>
                      <p class="text-muted mb-2">Unduh laporan Excel</p>
                      <button class="btn btn-info btn-sm" onclick="showExportModal()">
                        <i class="fas fa-download me-1"></i>Export
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-md-6">
              <div class="card">
                <div class="card-body">
                  <div class="d-flex align-items-center">
                    <div class="avatar-sm flex-shrink-0">
                      <span class="avatar-title bg-warning rounded-circle fs-3">
                        <i class="fas fa-cog"></i>
                      </span>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-0">Template</h6>
                      <p class="text-muted mb-2">Kelola template default</p>
                      <button class="btn btn-warning btn-sm" onclick="showTemplateModal()">
                        <i class="fas fa-edit me-1"></i>Kelola
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Data Table -->
          <div class="row">
            <div class="col-12">
              <div class="card">
                <div class="card-header">
                  <div class="row align-items-center">
                    <div class="col">
                      <h4 class="card-title mb-0">
                        <i class="fas fa-list me-2 text-primary"></i>Riwayat Data Shift Bahan
                      </h4>
                      <p class="text-muted mb-0">Daftar data bahan baku yang telah diinput per shift</p>
                    </div>
                    <div class="col-auto">
                      <button class="btn btn-outline-primary btn-sm" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-1"></i>Refresh
                      </button>
                    </div>
                  </div>
                </div>
                <div class="card-body">
                  <div class="table-responsive">
                    <table id="shiftTable" class="table table-bordered table-hover">
                      <thead class="table-light">
                        <tr>
                          <th width="5%">No</th>
                          <th width="15%">Tanggal</th>
                          <th width="10%">Shift</th>
                          <th width="15%">Status</th>
                          <th width="15%">Total Item</th>
                          <th width="15%">Waktu Input</th>
                          <th width="25%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="shiftTableBody">
                        <?php if (!empty($shift_list)) : ?>
                          <?php $no = 1;
                          foreach ($shift_list as $shift) : ?>
                            <tr>
                              <td class="text-center"><?= $no++ ?></td>
                              <td>
                                <strong><?= date('d/m/Y', strtotime($shift['tanggal_shift'])) ?></strong><br>
                                <small class="text-muted"><?= date('l', strtotime($shift['tanggal_shift'])) ?></small>
                              </td>
                              <td>
                                <span class="badge bg-primary"><?= strtoupper($shift['shift_type']) ?></span>
                              </td>
                              <td>
                                <?php
                                $status_color = [
                                  'draft' => 'warning',
                                  'completed' => 'info',
                                  'approved' => 'success'
                                ];
                                $status_text = [
                                  'draft' => 'Draft',
                                  'completed' => 'Selesai',
                                  'approved' => 'Disetujui'
                                ];
                                ?>
                                <span class="badge bg-<?= $status_color[$shift['status_input']] ?>">
                                  <?= $status_text[$shift['status_input']] ?>
                                </span>
                              </td>
                              <td class="text-center">
                                <span class="badge bg-secondary"><?= number_format($shift['total_bahan_items']) ?> item</span>
                              </td>
                              <td>
                                <small><?= date('d/m/Y H:i', strtotime($shift['created_at'])) ?></small>
                              </td>
                              <td>
                                <div class="btn-group" role="group">
                                  <button class="btn btn-sm btn-outline-primary" onclick="viewData('<?= $shift['tanggal_shift'] ?>', '<?= $shift['shift_type'] ?>')" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-success" onclick="editData('<?= $shift['tanggal_shift'] ?>', '<?= $shift['shift_type'] ?>')" title="Edit Data">
                                    <i class="fas fa-edit"></i>
                                  </button>
                                  <button class="btn btn-sm btn-outline-info" onclick="exportData('<?= $shift['tanggal_shift'] ?>', '<?= $shift['shift_type'] ?>')" title="Export Excel">
                                    <i class="fas fa-file-excel"></i>
                                  </button>
                                  <?php if ($shift['status_input'] !== 'approved') : ?>
                                    <button class="btn btn-sm btn-outline-warning" onclick="approveData(<?= $shift['id_header'] ?>)" title="Setujui">
                                      <i class="fas fa-check"></i>
                                    </button>
                                  <?php endif; ?>
                                  <button class="btn btn-sm btn-outline-danger" onclick="deleteData(<?= $shift['id_header'] ?>)" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                  </button>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else : ?>
                          <tr>
                            <td colspan="7" class="text-center py-5">
                              <div class="text-muted">
                                <i class="fas fa-clipboard-list fa-3x mb-3 text-primary"></i>
                                <h5>Belum ada data shift</h5>
                                <p>Mulai input data bahan baku untuk shift hari ini</p>
                                <button class="btn btn-primary" onclick="inputDataHariIni()">
                                  <i class="fas fa-plus me-1"></i>Input Data Pertama
                                </button>
                              </div>
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

        </div> <!-- container-fluid -->
      </div> <!-- page-content -->
    </div> <!-- main-content -->

  </div> <!-- layout-wrapper -->

  <!-- JavaScript -->
  <script src="<?php echo base_url('assets_back/libs/jquery/jquery.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net/js/jquery.dataTables.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js'); ?>"></script>
  <script src="<?php echo base_url('assets_back/libs/sweetalert2/sweetalert2.min.js'); ?>"></script>

  <script>
    $(document).ready(function() {
      // Initialize DataTable
      $('#shiftTable').DataTable({
        responsive: true,
        pageLength: 10,
        order: [
          [1, "desc"]
        ], // Sort by date desc
        language: {
          url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        }
      });
    });

    function inputDataHariIni() {
      const today = new Date().toISOString().split('T')[0];
      window.location.href = '<?= base_url("shift-bahan/input/") ?>' + today;
    }

    function inputDataTanggal() {
      const tanggal = $('#inputTanggalLain').val();
      if (tanggal) {
        window.location.href = '<?= base_url("shift-bahan/input/") ?>' + tanggal;
      }
    }

    function viewData(tanggal, shift_type) {
      window.location.href = '<?= base_url("shift-bahan/view/") ?>' + tanggal + '/' + shift_type;
    }

    function editData(tanggal, shift_type) {
      window.location.href = '<?= base_url("shift-bahan/input/") ?>' + tanggal + '?shift=' + shift_type;
    }

    function exportData(tanggal, shift_type) {
      window.open('<?= base_url("shift-bahan/export_excel") ?>?tanggal=' + tanggal + '&shift_type=' + shift_type, '_blank');
    }

    function approveData(id_header) {
      Swal.fire({
        title: 'Konfirmasi Persetujuan',
        text: 'Apakah Anda yakin ingin menyetujui data shift ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Setujui!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '<?= base_url("shift-bahan/approve_data") ?>',
            type: 'POST',
            data: {
              id_header: id_header
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                Swal.fire('Berhasil!', response.message, 'success');
                location.reload();
              } else {
                Swal.fire('Error!', response.message, 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'Terjadi kesalahan saat menyetujui data', 'error');
            }
          });
        }
      });
    }

    function deleteData(id_header) {
      Swal.fire({
        title: 'Konfirmasi Penghapusan',
        text: 'Apakah Anda yakin ingin menghapus data shift ini? Data yang dihapus tidak dapat dikembalikan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: '<?= base_url("shift-bahan/delete_data") ?>',
            type: 'POST',
            data: {
              id_header: id_header
            },
            dataType: 'json',
            success: function(response) {
              if (response.status === 'success') {
                Swal.fire('Terhapus!', response.message, 'success');
                location.reload();
              } else {
                Swal.fire('Error!', response.message, 'error');
              }
            },
            error: function() {
              Swal.fire('Error!', 'Terjadi kesalahan saat menghapus data', 'error');
            }
          });
        }
      });
    }

    function refreshData() {
      location.reload();
    }

    function showExportModal() {
      // TODO: Implement export modal
      Swal.fire('Info', 'Fitur export batch sedang dalam pengembangan', 'info');
    }

    function showTemplateModal() {
      // TODO: Implement template modal
      Swal.fire('Info', 'Fitur kelola template sedang dalam pengembangan', 'info');
    }
  </script>

</body>

</html>