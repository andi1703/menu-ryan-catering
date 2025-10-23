<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="form-modal-menu-harian" tabindex="-1" aria-labelledby="modalMenuHarianLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-menu-harian" method="POST">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMenuHarianLabel">
            <i class="fas fa-utensils me-2"></i>Tambah/Edit Menu Harian
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_menu_harian" id="id_menu_harian">

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
                  <option value="menu regular">Menu Regular</option>
                  <option value="menu paket">Menu Paket</option>
                  <option value="menu sehat">Menu Sehat</option>
                  <option value="menu staff">Menu Staff</option>
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
                <select class="form-control" name="id_kantin" id="id_kantin" required>
                  <option value="">-- Pilih Kantin --</option>
                  <!-- Option kantin diisi dari JS -->
                </select>
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
                <label for="total_menu_perkantin">Total Menu/Kantin <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="total_menu_perkantin" id="total_menu_perkantin" min="1" required placeholder="Jumlah porsi menu utama">
              </div>
            </div>
          </div>

          <hr class="my-3">

          <div class="mb-2">
            <label class="form-label">Menu Kondimen <span class="text-danger">*</span></label>
            <button type="button" class="btn btn-sm btn-success ms-2" id="btn-tambah-kondimen">
              <i class="fas fa-plus"></i> Tambah Kondimen
            </button>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered align-middle" id="table-kondimen-menu">
              <thead class="table-light">
                <tr>
                  <th class="text-center" style="width:40px;">No</th>
                  <th>Nama Kondimen</th>
                  <th>Qty Kondimen</th>
                  <th class="text-center" style="width:80px;">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <!-- Kondimen diisi dari JS -->
              </tbody>
            </table>
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
</style>