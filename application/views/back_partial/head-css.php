<!-- Select2 -->
<link href="<?php echo base_url() ?>assets_back/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
<!-- Bootstrap Css -->
<link href="<?php echo base_url(); ?>assets_back/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Datepicker -->
<link href="<?php echo base_url(); ?>assets_back/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet">
<!-- Icons Css -->
<link href="<?php echo base_url(); ?>assets_back/css/icons.min.css" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="<?php echo base_url(); ?>assets_back/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
<!-- Toastr -->
<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>assets_back/libs/toastr/build/toastr.min.css">
<!-- Google Font Nunito Offline -->
<link rel="stylesheet" href="<?php echo base_url('') ?>assets_back/css/gfont-nunito.css">
<!-- icon-->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

<!-- Boxicons CSS -->
<link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

<!-- ATAU gunakan Font Awesome jika sudah ada -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

<input type="text" value="<?php echo base_url() ?>" id="base_url" hidden="">

<style type="text/css">
	.swal-modal .swal-text {
		text-align: center;
	}

	.datepicker {
		border: 1px solid #f8f9fa;
		padding: 8px;
		z-index: 9999 !important
	}

	/* ===== SWEETALERT2 SIMPLE FIX ===== */

	/* ✅ TAMBAHKAN 3 BARIS INI SAJA */
	.swal2-popup {
		width: 330px !important;
		/* Ubah lebar */
		max-width: 90% !important;
		/* Responsive */
		font-size: 14px !important;
		/* Ukuran font */
	}

	/* Center buttons only */
	.swal2-actions {
		justify-content: center !important;
		gap: 1rem !important;
	}

	/* Button styling minimal */
	.swal2-confirm,
	.swal2-cancel {
		padding: 0.75rem 2rem !important;
		border-radius: 8px !important;
		font-weight: 500 !important;
	}

	/* Colors only */
	.swal2-confirm {
		background: #a72828ff !important;
		border: none !important;
	}

	.swal2-cancel {
		background: #6c757d !important;
		border: none !important;
	}
</style>