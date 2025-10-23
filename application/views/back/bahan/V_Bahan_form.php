<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!-- Modal Form Bahan -->
<div class="modal fade" id="form-modal-bahan" tabindex="-1" aria-labelledby="modalBahanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalBahanLabel">
          <i class="fas fa-carrot me-2"></i>Tambah Bahan Baku
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <!-- PENTING: Pastikan form ID konsisten dengan JavaScript -->
      <form id="form_bahan" method="POST">
        <div class="modal-body">
          <!-- PENTING: Field ID dan stat harus konsisten -->
          <input type="hidden" id="bahan_id" name="id" value="">
          <input type="hidden" id="form_stat" name="stat" value="add">

          <!-- Row 1: Nama Bahan dan Satuan -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="nama_bahan" class="form-label">Nama Bahan <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nama_bahan" name="nama_bahan" required placeholder="Masukkan nama bahan baku">
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="id_satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                <select class="form-select" id="id_satuan" name="id_satuan" required>
                  <option value="">-- Pilih Satuan --</option>

                  <!-- DEBUG: Tampilkan jika tidak ada data -->
                  <?php if (!isset($satuan_list) || empty($satuan_list)) : ?>
                    <option value="" disabled>Data satuan tidak tersedia</option>
                    <?php
                    // DEBUG: Log untuk developer
                    if (ENVIRONMENT === 'development') {
                      echo '<!-- DEBUG: $satuan_list tidak tersedia atau kosong -->';
                      if (isset($satuan_list)) {
                        echo '<!-- DEBUG: $satuan_list ada tapi kosong: ' . json_encode($satuan_list) . ' -->';
                      } else {
                        echo '<!-- DEBUG: $satuan_list tidak di-set dari controller -->';
                      }
                    }
                    ?>
                  <?php else : ?>
                    <!-- TAMPILKAN DATA SATUAN -->
                    <?php foreach ($satuan_list as $satuan) : ?>
                      <option value="<?php echo htmlspecialchars($satuan['id_satuan']); ?>">
                        <?php echo htmlspecialchars($satuan['nama_satuan']); ?>
                      </option>
                    <?php endforeach; ?>

                    <!-- DEBUG: Log jumlah data -->
                    <?php if (ENVIRONMENT === 'development') : ?>
                      <!-- DEBUG: Berhasil load <?php echo count($satuan_list); ?> satuan -->
                    <?php endif; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>

          <!-- Row 2: Harga Awal dan Harga Sekarang -->
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="harga_awal" class="form-label">Harga Awal <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" class="form-control" id="harga_awal" name="harga_awal" required min="0" step="0.01" placeholder="0">
                </div>
                <div class="form-text">Harga beli pertama kali untuk referensi</div>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="harga_sekarang" class="form-label">Harga Sekarang <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">Rp</span>
                  <input type="number" class="form-control" id="harga_sekarang" name="harga_sekarang" required min="0" step="0.01" placeholder="0">
                </div>
                <div class="form-text">Harga beli terbaru/saat ini</div>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>

          <!-- Price Difference Alert -->
          <div class="row">
            <div class="col-12">
              <div id="price-difference" class="alert alert-warning" style="display: none;">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <span id="price-diff-text"></span>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" id="btn-batal-bahan" class="btn btn-secondary">
            <i class="fas fa-times me-1"></i>Batal
          </button>
          <button type="submit" id="submitBtn" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>Simpan Bahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
  /* Dropdown styling yang konsisten dengan form menu */
  .form-select {
    background-color: #fff;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 2.25rem 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    appearance: none;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }

  .form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  }

  .form-select:hover {
    border-color: #b3d7ff;
  }

  /* Option styling */
  .form-select option {
    padding: 0.5rem 0.75rem;
    font-weight: 400;
  }

  .form-select option:first-child {
    color: #6c757d;
    font-style: italic;
  }

  .form-select option:not(:first-child) {
    color: #212529;
    font-weight: 500;
  }

  /* Konsistensi dengan form input lainnya */
  .form-control,
  .form-select {
    height: calc(2.25rem + 2px);
    min-height: calc(2.25rem + 2px);
  }

  /* Styling untuk form label */
  .form-label {
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
  }

  /* Required indicator */
  .text-danger {
    color: #dc3545 !important;
    font-weight: 700;
  }

  /* Form validation styling */
  .form-select.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23dc3545' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
  }

  .form-select.is-invalid:focus {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
  }

  .invalid-feedback {
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
  }
</style>