<!doctype html>
<html lang="en">
<!-- HEADER -->

<head>
    <?php
    $this->load->view('back_partial/title-meta');
    $this->load->view('back_partial/head-css');
    ?>
</head>

<!-- BODY -->

<body class="auth-body-bg">
    <div class="home-btn d-none d-sm-block">
        <a href="<?php echo base_url() ?>"><i class="mdi mdi-home-variant h2 text-white"></i></a>
    </div>
    <div>
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-lg-12">
                    <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                        <div class="w-100">
                            <div class="row justify-content-center">
                                <div class="col-lg-9">
                                    <div>
                                        <div class="text-center">
                                            <div>
                                                <a href="<?php echo base_url() ?>" class="logo"><img style="width: 250px;" src="<?php echo base_url() ?>file/logo/logo.png" alt="logo"></a>
                                            </div>

                                            <h4 class="font-size-18 mt-4">Welcome Back !</h4>
                                            <p class="text-muted">Sign in to start your session.</p>
                                        </div>

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

                                        <div class="p-2 mt-5">
                                            <form class="form-horizontal" method="POST" action="<?php echo base_url() ?>login/proses">
                                                <!-- <div hidden="">
                                                        <input type="text" class="txt_csrfname" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                    </div> -->

                                                <div class="form-group auth-form-group-custom mb-4">
                                                    <i class=" ri-building-line auti-custom-input-icon"></i>
                                                    <label for="line">Line</label>
                                                    <input type="text" class="form-control" name="line" id="line" placeholder="Enter Line">
                                                    <!-- <select  style="width:100%;" required class="form-control select2" name="line" id="line2">
                                                            <option value="">Pilih Line </option>
                                                            <?php
                                                            foreach ($line as $s) {
                                                            ?>
                                                                <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                                                            <?php
                                                            }
                                                            ?>
                                                        </select> -->
                                                </div>

                                                <div class="form-group auth-form-group-custom mb-4">
                                                    <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                                    <label for="password">Password</label>
                                                    <input type="password" class="form-control" name="password" id="password" placeholder="Enter Password">
                                                </div>

                                                <div class="form-group auth-form-group-custom mb-4">
                                                    <i class="ri-user-2-line auti-custom-input-icon"></i>
                                                    <label for="pic">PIC</label>
                                                    <input type="text" minlength="3" class="form-control" name="pic" id="pic" placeholder="Enter PIC" autocomplete="off">
                                                </div>
                                                <?php
                                                if ($maintenance == 0) {
                                                ?>

                                                    <div class="mt-4 text-center">
                                                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">Log In</button>
                                                        <button onclick="create_andon();" type="button" class="btn btn-outline-primary w-md waves-effect waves-light">Register Andon System</button>
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

    <?php
    // Modal Page
    $this->load->view('back/andon/andon_form');
    ?>

    <!-- FOOTER -->
    <?php $this->load->view('back_partial/vendor-scripts') ?>
    <script src="<?php echo base_url() ?>assets_back/js/app.js"></script>
    <!-- JS Page -->
    <?php $this->load->view('back/andon/andon_js') ?>



</body>

</html>