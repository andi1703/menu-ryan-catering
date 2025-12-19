<!-- Modal Preview Deskripsi Menu -->
<style>
  /* Bahan list formatting */
  #previewBahanUtama .bahan-list {
    margin: 0;
    padding-left: 1.25rem;
    /* ordered list indent */
  }

  #previewBahanUtama .bahan-list li {
    margin-bottom: 4px;
  }

  /* Responsive grid for bahan list: 1 / 2 / 3 columns */
  #previewBahanUtama .bahan-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 8px;
  }

  @media (min-width: 768px) {
    #previewBahanUtama .bahan-grid {
      grid-template-columns: repeat(2, 1fr);
    }
  }

  @media (min-width: 992px) {
    #previewBahanUtama .bahan-grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  /* Description box typography */
  #previewDeskripsiContent {
    white-space: pre-wrap;
    min-height: 220px;
    font-size: 14px;
    line-height: 1.7;
  }

  /* Unify title colors */
  .modal-header .modal-title {
    color: #fff !important;
  }

  /* Section titles inside modal body */
  .section-title {
    color: #212529;
    /* match default heading/text color */
  }
</style>
<div class="modal fade" id="modalPreviewDeskripsi" tabindex="-1" role="dialog" aria-labelledby="modalPreviewDeskripsiLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="modalPreviewDeskripsiLabel">
          Deskripsi Menu
        </h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="mb-4 text-center">
          <div class="d-inline-block bg-light border rounded p-3">
            <img id="previewMenuImage" src="" alt="Menu Image" class="img-fluid rounded" style="max-height: 220px; display: none; object-fit: cover;">
            <div id="previewMenuImagePlaceholder" class="text-muted fst-italic">Tidak ada gambar</div>
          </div>
        </div>

        <div class="mb-4">
          <h6 class="text-muted mb-2">Nama Menu:</h6>
          <h5 id="previewMenuName" class="mb-3 font-weight-bold"></h5>
        </div>

        <!-- <hr class="my-3"> -->
        <!-- Bahan Utama Section -->
        <div class="mb-4" id="bahanUtamaSection" style="display:none;">
          <h6 class="text-muted mb-2">Bahan Utama:</h6>
          <div class="alert alert-success mb-0" style="background-color: #d4edda; border-color: #c3e6cb; padding: 10px;">
            <div id="previewBahanUtama"></div>
          </div>
        </div>

        <div>
          <h6 class="text-muted mb-2">Deskripsi / Resep / Cara Membuat:</h6>
          <div id="previewDeskripsiContent" class="p-4 bg-light rounded border"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>