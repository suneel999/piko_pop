</main>
<?php
if (!isset($header_categories)) {
    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $header_categories = $CI->common->getdatabytableall('categories', array('parent_category_id' => 0, 'status' => 1), 'sort_order', 'ASC');
}
?>
<!-- Footer -->
<footer class="site-footer">
    <!-- Trust strip -->
    <div class="site-footer-trust">
        <div class="page-container">
            <div class="site-footer-trust-grid">
                <div class="site-footer-trust-item">
                    <span class="site-footer-trust-icon"><i class="fa-solid fa-truck-fast"></i></span>
                    <div>
                        <p class="site-footer-trust-title">Free Shipping</p>
                        <p class="site-footer-trust-desc">On orders above ₹499</p>
                    </div>
                </div>
                <div class="site-footer-trust-item">
                    <span class="site-footer-trust-icon"><i class="fa-solid fa-rotate-left"></i></span>
                    <div>
                        <p class="site-footer-trust-title">Easy Returns</p>
                        <p class="site-footer-trust-desc">Hassle-free process</p>
                    </div>
                </div>
                <div class="site-footer-trust-item">
                    <span class="site-footer-trust-icon"><i class="fa-solid fa-shield-heart"></i></span>
                    <div>
                        <p class="site-footer-trust-title">Safe for Kids</p>
                        <p class="site-footer-trust-desc">Child-friendly picks</p>
                    </div>
                </div>
                <div class="site-footer-trust-item">
                    <span class="site-footer-trust-icon"><i class="fa-solid fa-headset"></i></span>
                    <div>
                        <p class="site-footer-trust-title">Support</p>
                        <p class="site-footer-trust-desc">We&rsquo;re here to help</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main footer -->
    <div class="site-footer-main">
        <div class="page-container">
            <div class="site-footer-grid">
                <div class="site-footer-brand">
                    <a href="<?php echo base_url(); ?>" class="inline-block mb-5">
                        <img src="<?php echo brand_logo_url('white'); ?>" alt="PIKO POP" class="site-footer-logo">
                    </a>
                    <p class="site-footer-about">
                        Premium cute products for kids — stickers, stationery, toys &amp; gifts that spark joy every day.
                    </p>
                    <div class="site-footer-social">
                        <a href="#" class="site-footer-social-link" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="site-footer-social-link" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="site-footer-social-link" aria-label="X"><i class="fa-brands fa-x-twitter"></i></a>
                        <a href="#" class="site-footer-social-link" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <div class="site-footer-col">
                    <h4 class="site-footer-heading">Shop</h4>
                    <ul class="site-footer-links">
                        <li><a href="<?php echo base_url('shop'); ?>">All Products</a></li>
                        <li><a href="<?php echo base_url('shop'); ?>">New Arrivals</a></li>
                        <li><a href="<?php echo base_url('shop'); ?>">Best Sellers</a></li>
                        <?php if (!empty($header_categories)): ?>
                            <?php foreach (array_slice($header_categories, 0, 4) as $category): ?>
                            <li><a href="<?php echo category_url($category); ?>"><?php echo htmlspecialchars($category->category_name); ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>

                <div class="site-footer-col">
                    <h4 class="site-footer-heading">Help</h4>
                    <ul class="site-footer-links">
                        <li><a href="<?php echo base_url('page/shipping_policy'); ?>">Shipping Policy</a></li>
                        <li><a href="<?php echo base_url('page/refund_policy'); ?>">Return &amp; Refund</a></li>
                        <li><a href="<?php echo base_url('page/privacy_policy'); ?>">Privacy Policy</a></li>
                        <li><a href="<?php echo base_url('page/terms_conditions'); ?>">Terms &amp; Conditions</a></li>
                        <li><a href="<?php echo base_url('page/about'); ?>">About PIKO POP</a></li>
                    </ul>
                </div>

                <div class="site-footer-col">
                    <h4 class="site-footer-heading">Contact</h4>
                    <ul class="site-footer-contact">
                        <li>
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Sri Sai Nagar, Boduppal, Hyderabad - 500039</span>
                        </li>
                        <li>
                            <i class="fa-solid fa-phone"></i>
                            <a href="tel:+919542200222">+91 95422 00222</a>
                        </li>
                        <li>
                            <i class="fa-solid fa-envelope"></i>
                            <a href="mailto:hello@pikopop.in">hello@pikopop.in</a>
                        </li>
                        <li>
                            <i class="fa-solid fa-clock"></i>
                            <span>Mon – Sat: 10:00 AM – 7:00 PM</span>
                        </li>
                    </ul>
                    <a href="<?php echo base_url('page/contact'); ?>" class="site-footer-cta">
                        Get in touch <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="site-footer-bottom">
        <div class="page-container site-footer-bottom-inner">
            <p>&copy; <?php echo date('Y'); ?> PIKO POP. All rights reserved.</p>
            <p class="site-footer-bottom-note">Secure checkout &middot; Razorpay payments</p>
        </div>
    </div>
