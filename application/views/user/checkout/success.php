<!-- Confetti Animation -->
<div class="confetti-container">
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
    <div class="confetti"></div>
</div>

<!-- Main Content -->
<main class="min-h-screen bg-brand-bg py-8 md:py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-lg mx-auto">

            <!-- Success Icon & Message -->
            <div class="text-center mb-8">
                <div class="success-icon success-pulse w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-5 ring-4 ring-primary/20">
                    <span class="text-5xl">🎉</span>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold text-dark mb-3 fade-in-up">Yay! Your PIKO POP order is confirmed 🎉</h1>
                <p class="text-primary font-semibold text-base mb-2 fade-in-up">Your cute surprises are getting ready!</p>
                <p class="text-gray text-sm fade-in-up">Payment via Razorpay<?php echo !empty($order->payment_id) ? ' • ' . substr($order->payment_id, 0, 20) . '...' : ''; ?></p>
            </div>

            <!-- Order Summary Highlights -->
            <div class="bg-white rounded-3xl shadow-md shadow-primary/10 p-5 mb-6 border border-primary/5 fade-in-up">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-2xl bg-light-gray">
                        <p class="text-xs text-gray uppercase tracking-wide mb-1">Order Number</p>
                        <p class="font-bold text-dark text-lg">#<?php echo $order->order_number; ?></p>
                    </div>
                    <div class="p-4 rounded-2xl bg-light-gray">
                        <p class="text-xs text-gray uppercase tracking-wide mb-1">Amount Paid</p>
                        <p class="font-bold text-primary text-lg">Rs. <?php echo number_format($order->total_amount, 2); ?></p>
                    </div>
                </div>
                <?php if (!empty($order->delivery_fullname)): ?>
                <div class="mt-4 p-4 rounded-2xl bg-light-gray">
                    <p class="text-xs text-gray uppercase tracking-wide mb-2 flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-primary"></i> Delivery Details
                    </p>
                    <p class="font-semibold text-dark"><?php echo htmlspecialchars($order->delivery_fullname); ?></p>
                    <p class="text-sm text-gray mt-1 leading-relaxed">
                        <?php echo htmlspecialchars($order->delivery_address); ?>
                        <?php if (!empty($order->delivery_landmark)): ?>, <?php echo htmlspecialchars($order->delivery_landmark); ?><?php endif; ?>
                        <?php if (!empty($order->delivery_city)): ?>
                        <br><?php echo htmlspecialchars($order->delivery_city); ?>, <?php echo htmlspecialchars($order->delivery_state); ?> - <?php echo htmlspecialchars($order->delivery_pincode); ?>
                        <?php endif; ?>
                    </p>
                    <?php if (!empty($order->delivery_phone)): ?>
                    <p class="text-sm text-dark mt-2">
                        <i class="fa-solid fa-phone text-xs mr-1 text-primary"></i>
                        +91 <?php echo htmlspecialchars($order->delivery_phone); ?>
                    </p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Order Receipt Card -->
            <div class="receipt-card relative fade-in-up">
                <!-- Gradient Top Border -->
                <div class="h-1.5 bg-gradient-to-r from-primary via-secondary to-accent-yellow"></div>

                <!-- Order ID Header -->
                <div class="px-5 py-4 border-b border-primary/10">
                    <h2 class="text-sm font-bold text-dark uppercase tracking-wide">Order Summary</h2>
                </div>

                <!-- Order Items -->
                <div class="px-5 py-4 space-y-4">
                    <?php foreach ($order_items as $item): ?>
                    <?php
                        $image_url = !empty($item->product_image)
                            ? base_url('uploads/products/' . $item->product_image)
                            : base_url('user_assets/images/product-fallback.png');
                    ?>
                    <!-- Item -->
                    <div class="flex items-center gap-4 fade-in-up">
                        <div class="relative">
                            <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>" class="w-14 h-14 rounded-xl object-cover shadow-sm">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-semibold text-dark"><?php echo htmlspecialchars($item->product_name); ?></h3>
                            <p class="text-xs text-gray mt-0.5">
                                Rs. <?php echo number_format($item->unit_price, 2); ?> x <?php echo intval($item->quantity); ?>
                                <?php if (!empty($item->variant_name)): ?>
                                <span class="text-gray-400">• <?php echo htmlspecialchars($item->variant_name); ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <span class="text-sm font-bold text-dark"><?php echo number_format($item->total_price, 2); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Dashed Divider -->
                <div class="receipt-divider mx-5"></div>

                <!-- Price Breakdown -->
                <div class="px-5 py-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray">Subtotal</span>
                        <span class="text-sm font-semibold text-dark"><?php echo number_format($order->subtotal, 2); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray">Delivery Charges</span>
                        <span class="text-sm font-semibold <?php echo $order->delivery_charge == 0 ? 'text-green-600' : 'text-dark'; ?>">
                            <?php echo $order->delivery_charge == 0 ? 'FREE' : number_format($order->delivery_charge, 2); ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray">Discount<?php echo !empty($order->coupon_code) ? ' (' . $order->coupon_code . ')' : ''; ?></span>
                        <span class="text-sm font-semibold <?php echo $order->discount_amount > 0 ? 'text-green-600' : 'text-dark'; ?>">
                            -<?php echo number_format($order->discount_amount, 2); ?>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray">Tax (Included)</span>
                        <span class="text-sm font-semibold text-dark">0.00</span>
                    </div>
                </div>

                <!-- Dashed Divider -->
                <div class="receipt-divider mx-5"></div>

                <!-- Total Paid -->
                <div class="px-5 py-5 bg-gradient-to-r from-primary/5 to-secondary/5">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-bold text-dark">Paid Amount</span>
                        <span class="text-2xl font-bold text-primary">Rs. <?php echo number_format($order->total_amount, 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Download Receipt Button -->
            <div class="mt-6 fade-in-up">
                <a href="<?php echo base_url('account/orders/'); ?>" class="btn-shimmer w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-full font-semibold text-base transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/25">
                    <i class="fa-solid fa-box"></i>
                    <span>My PIKO Orders</span>
                </a>
            </div>

            <!-- Continue Shopping Link -->
            <div class="text-center mt-6 fade-in-up">
                <a href="<?php echo base_url('shop'); ?>" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline transition-all hover:gap-3">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Continue Shopping</span>
                </a>
            </div>

            <!-- Order Timeline -->
            <div class="mt-8 bg-white rounded-2xl shadow-lg shadow-gray-200/50 p-6 fade-in-up">
                <h3 class="font-bold text-dark mb-5 flex items-center gap-2">
                    <i class="fa-solid fa-route text-primary"></i>
                    Order Status
                </h3>
                <div class="order-timeline space-y-4">
                    <!-- Step 1 - Completed -->
                    <div class="timeline-step completed flex items-start gap-4">
                        <div class="timeline-icon w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-green-500">
                            <i class="fa-solid fa-check text-green-600 text-sm"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-semibold text-dark">Order Confirmed</p>
                            <p class="text-xs text-gray mt-0.5">Your order has been placed successfully</p>
                        </div>
                    </div>

                    <!-- Step 2 - Active -->
                    <div class="timeline-step active flex items-start gap-4">
                        <div class="timeline-icon w-8 h-8 bg-primary rounded-full flex items-center justify-center flex-shrink-0 border-2 border-primary">
                            <i class="fa-solid fa-box text-white text-sm"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-semibold text-dark">Processing</p>
                            <p class="text-xs text-gray mt-0.5">Your cute surprises are getting ready!</p>
                        </div>
                    </div>

                    <!-- Step 3 - Pending -->
                    <div class="timeline-step flex items-start gap-4">
                        <div class="timeline-icon w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-gray-200">
                            <i class="fa-solid fa-truck text-gray text-sm"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-semibold text-dark">Shipped</p>
                            <p class="text-xs text-gray mt-0.5">Your order will be shipped soon</p>
                        </div>
                    </div>

                    <!-- Step 4 - Pending -->
                    <div class="timeline-step flex items-start gap-4">
                        <div class="timeline-icon w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0 border-2 border-gray-200">
                            <i class="fa-solid fa-house text-gray text-sm"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-semibold text-dark">Delivered</p>
                            <p class="text-xs text-gray mt-0.5">Expected in 3-5 business days</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Need Help Card -->
            <div class="mt-6 bg-gradient-to-r from-primary/10 to-secondary/10 rounded-2xl p-5 fade-in-up">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-headset text-xl text-primary"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-semibold text-dark">Need Help?</h4>
                        <p class="text-xs text-gray">Contact our support team</p>
                    </div>
                    <a href="<?php echo base_url('contact'); ?>" class="px-4 py-2 bg-white rounded-full text-sm font-medium text-primary hover:shadow-md transition-all">
                        Contact Us
                    </a>
                </div>
            </div>

        </div>
    </div>
</main>

<!-- Success Page Styles -->
<style>
/* Receipt Card */
.receipt-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 10px 40px -10px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Dashed Divider */
.receipt-divider {
    border-top: 2px dashed #e5e7eb;
}

/* Success Pulse Animation */
.success-pulse {
    animation: pulse-success 2s ease-in-out infinite;
}

@keyframes pulse-success {
    0%, 100% {
        transform: scale(1);
        box-shadow: 0 0 0 0 rgba(255, 79, 163, 0.4);
    }
    50% {
        transform: scale(1.05);
        box-shadow: 0 0 0 15px rgba(255, 79, 163, 0);
    }
}

/* Fade In Up Animation */
.fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Button Shimmer Effect */
.btn-shimmer {
    position: relative;
    overflow: hidden;
}

.btn-shimmer::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Order Timeline */
.order-timeline .timeline-step {
    position: relative;
}

.order-timeline .timeline-step:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 32px;
    width: 2px;
    height: calc(100% + 8px);
    background: #e5e7eb;
}

