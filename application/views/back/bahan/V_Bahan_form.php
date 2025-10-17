<div class="modal fade" id="form-modal-bahan" tabindex="-1" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalBahanLabel">
          <i class="fas fa-carrot me-2"></i>Tambah Bahan Baku
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="formBahan" method="post" autocomplete="off">
        <input type="hidden" name="stat" id="stat" value="add">
        <input type="hidden" name="id" id="id" value="">

        <div class="modal-body">
          <div class="row">
            <div class="col-12">
              <div class="alert alert-info d-flex align-items-center" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <div>
                  <strong>Petunjuk:</strong> Masukkan data bahan baku dengan lengkap dan akurat. Harga akan digunakan untuk menghitung biaya produksi menu.
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-8">
              <div class="mb-3">
                <label for="nama_bahan" class="form-label">
                  <i class="fas fa-tag me-1"></i>Nama Bahan <span class="text-danger">*</span>
                </label>
                <input type="text" required name="nama_bahan" id="nama_bahan" class="form-control form-control-lg" placeholder="Masukkan nama bahan (contoh: Wortel Segar, Kentang Merah)" maxlength="100">
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="col-md-4">
              <div class="mb-3">
                <label for="id_satuan" class="form-label">
                  <i class="fas fa-balance-scale me-1"></i>Satuan <span class="text-danger">*</span>
                </label>
                <select name="id_satuan" id="id_satuan" class="form-select form-select-lg" required>
                  <option value="">-- Pilih Satuan --</option>
                  <?php if (isset($satuan_list)) : ?>
                    <?php foreach ($satuan_list as $s) : ?>
                      <option value="<?= $s['id_satuan'] ?>"><?= htmlspecialchars($s['nama_satuan']) ?></option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="harga_awal" class="form-label">
                  <i class="fas fa-money-bill-wave me-1"></i>Harga Awal <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">Rp</span>
                  <input type="number" required name="harga_awal" id="harga_awal" class="form-control" placeholder="0" min="0" step="1">
                </div>
                <small class="form-text text-muted">Harga beli pertama kali untuk referensi</small>
                <div class="invalid-feedback"></div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="harga_sekarang" class="form-label">
                  <i class="fas fa-coins me-1"></i>Harga Sekarang <span class="text-danger">*</span>
                </label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text">Rp</span>
                  <input type="number" required name="harga_sekarang" id="harga_sekarang" class="form-control" placeholder="0" min="0" step="1">
                </div>
                <small class="form-text text-muted">Harga terbaru untuk menghitung biaya produksi</small>
                <div class="invalid-feedback"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <div class="mb-3">
                <label for="keterangan" class="form-label">
                  <i class="fas fa-comment me-1"></i>Keterangan (Opsional)
                </label>
                <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Tambahkan keterangan khusus tentang bahan ini..." maxlength="500"></textarea>
                <small class="form-text text-muted">Maksimal 500 karakter</small>
              </div>
            </div>
          </div>

          <!-- Price difference indicator -->
          <div class="row" id="price-difference" style="display: none;">
            <div class="col-12">
              <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <div id="price-diff-text"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary" id="submitBtn">
            <i class="fas fa-save me-1"></i>Simpan Bahan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>