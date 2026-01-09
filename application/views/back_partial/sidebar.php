<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <?php
            $current = uri_string();
            $isDashboardActive   = ($current === 'dashboard');
            $isDailyMenuActive   = ($current === 'menu-harian');
            $isDailyReportActive = ($current === 'menu-harian-report');
            $isReviewMenuActive  = ($current === 'review-menu');

            $masterMenuActive     = in_array($current, ['menu', 'kategori-menu', 'thematik']);
            $masterCustomerActive = in_array($current, ['customer', 'kantin']);
            $masterBahanActive    = in_array($current, ['satuan', 'bahan']);
            $isVegetableCalcActive = ($current === 'vegetable-calculator');
            ?>

            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
                <li class="<?php echo $isDashboardActive ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('dashboard'); ?>" class="waves-effect">
                        <i class="ri-dashboard-line"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title">Daily Menu</li>
                <li class="<?php echo $isDailyMenuActive ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('menu-harian'); ?>" class="waves-effect">
                        <i class="fas fa-leaf"></i>
                        <span>Daily Menu</span>
                    </a>
                </li>
                <li class="<?php echo $isReviewMenuActive ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('review-menu'); ?>" class="waves-effect">
                        <i class="fas fa-book-open"></i>
                        <span>Review Menu</span>
                    </a>
                </li>

                <li class="menu-title">Report</li>
                <li class="<?php echo $isDailyReportActive ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('menu-harian-report'); ?>" class="waves-effect">
                        <i class="ri-file-list-3-line"></i>
                        <span>Daily Menu Report</span>
                    </a>
                </li>

                <li class="menu-title">Master Data</li>

                <!-- Data Master Menu -->
                <li class="<?php echo $masterMenuActive ? 'mm-active' : ''; ?>">
                    <a href="javascript:void(0);" class="has-arrow waves-effect" aria-expanded="<?php echo $masterMenuActive ? 'true' : 'false'; ?>">
                        <i class="ri-database-2-fill"></i>
                        <span>Data Master Menu</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="<?php echo $masterMenuActive ? 'true' : 'false'; ?>">
                        <li class="<?php echo ($current === 'menu') ? 'mm-active' : ''; ?>">
                            <a href="<?php echo base_url('menu'); ?>">
                                <i class="fas fa-utensils"></i>
                                <span>Kondimen Menu</span>
                            </a>
                        </li>
                        <li class="<?php echo ($current === 'kategori-menu') ? 'mm-active' : ''; ?>">
                            <a href="<?php echo base_url('kategori-menu'); ?>">
                                <i class="ri-list-check-2"></i>
                                <span>Kategori Menu</span>
                            </a>
                        </li>
                        <li class="<?php echo ($current === 'thematik') ? 'mm-active' : ''; ?>">
                            <a href="<?php echo base_url('thematik'); ?>">
                                <i class="ri-earth-line"></i>
                                <span>Thematic Menu</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Data Master Customer -->
                <li class="<?php echo $masterCustomerActive ? 'mm-active' : ''; ?>">
                    <a href="javascript:void(0);" class="has-arrow waves-effect" aria-expanded="<?php echo $masterCustomerActive ? 'true' : 'false'; ?>">
                        <i class="ri-database-2-fill"></i>
                        <span>Data Master Customer</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="<?php echo $masterCustomerActive ? 'true' : 'false'; ?>">
                        <li class="<?php echo ($current === 'customer') ? 'mm-active' : ''; ?>">
                            <a href="<?php echo base_url('customer'); ?>">
                                <i class="ri-user-3-line"></i>
                                <span>Customer</span>
                            </a>
                        </li>
                        <li class="<?php echo ($current === 'kantin') ? 'mm-active' : ''; ?>">
                            <a href="<?php echo base_url('kantin'); ?>">
                                <i class="fas fa-store"></i>
                                <span>Kantin</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Data Master Bahan -->
                <li class="<?php echo $masterBahanActive ? 'mm-active' : ''; ?>">
                    <a href="javascript:void(0);" class="has-arrow waves-effect" aria-expanded="<?php echo $masterBahanActive ? 'true' : 'false'; ?>">
                        <i class="ri-database-2-fill"></i>
                        <span>Data Master Bahan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="<?php echo $masterBahanActive ? 'true' : 'false'; ?>">
                        <li class="<?php echo ($current === 'satuan') ? 'mm-active' : ''; ?>">
                            <a href="<?php echo base_url('satuan'); ?>">
                                <i class="fas fa-balance-scale"></i>
                                <span>Satuan</span>
                            </a>
                        </li>
                        <li class="<?php echo ($current === 'bahan') ? 'mm-active' : ''; ?>">
                            <a href="<?php echo base_url('bahan'); ?>">
                                <i class="fas fa-carrot"></i>
                                <span>Bahan</span>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title">Food Cost Management</li>
                <li class="<?php echo ($current === 'food-cost') ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('food-cost'); ?>" class="waves-effect">
                        <i class="fas fa-calculator"></i>
                        <span>Food Cost</span>
                    </a>
                </li>

                <!-- Vegetable Calculator -->
                <li class="<?php echo $isVegetableCalcActive ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('vegetable-calculator'); ?>" class="waves-effect">
                        <i class="fas fa-seedling"></i>
                        <span>Vegetable Calculator</span>
                    </a>
                </li>

                <li class="menu-title">Laporan</li>
                <li class="<?php echo ($current === 'laporan-bahan') ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('laporan-bahan'); ?>" class="waves-effect">
                        <i class="fas fa-chart-bar"></i>
                        <span>Laporan Kebutuhan Bahan</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>