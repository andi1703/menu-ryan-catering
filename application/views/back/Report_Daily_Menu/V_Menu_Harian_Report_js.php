<script type="text/javascript">
  $(document).ready(function() {
    var $dropdownButton = $('#kantin-dropdown');

    function updateKantinState() {
      var count = $('.kantin-checkbox:checked').length;
      $('#kantin-selected-count').text(count > 0 ? count + ' Kantin Dipilih' : '- Pilih Kantin -');
      $dropdownButton.toggleClass('has-selection', count > 0);
    }

    $(document).on('change', '.kantin-checkbox', updateKantinState);

    $('#selectAllKantin').on('click', function(e) {
      e.preventDefault();
      $('.kantin-checkbox').prop('checked', true);
      updateKantinState();
    });

    $('#deselectAllKantin').on('click', function(e) {
      e.preventDefault();
      $('.kantin-checkbox').prop('checked', false);
      updateKantinState();
    });

    $('.dropdown-menu').on('click', function(e) {
      e.stopPropagation();
    });

    if ($('.alert-warning').length > 0) {
      $('html, body').animate({
        scrollTop: $('.alert-warning').offset().top - 100
      }, 500);
    }

    updateKantinState();
  });
</script>

<script>
  function printReport() {
    // Ambil data yang sudah difilter
    var tanggal = $('#tanggal').val();
    var shift = $('#shift-select').val();
    var customer = $('#customer-select option:selected').text();
    var customerId = $('#customer-select').val();

    // Validasi minimal filter
    if (!tanggal) {
      Swal.fire({
        icon: 'warning',
        title: 'Perhatian!',
        text: 'Silakan pilih tanggal terlebih dahulu',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Ambil kantin yang dipilih
    var selectedKantins = [];
    $('.kantin-checkbox:checked').each(function() {
      selectedKantins.push($(this).val());
    });

    if (selectedKantins.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'Perhatian!',
        text: 'Silakan pilih minimal 1 kantin',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Generate print window
    var printWindow = window.open('', '_blank');
    var printContent = generatePrintHTML();

    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.focus();

    setTimeout(function() {
      printWindow.print();
    }, 500);
  }

  function generatePrintHTML() {
    var tanggal = $('#tanggal').val();
    var shift = $('#shift-select').val();
    var customer = $('#customer-select option:selected').text();

    var html = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Menu Harian - Ryan Catering</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 9px;
            line-height: 1.3;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
        }
        
        .header h2 {
            font-size: 14px;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .header .company {
            font-size: 11px;
            color: #555;
            margin-bottom: 5px;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 9px;
        }
        
        .info-table td {
            padding: 2px 5px;
        }
        
        .info-table td:first-child {
            width: 120px;
            font-weight: bold;
        }
        
        .customer-section {
            margin-bottom: 10px;
            page-break-inside: avoid;
        }
        
        .customer-title {
            background: #007bff;
            color: white;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 8px;
        }
        
        table.data-table th {
            background: #4a4a4a;
            color: white;
            padding: 4px 3px;
            border: 1px solid #333;
            text-align: center;
            font-size: 8px;
            font-weight: bold;
        }
        
        table.data-table td {
            padding: 3px;
            border: 1px solid #ddd;
            text-align: center;
        }
        
        table.data-table td:nth-child(2) {
            text-align: left;
            padding-left: 5px;
        }
        
        table.data-table td:nth-child(3) {
            text-align: left;
        }
        
        table.data-table tbody tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        table.data-table tbody tr:hover {
            background: #f0f0f0;
        }
        
        table.data-table tfoot {
            background: #333;
            color: white;
            font-weight: bold;
        }
        
        table.data-table tfoot td {
            border-color: #333;
        }
        
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: bold;
            white-space: nowrap;
        }
        
        .badge-primary { background: #007bff; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: black; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-danger { background: #dc3545; color: white; }
        .badge-secondary { background: #6c757d; color: white; }
        
        .total-row {
            background: #fffacd !important;
            font-weight: bold;
        }
        
        @media print {
            body { margin: 0; }
            .customer-section { page-break-inside: avoid; }
            @page { margin: 8mm; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Menu Harian</h2>
        <div class="company">Ryan Catering</div>
    </div>
    
    <table class="info-table">
        <tr>
            <td>Tanggal Cetak:</td>
            <td>${formatDateIndo(new Date())}</td>
            <td>Tanggal:</td>
            <td>${formatDateIndo(tanggal)}</td>
        </tr>
        <tr>
            <td>Customer:</td>
            <td>${customer || 'Semua Customer'}</td>
            <td>Shift:</td>
            <td>${shift ? shift.toUpperCase() : 'Semua Shift'}</td>
        </tr>
    </table>
`;

    // Loop untuk setiap customer di accordion
    $('.accordion-item').each(function() {
      var customerName = $(this).find('.accordion-button strong').text().trim();
      var table = $(this).find('table');

      html += `
    <div class="customer-section">
        <div class="customer-title">${customerName}</div>
        <table class="data-table">
            <thead>
                <tr>
`;

      // Copy header
      table.find('thead th').each(function() {
        var thText = $(this).text().trim();
        html += `<th>${thText}</th>`;
      });

      html += `
                </tr>
            </thead>
            <tbody>
`;

      // Copy body
      table.find('tbody tr').each(function() {
        html += '<tr>';
        $(this).find('td').each(function(index) {
          var tdText = $(this).text().trim();
          if (index === 2) { // Kolom kategori
            var kategori = tdText;
            html += `<td>${getKategoriBadgeHTML(kategori)}</td>`;
          } else {
            html += `<td>${tdText}</td>`;
          }
        });
        html += '</tr>';
      });

      html += `
            </tbody>
            <tfoot>
                <tr>
`;

      // Copy footer
      table.find('tfoot th').each(function() {
        var thText = $(this).text().trim();
        html += `<td>${thText}</td>`;
      });

      html += `
                </tr>
            </tfoot>
        </table>
    </div>
`;
    });

    html += `
</body>
</html>
`;

    return html;
  }

  function getKategoriBadgeHTML(kategori) {
    var badgeClass = 'badge badge-secondary';
    var kategoriLower = kategori.toLowerCase();

    if (kategoriLower.includes('lauk utama')) {
      badgeClass = 'badge badge-primary';
    } else if (kategoriLower.includes('pendamping kering')) {
      badgeClass = 'badge badge-warning';
    } else if (kategoriLower.includes('pendamping basah')) {
      badgeClass = 'badge badge-info';
    } else if (kategoriLower.includes('sayur')) {
      badgeClass = 'badge badge-success';
    } else if (kategoriLower.includes('buah')) {
      badgeClass = 'badge badge-danger';
    } else if (kategoriLower.includes('nasi')) {
      badgeClass = 'badge badge-secondary';
    }

    return `<span class="${badgeClass}">${kategori}</span>`;
  }

  function formatDateIndo(date) {
    if (typeof date === 'string') {
      var parts = date.split('-');
      if (parts.length === 3) {
        date = new Date(parts[0], parts[1] - 1, parts[2]);
      }
    }

    var bulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
      'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    return d + ' ' + bulan[m] + ' ' + y;
  }
</script>