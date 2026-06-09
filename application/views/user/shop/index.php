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
$shop_heading = isset($search_keyword) ? 'Search Results' : 'Shop All Products';
$shop_subheading = isset($search_keyword)
    ? 'Results for "' . htmlspecialchars($search_keyword) . '"'
    : 'Stickers, stationery, toys and gifts for kids';
?>
<div class="site-page shop-page">
    <div class="page-breadcrumb">
        <div class="page-container page-breadcrumb-inner">
            <nav class="flex items-center gap-2 text-sm flex-wrap">
                <a href="<?php echo base_url(); ?>">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-dark font-medium"><?php echo isset($search_keyword) ? 'Search' : 'Shop'; ?></span>
            </nav>
        </div>
    </div>

    <div class="page-container shop-shell">
        <div class="shop-header">
            <div class="shop-header-copy">
                <h1 class="shop-title"><?php echo $shop_heading; ?></h1>
                <p class="shop-subtitle"><?php echo $shop_subheading; ?></p>
                <p class="shop-meta">
                    <?php if ($total_products > 0): ?>
                        Showing <?php echo $showing_from; ?>–<?php echo $showing_to; ?> of <?php echo $total_products; ?> products
                    <?php else: ?>
                        No products found
                    <?php endif; ?>
                </p>
            </div>

            <?php if ($total_products > 0): ?>
            <div class="shop-sort">
                <label for="sortSelect" class="shop-sort-label">Sort by</label>
                <div class="shop-sort-select-wrap">
                    <select id="sortSelect" class="shop-sort-select">
                        <option value="newest">Newest First</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="name-az">Name: A to Z</option>
                        <option value="name-za">Name: Z to A</option>
                    </select>
                    <i class="fa-solid fa-chevron-down shop-sort-chevron"></i>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <?php if (!empty($all_categories)): ?>
        <div class="shop-filters">
            <p class="shop-filter-label">Categories</p>
            <div class="shop-filter-list">
                <a href="<?php echo base_url('shop'); ?>" class="shop-filter-chip <?php echo !isset($search_keyword) ? 'is-active' : ''; ?>">
                    All Products
                </a>
                <?php foreach ($all_categories as $cat): ?>
                <a href="<?php echo category_url($cat); ?>" class="shop-filter-chip">
                    <?php echo htmlspecialchars($cat->category_name); ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="shop-filters shop-filters--scroll">
            <p class="shop-filter-label">Sticker collections</p>
            <div class="shop-filter-list shop-filter-list--scroll">
                <?php foreach ($sticker_filters as $filter): ?>
                <a href="<?php echo base_url('shop/search?q=' . urlencode($filter['query'])); ?>" class="shop-filter-chip shop-filter-chip--outline">
                    <?php echo $filter['label']; ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="shop-products">
            <?php if (!empty($products)): ?>
            <div class="shop-grid">
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
                ?>
                <div class="shop-product-card product-listing-card product-card group">
                    <div class="relative">
                        <a href="<?php echo product_url($product); ?>" class="block">
                            <div class="shop-product-image">
                                <img src="<?php echo $image_url; ?>" alt="<?php echo htmlspecialchars($product->product_name); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                <?php if ($discount > 0): ?>
                                <span class="shop-product-badge"><?php echo $discount; ?>% OFF</span>
                                <?php endif; ?>
                            </div>
                        </a>
                        <a href="<?php echo base_url('wishlist'); ?>" class="shop-product-wishlist" aria-label="Wishlist">
                            <i class="fa-regular fa-heart"></i>
                        </a>
                    </div>

                    <div class="shop-product-body">
                        <a href="<?php echo product_url($product); ?>">
                            <h3 class="shop-product-name"><?php echo htmlspecialchars($product->product_name); ?></h3>
                        </a>

                        <div class="shop-product-price">
                            <span class="shop-product-price-current">₹<?php echo number_format($min_price, 0); ?></span>
                            <?php if ($has_variants): ?><span class="shop-product-price-note">onwards</span><?php endif; ?>
                            <?php if ($discount > 0): ?>
                            <span class="shop-product-price-mrp">MRP ₹<?php echo number_format($min_mrp, 0); ?></span>
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
            <div class="shop-pagination">
                <?php
                $base_url = isset($search_keyword) ? base_url('shop/search') : base_url('shop');
                $separator = '?';
                $extra_params = isset($search_keyword) ? 'q=' . urlencode($search_keyword) . '&' : '';
                ?>

                <?php if ($current_page > 1): ?>
                <a href="<?php echo $base_url . $separator . $extra_params . 'page=' . ($current_page - 1); ?>" class="shop-page-btn" aria-label="Previous page">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </a>
                <?php else: ?>
                <span class="shop-page-btn is-disabled" aria-hidden="true">
                    <i class="fa-solid fa-chevron-left text-sm"></i>
                </span>
                <?php endif; ?>

                <?php
                $range = 2;
                $start_page = max(1, $current_page - $range);
                $end_page = min($total_pages, $current_page + $range);
                ?>

                <?php if ($start_page > 1): ?>
                <a href="<?php echo $base_url . $separator . $extra_params . 'page=1'; ?>" class="shop-page-btn">1</a>
                <?php if ($start_page > 2): ?><span class="shop-page-ellipsis">...</span><?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                <?php if ($i == $current_page): ?>
                <span class="shop-page-btn is-active"><?php echo $i; ?></span>
                <?php else: ?>
                <a href="<?php echo $base_url . $separator . $extra_params . 'page=' . $i; ?>" class="shop-page-btn"><?php echo $i; ?></a>
                <?php endif; ?>
                <?php endfor; ?>

                <?php if ($end_page < $total_pages): ?>
                <?php if ($end_page < $total_pages - 1): ?><span class="shop-page-ellipsis">...</span><?php endif; ?>
                <a href="<?php echo $base_url . $separator . $extra_params . 'page=' . $total_pages; ?>" class="shop-page-btn"><?php echo $total_pages; ?></a>
                <?php endif; ?>

                <?php if ($current_page < $total_pages): ?>
                <a href="<?php echo $base_url . $separator . $extra_params . 'page=' . ($current_page + 1); ?>" class="shop-page-btn" aria-label="Next page">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </a>
                <?php else: ?>
                <span class="shop-page-btn is-disabled" aria-hidden="true">
                    <i class="fa-solid fa-chevron-right text-sm"></i>
                </span>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php else: ?>
            <div class="shop-empty">
                <div class="shop-empty-icon"><i class="fa-solid fa-box-open"></i></div>
                <h3 class="shop-empty-title"><?php echo isset($search_keyword) ? 'No results found' : 'No products yet'; ?></h3>
                <?php if (isset($search_keyword)): ?>
                <p class="shop-empty-text">We couldn't find anything for "<?php echo htmlspecialchars($search_keyword); ?>". Try another search.</p>
                <a href="<?php echo base_url('shop'); ?>" class="btn-primary inline-flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i>
                    Browse All Products
                </a>
                <?php else: ?>
                <p class="shop-empty-text">New products are on the way. Check back soon.</p>
                <a href="<?php echo base_url(); ?>" class="btn-primary inline-flex items-center gap-2">
                    <i class="fa-solid fa-home"></i>
                    Back to Home
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
