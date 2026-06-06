<!-- Main Content -->
<main class="min-h-screen bg-brand-bg">
    <!-- Breadcrumb -->
    <div class="bg-white/80 backdrop-blur-sm border-b border-primary/10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('cart'); ?>" class="text-gray hover:text-primary transition-colors">My PIKO Bag</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium">Checkout</span>
            </nav>
        </div>
    </div>

    <!-- Checkout Section -->
    <div class="container mx-auto px-4 py-6 md:py-10">
        <h1 class="text-2xl md:text-3xl font-bold text-dark mb-2">Almost There 🎁</h1>
        <p class="text-gray text-sm mb-6 md:mb-8">Just a few steps before your cute surprises ship!</p>

        <?php if ($this->session->flashdata('error')): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
            <?php echo $this->session->flashdata('error'); ?>
        </div>
        <?php endif; ?>

        <form id="checkoutForm">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
                <!-- Delivery Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Delivery Details Card -->
                    <div class="bg-white rounded-3xl shadow-md shadow-primary/5 p-5 md:p-6 border border-primary/5">
                        <div class="flex items-center justify-between mb-6 pb-4 border-b border-primary/10">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center shadow-sm">
                                    <i class="fa-solid fa-location-dot text-white"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-dark">Delivery Details</h2>
                                    <p class="text-xs text-gray">Where should we send your PIKO POP goodies?</p>
                                </div>
                            </div>
                            <button type="button" id="addNewAddressBtn" class="text-primary font-medium text-sm hover:underline flex items-center gap-1">
                                <i class="fa-solid fa-plus text-xs"></i>
                                <span>Add New</span>
                            </button>
                        </div>

                        <!-- Hidden input for selected address ID -->
                        <input type="hidden" id="selectedAddressId" name="address_id" value="">

                        <?php if (empty($addresses)): ?>
                        <!-- No Address State -->
                        <div id="noAddressState" class="text-center py-8">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-location-dot text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="font-bold text-dark mb-2">No Saved Addresses</h3>
                            <p class="text-sm text-gray mb-4">Add a delivery address to continue with your order</p>
                            <button type="button" id="addFirstAddressBtn" class="btn-primary px-6 py-3">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Add New Address
                            </button>
                        </div>
                        <?php else: ?>
                        <!-- Saved Addresses List -->
                        <div id="savedAddressesList" class="space-y-3">
                            <?php foreach ($addresses as $index => $addr): ?>
                            <!-- Address -->
                            <label class="address-option block cursor-pointer">
                                <input type="radio" name="deliveryAddress" value="<?php echo $addr->id; ?>" class="sr-only address-radio" <?php echo ($addr->is_default || $index === 0) ? 'checked' : ''; ?>>
                                <div class="address-card relative border-2 border-gray-200 rounded-xl p-4 transition-all">
                                    <?php if ($addr->is_default): ?>
                                    <span class="absolute top-3 right-3 px-2 py-0.5 bg-primary text-white text-xs font-semibold rounded-full">Default</span>
                                    <?php endif; ?>
                                    <div class="flex items-start gap-3">
                                        <div class="radio-circle w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center mt-0.5 flex-shrink-0">
                                            <div class="radio-dot w-2.5 h-2.5 bg-primary rounded-full hidden"></div>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-1">
                                                <?php if ($addr->address_type == 'home'): ?>
                                                <i class="fa-solid fa-house text-primary text-sm"></i>
                                                <?php elseif ($addr->address_type == 'work'): ?>
                                                <i class="fa-solid fa-briefcase text-gray text-sm"></i>
                                                <?php else: ?>
                                                <i class="fa-solid fa-location-dot text-gray text-sm"></i>
                                                <?php endif; ?>
                                                <h4 class="font-semibold text-dark"><?php echo ucfirst($addr->address_type); ?></h4>
                                            </div>
                                            <p class="text-sm text-dark font-medium"><?php echo htmlspecialchars($addr->fullname); ?></p>
                                            <p class="text-sm text-gray leading-relaxed">
                                                <?php echo htmlspecialchars($addr->address_line1); ?>
                                                <?php if (!empty($addr->address_line2)): ?>, <?php echo htmlspecialchars($addr->address_line2); ?><?php endif; ?>,
                                                <?php echo htmlspecialchars($addr->city); ?>,
                                                <?php echo htmlspecialchars($addr->state); ?> - <?php echo htmlspecialchars($addr->pincode); ?>
                                            </p>
                                            <p class="text-sm text-gray mt-1">
                                                <i class="fa-solid fa-phone text-xs mr-1"></i>
                                                +91 <?php echo htmlspecialchars($addr->phone); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <div id="noAddressState" class="hidden text-center py-8">
                            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-location-dot text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="font-bold text-dark mb-2">No Saved Addresses</h3>
                            <p class="text-sm text-gray mb-4">Add a delivery address to continue with your order</p>
                            <button type="button" id="addFirstAddressBtn" class="btn-primary px-6 py-3">
                                <i class="fa-solid fa-plus mr-2"></i>
                                Add New Address
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Your Cute Picks -->
                    <div class="bg-white rounded-3xl shadow-md shadow-primary/5 p-5 md:p-6 border border-primary/5">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-primary/10">
                            <div class="w-10 h-10 bg-secondary rounded-full flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-gift text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-bold text-dark">Your Cute Picks</h2>
                                <p class="text-xs text-gray"><?php echo intval($item_count); ?> item<?php echo intval($item_count) != 1 ? 's' : ''; ?> in your order</p>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <?php foreach ($cart_items as $item): ?>
                            <?php
                                $images = json_decode($item->images, true) ?: array();
                                $image_url = !empty($images[0]) ? base_url('uploads/products/' . $images[0]) : base_url('user_assets/images/product-fallback.png');
                            ?>
                            <div class="flex items-center gap-3 p-3 bg-light-gray rounded-2xl">
                                <div class="relative flex-shrink-0">
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>" class="w-16 h-16 rounded-xl object-cover ring-2 ring-primary/10 shadow-sm">
                                    <span class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center"><?php echo intval($item->quantity); ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-dark line-clamp-2"><?php echo htmlspecialchars($item->product_name); ?></h4>
                                    <?php if (!empty($item->variant_name)): ?>
                                    <p class="text-xs text-gray"><?php echo htmlspecialchars($item->variant_name); ?></p>
                                    <?php endif; ?>
                                </div>
                                <span class="text-sm font-bold text-primary">Rs. <?php echo number_format($item->quantity * $item->unit_price, 2); ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="text-center mt-4 pt-4 border-t border-primary/10">
                            <a href="<?php echo base_url('cart'); ?>" class="inline-flex items-center gap-1.5 text-primary text-sm font-semibold hover:underline">
                                <i class="fa-solid fa-pen text-xs"></i>
                                <span>Edit My PIKO Bag</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Payment -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-3xl shadow-md shadow-primary/5 p-5 md:p-6 sticky top-24 border border-primary/5">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-primary/10">
                            <div class="w-10 h-10 bg-accent-yellow rounded-full flex items-center justify-center shadow-sm">
                                <i class="fa-solid fa-credit-card text-dark"></i>
                            </div>
                            <h2 class="text-lg font-bold text-dark">Payment</h2>
                        </div>

                        <div class="border-t border-gray-200 pt-5 space-y-3">
                            <!-- Subtotal -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Subtotal (<?php echo intval($item_count); ?> items)</span>
                                <span class="font-semibold text-dark">Rs. <?php echo number_format($subtotal, 2); ?></span>
                            </div>

                            <!-- Discount -->
                            <?php if ($coupon_discount > 0): ?>
                            <div class="flex items-center justify-between text-green-600">
                                <span>Discount</span>
                                <span class="font-semibold">- Rs. <?php echo number_format($coupon_discount, 2); ?></span>
                            </div>
                            <?php endif; ?>

                            <!-- Shipping -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Shipping</span>
                                <span class="font-semibold <?php echo $shipping == 0 ? 'text-green-600' : 'text-dark'; ?>">
                                    <?php echo $shipping == 0 ? 'FREE' : 'Rs. ' . number_format($shipping, 2); ?>
                                </span>
                            </div>

                            <!-- Tax -->
                            <div class="flex items-center justify-between">
                                <span class="text-gray">Tax (Included)</span>
                                <span class="font-semibold text-dark">Rs. 0.00</span>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="border-t border-gray-200 mt-5 pt-5">
                            <div class="flex items-center justify-between mb-5">
                                <span class="text-lg font-bold text-dark">Total</span>
                                <span class="text-xl font-bold text-primary">Rs. <?php echo number_format($total, 2); ?></span>
                            </div>

                            <button type="submit" id="placeOrderBtn" class="w-full bg-primary hover:bg-primary-dark text-white py-4 rounded-full font-semibold text-base transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/25">
                                <i class="fa-solid fa-lock text-sm"></i>
                                <span>Pay Securely with Razorpay</span>
                            </button>

                            <p class="text-xs text-gray text-center mt-4">
                                By placing this order, you agree to our
                                <a href="<?php echo base_url('terms'); ?>" class="text-primary hover:underline">Terms</a> &
                                <a href="<?php echo base_url('privacy'); ?>" class="text-primary hover:underline">Privacy Policy</a>
                            </p>
                        </div>

                        <!-- Trust Badges -->
                        <div class="mt-6 pt-5 border-t border-primary/10">
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="flex flex-col items-center gap-1 p-2 rounded-xl bg-light-gray">
                                    <span class="text-lg">🔒</span>
                                    <span class="text-xs font-semibold text-dark leading-tight">Secure Payment</span>
                                </div>
                                <div class="flex flex-col items-center gap-1 p-2 rounded-xl bg-light-gray">
                                    <span class="text-lg">🚚</span>
                                    <span class="text-xs font-semibold text-dark leading-tight">Fast Delivery</span>
                                </div>
                                <div class="flex flex-col items-center gap-1 p-2 rounded-xl bg-light-gray">
                                    <span class="text-lg">⭐</span>
                                    <span class="text-xs font-semibold text-dark leading-tight">Quality Checked</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<!-- Add Address Modal -->
