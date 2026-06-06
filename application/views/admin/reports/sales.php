<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Sales Report</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                            <li class="breadcrumb-item active">Sales Report</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->


        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <form class="needs-validation" id="sales_report_form" name="sales_report_form" action="">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="date_from">Date From</label>
                                        <input type="text" class="form-control flatpickr-date" name="date_from" id="date_from" placeholder="Select date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="date_to">Date To</label>
                                        <input type="text" class="form-control flatpickr-date" name="date_to" id="date_to" placeholder="Select date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="payment_method">Payment Method</label>
                                        <select class="form-control select2" name="payment_method" id="payment_method">
                                            <option value="">All Methods</option>
                                            <option value="stripe">Stripe</option>
                                            <option value="cod">Cash on Delivery</option>
                                            <option value="upi">UPI</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-primary w-100" type="submit" id="submit_button">
                                            <i class="uil-search-alt me-1"></i> Get Report
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label">&nbsp;</label>
                                        <button class="btn btn-outline-secondary w-100" type="button" id="clear_filters">
                                            <i class="uil-times me-1"></i> Clear
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- end card -->
            </div> <!-- end col -->
        </div>

        <!-- Summary Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Sales</p>
                                <h4 class="mb-0 text-success" id="summary_total_sales">₹0.00</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="uil-money-bill font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Orders</p>
                                <h4 class="mb-0 text-primary" id="summary_total_orders">0</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="uil-shopping-cart font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Avg. Order Value</p>
                                <h4 class="mb-0 text-info" id="summary_avg_order">₹0.00</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="uil-chart-line font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Discounts</p>
                                <h4 class="mb-0 text-warning" id="summary_total_discount">₹0.00</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="uil-percentage font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row">
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Items Sold</p>
                                <h4 class="mb-0" id="summary_total_items">0</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-secondary">
                                <span class="avatar-title">
                                    <i class="uil-box font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Subtotal (Before Discounts)</p>
                                <h4 class="mb-0" id="summary_subtotal">₹0.00</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-dark">
                                <span class="avatar-title">
                                    <i class="uil-receipt font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Shipping Collected</p>
                                <h4 class="mb-0" id="summary_shipping">₹0.00</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-danger">
                                <span class="avatar-title">
                                    <i class="uil-truck font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #sales_report_data_length {
                float: right !important;
            }
        </style>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="sales_report_data" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Subtotal</th>
                                        <th>Discount</th>
                                        <th>Shipping</th>
                                        <th>Total</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Totals:</th>
                                        <th id="footer_subtotal">-</th>
                                        <th id="footer_discount">-</th>
                                        <th id="footer_shipping">-</th>
                                        <th id="footer_total">-</th>
                                        <th colspan="2"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- end card -->
            </div> <!-- end col -->
        </div>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
