<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <!-- LOGO -->
    <div class="navbar-brand-box">
        <a href="<?php echo base_url('admin_root/') ?>" class="logo logo-dark text-center">
            <span class="logo-sm">
                <img src="<?php echo base_url('assets/') ?>images/logo-sm.png" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="<?php echo base_url('assets/') ?>images/logo.png" alt="" height="75">
            </span>
        </a>

        <a href="<?php echo base_url('admin_root/') ?>" class="logo logo-light text-center">
            <span class="logo-sm">
                <img src="<?php echo base_url('assets/') ?>images/logo-sm.png" alt="" height="50">
            </span>
            <span class="logo-lg">
                <img src="<?php echo base_url('assets/') ?>images/logo.png" alt="" height="75">
            </span>
        </a>
    </div>

    <button type="button" class="btn btn-sm px-3 font-size-16 header-item waves-effect vertical-menu-btn">
        <i class="fa fa-fw fa-bars"></i>
    </button>

    <div data-simplebar class="sidebar-menu-scroll">

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>

                <li>
                    <a href="<?php echo base_url('admin_root/') ?>">
                        <i class="uil-home-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-title">Product Management</li>
                <li>
                    <a href="<?php echo base_url('admin_root/products/categories') ?>">
                        <i class="uil-list-ul"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('admin_root/products') ?>">
                        <i class="uil-box"></i>
                        <span>Products</span>
                    </a>
                </li>

                <li class="menu-title">Inventory</li>
                <li>
                    <a href="<?php echo base_url('admin_root/inventory') ?>">
                        <i class="uil-clipboard-notes"></i>
                        <span>Inventory Report</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('admin_root/inventory/add') ?>">
                        <i class="uil-plus-circle"></i>
                        <span>Add Inventory</span>
                    </a>
                </li>

                <li class="menu-title">Orders</li>
                <li>
                    <a href="<?php echo base_url('admin_root/orders') ?>">
                        <i class="uil-shopping-cart"></i>
                        <span>Order Report</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('admin_root/orders/pending') ?>">
                        <i class="uil-clock"></i>
                        <span>Pending Orders</span>
                    </a>
                </li>

                <li class="menu-title">Marketing</li>
                <li>
                    <a href="<?php echo base_url('admin_root/coupons') ?>">
                        <i class="uil-tag-alt"></i>
                        <span>Coupons</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('admin_root/banners') ?>">
                        <i class="uil-image"></i>
                        <span>Banners</span>
                    </a>
                </li>

                <li class="menu-title">Content</li>
                <li>
                    <a href="<?php echo base_url('admin_root/recipes') ?>">
                        <i class="uil-utensils"></i>
                        <span>Recipes</span>
                    </a>
                </li>

                <li class="menu-title">Reports</li>
                <li>
                    <a href="<?php echo base_url('admin_root/reports/sales') ?>">
                        <i class="uil-chart-line"></i>
                        <span>Sales Report</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('admin_root/reports/users') ?>">
                        <i class="uil-users-alt"></i>
                        <span>Users Report</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('admin_root/reports/expenses') ?>">
                        <i class="uil-money-withdrawal"></i>
                        <span>Expenses Report</span>
                    </a>
                </li>

                <li class="menu-title">Analytics</li>
                <li>
                    <a href="<?php echo base_url('admin_root/analytics') ?>">
                        <i class="uil-chart"></i>
                        <span>Traffic Overview</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo base_url('admin_root/analytics/sessions') ?>">
                        <i class="uil-browser"></i>
                        <span>Sessions</span>
                    </a>
                </li>

                <li class="menu-title">Feedback</li>
                <li>
                    <a href="<?php echo base_url('admin_root/reviews') ?>">
                        <i class="uil-star"></i>
                        <span>Customer Reviews</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <!-- Sidebar Footer - Outside scrollable area -->
    <div class="sidebar-footer">
        <div class="sidebar-version-info">

            <div class="company-credit mt-2">
                <small class="text-muted d-block">
                    <?php echo date('Y') ?> © <?php echo config_item('application_name') ?> v<?php echo config_item('app_version') ?>
                </small>
                <small class="text-muted d-block">
                    <i class="uil-code-branch me-1"></i>
                    Designed by
                    <?php if (config_item('author_link')): ?>
                        <a href="<?php echo config_item('author_link'); ?>" target="_blank" class="author-link">
                            <strong><?php echo config_item('author') ? config_item('author') : 'Developer'; ?></strong>
                        </a>
                    <?php else: ?>
                        <strong><?php echo config_item('author') ? config_item('author') : 'Developer'; ?></strong>
                    <?php endif; ?>
                </small>
            </div>
        </div>
    </div>
</div>
<!-- Left Sidebar End -->

<style>
    /* Make vertical-menu a flex container */
    .vertical-menu {
        display: flex;
        flex-direction: column;
    }

    /* Scrollable menu takes remaining space */
    .sidebar-menu-scroll {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }

    /* Footer stays at bottom, never overlaps */
    .sidebar-footer {
        flex-shrink: 0;
        padding: 15px 20px;
        background: rgba(255, 255, 255, 0.05);
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-version-info {
        text-align: center;
    }

    .sidebar-version-info small {
        font-size: 11px;
        opacity: 0.8;
    }

    .sidebar-version-info .company-credit strong,
    .sidebar-version-info .company-credit .author-link {
        color: inherit;
        font-weight: 600;
        text-decoration: none;
        transition: opacity 0.2s ease;
    }

    .sidebar-version-info .company-credit .author-link:hover {
        opacity: 0.8;
        text-decoration: underline;
    }

    /* Dark theme adjustments */
    .vertical-menu.dark .sidebar-footer {
        background: rgba(0, 0, 0, 0.2);
        border-top-color: rgba(255, 255, 255, 0.05);
    }

    /* Light theme adjustments */
    .vertical-menu:not(.dark) .sidebar-footer {
        background: rgba(0, 0, 0, 0.03);
        border-top-color: rgba(0, 0, 0, 0.1);
    }

    .vertical-menu:not(.dark) .sidebar-footer .text-muted {
        color: #6c757d !important;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .sidebar-footer {
            padding: 10px 15px;
        }

        .sidebar-version-info small {
            font-size: 10px;
        }

    }

    @media (max-width: 992px) {

        /* Hide sidebar by default on mobile */
        .vertical-menu {
            display: none;
        }

        /* Show sidebar when toggle is clicked */
        body.sidebar-enable .vertical-menu {
            display: flex;
        }
    }


    /* When sidebar is collapsed */
    .vertical-menu.mm-collapsed .sidebar-footer {
        display: none;
    }
</style>


<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">