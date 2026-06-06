<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-section-bg" aria-hidden="true"></div>

    <div class="page-container hero-inner">
        <div class="hero-grid">
            <!-- Copy -->
            <div class="hero-copy">
                <span class="hero-welcome-badge">Welcome to PIKO POP</span>
                <h1 class="hero-title">
                    Where Little Things<br>
                    Create <span class="text-primary">Big Smiles</span>
                </h1>
                <p class="hero-subtitle">
                    Discover magical stickers, cute stationery, toys &amp; collectibles kids love.
                </p>
                <div class="hero-actions">
                    <a href="<?php echo base_url('shop'); ?>" class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-3.5">
                        <i class="fa-solid fa-bag-shopping"></i> Shop Collection
                    </a>
                    <a href="<?php echo base_url('shop/search?q=gifts'); ?>" class="btn-outline inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white">
                        <i class="fa-solid fa-gift"></i> Explore Gifts
                    </a>
                </div>
                <ul class="hero-trust-list">
                    <li><i class="fa-solid fa-star text-accent-yellow"></i> Kid Loved</li>
                    <li><i class="fa-solid fa-palette text-accent-blue"></i> Colorful Designs</li>
                    <li><i class="fa-solid fa-gift text-primary"></i> Gift Ready</li>
                </ul>
            </div>

            <!-- Visual -->
            <div class="hero-visual">
                <?php $hero_image_url = brand_image_url('hero.png'); ?>
                <?php if ($hero_image_url): ?>
                    <div class="hero-image-panel">
                        <img src="<?php echo $hero_image_url; ?>" alt="PIKO POP — stickers, toys and gifts for kids" class="hero-image" width="640" height="640" loading="eager">
                    </div>
                <?php elseif (!empty($banners) && count($banners) > 0): ?>
                    <div id="bannerSlider" class="hero-banner-slider">
                        <div class="slider-wrapper flex transition-transform duration-500 ease-in-out">
                            <?php foreach ($banners as $banner): ?>
                                <div class="slide flex-shrink-0 w-full">
                                    <a href="<?php echo !empty($banner->target_link) ? $banner->target_link : 'javascript:void(0)'; ?>" class="block">
                                        <picture>
                                            <source media="(min-width: 768px)" srcset="<?php echo base_url('uploads/banners/' . $banner->desktop_image); ?>">
                                            <img src="<?php echo base_url('uploads/banners/' . $banner->mobile_image); ?>" alt="<?php echo htmlspecialchars($banner->title); ?>" class="w-full h-full object-cover" loading="eager">
                                        </picture>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($banners) > 1): ?>
                            <button id="prevSlide" type="button" class="hero-slider-btn hero-slider-btn-prev" aria-label="Previous slide">
                                <i class="fa-solid fa-chevron-left"></i>
                            </button>
                            <button id="nextSlide" type="button" class="hero-slider-btn hero-slider-btn-next" aria-label="Next slide">
                                <i class="fa-solid fa-chevron-right"></i>
                            </button>
                            <div class="hero-slider-dots">
                                <?php foreach ($banners as $index => $banner): ?>
                                    <button type="button" class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>" aria-label="Slide <?php echo $index + 1; ?>"></button>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <div class="hero-showcase-panel">
                        <div class="hero-showcase-grid">
                            <article class="hero-mini-card hero-mini-card--pink">
                                <span class="hero-mini-card-label">ABC Stickers</span>
                                <div class="hero-mini-card-art" aria-hidden="true"></div>
                            </article>
                            <article class="hero-mini-card hero-mini-card--yellow">
                                <span class="hero-mini-card-label">Animal Stickers</span>
                                <div class="hero-mini-card-art" aria-hidden="true"></div>
                            </article>
                            <article class="hero-mini-card hero-mini-card--blue">
                                <span class="hero-mini-card-label">Cute Toys</span>
                                <div class="hero-mini-card-art" aria-hidden="true"></div>
                            </article>
                            <article class="hero-mini-card hero-mini-card--purple">
                                <span class="hero-mini-card-label">Stationery</span>
                                <div class="hero-mini-card-art" aria-hidden="true"></div>
                            </article>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
