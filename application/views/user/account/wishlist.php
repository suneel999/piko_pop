<!-- Main Content -->
<div class="site-page">
    <div class="bg-white/80 backdrop-blur-sm border-b border-primary/10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('account/profile'); ?>" class="text-gray hover:text-primary transition-colors">My Account</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium">Wishlist</span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 lg:gap-8">
            <?php $this->load->view('user/account/sidebar'); ?>

            <div class="lg:col-span-3 space-y-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-dark">My Wishlist</h1>
                    <p class="text-gray text-sm mt-1">Save your favourite PIKO POP picks for later</p>
                </div>

                <?php if (!empty($wishlist_items)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-5" id="wishlistGrid">
                    <?php foreach ($wishlist_items as $item): ?>
                    <?php
                        $images = !empty($item->images_array) ? $item->images_array : (json_decode($item->images ?? '[]', true) ?: array());
                        $image_url = !empty($images[0])
                            ? base_url('uploads/products/' . $images[0])
                            : base_url('user_assets/images/product-fallback.png');

                        $has_variants = !empty($item->variants);
                        $min_price = $item->sale_price;
                        $min_mrp = $item->mrp;
                        if ($has_variants) {
                            foreach ($item->variants as $variant) {
                                if ($variant->sale_price < $min_price) {
                                    $min_price = $variant->sale_price;
                                }
                                if ($variant->mrp < $min_mrp) {
                                    $min_mrp = $variant->mrp;
                                }
                            }
                        }

                        $discount = 0;
                        if ($min_mrp > $min_price) {
                            $discount = round((($min_mrp - $min_price) / $min_mrp) * 100);
                        }

                        $product_obj = new stdClass();
                        $product_obj->id = $item->id;
                        $product_obj->product_name = $item->product_name;
                    ?>
                    <div class="wishlist-item bg-white rounded-3xl shadow-md shadow-primary/5 border border-primary/5 overflow-hidden" data-product-id="<?php echo (int) $item->id; ?>">
                        <div class="relative">
                            <a href="<?php echo product_url($product_obj); ?>" class="block">
                                <div class="aspect-square bg-light-gray overflow-hidden">
                                    <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($item->product_name); ?>" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                </div>
                            </a>
                            <?php if ($discount > 0): ?>
                            <span class="absolute top-3 left-3 px-2 py-1 bg-primary text-white text-xs font-bold rounded-full"><?php echo $discount; ?>% OFF</span>
                            <?php endif; ?>
                            <button type="button" class="remove-wishlist-btn absolute top-3 right-3 w-9 h-9 rounded-full bg-white/95 shadow-md flex items-center justify-center text-primary hover:bg-primary hover:text-white transition-all" data-product-id="<?php echo (int) $item->id; ?>" aria-label="Remove from wishlist">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </div>

                        <div class="p-4 space-y-3">
                            <a href="<?php echo product_url($product_obj); ?>">
                                <h3 class="font-semibold text-dark line-clamp-2 hover:text-primary transition-colors"><?php echo htmlspecialchars($item->product_name); ?></h3>
                            </a>
                            <?php if (!empty($item->category_name)): ?>
                            <p class="text-xs text-gray"><?php echo htmlspecialchars($item->category_name); ?></p>
                            <?php endif; ?>

                            <div class="flex items-baseline gap-2 flex-wrap">
                                <span class="text-lg font-bold text-primary">₹<?php echo number_format($min_price, 0); ?></span>
                                <?php if ($has_variants): ?>
                                <span class="text-xs text-gray">onwards</span>
                                <?php endif; ?>
                                <?php if ($discount > 0): ?>
                                <span class="text-sm text-gray line-through">₹<?php echo number_format($min_mrp, 0); ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($has_variants): ?>
                            <a href="<?php echo product_url($product_obj); ?>" class="block w-full text-center py-2.5 rounded-full border border-primary text-primary font-semibold text-sm hover:bg-primary hover:text-white transition-all">
                                Choose options
                            </a>
                            <?php else: ?>
                            <button type="button" class="add-to-cart-btn add-to-cart-btn-action w-full py-2.5 rounded-full bg-primary text-white font-semibold text-sm hover:bg-primary-dark transition-all" data-product-id="<?php echo (int) $item->id; ?>" data-variant-id="0">
                                <i class="fa-solid fa-cart-plus mr-1"></i> Add to Cart
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="bg-white rounded-3xl shadow-md shadow-primary/5 p-8 text-center border border-primary/5">
                    <div class="w-24 h-24 bg-light-gray rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-heart text-4xl text-primary"></i>
                    </div>
                    <h2 class="text-xl font-bold text-dark mb-2">Your wishlist is empty</h2>
                    <p class="text-gray mb-6">Tap the heart on products you love and they will show up here.</p>
                    <a href="<?php echo base_url('shop'); ?>" class="btn-primary inline-flex items-center gap-2">
                        <i class="fa-solid fa-sparkles"></i>
                        <span>Explore Shop</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
