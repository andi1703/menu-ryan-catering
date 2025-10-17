<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="<?= base_url() ?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img class="img-fluid" src="<?php echo base_url() ?>file/logo/logo-sm.png" alt="">
                    </span>
                    <span class="logo-lg">
                        <img src="<?php echo base_url() ?>file/logo/logo-light.png" alt="" height="45">
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 d-lg-none header-item" data-toggle="collapse" data-target="#topnav-menu-content">
                <i class="ri-menu-2-line align-middle"></i>
            </button>

            <!-- App Search-->
            <!-- <form class="app-search d-none d-lg-block">
                <div class="position-relative">
                    <input type="text" class="form-control" placeholder="Search...">
                    <span class="ri-search-line"></span>
                </div>
            </form> -->

        </div>

        <div class="d-flex">

            <div class="dropdown d-inline-block d-lg-none ml-2">
                <button type="button" class="btn header-item noti-icon waves-effect" id="page-header-search-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="ri-search-line"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right p-0" aria-labelledby="page-header-search-dropdown">

                    <form class="p-3">
                        <div class="form-group m-0">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search ...">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" type="submit"><i class="ri-search-line"></i></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="dropdown d-none d-lg-inline-block ml-1">
                <button type="button" class="btn header-item noti-icon waves-effect" data-toggle="fullscreen">
                    <i class="ri-fullscreen-line"></i>
                </button>
            </div>

            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="<?php echo base_url() ?>assets_back/images/users/avatar-2.jpg" alt="Header Avatar">
                    <span class=" d-xl-inline-block ml-1">
                        <?php
                        if ($this->session->userdata('line_name') == 'PACKING') {
                            echo $this->session->userdata('opt_finishing');
                        } else {
                            echo $this->session->userdata('pic');
                        }
                        ?> - <?php echo $this->session->userdata('line_name') ?></span>
                    <i class="mdi mdi-chevron-down  d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <!-- item-->
                    <!-- <a href="<?php echo base_url() ?>profile" class="dropdown-item" href="#"><i class="ri-user-line align-middle mr-1"></i> Profile</a> -->
                    <a class="dropdown-item text-danger" href="<?php echo base_url() ?>logout"><i class="ri-shut-down-line align-middle mr-1 text-danger"></i> Finish Andon & Logout</a>
                </div>
            </div>

            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item noti-icon right-bar-toggle waves-effect">
                    <i class="ri-settings-2-line"></i>
                </button>
            </div>

        </div>
    </div>
</header>