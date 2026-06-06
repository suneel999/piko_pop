<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Order #<?php echo $order->order_number ?></h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="<?php echo base_url('admin_root/orders/pending') ?>">Orders</a></li>
                            <li class="breadcrumb-item active">Order Details</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <!-- Order Summary Card -->
            <div class="col-xl-4">
                <div class="card checkout-order-summary">
                    <div class="card-body">
                        <div class="p-3 bg-light mb-4 rounded">
                            <h5 class="font-size-16 mb-0">Order Summary <span class="float-end ms-2">#<?php echo $order->order_number ?></span></h5>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-centered mb-0 table-nowrap">
                                <thead>
                                    <tr>
                                        <th class="border-top-0" style="width: 80px;" scope="col">Product</th>
                                        <th class="border-top-0" scope="col">Details</th>
                                        <th class="border-top-0" scope="col">Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($order_items)) {
                                        foreach ($order_items as $item) {
                                            $image = !empty($item->product_image) ? base_url('uploads/products/' . $item->product_image) : base_url('assets/images/no-image.png');
                                    ?>
                                            <tr>
                                                <th scope="row"><img src="<?php echo $image ?>" alt="product-img" title="product-img" class="avatar-md rounded-3 shadow"></th>
                                                <td>
                                                    <h6 class="font-size-14 text-truncate mb-1"><?php echo htmlspecialchars($item->product_name) ?></h6>
                                                    <?php if (!empty($item->variant_name)): ?>
                                                        <small class="text-muted"><?php echo htmlspecialchars($item->variant_name) ?></small><br>
                                                    <?php endif; ?>
                                                    <small class="text-muted">₹<?php echo number_format($item->unit_price, 2) ?> x <?php echo floatval($item->quantity) ?></small>
                                                </td>
                                                <td>₹<?php echo number_format($item->total_price, 2) ?></td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>

                                    <tr>
                                        <td colspan="2">
                                            <h6 class="font-size-14 m-0">Sub Total :</h6>
                                        </td>
                                        <td>
                                            ₹<?php echo number_format($order->subtotal, 2) ?>
                                        </td>
                                    </tr>
                                    <?php if ($order->discount_amount > 0): ?>
                                        <tr>
                                            <td colspan="2">
                                                <h6 class="font-size-14 m-0">Discount <?php if (!empty($order->coupon_code)): ?><small class="fst-italic">(<?php echo strtoupper($order->coupon_code) ?>)</small><?php endif; ?> :</h6>
                                            </td>
                                            <td class="text-success">
                                                -₹<?php echo number_format($order->discount_amount, 2) ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                    <tr>
                                        <td colspan="2">
                                            <h6 class="font-size-14 m-0">Delivery Charge :</h6>
                                        </td>
                                        <td>
                                            <?php echo $order->delivery_charge > 0 ? '₹' . number_format($order->delivery_charge, 2) : '<span class="text-success">Free</span>' ?>
                                        </td>
                                    </tr>
                                    <?php if ($order->tax_amount > 0): ?>
                                        <tr>
                                            <td colspan="2">
                                                <h6 class="font-size-14 m-0">Tax :</h6>
                                            </td>
                                            <td>
                                                ₹<?php echo number_format($order->tax_amount, 2) ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>

                                    <tr class="bg-light">
                                        <td colspan="2">
                                            <h5 class="font-size-14 m-0">Total Paid:</h5>
                                        </td>
                                        <td>
                                            <h4 class="m-0">₹<?php echo number_format($order->total_amount, 2) ?></h4>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Shipping & Payment Info -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Delivery Address</h4>

                        <h6 class="fw-bold"><?php echo htmlspecialchars($order->delivery_fullname) ?></h6>
                        <p class="fs-6 m-0 pe-md-5">
                            <?php echo htmlspecialchars($order->delivery_address) ?><br>
                            <?php echo htmlspecialchars($order->delivery_city) ?>
                            <?php if (!empty($order->delivery_state)): ?>, <?php echo htmlspecialchars($order->delivery_state) ?><?php endif; ?>
                            - <?php echo htmlspecialchars($order->delivery_pincode) ?>
                            <?php if (!empty($order->delivery_landmark)): ?>
                                <br><small class="text-muted">Landmark: <?php echo htmlspecialchars($order->delivery_landmark) ?></small>
                            <?php endif; ?>
                        </p>

                        <div class="mt-4">
                            <h6 class="fw-bold">Phone :</h6>
                            <p class="fs-6 m-0"><a href="tel:<?php echo $order->delivery_phone ?>"><?php echo $order->delivery_phone ?></a></p>
                        </div>

                        <?php if (!empty($order->user_email)): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold">Email :</h6>
                                <p class="fs-6 m-0"><a href="mailto:<?php echo $order->user_email ?>"><?php echo $order->user_email ?></a></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Payment Details</h4>

                        <div class="mt-2">
                            <h6 class="fw-bold">Payment Method :</h6>
                            <p class="fs-6 m-0"><?php echo ucwords($order->payment_method) ?></p>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold">Payment Status :</h6>
                            <?php
                            $payment_class = $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'refunded' ? 'danger' : 'warning');
                            ?>
                            <span class="badge bg-soft-<?php echo $payment_class ?> text-<?php echo $payment_class ?> font-size-14"><?php echo ucwords($order->payment_status) ?></span>
                        </div>

                        <?php if (!empty($order->payment_id)): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold">Payment ID :</h6>
                                <p class="fs-6 m-0 text-break"><small><?php echo $order->payment_id ?></small></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Order Actions</h4>

                        <div class="mt-2">
                            <h6 class="fw-bold">Current Status :</h6>
                            <?php
                            $status_config = isset($order_statuses[$order->order_status]) ? $order_statuses[$order->order_status] : $order_statuses['pending'];
                            ?>
                            <span id="current_status_badge" class="badge bg-soft-<?php echo $status_config['badge'] ?> text-<?php echo $status_config['badge'] ?> font-size-14"><?php echo $status_config['label'] ?></span>
                        </div>

                        <div class="mt-4">
                            <h6 class="fw-bold">Order Date :</h6>
                            <p class="fs-6 m-0"><?php echo date('d M, Y h:i A', strtotime($order->created_at)) ?></p>
                        </div>

                        <?php if (!empty($order->delivered_at)): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold">Delivered At :</h6>
                                <p class="fs-6 m-0"><?php echo date('d M, Y h:i A', strtotime($order->delivered_at)) ?></p>
                            </div>
                        <?php endif; ?>

                        <input type="hidden" name="order_id" id="order_id" value="<?php echo $order->id ?>">

                        <!-- Status Update Section -->
                        <?php if (!in_array($order->order_status, array('delivered', 'cancelled', 'returned'))): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold">Update Status :</h6>

                                <?php if (!empty($next_status)): ?>
                                    <button type="button" id="btn_next_status" class="btn btn-success w-100 mb-2" data-next-status="<?php echo $next_status ?>" data-next-label="<?php echo $order_statuses[$next_status]['label'] ?>">
                                        <i class="uil uil-arrow-right me-1"></i> Move to <?php echo $order_statuses[$next_status]['label'] ?>
                                    </button>
                                <?php endif; ?>

                                <?php if ($can_cancel): ?>
                                    <button type="button" id="btn_cancel_order" class="btn btn-outline-danger w-100 mb-2">
                                        <i class="uil uil-times-circle me-1"></i> Cancel Order
                                    </button>
                                <?php endif; ?>

                                <?php if ($can_refund): ?>
                                    <button type="button" id="btn_refund_order" class="btn btn-danger w-100">
                                        <i class="uil uil-money-withdraw me-1"></i> Issue Refund (₹<?php echo number_format($order->total_amount, 2) ?>)
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php elseif ($order->order_status == 'delivered'): ?>
                            <div class="mt-4">
                                <div class="alert alert-success mb-3">
                                    <i class="uil uil-check-circle me-1"></i> This order has been delivered successfully.
                                </div>

                                <!-- Feedback Link Section -->
                                <h6 class="fw-bold">Customer Feedback :</h6>
                                <div id="feedback_link_section">
                                    <button type="button" class="btn btn-outline-primary w-100" id="btn_generate_review_link">
                                        <i class="uil uil-link me-1"></i> Generate Review Link
                                    </button>
                                </div>
                                <div id="feedback_link_result" class="d-none mt-2">
                                    <div class="input-group input-group-sm">
                                        <input type="text" class="form-control" id="generated_review_link" readonly>
                                        <button class="btn btn-primary" type="button" id="btn_copy_review_link"><i class="uil-copy"></i> Copy</button>
                                    </div>
                                    <div id="feedback_status_info" class="mt-2"></div>
                                </div>
                            </div>
                        <?php elseif ($order->order_status == 'cancelled'): ?>
                            <div class="mt-4">
                                <div class="alert alert-danger mb-0">
                                    <i class="uil uil-times-circle me-1"></i> This order has been cancelled.
                                    <?php if (!empty($order->cancellation_reason)): ?>
                                        <br><small>Reason: <?php echo htmlspecialchars($order->cancellation_reason) ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php if ($can_refund): ?>
                                    <button type="button" id="btn_refund_order" class="btn btn-danger w-100 mt-2">
                                        <i class="uil uil-money-withdraw me-1"></i> Issue Refund (₹<?php echo number_format($order->total_amount, 2) ?>)
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Customer Notes -->
                <?php if (!empty($order->customer_notes)): ?>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Customer Notes</h4>
                            <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($order->customer_notes)) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Admin Notes -->
                <?php if (!empty($order->admin_notes)): ?>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Admin Notes</h4>
                            <p class="text-muted mb-0"><?php echo nl2br(htmlspecialchars($order->admin_notes)) ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Status History -->
                <?php if (!empty($status_history)): ?>
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-3">Status History</h4>
                            <ul class="list-unstyled activity-wid mb-0">
                                <?php foreach ($status_history as $history): ?>
                                    <li class="activity-list">
                                        <div class="activity-icon avatar-xs">
                                            <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                                <i class="uil uil-clock-three"></i>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-muted mb-0 font-size-13">
                                                <strong><?php echo ucwords(str_replace('_', ' ', $history->status)) ?></strong>
                                                <span class="text-muted ms-2"><?php echo date('d M Y, h:i A', strtotime($history->created_at)) ?></span>
                                            </p>
                                            <?php if (!empty($history->notes)): ?>
                                                <small class="text-muted"><?php echo htmlspecialchars($history->notes) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<style>
    .activity-list {
        position: relative;
        padding-left: 40px;
        margin-bottom: 20px;
    }

    .activity-list:before {
        content: "";
        position: absolute;
        left: 12px;
        top: 30px;
        height: calc(100% + 10px);
        border-left: 2px dashed #e9e9ef;
    }

    .activity-list:last-child:before {
        display: none;
    }

    .activity-icon {
        position: absolute;
        left: 0;
        top: 0;
    }
</style>
