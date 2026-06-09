<?php
$first_image = !empty($product->images_array) ? $product->images_array[0] : '';
$main_image_url = !empty($first_image) ? base_url('uploads/products/' . $first_image) : base_url('user_assets/images/product-fallback.png');

$has_variants = !empty($product->variants) && count($product->variants) > 0;
if ($has_variants) {
    $default_variant = $product->variants[0];
    $current_price = $default_variant->sale_price;
    $current_mrp = $default_variant->mrp;
} else {
    $current_price = $product->sale_price;
    $current_mrp = $product->mrp;
}

$discount = 0;
if ($current_mrp > $current_price) {
    $discount = round((($current_mrp - $current_price) / $current_mrp) * 100);
}

$meta_data = !empty($product->meta_data) ? json_decode($product->meta_data, true) : null;
$about_text = !empty($product->description)
    ? strip_tags($product->description)
    : (!empty($product->short_description) ? $product->short_description : 'Bright, colorful designs made for endless creativity.');
?>
<!-- Product Detail Page -->
<div class="pdp-page">
    <div class="page-breadcrumb">
        <div class="page-container page-breadcrumb-inner">
            <nav class="flex items-center gap-2 text-sm flex-wrap">
                <a href="<?php echo base_url(); ?>">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <a href="<?php echo base_url('shop'); ?>">Shop</a>
                <?php if (!empty($product->category)): ?>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <a href="<?php echo category_url($product->category); ?>"><?php echo htmlspecialchars($product->category->category_name); ?></a>
                <?php endif; ?>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-dark font-medium line-clamp-1"><?php echo htmlspecialchars($product->product_name); ?></span>
            </nav>
        </div>
    </div>

    <div class="page-container pdp-shell">
        <div class="pdp-layout">

            <div class="product-gallery pdp-gallery">
                <div class="main-image-container pdp-main-image">
                    <img id="mainImage" src="<?php echo $main_image_url; ?>" alt="<?php echo htmlspecialchars($product->product_name); ?>">
                    <?php if ($discount > 0): ?>
                    <span class="pdp-discount-badge"><?php echo $discount; ?>% OFF</span>
                    <?php endif; ?>
                    <a href="<?php echo base_url('wishlist'); ?>" class="pdp-wishlist-btn" aria-label="Wishlist">
                        <i class="fa-regular fa-heart text-lg"></i>
                    </a>
                </div>

                <?php if (count($product->images_array) > 1): ?>
                <div class="pdp-thumbs">
                    <button id="thumbPrev" type="button" class="pdp-thumb-btn" aria-label="Previous thumbnails">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <div id="thumbnailContainer" class="pdp-thumb-list">
                        <?php foreach ($product->images_array as $index => $image): ?>
                        <?php $thumb_url = base_url('uploads/products/' . $image); ?>
                        <button type="button" class="thumbnail-btn <?php echo $index === 0 ? 'border-secondary' : ''; ?>" data-image="<?php echo $thumb_url; ?>" aria-label="View image <?php echo $index + 1; ?>">
                            <img src="<?php echo $thumb_url; ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="w-full h-full object-cover rounded-md">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <button id="thumbNext" type="button" class="pdp-thumb-btn" aria-label="Next thumbnails">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <div class="product-details pdp-info">
                <?php if (!empty($product->category)): ?>
                <p class="text-xs font-semibold uppercase tracking-wider text-primary mb-2"><?php echo htmlspecialchars($product->category->category_name); ?></p>
                <?php endif; ?>

                <h1 class="pdp-title"><?php echo htmlspecialchars($product->product_name); ?></h1>

                <p class="pdp-meta">Inclusive of all taxes · Secure checkout · Fast delivery available</p>

                <div class="pdp-price-box">
                    <div class="pdp-price-row">
                        <span id="productPrice" class="pdp-price-current">₹<?php echo number_format($current_price, 0); ?></span>
                        <?php if ($current_mrp > $current_price): ?>
                        <span id="productMrp" class="pdp-price-mrp">MRP ₹<?php echo number_format($current_mrp, 0); ?></span>
                        <?php if ($discount > 0): ?>
                        <span class="pdp-price-save">Save <?php echo $discount; ?>%</span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <p id="serversText" class="pdp-tax-note"><?php echo ($has_variants && !empty($default_variant->servers_text)) ? htmlspecialchars($default_variant->servers_text) : 'Price inclusive of all applicable taxes'; ?></p>
                </div>

                <?php if ($has_variants): ?>
                <div class="mb-6">
                    <label class="pdp-field-label">Select option</label>
                    <div class="flex flex-wrap gap-2.5">
                        <?php foreach ($product->variants as $index => $variant): ?>
                        <?php
                            $variant_discount = 0;
                            if ($variant->mrp > $variant->sale_price) {
                                $variant_discount = round((($variant->mrp - $variant->sale_price) / $variant->mrp) * 100);
                            }
                        ?>
                        <div class="variant-wrapper relative">
                            <?php if ($variant_discount > 0): ?>
                            <span class="variant-discount-tooltip">SAVE <?php echo $variant_discount; ?>%</span>
                            <?php endif; ?>
                            <button type="button" class="variant-option pdp-variant-option <?php echo $index === 0 ? 'border-secondary bg-secondary/5' : ''; ?>"
                                data-variant-id="<?php echo $variant->id; ?>"
                                data-price="<?php echo $variant->sale_price; ?>"
                                data-mrp="<?php echo $variant->mrp; ?>"
                                data-stock="<?php echo isset($variant->available_stock) ? $variant->available_stock : 999; ?>"
                                data-servers-text="<?php echo htmlspecialchars(isset($variant->servers_text) ? $variant->servers_text : ''); ?>">
                                <?php echo htmlspecialchars($variant->variant_name); ?>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <div class="mb-6">
                    <label class="pdp-field-label" for="productQty">Quantity</label>
                    <div class="pdp-qty">
                        <button type="button" id="qtyMinus" class="qty-btn" aria-label="Decrease quantity">
                            <i class="fa-solid fa-minus text-sm"></i>
                        </button>
                        <input type="number" id="productQty" value="1" min="1" aria-label="Quantity">
                        <button type="button" id="qtyPlus" class="qty-btn" aria-label="Increase quantity">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="pdp-actions">
                    <button type="button" id="addToCartBtn" class="flex-1 btn-primary flex items-center justify-center gap-2"
                        data-product-id="<?php echo $product->id; ?>"
                        data-variant-id="<?php echo $has_variants ? $default_variant->id : 0; ?>">
                        <i class="fa-solid fa-cart-plus"></i>
                        Add to Cart
                    </button>
                    <button type="button" id="buyNowBtn" class="flex-1 btn-secondary flex items-center justify-center gap-2"
                        data-product-id="<?php echo $product->id; ?>"
                        data-variant-id="<?php echo $has_variants ? $default_variant->id : 0; ?>">
                        <i class="fa-solid fa-bolt"></i>
                        Buy Now
                    </button>
                </div>

                <div class="pdp-accordions">
                    <div class="product-accordion pdp-accordion">
                        <button type="button" class="accordion-toggle pdp-accordion-toggle">
                            <span class="pdp-accordion-title">About this product</span>
                            <i class="fa-solid fa-minus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content pdp-accordion-body">
                            <div class="text-gray text-sm leading-relaxed prose prose-sm max-w-none">
                                <?php if (!empty($product->description)): ?>
                                    <?php echo $product->description; ?>
                                <?php else: ?>
                                    <p><?php echo htmlspecialchars($about_text); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="product-accordion pdp-accordion">
                        <button type="button" class="accordion-toggle pdp-accordion-toggle">
                            <span class="pdp-accordion-title">Highlights</span>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pdp-accordion-body">
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-green-600 mt-0.5"></i> Quality-checked products for kids</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-green-600 mt-0.5"></i> Ideal for gifting and everyday use</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-green-600 mt-0.5"></i> Secure packaging for safe delivery</li>
                            </ul>
                            <?php if (!empty($product->short_description)): ?>
                            <p class="text-gray text-sm leading-relaxed mt-4"><?php echo nl2br(htmlspecialchars($product->short_description)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($meta_data) && is_array($meta_data)): ?>
                    <div class="product-accordion pdp-accordion">
                        <button type="button" class="accordion-toggle pdp-accordion-toggle">
                            <span class="pdp-accordion-title">Specifications</span>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pdp-accordion-body">
                            <table class="w-full text-sm">
                                <tbody>
                                    <?php foreach ($meta_data as $key => $value): ?>
                                    <tr class="border-b border-gray-200 last:border-0">
                                        <td class="py-3 pr-4 text-gray font-medium w-2/5 align-top"><?php echo htmlspecialchars($key); ?></td>
                                        <td class="py-3 text-dark align-top"><?php echo htmlspecialchars($value); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="product-accordion pdp-accordion">
                        <button type="button" class="accordion-toggle pdp-accordion-toggle">
                            <span class="pdp-accordion-title">Specifications</span>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pdp-accordion-body">
                            <table class="w-full text-sm">
                                <tbody>
                                    <tr class="border-b border-gray-200"><td class="py-3 pr-4 text-gray font-medium">Type</td><td class="py-3 text-dark"><?php echo !empty($product->category) ? htmlspecialchars($product->category->category_name) : 'Kids Product'; ?></td></tr>
                                    <tr class="border-b border-gray-200"><td class="py-3 pr-4 text-gray font-medium">Brand</td><td class="py-3 text-dark">PIKO POP</td></tr>
                                    <tr><td class="py-3 pr-4 text-gray font-medium">Ideal for</td><td class="py-3 text-dark">Kids &amp; gifting</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="product-accordion pdp-accordion">
                        <button type="button" class="accordion-toggle pdp-accordion-toggle">
                            <span class="pdp-accordion-title">Shipping &amp; returns</span>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pdp-accordion-body">
                            <p class="mb-3">We pack every order with care. Delivery timelines vary by location; you will see updates after checkout.</p>
                            <ul class="space-y-2">
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-green-600 mt-0.5"></i> Free shipping on orders above ₹499</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-green-600 mt-0.5"></i> Easy returns as per store policy</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-green-600 mt-0.5"></i> Secure payment options at checkout</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="button" id="shareBtn" class="flex items-center gap-2 text-gray hover:text-primary transition-colors">
                        <i class="fa-solid fa-share-nodes"></i>
                        <span class="text-sm font-medium">Share product</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($related_products)): ?>
    <section class="pdp-related">
        <div class="page-container">
            <div class="flex items-end justify-between mb-8">
                <h2 class="pdp-related-title">You may also like</h2>
                <a href="<?php echo base_url('shop'); ?>" class="text-primary font-semibold text-sm hover:underline hidden sm:inline-flex items-center gap-1">
                    View all <i class="fa-solid fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <?php foreach ($related_products as $rel_product): ?>
                <?php
                    $rel_first_image = !empty($rel_product->images_array) ? $rel_product->images_array[0] : '';
                    $rel_image_url = !empty($rel_first_image) ? base_url('uploads/products/' . $rel_first_image) : base_url('user_assets/images/product-fallback.png');

                    $rel_has_variants = !empty($rel_product->variants) && count($rel_product->variants) > 0;
                    if ($rel_has_variants) {
                        $rel_min_price = $rel_product->variants[0]->sale_price;
                        $rel_min_mrp = $rel_product->variants[0]->mrp;
                    } else {
                        $rel_min_price = $rel_product->sale_price;
                        $rel_min_mrp = $rel_product->mrp;
                    }

                    $rel_discount = 0;
                    if ($rel_min_mrp > $rel_min_price) {
                        $rel_discount = round((($rel_min_mrp - $rel_min_price) / $rel_min_mrp) * 100);
                    }
                ?>
                <div class="product-listing-card product-card group">
                    <div class="relative">
                        <a href="<?php echo product_url($rel_product); ?>" class="block">
                            <div class="aspect-square bg-gray-50 rounded-t-xl overflow-hidden relative border border-gray-200 border-b-0">
                                <img src="<?php echo $rel_image_url; ?>" alt="<?php echo htmlspecialchars($rel_product->product_name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                <?php if ($rel_discount > 0): ?>
                                <span class="absolute top-3 left-3 badge badge-primary z-10"><?php echo $rel_discount; ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                        </a>
                        <a href="<?php echo base_url('wishlist'); ?>" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/95 shadow-md flex items-center justify-center text-gray hover:text-primary transition-all z-10" aria-label="Wishlist">
                            <i class="fa-regular fa-heart"></i>
                        </a>
                    </div>
                    <div class="p-4">
                        <a href="<?php echo product_url($rel_product); ?>">
                            <h3 class="font-bold text-dark text-sm md:text-base mb-2 line-clamp-2 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($rel_product->product_name); ?></h3>
                        </a>
                        <p class="text-primary font-bold text-base mb-3">
                            ₹<?php echo number_format($rel_min_price, 0); ?>
                            <?php if ($rel_has_variants): ?><span class="text-xs text-gray font-semibold"> onwards</span><?php endif; ?>
                        </p>
                        <?php if ($rel_has_variants): ?>
                        <a href="<?php echo product_url($rel_product); ?>" class="choose-options-btn w-full text-center text-sm">Choose options</a>
                        <?php else: ?>
                        <button class="add-to-cart-btn add-to-cart-btn-action w-full" data-product-id="<?php echo $rel_product->id; ?>" data-variant-id="0">
                            <span><i class="fa-solid fa-cart-plus mr-1"></i> Add to Cart</span>
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>
</div>
