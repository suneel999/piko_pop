<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Expenses Report</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Reports</a></li>
                            <li class="breadcrumb-item active">Expenses Report</li>
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
                        <form class="needs-validation" id="expenses_report_form" name="expenses_report_form" action="">
                            <div class="row align-items-end">
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
                                        <label class="form-label" for="category">Category</label>
                                        <select class="form-control select2" name="category" id="category">
                                            <option value="">All Categories</option>
                                            <option value="Masalas">Masalas</option>
                                            <option value="Packing">Packing</option>
                                            <option value="Transport">Transport</option>
                                            <option value="Covers">Covers</option>
                                            <option value="Salary">Salary</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <button class="btn btn-primary w-100" type="submit" id="submit_button">
                                            <i class="uil-search-alt me-1"></i> Get Report
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group mb-3">
                                        <button class="btn btn-outline-secondary w-100" type="button" id="clear_filters">
                                            <i class="uil-times me-1"></i> Clear
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group mb-3">
                                        <button class="btn btn-success w-100" type="button" id="btn_add_expense" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                            <i class="uil-plus me-1"></i> Add Expense
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
                                <p class="text-muted fw-medium mb-2">Total Expenses</p>
                                <h4 class="mb-0 text-danger" id="summary_total_expenses">₹0.00</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-danger">
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
                                <p class="text-muted fw-medium mb-2">Total Entries</p>
                                <h4 class="mb-0 text-primary" id="summary_total_count">0</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-primary">
                                <span class="avatar-title">
                                    <i class="uil-receipt font-size-24"></i>
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
                                <p class="text-muted fw-medium mb-2">Avg. Expense</p>
                                <h4 class="mb-0 text-info" id="summary_avg_expense">₹0.00</h4>
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
                                <p class="text-muted fw-medium mb-2">Highest Expense</p>
                                <h4 class="mb-0 text-warning" id="summary_highest_expense">₹0.00</h4>
                            </div>
                            <div class="mini-stat-icon avatar-sm align-self-center rounded-circle bg-warning">
                                <span class="avatar-title">
                                    <i class="uil-arrow-growth font-size-24"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #expenses_report_data_length {
                float: right !important;
            }
        </style>
        <div class="row">
            <div class="col-xl-12 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="expenses_report_data" class="table table-striped table-bordered dt-responsive nowrap" style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Comments</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total:</th>
                                        <th id="footer_total">-</th>
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

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="add_expense_form" action="<?php echo base_url('admin_root/reports/save_expense') ?>" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" for="expense_title">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" id="expense_title" placeholder="Enter expense title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="expense_category">Category <span class="text-danger">*</span></label>
                        <select class="form-control" name="category" id="expense_category" required>
                            <option value="">Select Category</option>
                            <option value="Masalas">Masalas</option>
                            <option value="Packing">Packing</option>
                            <option value="Transport">Transport</option>
                            <option value="Covers">Covers</option>
                            <option value="Salary">Salary</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="expense_date">Expense Date <span class="text-danger">*</span></label>
                        <input type="text" class="form-control flatpickr-date" name="expense_date" id="expense_date" placeholder="Select date" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="expense_amount">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" id="expense_amount" placeholder="Enter amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="expense_comments">Comments</label>
                        <textarea class="form-control" name="comments" id="expense_comments" rows="3" placeholder="Enter any comments (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" id="save_expense_btn">
                        <i class="uil-check me-1"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
