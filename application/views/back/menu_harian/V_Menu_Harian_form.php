<!-- Choices.js CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="form-modal-menu-harian" tabindex="-1" aria-labelledby="modalMenuHarianLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="form-menu-harian" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMenuHarianLabel">
            <i class="fas fa-utensils me-2"></i>Tambah/Edit Menu Harian
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" id="id_menu_harian" name="id_menu_harian" />

          <div class="row">
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="tanggal">Tanggal <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="tanggal" id="tanggal" required>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="shift">Shift <span class="text-danger">*</span></label>
                <select class="form-control" name="shift" id="shift" required>
                  <option value="">-- Pilih Shift --</option>
                  <option value="lunch">Lunch</option>
                  <option value="dinner">Dinner</option>
                  <option value="supper">Supper</option>
                </select>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label for="jenis_menu">Jenis Menu <span class="text-danger">*</span></label>
                <select class="form-control" name="jenis_menu" id="jenis_menu" required>
                  <option value="">-- Pilih Jenis Menu --</option>
                  <option value="regular">regular</option>
                  <option value="paket">paket</option>
                  <option value="sehat">sehat</option>
                  <option value="staff">staff</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="id_customer">Customer <span class="text-danger">*</span></label>
                <select class="form-control" name="id_customer" id="id_customer" required>
                  <option value="">-- Pilih Customer --</option>
                  <!-- Option customer diisi dari JS -->
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="id_kantin">Kantin <span class="text-danger">*</span></label>
                <div id="kantin-radio-group" class="border rounded p-2" style="min-height:44px; max-height:200px; overflow-y:auto;">
                  <!-- Radio kantin diisi dari JS -->
                </div>
                <!-- JS radio kantin dipindah ke V_Menu_Harian_js.php -->
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="nama_menu">Nama Menu <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="nama_menu" id="nama_menu" required placeholder="Masukkan nama menu utama">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label for="total_orderan_perkantin">Total Order/Kantin <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="total_orderan_perkantin" id="total_orderan_perkantin" min="1" required placeholder="Jumlah porsi menu utama">
              </div>
            </div>
          </div>
          <hr class="my-3">
          <div class="mb-2">
            <label class="form-label">Menu Kondimen <span class="text-danger">*</span></label>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="table-kondimen-menu">
              <thead class="table-light">
                <tr>
                  <th class="text-center" style="width:40px;">No</th>
                  <th>Nama Kondimen</th>
                  <th>Kategori</th>
                  <!-- Kolom qty per kantin akan diisi JS -->
                  <th class="text-center" style="width:80px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <!-- Baris kondimen akan diisi oleh JS -->
              </tbody>
            </table>
            <div class="mt-2 text-end">
              <button type="button" class="btn btn-sm btn-success" id="btn-tambah-kondimen">
                <i class="fas fa-plus"></i> Tambah Kondimen
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Close
          </button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  .form-label,
  .form-group label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #495057;
    font-size: 0.95rem;
  }

  .text-danger {
    color: #dc3545 !important;
    font-weight: 700;
  }

  .form-control,
  .form-select {
    height: calc(2.25rem + 2px);
    min-height: calc(2.25rem + 2px);
  }

  .table-bordered th,
  .table-bordered td {
    vertical-align: middle;
  }

  .btn-sm {
    font-size: 0.85rem;
    padding: 0.25rem 0.5rem;
  }

  #kantin-radio-group {
    max-height: 200px;
    overflow-y: auto;
  }
</style>