<?php
$sticker_filters = array(
    array('label' => 'ABCD Stickers', 'query' => 'ABCD Stickers'),
    array('label' => 'Animals', 'query' => 'Animal Stickers'),
    array('label' => 'Aqua World', 'query' => 'Aqua Stickers'),
    array('label' => 'Dinosaurs', 'query' => 'Dinosaur Stickers'),
    array('label' => 'Vehicles', 'query' => 'Vehicle Stickers'),
    array('label' => 'Space', 'query' => 'Space Stickers'),
    array('label' => 'Cartoons', 'query' => 'Cartoon Stickers'),
    array('label' => 'Emoji', 'query' => 'Emoji Stickers'),
    array('label' => 'Glow Collection', 'query' => 'Glow Stickers'),
);
?>
<!-- Main Content -->
<div class="site-page">
    <!-- Playful page header -->
    <section class="relative overflow-hidden border-b border-primary/10 bg-gradient-to-br from-brand-bg via-light-gray to-primary/10">
        <div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
            <div class="absolute -top-10 right-0 w-44 h-44 rounded-full bg-primary/10 blur-2xl"></div>
            <div class="absolute bottom-2 left-[10%] text-accent-yellow/70 text-lg"><i class="fa-solid fa-star"></i></div>
            <div class="absolute top-10 right-[18%] hidden md:block w-12 h-12 rounded-2xl bg-accent-blue/20 rotate-6"></div>
        </div>

        <div class="container mx-auto px-4 py-5 md:py-8 relative z-10">
            <nav class="flex items-center gap-2 text-sm mb-4 md:mb-6 flex-wrap">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('shop'); ?>" class="text-gray hover:text-primary transition-colors">Shop</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-semibold"><?php echo htmlspecialchars($category->category_name); ?></span>
            </nav>

            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
                <div class="max-w-2xl">
                    <span class="badge badge-secondary mb-3">Collection</span>
                    <h1 class="text-2xl md:text-4xl font-bold text-dark leading-tight"><?php echo htmlspecialchars($category->category_name); ?></h1>
                    <p class="text-gray text-sm md:text-base mt-2">Find your favorite PIKO POP picks in this cute collection.</p>
                    <p class="text-gray text-xs md:text-sm mt-2">
                        <?php if ($total_products > 0): ?>
                            Showing <?php echo $showing_from; ?>–<?php echo $showing_to; ?> of <?php echo $total_products; ?> cute finds
                        <?php else: ?>
                            No cute finds here yet ✨
                        <?php endif; ?>
                    </p>
                </div>

                <?php if ($total_products > 0): ?>
                <div class="flex items-center gap-3">
                    <label class="text-sm text-gray whitespace-nowrap hidden sm:block">Sort by</label>
                    <div class="relative">
                        <select id="sortSelect" class="appearance-none bg-white border border-primary/15 rounded-full px-5 py-2.5 pr-10 text-sm font-semibold text-dark focus:outline-none focus:border-primary cursor-pointer min-w-[170px] shadow-sm">
                            <option value="newest">Newest First</option>
                            <option value="price-low">Price: Low to High</option>
                            <option value="price-high">Price: High to Low</option>
                            <option value="name-az">Name: A to Z</option>
                            <option value="name-za">Name: Z to A</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-gray pointer-events-none"></i>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($subcategories)): ?>
    <div class="container mx-auto px-4 py-5 md:py-6">
        <p class="text-sm font-bold text-dark mb-3">Browse subcollections</p>
        <div class="flex flex-wrap gap-2 md:gap-3">
            <?php foreach ($subcategories as $subcat): ?>
            <a href="<?php echo category_url($subcat); ?>" class="px-4 py-2 bg-white border border-primary/15 rounded-full text-sm font-semibold text-dark hover:border-secondary hover:text-secondary transition-all">
                <?php echo htmlspecialchars($subcat->category_name); ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sticker collection filters -->
    <div class="container mx-auto px-4 pb-5 md:pb-6">
        <p class="text-sm font-bold text-dark mb-3">Sticker collections</p>
        <div class="flex overflow-x-auto gap-2 pb-1 hide-scrollbar">
            <?php foreach ($sticker_filters as $filter): ?>
            <a href="<?php echo base_url('shop/search?q=' . urlencode($filter['query'])); ?>" class="flex-shrink-0 px-4 py-2 rounded-full bg-secondary/10 text-secondary border border-secondary/15 text-sm font-semibold whitespace-nowrap hover:bg-secondary hover:text-white transition-all">
                <?php echo $filter['label']; ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Products Grid -->
    <div class="container mx-auto px-4 pb-12 md:pb-16">
        <?php if (!empty($products)): ?>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            <?php foreach ($products as $product): ?>
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

                $category_label = !empty($category->category_name) ? $category->category_name : 'Kids Favorite';
            ?>
            <div class="product-listing-card product-card group">
                <div class="relative">
                    <a href="<?php echo product_url($product); ?>" class="block">
                        <div class="aspect-square bg-light-gray rounded-t-3xl overflow-hidden relative">
                            <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($product->product_name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                            <span class="absolute top-3 left-3 badge badge-yellow z-10">⭐ <?php echo htmlspecialchars($category_label); ?></span>
                            <?php if ($discount > 0): ?>
                            <span class="absolute top-3 right-12 badge badge-primary z-10"><?php echo $discount; ?>% OFF</span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <a href="<?php echo base_url('wishlist'); ?>" class="absolute top-3 right-3 w-9 h-9 rounded-full bg-white/95 shadow-md flex items-center justify-center text-gray hover:text-primary hover:scale-110 transition-all z-10" aria-label="Wishlist">
                        <i class="fa-regular fa-heart"></i>
                    </a>
                </div>

                <div class="p-4">
                    <a href="<?php echo product_url($product); ?>">
                        <h3 class="font-bold text-dark text-sm md:text-base mb-2 line-clamp-2 min-h-[2.5rem] group-hover:text-primary transition-colors"><?php echo htmlspecialchars($product->product_name); ?></h3>
                    </a>

                    <div class="flex items-center gap-0.5 mb-2 text-accent-yellow text-xs">
                        <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
                    </div>

                    <div class="mb-3">
                        <p class="text-primary font-bold text-base md:text-lg">
                            ₹<?php echo number_format($min_price, 0); ?>
                            <?php if ($has_variants): ?><span class="text-xs font-semibold text-gray"> onwards</span><?php endif; ?>
                        </p>
                        <?php if ($discount > 0): ?>
                        <p class="text-xs text-gray mt-0.5">
                            <span class="line-through">MRP ₹<?php echo number_format($min_mrp, 0); ?></span>
                            <span class="text-secondary font-bold ml-1">Save <?php echo $discount; ?>%</span>
                        </p>
                        <?php endif; ?>
                    </div>

                    <?php if ($has_variants): ?>
                    <a href="<?php echo product_url($product); ?>" class="choose-options-btn w-full text-center">Choose options</a>
                    <?php else: ?>
                    <button class="add-to-cart-btn add-to-cart-btn-action w-full" data-product-id="<?php echo $product->id; ?>" data-variant-id="0">
                        <span><i class="fa-solid fa-cart-plus mr-1"></i> Add to Cart</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-10 md:mt-12 flex-wrap">
            <?php
            $base_url = category_url($category);
            $separator = strpos($base_url, '?') !== false ? '&' : '?';
            ?>

            <?php if ($current_page > 1): ?>
            <a href="<?php echo $base_url . $separator . 'page=' . ($current_page - 1); ?>" class="w-10 h-10 rounded-full border border-primary/15 bg-white flex items-center justify-center text-gray hover:border-primary hover:text-primary transition-colors">
                <i class="fa-solid fa-chevron-left text-sm"></i>
            </a>
            <?php else: ?>
            <button class="w-10 h-10 rounded-full border border-primary/15 bg-white flex items-center justify-center text-gray opacity-50 cursor-not-allowed" disabled>
                <i class="fa-solid fa-chevron-left text-sm"></i>
            </button>
            <?php endif; ?>

            <?php
            $range = 2;
            $start_page = max(1, $current_page - $range);
            $end_page = min($total_pages, $current_page + $range);
            ?>

            <?php if ($start_page > 1): ?>
            <a href="<?php echo $base_url . $separator . 'page=1'; ?>" class="w-10 h-10 rounded-full border border-primary/15 bg-white text-dark font-semibold hover:border-primary hover:text-primary transition-colors flex items-center justify-center">1</a>
            <?php if ($start_page > 2): ?><span class="px-2 text-gray">...</span><?php endif; ?>
            <?php endif; ?>

            <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <?php if ($i == $current_page): ?>
            <button class="w-10 h-10 rounded-full bg-primary text-white font-bold shadow-md shadow-primary/25"><?php echo $i; ?></button>
            <?php else: ?>
            <a href="<?php echo $base_url . $separator . 'page=' . $i; ?>" class="w-10 h-10 rounded-full border border-primary/15 bg-white text-dark font-semibold hover:border-primary hover:text-primary transition-colors flex items-center justify-center"><?php echo $i; ?></a>
            <?php endif; ?>
            <?php endfor; ?>

            <?php if ($end_page < $total_pages): ?>
            <?php if ($end_page < $total_pages - 1): ?><span class="px-2 text-gray">...</span><?php endif; ?>
            <a href="<?php echo $base_url . $separator . 'page=' . $total_pages; ?>" class="w-10 h-10 rounded-full border border-primary/15 bg-white text-dark font-semibold hover:border-primary hover:text-primary transition-colors flex items-center justify-center"><?php echo $total_pages; ?></a>
            <?php endif; ?>

            <?php if ($current_page < $total_pages): ?>
            <a href="<?php echo $base_url . $separator . 'page=' . ($current_page + 1); ?>" class="w-10 h-10 rounded-full border border-primary/15 bg-white flex items-center justify-center text-gray hover:border-primary hover:text-primary transition-colors">
                <i class="fa-solid fa-chevron-right text-sm"></i>
            </a>
            <?php else: ?>
            <button class="w-10 h-10 rounded-full border border-primary/15 bg-white flex items-center justify-center text-gray opacity-50 cursor-not-allowed" disabled>
                <i class="fa-solid fa-chevron-right text-sm"></i>
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="text-center py-16 md:py-20 card-pop max-w-lg mx-auto px-6">
            <div class="w-20 h-20 mx-auto mb-5 rounded-3xl bg-secondary/15 flex items-center justify-center">
                <i class="fa-solid fa-face-smile text-4xl text-secondary"></i>
            </div>
            <h3 class="text-xl font-bold text-dark mb-2">No cute finds here yet ✨</h3>
            <p class="text-gray mb-6 text-sm">This collection is waiting for adorable new arrivals.</p>
            <a href="<?php echo base_url('shop'); ?>" class="btn-primary inline-flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Browse All Products
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
