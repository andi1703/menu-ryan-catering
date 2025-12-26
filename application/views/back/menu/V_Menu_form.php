<!-- Modal Form Menu -->
<div class="modal fade" id="form-modal-menu-form" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalMenuLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form id="form-data" method="POST" enctype="multipart/form-data" action="<?= base_url('menu/simpan') ?>">
        <div class="modal-header">
          <h5 class="modal-title text-dark" id="modalMenuLabel">Tambah Menu Kondimen</h5>
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
            <div class="col-md-12">
              <div class="form-group">
                <label for="menu_nama">Nama Menu <span class="text-danger">*</span></label>
                <input type="text" required name="menu_nama" id="menu_nama" class="form-control" placeholder="Masukkan nama menu">
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
            <div class="col-md-12">
              <div class="form-group">
                <label for="id_bahan_utama">Bahan Utama</label>
                <div class="multi-select-wrapper" id="bahanUtamaSelect">
                  <div class="multi-select-box" id="bahanSelectBox">
                    <div id="bahanTagsContainer" class="d-flex flex-wrap">
                      <span class="placeholder-text" id="bahanPlaceholder">Search...</span>
                    </div>
                    <div class="controls">
                      <i class="bi bi-x fs-5 clear-all" id="bahanClearBtn" title="Clear all" style="display:none;"></i>
                      <div class="control-divider" style="display:none;"></div>
                      <i class="bi bi-chevron-down ms-1"></i>
                    </div>
                  </div>
                  <div class="options-menu" id="bahanDropdownMenu">
                    <div class="search-wrapper">
                      <input type="text" class="custom-search-input" id="bahanSearchInput" placeholder="Search..." autocomplete="off">
                    </div>
                    <ul class="options-list" id="bahanOptionsList"></ul>
                  </div>
                </div>
                <input type="hidden" name="id_bahan_utama[]" id="id_bahan_utama" value="">
                <small class="form-text text-muted">Pilih satu atau beberapa bahan utama. Gunakan kolom search untuk mempercepat.</small>
              </div>
            </div>
            <div class="col-md-12">
              <div class="form-group">
                <label for="menu_deskripsi">Deskripsi / Resep / Cara Membuat</label>
                <textarea name="menu_deskripsi" id="menu_deskripsi" class="form-control" rows="8" placeholder="Masukkan deskripsi menu, resep lengkap, dan cara pembuatan...&#10;&#10;Contoh:&#10;Bahan-bahan Resep:&#10;- Nasi putih 200gr&#10;- Ayam 100gr&#10;- Bumbu...&#10;&#10;Cara Membuat:&#10;1. Tumis bumbu hingga harum&#10;2. Masukkan ayam..." style="font-family: monospace; font-size: 13px;"></textarea>
                <small class="form-text text-muted">Anda dapat menuliskan resep lengkap, bahan-bahan resep, dan cara pembuatan menu secara detail di sini.</small>
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