</footer>

<!-- Login Prompt Modal -->
<div id="loginPromptModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <!-- Overlay -->
    <div class="login-modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

    <!-- Modal Container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <!-- Modal Content -->
        <div class="relative w-full max-w-sm bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all">
            <!-- Modal Body -->
            <div class="p-6 text-center">
                <!-- Icon -->
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-primary/10 flex items-center justify-center">
                    <i class="fa-solid fa-user-lock text-3xl text-primary"></i>
                </div>

                <!-- Title -->
                <h3 class="text-xl font-bold text-dark mb-2">Login Required</h3>

                <!-- Message -->
                <p class="text-gray text-sm mb-6">Please login to add items to your cart and enjoy a seamless shopping experience.</p>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button" class="login-modal-close flex-1 px-4 py-3 border border-gray-200 text-dark font-semibold rounded-full hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <a href="<?php echo base_url('login'); ?>" class="flex-1 px-4 py-3 bg-primary text-white font-semibold rounded-full hover:bg-primary-dark transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($quick_view_modal)): ?>
    <!-- Quick View Modal -->
    <div id="quickViewModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <!-- Overlay -->
        <div class="modal-overlay fixed inset-0 bg-black/50 backdrop-blur-sm"></div>

        <!-- Modal Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal Content -->
            <div class="modal-content relative w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Close Button -->
                <button class="modal-close absolute top-4 right-4 w-10 h-10 rounded-full bg-white hover:bg-gray-100 flex items-center justify-center text-gray-500 hover:text-dark z-20 transition-colors shadow-md">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>

                <!-- Modal Body -->
                <div class="grid grid-cols-1 md:grid-cols-2">
                    <!-- Product Image Section -->
                    <div class="bg-gray-100 p-6 md:p-8">
                        <div class="aspect-square rounded-xl overflow-hidden bg-white shadow-sm">
                            <img id="modalProductImage" src="" alt="Product" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Product Details Section -->
                    <div class="p-6 md:p-8 flex flex-col">
                        <!-- Product Title -->
                        <h2 id="modalProductName" class="text-2xl md:text-3xl font-bold text-dark mb-2 pr-8"></h2>

                        <!-- Price -->
                        <div class="flex items-center gap-3 mb-2">
                            <p id="modalProductPrice" class="text-primary font-bold text-xl md:text-2xl"></p>
                            <p id="modalProductMrp" class="text-gray text-base line-through hidden"></p>
                        </div>

                        <!-- Tax Info -->
                        <p class="text-secondary text-sm mb-4">Inclusive of all taxes.</p>

                        <!-- Description -->
                        <p id="modalProductDesc" class="text-gray text-sm leading-relaxed mb-6"></p>

                        <!-- Weight Options (will be populated dynamically) -->
                        <div id="modalVariantsSection" class="mb-5 hidden">
                            <label class="block text-dark font-semibold mb-3">Select Weight:</label>
                            <div id="modalVariantOptions" class="flex flex-wrap gap-2">
                                <!-- Variants will be added here -->
                            </div>
                        </div>

                        <!-- Quantity -->
                        <div class="mb-6">
                            <label class="block text-dark font-semibold mb-3">Quantity:</label>
                            <div class="inline-flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                <button class="modal-qty-minus w-12 h-12 flex items-center justify-center hover:bg-gray-100 transition-colors text-dark">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" id="modalQty" value="1" min="1" class="w-14 h-12 text-center font-semibold text-lg border-x border-gray-200 focus:outline-none">
                                <button class="modal-qty-plus w-12 h-12 flex items-center justify-center hover:bg-gray-100 transition-colors text-dark">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3 mt-auto">
                            <button id="modalAddToCart" class="flex-1 bg-primary hover:bg-primary-dark text-white px-5 py-3 rounded-full font-semibold text-sm transition-all duration-300 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-cart-shopping"></i>
                                <span>Add to Cart</span>
                            </button>
                            <a id="modalViewProduct" href="#" class="px-5 py-3 border-2 border-primary text-primary rounded-full font-semibold text-sm transition-all duration-300 hover:bg-primary/5 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-eye"></i>
                                <span>View Details</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- jQuery Validation Plugin -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/additional-methods.min.js"></script>

