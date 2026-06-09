<?php
// Calculate cart totals
$cart_subtotal = 0;
$cart_items_count = 0;
if (!empty($cart_items)) {
    foreach ($cart_items as $item) {
        $cart_subtotal += $item->quantity * $item->unit_price;
        $cart_items_count += $item->quantity;
    }
}

// Get shipping info using helper function
$shipping_info = get_shipping_info($cart_subtotal);
$shipping_charge = $shipping_info['charge'];
$amount_for_free_shipping = $shipping_info['amount_to_free'];

// Get applied coupon
$applied_coupon = $this->session->userdata('applied_coupon');
$coupon_discount = !empty($applied_coupon) ? $applied_coupon['discount_amount'] : 0;

// Calculate total
$cart_total = $cart_subtotal - $coupon_discount + $shipping_charge;
?>
<!-- Main Content -->
<div class="site-page">
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-primary/10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium">My PIKO Bag</span>
            </nav>
        </div>
    </div>

    <!-- Cart Section -->
    <div class="container mx-auto px-4 py-6 md:py-10">
        <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2">My PIKO Bag</h1>
        <p class="text-gray text-sm mb-6 md:mb-8">Your cute picks, all in one place ✨</p>

        <?php if (!empty($cart_items)): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden border border-primary/5">
                        <!-- Cart Header (Desktop) -->
                        <div class="hidden md:grid grid-cols-12 gap-4 px-6 py-4 bg-light-gray border-b border-primary/10 text-sm font-semibold text-gray">
                            <div class="col-span-6">Product</div>
                            <div class="col-span-2 text-center">Price</div>
                            <div class="col-span-2 text-center">Quantity</div>
                            <div class="col-span-2 text-right">Total</div>
                        </div>

                        <!-- Cart Items List -->
                        <div id="cartItemsList" class="divide-y divide-gray-100">
                            <?php foreach ($cart_items as $item): ?>
                                <?php
                                // Get product image
                                $images = json_decode($item->images, true) ?: array();
                                $image_url = !empty($images[0]) ? base_url('uploads/products/' . $images[0]) : base_url('user_assets/images/product-fallback.png');

                                // Get variant name
                                $variant_name = !empty($item->variant_name) ? $item->variant_name : '';

                                // Calculate item total
                                $item_total = $item->quantity * $item->unit_price;

                                // Create product object for URL
                                $product_obj = new stdClass();
                                $product_obj->id = $item->product_id;
                                $product_obj->product_name = $item->product_name;
                                ?>
                                <!-- Cart Item -->
                                <div class="cart-item p-4 md:p-6 bg-white hover:bg-light-gray/50 transition-colors" data-cart-id="<?php echo $item->id; ?>" data-product-id="<?php echo $item->product_id; ?>" data-variant-id="<?php echo $item->variant_id ?: 0; ?>">
                                    <div class="md:grid md:grid-cols-12 md:gap-4 md:items-center">
                                        <!-- Product Info -->
                                        <div class="col-span-6 flex gap-4 mb-4 md:mb-0">
                                            <a href="<?php echo product_url($product_obj); ?>" class="flex-shrink-0">
                                                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>" class="w-24 h-24 md:w-28 md:h-28 rounded-2xl object-cover shadow-sm ring-2 ring-primary/10">
                                            </a>
                                            <div class="flex-1 min-w-0">
                                                <a href="<?php echo product_url($product_obj); ?>" class="font-medium text-dark hover:text-primary transition-colors line-clamp-2"><?php echo htmlspecialchars($item->product_name); ?></a>
                                                <?php if (!empty($variant_name)): ?>
                                                    <p class="text-sm text-gray mt-1">Size: <?php echo htmlspecialchars($variant_name); ?></p>
                                                <?php endif; ?>
                                                <button class="remove-item-btn text-red-500 text-sm font-medium hover:underline mt-2 md:hidden">
                                                    <i class="fa-solid fa-trash-can mr-1"></i> Remove
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Price -->
                                        <div class="col-span-2 text-center hidden md:block">
                                            <span class="font-semibold text-dark">Rs. <?php echo number_format($item->unit_price, 2); ?></span>
                                        </div>

                                        <!-- Quantity -->
                                        <div class="col-span-2 flex items-center justify-between md:justify-center mb-4 md:mb-0">
                                            <span class="text-sm text-gray md:hidden">Qty:</span>
                                            <div class="inline-flex items-center bg-light-gray border border-primary/15 rounded-full shadow-sm">
                                                <button class="qty-minus-btn w-9 h-9 flex items-center justify-center text-primary hover:bg-primary/10 rounded-full transition-colors">
                                                    <i class="fa-solid fa-minus text-xs"></i>
                                                </button>
                                                <input type="number" value="<?php echo intval($item->quantity); ?>" min="1" class="qty-input w-10 h-9 text-center font-bold text-dark border-0 bg-transparent focus:outline-none" readonly>
                                                <button class="qty-plus-btn w-9 h-9 flex items-center justify-center text-primary hover:bg-primary/10 rounded-full transition-colors">
                                                    <i class="fa-solid fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Total & Remove -->
                                        <div class="col-span-2 flex items-center justify-between md:justify-end gap-4">
                                            <span class="item-total font-bold text-dark md:hidden">Rs. <?php echo number_format($item_total, 2); ?></span>
                                            <span class="item-total font-bold text-dark hidden md:block">Rs. <?php echo number_format($item_total, 2); ?></span>
                                            <button class="remove-item-btn text-gray-400 hover:text-red-500 transition-colors hidden md:block" title="Remove">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Cart Footer -->
                        <div class="px-4 md:px-6 py-4 bg-light-gray border-t border-primary/10">
                            <a href="<?php echo base_url('shop'); ?>" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Explore Collections</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl shadow-md shadow-primary/5 p-5 md:p-6 sticky top-24 border border-primary/5">
                        <h2 class="text-lg font-bold text-dark mb-5">Bag Summary</h2>

                        <!-- Coupon Code -->
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-dark mb-2">Promo Code</label>
                            <div id="couponInputSection" class="<?php echo !empty($applied_coupon) ? 'hidden' : ''; ?>">
                                <div class="flex gap-2">
                                    <input
                                        type="text"
                                        id="couponCode"
                                        placeholder="Enter code"
                                        class="flex-1 px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-primary transition-colors text-sm">
                                    <button id="applyCouponBtn" class="px-4 py-2.5 bg-primary hover:bg-primary-dark text-white rounded-full font-semibold text-sm transition-colors">
                                        Apply
                                    </button>
                                </div>
                                <p id="couponError" class="text-red-500 text-sm mt-2 hidden"></p>
                            </div>
                            <?php if (!empty($applied_coupon)): ?>
                                <div id="appliedCouponSection" class="flex items-center justify-between bg-green-50 rounded-lg p-3">
                                    <div>
                                        <p class="text-green-700 text-sm font-medium"><?php echo htmlspecialchars($applied_coupon['coupon_code']); ?></p>
                                        <p class="text-green-600 text-xs">-Rs. <?php echo number_format($coupon_discount, 2); ?> discount</p>
                                    </div>
                                    <button id="removeCouponBtn" class="text-green-700 hover:text-green-900">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            <?php else: ?>
                                <div id="appliedCouponSection" class="hidden flex items-center justify-between bg-green-50 rounded-lg p-3">
                                    <div>
                                        <p id="appliedCouponCode" class="text-green-700 text-sm font-medium"></p>
                                        <p id="appliedCouponDiscount" class="text-green-600 text-xs"></p>
                                    </div>
                                    <button id="removeCouponBtn" class="text-green-700 hover:text-green-900">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="border-t border-gray-200 pt-5 space-y-3">
                            <!-- Subtotal -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Subtotal (<span id="itemsCount"><?php echo $cart_items_count; ?></span> items)</span>
                                <span id="cartSubtotal" class="font-semibold text-dark">Rs. <?php echo number_format($cart_subtotal, 2); ?></span>
                            </div>

                            <!-- Discount -->
                            <div id="discountRow" class="flex items-center justify-between text-green-600 <?php echo $coupon_discount <= 0 ? 'hidden' : ''; ?>">
                                <span>Discount</span>
                                <span id="discountAmount" class="font-semibold">- Rs. <?php echo number_format($coupon_discount, 2); ?></span>
                            </div>

                            <!-- Shipping -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Shipping</span>
                                <span id="shippingAmount" class="font-semibold <?php echo $shipping_charge == 0 ? 'text-green-600' : 'text-dark'; ?>">
                                    <?php echo $shipping_charge == 0 ? 'FREE' : 'Rs. ' . number_format($shipping_charge, 2); ?>
                                </span>
                            </div>

                            <!-- Free Shipping Info -->
                            <?php if ($shipping_charge == 0): ?>
                                <div id="freeShippingMsg" class="bg-green-50 rounded-lg p-3 flex items-start gap-2">
                                    <i class="fa-solid fa-truck text-green-600 mt-0.5"></i>
                                    <p class="text-sm text-green-700">You've unlocked <strong>FREE shipping!</strong></p>
                                </div>
                            <?php else: ?>
                                <div id="shippingProgressMsg" class="bg-amber-50 rounded-lg p-3 flex items-start gap-2">
                                    <i class="fa-solid fa-truck text-amber-600 mt-0.5"></i>
                                    <p class="text-sm text-amber-700">Add <strong>Rs. <?php echo number_format($amount_for_free_shipping, 2); ?></strong> more for FREE shipping!</p>
                                </div>
                                <div id="freeShippingMsg" class="bg-green-50 rounded-lg p-3 flex items-start gap-2 hidden">
                                    <i class="fa-solid fa-truck text-green-600 mt-0.5"></i>
                                    <p class="text-sm text-green-700">You've unlocked <strong>FREE shipping!</strong></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Total -->
                        <div class="border-t border-gray-200 mt-5 pt-5">
                            <div class="flex items-center justify-between mb-5">
                                <span class="text-lg font-bold text-dark">Total</span>
                                <span id="cartTotal" class="text-xl font-bold text-primary">Rs. <?php echo number_format($cart_total, 2); ?></span>
                            </div>

                            <p class="text-xs text-gray mb-4">Inclusive of all taxes</p>

                            <a href="<?php echo base_url('checkout'); ?>" class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-full font-semibold text-base transition-all flex items-center justify-center gap-2">
                                <span>Proceed to Checkout</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>

                        <!-- Trust Badges -->
                        <div class="mt-6 pt-5 border-t border-gray-200">
                            <div class="flex items-center justify-center gap-4 text-gray">
                                <div class="flex flex-col items-center text-center">
                                    <i class="fa-solid fa-lock text-lg mb-1"></i>
                                    <span class="text-xs">Secure<br>Checkout</span>
                                </div>
                                <div class="flex flex-col items-center text-center">
                                    <i class="fa-solid fa-shield-check text-lg mb-1"></i>
                                    <span class="text-xs">100%<br>Genuine</span>
                                </div>
                                <div class="flex flex-col items-center text-center">
                                    <i class="fa-solid fa-rotate-left text-lg mb-1"></i>
                                    <span class="text-xs">Easy<br>Returns</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty Cart Section -->
            <div class="container mx-auto px-4 py-12 md:py-20 empty-cart-section">
                <div class="max-w-md mx-auto text-center">
                    <!-- Empty Cart Illustration -->
                    <div class="empty-cart-icon">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <div class="empty-cart-badge">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                    </div>

                    <!-- Empty Cart Message -->
                    <h1 class="text-2xl md:text-3xl font-bold text-dark mb-3">No cute finds added yet ✨</h1>
                    <p class="text-gray text-base mb-10 leading-relaxed">
                        Your PIKO bag is waiting! Browse stickers, stationery, toys &amp; more.
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                        <a href="<?php echo base_url('shop'); ?>" class="w-full sm:w-auto btn-primary inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-sparkles"></i>
                            <span>Explore Collections</span>
                        </a>
                        <a href="<?php echo base_url(); ?>" class="w-full sm:w-auto px-6 py-3 border border-gray-200 rounded-full font-medium text-dark hover:bg-gray-50 transition-all inline-flex items-center justify-center gap-2">
                            <i class="fa-solid fa-home"></i>
                            <span>Back to Home</span>
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<style>
    /* Empty Cart Illustration */
    .empty-cart-icon {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 0 auto 2rem;
    }

    .empty-cart-icon::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 79, 163, 0.12) 0%, rgba(108, 60, 201, 0.08) 100%);
        border-radius: 50%;
    }

    .empty-cart-icon::after {
        content: '';
        position: absolute;
        inset: 15px;
        border: 2px dashed rgba(255, 79, 163, 0.2);
        border-radius: 50%;
        animation: rotate 20s linear infinite;
    }

    @keyframes rotate {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    .empty-cart-icon i {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 4rem;
        color: rgba(255, 79, 163, 0.35);
    }

    .empty-cart-badge {
        position: absolute;
        top: 30px;
        right: 30px;
        width: 36px;
        height: 36px;
        background: #f3f4f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .empty-cart-badge i {
        position: static;
        transform: none;
        font-size: 0.875rem;
        color: #9ca3af;
    }

    /* CTA Buttons */
    .empty-cart-section .btn-primary {
        box-shadow: 0 4px 14px rgba(255, 79, 163, 0.25);
        transition: all 0.3s ease;
    }

    .empty-cart-section .btn-primary:hover {
        box-shadow: 0 6px 20px rgba(255, 79, 163, 0.35);
        transform: translateY(-2px);
    }

    .empty-cart-section .btn-secondary {
        background: white;
        border: 1.5px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .empty-cart-section .btn-secondary:hover {
        border-color: #FF4FA3;
        color: #FF4FA3;
    }

    /* Feature Cards */
    .feature-card {
        background: white;
        border-radius: 1rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .feature-card:hover {
        border-color: rgba(255, 79, 163, 0.15);
        transform: translateY(-4px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .feature-card .icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        transition: transform 0.3s ease;
    }

    .feature-card:hover .icon-wrap {
        transform: scale(1.1);
    }

    /* Product Cards in Empty Cart */
    .empty-cart-products .product-card {
        background: white;
        border-radius: 1rem;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .empty-cart-products .product-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }

    /* Section Divider */
    .section-divider {
        width: 60px;
        height: 3px;
        background: linear-gradient(90deg, #FF4FA3, #6C3CC9);
        border-radius: 2px;
        margin: 0 auto 1.5rem;
    }
</style>