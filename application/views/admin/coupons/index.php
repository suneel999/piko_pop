<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Coupons</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Marketing</a></li>
                            <li class="breadcrumb-item active">Coupons</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-md-4">
                <div>
                    <a type="button" href="<?php echo base_url('admin_root/coupons/add') ?>" class="btn btn-success waves-effect waves-light mb-3"><i class="mdi mdi-plus me-1"></i> Add Coupon</a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive mb-4">
                            <table class="table table-centered datatable dt-responsive nowrap table-card-list" style="border-collapse: collapse; border-spacing: 0 12px; width: 100%;" id="coupons_table">
                                <thead>
                                    <tr class="bg-transparent">
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Code</th>
                                        <th>Type</th>
                                        <th>Discount</th>
                                        <th>Min Order</th>
                                        <th>Validity</th>
                                        <th>Usage</th>
                                        <th>Status</th>
                                        <th style="width: 120px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($coupons)): ?>
                                        <?php foreach ($coupons as $coupon): ?>
                                            <?php
                                            // Status badge
                                            if ($coupon->status == 1) {
                                                $status_badge = '<span class="badge bg-soft-success text-success">Active</span>';
                                                $next_status = 2;
                                            } else {
                                                $status_badge = '<span class="badge bg-soft-warning text-warning">Inactive</span>';
                                                $next_status = 1;
                                            }

                                            // Coupon type display
                                            if ($coupon->coupon_type == 1) {
                                                $type_display = '<span class="badge bg-soft-info text-info">Percentage</span>';
                                                $discount_display = $coupon->discount_value . '%';
                                            } else {
                                                $type_display = '<span class="badge bg-soft-primary text-primary">Fixed</span>';
                                                $discount_display = '₹' . number_format($coupon->discount_value, 2);
                                            }

                                            // Validity display
                                            $today = date('Y-m-d');
                                            $validity = '';
                                            if (!empty($coupon->start_date) && !empty($coupon->end_date)) {
                                                $validity = date('d M', strtotime($coupon->start_date)) . ' - ' . date('d M Y', strtotime($coupon->end_date));
                                                if ($coupon->end_date < $today) {
                                                    $validity .= ' <span class="badge bg-soft-danger text-danger">Expired</span>';
                                                }
                                            } elseif (!empty($coupon->start_date)) {
                                                $validity = 'From ' . date('d M Y', strtotime($coupon->start_date));
                                            } else {
                                                $validity = 'No limit';
                                            }

                                            // Usage display
                                            $used = isset($coupon->used_count) ? $coupon->used_count : 0;
                                            $limit = isset($coupon->usage_limit) && $coupon->usage_limit > 0 ? $coupon->usage_limit : '∞';
                                            $usage_display = $used . ' / ' . $limit;
                                            ?>
                                            <tr>
                                                <td><?php echo $coupon->id ?></td>
                                                <td><?php echo $coupon->title ?></td>
                                                <td><code class="fw-bold"><?php echo $coupon->coupon_code ?></code></td>
                                                <td><?php echo $type_display ?></td>
                                                <td class="fw-bold"><?php echo $discount_display ?></td>
                                                <td>₹<?php echo number_format($coupon->min_amount, 2) ?></td>
                                                <td><?php echo $validity ?></td>
                                                <td><?php echo $usage_display ?></td>
                                                <td><?php echo $status_badge ?></td>
                                                <td>
                                                    <a href="<?php echo base_url('admin_root/coupons/edit/') . $coupon->id ?>" class="px-2 text-primary" title="Edit">
                                                        <i class="uil uil-pen font-size-18"></i>
                                                    </a>
                                                    <button onclick="change_status(<?php echo $coupon->id ?>, <?php echo $next_status ?>)" class="btn px-2 text-info" title="Toggle Status">
                                                        <i class="uil uil-eye font-size-18"></i>
                                                    </button>
                                                    <button onclick="delete_coupon(<?php echo $coupon->id ?>)" class="btn px-2 text-danger" title="Delete">
                                                        <i class="uil uil-trash-alt font-size-18"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end row -->

    </div> <!-- container-fluid -->
</div>
<!-- End Page-content -->