<div id="checkoutAddressModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="checkout-address-modal-backdrop absolute inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="checkout-address-modal-content bg-white rounded-2xl w-full max-w-lg max-h-[90vh] overflow-hidden shadow-2xl transform scale-95 opacity-0 transition-all duration-300">
            <!-- Modal Header -->
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-dark">Add New Address</h3>
                <button type="button" id="closeCheckoutAddressModal" class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-gray hover:text-dark transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-5 overflow-y-auto max-h-[calc(90vh-140px)]">
                <form id="checkoutAddressForm" class="space-y-4">
                    <!-- Address Type -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Address Type *</label>
                        <div class="flex gap-3">
                            <label class="flex-1">
                                <input type="radio" name="address_type" value="home" class="sr-only peer" checked>
                                <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                    <i class="fa-solid fa-house text-gray peer-checked:text-primary"></i>
                                    <span class="font-medium">Home</span>
                                </div>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="address_type" value="work" class="sr-only peer">
                                <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                    <i class="fa-solid fa-briefcase text-gray peer-checked:text-primary"></i>
                                    <span class="font-medium">Office</span>
                                </div>
                            </label>
                            <label class="flex-1">
                                <input type="radio" name="address_type" value="other" class="sr-only peer">
                                <div class="flex items-center justify-center gap-2 px-4 py-3 border-2 border-gray-200 rounded-xl cursor-pointer peer-checked:border-primary peer-checked:bg-primary/5 transition-all">
                                    <i class="fa-solid fa-location-dot text-gray peer-checked:text-primary"></i>
                                    <span class="font-medium">Other</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Full Name *</label>
                        <input
                            type="text"
                            id="checkoutAddrFullName"
                            name="fullname"
                            placeholder="Enter full name"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all"
                            required
                        >
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Phone Number *</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-dark font-medium text-sm">+91</span>
                            <input
                                type="tel"
                                id="checkoutAddrPhone"
                                name="phone"
                                placeholder="Enter phone number"
                                maxlength="10"
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all"
                                required
                            >
                        </div>
                    </div>

                    <!-- Address Line 1 -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Address Line 1 *</label>
                        <input
                            type="text"
                            id="checkoutAddrLine1"
                            name="address_line1"
                            placeholder="House no., Building, Street"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all"
                            required
                        >
                    </div>

                    <!-- Address Line 2 -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">Address Line 2</label>
                        <input
                            type="text"
                            id="checkoutAddrLine2"
                            name="address_line2"
                            placeholder="Apartment, Landmark (Optional)"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all"
                        >
                    </div>

                    <!-- City & Pincode -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">City *</label>
                            <input
                                type="text"
                                id="checkoutAddrCity"
                                name="city"
                                placeholder="Enter city"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all"
                                required
                            >
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-dark mb-2">Pincode *</label>
                            <input
                                type="text"
                                id="checkoutAddrPincode"
                                name="pincode"
                                placeholder="Enter pincode"
                                maxlength="6"
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all"
                                required
                            >
                        </div>
                    </div>

                    <!-- State -->
                    <div>
                        <label class="block text-sm font-medium text-dark mb-2">State *</label>
                        <select
                            id="checkoutAddrState"
                            name="state"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 transition-all appearance-none cursor-pointer"
                            required
                        >
                            <option value="">Select State</option>
                            <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
                            <option value="Andhra Pradesh">Andhra Pradesh</option>
                            <option value="Arunachal Pradesh">Arunachal Pradesh</option>
                            <option value="Assam">Assam</option>
                            <option value="Bihar">Bihar</option>
                            <option value="Chandigarh">Chandigarh</option>
                            <option value="Chhattisgarh">Chhattisgarh</option>
                            <option value="Delhi">Delhi</option>
                            <option value="Goa">Goa</option>
                            <option value="Gujarat">Gujarat</option>
                            <option value="Haryana">Haryana</option>
                            <option value="Himachal Pradesh">Himachal Pradesh</option>
                            <option value="Jammu and Kashmir">Jammu and Kashmir</option>
                            <option value="Jharkhand">Jharkhand</option>
                            <option value="Karnataka">Karnataka</option>
                            <option value="Kerala">Kerala</option>
                            <option value="Madhya Pradesh">Madhya Pradesh</option>
                            <option value="Maharashtra">Maharashtra</option>
                            <option value="Manipur">Manipur</option>
                            <option value="Meghalaya">Meghalaya</option>
                            <option value="Mizoram">Mizoram</option>
                            <option value="Nagaland">Nagaland</option>
                            <option value="Odisha">Odisha</option>
                            <option value="Punjab">Punjab</option>
                            <option value="Rajasthan">Rajasthan</option>
                            <option value="Sikkim">Sikkim</option>
                            <option value="Tamil Nadu">Tamil Nadu</option>
                            <option value="Telangana">Telangana</option>
                            <option value="Tripura">Tripura</option>
                            <option value="Uttar Pradesh">Uttar Pradesh</option>
                            <option value="Uttarakhand">Uttarakhand</option>
                            <option value="West Bengal">West Bengal</option>
                        </select>
                    </div>

                    <!-- Set as Default -->
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" id="checkoutAddrDefault" name="is_default" value="1" class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary/20">
                            <span class="text-sm font-medium text-dark">Set as default address</span>
                        </label>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50">
                <button type="button" id="cancelCheckoutAddressBtn" class="px-6 py-2.5 border border-gray-200 rounded-full font-medium text-dark hover:bg-white transition-all">
                    Cancel
                </button>
                <button type="button" id="saveAddressBtn" class="btn-primary px-6 py-2.5">
                    <i class="fa-solid fa-check mr-2"></i>
                    Save Address
                </button>
            </div>
        </div>
    </div>
</div>