<!-- Toastr for AJAX notifications -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- jQuery Validation Styles & Toastr Config -->
<style>
    label.error {
        color: #dc2626;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        display: block;
        font-weight: 500;
    }

    input.error,
    select.error,
    textarea.error {
        border-color: #dc2626 !important;
        background-color: #fef2f2 !important;
    }

    input.valid,
    select.valid,
    textarea.valid {
        border-color: #16a34a !important;
    }
</style>
<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "3000",
        "extendedTimeOut": "1000"
    };
</script>

<!-- Base URL for JS -->
<script>
    var base_url = '<?php echo base_url(); ?>';
    var is_logged_in = <?php echo is_loggedin_user() ? 'true' : 'false'; ?>;
</script>

<!-- Custom JS -->
<script src="<?php echo base_url('user_assets/') ?>js/main.js"></script>

<?php if (!empty($active_page) && $active_page == 'profile'): ?>
    <?php $this->load->view('user/account/profile_js'); ?>
<?php endif; ?>

<?php if (!empty($active_page) && $active_page == 'addresses'): ?>
    <?php $this->load->view('user/account/addresses_js'); ?>
<?php endif; ?>


<?php if (!empty($cart_validation)): ?>
    <!-- Cart Functionality -->
    <script>
        $(document).ready(function() {
            // Show Login Prompt Modal
            function showLoginPrompt() {
                $('#loginPromptModal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            }

            // Close Login Prompt Modal
            $(document).on('click', '.login-modal-close, .login-modal-overlay', function() {
                $('#loginPromptModal').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            });

            // Add to Cart
            $(document).on('click', '.add-to-cart-btn-action', function(e) {
                e.preventDefault();

                // Check if user is logged in
                if (!is_logged_in) {
                    showLoginPrompt();
                    return;
                }

                var btn = $(this);
                var productId = btn.data('product-id');
                var variantId = btn.data('variant-id') || 0;
                var qty = 1;

                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Adding...');

                $.ajax({
                    url: base_url + 'cart/add',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        variant_id: variantId,
                        quantity: qty
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 200) {
                            // Update cart count
                            $('.cart-count').text(response.cart_count);
                            btn.html('<i class="fa-solid fa-check mr-1"></i> Added!');
                            toastr.success('Item added to cart!');
                            setTimeout(function() {
                                btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus mr-1"></i> Add to Cart');
                            }, 2000);
                        } else {
                            toastr.error(response.message);
                            btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus mr-1"></i> Add to Cart');
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong. Please try again.');
                        btn.prop('disabled', false).html('<i class="fa-solid fa-cart-plus mr-1"></i> Add to Cart');
                    }
                });
            });

            // Quick View Modal
            $(document).on('click', '.quick-view-btn', function(e) {
                e.preventDefault();
                var btn = $(this);
                var productId = btn.data('product-id');

                // Populate modal with data attributes
                $('#modalProductImage').attr('src', btn.data('product-image'));
                $('#modalProductName').text(btn.data('product-name'));
                $('#modalProductPrice').text('Rs. ' + parseFloat(btn.data('product-price')).toFixed(2));
                $('#modalProductDesc').text(btn.data('product-desc') || 'A cute pick from PIKO POP.');
                $('#modalViewProduct').attr('href', btn.data('product-url'));
                $('#modalAddToCart').data('product-id', productId);
                $('#modalQty').val(1);

                // Show MRP if different
                var price = parseFloat(btn.data('product-price'));
                var mrp = parseFloat(btn.data('product-mrp'));
                if (mrp > price) {
                    $('#modalProductMrp').text('Rs. ' + mrp.toFixed(2)).removeClass('hidden');
                } else {
                    $('#modalProductMrp').addClass('hidden');
                }

                // Handle variants
                if (btn.data('has-variants') == '1') {
                    // Fetch variants via AJAX
                    $.ajax({
                        url: base_url + 'cart/get_variants',
                        type: 'POST',
                        data: {
                            product_id: productId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status == 200 && response.variants.length > 0) {
                                var html = '';
                                $.each(response.variants, function(index, variant) {
                                    var activeClass = index === 0 ? 'border-dark bg-gray-50' : 'border-gray-200';
                                    html += '<button class="variant-option px-5 py-2.5 border-2 ' + activeClass + ' rounded-full text-sm font-medium hover:border-dark transition-colors" data-variant-id="' + variant.id + '" data-price="' + variant.sale_price + '" data-mrp="' + variant.mrp + '">' + variant.variant_name + '</button>';
                                });
                                $('#modalVariantOptions').html(html);
                                $('#modalVariantsSection').removeClass('hidden');
                                $('#modalAddToCart').data('variant-id', response.variants[0].id);
                            }
                        }
                    });
                } else {
                    $('#modalVariantsSection').addClass('hidden');
                    $('#modalAddToCart').data('variant-id', 0);
                }

                // Show modal
                $('#quickViewModal').removeClass('hidden');
                $('body').addClass('overflow-hidden');
            });

            // Variant selection in modal
            $(document).on('click', '.variant-option', function() {
                $('.variant-option').removeClass('border-dark bg-gray-50').addClass('border-gray-200');
                $(this).removeClass('border-gray-200').addClass('border-dark bg-gray-50');

                var price = parseFloat($(this).data('price'));
                var mrp = parseFloat($(this).data('mrp'));
                var variantId = $(this).data('variant-id');

                $('#modalProductPrice').text('Rs. ' + price.toFixed(2));
                $('#modalAddToCart').data('variant-id', variantId);

                if (mrp > price) {
                    $('#modalProductMrp').text('Rs. ' + mrp.toFixed(2)).removeClass('hidden');
                } else {
                    $('#modalProductMrp').addClass('hidden');
                }
            });

            // Modal quantity buttons
            $('.modal-qty-minus').click(function() {
                var qty = parseInt($('#modalQty').val());
                if (qty > 1) {
                    $('#modalQty').val(qty - 1);
                }
            });

            $('.modal-qty-plus').click(function() {
                var qty = parseInt($('#modalQty').val());
                $('#modalQty').val(qty + 1);
            });

            // Modal Add to Cart
            $('#modalAddToCart').click(function() {
                // Check if user is logged in
                if (!is_logged_in) {
                    $('#quickViewModal').addClass('hidden');
                    showLoginPrompt();
                    return;
                }

                var btn = $(this);
                var productId = btn.data('product-id');
                var variantId = btn.data('variant-id') || 0;
                var qty = parseInt($('#modalQty').val());

                btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-1"></i> Adding...');

                $.ajax({
                    url: base_url + 'cart/add',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        variant_id: variantId,
                        quantity: qty
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 200) {
                            $('.cart-count').text(response.cart_count);
                            btn.html('<i class="fa-solid fa-check mr-1"></i> Added!');
                            toastr.success('Item added to cart!');
                            setTimeout(function() {
                                $('#quickViewModal').addClass('hidden');
                                $('body').removeClass('overflow-hidden');
                                btn.prop('disabled', false).html('<i class="fa-solid fa-cart-shopping mr-1"></i> Add to Cart');
                            }, 1500);
                        } else {
                            toastr.error(response.message);
                            btn.prop('disabled', false).html('<i class="fa-solid fa-cart-shopping mr-1"></i> Add to Cart');
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong. Please try again.');
                        btn.prop('disabled', false).html('<i class="fa-solid fa-cart-shopping mr-1"></i> Add to Cart');
                    }
                });
            });

            // Close modal
            $(document).on('click', '.modal-close, .modal-overlay', function() {
                $('#quickViewModal').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            });

            // Close modal on escape key
            $(document).keyup(function(e) {
                if (e.key === "Escape") {
                    $('#quickViewModal').addClass('hidden');
                    $('#loginPromptModal').addClass('hidden');
                    $('body').removeClass('overflow-hidden');
                }
            });
        });
    </script>
