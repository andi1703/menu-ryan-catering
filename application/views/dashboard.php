<!doctype html>
<html lang="en">

<head>
    <!-- DataTables -->
    <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url(); ?>assets_back/libs/datatables.net-select-bs4/css//select.bootstrap4.min.css" rel="stylesheet" type="text/css" />

    <?php
    $this->load->view('back_partial/title-meta');
    $this->load->view('back_partial/head-css');
    ?>
</head>


<!-- <body data-sidebar="dark" > -->

<body data-sidebar="dark" data-layout="horizontal">


    <!-- Begin page -->
    <div id="layout-wrapper">

        <?php
        $this->load->view('back_partial/horizontal-topbar');
        $this->load->view('back_partial/horizontal-menu');
        $this->load->view('back_partial/head-css');

        ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">
                    <?php
                    $this->load->view('back_partial/page-title.php');
                    ?>
                    <div class="row">
                        <div class="col-xl-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3>Welcome Back, <?php echo $this->session->userdata('pic') ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">

                                            <h4 class="card-title mb-4">Recent Activity Feed</h4>

                                            <div data-simplebar style="max-height: 330px;">
                                                <ul class="list-unstyled activity-wid">
                                                    <!-- <li class="activity-list">
                                                            <div class="activity-icon avatar-xs">
                                                                <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                    <i class=" fas fa-edit"></i>
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <div>
                                                                    <h5 class="font-size-13">22 Dec, 2022 <small class="text-muted">16:00</small></h5>
                                                                </div>
                                                                
                                                                <div>
                                                                    <p class="text-muted mb-0">Ongoing Projection For Line Katazure :  
                                                                        <ul>
                                                                            <li>Added Column Weight, Image Katazure, Number Of Tolerance Misalignment, Status Prodcut Katazure in Master Product Module</li>
                                                                        </ul>
                                                                    </p>
                                                                </div>
                                                            </div>                                                            
                                                        </li> -->
                                                    <li class="activity-list">
                                                        <div class="activity-icon avatar-xs">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                <i class=" ri-file-list-3-fill"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <h5 class="font-size-13">16 Februari, 2024 <small class="text-muted">07:50</small></h5>
                                                            </div>

                                                            <div>
                                                                <p class="text-muted mb-0">Hallo, Report Reject Control Per Group Product, Sudah Bisa diakses !</p>

                                                                <!-- Metode Crosscheck : Hasil Process Date dicocokan dengan Report Asakai QC, Hasil Charging Date dicocokan dengan report pending control -->
                                                            </div>
                                                        </div>
                                                    </li>


                                                    <li class="activity-list">
                                                        <div class="activity-icon avatar-xs">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                <i class=" ri-file-list-3-fill"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <h5 class="font-size-13">3 November, 2023 <small class="text-muted">14:46</small></h5>
                                                            </div>

                                                            <div>
                                                                <p class="text-muted mb-0">Hallo, Report Reject Control Sekarang Sudah Bisa Difilter Berdasarkan Charging Date !</p>

                                                                <!-- Metode Crosscheck : Hasil Process Date dicocokan dengan Report Asakai QC, Hasil Charging Date dicocokan dengan report pending control -->
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="activity-list">
                                                        <div class="activity-icon avatar-xs">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                <i class=" ri-file-list-3-fill"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <h5 class="font-size-13">07 Februari, 2023 <small class="text-muted">16:00</small></h5>
                                                            </div>

                                                            <div>
                                                                <p class="text-muted mb-0">Added Report Pending Control</p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="activity-list">
                                                        <div class="activity-icon avatar-xs">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                <i class=" ri-file-list-3-fill"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <h5 class="font-size-13">07 Jan, 2023 <small class="text-muted">08:10</small></h5>
                                                            </div>

                                                            <div>
                                                                <p class="text-muted mb-0">Congratulations ! Line Katazure is ready right now.</p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="activity-list">
                                                        <div class="activity-icon avatar-xs">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                <i class=" ri-file-list-3-fill"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <h5 class="font-size-13">31 Dec, 2022 <small class="text-muted">13:35</small></h5>
                                                            </div>

                                                            <div>
                                                                <p class="text-muted mb-0">Added Report Reject Control</p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="activity-list">
                                                        <div class="activity-icon avatar-xs">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                <i class=" ri-file-list-3-fill"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <h5 class="font-size-13">19 Dec, 2022 <small class="text-muted">15:08</small></h5>
                                                            </div>

                                                            <div>
                                                                <p class="text-muted mb-0">Added Report Final Inspection</p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                    <li class="activity-list">
                                                        <div class="activity-icon avatar-xs">
                                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                                <i class="fas fa-file-excel"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div>
                                                                <h5 class="font-size-13">13 Dec, 2022 <small class="text-muted">15:37</small></h5>
                                                            </div>

                                                            <div>
                                                                <p class="text-muted mb-0">Congratulations ! Now, all your report can be exported to excel file</p>
                                                            </div>
                                                        </div>
                                                    </li>

                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->

                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <h4 class="card-title mb-4">Performance Kerja Nutrisionist</h4>
                                            <div class="row text-center">
                                                <div class="col-md-4">
                                                    <h5><?= $nutrisionist_stats['menu_input'] ?? 0 ?></h5>
                                                    <p class="text-muted mb-0">Menu Diinput</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5><?= $nutrisionist_stats['menu_update'] ?? 0 ?></h5>
                                                    <p class="text-muted mb-0">Menu Diupdate</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <h5><?= $nutrisionist_stats['activity_count'] ?? 0 ?></h5>
                                                    <p class="text-muted mb-0">Aktivitas Lain</p>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <p class="text-success">Terus tingkatkan penggunaan aplikasi untuk hasil kerja yang maksimal!</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end row -->
                        </div>
                    </div> <!-- end row -->
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            <?php
            $this->load->view('back_partial/footer');
            ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->
    <?php
    $this->load->view('back_partial/right-sidebar');
    ?>

    <!-- JAVASCRIPT  -->
    <?php $this->load->view('back_partial/vendor-scripts') ?>
    <!-- Required datatable js -->
    <script src="<?php echo base_url() ?>assets_back/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url() ?>assets_back/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url() ?>assets_back/js/app.js"></script>

    <!-- JS Page -->
    <?php $this->load->view('back/dashboard_js') ?>

    <ul>
        <?php foreach ($popular_menu as $menu) : ?>
            <li>
                <strong><?= htmlspecialchars($menu['menu_nama']) ?></strong>
                <br>Tanggal: <?= $menu['created_at'] ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>

</html>