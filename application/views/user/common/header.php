<?php
// Get dynamic data for header
$CI = &get_instance();
$CI->load->model('Common_model', 'common');

// Get all parent categories for Shop dropdown
if (!isset($header_categories)) {
    $header_categories = $CI->common->getdatabytableall('categories', array('parent_category_id' => 0, 'status' => 1), 'sort_order', 'ASC');
}

// Get user login status and data
$is_logged_in = is_loggedin_user();
$cart_count = get_cart_count();
$wishlist_count = get_wishlist_count();

if ($is_logged_in) {
    $user_name = get_user_sessiondata('fullname');
    $user_email = get_user_sessiondata('email');
    $user_initials = strtoupper(substr($user_name, 0, 1) . (strpos($user_name, ' ') !== false ? substr($user_name, strpos($user_name, ' ') + 1, 1) : ''));
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>PIKO POP | Kids Store Online</title>
    <meta name="description" content="<?php echo isset($meta_description) ? htmlspecialchars($meta_description) : 'Shop stickers, stationery, toys and gifts at PIKO POP. Quality products for kids with fast delivery across India.'; ?>">
    <link rel="icon" type="image/png" href="<?php echo base_url('user_assets/images/piko-pop-favicon.png'); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">


    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/fontawesome.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/sharp-solid.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/sharp-regular.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/sharp-light.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/duotone.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/solid.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/regular.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/light.css" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/rizmyabdulla/fontawesome-pro@main/releases/v7.2.0/css/brands.css" />

    <!-- Tailwind CSS -->
    <link href="<?php echo base_url('user_assets/') ?>css/output.css?<?php echo time(); ?>" rel="stylesheet">


    <style>
        .choose-options-btn {
            -webkit-appearance: button;
            /* iOS Safari */
            -moz-appearance: button;
            /* Firefox */
            appearance: button;
        }

        .categories-scrollbar {
            justify-content: center;
            gap: calc(var(--spacing) * 8);
        }


        .p-8 {
            padding: calc(var(--spacing) * 8);
        }
    </style>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-CC0VGT5DKS"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-CC0VGT5DKS');
    </script>
    <script>
        const BASE_URL = '<?php echo base_url(); ?>';
    </script>
</head>

<body class="bg-white <?php echo (isset($page_title) && $page_title === 'Home') ? 'home-page' : ''; ?>">

    <!-- Announcement Bar -->
    <div class="announcement-bar">
        <div class="page-container py-2 sm:py-2.5 text-center text-[11px] sm:text-sm font-medium leading-snug">
            <span class="sm:hidden">Free shipping on orders ₹499+ · Secure checkout</span>
            <span class="hidden sm:inline">Free shipping on orders above ₹499 &nbsp;·&nbsp; Easy returns &nbsp;·&nbsp; Secure checkout</span>
        </div>
    </div>

    <!-- Header -->
    <header class="site-header site-header-premium">
        <div class="page-container">
            <!-- Main header row -->
            <div class="site-header-top">
                <button id="menuToggle" class="site-header-menu lg:hidden" aria-label="Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <a href="<?php echo base_url(); ?>" class="site-header-logo-link">
                    <img src="<?php echo brand_logo_url(); ?>" alt="PIKO POP" class="site-header-logo">
                </a>

                <!-- Desktop search -->
                <form action="<?php echo base_url('shop/search'); ?>" method="get" class="site-header-search hidden md:flex">
                    <div class="relative w-full">
                        <input type="text" name="q" placeholder="Search products, categories..." class="header-search-input w-full pl-5 pr-14 py-3.5 rounded-lg border border-gray-300 bg-white text-dark placeholder:text-gray/70 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/15 transition-all">
                        <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-10 h-10 rounded-lg bg-primary text-white flex items-center justify-center hover:bg-primary-dark transition-colors" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass text-sm"></i>
                        </button>
                    </div>
                </form>

                <div class="site-header-actions">
                    <button id="searchToggle" class="site-header-icon-btn md:hidden" aria-label="Search">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <a href="<?php echo base_url('wishlist'); ?>" class="header-action-pill hidden sm:flex" aria-label="Wishlist">
                        <span class="header-action-icon"><i class="fa-regular fa-heart"></i></span>
                        <span class="header-action-label">Wishlist</span>
                        <span class="wishlist-count header-badge <?php echo $wishlist_count == 0 ? 'hidden' : ''; ?>"><?php echo $wishlist_count; ?></span>
                    </a>

                    <?php if ($is_logged_in): ?>
                        <a href="<?php echo base_url('account'); ?>" class="header-action-pill hidden sm:flex" aria-label="Account">
                            <span class="header-action-icon"><i class="fa-regular fa-user"></i></span>
                            <span class="header-action-label">Account</span>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo base_url('login'); ?>" class="header-action-pill hidden sm:flex" aria-label="Account">
                            <span class="header-action-icon"><i class="fa-regular fa-user"></i></span>
                            <span class="header-action-label">Account</span>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo base_url('cart'); ?>" class="header-action-pill header-action-pill--cart" aria-label="Cart">
                        <span class="header-action-icon"><i class="fa-solid fa-bag-shopping"></i></span>
                        <span class="header-action-label hidden sm:inline">Cart</span>
                        <span class="cart-count header-badge <?php echo $cart_count == 0 ? 'hidden' : ''; ?>"><?php echo $cart_count; ?></span>
                    </a>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="site-header-nav hidden lg:flex">
                <div class="relative group/shop">
                    <a href="<?php echo base_url('shop'); ?>" class="header-nav-link flex items-center gap-1">
                        Shop <i class="fa-solid fa-chevron-down text-[10px] opacity-60"></i>
                    </a>
                    <div class="header-nav-dropdown">
                        <div class="py-2">
                            <?php if (!empty($header_categories)): ?>
                                <?php foreach ($header_categories as $category): ?>
                                    <a href="<?php echo category_url($category); ?>" class="header-nav-dropdown-item">
                                        <?php if (!empty($category->icon)): ?>
                                            <img src="<?php echo base_url('uploads/categories/' . $category->icon); ?>" alt="" class="w-8 h-8 rounded-xl object-cover">
                                        <?php else: ?>
                                            <span class="w-8 h-8 rounded-xl bg-primary/15 flex items-center justify-center text-primary"><i class="fa-solid fa-star text-sm"></i></span>
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($category->category_name); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <a href="<?php echo base_url('shop'); ?>" class="header-nav-dropdown-item text-primary font-bold">
                                <span class="w-8 h-8 rounded-xl bg-primary/15 flex items-center justify-center"><i class="fa-solid fa-grid-2"></i></span>
                                View All Products
                            </a>
                        </div>
                    </div>
                </div>

                <div class="relative group/stickers">
                    <a href="<?php echo base_url('shop/search?q=stickers'); ?>" class="header-nav-link flex items-center gap-1">
                        Stickers <i class="fa-solid fa-chevron-down text-[10px] opacity-60"></i>
                    </a>
                    <div class="header-nav-dropdown">
                        <div class="py-2">
                            <?php
                            $sticker_links = array('ABCD Stickers', 'Animal Stickers', 'Space Stickers', 'Cartoon Stickers', 'Emoji Stickers');
                            foreach ($sticker_links as $sl): ?>
                                <a href="<?php echo base_url('shop/search?q=' . urlencode($sl)); ?>" class="header-nav-dropdown-item">
                                    <span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600"><i class="fa-solid fa-tag text-sm"></i></span>
                                    <span><?php echo $sl; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <a href="<?php echo base_url('shop'); ?>" class="header-nav-link">
                    New Arrivals <span class="nav-new-badge">NEW</span>
                </a>
                <a href="<?php echo base_url('shop'); ?>" class="header-nav-link">Best Sellers</a>

                <div class="relative group/gifts">
                    <a href="<?php echo base_url('shop/search?q=gifts'); ?>" class="header-nav-link flex items-center gap-1">
                        Gifts <i class="fa-solid fa-chevron-down text-[10px] opacity-60"></i>
                    </a>
                    <div class="header-nav-dropdown">
                        <div class="py-2">
                            <a href="<?php echo base_url('shop/search?q=gift'); ?>" class="header-nav-dropdown-item"><span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600"><i class="fa-solid fa-gift text-sm"></i></span> Gift Sets</a>
                            <a href="<?php echo base_url('page/bulk_order'); ?>" class="header-nav-dropdown-item"><span class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-gray-600"><i class="fa-solid fa-boxes-stacked text-sm"></i></span> Party &amp; Bulk Orders</a>
                        </div>
                    </div>
                </div>

                <a href="<?php echo base_url('page/about'); ?>" class="header-nav-link">About Us</a>
            </nav>
        </div>

        <!-- Mobile Search Bar -->
        <div id="searchBar" class="hidden border-t border-gray-200 bg-white md:hidden">
            <div class="page-container py-3">
                <form action="<?php echo base_url('shop/search'); ?>" method="get" class="flex items-center gap-2">
                    <input type="text" name="q" placeholder="Search products..." class="header-search-input flex-1 px-4 py-3 rounded-lg border border-gray-300 bg-white focus:outline-none focus:border-primary">
                    <button type="submit" class="btn-primary px-5 py-3 text-sm">Go</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Mobile Sidebar Navigation -->
    <div id="navOverlay" class="nav-overlay"></div>
    <aside id="mobileSidebar" class="mobile-sidebar">
        <?php if ($is_logged_in): ?>
            <!-- Sidebar Header with User Info (Logged In) -->
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between mb-4">
                    <img src="<?php echo brand_logo_url(); ?>" alt="PIKO POP" class="site-header-logo site-header-logo--sm">
                    <button id="sidebarClose" class="text-2xl text-gray-600 hover:text-primary transition-colors">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
                <!-- User Profile Card -->
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-primary/20 flex items-center justify-center">
                        <span class="text-primary font-bold"><?php echo $user_initials; ?></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-dark truncate"><?php echo htmlspecialchars($user_name); ?></p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Sidebar Header (Guest) -->
            <div class="flex items-center justify-between p-4 border-b border-primary/10">
                <img src="<?php echo brand_logo_url(); ?>" alt="PIKO POP" class="site-header-logo site-header-logo--sm">
                <button id="sidebarClose" class="text-2xl text-gray-600 hover:text-primary transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        <?php endif; ?>

        <!-- Sidebar Navigation -->
        <nav class="flex flex-col py-4 overflow-y-auto <?php echo $is_logged_in ? 'max-h-[calc(100vh-280px)]' : 'max-h-[calc(100vh-200px)]'; ?>">
            <?php if ($is_logged_in): ?>
                <!-- Quick Links (Logged In) -->
                <div class="px-6 py-2">
                    <p class="text-xs font-semibold text-gray uppercase tracking-wider">Quick Links</p>
                </div>
                <a href="<?php echo base_url('account/orders'); ?>" class="flex items-center gap-3 px-6 py-3 text-dark font-medium hover:bg-gray-50 hover:text-primary transition-all">
                    <i class="fa-solid fa-box w-5 text-center text-gray"></i>
                    <span>My Orders</span>
                </a>
                <a href="<?php echo base_url('cart'); ?>" class="flex items-center gap-3 px-6 py-3 text-dark font-medium hover:bg-gray-50 hover:text-primary transition-all">
                    <i class="fa-solid fa-bag-shopping w-5 text-center text-gray"></i>
                    <span>Cart</span>
                    <?php if ($cart_count > 0): ?>
                        <span class="ml-auto bg-primary/10 text-primary text-xs font-semibold px-2 py-0.5 rounded-full"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>

                <a href="<?php echo base_url('wishlist'); ?>" class="flex items-center gap-3 px-6 py-3 text-dark font-medium hover:bg-light-gray hover:text-primary transition-all">
                    <i class="fa-regular fa-heart w-5 text-center text-gray"></i>
                    <span>Wishlist</span>
                    <?php if ($wishlist_count > 0): ?>
                        <span class="ml-auto bg-secondary/10 text-secondary text-xs font-semibold px-2 py-0.5 rounded-full"><?php echo $wishlist_count; ?></span>
                    <?php endif; ?>
                </a>

                <div class="border-t border-primary/10 my-3"></div>
            <?php endif; ?>

            <a href="<?php echo base_url(); ?>" class="flex items-center justify-between px-6 py-4 text-dark font-semibold hover:bg-light-gray hover:text-primary transition-all">
                <span>Home</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>

            <!-- Shop with Submenu -->
            <div class="mobile-menu-item">
                <button class="mobile-submenu-toggle w-full flex items-center justify-between px-6 py-4 text-dark font-semibold hover:bg-light-gray hover:text-primary transition-all">
                    <span>Shop</span>
                    <i class="fa-solid fa-chevron-down text-sm transition-transform duration-300"></i>
                </button>
                <div class="mobile-submenu hidden bg-light-gray/60">
                    <?php if (!empty($header_categories)): ?>
                        <?php foreach ($header_categories as $category): ?>
                            <a href="<?php echo category_url($category); ?>" class="flex items-center gap-3 px-8 py-3 text-dark hover:text-primary transition-all">
                                <?php if (!empty($category->icon)): ?>
                                    <img src="<?php echo base_url('uploads/categories/' . $category->icon); ?>" alt="<?php echo htmlspecialchars($category->category_name); ?>" class="w-6 h-6 rounded-lg object-cover">
                                <?php else: ?>
                                    <div class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center">
                                        <span class="text-primary font-bold text-xs"><?php echo strtoupper(substr($category->category_name, 0, 1)); ?></span>
                                    </div>
                                <?php endif; ?>
                                <span class="text-sm"><?php echo htmlspecialchars($category->category_name); ?></span>
                            </a>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <a href="<?php echo base_url('shop'); ?>" class="flex items-center gap-3 px-8 py-3 text-primary font-medium transition-all">
                        <i class="fa-solid fa-grid-2 w-6 text-center text-sm"></i>
                        <span class="text-sm">View All Products</span>
                    </a>
                </div>
            </div>

            <a href="<?php echo base_url('shop'); ?>" class="flex items-center justify-between px-6 py-4 text-dark font-semibold hover:bg-light-gray hover:text-primary transition-all">
                <span>Collections</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
            <a href="<?php echo base_url('shop'); ?>" class="flex items-center justify-between px-6 py-4 text-dark font-semibold hover:bg-light-gray hover:text-primary transition-all">
                <span>New Arrivals</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
            <a href="<?php echo base_url('shop'); ?>" class="flex items-center justify-between px-6 py-4 text-dark font-semibold hover:bg-light-gray hover:text-primary transition-all">
                <span>Best Sellers</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>
            <a href="<?php echo base_url('page/about'); ?>" class="flex items-center justify-between px-6 py-4 text-dark font-semibold hover:bg-light-gray hover:text-primary transition-all">
                <span>About</span>
                <i class="fa-solid fa-arrow-right text-sm"></i>
            </a>

            <?php if ($is_logged_in): ?>
                <div class="border-t border-gray-100 my-3"></div>

                <!-- Account Links (Logged In) -->
                <div class="px-6 py-2">
                    <p class="text-xs font-semibold text-gray uppercase tracking-wider">My Account</p>
                </div>
                <a href="<?php echo base_url('account'); ?>" class="flex items-center gap-3 px-6 py-3 text-dark font-medium hover:bg-gray-50 hover:text-primary transition-all">
                    <i class="fa-solid fa-user w-5 text-center text-gray"></i>
                    <span>My Profile</span>
                </a>
                <a href="<?php echo base_url('account/addresses'); ?>" class="flex items-center gap-3 px-6 py-3 text-dark font-medium hover:bg-gray-50 hover:text-primary transition-all">
                    <i class="fa-solid fa-location-dot w-5 text-center text-gray"></i>
                    <span>Saved Addresses</span>
                </a>
                <a href="<?php echo base_url('page/contact'); ?>" class="flex items-center gap-3 px-6 py-3 text-dark font-medium hover:bg-light-gray hover:text-primary transition-all">
                    <i class="fa-solid fa-headset w-5 text-center text-gray"></i>
                    <span>Help & Support</span>
                </a>
            <?php endif; ?>
        </nav>

        <!-- Sidebar Footer -->
        <div class="absolute bottom-0 left-0 right-0 border-t border-gray-100 bg-white">
            <?php if ($is_logged_in): ?>
                <!-- Logout (Logged In) -->
                <a href="<?php echo base_url('login/logout'); ?>" class="flex items-center gap-3 px-6 py-4 text-red-500 font-medium hover:bg-red-50 transition-all">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </a>
            <?php else: ?>
                <!-- Login (Guest) -->
                <a href="<?php echo base_url('login'); ?>" class="flex items-center gap-3 px-6 py-4 text-dark font-medium hover:bg-gray-50 hover:text-primary transition-all">
                    <i class="fa-regular fa-user"></i>
                    <span>Log in</span>
                </a>
            <?php endif; ?>

            <!-- Social Links -->
            <div class="flex items-center gap-4 px-6 py-4 border-t border-gray-100">
                <a href="#" class="text-gray-500 hover:text-primary transition-colors">
                    <i class="fa-brands fa-x-twitter text-lg"></i>
                </a>
                <a href="#" class="text-gray-500 hover:text-primary transition-colors">
                    <i class="fa-brands fa-facebook text-lg"></i>
                </a>
                <a href="#" class="text-gray-500 hover:text-primary transition-colors">
                    <i class="fa-brands fa-pinterest text-lg"></i>
                </a>
                <a href="#" class="text-gray-500 hover:text-primary transition-colors">
                    <i class="fa-brands fa-instagram text-lg"></i>
                </a>
                <a href="#" class="text-gray-500 hover:text-primary transition-colors">
                    <i class="fa-brands fa-youtube text-lg"></i>
                </a>
            </div>
        </div>
    </aside>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/919542200222" target="_blank" class="site-whatsapp-btn fixed bottom-5 right-4 sm:bottom-6 sm:right-6 w-12 h-12 sm:w-14 sm:h-14 bg-green-500 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl shadow-lg hover:bg-green-600 transition-colors z-50" aria-label="Chat on WhatsApp">
        <i class="fa-brands fa-whatsapp"></i>
    </a>

    <main>