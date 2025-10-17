<!-- Modal Form Menu -->
<div class="modal fade" id="modal-form" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalLabel">
          <i class="ri-restaurant-line me-2"></i>Form Menu
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <form id="form-menu" enctype="multipart/form-data">
        <div class="modal-body">
          <input type="hidden" name="id_menu" id="id_menu">

          <div class="row">
            <div class="col-md-12" hidden>
              <input type="hidden" name="id" id="id" value="">
              <input type="hidden" name="stat" id="stat" value="new">
              <input type="hidden" id="id_komponen" name="id_komponen">
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="menu_nama">Nama Menu <span class="text-danger">*</span></label>
                <input type="text" required name="menu_nama" id="menu_nama" class="form-control" placeholder="Masukkan nama menu">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="menu_harga">Harga Menu</label>
                <input type="number" min="0" name="menu_harga" id="menu_harga" class="form-control" placeholder="Masukkan harga menu">
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="id_kategori">Kategori <span class="text-danger">*</span></label>
                <select name="id_kategori" id="id_kategori" required class="form-control">
                  <option value="">-- Pilih Kategori --</option>
                  <!-- Opsi kategori dinamis -->
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="id_thematik">Thematic (Opsional)</label>
                <select name="id_thematik" id="id_thematik" class="form-control">
                  <option value="">-- Pilih Thematic --</option>
                  <!-- Opsi thematic dinamis -->
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="status_aktif">Status</label>
                <select name="status_aktif" id="status_aktif" class="form-control">
                  <option value="1">Aktif</option>
                  <option value="0">Tidak Aktif</option>
                </select>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="menu_deskripsi">Deskripsi Menu</label>
                <textarea name="menu_deskripsi" id="menu_deskripsi" class="form-control" rows="4" placeholder="Masukkan deskripsi menu (opsional)"></textarea>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="menu_gambar">Gambar Menu</label>
                <input type="file" id="menu_gambar" name="menu_gambar" class="form-control" accept="image/*">
                <small class="form-text text-muted">Format: JPG, PNG, GIF. Maksimal 2MB.</small>
              </div>
              <div id="image-preview" class="text-center" style="display: none;">
                <img id="preview-img" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 2px solid #e3e6f0;">
                <div class="mt-2">
                  <button type="button" class="btn btn-sm btn-danger" onclick="removeImagePreview()">
                    <i class="fas fa-times"></i> Hapus Preview
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- SECTION BAHAN - TAMBAHKAN SETELAH FIELD EXISTING -->
          <div class="row mb-3">
            <div class="col-12">
              <div class="card border">
                <div class="card-header bg-light">
                  <h6 class="mb-0">
                    <i class="ri-archive-line me-2"></i>Bahan-Bahan Menu
                  </h6>
                </div>
                <div class="card-body">
                  <!-- Select2 Multiple untuk Bahan -->
                  <div class="mb-3">
                    <label class="form-label">Pilih Bahan</label>
                    <select class="form-select" id="select-bahan" multiple="multiple" style="width: 100%">
                      <?php if (isset($bahan_list) && !empty($bahan_list)) : ?>
                        <?php foreach ($bahan_list as $bahan) : ?>
                          <option value="<?= $bahan->id_bahan ?>" data-nama="<?= htmlspecialchars($bahan->nama_bahan) ?>" data-satuan="<?= htmlspecialchars($bahan->nama_satuan) ?>" data-harga="<?= $bahan->harga_sekarang ?>">
                            <?= $bahan->nama_bahan ?> (<?= $bahan->nama_satuan ?>) - Rp <?= number_format($bahan->harga_sekarang, 0, ',', '.') ?>
                          </option>
                        <?php endforeach; ?>
                      <?php endif; ?>
                    </select>
                    <small class="text-muted">Pilih satu atau lebih bahan untuk menu ini</small>
                  </div>

                  <!-- Tabel Detail Bahan yang Dipilih -->
                  <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="table-bahan-detail">
                      <thead class="table-light">
                        <tr>
                          <th width="5%">No</th>
                          <th width="35%">Nama Bahan</th>
                          <th width="15%">Satuan</th>
                          <th width="15%">Qty</th>
                          <th width="20%">Harga Satuan</th>
                          <th width="10%">Aksi</th>
                        </tr>
                      </thead>
                      <tbody id="tbody-bahan-detail">
                        <tr class="text-center text-muted" id="empty-row">
                          <td colspan="6">
                            <i class="ri-inbox-line me-2"></i>Belum ada bahan dipilih
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Summary -->
                  <div class="row mt-3">
                    <div class="col-md-6 offset-md-6">
                      <div class="card bg-light">
                        <div class="card-body p-3">
                          <div class="d-flex justify-content-between mb-2">
                            <strong>Total Bahan:</strong>
                            <span id="total-bahan-count">0 item</span>
                          </div>
                          <div class="d-flex justify-content-between">
                            <strong>Estimasi Biaya:</strong>
                            <span id="total-bahan-harga" class="text-primary fw-bold">Rp 0</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="ri-close-line me-1"></i>Batal
          </button>
          <button type="submit" class="btn btn-primary" id="btn-simpan">
            <i class="ri-save-line me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Bahan -->
<div class="modal fade" id="modal-detail-bahan" tabindex="-1" aria-labelledby="modalDetailBahanLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="modalDetailBahanLabel">
          <i class="ri-file-list-line me-2"></i>Detail Bahan Menu
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="detail-bahan-content">
        <div class="text-center">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>