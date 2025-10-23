<!-- Modal Form Menu -->
<div class="modal fade" id="form-modal-menu-form" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalMenuLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="form-data" method="POST" enctype="multipart/form-data" action="<?= base_url('menu/simpan') ?>">
        <div class="modal-header">
          <h5 class="modal-title" id="modalMenuLabel">Tambah Menu</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
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
                <label for="id_thematik">Thematik (Opsional)</label>
                <select name="id_thematik" id="id_thematik" class="form-control">
                  <option value="">-- Pilih Thematik --</option>
                  <!-- Opsi thematik dinamis -->
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
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light waves-effect" data-dismiss="modal">Close</button>
          <button type="submit" id="btn_save" class="btn btn-primary waves-effect waves-light">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>