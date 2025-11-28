(function () {
	"use strict";

	if (typeof jQuery === "undefined") {
		if (window.console && typeof window.console.error === "function") {
			console.error("menu_harian.js: jQuery tidak ditemukan.");
		}
		return;
	}
	if (window.menuHarianInitialized) return;
	window.menuHarianInitialized = true;

	var base_url = $("#ajax_url").val();
	var kondimenList = [];
	var menuList = [];
	var isRendering = false;
	var renderTimeout = null;

	// Toast & error popup (SAMA SEPERTI V_Menu_js.php)
	function showSuccess(message) {
		if (typeof Swal !== "undefined") {
			Swal.fire({
				title: "Berhasil!",
				text: message,
				icon: "success",
				timer: 2000,
				showConfirmButton: false,
				toast: true,
				position: "top-end",
			});
		} else {
			alert("✅ " + message);
		}
	}

	function showError(message) {
		if (typeof Swal !== "undefined") {
			Swal.fire({
				title: "Error!",
				text: message,
				icon: "error",
				timer: 3000,
				showConfirmButton: false,
				toast: true,
				position: "top-end",
			});
		} else {
			alert("❌ " + message);
		}
	}

	// Style tweaks for Select2
	(function () {
		var css =
			"\n.select2-container{width:100% !important;}\n.select2-container .select2-selection--single{height:38px;}\n.select2-dropdown{z-index:2100 !important;}\n";
		var style = document.createElement("style");
		style.type = "text/css";
		if (style.styleSheet) style.styleSheet.cssText = css;
		else style.appendChild(document.createTextNode(css));
		document.getElementsByTagName("head")[0].appendChild(style);
	})();

	function renderKantinRadio(kantinList) {
		var html = "";
		if (Array.isArray(kantinList) && kantinList.length > 0) {
			kantinList.forEach(function (kantin) {
				html +=
					"<div class='form-check'>" +
					"<input class='form-check-input kantin-checkbox' type='checkbox' name='id_kantin[]' id='kantin_" +
					kantin.id_kantin +
					"' data-nama='" +
					(kantin.nama_kantin || "") +
					"' value='" +
					kantin.id_kantin +
					"'>" +
					"<label class='form-check-label' for='kantin_" +
					kantin.id_kantin +
					"'>" +
					kantin.nama_kantin +
					"</label>" +
					"</div>";
			});
		}
		$("#kantin-radio-group").html(html);
	}

	function getSelectedKantins() {
		var selected = [];
		$(".kantin-checkbox:checked").each(function () {
			var id = $(this).val();
			var nama =
				$(this).data("nama") || $('label[for="kantin_' + id + '"]').text();
			selected.push({ id_kantin: id, nama_kantin: nama });
		});
		return selected;
	}

	$(document).ready(function () {
		loadMenuHarianData();
		loadCustomerOptions();
		loadKantinRadioOptions();
		loadMenuList();

		// Event delegation untuk checkbox kantin
		$(document).on("change", ".kantin-checkbox", function () {
			if (typeof window.renderKondimenTable === "function")
				window.renderKondimenTable();
		});

		// Event delegation untuk tombol tambah kondimen
		$(document).on("click", "#btn-tambah-kondimen", function () {
			if (menuList.length === 0) {
				loadMenuList(function () {
					if (typeof window.tambahKondimenRow === "function")
						window.tambahKondimenRow();
				});
			} else {
				if (typeof window.tambahKondimenRow === "function")
					window.tambahKondimenRow();
			}
		});

		// Submit form via AJAX (add/edit)
		$("#form-menu-harian").on("submit", function (e) {
			e.preventDefault();

			if (kondimenList.length === 0) {
				showError("Menu kondimen wajib diisi!");
				return;
			}

			var selectedKantins = getSelectedKantins();
			if (selectedKantins.length === 0) {
				showError("Pilih minimal 1 kantin!");
				return;
			}

			// Validasi kondimen
			for (var k of kondimenList) {
				if (!k.id_komponen) {
					showError("Semua kondimen harus dipilih!");
					return;
				}
				if (!k.qty_per_kantin) {
					showError("Qty per kantin untuk semua kondimen harus diisi!");
					return;
				}
				for (var sk of selectedKantins) {
					var q = k.qty_per_kantin[sk.id_kantin];
					if (q === undefined || q === null || q === "") {
						showError(
							"Semua qty per kantin harus diisi untuk setiap kondimen!"
						);
						return;
					}
				}
			}

			var formData = new FormData(this);
			formData.append("kondimen", JSON.stringify(kondimenList));

			// Blur active input (SAMA SEPERTI V_Menu_js.php)
			$(this).find(":focus").blur();

			var $submitButton = $(this).find('button[type="submit"]');
			$submitButton
				.prop("disabled", true)
				.html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');

			$.ajax({
				url: base_url + "/save",
				type: "POST",
				data: formData,
				processData: false,
				contentType: false,
				dataType: "json",
				success: function (res) {
					$submitButton
						.prop("disabled", false)
						.html('<i class="fas fa-save me-1"></i>Simpan');

					if (res.status === "success") {
						$("#form-modal-menu-harian").modal("hide");
						showSuccess(res.msg || "Menu harian berhasil disimpan!");
					} else {
						showError(res.msg || "Gagal menyimpan data!");
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error("AJAX Error: ", textStatus, errorThrown);
					console.error("Response:", jqXHR.responseText);

					$submitButton
						.prop("disabled", false)
						.html('<i class="fas fa-save me-1"></i>Simpan');

					showError("Terjadi kesalahan saat menyimpan data!");
				},
			});
		});

		// Reload data otomatis setelah modal tertutup (SAMA SEPERTI V_Menu_js.php)
		$(document).on("hidden.bs.modal", "#form-modal-menu-harian", function () {
			loadMenuHarianData();
		});

		// Blur input saat modal ditutup (SAMA SEPERTI V_Menu_js.php)
		$(document).on("hide.bs.modal", ".modal", function () {
			$(this).find(":focus").blur();
		});

		// Event delegation untuk customer change
		$(document).on("change", "#id_customer", function () {
			var id_customer = $(this).val();
			loadKantinRadioOptions(id_customer);
		});
	});

	// Global functions untuk tombol
	window.tambah_menu_harian = function () {
		kondimenList = [];
		$("#form-menu-harian")[0].reset();
		$(".kantin-checkbox").prop("checked", false);
		$("#id_menu_harian").val("");
		if (typeof window.renderKondimenTable === "function")
			window.renderKondimenTable();
		$("#form-modal-menu-harian").modal("show");
		$("#modalMenuHarianLabel").text("Tambah Menu Harian");
	};

	// Event delegation untuk tombol edit (SAMA SEPERTI V_Menu_js.php)
	$(document).on("click", ".btn-edit-menu-harian", function () {
		var id = $(this).data("id");
		window.edit_menu_harian(id);
	});

	window.edit_menu_harian = function (id) {
		$.ajax({
			url: base_url + "/get_by_id/" + id,
			type: "GET",
			dataType: "json",
			success: function (response) {
				if (response.status === "success") {
					var data = response.data;
					var kondimen = response.kondimen || [];

					$("#id_menu_harian").val(data.id_menu_harian);
					$("#tanggal").val(data.tanggal);
					$("#shift").val(data.shift);
					$("#jenis_menu").val(data.jenis_menu);
					$("#id_customer").val(data.id_customer);
					$("#nama_menu").val(data.nama_menu);
					$("#total_orderan_perkantin").val(data.total_orderan_perkantin);

					kondimenList = kondimen.map(function (k) {
						return {
							id_komponen: k.id_komponen,
							kategori: k.kategori_kondimen,
							qty_per_kantin:
								k.qty_per_kantin ||
								(k.qty_kondimen ? { [data.id_kantin]: k.qty_kondimen } : {}),
						};
					});

					var existingKantins = data.id_kantin || data.id_kantins || "";
					loadKantinRadioOptions(data.id_customer, function () {
						var arr = [];
						if (Array.isArray(existingKantins)) arr = existingKantins;
						else if (
							typeof existingKantins === "string" &&
							existingKantins.indexOf(",") !== -1
						)
							arr = existingKantins.split(",").map(function (s) {
								return s.trim();
							});
						else if (existingKantins) arr = [existingKantins.toString()];

						arr.forEach(function (id) {
							$("#kantin_" + id).prop("checked", true);
						});

						if (typeof window.renderKondimenTable === "function")
							window.renderKondimenTable();
						$("#form-modal-menu-harian").modal("show");
						$("#modalMenuHarianLabel").text("Edit Menu Harian");
					});
				} else {
					showError(response.msg || "Data tidak ditemukan!");
				}
			},
			error: function () {
				showError("Gagal mengambil data!");
			},
		});
	};

	// Event delegation untuk tombol delete (SAMA SEPERTI V_Menu_js.php)
	$(document).on("click", ".btn-delete-menu-harian", function () {
		var id = $(this).data("id");
		window.delete_menu_harian(id);
	});

	window.delete_menu_harian = function (id) {
		if (typeof Swal !== "undefined") {
			Swal.fire({
				title: "Hapus Menu Harian?",
				text: "Data yang dihapus tidak dapat dikembalikan!",
				icon: "warning",
				showCancelButton: true,
				confirmButtonColor: "#d33",
				cancelButtonColor: "#3085d6",
				confirmButtonText: "Ya, Hapus!",
				cancelButtonText: "Batal",
				reverseButtons: true,
			}).then((result) => {
				if (result.isConfirmed) {
					ajaxDeleteMenuHarian(id);
				}
			});
		} else {
			if (confirm("Yakin ingin menghapus menu harian ini?")) {
				ajaxDeleteMenuHarian(id);
			}
		}
	};

	// Delete data (langsung reload di AJAX success)
	function ajaxDeleteMenuHarian(id) {
		$.ajax({
			url: base_url + "/delete/" + id,
			type: "POST",
			dataType: "json",
			success: function (res) {
				showSuccess(res.message || "Menu harian berhasil dihapus!");
				if (res.status === "success") {
					loadMenuHarianData(); // Data reload otomatis setelah delete
				}
			},
			error: function () {
				showError("Gagal menghapus data!");
			},
		});
	}

	function loadCustomerOptions() {
		$.get(
			base_url + "/get_customers",
			function (data) {
				var options = '<option value="">-- Pilih Customer --</option>';
				if (data && data.length > 0) {
					$.each(data, function (_, customer) {
						options += `<option value="${customer.id_customer}">${customer.nama_customer}</option>`;
					});
				}
				$("#id_customer").html(options);
			},
			"json"
		);
	}

	function loadKantinRadioOptions(id_customer, callback) {
		var url = id_customer
			? base_url + "/get_kantin_by_customer"
			: base_url + "/get_kantins";
		var dataAjax = id_customer ? { id_customer: id_customer } : {};
		$.ajax({
			url: url,
			type: id_customer ? "POST" : "GET",
			data: dataAjax,
			dataType: "json",
			success: function (kantinList) {
				renderKantinRadio(kantinList);
				if (typeof callback === "function") callback(kantinList);
			},
		});
	}

	function loadMenuHarianData() {
		if ($.fn.DataTable && $.fn.DataTable.isDataTable("#menu-harian-table")) {
			$("#menu-harian-table").DataTable().destroy();
		}
		$("#menu-harian-table tbody").html(buildLoadingRow());
		$("#menu-harian-table").show();

		$.ajax({
			url: base_url + "/ajax_list",
			type: "GET",
			dataType: "json",
			success: renderMenuHarianTable,
			error: function () {
				showError("Gagal memuat data menu harian!");
			},
		});
	}

	function renderMenuHarianTable(result) {
		var html = "";
		var no = 1;

		if (
			result &&
			result.show_data &&
			Array.isArray(result.show_data) &&
			result.show_data.length > 0
		) {
			result.show_data.forEach(function (item) {
				html += buildTableRow(item, no++);
			});
		} else {
			html = buildEmptyRow();
		}

		if ($.fn.DataTable && $.fn.DataTable.isDataTable("#menu-harian-table")) {
			$("#menu-harian-table").DataTable().destroy();
		}
		$("#menu-harian-table tbody").html(html);
		initDataTable();
	}

	function buildTableRow(item, no) {
		function getShiftBadge(shift) {
			var badgeClass = "";
			switch (shift) {
				case "lunch":
					badgeClass = "badge bg-success text-white";
					break;
				case "dinner":
					badgeClass = "badge bg-primary text-white";
					break;
				case "supper":
					badgeClass = "badge bg-danger text-white";
					break;
				default:
					badgeClass = "badge bg-secondary text-white";
			}
			return `<span class="${badgeClass}">${
				shift.charAt(0).toUpperCase() + shift.slice(1)
			}</span>`;
		}

		function getKategoriBadge(kategori) {
			var badgeClass = "badge bg-secondary text-white";
			switch (kategori.toLowerCase()) {
				case "lauk utama":
					badgeClass = "badge bg-primary text-white";
					break;
				case "pendamping kering":
					badgeClass = "badge bg-warning text-dark";
					break;
				case "pendamping basah":
					badgeClass = "badge bg-info text-white";
					break;
				case "sayur":
				case "sayuran berkuah":
					badgeClass = "badge bg-success text-white";
					break;
				case "buah":
					badgeClass = "badge bg-danger text-white";
					break;
				case "nasi":
					badgeClass = "badge bg-secondary text-white";
					break;
				default:
					badgeClass = "badge bg-danger text-white";
			}
			return `<span class="${badgeClass}">${kategori}</span>`;
		}

		var kondimen = item.kondimen ? item.kondimen : "-";
		if (kondimen !== "-") {
			var kondimenItems = kondimen.split(", ");
			kondimen = kondimenItems
				.map(function (str, idx) {
					var match = str.match(/^(.*?) \((.*?)\) \((.*?)\)$/);
					if (match) {
						var nama = match[1];
						var kategori = match[2];
						var qty = match[3];
						return `${idx + 1}. ${nama} ${getKategoriBadge(kategori)} (${qty})`;
					} else {
						return `${idx + 1}. ${str}`;
					}
				})
				.join("<br>");
		}

		return `
            <tr>
                <td class="text-center">${no}</td>
                <td>${item.tanggal}</td>
                <td class="text-center">${getShiftBadge(item.shift)}</td>
                <td>${item.nama_customer}</td>
                <td>${item.nama_kantin}</td>
                <td>${item.jenis_menu}</td>
                <td>${item.nama_menu}</td>
                <td>${kondimen}</td>
                <td class="text-end">${item.total_orderan_perkantin}</td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-warning btn-sm btn-edit-menu-harian" data-id="${
													item.id_menu_harian
												}" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm btn-delete-menu-harian" data-id="${
													item.id_menu_harian
												}" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
	}

	function buildLoadingRow() {
		return `<tr><td class="text-center" colspan="10"><div class="text-muted p-3"><i class="fas fa-spinner fa-spin fa-2x mb-1"></i><div>Memuat data...</div></div></td></tr>`;
	}

	function buildEmptyRow() {
		return `<tr><td class="text-center" colspan="10"><div class="text-muted p-3"><i class="fas fa-utensils fa-2x mb-1"></i><div>Tidak ada data menu harian</div></div></td></tr>`;
	}

	function initDataTable() {
		if ($.fn.DataTable) {
			if ($.fn.DataTable.isDataTable("#menu-harian-table")) {
				$("#menu-harian-table").DataTable().destroy();
			}
			$("#menu-harian-table").DataTable({
				responsive: true,
				autoWidth: false,
				order: [],
				language: {
					search: "Cari:",
					lengthMenu: "Tampilkan _MENU_ data per halaman",
					zeroRecords: "Tidak ada data yang ditemukan",
					info: "Menampilkan halaman _PAGE_ dari _PAGES_",
					infoEmpty: "Tidak ada data tersedia",
					paginate: {
						next: "Selanjutnya",
						previous: "Sebelumnya",
					},
				},
			});
		}
	}

	// ========== KONDIMEN TABLE ==========

	window.tambahKondimenRow = function () {
		var selectedKantins = getSelectedKantins();
		var qtyMap = {};
		selectedKantins.forEach(function (k) {
			qtyMap[k.id_kantin] = "";
		});

		kondimenList.push({
			id_komponen: "",
			kategori: "",
			qty_per_kantin: qtyMap,
		});

		if (typeof window.renderKondimenTable === "function")
			window.renderKondimenTable();
	};

	window.renderKondimenTable = function () {
		if (renderTimeout) {
			clearTimeout(renderTimeout);
		}

		if (isRendering) {
			console.log(
				"[menu_harian] renderKondimenTable skipped (already rendering)"
			);
			return;
		}

		console.log("[menu_harian] renderKondimenTable called");

		renderTimeout = setTimeout(function () {
			actualRenderKondimenTable();
		}, 100);
	};

	function actualRenderKondimenTable() {
		isRendering = true;

		if (!Array.isArray(menuList) || menuList.length === 0) {
			loadMenuList(function () {
				isRendering = false;
				if (typeof window.renderKondimenTable === "function")
					window.renderKondimenTable();
			});
			return;
		}

		var selectedKantins = getSelectedKantins();

		var thead =
			"<tr>" +
			'<th class="text-center" style="width:40px;">No</th>' +
			'<th style="min-width:200px;">Nama Kondimen</th>' +
			'<th style="min-width:140px;">Kategori</th>';
		selectedKantins.forEach(function (k) {
			thead += `<th class="text-center">Qty<br>${k.nama_kantin}</th>`;
		});
		thead += '<th class="text-center" style="width:80px;">Aksi</th></tr>';
		$("#table-kondimen-menu thead").html(thead);

		var tbody = "";
		kondimenList.forEach(function (kondimen, idx) {
			if (!kondimen.qty_per_kantin) {
				var map = {};
				selectedKantins.forEach(function (k) {
					map[k.id_kantin] = kondimen.qty || "";
				});
				kondimen.qty_per_kantin = map;
			}

			var options = '<option value="">-- Pilih Kondimen --</option>';
			menuList.forEach(function (menu) {
				var sel = menu.id_komponen == kondimen.id_komponen ? "selected" : "";
				options += `<option value="${menu.id_komponen}" ${sel}>${menu.menu_nama}</option>`;
			});

			tbody += `<tr>
                <td class="text-center">${idx + 1}</td>
                <td>
                    <div class="kondimen-select-wrap">
                        <select class="form-control kondimen-nama" data-idx="${idx}">
                            ${options}
                        </select>
                    </div>
                </td>
                <td>
                    <input type="text" class="form-control kondimen-kategori" data-idx="${idx}" value="${
				kondimen.kategori || ""
			}" readonly>
                </td>`;

			selectedKantins.forEach(function (k) {
				var val =
					kondimen.qty_per_kantin &&
					kondimen.qty_per_kantin[k.id_kantin] !== undefined
						? kondimen.qty_per_kantin[k.id_kantin]
						: "";
				tbody += `<td><input type="number" min="0" class="form-control kondimen-qty" data-idx="${idx}" data-kantin="${k.id_kantin}" value="${val}"></td>`;
			});

			tbody += `
                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-sm" onclick="hapusKondimenRow(${idx})"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;
		});

		$("#table-kondimen-menu tbody").html(tbody);

		$(".kondimen-nama").each(function () {
			if ($(this).hasClass("select2-hidden-accessible")) {
				try {
					$(this).select2("destroy");
				} catch (e) {}
			}
		});

		$(".kondimen-nama").select2({
			width: "100%",
			dropdownParent: $("#form-modal-menu-harian .modal-content"),
			dropdownCss: { "z-index": 2100 },
		});

		// Event delegation untuk kondimen change
		$(document).off("change", ".kondimen-nama");
		$(document).on("change", ".kondimen-nama", function () {
			var idx = $(this).data("idx");
			var id_komponen = $(this).val();

			var isDuplicate = kondimenList.some(function (kondimen, i) {
				return kondimen.id_komponen === id_komponen && i !== idx;
			});
			if (isDuplicate) {
				showError("Menu kondimen sudah ada di tabel!");
				$(this).val("");
				kondimenList[idx].id_komponen = "";
				$(this).select2().val("").trigger("change.select2");
				return;
			}

			kondimenList[idx].id_komponen = id_komponen;

			if (id_komponen) {
				$.post(
					base_url + "/get_kategori_by_menu",
					{ id_komponen: id_komponen },
					function (data) {
						kondimenList[idx].kategori = data.nama_kategori;
						$('.kondimen-kategori[data-idx="' + idx + '"]').val(
							data.nama_kategori
						);
						computeTotalOrder();
					},
					"json"
				);
			} else {
				kondimenList[idx].kategori = "";
				$('.kondimen-kategori[data-idx="' + idx + '"]').val("");
				computeTotalOrder();
			}
		});

		// Event delegation untuk qty input
		$(document).off("input", ".kondimen-qty");
		$(document).on("input", ".kondimen-qty", function () {
			var idx = $(this).data("idx");
			var id_kantin = $(this).data("kantin");
			var val = $(this).val();
			kondimenList[idx].qty_per_kantin = kondimenList[idx].qty_per_kantin || {};
			kondimenList[idx].qty_per_kantin[id_kantin] = val;
			computeTotalOrder();
		});

		setTimeout(function () {
			isRendering = false;
			console.log("[menu_harian] Render complete, flag reset");
		}, 200);
	}

	window.hapusKondimenRow = function (idx) {
		if (typeof idx === "undefined" || idx === null) return;
		kondimenList.splice(idx, 1);
		if (typeof window.renderKondimenTable === "function")
			window.renderKondimenTable();
	};

	function loadMenuList(callback) {
		$.get(
			base_url + "/get_menu_list",
			function (data) {
				menuList = data;
				if (typeof callback === "function") callback();
			},
			"json"
		);
	}

	function computeTotalOrder() {
		var selected = getSelectedKantins();
		if (!selected || selected.length === 0) {
			$("#total_orderan_perkantin").val("");
			return;
		}

		var sums = {};
		selected.forEach(function (k) {
			sums[k.id_kantin] = 0;
		});

		kondimenList.forEach(function (k) {
			if (!k) return;
			var kat = (k.kategori || "").toString().toLowerCase();
			if (kat.indexOf("lauk utama") === -1) return;
			var map = k.qty_per_kantin || {};
			selected.forEach(function (sk) {
				var q = parseFloat(map[sk.id_kantin]);
				if (!isFinite(q)) q = 0;
				sums[sk.id_kantin] = (sums[sk.id_kantin] || 0) + q;
			});
		});

		var first = selected[0].id_kantin;
		var total = sums[first] || 0;
		if (Math.abs(total - Math.round(total)) < 1e-6) total = Math.round(total);
		$("#total_orderan_perkantin").val(total);
		return sums;
	}
})();
