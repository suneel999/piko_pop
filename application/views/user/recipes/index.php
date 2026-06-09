<div class="site-page">
    <!-- Breadcrumb -->
    <div class="bg-white/80 border-b border-primary/10">
        <div class="container mx-auto px-4 py-3">
            <nav class="flex items-center gap-2 text-sm">
                <a href="<?php echo base_url(); ?>" class="text-gray hover:text-primary transition-colors">Home</a>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
                <span class="text-dark font-medium">Ideas &amp; Inspiration</span>
            </nav>
        </div>
    </div>

    <!-- Page Header -->
    <div class="container mx-auto px-4 py-6 md:py-10">
        <div class="text-center max-w-2xl mx-auto mb-8">
            <span class="inline-block px-4 py-1.5 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">Get Creative ✨</span>
            <h1 class="text-3xl md:text-4xl font-bold text-dark mb-3">Ideas &amp; Inspiration</h1>
            <p class="text-gray">Sticker ideas, DIY crafts, notebook decorating, creative play — watch, get inspired, and shop the cute picks in one click.</p>
        </div>

        <?php if (!empty($recipes)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($recipes as $recipe): ?>
                    <?php
                    $thumb = recipe_thumbnail_url($recipe);
                    $total_time = intval($recipe->prep_time_minutes) + intval($recipe->cook_time_minutes);
                    ?>
                    <a href="<?php echo recipe_url($recipe); ?>" class="group block bg-white rounded-3xl shadow-md shadow-primary/5 overflow-hidden hover:shadow-lg transition-all border border-primary/5">
                        <div class="aspect-video bg-light-gray overflow-hidden relative">
                            <img src="<?php echo $thumb; ?>" alt="<?php echo htmlspecialchars($recipe->title); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <!-- Play overlay -->
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                <div class="w-16 h-16 rounded-full bg-white/95 flex items-center justify-center shadow-lg">
                                    <i class="fa-solid fa-play text-primary text-xl ml-1"></i>
                                </div>
                            </div>
                            <?php if ($total_time > 0): ?>
                                <span class="absolute top-3 right-3 bg-primary text-white text-xs font-semibold px-2.5 py-1 rounded-full">
                                    <i class="fa-regular fa-clock mr-1"></i><?php echo $total_time; ?> min
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-dark text-lg mb-2 line-clamp-2 group-hover:text-primary transition-colors">
                                <?php echo htmlspecialchars($recipe->title); ?>
                            </h3>
                            <?php if (!empty($recipe->description)): ?>
                                <p class="text-gray text-sm line-clamp-2 mb-3"><?php echo htmlspecialchars($recipe->description); ?></p>
                            <?php endif; ?>
                            <div class="flex items-center gap-3 text-xs text-gray">
                                <?php if ($recipe->servings > 0): ?>
                                    <span><i class="fa-solid fa-sparkles text-primary mr-1"></i><?php echo intval($recipe->servings); ?> ideas</span>
                                <?php endif; ?>
                                <?php
                                $dcolors = array('easy' => 'text-secondary', 'medium' => 'text-yellow-600', 'hard' => 'text-red-500');
                                $dc = isset($dcolors[$recipe->difficulty]) ? $dcolors[$recipe->difficulty] : 'text-gray';
                                ?>
                                <span class="<?php echo $dc; ?> font-medium"><i class="fa-solid fa-signal mr-1"></i><?php echo ucfirst($recipe->difficulty); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16 bg-white rounded-3xl border border-primary/5">
                <span class="text-6xl block mb-4">✨</span>
                <p class="text-gray">No inspiration posts yet. Check back soon for sticker ideas &amp; DIY fun!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
