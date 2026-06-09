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
                <span class="text-dark font-medium">My PIKO Orders</span>
            </nav>
        </div>
    </div>

    <!-- Dashboard Section -->
    <div class="container mx-auto px-4 py-6 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8">

            <!-- Sidebar -->
            <?php $this->load->view('user/account/sidebar'); ?>

            <!-- Main Content -->
            <div class="lg:col-span-3 space-y-6">
                <!-- Page Title & Filters -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold text-dark">My PIKO Orders</h1>
                        <p class="text-gray text-sm mt-1">Track your cute deliveries ✨</p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex items-center gap-2 overflow-x-auto hide-scrollbar">
                        <button class="order-filter-btn active px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all" data-filter="all">
                            All Orders
                        </button>
                        <button class="order-filter-btn px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all" data-filter="processing">
                            Processing
                        </button>
                        <button class="order-filter-btn px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all" data-filter="shipped">
                            Shipped
                        </button>
                        <button class="order-filter-btn px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all" data-filter="delivered">
                            Delivered
                        </button>
                    </div>
                </div>

                <?php if (!empty($orders)): ?>
                <!-- Orders List -->
                <div class="space-y-4" id="ordersList">
                    <?php foreach ($orders as $order): ?>
                    <?php
                        // Determine status badge styling
                        $status_classes = '';
                        $status_icon = '';
                        $status_text = ucfirst($order->order_status);

                        switch ($order->order_status) {
                            case 'pending':
                            case 'processing':
                                $status_classes = 'bg-amber-100 text-amber-700';
                                $status_icon = 'fa-box';
                                break;
                            case 'shipped':
                                $status_classes = 'bg-blue-100 text-blue-700';
                                $status_icon = 'fa-truck-fast';
                                break;
                            case 'delivered':
                                $status_classes = 'bg-green-100 text-green-700';
                                $status_icon = 'fa-circle-check';
                                break;
                            case 'cancelled':
                                $status_classes = 'bg-red-100 text-red-600';
                                $status_icon = 'fa-xmark';
                                break;
                            default:
                                $status_classes = 'bg-gray-100 text-gray-700';
                                $status_icon = 'fa-clock';
                        }

                        // Prepare product names
                        $product_names = [];
                        foreach ($order->items as $item) {
                            $product_names[] = $item->product_name;
                        }
                        $product_names_str = implode(', ', array_slice($product_names, 0, 3));
                        if (count($product_names) > 3) {
                            $product_names_str .= ' and ' . (count($product_names) - 3) . ' more';
                        }
                    ?>
                    <!-- Order Card -->
                    <div class="order-card bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5" data-status="<?php echo $order->order_status; ?>">
                        <!-- Order Header -->
                        <div class="flex flex-wrap items-center justify-between gap-4 px-5 py-4 bg-light-gray border-b border-primary/10">
                            <div class="flex flex-wrap items-center gap-4 md:gap-6">
                                <div>
                                    <p class="text-xs text-gray">Order ID</p>
                                    <p class="font-bold text-dark">#<?php echo $order->order_number; ?></p>
                                </div>
                                <div class="hidden sm:block w-px h-8 bg-gray-200"></div>
                                <div>
                                    <p class="text-xs text-gray">Order Date</p>
                                    <p class="font-semibold text-dark"><?php echo date('M d, Y', strtotime($order->created_at)); ?></p>
                                </div>
                                <div class="hidden sm:block w-px h-8 bg-gray-200"></div>
                                <div>
                                    <p class="text-xs text-gray">Total Amount</p>
                                    <p class="font-bold <?php echo $order->order_status == 'cancelled' ? 'text-gray line-through' : 'text-primary'; ?>">Rs. <?php echo number_format($order->total_amount, 2); ?></p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 <?php echo $status_classes; ?> text-sm font-semibold rounded-full">
                                <i class="fa-solid <?php echo $status_icon; ?> text-xs"></i>
                                <?php echo $status_text; ?>
                            </span>
                        </div>

                        <!-- Order Items -->
                        <div class="p-5">
                            <div class="flex flex-wrap gap-4 mb-4">
                                <!-- Item Images -->
                                <div class="flex -space-x-3">
                                    <?php
                                    $display_items = array_slice($order->items, 0, 3);
                                    $remaining_items = count($order->items) - 3;
                                    foreach ($display_items as $item):
                                        $images = json_decode($item->images);
                                        $first_image = !empty($images) ? $images[0] : '';
                                        $image_url = !empty($first_image)
                                            ? base_url('uploads/products/' . $first_image)
                                            : base_url('user_assets/images/product-fallback.png');
                                        $grayscale = $order->order_status == 'cancelled' ? 'grayscale opacity-60' : '';
                                    ?>
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>" class="w-14 h-14 rounded-lg object-cover border-2 border-white shadow-sm <?php echo $grayscale; ?>">
                                    <?php endforeach; ?>
                                    <?php if ($remaining_items > 0): ?>
                                    <div class="w-14 h-14 rounded-lg bg-gray-100 border-2 border-white shadow-sm flex items-center justify-center">
                                        <span class="text-sm font-bold text-gray">+<?php echo $remaining_items; ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm <?php echo $order->order_status == 'cancelled' ? 'text-gray' : 'text-dark'; ?> font-medium line-clamp-2"><?php echo htmlspecialchars($product_names_str); ?></p>
                                    <p class="text-xs text-gray mt-1"><?php echo $order->item_count; ?> item<?php echo $order->item_count > 1 ? 's' : ''; ?></p>
                                </div>
                            </div>

                            <!-- Status Info -->
                            <div class="flex items-center gap-2 text-sm mb-4">
                                <?php if ($order->order_status == 'delivered'): ?>
                                <i class="fa-solid fa-truck text-green-600"></i>
                                <span class="text-green-600">Delivered on <?php echo !empty($order->delivered_at) ? date('M d, Y', strtotime($order->delivered_at)) : date('M d, Y', strtotime($order->updated_at)); ?></span>
                                <?php elseif ($order->order_status == 'shipped'): ?>
                                <i class="fa-solid fa-truck-fast text-blue-600"></i>
                                <span class="text-blue-600">Out for delivery</span>
                                <?php elseif ($order->order_status == 'processing' || $order->order_status == 'pending'): ?>
                                <i class="fa-solid fa-clock text-amber-600"></i>
                                <span class="text-amber-600">Expected delivery in 4-6 business days</span>
                                <?php elseif ($order->order_status == 'cancelled'): ?>
                                <i class="fa-solid fa-circle-info text-red-500"></i>
                                <span class="text-red-500">Order cancelled<?php echo !empty($order->refund_status) ? ' - ' . ucfirst($order->refund_status) : ''; ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="flex flex-wrap items-center gap-3">
                                <a href="<?php echo base_url('account/order_detail/' . $order->id); ?>" class="<?php echo $order->order_status == 'cancelled' ? 'px-4 py-2.5 border border-gray-200 rounded-full text-sm font-medium text-dark hover:bg-gray-50 transition-all' : 'btn-primary text-sm py-2.5'; ?>">
                                    <i class="fa-solid fa-eye mr-1.5"></i>
                                    View Details
                                </a>
                                <?php if ($order->order_status == 'delivered' || $order->order_status == 'cancelled'): ?>
                                <button class="reorder-btn px-4 py-2.5 border border-gray-200 rounded-full text-sm font-medium text-dark hover:bg-gray-50 transition-all" data-order-id="<?php echo $order->id; ?>">
                                    <i class="fa-solid fa-rotate-left mr-1.5"></i>
                                    Reorder
                                </button>
                                <?php endif; ?>
                                <?php if ($order->order_status == 'shipped' || $order->order_status == 'processing'): ?>
                                <a href="<?php echo base_url('account/order_detail/' . $order->id); ?>" class="px-4 py-2.5 border border-gray-200 rounded-full text-sm font-medium text-dark hover:bg-gray-50 transition-all">
                                    <i class="fa-solid fa-route mr-1.5"></i>
                                    Track Order
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- No Results Message (hidden by default) -->
                <div id="noOrdersMessage" class="hidden bg-white rounded-2xl shadow-sm p-8 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-solid fa-box-open text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-dark mb-2">No orders found</h3>
                    <p class="text-gray text-sm">No orders match the selected filter.</p>
                </div>

                <?php else: ?>
                <!-- Empty State -->
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 p-8 text-center border border-primary/5">
                    <div class="w-24 h-24 bg-light-gray rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-4xl">🎁</span>
                    </div>
                    <h2 class="text-xl font-bold text-dark mb-2">No PIKO orders yet</h2>
                    <p class="text-gray mb-6">Your first cute surprise is just a click away!</p>
                    <a href="<?php echo base_url('shop'); ?>" class="btn-primary inline-flex items-center gap-2">
                        <i class="fa-solid fa-sparkles"></i>
                        <span>Explore Collections</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

<!-- Orders Page Styles -->
<style>
.order-filter-btn {
    background: white;
    color: #6b7280;
    border: 1px solid #e5e7eb;
}
.order-filter-btn:hover {
    border-color: #FF4FA3;
    color: #FF4FA3;
}
.order-filter-btn.active {
    background: #FF4FA3;
    color: white;
    border-color: #FF4FA3;
}
.hide-scrollbar::-webkit-scrollbar {
    display: none;
}
.hide-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

