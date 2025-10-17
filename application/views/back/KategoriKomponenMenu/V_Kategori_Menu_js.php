<script type="text/javascript">
	(function() {
		'use strict';

		// CHECK JQUERY
		if (typeof jQuery === 'undefined') {
			console.error('jQuery not found!');
			return;
		}

		// PREVENT MULTIPLE INIT
		if (window.sidebarInitialized) {
			return;
		}
		window.sidebarInitialized = true;

		$(document).ready(function() {
			initSidebar();
			initOtherFunctions();
		});

		// ===== SIDEBAR FUNCTIONS =====
		function initSidebar() {
			$(document).on('click', '#vertical-menu-btn', function(e) {
				e.preventDefault();
				toggleSidebar();
			});

			$(window).on('resize', function() {
				handleResize();
			});

			$(document).on('click', function(e) {
				if ($(window).width() < 992) {
					if (!$(e.target).closest('.vertical-menu, #vertical-menu-btn').length) {
						closeSidebar();
					}
				}
			});

			initSidebarState();
		}

		function toggleSidebar() {
			var $body = $('body');
			var windowWidth = $(window).width();

			if (windowWidth >= 992) {
				$body.toggleClass('sidebar-collapsed');
				var isCollapsed = $body.hasClass('sidebar-collapsed');
				localStorage.setItem('sidebar-collapsed', isCollapsed);

				setTimeout(function() {
					if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
						$('#datatable').DataTable().columns.adjust().responsive.recalc();
					}
				}, 350);
			} else {
				$body.toggleClass('sidebar-open');
			}
		}

		function closeSidebar() {
			$('body').removeClass('sidebar-open');
		}

		function initSidebarState() {
			var $body = $('body');
			var windowWidth = $(window).width();

			$body.removeClass('sidebar-collapsed sidebar-open');

			if (windowWidth >= 992) {
				var isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
				if (isCollapsed) {
					$body.addClass('sidebar-collapsed');
				}
			}
		}

		function handleResize() {
			var $body = $('body');
			var windowWidth = $(window).width();

			if (windowWidth >= 992) {
				$body.removeClass('sidebar-open');
				var isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
				if (isCollapsed) {
					$body.addClass('sidebar-collapsed');
				} else {
					$body.removeClass('sidebar-collapsed');
				}
			} else {
				$body.removeClass('sidebar-collapsed');
			}

			setTimeout(function() {
				if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
					$('#datatable').DataTable().columns.adjust().responsive.recalc();
				}
			}, 100);
		}

		// ===== MAIN FUNCTIONS =====
		function initOtherFunctions() {
			initModals();
			initFormHandlers();
			initButtonHandlers();
			loadData();
		}

		function initModals() {
			$(document).on('click', '#btn_cancel', function(e) {
				e.preventDefault();
				$('#form-modal-kategori-form').modal('hide');
				resetForm();
			});

			$('#form-modal-kategori-form').on('hidden.bs.modal', function() {
				resetForm();
			});
		}

		function initFormHandlers() {
			$('#form-data').on('submit', function(e) {
				e.preventDefault();
				if (!$(this).data('submitting')) {
					submitForm();
				}
			});
		}

		function initButtonHandlers() {
			$(document).on('click', '.btn-edit', function(e) {
				e.preventDefault();
				var id = $(this).data('id');
				if (id) editData(id);
			});

			$(document).on('click', '.btn-delete', function(e) {
				e.preventDefault();
				var id = $(this).data('id');
				if (id) confirmDelete(id, $(this));
			});

			window.tambah_data = function() {
				resetForm();
				$('#modalLabel').text('Tambah Kategori Menu');
				$('#form-modal-kategori-form').modal('show');
				setTimeout(function() {
					$('#nama_kategori').focus();
				}, 500);
			};
		}

		// ===== DATA FUNCTIONS =====
		function loadData() {
			$.ajax({
				type: 'POST',
				url: "<?php echo base_url('kategori-menu/tampil'); ?>",
				dataType: 'json',
				success: function(result) {
					renderDataTable(result);
				},
				error: function() {
					showError('Gagal memuat data. Silakan refresh halaman.');
				}
			});
		}

		function renderDataTable(result) {
			var html = '';
			var no = 1;

			if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
				$('#datatable').DataTable().clear().destroy();
			}

			if (result.show_data && result.show_data.length > 0) {
				result.show_data.forEach(function(item) {
					html += buildTableRow(item, no++);
				});
			} else {
				html = buildEmptyRow();
			}

			$('#show_data_kategori').html(html);

			setTimeout(function() {
				initDataTable();
			}, 100);
		}

		function buildTableRow(item, no) {
			// BUAT VARIABEL DESKRIPSI DENGAN KONDISI
			var deskripsi = item.deskripsi_kategori && item.deskripsi_kategori.trim() !== '' ?
				escapeHtml(item.deskripsi_kategori) :
				'<em class="text-muted">Tidak ada deskripsi</em>';

			// GUNAKAN VARIABEL DESKRIPSI DI TEMPLATE (BUKAN escapeHtml lagi)
			return `
        <tr>
            <td class="text-center col-no">${no}</td>
            <td class="col-nama">${escapeHtml(item.nama_kategori)}</td>
            <td class="col-deskripsi">${deskripsi}</td>
            <td class="text-center col-aksi">
                <div class="table-action-buttons">
                    <button class="btn btn-warning btn-sm btn-edit" data-id="${item.id_kategori}" type="button">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger btn-sm btn-delete" data-id="${item.id_kategori}" type="button">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </td>
        </tr>
    `;
		}

		function buildEmptyRow() {
			return `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-folder-open fa-3x mb-3"></i>
                        <h5>Tidak ada data</h5>
                        <p>Belum ada kategori menu yang ditambahkan</p>
                        <button type="button" class="btn btn-primary btn-sm" onclick="tambah_data()">
                            <i class="ri-add-circle-line me-1"></i>Tambah Kategori Pertama
                        </button>
                    </div>
                </td>
            </tr>
        `;
		}

		function initDataTable() {
			if ($.fn.DataTable) {
				$('#datatable').DataTable({
					responsive: false,
					autoWidth: false,
					scrollX: true,
					scrollCollapse: true,
					order: [
						[1, 'asc']
					],
					columnDefs: [{
							targets: [0, 3],
							orderable: false,
							searchable: false,
							className: 'text-center'
						},
						{
							targets: 0,
							width: '60px',
							className: 'text-center'
						},
						{
							targets: 1,
							width: '200px',
							className: 'text-left'
						},
						{
							targets: 2,
							width: 'auto',
							className: 'text-left'
						},
						{
							targets: 3,
							width: '180px',
							className: 'text-center'
						}
					],
					language: {
						search: 'Cari:',
						lengthMenu: 'Tampilkan _MENU_ data per halaman',
						zeroRecords: 'Tidak ada data yang ditemukan',
						info: 'Menampilkan halaman _PAGE_ dari _PAGES_',
						infoEmpty: 'Tidak ada data tersedia',
						infoFiltered: '(difilter dari _MAX_ total data)',
						paginate: {
							first: 'Pertama',
							last: 'Terakhir',
							next: 'Selanjutnya',
							previous: 'Sebelumnya'
						}
					},
					createdRow: function(row, data, dataIndex) {
						// Force word wrap pada setiap row
						$(row).find('td').each(function() {
							$(this).css({
								'white-space': 'normal',
								'word-wrap': 'break-word',
								'word-break': 'break-word'
							});
						});
					},
					drawCallback: function() {
						this.api().column(0, {
							search: 'applied',
							order: 'applied'
						}).nodes().each(function(cell, i) {
							cell.innerHTML = i + 1;
						});
					},
					initComplete: function() {
						this.api().columns.adjust();
					}
				});
			}
		}

		function editData(id) {
			$.ajax({
				url: "<?php echo base_url('kategori-menu/edit'); ?>",
				type: 'POST',
				data: {
					id: id
				},
				dataType: 'json',
				success: function(result) {
					if (result.status === 'success') {
						$('#stat').val('edit');
						$('#id').val(result.data.id_kategori);
						$('#nama_kategori').val(result.data.nama_kategori);
						$('#deskripsi_kategori').val(result.data.deskripsi_kategori);
						$('#modalLabel').text('Edit Kategori Menu');
						$('#form-modal-kategori-form').modal('show');
						setTimeout(function() {
							$('#nama_kategori').focus();
						}, 500);
					} else {
						showError(result.message || 'Gagal memuat data edit');
					}
				},
				error: function() {
					showError('Gagal memuat data edit');
				}
			});
		}

		function submitForm() {
			var $form = $('#form-data');
			var $submitBtn = $('#btn_save');

			$form.data('submitting', true);
			$submitBtn.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin me-1"></i> Menyimpan...');

			$.ajax({
				url: "<?php echo base_url('kategori-menu/save'); ?>",
				type: 'POST',
				data: new FormData($form[0]),
				dataType: 'json',
				processData: false,
				contentType: false,
				success: function(result) {
					if (result.status === 'success') {
						$('#form-modal-kategori-form').modal('hide');
						showSuccess(result.message || 'Data berhasil disimpan');
						setTimeout(loadData, 500);
					} else {
						showError(result.message || 'Gagal menyimpan data');
					}
				},
				error: function() {
					showError('Terjadi kesalahan pada server');
				},
				complete: function() {
					$form.data('submitting', false);
					$submitBtn.prop('disabled', false).html('<i class="bx bx-save me-1"></i> Simpan');
				}
			});
		}

		function confirmDelete(id, button) {
			if (typeof Swal !== 'undefined') {
				Swal.fire({
					title: 'Hapus Kategori?',
					text: 'Data yang dihapus tidak dapat dikembalikan!',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#d33',
					cancelButtonColor: '#3085d6',
					confirmButtonText: 'Ya, Hapus!',
					cancelButtonText: 'Batal',
					reverseButtons: true
				}).then((result) => {
					if (result.isConfirmed) {
						deleteData(id, button);
					}
				});
			} else {
				if (confirm('Hapus kategori?\nData yang dihapus tidak dapat dikembalikan!')) {
					deleteData(id, button);
				}
			}
		}

		function deleteData(id, button) {
			var originalHtml = button.html();
			button.prop('disabled', true).html('<i class="bx bx-loader-alt bx-spin"></i>');

			$.ajax({
				url: "<?php echo base_url('kategori-menu/delete'); ?>",
				type: 'POST',
				data: {
					id: id
				},
				dataType: 'json',
				success: function(response) {
					if (response.status === 'success') {
						showSuccess(response.message || 'Data berhasil dihapus');
						setTimeout(loadData, 500);
					} else {
						showError(response.message || 'Gagal menghapus data');
						button.prop('disabled', false).html(originalHtml);
					}
				},
				error: function() {
					showError('Terjadi kesalahan pada server');
					button.prop('disabled', false).html(originalHtml);
				}
			});
		}

		function resetForm() {
			$('#form-data')[0].reset();
			$('#stat').val('new');
			$('#id').val('');
			$('#form-data').data('submitting', false);
			$('.form-control').removeClass('is-invalid');
			$('.invalid-feedback').remove();
		}

		function showSuccess(message) {
			if (typeof Swal !== 'undefined') {
				Swal.fire({
					title: 'Berhasil!',
					text: message,
					icon: 'success',
					timer: 2000,
					showConfirmButton: false,
					toast: true,
					position: 'top-end'
				});
			} else {
				alert('✅ ' + message);
			}
		}

		function showError(message) {
			if (typeof Swal !== 'undefined') {
				Swal.fire({
					title: 'Error!',
					text: message,
					icon: 'error',
					timer: 3000,
					showConfirmButton: false,
					toast: true,
					position: 'top-end'
				});
			} else {
				alert('❌ ' + message);
			}
		}

		function escapeHtml(text) {
			var map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text ? text.replace(/[&<>"']/g, function(m) {
				return map[m];
			}) : '';
		}

	})();
</script>