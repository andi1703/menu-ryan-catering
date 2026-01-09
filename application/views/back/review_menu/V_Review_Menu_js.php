<script>
  (function() {
    'use strict';

    const base_url = $('#ajax_url').val();
    const file_url = '<?= base_url("file/products/menukondimen/") ?>';

    // Pagination state
    let currentPage = 1;
    let totalPages = 1;
    let allMenuData = [];
    let perPage = 12;

    // =============================================
    // UTILITY FUNCTIONS
    // =============================================
    function getShiftBadge(shift) {
      const badges = {
        'lunch': {
          color: 'success',
          icon: 'sun',
          label: 'Lunch'
        },
        'dinner': {
          color: 'warning',
          icon: 'cloud-sun',
          label: 'Dinner'
        },
        'supper': {
          color: 'secondary',
          icon: 'moon',
          label: 'Supper'
        }
      };
      const config = badges[shift] || {
        color: 'secondary',
        icon: 'clock',
        label: shift
      };
      return `<span class="badge bg-${config.color} menu-info-badge">
              <i class="fas fa-${config.icon}"></i> ${config.label}
            </span>`;
    }

    function getKategoriBadge(kategori) {
      if (!kategori) return '<span class="badge bg-secondary badge-kategori">-</span>';

      const colors = {
        'nasi': '#ffc107',
        'lauk utama': '#dc3545',
        'lauk_utama': '#dc3545',
        'pendamping basah': '#28a745',
        'pendamping_basah': '#28a745',
        'pendamping kering': '#17a2b8',
        'pendamping_kering': '#17a2b8',
        'pendamping gantin': '#20c997',
        'pendamping_gantin': '#20c997',
        'sayuran berkuah': '#20c997',
        'sayuran_berkuah': '#20c997',
        'buah': '#e83e8c',
        'sambal': '#fd7e14',
        'tumisan': '#6f42c1'
      };

      const key = kategori.toLowerCase().trim();
      const color = colors[key] || '#6c757d';

      return `<span class="badge badge-kategori" style="background-color: ${color}; color: white;">${kategori}</span>`;
    }

    function formatDate(dateStr) {
      if (!dateStr) return '-';
      const parts = dateStr.split('-');
      if (parts.length === 3) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        return `${parts[2]} ${months[parseInt(parts[1]) - 1]} ${parts[0]}`;
      }
      return dateStr;
    }

    // =============================================
    // BUILD MENU CARD
    // =============================================
    function buildMenuCard(menu) {
      // Foto Menu
      const fotoHtml = menu.foto_menu ?
        `<img src="${file_url}${menu.foto_menu}" class="menu-card-img" alt="${menu.nama_menu}" onerror="this.parentElement.innerHTML='<div class=\\'menu-card-img-placeholder\\'><i class=\\'fas fa-utensils\\'></i></div>';">` :
        `<div class="menu-card-img-placeholder"><i class="fas fa-utensils"></i></div>`;

      // Kondimen List (max 5 items di card)
      let kondimenHtml = '';
      if (menu.kondimen_list && menu.kondimen_list.length > 0) {
        const displayKondimen = menu.kondimen_list.slice(0, 5);
        kondimenHtml = displayKondimen.map((k, idx) => `
        <div class="kondimen-item">
          <span class="kondimen-name">
            <strong>${idx + 1}.</strong> ${k.nama_kondimen || '-'}
          </span>
          ${getKategoriBadge(k.kategori_kondimen)}
        </div>
      `).join('');

        if (menu.kondimen_list.length > 5) {
          kondimenHtml += `
          <div class="kondimen-more">
            <i class="fas fa-plus-circle me-1"></i>
            +${menu.kondimen_list.length - 5} kondimen lainnya
          </div>
        `;
        }
      } else {
        kondimenHtml = '<p class="text-muted fst-italic small mb-0">Tidak ada kondimen</p>';
      }

      return `
      <div class="col-xxl-2 col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-4">
        <div class="card menu-card" data-menu='${JSON.stringify(menu).replace(/'/g, "&#39;")}'>
          <div class="menu-card-img-wrapper">
            ${fotoHtml}
          </div>
          <div class="menu-card-body">
            <h6 class="menu-card-title" style="margin-bottom: 5px;">${menu.nama_menu}</h6>
            <div class="d-flex justify-content-between align-items-center mb-3">
              <span class="badge bg-info text-white menu-info-badge fw-bold">
                ${menu.jenis_menu || '-'}
              </span>
              <strong class="text-dark text-end" style="font-size: 0.85rem;">
                ${menu.customers || '-'}
              </strong>
            </div>
            <p class="mb-2"><strong>Kondimen :</strong></p>
            <div class="kondimen-list-container">
              ${kondimenHtml}
            </div>
          </div>
          <div class="card-footer-custom">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                Dibuat ${menu.total_dibuat || '0'}x
              </small>
            </div>
          </div>
        </div>
      </div>
    `;
    }

    // =============================================
    // LOAD MENU DATA
    // =============================================
    function loadMenuData(page = 1) {
      currentPage = page;
      const filters = {
        id_customer: $('#filter-customer').val(),
        jenis_menu: $('#filter-jenis').val(),
        search: $('#filter-search').val()
      };

      $('#menu-grid-container').html(`
      <div class="col-12 loading-state">
        <div class="circle-loader">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
        <p class="mt-3 text-muted fs-5">Memuat data menu...</p>
      </div>
    `);
      $('#pagination-wrapper').hide();

      $.ajax({
        url: base_url + '/get_menu_list',
        type: 'POST',
        data: filters,
        dataType: 'json',
        success: function(response) {
          if (response.status === 'success' && response.data.length > 0) {
            allMenuData = response.data;
            totalPages = Math.ceil(allMenuData.length / perPage);
            renderPage(currentPage);
            renderPagination();
            $('#pagination-wrapper').show();
          } else {
            $('#menu-grid-container').html(`
            <div class="col-12 empty-state">
              <i class="fas fa-search text-muted"></i>
              <p class="text-muted fs-5 mb-0">Tidak ada menu ditemukan</p>
              <p class="text-muted small">Coba ubah filter atau kata kunci pencarian</p>
            </div>
          `);
            $('#pagination-wrapper').hide();
          }
        },
        error: function(xhr, status, error) {
          console.error('Error loading menu:', error);
          $('#menu-grid-container').html(`
          <div class="col-12 empty-state">
            <i class="fas fa-exclamation-triangle text-danger"></i>
            <p class="text-danger fs-5 mb-0">Gagal memuat data menu</p>
            <p class="text-muted small">${error}</p>
          </div>
        `);
          $('#pagination-wrapper').hide();
        }
      });
    }

    // =============================================
    // RENDER PAGE (Display current page items)
    // =============================================
    function renderPage(page) {
      const start = (page - 1) * perPage;
      const end = start + perPage;
      const pageData = allMenuData.slice(start, end);

      const html = pageData.map(menu => buildMenuCard(menu)).join('');
      $('#menu-grid-container').html(html);

      // Update pagination info
      const showingStart = start + 1;
      const showingEnd = Math.min(end, allMenuData.length);
      $('#pagination-info-text').text(`Menampilkan ${showingStart}-${showingEnd} dari ${allMenuData.length} menu`);

      // Scroll to top
      $('html, body').animate({
        scrollTop: $('#menu-grid-container').offset().top - 100
      }, 300);
    }

    // =============================================
    // RENDER PAGINATION CONTROLS
    // =============================================
    function renderPagination() {
      const maxButtons = 5;
      let html = '';

      // Previous button
      html += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
          <a class="page-link" href="#" data-page="${currentPage - 1}">
            <i class="fas fa-chevron-left"></i>
          </a>
        </li>
      `;

      // Calculate page range
      let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
      let endPage = Math.min(totalPages, startPage + maxButtons - 1);

      if (endPage - startPage < maxButtons - 1) {
        startPage = Math.max(1, endPage - maxButtons + 1);
      }

      // First page
      if (startPage > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
        if (startPage > 2) {
          html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
      }

      // Page numbers
      for (let i = startPage; i <= endPage; i++) {
        html += `
          <li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
          </li>
        `;
      }

      // Last page
      if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
          html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
      }

      // Next button
      html += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
          <a class="page-link" href="#" data-page="${currentPage + 1}">
            <i class="fas fa-chevron-right"></i>
          </a>
        </li>
      `;

      $('#pagination-controls').html(html);
    }

    // =============================================
    // SHOW DETAIL MODAL
    // =============================================
    function showDetailModal(menu) {
      $('#modal-title-text').text(menu.nama_menu);

      // Build foto
      const fotoHtml = menu.foto_menu ?
        `<img src="${file_url}${menu.foto_menu}" class="modal-img-preview" alt="${menu.nama_menu}">` :
        '';

      // Build kondimen table
      let kondimenTableHtml = '';
      if (menu.kondimen_list && menu.kondimen_list.length > 0) {
        kondimenTableHtml = `
        <table class="table table-sm table-bordered kondimen-table-detail">
          <thead>
            <tr>
              <th width="50" class="text-center">#</th>
              <th>Kondimen</th>
              <th width="200">Kategori</th>
            </tr>
          </thead>
          <tbody>
            ${menu.kondimen_list.map((k, idx) => `
              <tr>
                <td class="text-center">${idx + 1}</td>
                <td>${k.nama_kondimen || '-'}</td>
                <td class="text-center">${getKategoriBadge(k.kategori_kondimen)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
      `;
      } else {
        kondimenTableHtml = '<p class="text-muted fst-italic">Tidak ada kondimen</p>';
      }

      const content = `
      ${fotoHtml}
      <table class="table table-borderless detail-table mb-3">
        <tbody>
          <tr>
            <th>Jenis Menu:</th>
            <td><span class="badge bg-info text-white">${menu.jenis_menu || '-'}</span></td>
          </tr>
          <tr>
            <th>Customer:</th>
            <td>${menu.customers || '-'}</td>
          </tr>
          <tr>
            <th>Total Dibuat:</th>
            <td><span class="badge bg-success">${menu.total_dibuat || '0'} kali</span></td>
          </tr>
        </tbody>
      </table>
      <hr>
      <h6 class="mb-3">Daftar Kondimen</h6>
      ${kondimenTableHtml}
    `;

      $('#modal-detail-content').html(content);

      // Show modal using jQuery
      $('#modal-detail-menu').modal('show');
    }

    // =============================================
    // DEBOUNCE FUNCTION
    // =============================================
    function debounce(func, wait) {
      let timeout;
      return function executedFunction(...args) {
        const later = () => {
          clearTimeout(timeout);
          func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
      };
    }

    // =============================================
    // DOCUMENT READY
    // =============================================
    $(document).ready(function() {
      // Initialize Select2
      $('.select2-dropdown').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: function() {
          return $(this).find('option:first').text();
        },
        allowClear: false
      });

      // Load initial data
      loadMenuData();

      // Filter triggers
      $('#filter-customer').on('change', function() {
        loadMenuData();
      });

      $('#filter-jenis').on('change', function() {
        loadMenuData();
      });

      $('#filter-search').on('keyup', debounce(function() {
        loadMenuData();
      }, 500));

      // Reset filter
      $('#btn-reset-filter').on('click', function() {
        $('#filter-customer, #filter-jenis').val('').trigger('change');
        $('#filter-search').val('');
        loadMenuData(1);
      });

      // Pagination click
      $(document).on('click', '#pagination-controls .page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page && page !== currentPage && page >= 1 && page <= totalPages) {
          renderPage(page);
          currentPage = page;
          renderPagination();
        }
      });

      // Per page change
      $('#per-page-select').on('change', function() {
        perPage = parseInt($(this).val());
        currentPage = 1;
        totalPages = Math.ceil(allMenuData.length / perPage);
        renderPage(currentPage);
        renderPagination();
      });

      // Click card to show detail
      $(document).on('click', '.menu-card', function() {
        try {
          const menuDataStr = $(this).attr('data-menu');
          const menuData = JSON.parse(menuDataStr);
          showDetailModal(menuData);
        } catch (e) {
          console.error('Error parsing menu data:', e);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Gagal membuka detail menu'
          });
        }
      });
    });

  })();
</script>