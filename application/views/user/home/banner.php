<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-section-bg" aria-hidden="true"></div>

    <div class="page-container hero-inner">
        <div class="hero-grid">
            <div class="hero-copy">
                <span class="hero-welcome-badge">PIKO POP Store</span>
                <h1 class="hero-title">
                    Quality Kids Products,<br>
                    Delivered to Your Door
                </h1>
                <p class="hero-subtitle">
                    Stickers, stationery, toys and gifts — curated for children, trusted by parents.
                </p>
                <div class="hero-actions">
                    <a href="<?php echo base_url('shop'); ?>" class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-3.5">
                        Shop Now
                    </a>
                    <a href="<?php echo base_url('shop/search?q=gifts'); ?>" class="btn-outline inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white">
                        View Gifts
                    </a>
                </div>
                <ul class="hero-trust-list">
                    <li><i class="fa-solid fa-truck-fast text-gray-500"></i> Fast delivery</li>
                    <li><i class="fa-solid fa-shield-check text-gray-500"></i> Secure payments</li>
                    <li><i class="fa-solid fa-rotate-left text-gray-500"></i> Easy returns</li>
                </ul>
            </div>

            <div class="hero-visual">
                <?php
                $hero_image = brand_image_url('hero1.jpeg');
                if (!$hero_image) {
                    $hero_showcase = hero_showcase_images();
                    $hero_image = !empty($hero_showcase[0]) ? $hero_showcase[0] : brand_image_url('hero.png');
                }
                ?>

                <?php if ($hero_image): ?>
                    <div class="hero-feature-panel">
                        <img src="<?php echo $hero_image; ?>" alt="PIKO POP products" class="hero-feature-image" loading="eager" decoding="async">
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
                    <div class="hero-image-panel">
                        <img src="<?php echo base_url('user_assets/images/product-fallback.png'); ?>" alt="PIKO POP" class="hero-image" loading="eager">
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
