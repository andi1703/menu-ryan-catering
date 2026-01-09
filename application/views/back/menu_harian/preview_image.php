<div class="modal fade" id="form-modal-preview-image" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="previewImageLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg">
      <!-- Close Button Floating -->
      <button type="button" class="btn-close-custom" data-dismiss="modal" aria-label="Close">
        <i class="fas fa-times"></i>
      </button>

      <!-- Header Modern -->
      <div class="modal-header-custom">
        <div class="header-content">
          <div class="icon-wrapper">
            <i class="fas fa-camera-retro"></i>
          </div>
          <div class="title-wrapper">
            <h5 class="modal-title-custom">Preview Foto Menu</h5>
            <p class="menu-name-custom" id="preview-menu-text">
              <!-- <i class="fas fa-utensils"></i> -->
              <span>Loading...</span>
            </p>
          </div>
        </div>
      </div>

      <!-- Body dengan gradient background -->
      <div class="modal-body-custom">
        <div class="image-container">
          <div id="xpreview_image" class="preview-image-wrapper">
            <!-- Image will be injected here by JS -->
            <div class="loading-state">
              <div class="spinner-custom">
                <i class="fas fa-spinner fa-spin"></i>
              </div>
              <p class="loading-text">Memuat gambar...</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer Modern -->
      <div class="modal-footer-custom">
        <div class="footer-info">
          <i class="fas fa-info-circle me-2"></i>
          <span>Klik di luar area untuk menutup</span>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  /* Modal Custom Styling */
  #form-modal-preview-image .modal-content {
    border-radius: 14px;
    overflow: hidden;
    background: #212529;
  }

  #form-modal-preview-image .modal-lg {
    max-width: 550px;
  }

  /* Close Button Floating */
  .btn-close-custom {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1060;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.95);
    border: none;
    color: #212529;
    font-size: 14px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
  }

  .btn-close-custom:hover {
    background: #fff;
    transform: rotate(90deg) scale(1.1);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
  }

  /* Header Modern */
  .modal-header-custom {
    background-color: #181f27ff;
    backdrop-filter: blur(10px);
    padding: 15px 20px;
    border-bottom: 2px solid #16181b;
  }

  .header-content {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .icon-wrapper {
    width: 45px;
    height: 45px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
  }

  .title-wrapper {
    flex: 1;
  }

  .modal-title-custom {
    font-size: 18px;
    font-weight: 700;
    color: #fff;
    margin: 0;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
  }

  .menu-name-custom {
    margin: 3px 0 0 0;
    font-size: 13px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
  }

  .menu-name-custom i {
    margin-right: 8px;
    color: #ffd700;
  }

  /* Body Modern */
  .modal-body-custom {
    padding: 15px;
    background: rgba(255, 255, 255, 0.98);
    min-height: 250px;
  }

  .image-container {
    position: relative;
    border-radius: 10px;
    overflow: hidden;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  }

  .preview-image-wrapper {
    position: relative;
    min-height: 250px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* Loading State */
  .loading-state {
    text-align: center;
    padding: 30px 15px;
  }

  .spinner-custom {
    font-size: 32px;
    color: #1f252bff;
    margin-bottom: 12px;
  }

  .spinner-custom i {
    animation: spinCustom 1s linear infinite;
  }

  @keyframes spinCustom {
    from {
      transform: rotate(0deg);
    }

    to {
      transform: rotate(360deg);
    }
  }

  .loading-text {
    color: #191e24ff;
    font-size: 16px;
    font-weight: 500;
    margin: 0;
  }

  /* Image Styling */
  #xpreview_image img {
    max-width: 100%;
    height: auto;
    display: block;
    border-radius: 10px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    animation: fadeInImage 0.5s ease-out;
  }

  @keyframes fadeInImage {
    from {
      opacity: 0;
      transform: scale(0.95);
    }

    to {
      opacity: 1;
      transform: scale(1);
    }
  }

  /* Footer Modern */
  .modal-footer-custom {
    background-color: #212529;
    backdrop-filter: blur(10px);
    padding: 12px 20px;
    border-top: 2px solid #16181b;
  }

  .footer-info {
    color: rgba(255, 255, 255, 0.9);
    font-size: 12px;
    text-align: center;
    width: 100%;
  }

  .footer-info i {
    color: #ffd700;
  }

  /* Modal Animation */
  #form-modal-preview-image.fade .modal-dialog {
    transform: scale(0.8) translateY(-50px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
  }

  #form-modal-preview-image.show .modal-dialog {
    transform: scale(1) translateY(0);
    opacity: 1;
  }

  /* Backdrop Custom */
  #form-modal-preview-image .modal-backdrop {
    background-color: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
  }

  /* Responsive */
  @media (max-width: 768px) {
    #form-modal-preview-image .modal-xl {
      max-width: 95%;
      margin: 10px auto;
    }

    .modal-header-custom {
      padding: 20px 15px;
    }

    .header-content {
      gap: 15px;
    }

    .icon-wrapper {
      width: 50px;
      height: 50px;
      font-size: 20px;
    }

    .modal-title-custom {
      font-size: 20px;
    }

    .menu-name-custom {
      font-size: 14px;
    }

    .modal-body-custom {
      padding: 20px 15px;
    }

    .btn-close-custom {
      width: 35px;
      height: 35px;
      font-size: 16px;
    }

    .preview-image-wrapper {
      min-height: 300px;
    }
  }

  /* Hover Effects */
  #xpreview_image img:hover {
    transform: scale(1.02);
    transition: transform 0.3s ease;
  }

  /* Glass Morphism Effect */
  .modal-header-custom,
  .modal-footer-custom {
    position: relative;
  }

  .modal-header-custom::before,
  .modal-footer-custom::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.1);
    pointer-events: none;
  }
</style>