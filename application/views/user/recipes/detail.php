<?php
$thumb = recipe_thumbnail_url($recipe);
$total_time = intval($recipe->prep_time_minutes) + intval($recipe->cook_time_minutes);
$ingredients_list = !empty($recipe->ingredients) ? array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $recipe->ingredients))) : array();
?>
<main class="min-h-screen bg-white">
    <!-- Breadcrumb -->
    <div class="bg-light-gray border-b border-gray-200">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <a href="<?php echo base_url('recipes'); ?>" class="text-gray hover:text-primary transition-colors">Ideas &amp; Inspiration</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium line-clamp-1"><?php echo htmlspecialchars($recipe->title); ?></span>
            </nav>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 md:py-10">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">

            <!-- Left: Video + Description + Ingredients -->
            <div class="lg:col-span-2 space-y-8">
                <div>
                    <h1 class="text-2xl md:text-4xl font-bold text-dark mb-3"><?php echo htmlspecialchars($recipe->title); ?></h1>
                    <div class="flex flex-wrap items-center gap-3 mb-6">
                        <?php if ($recipe->prep_time_minutes > 0): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-sm font-medium rounded-full">
                                <i class="fa-regular fa-clock"></i> Prep: <?php echo intval($recipe->prep_time_minutes); ?>m
                            </span>
                        <?php endif; ?>
                        <?php if ($recipe->cook_time_minutes > 0): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 text-primary text-sm font-medium rounded-full">
                                <i class="fa-solid fa-wand-magic-sparkles"></i> Activity: <?php echo intval($recipe->cook_time_minutes); ?>m
                            </span>
                        <?php endif; ?>
                        <?php if ($recipe->servings > 0): ?>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-secondary/10 text-secondary text-sm font-medium rounded-full">
                                <i class="fa-solid fa-sparkles"></i> <?php echo intval($recipe->servings); ?> ideas
                            </span>
                        <?php endif; ?>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-light-gray text-dark text-sm font-medium rounded-full">
                            <i class="fa-solid fa-signal"></i> <?php echo ucfirst($recipe->difficulty); ?>
                        </span>
                    </div>
                </div>

                <!-- Video Player Card -->
                <div class="bg-black rounded-2xl overflow-hidden aspect-video relative" id="videoCard"
                     data-video-id="<?php echo htmlspecialchars($recipe->youtube_video_id); ?>">
                    <!-- Poster + play button (shown until play is tapped) -->
                    <div id="videoPoster" class="absolute inset-0 cursor-pointer group">
                        <img src="<?php echo $thumb; ?>" alt="<?php echo htmlspecialchars($recipe->title); ?>" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/30 flex items-center justify-center group-hover:bg-black/40 transition-colors">
                            <div class="w-20 h-20 md:w-24 md:h-24 rounded-full bg-white flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform">
                                <i class="fa-solid fa-play text-primary text-2xl md:text-3xl ml-1"></i>
                            </div>
                        </div>
                    </div>
                    <!-- Embed iframe is injected on click -->
                </div>

                <?php if (!empty($recipe->description)): ?>
                    <div class="prose max-w-none">
                        <h2 class="text-xl font-bold text-dark mb-3">About this idea</h2>
                        <p class="text-gray leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($recipe->description); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (!empty($ingredients_list)): ?>
                    <div>
                        <h2 class="text-xl font-bold text-dark mb-4">What You'll Need</h2>
                        <ul class="space-y-2">
                            <?php foreach ($ingredients_list as $ing): ?>
                                <li class="flex items-start gap-3">
                                    <span class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <i class="fa-solid fa-check text-primary text-xs"></i>
                                    </span>
                                    <span class="text-dark"><?php echo htmlspecialchars($ing); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right: Featured Products / Quick Shop -->
            <aside class="lg:col-span-1">
                <div class="sticky top-24 space-y-4">
                    <?php if (!empty($products)): ?>
                        <div class="bg-light-gray rounded-2xl p-5">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg font-bold text-dark">Shop Featured Products</h2>
                                <span class="text-xs text-gray"><?php echo count($products); ?> items</span>
                            </div>

                            <button id="addAllBtn" class="w-full bg-primary hover:bg-primary-dark text-white py-3 rounded-full font-semibold mb-4 flex items-center justify-center gap-2 transition-all">
                                <i class="fa-solid fa-cart-plus"></i>
                                Add All to Cart
                            </button>

                            <div class="space-y-3" id="recipeProducts">
                                <?php foreach ($products as $p): ?>
                                    <?php
                                    $first_image = !empty($p->images_array) ? $p->images_array[0] : '';
                                    $img = !empty($first_image) ? base_url('uploads/products/' . $first_image) : base_url('user_assets/images/product-fallback.png');
                                    $has_variants = !empty($p->variants) && count($p->variants) > 0;
                                    $price = $has_variants ? $p->variants[0]->sale_price : $p->sale_price;
                                    $variant_id = $has_variants ? $p->variants[0]->id : 0;
                                    $variant_label = $has_variants ? $p->variants[0]->variant_name : '';
                                    ?>
                                    <div class="flex items-center gap-3 bg-white rounded-xl p-3">
                                        <a href="<?php echo product_url($p); ?>" class="flex-shrink-0">
                                            <img src="<?php echo $img; ?>" alt="" class="w-16 h-16 rounded-lg object-cover">
                                        </a>
                                        <div class="flex-1 min-w-0">
                                            <a href="<?php echo product_url($p); ?>" class="block">
                                                <h4 class="font-semibold text-dark text-sm line-clamp-1 hover:text-primary transition-colors"><?php echo htmlspecialchars($p->product_name); ?></h4>
                                            </a>
                                            <?php if ($has_variants): ?>
                                                <p class="text-xs text-gray mt-0.5"><?php echo htmlspecialchars($variant_label); ?></p>
                                            <?php endif; ?>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span class="text-primary font-bold text-sm">Rs. <?php echo number_format($price, 2); ?></span>
                                                <span class="text-xs text-gray">× <?php echo intval($p->default_quantity); ?></span>
                                            </div>
                                        </div>
                                        <button class="recipe-add-btn flex-shrink-0 w-9 h-9 rounded-full bg-primary/10 hover:bg-primary hover:text-white text-primary flex items-center justify-center transition-all"
                                                data-product-id="<?php echo $p->id; ?>"
                                                data-variant-id="<?php echo intval($variant_id); ?>"
                                                data-qty="<?php echo intval($p->default_quantity); ?>"
                                                title="Add to cart">
                                            <i class="fa-solid fa-plus text-sm"></i>
                                        </button>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <a href="<?php echo base_url('recipes'); ?>" class="block text-center text-primary font-medium hover:underline">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Back to Ideas &amp; Inspiration
                    </a>
                </div>
            </aside>
        </div>

        <?php if (!empty($related)): ?>
            <section class="mt-16 pt-10 border-t border-gray-100">
                <h2 class="text-2xl font-bold text-dark mb-6">More Inspiration</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                    <?php foreach ($related as $r): ?>
                        <a href="<?php echo recipe_url($r); ?>" class="group block">
                            <div class="aspect-video bg-gray-100 rounded-xl overflow-hidden mb-3 relative">
                                <img src="<?php echo recipe_thumbnail_url($r); ?>" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div class="w-12 h-12 rounded-full bg-white/95 flex items-center justify-center">
                                        <i class="fa-solid fa-play text-primary ml-0.5"></i>
                                    </div>
                                </div>
                            </div>
                            <h3 class="font-semibold text-dark text-sm line-clamp-2 group-hover:text-primary transition-colors"><?php echo htmlspecialchars($r->title); ?></h3>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</main>
