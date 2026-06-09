<?php
$about_image = brand_image_url('hero1.jpeg');
if (!$about_image) {
    $about_image = brand_image_url('hero.png');
}
if (!$about_image) {
    $about_image = base_url('user_assets/images/about-hero.png');
}
?>
<div class="site-page about-page">
    <div class="page-breadcrumb">
        <div class="page-container page-breadcrumb-inner">
            <nav class="flex items-center gap-2 text-sm flex-wrap">
                <a href="<?php echo base_url(); ?>">Home</a>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                <span class="text-dark font-medium">About Us</span>
            </nav>
        </div>
    </div>

    <section class="about-hero">
        <div class="page-container">
            <div class="about-hero-grid">
                <div class="about-hero-copy">
                    <span class="about-eyebrow">About PIKO POP</span>
                    <h1 class="about-hero-title">Quality kids products, thoughtfully curated</h1>
                    <p class="about-hero-text">
                        PIKO POP brings stickers, stationery, toys, and gifts for children — selected for quality, safety, and everyday joy.
                    </p>
                </div>
                <div class="about-hero-visual">
                    <img src="<?php echo $about_image; ?>" alt="PIKO POP — About us" class="about-hero-image">
                </div>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="page-container">
            <div class="about-split">
                <div>
                    <span class="about-eyebrow">Our story</span>
                    <h2 class="about-section-title">Where quality meets play</h2>
                    <div class="about-prose">
                        <p>
                            PIKO POP started with a simple idea: small, well-made products can make a big difference in a child's day — a sticker on a notebook, a colorful pen, or a toy that becomes a favorite companion.
                        </p>
                        <p>
                            We curate items that feel premium and parent-friendly: safe materials, thoughtful design, and products kids genuinely enjoy using.
                        </p>
                        <p class="about-prose-highlight">
                            Every collection is chosen to inspire creativity and spread happiness — one thoughtful pick at a time.
                        </p>
                    </div>
                </div>
                <div class="about-stats">
                    <div class="about-stat-card">
                        <span class="about-stat-icon"><i class="fa-solid fa-medal"></i></span>
                        <h3 class="about-stat-title">Curated quality</h3>
                        <p class="about-stat-text">Products selected for durability, design, and kid appeal.</p>
                    </div>
                    <div class="about-stat-card">
                        <span class="about-stat-icon"><i class="fa-solid fa-shield-check"></i></span>
                        <h3 class="about-stat-title">Parent trusted</h3>
                        <p class="about-stat-text">Safe, reliable picks you can feel good about gifting.</p>
                    </div>
                    <div class="about-stat-card">
                        <span class="about-stat-icon"><i class="fa-solid fa-truck-fast"></i></span>
                        <h3 class="about-stat-title">Fast delivery</h3>
                        <p class="about-stat-text">Secure packaging and shipping across India.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section about-section--muted">
        <div class="page-container">
            <div class="section-header">
                <h2 class="home-section-title">What we offer</h2>
                <p class="section-subtitle">Shop by collection for every age and occasion</p>
            </div>
            <div class="about-collections">
                <div class="about-collection-card">
                    <span class="about-collection-icon"><i class="fa-solid fa-tags"></i></span>
                    <h3>Stickers</h3>
                </div>
                <div class="about-collection-card">
                    <span class="about-collection-icon"><i class="fa-solid fa-pen-nib"></i></span>
                    <h3>Stationery</h3>
                </div>
                <div class="about-collection-card">
                    <span class="about-collection-icon"><i class="fa-solid fa-puzzle-piece"></i></span>
                    <h3>Toys</h3>
                </div>
                <div class="about-collection-card">
                    <span class="about-collection-icon"><i class="fa-solid fa-gift"></i></span>
                    <h3>Gifts</h3>
                </div>
                <div class="about-collection-card">
                    <span class="about-collection-icon"><i class="fa-solid fa-palette"></i></span>
                    <h3>Creative kits</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="page-container">
            <div class="section-header">
                <h2 class="home-section-title">Why shop with us</h2>
                <p class="section-subtitle">Everything you need for a smooth shopping experience</p>
            </div>
            <div class="why-piko-grid">
                <div class="why-piko-card">
                    <div class="why-piko-icon-wrap why-piko-icon-wrap--pink"><i class="fa-solid fa-medal"></i></div>
                    <h4 class="why-piko-title">Quality Products</h4>
                    <p class="why-piko-text">Carefully selected for kids</p>
                </div>
                <div class="why-piko-card">
                    <div class="why-piko-icon-wrap why-piko-icon-wrap--yellow"><i class="fa-solid fa-truck-fast"></i></div>
                    <h4 class="why-piko-title">Fast Shipping</h4>
                    <p class="why-piko-text">Quick delivery across India</p>
                </div>
                <div class="why-piko-card">
                    <div class="why-piko-icon-wrap why-piko-icon-wrap--blue"><i class="fa-solid fa-lock"></i></div>
                    <h4 class="why-piko-title">Secure Checkout</h4>
                    <p class="why-piko-text">Safe &amp; encrypted payments</p>
                </div>
                <div class="why-piko-card">
                    <div class="why-piko-icon-wrap why-piko-icon-wrap--purple"><i class="fa-solid fa-headset"></i></div>
                    <h4 class="why-piko-title">Customer Support</h4>
                    <p class="why-piko-text">We are here to help</p>
                </div>
            </div>
        </div>
    </section>

    <section class="about-cta">
        <div class="page-container">
            <div class="about-cta-inner">
                <h2 class="about-cta-title">Ready to explore our store?</h2>
                <p class="about-cta-text">Browse stickers, stationery, toys and more — delivered to your door.</p>
                <div class="about-cta-actions">
                    <a href="<?php echo base_url('shop'); ?>" class="btn-primary inline-flex items-center justify-center gap-2 px-8 py-3.5">
                        Shop Now
                    </a>
                    <a href="<?php echo base_url('page/contact'); ?>" class="btn-outline inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-white">
                        Contact Us
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