.order-timeline .timeline-step.completed:not(:last-child)::before {
    background: #22c55e;
}

.order-timeline .timeline-step.active:not(:last-child)::before {
    background: linear-gradient(to bottom, #FF4FA3 0%, #e5e7eb 100%);
}

/* Confetti Animation */
.confetti-container {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    overflow: hidden;
    z-index: 100;
}

.confetti {
    position: absolute;
    width: 10px;
    height: 10px;
    top: -10px;
    animation: confetti-fall 3s ease-in-out forwards;
}

.confetti:nth-child(1) { left: 5%; background: #FF4FA3; animation-delay: 0s; }
.confetti:nth-child(2) { left: 10%; background: #FFD83D; animation-delay: 0.2s; border-radius: 50%; }
.confetti:nth-child(3) { left: 15%; background: #6C3CC9; animation-delay: 0.4s; }
.confetti:nth-child(4) { left: 20%; background: #20BDF7; animation-delay: 0.1s; border-radius: 50%; }
.confetti:nth-child(5) { left: 25%; background: #FF7FBC; animation-delay: 0.3s; }
.confetti:nth-child(6) { left: 35%; background: #FFD83D; animation-delay: 0.5s; border-radius: 50%; }
.confetti:nth-child(7) { left: 40%; background: #6C3CC9; animation-delay: 0.15s; }
.confetti:nth-child(8) { left: 45%; background: #FF4FA3; animation-delay: 0.35s; border-radius: 50%; }
.confetti:nth-child(9) { left: 50%; background: #20BDF7; animation-delay: 0.25s; }
.confetti:nth-child(10) { left: 55%; background: #FFD83D; animation-delay: 0.45s; border-radius: 50%; }
.confetti:nth-child(11) { left: 60%; background: #FF4FA3; animation-delay: 0.1s; }
.confetti:nth-child(12) { left: 65%; background: #6C3CC9; animation-delay: 0.3s; border-radius: 50%; }
.confetti:nth-child(13) { left: 70%; background: #20BDF7; animation-delay: 0.2s; }
.confetti:nth-child(14) { left: 75%; background: #FFD83D; animation-delay: 0.4s; border-radius: 50%; }
.confetti:nth-child(15) { left: 80%; background: #FF4FA3; animation-delay: 0.5s; }
.confetti:nth-child(16) { left: 85%; background: #6C3CC9; animation-delay: 0.25s; border-radius: 50%; }
.confetti:nth-child(17) { left: 90%; background: #20BDF7; animation-delay: 0.35s; }
.confetti:nth-child(18) { left: 30%; background: #FFD83D; animation-delay: 0.15s; border-radius: 50%; }
.confetti:nth-child(19) { left: 95%; background: #FF4FA3; animation-delay: 0.45s; }
.confetti:nth-child(20) { left: 2%; background: #6C3CC9; animation-delay: 0.55s; border-radius: 50%; }

@keyframes confetti-fall {
    0% {
        top: -10px;
        transform: rotate(0deg) scale(1);
        opacity: 1;
    }
    100% {
        top: 100vh;
        transform: rotate(720deg) scale(0);
        opacity: 0;
    }
}
</style>
