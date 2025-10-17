<div class="topnav">
    <div class="container-fluid">
        <nav class="navbar navbar-light navbar-expand-lg topnav-menu">

            <div class="collapse navbar-collapse" id="topnav-menu-content">
                <ul class="navbar-nav">

                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>dashboard">
                            <i class="ri-dashboard-line mr-2"></i> Dashboard
                        </a>
                    </li>

                    <?php
                    $hide_adjustment = "hidden";
                    $hide_order_pouring  = "hidden";
                    if ($this->session->userdata('is_akses_master') == 1) {
                        $hide_adjustment = "";
                        $hide_order_pouring = "";
                    ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-layout" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-folder-2-line mr-2"></i>Master <div class="arrow-down"></div>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="topnav-layout">
                                <a href="<?= base_url() ?>master/product" class="dropdown-item">Product</a>
                                <a href="<?= base_url() ?>master/user-line" class="dropdown-item">User Line</a>
                            </div>
                        </li>
                    <?php
                    }
                    ?>





                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-layout" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-file-chart-line mr-2"></i>Report <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="topnav-layout">
                            <a href="<?= base_url() ?>report/hb" class="dropdown-item">Report HB</a>


                        </div>
                    </li>

                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-uielement" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-pencil-ruler-2-line mr-2"></i>UI Elements <div class="arrow-down"></div>
                        </a>

                        <div class="dropdown-menu mega-dropdown-menu px-2 dropdown-mega-menu-xl"
                            aria-labelledby="topnav-uielement">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div>
                                        <a href="ui-alerts.html" class="dropdown-item">Alerts</a>
                                        <a href="ui-buttons.html" class="dropdown-item">Buttons</a>
                                        <a href="ui-cards.html" class="dropdown-item">Cards</a>
                                        <a href="ui-carousel.html" class="dropdown-item">Carousel</a>
                                        <a href="ui-dropdowns.html" class="dropdown-item">Dropdowns</a>
                                        <a href="ui-grid.html" class="dropdown-item">Grid</a>
                                        <a href="ui-images.html" class="dropdown-item">Images</a>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <a href="ui-lightbox.html" class="dropdown-item">Lightbox</a>
                                        <a href="ui-modals.html" class="dropdown-item">Modals</a>
                                        <a href="ui-rangeslider.html" class="dropdown-item">Range Slider</a>
                                        <a href="ui-roundslider.html" class="dropdown-item">Round slider</a>
                                        <a href="ui-session-timeout.html" class="dropdown-item">Session Timeout</a>
                                        <a href="ui-progressbars.html" class="dropdown-item">Progress Bars</a>
                                        <a href="ui-sweet-alert.html" class="dropdown-item">Sweet-Alert</a>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div>
                                        <a href="ui-tabs-accordions.html" class="dropdown-item">Tabs & Accordions</a>
                                        <a href="ui-typography.html" class="dropdown-item">Typography</a>
                                        <a href="ui-video.html" class="dropdown-item">Video</a>
                                        <a href="ui-general.html" class="dropdown-item">General</a>
                                        <a href="ui-rating.html" class="dropdown-item">Rating</a>
                                        <a href="ui-notifications.html" class="dropdown-item">Notifications</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </li> -->

                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-apps" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-apps-2-line mr-2"></i>Apps <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-apps">

                            <a href="calendar.html" class="dropdown-item">Calendar</a>
                            <a href="apps-chat.html" class="dropdown-item">Chat</a>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-email"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Email <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-email">
                                    <a href="email-inbox.html" class="dropdown-item">Inbox</a>
                                    <a href="email-read.html" class="dropdown-item">Read Email</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-ecommerce"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Ecommerce <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-ecommerce">
                                    <a href="ecommerce-products.html" class="dropdown-item">Products</a>
                                    <a href="ecommerce-product-detail.html" class="dropdown-item">Product Detail</a>
                                    <a href="ecommerce-orders.html" class="dropdown-item">Orders</a>
                                    <a href="ecommerce-customers.html" class="dropdown-item">Customers</a>
                                    <a href="ecommerce-cart.html" class="dropdown-item">Cart</a>
                                    <a href="ecommerce-checkout.html" class="dropdown-item">Checkout</a>
                                    <a href="ecommerce-shops.html" class="dropdown-item">Shops</a>
                                    <a href="ecommerce-add-product.html" class="dropdown-item">Add Product</a>
                                </div>
                            </div>

                            <a href="apps-kanban-board.html" class="dropdown-item">Kanban Board</a>
                        </div>
                    </li> -->

                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-components" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-stack-line mr-2"></i>Components <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-components">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-form"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Forms <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-form">
                                    <a href="form-elements.html" class="dropdown-item">Elements</a>
                                    <a href="form-validation.html" class="dropdown-item">Validation</a>
                                    <a href="form-advanced.html" class="dropdown-item">Advanced Plugins</a>
                                    <a href="form-editors.html" class="dropdown-item">Editors</a>
                                    <a href="form-uploads.html" class="dropdown-item">File Upload</a>
                                    <a href="form-xeditable.html" class="dropdown-item">Xeditable</a>
                                    <a href="form-wizard.html" class="dropdown-item">Wizard</a>
                                    <a href="form-mask.html" class="dropdown-item">Mask</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-table"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Tables <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-table">
                                    <a href="tables-basic.html" class="dropdown-item">Basic Tables</a>
                                    <a href="tables-datatable.html" class="dropdown-item">Data Tables</a>
                                    <a href="tables-responsive.html" class="dropdown-item">Responsive Table</a>
                                    <a href="tables-editable.html" class="dropdown-item">Editable Table</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-charts"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Charts <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-charts">
                                    <a href="charts-apex.html" class="dropdown-item">Apex charts</a>
                                    <a href="charts-chartjs.html" class="dropdown-item">Chartjs</a>
                                    <a href="charts-flot.html" class="dropdown-item">Flot Chart</a>
                                    <a href="charts-knob.html" class="dropdown-item">Jquery Knob Chart</a>
                                    <a href="charts-sparkline.html" class="dropdown-item">Sparkline Chart</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-icons"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Icons <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-icons">
                                    <a href="icons-remix.html" class="dropdown-item">Remix Icons</a>
                                    <a href="icons-materialdesign.html" class="dropdown-item">Material Design</a>
                                    <a href="icons-dripicons.html" class="dropdown-item">Dripicons</a>
                                    <a href="icons-fontawesome.html" class="dropdown-item">Font awesome 5</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-map"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Maps <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-map">
                                    <a href="maps-google.html" class="dropdown-item">Google Maps</a>
                                    <a href="maps-vector.html" class="dropdown-item">Vector Maps</a>
                                </div>
                            </div>
                        </div>
                    </li> -->

                    <!-- <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle arrow-none" href="#" id="topnav-more" role="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="ri-file-copy-2-line mr-2"></i>Pages <div class="arrow-down"></div>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="topnav-more">
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-auth"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Authentication <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-auth">
                                    <a href="auth-login.html" class="dropdown-item">Login</a>
                                    <a href="auth-register.html" class="dropdown-item">Register</a>
                                    <a href="auth-recoverpw.html" class="dropdown-item">Recover Password</a>
                                    <a href="auth-lock-screen.html" class="dropdown-item">Lock Screen</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-item dropdown-toggle arrow-none" href="#" id="topnav-utility"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Utility <div class="arrow-down"></div>
                                </a>
                                <div class="dropdown-menu" aria-labelledby="topnav-utility">
                                    <a href="pages-starter.html" class="dropdown-item">Starter Page</a>
                                    <a href="pages-maintenance.html" class="dropdown-item">Maintenance</a>
                                    <a href="pages-comingsoon.html" class="dropdown-item">Coming Soon</a>
                                    <a href="pages-timeline.html" class="dropdown-item">Timeline</a>
                                    <a href="pages-faqs.html" class="dropdown-item">FAQs</a>
                                    <a href="pages-pricing.html" class="dropdown-item">Pricing</a>
                                    <a href="pages-404.html" class="dropdown-item">Error 404</a>
                                    <a href="pages-500.html" class="dropdown-item">Error 500</a>
                                </div>
                            </div>
                        </div>
                    </li> -->



                </ul>
            </div>
        </nav>
    </div>
</div>