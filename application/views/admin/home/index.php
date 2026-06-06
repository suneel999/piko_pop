<div class="page-content">
    <div class="container-fluid">

        <!-- Page Title -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="page-title-custom">Dashboard</h4>
                    <div class="text-muted">Welcome back! Here's your store overview</div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Row 1 - Revenue & Orders Overview -->
        <div class="row g-3 mb-3">
            <!-- Total Revenue -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Total Revenue</p>
                                <h3 class="stat-value">₹<?php echo number_format($total_revenue, 2) ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-success">
                                <i class="uil uil-money-bill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-success-subtle text-success">
                            <i class="uil uil-check-circle"></i> All Time Sales
                        </span>
                    </div>
                </div>
            </div>

            <!-- This Month Revenue -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">This Month Revenue</p>
                                <h3 class="stat-value">₹<?php echo number_format($month_revenue, 2) ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-primary">
                                <i class="uil uil-chart-line"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-primary-subtle text-primary">
                            <i class="uil uil-calendar-alt"></i> <?php echo date('F Y') ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Total Orders</p>
                                <h3 class="stat-value"><?php echo $total_orders ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-info">
                                <i class="uil uil-shopping-cart"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-info-subtle text-info">
                            <i class="uil uil-clock"></i> <?php echo $today_orders ?> orders today
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Total Customers</p>
                                <h3 class="stat-value"><?php echo $total_users ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-warning">
                                <i class="uil uil-users-alt"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-warning-subtle text-warning">
                            <i class="uil uil-user-plus"></i> <?php echo $new_users ?> new this month
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Row 2 - Order Status -->
        <div class="row g-3 mb-3">
            <!-- Pending Orders -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Pending Orders</p>
                                <h3 class="stat-value text-warning"><?php echo $pending_orders ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-warning">
                                <i class="uil uil-clock"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <a href="<?php echo base_url('admin_root/orders/pending') ?>" class="text-warning text-decoration-none">
                            <i class="uil uil-arrow-right"></i> View pending orders
                        </a>
                    </div>
                </div>
            </div>

            <!-- Processing Orders -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Processing</p>
                                <h3 class="stat-value text-info"><?php echo $processing_orders ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-info">
                                <i class="uil uil-sync"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="text-muted">Confirmed & being packed</span>
                    </div>
                </div>
            </div>

            <!-- Shipped Orders -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">In Transit</p>
                                <h3 class="stat-value text-primary"><?php echo $shipped_orders ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-primary">
                                <i class="uil uil-truck"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="text-muted">Shipped & out for delivery</span>
                    </div>
                </div>
            </div>

            <!-- Delivered Orders -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Delivered</p>
                                <h3 class="stat-value text-success"><?php echo $delivered_orders ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-success">
                                <i class="uil uil-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-success-subtle text-success">
                            <i class="uil uil-check"></i> Successfully delivered
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards Row 3 - Products & Inventory -->
        <div class="row g-3 mb-3">
            <!-- Total Products -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Total Products</p>
                                <h3 class="stat-value"><?php echo $total_products ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-primary">
                                <i class="uil uil-box"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <a href="<?php echo base_url('admin_root/catalog/products') ?>" class="text-primary text-decoration-none">
                            <i class="uil uil-arrow-right"></i> Manage products
                        </a>
                    </div>
                </div>
            </div>

            <!-- Low Stock Alert -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Low Stock Alert</p>
                                <h3 class="stat-value text-danger"><?php echo $low_stock_products ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-danger">
                                <i class="uil uil-exclamation-triangle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="stat-badge bg-danger-subtle text-danger">
                            <i class="uil uil-info-circle"></i> Stock 10 units or less
                        </span>
                    </div>
                </div>
            </div>

            <!-- Total Categories -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Categories</p>
                                <h3 class="stat-value"><?php echo $total_categories ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-info">
                                <i class="uil uil-folder-open"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <a href="<?php echo base_url('admin_root/catalog/categories') ?>" class="text-info text-decoration-none">
                            <i class="uil uil-arrow-right"></i> Manage categories
                        </a>
                    </div>
                </div>
            </div>

            <!-- Pending Payment -->
            <div class="col-xl-3 col-md-6">
                <div class="card dashboard-card">
                    <div class="stat-card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="flex-grow-1">
                                <p class="stat-label">Pending Payment</p>
                                <h3 class="stat-value text-warning"><?php echo $pending_payment ?></h3>
                            </div>
                            <div class="stat-icon icon-bg-warning">
                                <i class="uil uil-wallet"></i>
                            </div>
                        </div>
                    </div>
                    <div class="stat-footer">
                        <span class="text-muted">Awaiting payment confirmation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 1 - Monthly Sales (Full Width) -->
        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card chart-card">
                    <div class="chart-card-body">
                        <h5 class="chart-title">Monthly Orders & Revenue (<?php echo date('Y') ?>)</h5>
                        <div id="monthly-orders-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row 2 - Order Status & Payment Methods -->
        <div class="row g-3 mb-3">
            <!-- Order Status -->
            <div class="col-xl-6 col-lg-6">
                <div class="card chart-card">
                    <div class="chart-card-body">
                        <h5 class="chart-title">Order Status Breakdown</h5>
                        <div id="order-status-chart"></div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="col-xl-6 col-lg-6">
                <div class="card chart-card">
                    <div class="chart-card-body">
                        <h5 class="chart-title">Payment Methods</h5>
                        <div id="payment-method-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders & Top Products -->
        <div class="row g-3">
            <!-- Recent Orders -->
            <div class="col-xl-7">
                <div class="card list-card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Recent Orders</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="recent-orders-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Order #</th>
                                        <th>Customer</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="col-xl-5">
                <div class="card list-card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Top Selling Products</h5>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="top-products-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Sold</th>
                                        <th>Revenue</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Populated via AJAX -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
