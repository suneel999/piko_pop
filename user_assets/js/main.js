/**
 * PIKO POP - Main JavaScript
 * Customer-facing UI interactions
 */

$(document).ready(function () {
    'use strict';

    // ========================================
    // Mobile Navigation
    // ========================================
    const $menuToggle = $('#menuToggle');
    const $mobileSidebar = $('#mobileSidebar');
    const $navOverlay = $('#navOverlay');
    const $sidebarClose = $('#sidebarClose');

    // Open mobile sidebar
    $menuToggle.on('click', function () {
        $mobileSidebar.addClass('active');
        $navOverlay.addClass('active');
        $('body').css('overflow', 'hidden');
    });

    // Close mobile sidebar
    function closeSidebar() {
        $mobileSidebar.removeClass('active');
        $navOverlay.removeClass('active');
        $('body').css('overflow', '');
    }

    $sidebarClose.on('click', closeSidebar);
    $navOverlay.on('click', closeSidebar);

    // Mobile Submenu Toggle
    $('.mobile-submenu-toggle').on('click', function () {
        const $menuItem = $(this).closest('.mobile-menu-item');
        const $submenu = $menuItem.find('.mobile-submenu');
        const $icon = $(this).find('i');

        $submenu.slideToggle(200);
        $icon.toggleClass('rotate-180');
    });

    // Close sidebar on escape key
    $(document).on('keydown', function (e) {
        if (e.key === 'Escape') {
            closeSidebar();
            closeQuickViewModal();
            closeSearchBar();
        }
    });

    // ========================================
    // Search Bar Toggle
    // ========================================
    const $searchToggle = $('#searchToggle');
    const $searchBar = $('#searchBar');

    $searchToggle.on('click', function () {
        $searchBar.slideToggle(200);
        if ($searchBar.is(':visible')) {
            $searchBar.find('input').focus();
        }
    });

    function closeSearchBar() {
        $searchBar.slideUp(200);
    }

    // ========================================
    // Banner Slider
    // ========================================
    const $sliderWrapper = $('.slider-wrapper');
    const $slides = $('.slide');
    const $prevBtn = $('#prevSlide');
    const $nextBtn = $('#nextSlide');
    const $dots = $('.slider-dot');
    let currentSlide = 0;
    const totalSlides = $slides.length;
    let autoSlideInterval;

    if ($sliderWrapper.length && totalSlides > 0) {
        // Initialize slider
        function updateSlider() {
            $sliderWrapper.css('transform', `translateX(-${currentSlide * 100}%)`);

            // Update dots
            $dots.removeClass('active bg-white').addClass('bg-white/50');
            $dots.eq(currentSlide).addClass('active bg-white').removeClass('bg-white/50');
        }

        // Next slide
        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
        }

        // Previous slide
        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
        }

        // Auto slide
        function startAutoSlide() {
            autoSlideInterval = setInterval(nextSlide, 5000);
        }

        function stopAutoSlide() {
            clearInterval(autoSlideInterval);
        }

        // Event listeners for slider
        $nextBtn.on('click', function () {
            stopAutoSlide();
            nextSlide();
            startAutoSlide();
        });

        $prevBtn.on('click', function () {
            stopAutoSlide();
            prevSlide();
            startAutoSlide();
        });

        $dots.on('click', function () {
            stopAutoSlide();
            currentSlide = $(this).data('slide');
            updateSlider();
            startAutoSlide();
        });

        // Pause on hover
        $('#bannerSlider').on('mouseenter', stopAutoSlide);
        $('#bannerSlider').on('mouseleave', startAutoSlide);

        // Touch support for slider
        let touchStartX = 0;
        let touchEndX = 0;

        $('#bannerSlider').on('touchstart', function (e) {
            touchStartX = e.originalEvent.changedTouches[0].screenX;
        });

        $('#bannerSlider').on('touchend', function (e) {
            touchEndX = e.originalEvent.changedTouches[0].screenX;
            handleSwipe();
        });

        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;

            if (Math.abs(diff) > swipeThreshold) {
                stopAutoSlide();
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
                startAutoSlide();
            }
        }

        // Start auto slide
        startAutoSlide();
    }

    // ========================================
    // Products Horizontal Scroll Navigation
    // ========================================
    // Initialize all product sliders (supports multiple instances)
    function initProductSliders() {
        $('.products-scroll').each(function() {
            const $scroll = $(this);
            // Use parent() to get the container since group/slider has special character
            const $container = $scroll.parent();
            const $prevBtn = $container.find('.product-slider-prev');
            const $nextBtn = $container.find('.product-slider-next');

            if (!$scroll.length) return;

            // Calculate scroll amount based on card width
            function getScrollAmount() {
                const $firstCard = $scroll.find('.product-card').first();
                if ($firstCard.length) {
                    return $firstCard.outerWidth(true); // includes margin
                }
                return 300; // fallback
            }

            // Next button click
            $nextBtn.off('click').on('click', function() {
                const scrollAmount = getScrollAmount();
                $scroll.animate({
                    scrollLeft: $scroll.scrollLeft() + scrollAmount
                }, 300);
            });

            // Prev button click
            $prevBtn.off('click').on('click', function() {
                const scrollAmount = getScrollAmount();
                $scroll.animate({
                    scrollLeft: $scroll.scrollLeft() - scrollAmount
                }, 300);
            });

            // Show/hide arrows based on scroll position
            function updateArrows() {
                const scrollLeft = $scroll.scrollLeft();
                const maxScroll = $scroll[0].scrollWidth - $scroll[0].clientWidth;

                if (scrollLeft <= 10) {
                    $prevBtn.addClass('!opacity-30 pointer-events-none');
                } else {
                    $prevBtn.removeClass('!opacity-30 pointer-events-none');
                }

                if (scrollLeft >= maxScroll - 10) {
                    $nextBtn.addClass('!opacity-30 pointer-events-none');
                } else {
                    $nextBtn.removeClass('!opacity-30 pointer-events-none');
                }
            }

            $scroll.off('scroll.slider').on('scroll.slider', updateArrows);
            updateArrows(); // Initial check
        });
    }

    // Initialize sliders on page load
    initProductSliders();

    // Also support legacy ID-based selectors for backwards compatibility
    const $productsScroll = $('#productsScroll');
    const $productPrev = $('#productPrev');
    const $productNext = $('#productNext');

    if ($productsScroll.length) {
        // Calculate scroll amount based on card width
        function getProductScrollAmount() {
            const $firstCard = $productsScroll.find('.product-card').first();
            if ($firstCard.length) {
                return $firstCard.outerWidth(true); // includes margin
            }
            return 300; // fallback
        }

        $productNext.on('click', function () {
            const scrollAmount = getProductScrollAmount();
            $productsScroll.animate({
                scrollLeft: $productsScroll.scrollLeft() + scrollAmount
            }, 300);
        });

        $productPrev.on('click', function () {
            const scrollAmount = getProductScrollAmount();
            $productsScroll.animate({
                scrollLeft: $productsScroll.scrollLeft() - scrollAmount
            }, 300);
        });

        // Show/hide arrows based on scroll position
        function updateProductArrows() {
            const scrollLeft = $productsScroll.scrollLeft();
            const maxScroll = $productsScroll[0].scrollWidth - $productsScroll[0].clientWidth;

            if (scrollLeft <= 10) {
                $productPrev.addClass('!opacity-30 pointer-events-none');
            } else {
                $productPrev.removeClass('!opacity-30 pointer-events-none');
            }

            if (scrollLeft >= maxScroll - 10) {
                $productNext.addClass('!opacity-30 pointer-events-none');
            } else {
                $productNext.removeClass('!opacity-30 pointer-events-none');
            }
        }

        $productsScroll.on('scroll', updateProductArrows);
        updateProductArrows(); // Initial check
    }

    // ========================================
    // FAQ Accordion
    // ========================================
    const $faqToggles = $('.faq-toggle');

    $faqToggles.on('click', function () {
        const $faqItem = $(this).closest('.faq-item');
        const $content = $faqItem.find('.faq-content');
        const $icon = $(this).find('i');

        // Toggle current item
        $content.slideToggle(200);
        $icon.toggleClass('rotate-180');

        // Close other items
        $('.faq-item').not($faqItem).each(function () {
            $(this).find('.faq-content').slideUp(200);
            $(this).find('.faq-toggle i').removeClass('rotate-180');
        });
    });

    // ========================================
    // Quick View Modal
    // ========================================
    const $quickViewModal = $('#quickViewModal');
    const $modalClose = $('.modal-close');
    const $modalOverlay = $('.modal-overlay');

    // Sample product data (in real app, this would come from API)
    const products = {
        1: {
            name: 'Cute Stickers Pack',
            price: 'From Rs. 99.00',
            image: 'https://placehold.co/500x500/FFF0F8/FF4FA3?text=Cute+Stickers',
            bgColor: 'bg-pink-100',
            description: 'Adorable sticker sheets with animals, stars, and fun shapes — perfect for notebooks and crafts.'
        },
        2: {
            name: 'Animal Stickers Collection',
            price: 'From Rs. 129.00',
            image: 'https://placehold.co/500x500/FFF0F8/6C3CC9?text=Animal+Stickers',
            bgColor: 'bg-purple-100',
            description: 'Wildly cute animal stickers kids love to collect, trade, and decorate with.'
        },
        3: {
            name: 'ABC Stickers Set',
            price: 'From Rs. 149.00',
            image: 'https://placehold.co/500x500/FFF9FD/20BDF7?text=ABC+Stickers',
            bgColor: 'bg-blue-100',
            description: 'Colorful alphabet stickers that make learning letters fun and playful.'
        },
        4: {
            name: 'Kids Stationery Bundle',
            price: 'From Rs. 199.00',
            image: 'https://placehold.co/500x500/FFF0F8/FFD83D?text=Stationery',
            bgColor: 'bg-yellow-100',
            description: 'Pens, notebooks, and cute stationery essentials for school and creative time.'
        },
        5: {
            name: 'PIKO POP Gift Sets',
            price: 'From Rs. 299.00',
            image: 'https://placehold.co/500x500/FFF0F8/FF4FA3?text=Gift+Sets',
            bgColor: 'bg-pink-100',
            description: 'Curated gift combos for birthdays, return gifts, and little celebrations.'
        }
    };

    // Open quick view modal
    $(document).on('click', '.quick-view-btn', function () {
        const productId = $(this).data('product-id');
        const product = products[productId];

        if (product) {
            $('#modalProductName').text(product.name);
            $('#modalProductPrice').text(product.price);
            $('#modalProductImage').attr('src', product.image);
            $('#modalProductDesc').text(product.description);

            // Update background color
            const $imageSection = $quickViewModal.find('.bg-amber-100, .bg-green-100, .bg-red-100, .bg-yellow-100');
            $imageSection.removeClass('bg-amber-100 bg-green-100 bg-red-100 bg-yellow-100').addClass(product.bgColor);
        }

        openQuickViewModal();
    });

    function openQuickViewModal() {
        $quickViewModal.removeClass('hidden');
        $('body').css('overflow', 'hidden');
    }

    function closeQuickViewModal() {
        $quickViewModal.addClass('hidden');
        $('body').css('overflow', '');
    }

    $modalClose.on('click', closeQuickViewModal);
    $modalOverlay.on('click', closeQuickViewModal);

    // Weight options selection
    $(document).on('click', '.weight-option', function () {
        $('.weight-option').removeClass('active border-dark bg-gray-50').addClass('border-gray-200');
        $(this).addClass('active border-dark bg-gray-50').removeClass('border-gray-200');
    });

    // Quantity controls
    $(document).on('click', '.qty-minus', function () {
        const $input = $(this).siblings('input');
        const currentVal = parseInt($input.val());
        if (currentVal > 1) {
            $input.val(currentVal - 1);
        }
    });

    $(document).on('click', '.qty-plus', function () {
        const $input = $(this).siblings('input');
        const currentVal = parseInt($input.val());
        $input.val(currentVal + 1);
    });

    // ========================================
    // Header Scroll Effect
    // ========================================
    let lastScrollTop = 0;
    const $header = $('header');

    $(window).on('scroll', function () {
        const scrollTop = $(this).scrollTop();

        if (scrollTop > 100) {
            $header.addClass('shadow-md');
        } else {
            $header.removeClass('shadow-md');
        }

        lastScrollTop = scrollTop;
    });

    // ========================================
    // Smooth Scroll for Anchor Links
    // ========================================
    $('a[href^="#"]').on('click', function (e) {
        const href = this.getAttribute('href');

        // allow only valid in-page anchors
        if (href && href.startsWith('#') && href.length > 1) {
            const target = document.querySelector(href);

            if (target) {
                e.preventDefault();
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 80
                }, 500);
            }
        }
    });


    // ========================================
    // Add to Cart Animation (Visual Feedback)
    // ========================================
    $('.btn-primary, .btn-secondary').on('click', function () {
        const $btn = $(this);
        const originalText = $btn.html();

        // If it's an "Add to Cart" button
        if ($btn.text().includes('Add to Cart')) {
            $btn.html('<i class="fa-solid fa-check mr-2"></i>Added!');
            $btn.addClass('bg-green-500 border-green-500').removeClass('bg-primary bg-secondary');

            setTimeout(function () {
                $btn.html(originalText);
                $btn.removeClass('bg-green-500 border-green-500').addClass('bg-primary bg-secondary');
            }, 1500);
        }
    });

    // ========================================
    // Lazy Loading Images (for performance)
    // ========================================
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver(function (entries, observer) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                    }
                    observer.unobserve(img);
                }
            });
        });

        $('img[data-src]').each(function () {
            imageObserver.observe(this);
        });
    }

    // ========================================
    // Newsletter Form Validation
    // ========================================
    $('footer form').on('submit', function (e) {
        e.preventDefault();
        const $email = $(this).find('input[type="email"]');
        const email = $email.val().trim();

        if (email && isValidEmail(email)) {
            // Show success message
            const $btn = $(this).find('button[type="submit"]');
            const originalText = $btn.text();
            $btn.html('<i class="fa-solid fa-check mr-2"></i>Subscribed!');
            $email.val('');

            setTimeout(function () {
                $btn.text(originalText);
            }, 2000);
        }
    });

    function isValidEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    // ========================================
    // Initialize tooltips and other UI elements
    // ========================================
    console.log('PIKO POP - Website Initialized Successfully!');

    // ========================================
    // Product Detail Page - Gallery
    // ========================================
    const $mainImage = $('#mainImage');
    const $thumbnails = $('.thumbnail-btn');
    const $thumbContainer = $('#thumbnailContainer');
    const $thumbPrev = $('#thumbPrev');
    const $thumbNext = $('#thumbNext');

    if ($mainImage.length && $thumbnails.length) {
        // Thumbnail click - change main image
        $thumbnails.on('click', function() {
            const newImage = $(this).data('image');
            $mainImage.attr('src', newImage);

            // Update active thumbnail
            $thumbnails.removeClass('active border-dark').addClass('border-gray-200');
            $(this).removeClass('border-gray-200').addClass('active border-dark');
        });

        // Thumbnail navigation
        if ($thumbContainer.length) {
            $thumbNext.on('click', function() {
                $thumbContainer.animate({
                    scrollLeft: $thumbContainer.scrollLeft() + 112
                }, 200);
            });

            $thumbPrev.on('click', function() {
                $thumbContainer.animate({
                    scrollLeft: $thumbContainer.scrollLeft() - 112
                }, 200);
            });
        }
    }

    // ========================================
    // Product Detail Page - Size Selection
    // ========================================
    $(document).on('click', '.size-option', function() {
        $('.size-option').removeClass('active border-dark bg-gray-50').addClass('border-gray-200');
        $(this).addClass('active border-dark bg-gray-50').removeClass('border-gray-200');
    });

    // ========================================
    // Product Detail Page - Quantity Controls
    // ========================================
    $(document).on('click', '.qty-btn', function() {
        const $input = $(this).siblings('input');
        const currentVal = parseInt($input.val()) || 1;

        if ($(this).find('i').hasClass('fa-minus')) {
            if (currentVal > 1) {
                $input.val(currentVal - 1);
            }
        } else {
            $input.val(currentVal + 1);
        }
    });

    // ========================================
    // Product Detail Page - Accordions
    // ========================================
    $('.accordion-toggle').on('click', function() {
        const $accordion = $(this).closest('.product-accordion');
        const $content = $accordion.find('.accordion-content');
        const $icon = $(this).find('i:last-child');

        // Toggle current
        $content.slideToggle(200);
        $icon.toggleClass('fa-plus fa-minus');

        // Optionally close others
        $('.product-accordion').not($accordion).each(function() {
            $(this).find('.accordion-content').slideUp(200);
            $(this).find('.accordion-toggle i:last-child').removeClass('fa-minus').addClass('fa-plus');
        });
    });

    // ========================================
    // Cart Page - Quantity Controls
    // ========================================
    $(document).on('click', '.cart-qty-btn', function() {
        const $container = $(this).closest('.cart-item');
        const $input = $(this).siblings('input');
        const currentVal = parseInt($input.val()) || 1;

        if ($(this).find('i').hasClass('fa-minus')) {
            if (currentVal > 1) {
                $input.val(currentVal - 1);
            }
        } else {
            $input.val(currentVal + 1);
        }

        // Here you would update the cart totals via AJAX in real implementation
    });

    
});
