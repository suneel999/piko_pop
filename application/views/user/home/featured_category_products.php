<!-- Trending Products -->
<?php if (!empty($featured_category_products)): ?>
    <?php
    $product_badges = array('Bestseller', 'New', 'Popular');
    ?>
    <?php foreach ($featured_category_products as $section_index => $section): ?>
        <?php if ($section_index > 0) continue; ?>
        <?php
        $category = $section['category'];
        $products = $section['products'];
        ?>

        <section class="home-section bg-gray-50 border-t border-gray-100">
            <div class="page-container">
                <div class="section-header">
                    <h2 class="home-section-title">Trending Products</h2>
                    <p class="section-subtitle">Popular picks from <?php echo htmlspecialchars($category->category_name); ?></p>
                </div>

                <div class="relative group/slider">
                    <button class="product-slider-prev hidden md:flex absolute -left-2 lg:-left-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white rounded-full shadow-lg items-center justify-center text-dark hover:bg-primary hover:text-white transition-all border border-primary/10">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="product-slider-next hidden md:flex absolute -right-2 lg:-right-4 top-1/2 -translate-y-1/2 z-10 w-11 h-11 bg-white rounded-full shadow-lg items-center justify-center text-dark hover:bg-primary hover:text-white transition-all border border-primary/10">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>

                    <div class="products-scroll flex overflow-x-auto gap-4 md:gap-5 pb-4 hide-scrollbar scroll-smooth snap-x snap-mandatory">
                        <?php foreach ($products as $pindex => $product): ?>
                            <?php
                            $first_image = !empty($product->images_array) ? $product->images_array[0] : '';
                            $image_url = !empty($first_image) ? base_url('uploads/products/' . $first_image) : base_url('user_assets/images/product-fallback.png');

                            $has_variants = !empty($product->variants) && count($product->variants) > 0;
                            if ($has_variants) {
                                $min_price = $product->variants[0]->sale_price;
                                $min_mrp = $product->variants[0]->mrp;
                            } else {
                                $min_price = $product->sale_price;
                                $min_mrp = $product->mrp;
                            }

                            $discount = 0;
                            if ($min_mrp > $min_price) {
                                $discount = round((($min_mrp - $min_price) / $min_mrp) * 100);
                            }
                            $badge = $product_badges[$pindex % count($product_badges)];
                            ?>
                            <div class="home-product-card flex-shrink-0 w-[78%] sm:w-[48%] md:w-[31%] lg:w-[24%] xl:w-[270px] snap-start">
                                <div class="home-product-card-inner group">
                                    <div class="relative aspect-square bg-light-gray overflow-hidden">
                                        <span class="home-product-badge"><?php echo htmlspecialchars($badge); ?></span>
                                        <a href="<?php echo product_url($product); ?>">
                                            <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($product->product_name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">
                                        </a>
                                        <a href="<?php echo base_url('wishlist'); ?>" class="home-product-wishlist" aria-label="Add to wishlist">
                                            <i class="fa-regular fa-heart"></i>
                                        </a>
                                        <button class="quick-view-btn absolute bottom-3 left-1/2 -translate-x-1/2 bg-white text-dark px-4 py-2 rounded-full text-xs font-bold shadow-lg opacity-0 group-hover:opacity-100 transition-all duration-300 hover:bg-primary hover:text-white whitespace-nowrap"
                                            data-product-id="<?php echo $product->id; ?>"
                                            data-product-name="<?php echo htmlspecialchars($product->product_name); ?>"
                                            data-product-image="<?php echo $image_url; ?>"
                                            data-product-price="<?php echo $min_price; ?>"
                                            data-product-mrp="<?php echo $min_mrp; ?>"
                                            data-product-desc="<?php echo htmlspecialchars($product->short_description ?? ''); ?>"
                                            data-has-variants="<?php echo $has_variants ? '1' : '0'; ?>"
                                            data-product-url="<?php echo product_url($product); ?>">
                                            <i class="fa-solid fa-eye mr-1"></i> Quick View
                                        </button>
                                    </div>
                                    <div class="p-4 md:p-5 flex flex-col flex-1">
                                        <a href="<?php echo product_url($product); ?>">
                                            <h3 class="font-bold text-dark text-sm md:text-base mb-2 line-clamp-2 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($product->product_name); ?></h3>
                                        </a>
                                        <div class="flex items-center gap-0.5 mb-3 text-accent-yellow text-xs">
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                        </div>
                                        <div class="flex flex-wrap items-baseline gap-2 mb-4 mt-auto">
                                            <span class="text-primary font-bold text-lg">₹<?php echo number_format($min_price, 0); ?></span>
                                            <?php if ($discount > 0): ?>
                                                <span class="text-gray line-through text-sm">₹<?php echo number_format($min_mrp, 0); ?></span>
                                                <span class="text-green-600 text-xs font-bold bg-green-50 px-2 py-0.5 rounded-full"><?php echo $discount; ?>% OFF</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($has_variants): ?>
                                            <a href="<?php echo product_url($product); ?>" class="choose-options-btn block text-center text-sm py-3">Choose options</a>
                                        <?php else: ?>
                                            <button class="add-to-cart-btn add-to-cart-btn-action home-add-bag-btn"
                                                data-product-id="<?php echo $product->id; ?>"
                                                data-variant-id="0">
                                                <span>Add to Cart</span>
                                                <i class="fa-solid fa-bag-shopping"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="mt-8 text-center">
                    <a href="<?php echo category_url($category); ?>" class="btn-outline inline-flex items-center gap-2 bg-white">
                        View All <?php echo htmlspecialchars($category->category_name); ?>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
<?php endif; ?>
