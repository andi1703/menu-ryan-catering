<!doctype html>
<html lang="en">

<head>
    <?php
    $this->load->view('back_partial/title-meta');
    $this->load->view('back_partial/head-css');
    ?>
    <style>
        /* CSS untuk Latar Belakang Gambar dengan Transparansi (Overlay) */
        body.auth-body-bg {
            /* Properti background sudah ada di inline style <body>, jadi ini akan menimpanya. */
            /* Jika Anda mau konsisten, Anda bisa pindahkan properti background-image ke sini juga */
            position: relative;
            /* Penting untuk pseudo-element ::before */
        }

        body.auth-body-bg::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.66);
            /* Hitam dengan opasitas 50%. Ubah 0.5 untuk tingkat transparansi */
            z-index: -1;
            /* Pastikan overlay berada di belakang konten */
        }

        /* Pastikan card memiliki latar belakang putih solid di atas overlay */
        .card {
            background-color: #fff;
            /* Ini default, tapi pastikan */
            z-index: 1;
            /* Pastikan card berada di atas overlay */
            position: relative;
            /* Penting agar z-index bekerja dengan baik */
        }
    </style>
</head>

<body class="auth-body-bg" style="background-image: url('file/background/background.jpg'); background-size: cover; background-position: center center; background-repeat: no-repeat;">
    <div>
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-lg-12">
                    <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                        <div class="w-100">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div>
                                        <div class="col-lg-12">
                                            <?php echo $this->session->flashdata('result'); ?>

                                            <?php
                                            $maintenance = 0;
                                            if ($maintenance == 1) {
                                            ?>
                                                <div class="alert alert-danger alert-dismissable">
                                                    <i class="fa fa-info"></i>
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                    <b>System Sedang Maintenance </b> Mohon Untuk Menunggu Beberapa Saat
                                                </div>

                                            <?php
                                            }
                                            ?>
                                        </div>

                                        <div class="row justify-content-center mt-5">
                                            <div class="col-md-8 col-lg-6 col-xl-5">
                                                <div class="card overflow-hidden">
                                                    <div class="card-body p-4">
                                                        <div class="text-center">
                                                            <div>
                                                                <img style="width: 250px; margin-bottom: 0px;" src="<?php echo base_url() ?>file/logo/logo.png" alt="logo"></a>
                                                            </div>
                                                            <h4 class="font-size-18 mt-1">Welcome Back !</h4>
                                                            <p class="text-muted">Sign in to start your session.</p>
                                                        </div>

                                                        <form class="space-y-6" method="POST" action="<?php echo base_url() ?>login/proses">
                                                            <div class="form-group auth-form-group-custom mb-4">
                                                                <i class="ri-user-fill auti-custom-input-icon"></i>
                                                                <label for="UserName">User Name</label>
                                                                <input type="text" class="form-control" name="UserName" id="UserName" placeholder="Enter User Name">
                                                            </div>

                                                            <div class="form-group auth-form-group-custom mb-4">
                                                                <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                                <label for="password">Password</label>
                                                                <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                                                            </div>

                                                            <?php
                                                            if ($maintenance == 0) {
                                                            ?>
                                                                <div class="mt-4 text-center">
                                                                    <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Log In</button>
                                                                    <button onclick="signup();" type="button" class="btn btn-outline-primary w-md waves-effect waves-light">Signiup</button>
                                                                </div>
                                                            <?php
                                                            }
                                                            ?>
                                                        </form>
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
            </div>
        </div>
    </div>

    <?php $this->load->view('back_partial/vendor-scripts') ?>
    <script src="<?php echo base_url() ?>assets_back/js/app.js"></script>

</body>

</html>