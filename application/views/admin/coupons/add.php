<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Add Coupon</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/coupons') ?>">Coupons</a></li>
                            <li class="breadcrumb-item active">Add Coupon</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <form class="needs-validation" id="add_coupon" name="add_coupon" action="<?php echo base_url('admin_root/coupons/save_coupon') ?>">
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Coupon Details</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="title">Coupon Title<code>*</code></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Diwali Sale 20% Off" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="coupon_code">Coupon Code<code>*</code></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-uppercase" id="coupon_code" name="coupon_code" placeholder="e.g., DIWALI20" required>
                                            <button class="btn btn-outline-secondary" type="button" id="generate_code" title="Generate Code">
                                                <i class="uil uil-sync"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="coupon_type">Discount Type<code>*</code></label>
                                        <select name="coupon_type" id="coupon_type" class="form-control" required>
                                            <option value="1">Percentage (%)</option>
                                            <option value="2">Fixed Amount (₹)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="discount_value">Discount Value<code>*</code></label>
                                        <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" placeholder="e.g., 20" required>
                                    </div>
                                </div>
                                <div class="col-md-4" id="max_discount_group">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="max_discount">Max Discount (₹)</label>
                                        <input type="number" step="0.01" class="form-control" id="max_discount" name="max_discount" placeholder="e.g., 100">
                                        <small class="text-muted">Leave empty for no limit</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="min_amount">Minimum Order Amount (₹)</label>
                                        <input type="number" step="0.01" class="form-control" id="min_amount" name="min_amount" placeholder="e.g., 500" value="0">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="usage_limit">Usage Limit</label>
                                        <input type="number" class="form-control" id="usage_limit" name="usage_limit" placeholder="e.g., 100" value="0">
                                        <small class="text-muted">0 = Unlimited</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="start_date">Start Date<code>*</code></label>
                                        <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Select start date" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="end_date">End Date</label>
                                        <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="Select end date">
                                        <small class="text-muted">Leave empty for no expiry</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <button class="btn btn-primary w-100" type="submit" id="submit_button">
                                <i class="uil uil-plus me-1"></i> Create Coupon
                            </button>
                            <a href="<?php echo base_url('admin_root/coupons') ?>" class="btn btn-outline-secondary mt-2 w-100">
                                <i class="uil uil-arrow-left me-1"></i> Back to Coupons
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Coupon Preview</h5>
                            <div class="border rounded p-3 text-center bg-light">
                                <div class="fs-4 fw-bold text-primary" id="preview_code">CODE</div>
                                <div class="text-muted" id="preview_discount">- discount</div>
                                <div class="small text-muted" id="preview_min">Min order: ₹0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
