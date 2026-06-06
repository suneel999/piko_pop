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
<!-- Main Content -->
<main class="min-h-screen bg-brand-bg">
    <!-- Breadcrumb -->
    <div class="border-b border-primary/10 bg-white/70">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm flex-wrap">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('shop'); ?>" class="text-gray hover:text-primary transition-colors">Shop</a>
                <?php if (!empty($product->category)): ?>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo category_url($product->category); ?>" class="text-gray hover:text-primary transition-colors"><?php echo htmlspecialchars($product->category->category_name); ?></a>
                <?php endif; ?>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-semibold line-clamp-1"><?php echo htmlspecialchars($product->product_name); ?></span>
            </nav>
        </div>
    </div>

    <!-- Product Section -->
    <div class="container mx-auto px-4 py-6 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">

            <!-- Left: Product Gallery -->
            <div class="product-gallery">
                <div class="main-image-container bg-light-gray rounded-3xl overflow-hidden mb-4 md:mb-6 aspect-square relative card-pop">
                    <img id="mainImage" src="<?php echo $main_image_url; ?>" alt="<?php echo htmlspecialchars($product->product_name); ?>" class="w-full h-full object-cover">
                    <?php if ($discount > 0): ?>
                    <span class="absolute top-4 left-4 badge badge-primary text-sm"><?php echo $discount; ?>% OFF</span>
                    <?php endif; ?>
                    <a href="<?php echo base_url('wishlist'); ?>" class="absolute top-4 right-4 w-11 h-11 rounded-full bg-white/95 shadow-md flex items-center justify-center text-gray hover:text-primary transition-all z-10" aria-label="Wishlist">
                        <i class="fa-regular fa-heart text-lg"></i>
                    </a>
                </div>

                <?php if (count($product->images_array) > 1): ?>
                <div class="relative flex items-center">
                    <button id="thumbPrev" class="flex-shrink-0 w-9 h-9 rounded-full bg-white border border-primary/15 flex items-center justify-center text-gray hover:text-primary transition-colors shadow-sm">
                        <i class="fa-solid fa-chevron-left text-sm"></i>
                    </button>
                    <div id="thumbnailContainer" class="flex gap-3 overflow-x-auto hide-scrollbar flex-1 px-2">
                        <?php foreach ($product->images_array as $index => $image): ?>
                        <?php $thumb_url = base_url('uploads/products/' . $image); ?>
                        <button class="thumbnail-btn flex-shrink-0 w-20 h-20 rounded-2xl overflow-hidden border-2 <?php echo $index === 0 ? 'border-secondary' : 'border-primary/15 hover:border-primary/40'; ?> bg-white p-1 transition-colors" data-image="<?php echo $thumb_url; ?>">
                            <img src="<?php echo $thumb_url; ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="w-full h-full object-cover rounded-xl">
                        </button>
                        <?php endforeach; ?>
                    </div>
                    <button id="thumbNext" class="flex-shrink-0 w-9 h-9 rounded-full bg-white border border-primary/15 flex items-center justify-center text-gray hover:text-primary transition-colors shadow-sm">
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right: Product Details -->
            <div class="product-details">
                <div class="flex flex-wrap gap-2 mb-4">
                    <span class="badge badge-yellow">⭐ Kids Love It</span>
                    <span class="badge badge-primary">🎁 Perfect Gift</span>
                    <span class="badge badge-blue">✨ Collectible</span>
                </div>

                <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-dark leading-tight mb-4">
                    <?php echo htmlspecialchars($product->product_name); ?>
                </h1>

                <div class="flex items-center gap-1 mb-4 text-accent-yellow">
                    <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    <span class="text-gray text-sm ml-2 font-semibold">Loved by kids &amp; parents</span>
                </div>

                <!-- Price block -->
                <div class="card p-4 md:p-5 mb-5">
                    <div class="flex flex-wrap items-end gap-2 mb-1">
                        <span id="productPrice" class="text-2xl md:text-3xl font-bold text-primary">₹<?php echo number_format($current_price, 0); ?></span>
                        <?php if ($current_mrp > $current_price): ?>
                        <span id="productMrp" class="text-base text-gray line-through">MRP ₹<?php echo number_format($current_mrp, 0); ?></span>
                        <?php if ($discount > 0): ?>
                        <span class="badge badge-secondary">Save <?php echo $discount; ?>%</span>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                    <p id="serversText" class="text-sm text-gray font-medium"><?php echo ($has_variants && !empty($default_variant->servers_text)) ? htmlspecialchars($default_variant->servers_text) : 'Inclusive of all taxes · Fast delivery available'; ?></p>
                </div>

                <?php if ($has_variants): ?>
                <div class="mb-6">
                    <label class="block text-dark font-bold mb-3">Select Pack</label>
                    <div class="flex flex-wrap gap-3">
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
                            <button class="variant-option px-5 py-2.5 border-2 <?php echo $index === 0 ? 'border-secondary bg-secondary/5' : 'border-primary/15 hover:border-secondary'; ?> rounded-full text-sm font-bold transition-colors"
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

                <!-- Quantity -->
                <div class="mb-6">
                    <label class="block text-dark font-bold mb-3">Quantity</label>
                    <div class="inline-flex items-center border border-primary/15 rounded-full overflow-hidden bg-white shadow-sm">
                        <button id="qtyMinus" class="qty-btn w-11 h-11 flex items-center justify-center text-gray hover:text-primary hover:bg-light-gray transition-colors">
                            <i class="fa-solid fa-minus text-sm"></i>
                        </button>
                        <input type="number" id="productQty" value="1" min="1" class="w-12 h-11 text-center font-bold text-dark border-x border-primary/15 focus:outline-none">
                        <button id="qtyPlus" class="qty-btn w-11 h-11 flex items-center justify-center text-gray hover:text-primary hover:bg-light-gray transition-colors">
                            <i class="fa-solid fa-plus text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 mb-8">
                    <button id="addToCartBtn" class="flex-1 btn-primary py-4 text-base flex items-center justify-center gap-2"
                        data-product-id="<?php echo $product->id; ?>"
                        data-variant-id="<?php echo $has_variants ? $default_variant->id : 0; ?>">
                        <i class="fa-solid fa-cart-plus"></i>
                        Add to Cart
                    </button>
                    <button id="buyNowBtn" class="flex-1 btn-secondary py-4 text-base flex items-center justify-center gap-2"
                        data-product-id="<?php echo $product->id; ?>"
                        data-variant-id="<?php echo $has_variants ? $default_variant->id : 0; ?>">
                        <i class="fa-solid fa-bolt"></i>
                        Buy Now
                    </button>
                </div>

                <?php if (!empty($product->instructions_image_desktop) || !empty($product->instructions_image_mobile)): ?>
                <div class="product-instructions-image mb-8">
                    <?php if (!empty($product->instructions_image_desktop)): ?>
                    <img src="<?php echo base_url('uploads/products/instructions/') . $product->instructions_image_desktop; ?>"
                         alt="Product details"
                         class="w-full h-auto rounded-3xl object-cover hidden md:block card-pop">
                    <?php endif; ?>
                    <?php if (!empty($product->instructions_image_mobile)): ?>
                    <img src="<?php echo base_url('uploads/products/instructions/') . $product->instructions_image_mobile; ?>"
                         alt="Product details"
                         class="w-full h-auto rounded-3xl object-cover md:hidden card-pop">
                    <?php elseif (!empty($product->instructions_image_desktop)): ?>
                    <img src="<?php echo base_url('uploads/products/instructions/') . $product->instructions_image_desktop; ?>"
                         alt="Product details"
                         class="w-full h-auto rounded-3xl object-cover md:hidden card-pop">
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Accordions -->
                <div class="border-t border-primary/10">
                    <div class="product-accordion border-b border-primary/10">
                        <button class="accordion-toggle w-full flex items-center justify-between py-4 text-left">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-sparkles text-primary"></i>
                                <span class="font-bold text-dark">About this item</span>
                            </div>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pb-4">
                            <div class="text-gray text-sm leading-relaxed prose prose-sm max-w-none">
                                <?php if (!empty($product->description)): ?>
                                    <?php echo $product->description; ?>
                                <?php else: ?>
                                    <p><?php echo htmlspecialchars($about_text); ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="product-accordion border-b border-primary/10">
                        <button class="accordion-toggle w-full flex items-center justify-between py-4 text-left">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-heart text-primary"></i>
                                <span class="font-bold text-dark">Why Kids Love It</span>
                            </div>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pb-4">
                            <ul class="space-y-2 text-gray text-sm">
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-secondary mt-0.5"></i> Fun, bright designs kids adore</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-secondary mt-0.5"></i> Collectible &amp; creative — perfect for crafts</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-secondary mt-0.5"></i> Great for decorating books, bottles &amp; more</li>
                            </ul>
                            <?php if (!empty($product->short_description)): ?>
                            <p class="text-gray text-sm leading-relaxed mt-4"><?php echo nl2br(htmlspecialchars($product->short_description)); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if (!empty($meta_data) && is_array($meta_data)): ?>
                    <div class="product-accordion border-b border-primary/10">
                        <button class="accordion-toggle w-full flex items-center justify-between py-4 text-left">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-list-check text-primary"></i>
                                <span class="font-bold text-dark">Product Details</span>
                            </div>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pb-4">
                            <table class="w-full text-sm rounded-2xl overflow-hidden">
                                <tbody>
                                    <?php foreach ($meta_data as $key => $value): ?>
                                    <tr class="border-b border-primary/10 last:border-0">
                                        <td class="py-2.5 pr-4 text-gray font-semibold w-2/5"><?php echo htmlspecialchars($key); ?></td>
                                        <td class="py-2.5 text-dark"><?php echo htmlspecialchars($value); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="product-accordion border-b border-primary/10">
                        <button class="accordion-toggle w-full flex items-center justify-between py-4 text-left">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-list-check text-primary"></i>
                                <span class="font-bold text-dark">Product Details</span>
                            </div>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pb-4">
                            <table class="w-full text-sm">
                                <tbody>
                                    <tr class="border-b border-primary/10"><td class="py-2.5 pr-4 text-gray font-semibold">Type</td><td class="py-2.5 text-dark"><?php echo !empty($product->category) ? htmlspecialchars($product->category->category_name) : 'Kids Product'; ?></td></tr>
                                    <tr class="border-b border-primary/10"><td class="py-2.5 pr-4 text-gray font-semibold">Brand</td><td class="py-2.5 text-dark">PIKO POP</td></tr>
                                    <tr><td class="py-2.5 pr-4 text-gray font-semibold">Ideal for</td><td class="py-2.5 text-dark">Kids, gifting &amp; creative play</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="product-accordion border-b border-primary/10">
                        <button class="accordion-toggle w-full flex items-center justify-between py-4 text-left">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-shield-heart text-primary"></i>
                                <span class="font-bold text-dark">Safety &amp; Quality</span>
                            </div>
                            <i class="fa-solid fa-plus text-gray text-sm transition-transform"></i>
                        </button>
                        <div class="accordion-content hidden pb-4">
                            <p class="text-gray text-sm leading-relaxed mb-3">PIKO POP products are thoughtfully selected with kids in mind. We focus on quality materials, cheerful designs and a premium unboxing experience parents can trust.</p>
                            <ul class="space-y-2 text-gray text-sm">
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-secondary mt-0.5"></i> Quality-checked before shipping</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-secondary mt-0.5"></i> Safe, kid-friendly product selection</li>
                                <li class="flex items-start gap-2"><i class="fa-solid fa-check text-secondary mt-0.5"></i> Secure packaging for delivery</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button id="shareBtn" class="flex items-center gap-2 text-gray hover:text-primary transition-colors">
                        <i class="fa-solid fa-share-nodes"></i>
                        <span class="text-sm font-semibold">Share this cute find</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($related_products)): ?>
    <section class="py-10 md:py-16 section-pastel-purple border-t border-primary/10">
        <div class="container mx-auto px-4">
            <div class="flex items-end justify-between mb-6 md:mb-8">
                <h2 class="section-title">You May Also Like</h2>
                <a href="<?php echo base_url('shop'); ?>" class="text-primary font-bold text-sm hover:underline hidden sm:inline-flex items-center gap-1">
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
                            <div class="aspect-square bg-light-gray rounded-t-3xl overflow-hidden relative">
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
</main>
