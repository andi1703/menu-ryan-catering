<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
                <li class="<?php echo (uri_string() == 'dashboard') ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('dashboard'); ?>" class="waves-effect">
                        <i class="ri-dashboard-line"></i><span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title">Daily Menu</li>
                <li class="<?php echo (uri_string() == 'dailymenu') ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('dailymenu'); ?>" class="waves-effect">
                        <i class="fas fa-leaf"></i><span>Daily Menu</span>
                    </a>
                </li>

                <li class="menu-title">Master Data</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-database-2-fill"></i> <span>Data Master Menu</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo base_url('menu'); ?>">
                                <i class="fas fa-utensils"></i>
                                <span>Kondimen Menu</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('kategori-menu'); ?>">
                                <i class="ri-list-check-2"></i>Kategori Menu
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('thematik'); ?>">
                                <i class="ri-earth-line"></i>Thematic Menu
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('regular-menu'); ?>">
                                <i class="fas fa-hamburger"></i>Regular Menu
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('paket-menu'); ?>">
                                <i class="fas fa-box"></i>Paket Menu
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('sehat-menu'); ?>">
                                <i class="fas fa-apple-alt"></i>Sehat Menu
                            </a>
                        </li>
                    </ul>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-database-2-fill"></i> <span>Data Master Customer</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li>
                            <a href="<?php echo base_url('customer'); ?>">
                                <i class="ri-user-3-line"></i>Customer
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('kantin'); ?>">
                                <i class="fas fa-store"></i>Kantin
                            </a>
                        </li>
                    </ul>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="ri-database-2-fill"></i> <span>Data Master Bahan</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li>
                            <a href="<?php echo base_url('satuan'); ?>">
                                <i class="fas fa-balance-scale"></i>Satuan
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo base_url('bahan'); ?>">
                                <i class="fas fa-carrot"></i>Bahan
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="menu-title">Food Cost Management</li>
                <li class="<?php echo (uri_string() == 'food-cost') ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('food-cost'); ?>" class="waves-effect">
                        <i class="fas fa-calculator"></i><span>Food Cost</span>
                    </a>
                </li>

                <li class="menu-title">Laporan</li>
                <li class="<?php echo (uri_string() == 'laporan-bahan') ? 'mm-active' : ''; ?>">
                    <a href="<?php echo base_url('laporan-bahan'); ?>" class="waves-effect">
                        <i class="fas fa-chart-bar"></i><span>Laporan Kebutuhan Bahan</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>