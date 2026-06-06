<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Order Report</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Orders</a></li>
                            <li class="breadcrumb-item active">Order Report</li>
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
                        <form class="needs-validation" id="orders_report_form" name="orders_report_form" action="">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="status">Order Status</label>
                                        <select class="form-control select2" name="status" id="status">
                                            <option value="">All Statuses</option>
                                            <?php
                                            foreach ($order_statuses as $status_key => $status_info) {
                                                echo '<option value="' . $status_key . '">' . $status_info['label'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="payment_status">Payment Status</label>
                                        <select class="form-control select2" name="payment_status" id="payment_status">
                                            <option value="">All</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid" selected>Paid</option>
                                            <option value="failed">Failed</option>
                                            <option value="refunded">Refunded</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="date_from">From Date</label>
                                        <input type="text" class="form-control flatpickr-date" name="date_from" id="date_from" placeholder="Select date">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="date_to">To Date</label>
                                        <input type="text" class="form-control flatpickr-date" name="date_to" id="date_to" placeholder="Select date">
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
            <div class="col-md-4">
                <div class="card mini-stats-wid">
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="flex-grow-1">
                                <p class="text-muted fw-medium mb-2">Total Orders</p>
                                <h4 class="mb-0" id="summary_total_orders">0</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="uil-shopping-bag font-size-24"></i>
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
                                <p class="text-muted fw-medium mb-2">Total Order Value</p>
                                <h4 class="mb-0">₹<span id="summary_total_amount">0.00</span></h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-info">
                                <span class="avatar-title">
                                    <i class="uil-money-bill font-size-24"></i>
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
                                <p class="text-muted fw-medium mb-2">Total Paid (Completed)</p>
                                <h4 class="mb-0 text-success">₹<span id="summary_total_paid">0.00</span></h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-success">
                                <span class="avatar-title">
                                    <i class="uil-check-circle font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #orders_report_data_length {
                float: right !important;
            }
        </style>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="orders_report_data" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Customer</th>
                                        <th>Phone</th>
                                        <th>Amount</th>
                                        <th>Payment</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">Totals:</th>
                                        <th id="footer_total_amount">-</th>
                                        <th colspan="3"></th>
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