<?php endif; ?>


<?php $this->load->view('user/common/analytics_js'); ?>

<?php if (!empty($comparison)): ?>
    <script>
        $(document).ready(function() {
            initComparisons();

            function initComparisons() {
                const slider = document.getElementById('beforeAfterSlider');
                const beforeContainer = document.getElementById('beforeImageContainer');
                const beforeImage = document.getElementById('beforeImage');
                const handle = document.getElementById('sliderHandle');
                let clicked = 0;
                let sliderWidth;

                // Set before image width to match slider
                function setImageWidth() {
                    sliderWidth = slider.offsetWidth;
                    beforeImage.style.width = sliderWidth + 'px';
                }

                // Wait for images to load
                const afterImg = document.getElementById('afterImage');
                if (afterImg.complete) {
                    setImageWidth();
                } else {
                    afterImg.onload = setImageWidth;
                }

                window.addEventListener('resize', setImageWidth);

                // Set initial position
                slide(sliderWidth / 2);

                // Add events for mouse
                handle.addEventListener('mousedown', slideReady);
                window.addEventListener('mouseup', slideFinish);
                window.addEventListener('mousemove', slideMove);

                // Add events for touch
                handle.addEventListener('touchstart', slideReady);
                window.addEventListener('touchend', slideFinish);
                window.addEventListener('touchmove', slideMove);

                // Click anywhere on slider to move
                slider.addEventListener('click', function(e) {
                    if (e.target === handle || handle.contains(e.target)) return;
                    let pos = getCursorPos(e);
                    if (pos < 0) pos = 0;
                    if (pos > sliderWidth) pos = sliderWidth;
                    slide(pos);
                });

                function slideReady(e) {
                    e.preventDefault();
                    clicked = 1;
                }

                function slideFinish() {
                    clicked = 0;
                }

                function slideMove(e) {
                    if (clicked === 0) return;
                    let pos = getCursorPos(e);
                    if (pos < 0) pos = 0;
                    if (pos > sliderWidth) pos = sliderWidth;
                    requestAnimationFrame(function() {
                        slide(pos);
                    });
                }

                function getCursorPos(e) {
                    const rect = slider.getBoundingClientRect();
                    let x = 0;
                    if (e.type.includes('touch')) {
                        x = e.touches[0].pageX - rect.left;
                    } else {
                        x = e.pageX - rect.left;
                    }
                    x = x - window.pageXOffset;
                    return x;
                }

                function slide(x) {
                    beforeContainer.style.width = x + 'px';
                    handle.style.left = x + 'px';
                }
            }
        });
    </script>
<?php endif; ?>

</body>

</html>
