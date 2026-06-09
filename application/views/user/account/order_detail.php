<!-- Main Content -->
<div class="site-page">
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-primary/10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('account/profile'); ?>" class="text-gray hover:text-primary transition-colors">My Account</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('account/orders'); ?>" class="text-gray hover:text-primary transition-colors">My PIKO Orders</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium">#<?php echo $order->order_number; ?></span>
            </nav>
        </div>
    </div>

    <?php
        // Determine status badge styling
        $status_classes = '';
        $status_icon = '';
        $status_text = ucfirst($order->order_status);
        $status_bg = '';
        $status_color = '';

        switch ($order->order_status) {
            case 'pending':
            case 'processing':
                $status_classes = 'bg-amber-100 text-amber-700';
                $status_icon = 'fa-box';
                $status_bg = 'bg-amber-100';
                $status_color = 'text-amber-600';
                break;
            case 'shipped':
                $status_classes = 'bg-blue-100 text-blue-700';
                $status_icon = 'fa-truck-fast';
                $status_bg = 'bg-blue-100';
                $status_color = 'text-blue-600';
                break;
            case 'out_for_delivery':
                $status_classes = 'bg-indigo-100 text-indigo-700';
                $status_icon = 'fa-truck';
                $status_bg = 'bg-indigo-100';
                $status_color = 'text-indigo-600';
                $status_text = 'Out for Delivery';
                break;
            case 'delivered':
                $status_classes = 'bg-green-100 text-green-700';
                $status_icon = 'fa-circle-check';
                $status_bg = 'bg-green-100';
                $status_color = 'text-green-600';
                break;
            case 'cancelled':
                $status_classes = 'bg-red-100 text-red-600';
                $status_icon = 'fa-xmark';
                $status_bg = 'bg-red-100';
                $status_color = 'text-red-500';
                break;
            default:
                $status_classes = 'bg-gray-100 text-gray-700';
                $status_icon = 'fa-clock';
                $status_bg = 'bg-gray-100';
                $status_color = 'text-gray-600';
        }

        // Calculate progress percentage (5 steps: 0, 25, 50, 75, 100)
        $progress = 0;
        switch ($order->order_status) {
            case 'pending':
                $progress = 0;
                break;
            case 'processing':
                $progress = 25;
                break;
            case 'shipped':
                $progress = 50;
                break;
            case 'out_for_delivery':
                $progress = 75;
                break;
            case 'delivered':
                $progress = 100;
                break;
        }

        $item_count = count($order_items);
    ?>

    <!-- Order Details Section -->
    <div class="container mx-auto px-4 py-6 md:py-10">
        <!-- Back Link & Title -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <a href="<?php echo base_url('account/orders'); ?>" class="inline-flex items-center gap-2 text-primary font-medium hover:underline mb-2">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                    <span>Back to My PIKO Orders</span>
                </a>
                <h1 class="text-2xl md:text-3xl font-bold text-dark">Order #<?php echo $order->order_number; ?></h1>
            </div>
            <div class="flex items-center gap-3">
                <?php if ($order->order_status == 'delivered'): ?>
                <button class="px-4 py-2.5 border border-gray-200 rounded-full text-sm font-medium text-dark hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-download"></i>
                    <span>Download Invoice</span>
                </button>
                <?php endif; ?>
                <a href="<?php echo base_url('contact'); ?>" class="px-4 py-2.5 border border-gray-200 rounded-full text-sm font-medium text-dark hover:bg-gray-50 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-headset"></i>
                    <span>Need Help?</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Order Status Card -->
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5">
                    <div class="flex items-center justify-between px-5 md:px-6 py-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 <?php echo $status_bg; ?> rounded-full flex items-center justify-center">
                                <i class="fa-solid <?php echo $status_icon; ?> <?php echo $status_color; ?>"></i>
                            </div>
                            <div>
                                <h2 class="font-bold text-dark">Order Status</h2>
                                <?php if ($order->order_status == 'delivered'): ?>
                                <p class="text-sm text-green-600">Delivered on <?php echo !empty($order->delivered_at) ? date('M d, Y', strtotime($order->delivered_at)) : date('M d, Y', strtotime($order->updated_at)); ?></p>
                                <?php elseif ($order->order_status == 'shipped'): ?>
                                <p class="text-sm text-blue-600">Order Shipped</p>
                                <?php elseif ($order->order_status == 'out_for_delivery'): ?>
                                <p class="text-sm text-blue-600">Out for delivery</p>
                                <?php elseif ($order->order_status == 'processing'): ?>
                                <p class="text-sm text-amber-600">Being prepared</p>
                                <?php elseif ($order->order_status == 'cancelled'): ?>
                                <p class="text-sm text-red-500">Order was cancelled</p>
                                <?php else: ?>
                                <p class="text-sm text-gray">Order placed on <?php echo date('M d, Y', strtotime($order->created_at)); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 <?php echo $status_classes; ?> text-sm font-semibold rounded-full">
                            <i class="fa-solid <?php echo $status_icon; ?> text-xs"></i>
                            <?php echo $status_text; ?>
                        </span>
                    </div>

                    <?php if ($order->order_status != 'cancelled'): ?>
                    <!-- Order Timeline -->
                    <div class="p-5 md:p-6">
                        <?php
                            // Determine which steps are completed
                            $step1_completed = true; // Order always placed
                            $step2_completed = $progress >= 25;
                            $step3_completed = $progress >= 50;
                            $step4_completed = $progress >= 75;
                            $step5_completed = $progress >= 100;

                            // Calculate mobile progress line height percentage
                            $mobile_progress = min(100, ($progress / 100) * 100);
                        ?>

                        <!-- Mobile: Vertical Timeline -->
                        <div class="block md:hidden">
                            <div class="relative">
                                <!-- Background Line -->
                                <div class="absolute left-[15px] top-0 bottom-0 w-0.5 bg-gray-200"></div>
                                <!-- Progress Line -->
                                <div class="absolute left-[15px] top-0 w-0.5 bg-secondary transition-all duration-500" style="height: <?php echo $mobile_progress; ?>%;"></div>

                                <!-- Steps -->
                                <div class="space-y-5">
                                    <!-- Step 1 - Ordered -->
                                    <div class="relative flex items-start gap-4 pl-10">
                                        <div class="absolute left-0 w-8 h-8 <?php echo $step1_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-bag-shopping <?php echo $step1_completed ? 'text-white' : 'text-dark'; ?> text-xs"></i>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-semibold text-dark text-sm">Order Placed</p>
                                            <p class="text-xs text-gray"><?php echo date('M d, g:i A', strtotime($order->created_at)); ?></p>
                                        </div>
                                    </div>

                                    <!-- Step 2 - Processing -->
                                    <div class="relative flex items-start gap-4 pl-10">
                                        <div class="absolute left-0 w-8 h-8 <?php echo $step2_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-box <?php echo $step2_completed ? 'text-white' : 'text-dark'; ?> text-xs"></i>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-semibold text-dark text-sm">Processing</p>
                                            <p class="text-xs text-gray"><?php echo $step2_completed && !empty($order->processed_at) ? date('M d, g:i A', strtotime($order->processed_at)) : '--'; ?></p>
                                        </div>
                                    </div>

                                    <!-- Step 3 - Shipped -->
                                    <div class="relative flex items-start gap-4 pl-10">
                                        <div class="absolute left-0 w-8 h-8 <?php echo $step3_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-truck <?php echo $step3_completed ? 'text-white' : 'text-dark'; ?> text-xs"></i>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-semibold text-dark text-sm">Shipped</p>
                                            <p class="text-xs text-gray"><?php echo $step3_completed && !empty($order->shipped_at) ? date('M d, g:i A', strtotime($order->shipped_at)) : '--'; ?></p>
                                        </div>
                                    </div>

                                    <!-- Step 4 - Out for Delivery -->
                                    <div class="relative flex items-start gap-4 pl-10">
                                        <div class="absolute left-0 w-8 h-8 <?php echo $step4_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-truck-fast <?php echo $step4_completed ? 'text-white' : 'text-dark'; ?> text-xs"></i>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-semibold text-dark text-sm">Out for Delivery</p>
                                            <p class="text-xs text-gray"><?php echo $step4_completed && !empty($order->out_for_delivery_at) ? date('M d, g:i A', strtotime($order->out_for_delivery_at)) : '--'; ?></p>
                                        </div>
                                    </div>

                                    <!-- Step 5 - Delivered -->
                                    <div class="relative flex items-start gap-4 pl-10">
                                        <div class="absolute left-0 w-8 h-8 <?php echo $step5_completed ? 'bg-secondary ring-4 ring-secondary/20' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-circle-check <?php echo $step5_completed ? 'text-white' : 'text-dark'; ?> text-xs"></i>
                                        </div>
                                        <div class="flex-1 pt-1">
                                            <p class="font-semibold <?php echo $step5_completed ? 'text-secondary' : 'text-dark'; ?> text-sm">Delivered</p>
                                            <p class="text-xs text-gray"><?php echo $step5_completed && !empty($order->delivered_at) ? date('M d, g:i A', strtotime($order->delivered_at)) : '--'; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Desktop: Horizontal Timeline -->
                        <div class="hidden md:block">
                            <!-- Steps Container -->
                            <div class="flex items-start">
                                <!-- Step 1 - Ordered -->
                                <div class="flex flex-col items-center text-center w-24">
                                    <div class="w-11 h-11 <?php echo $step1_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-md">
                                        <i class="fa-solid fa-bag-shopping <?php echo $step1_completed ? 'text-white' : 'text-dark'; ?> text-sm"></i>
                                    </div>
                                    <p class="font-semibold text-dark text-sm mt-3">Ordered</p>
                                    <p class="text-xs text-gray mt-0.5"><?php echo date('M d', strtotime($order->created_at)); ?></p>
                                </div>

                                <!-- Connector 1-2 -->
                                <div class="flex-1 h-1 <?php echo $step2_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full mt-5 -mx-1"></div>

                                <!-- Step 2 - Processing -->
                                <div class="flex flex-col items-center text-center w-24">
                                    <div class="w-11 h-11 <?php echo $step2_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-md">
                                        <i class="fa-solid fa-box <?php echo $step2_completed ? 'text-white' : 'text-dark'; ?> text-sm"></i>
                                    </div>
                                    <p class="font-semibold text-dark text-sm mt-3">Processing</p>
                                    <p class="text-xs text-gray mt-0.5"><?php echo $step2_completed && !empty($order->processed_at) ? date('M d', strtotime($order->processed_at)) : '--'; ?></p>
                                </div>

                                <!-- Connector 2-3 -->
                                <div class="flex-1 h-1 <?php echo $step3_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full mt-5 -mx-1"></div>

                                <!-- Step 3 - Shipped -->
                                <div class="flex flex-col items-center text-center w-24">
                                    <div class="w-11 h-11 <?php echo $step3_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-md">
                                        <i class="fa-solid fa-truck <?php echo $step3_completed ? 'text-white' : 'text-dark'; ?> text-sm"></i>
                                    </div>
                                    <p class="font-semibold text-dark text-sm mt-3">Shipped</p>
                                    <p class="text-xs text-gray mt-0.5"><?php echo $step3_completed && !empty($order->shipped_at) ? date('M d', strtotime($order->shipped_at)) : '--'; ?></p>
                                </div>

                                <!-- Connector 3-4 -->
                                <div class="flex-1 h-1 <?php echo $step4_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full mt-5 -mx-1"></div>

                                <!-- Step 4 - Out for Delivery -->
                                <div class="flex flex-col items-center text-center w-28">
                                    <div class="w-11 h-11 <?php echo $step4_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-md">
                                        <i class="fa-solid fa-truck-fast <?php echo $step4_completed ? 'text-white' : 'text-dark'; ?> text-sm"></i>
                                    </div>
                                    <p class="font-semibold text-dark text-sm mt-3">Out for Delivery</p>
                                    <p class="text-xs text-gray mt-0.5"><?php echo $step4_completed && !empty($order->out_for_delivery_at) ? date('M d', strtotime($order->out_for_delivery_at)) : '--'; ?></p>
                                </div>

                                <!-- Connector 4-5 -->
                                <div class="flex-1 h-1 <?php echo $step5_completed ? 'bg-secondary' : 'bg-gray-200'; ?> rounded-full mt-5 -mx-1"></div>

                                <!-- Step 5 - Delivered -->
                                <div class="flex flex-col items-center text-center w-24">
                                    <div class="w-11 h-11 <?php echo $step5_completed ? 'bg-secondary ring-4 ring-secondary/20' : 'bg-gray-200'; ?> rounded-full flex items-center justify-center shadow-md">
                                        <i class="fa-solid fa-circle-check <?php echo $step5_completed ? 'text-white' : 'text-dark'; ?> text-sm"></i>
                                    </div>
                                    <p class="font-semibold <?php echo $step5_completed ? 'text-secondary' : 'text-dark'; ?> text-sm mt-3">Delivered</p>
                                    <p class="text-xs text-gray mt-0.5"><?php echo $step5_completed && !empty($order->delivered_at) ? date('M d', strtotime($order->delivered_at)) : '--'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($order->sr_shipment_id) && in_array($order->order_status, ['processing','shipped', 'out_for_delivery'])): ?>
                    <!-- Shipping Info -->
                    <div class="px-5 md:px-6 pb-5 md:pb-6">
                        <?php
                            $shiprocket_track_url = 'https://shiprocket.co/tracking/' . $order->order_number;
                            if (!empty(config_item('shiprocket_company_id'))) {
                                $shiprocket_track_url .= '?company_id=' . config_item('shiprocket_company_id');
                            }
                        ?>
                        <div class="bg-gray-50 rounded-xl p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-sm flex-shrink-0">
                                        <i class="fa-solid fa-truck-fast text-secondary"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm text-gray">Shipped via Shiprocket</p>
                                        <p class="font-semibold text-dark text-sm truncate">ID: <?php echo htmlspecialchars($order->sr_order_id); ?></p>
                                    </div>
                                </div>
                                <a href="<?php echo $shiprocket_track_url; ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary-dark text-white text-sm font-semibold rounded-full transition-all flex-shrink-0">
                                    <span>Track</span>
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php endif; ?>
                </div>

                <!-- Order Items Card -->
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5">
                    <div class="flex items-center gap-3 px-5 md:px-6 py-4 border-b border-gray-100">
                        <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-box text-primary"></i>
                        </div>
                        <h2 class="font-bold text-dark">Order Items (<?php echo $item_count; ?>)</h2>
                    </div>

                    <div class="divide-y divide-gray-100">
                        <?php foreach ($order_items as $item):
                            $images = json_decode($item->images);
                            $first_image = !empty($images) ? $images[0] : '';
                            $image_url = !empty($first_image)
                                ? base_url('uploads/products/' . $first_image)
                                : base_url('user_assets/images/product-fallback.png');
                        ?>
                        <!-- Item -->
                        <div class="p-5 md:p-6">
                            <div class="flex gap-4">
                                <a href="<?php echo base_url('product/' . $item->product_id); ?>" class="flex-shrink-0">
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>" class="w-20 h-20 md:w-24 md:h-24 rounded-xl object-cover">
                                </a>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <a href="<?php echo base_url('product/' . $item->product_id); ?>" class="font-semibold text-dark hover:text-primary transition-colors"><?php echo htmlspecialchars($item->product_name); ?></a>
                                            <?php if (!empty($item->variant_name)): ?>
                                            <p class="text-sm text-gray mt-1">Variant: <?php echo htmlspecialchars($item->variant_name); ?></p>
                                            <?php endif; ?>
                                            <p class="text-sm text-gray">Qty: <?php echo intval($item->quantity); ?></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-dark">Rs. <?php echo number_format($item->total_price, 2); ?></p>
                                            <?php if ($item->quantity > 1): ?>
                                            <p class="text-sm text-gray">Rs. <?php echo number_format($item->unit_price, 2); ?> x <?php echo intval($item->quantity); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php if ($order->order_status == 'delivered'): ?>
                                    <div class="flex items-center gap-3 mt-3">
                                        <button class="buy-again-btn text-sm text-primary font-medium hover:underline" data-product-id="<?php echo $item->product_id; ?>" data-variant-id="<?php echo $item->variant_id ?: ''; ?>">
                                            <i class="fa-solid fa-rotate-left mr-1"></i>
                                            Buy Again
                                        </button>
                                        <span class="text-gray-300">|</span>
                                        <button class="text-sm text-primary font-medium hover:underline">
                                            <i class="fa-solid fa-star mr-1"></i>
                                            Write Review
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Tracking History -->
                <?php if (!empty($status_history)): ?>
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5">
                    <div class="flex items-center gap-3 px-5 md:px-6 py-4 border-b border-gray-100">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-route text-blue-600"></i>
                        </div>
                        <h2 class="font-bold text-dark">Tracking History</h2>
                    </div>

                    <div class="p-5 md:p-6">
                        <div class="tracking-timeline space-y-6">
                            <?php
                            $total_history = count($status_history);
                            $index = 0;
                            foreach ($status_history as $history):
                                $index++;
                                $is_last = ($index == $total_history);

                                // Determine status display name and icon
                                $status_display = ucfirst(str_replace('_', ' ', $history->status));
                                switch ($history->status) {
                                    case 'pending':
                                        $status_display = 'Order Placed';
                                        break;
                                    case 'confirmed':
                                        $status_display = 'Order Confirmed';
                                        break;
                                    case 'processing':
                                        $status_display = 'Processing';
                                        break;
                                    case 'shipped':
                                        $status_display = 'Shipped';
                                        break;
                                    case 'out_for_delivery':
                                        $status_display = 'Out for Delivery';
                                        break;
                                    case 'delivered':
                                        $status_display = 'Delivered';
                                        break;
                                    case 'cancelled':
                                        $status_display = 'Cancelled';
                                        break;
                                    case 'refunded':
                                        $status_display = 'Refunded';
                                        break;
                                }
                            ?>
                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 <?php echo in_array($history->status, ['cancelled', 'refunded']) ? 'bg-red-500' : 'bg-green-500'; ?> rounded-full"></div>
                                    <?php if (!$is_last): ?>
                                    <div class="w-0.5 h-full bg-gray-200 mt-2"></div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 <?php echo !$is_last ? 'pb-6' : ''; ?>">
                                    <p class="font-semibold text-dark"><?php echo $status_display; ?></p>
                                    <?php if (!empty($history->notes)): ?>
                                    <p class="text-sm text-gray"><?php echo htmlspecialchars($history->notes); ?></p>
                                    <?php endif; ?>
                                    <p class="text-xs text-gray mt-1"><?php echo date('M d, Y \a\t h:i A', strtotime($history->created_at)); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5 sticky top-24">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="font-bold text-dark">Order Summary</h2>
                    </div>

                    <div class="p-5 space-y-4">
                        <!-- Price Breakdown -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Subtotal (<?php echo $item_count; ?> item<?php echo $item_count > 1 ? 's' : ''; ?>)</span>
                                <span class="font-semibold text-dark">Rs. <?php echo number_format($order->subtotal, 2); ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Shipping</span>
                                <span class="font-semibold <?php echo $order->delivery_charge == 0 ? 'text-green-600' : 'text-dark'; ?>">
                                    <?php echo $order->delivery_charge == 0 ? 'FREE' : 'Rs. ' . number_format($order->delivery_charge, 2); ?>
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Discount<?php echo !empty($order->coupon_code) ? ' (' . $order->coupon_code . ')' : ''; ?></span>
                                <span class="font-semibold <?php echo $order->discount_amount > 0 ? 'text-green-600' : 'text-dark'; ?>">-Rs. <?php echo number_format($order->discount_amount, 2); ?></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Tax (Included)</span>
                                <span class="font-semibold text-dark">Rs. 0.00</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-dark">Total Paid</span>
                                <span class="text-xl font-bold text-primary">Rs. <?php echo number_format($order->total_amount, 2); ?></span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-sm text-gray mb-1">Payment Method</p>
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-credit-card text-primary"></i>
                                <span class="font-semibold text-dark"><?php echo !empty($order->payment_method) ? ucfirst($order->payment_method) : 'Online Payment'; ?></span>
                            </div>
                            <?php if (!empty($order->payment_id)): ?>
                            <p class="text-sm text-gray mt-1">ID: <?php echo substr($order->payment_id, 0, 20); ?>...</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Delivery Address -->
                    <div class="border-t border-gray-100 p-5">
                        <h3 class="font-bold text-dark mb-3">Delivery Address</h3>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-house text-primary text-sm"></i>
                            </div>
                            <div>
                                <p class="font-semibold text-dark"><?php echo htmlspecialchars($order->delivery_fullname); ?></p>
                                <p class="text-sm text-gray leading-relaxed mt-1">
                                    <?php echo htmlspecialchars($order->delivery_address); ?>
                                    <?php if (!empty($order->delivery_landmark)): ?>
                                    <br><?php echo htmlspecialchars($order->delivery_landmark); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($order->delivery_city)): ?>
                                    <br><?php echo htmlspecialchars($order->delivery_city); ?>, <?php echo htmlspecialchars($order->delivery_state); ?> - <?php echo htmlspecialchars($order->delivery_pincode); ?>
                                    <?php endif; ?>
                                </p>
                                <?php if (!empty($order->delivery_phone)): ?>
                                <p class="text-sm text-dark mt-2">
                                    <i class="fa-solid fa-phone text-xs mr-1"></i>
                                    +91 <?php echo htmlspecialchars($order->delivery_phone); ?>
                                </p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="border-t border-gray-100 p-5 space-y-3">
                        <?php if ($order->order_status != 'cancelled'): ?>
                        <button class="reorder-btn w-full btn-primary flex items-center justify-center gap-2" data-order-id="<?php echo $order->id; ?>">
                            <i class="fa-solid fa-rotate-left"></i>
                            <span>Reorder Items</span>
                        </button>
                        <?php endif; ?>
                        <a href="<?php echo base_url('contact'); ?>" class="w-full px-4 py-3 border border-gray-200 rounded-full font-semibold text-dark hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-headset"></i>
                            <span>Contact Support</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Order Details Page Styles -->
