<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Coupon</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/coupons') ?>">Coupons</a></li>
                            <li class="breadcrumb-item active">Edit Coupon</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <form class="needs-validation" id="edit_coupon" name="edit_coupon" action="<?php echo base_url('admin_root/coupons/update_coupon') ?>">
            <input type="hidden" name="cid" value="<?php echo $coupon_data->id ?>">
            <div class="row">
                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Coupon Details</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="title">Coupon Title<code>*</code></label>
                                        <input type="text" class="form-control" id="title" name="title" placeholder="e.g., Diwali Sale 20% Off" value="<?php echo $coupon_data->title ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="coupon_code">Coupon Code<code>*</code></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control text-uppercase" id="coupon_code" name="coupon_code" placeholder="e.g., DIWALI20" value="<?php echo $coupon_data->coupon_code ?>" required>
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
                                            <option value="1" <?php echo ($coupon_data->coupon_type == 1) ? 'selected' : '' ?>>Percentage (%)</option>
                                            <option value="2" <?php echo ($coupon_data->coupon_type == 2) ? 'selected' : '' ?>>Fixed Amount (₹)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="discount_value">Discount Value<code>*</code></label>
                                        <input type="number" step="0.01" class="form-control" id="discount_value" name="discount_value" placeholder="e.g., 20" value="<?php echo $coupon_data->discount_value ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4" id="max_discount_group">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="max_discount">Max Discount (₹)</label>
                                        <input type="number" step="0.01" class="form-control" id="max_discount" name="max_discount" placeholder="e.g., 100" value="<?php echo isset($coupon_data->max_discount) ? $coupon_data->max_discount : '' ?>">
                                        <small class="text-muted">Leave empty for no limit</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="min_amount">Minimum Order Amount (₹)</label>
                                        <input type="number" step="0.01" class="form-control" id="min_amount" name="min_amount" placeholder="e.g., 500" value="<?php echo $coupon_data->min_amount ?>">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="usage_limit">Usage Limit</label>
                                        <input type="number" class="form-control" id="usage_limit" name="usage_limit" placeholder="e.g., 100" value="<?php echo isset($coupon_data->usage_limit) ? $coupon_data->usage_limit : 0 ?>">
                                        <small class="text-muted">0 = Unlimited</small>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="start_date">Start Date<code>*</code></label>
                                        <input type="text" class="form-control datepicker" id="start_date" name="start_date" placeholder="Select start date" value="<?php echo $coupon_data->start_date ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group mb-3">
                                        <label class="form-label" for="end_date">End Date</label>
                                        <input type="text" class="form-control datepicker" id="end_date" name="end_date" placeholder="Select end date" value="<?php echo $coupon_data->end_date ?>">
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
                                <i class="uil uil-check me-1"></i> Update Coupon
                            </button>
                            <a href="<?php echo base_url('admin_root/coupons') ?>" class="btn btn-outline-secondary mt-2 w-100">
                                <i class="uil uil-arrow-left me-1"></i> Back to Coupons
                            </a>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Usage Statistics</h5>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Times Used:</span>
                                <span class="badge bg-info"><?php echo isset($coupon_data->used_count) ? $coupon_data->used_count : 0 ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>Usage Limit:</span>
                                <span class="badge bg-secondary"><?php echo (isset($coupon_data->usage_limit) && $coupon_data->usage_limit > 0) ? $coupon_data->usage_limit : 'Unlimited' ?></span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Created:</span>
                                <span class="text-muted"><?php echo date('d M Y', strtotime($coupon_data->created_at)) ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Coupon Preview</h5>
                            <div class="border rounded p-3 text-center bg-light">
                                <div class="fs-4 fw-bold text-primary" id="preview_code"><?php echo $coupon_data->coupon_code ?></div>
                                <div class="text-muted" id="preview_discount">
                                    <?php echo ($coupon_data->coupon_type == 1) ? $coupon_data->discount_value . '% off' : '₹' . $coupon_data->discount_value . ' off' ?>
                                </div>
                                <div class="small text-muted" id="preview_min">Min order: ₹<?php echo number_format($coupon_data->min_amount, 2) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