<style>
/* Horizontal Timeline */
.order-timeline-horizontal {
    display: flex;
    justify-content: space-between;
    position: relative;
    padding: 0 10px;
}

.order-timeline-horizontal::before {
    content: '';
    position: absolute;
    top: 16px;
    left: 40px;
    right: 40px;
    height: 3px;
    background: #e5e7eb;
    z-index: 0;
}

.order-timeline-horizontal::after {
    content: '';
    position: absolute;
    top: 16px;
    left: 40px;
    height: 3px;
    background: #22c55e;
    z-index: 1;
    transition: width 0.5s ease;
}

.order-timeline-horizontal[data-progress="0"]::after { width: 0%; }
.order-timeline-horizontal[data-progress="33"]::after { width: calc((100% - 80px) * 0.33); }
.order-timeline-horizontal[data-progress="66"]::after { width: calc((100% - 80px) * 0.66); }
.order-timeline-horizontal[data-progress="100"]::after { width: calc(100% - 80px); }

.timeline-step-h {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 2;
    flex: 1;
}

.timeline-step-h .step-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f3f4f6;
    border: 3px solid #e5e7eb;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    font-size: 12px;
    transition: all 0.3s ease;
}

.timeline-step-h.completed .step-circle {
    background: #22c55e;
    border-color: #22c55e;
    color: white;
}

.timeline-step-h .step-label {
    margin-top: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #9ca3af;
    text-align: center;
}

.timeline-step-h.completed .step-label {
    color: #1f2937;
}

.timeline-step-h .step-date {
    font-size: 11px;
    color: #9ca3af;
    margin-top: 2px;
}

.timeline-step-h.completed .step-date {
    color: #6b7280;
}

/* Mobile Timeline */
@media (max-width: 480px) {
    .order-timeline-horizontal {
        flex-direction: column;
        gap: 20px;
        padding: 0;
    }

    .order-timeline-horizontal::before,
    .order-timeline-horizontal::after {
        display: none;
    }

    .timeline-step-h {
        flex-direction: row;
        justify-content: flex-start;
        gap: 12px;
    }

    .timeline-step-h::before {
        content: '';
        position: absolute;
        left: 14px;
        top: 36px;
        width: 3px;
        height: calc(100% + 20px);
        background: #e5e7eb;
    }

    .timeline-step-h:last-child::before {
        display: none;
    }

    .timeline-step-h.completed::before {
        background: #22c55e;
    }

    .timeline-step-h .step-label,
    .timeline-step-h .step-date {
        margin-top: 0;
        text-align: left;
    }

    .timeline-step-h .step-date {
        margin-left: 8px;
    }
}
</style>
