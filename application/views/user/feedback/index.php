<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link rel="icon" type="image/png" href="<?php echo base_url('user_assets/images/piko-pop-favicon.png'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?php echo base_url('user_assets/css/output.css?') . time(); ?>">
    <style>
        .star-rating { display: flex; gap: 8px; justify-content: center; }
        .star-rating .star { cursor: pointer; font-size: 2.5rem; color: #e5e7eb; transition: all 0.2s ease; }
        .star-rating .star:hover, .star-rating .star.active { color: #FF4FA3; transform: scale(1.15); }
        .star-rating .star:hover ~ .star { color: #e5e7eb !important; }
        .star-rating:hover .star { color: #FF4FA3; }
        .star-rating:hover .star:hover ~ .star { color: #e5e7eb; }

        .option-pill { cursor: pointer; transition: all 0.2s ease; }
        .option-pill input[type="radio"] { display: none; }
        .option-pill .pill-label {
            display: block; padding: 10px 20px; border: 2px solid #e5e7eb; border-radius: 9999px;
            text-align: center; font-weight: 500; font-size: 0.875rem; color: #6b7280;
            transition: all 0.2s ease; user-select: none;
        }
        .option-pill input[type="radio"]:checked + .pill-label {
            border-color: #FF4FA3; background: #FF4FA3; color: white;
            box-shadow: 0 4px 12px rgba(255, 79, 163, 0.3);
        }
        .option-pill .pill-label:hover { border-color: #FF4FA3; color: #FF4FA3; }
        .option-pill input[type="radio"]:checked + .pill-label:hover { color: white; }

        .feedback-section { animation: fadeInUp 0.5s ease-out both; }
        .feedback-section:nth-child(2) { animation-delay: 0.05s; }
        .feedback-section:nth-child(3) { animation-delay: 0.1s; }
        .feedback-section:nth-child(4) { animation-delay: 0.15s; }
        .feedback-section:nth-child(5) { animation-delay: 0.2s; }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .image-upload-area {
            border: 2px dashed #e5e7eb; border-radius: 1rem; padding: 2rem; text-align: center;
            cursor: pointer; transition: all 0.2s ease;
        }
        .image-upload-area:hover { border-color: #FF4FA3; background: rgba(255, 79, 163, 0.02); }
        .image-upload-area.has-image { border-style: solid; border-color: #22c55e; }

        @keyframes successBounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4 py-3 flex items-center justify-center">
            <a href="<?php echo base_url() ?>">
                <img src="<?php echo base_url('user_assets/images/piko-pop-logo.png') ?>" alt="<?php echo config_item('application_name') ?>" class="h-10 md:h-12">
            </a>
        </div>
    </header>

    <?php
        $is_guest = !empty($is_guest);
    ?>

    <?php if (!empty($error)): ?>
    <!-- Error State -->
    <div class="container mx-auto px-4 py-20 text-center">
        <div class="max-w-md mx-auto">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fa-solid fa-link-slash text-red-500 text-3xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-dark mb-3">Invalid Feedback Link</h1>
            <p class="text-gray mb-8">This feedback link is invalid or has expired. Please contact us if you need a new link.</p>
            <a href="<?php echo base_url() ?>" class="btn-primary inline-block">Visit Our Store</a>
        </div>
    </div>

    <?php else: ?>
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-br from-primary/5 via-white to-secondary/5 py-8 md:py-12">
        <div class="container mx-auto px-4 text-center">
            <?php if (!$is_guest): ?>
            <span class="inline-block px-4 py-1.5 bg-primary/10 text-primary text-sm font-semibold rounded-full mb-4">
                Order #<?php echo htmlspecialchars($order->order_number) ?>
            </span>
            <?php else: ?>
            <span class="inline-block px-4 py-1.5 bg-secondary/10 text-secondary text-sm font-semibold rounded-full mb-4">
                Guest Feedback
            </span>
            <?php endif; ?>
            <h1 class="text-2xl md:text-4xl font-bold text-dark mb-3">How was your PIKO POP experience?</h1>
            <p class="text-gray text-base md:text-lg max-w-lg mx-auto">
                We'd love to hear about your order — your feedback helps us spread more smiles!
            </p>
        </div>
    </section>

    <?php if ($is_guest && !empty($success)): ?>
    <!-- Success Banner for Guest -->
    <div class="container mx-auto px-4 -mt-2 mb-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-green-50 border border-green-200 rounded-2xl p-4 md:p-5 text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fa-solid fa-check text-green-600 text-xl"></i>
                </div>
                <h3 class="text-lg font-bold text-green-800 mb-1">Thank You!</h3>
                <p class="text-green-700 text-sm">Your feedback has been submitted successfully. You can submit another review below.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!$is_guest && !empty($order_items)): ?>
    <!-- Order Summary (only for order-linked reviews) -->
    <div class="container mx-auto px-4 -mt-2 mb-6">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm p-4 md:p-5">
                <h3 class="text-sm font-semibold text-gray uppercase tracking-wider mb-3">Your Order</h3>
                <div class="flex flex-wrap gap-3">
                    <?php foreach ($order_items as $item):
                        $img = !empty($item->product_image) ? base_url('uploads/products/' . $item->product_image) : base_url('user_assets/images/product-fallback.png');
                    ?>
                    <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-3 py-2 flex-1 min-w-[200px]">
                        <img src="<?php echo $img ?>" class="w-12 h-12 rounded-lg object-cover" alt="">
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-dark truncate"><?php echo htmlspecialchars($item->product_name) ?></p>
                            <?php if (!empty($item->variant_name)): ?>
                            <p class="text-xs text-gray"><?php echo htmlspecialchars($item->variant_name) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Feedback Form -->
    <div class="container mx-auto px-4 pb-12">
        <div class="max-w-2xl mx-auto">
            <form id="feedback_form" enctype="multipart/form-data">
                <?php if (!$is_guest): ?>
                <input type="hidden" name="review_token" value="<?php echo htmlspecialchars($review->review_token) ?>">
                <?php endif; ?>
                <input type="hidden" name="is_guest" value="<?php echo $is_guest ? '1' : '0' ?>">

                <!-- Customer Name -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <label class="block text-sm font-medium text-dark mb-2">Your Name <?php if ($is_guest): ?><span class="text-red-500">*</span><?php endif; ?></label>
                    <input type="text" name="customer_name" id="customer_name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all" placeholder="Enter your name" value="<?php echo !$is_guest ? htmlspecialchars($order->delivery_fullname) : '' ?>" <?php echo $is_guest ? 'required' : '' ?>>
                    <?php if ($is_guest): ?>
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-dark mb-2">Phone Number</label>
                        <input type="tel" name="guest_phone" id="guest_phone" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all" placeholder="Enter your phone number (optional)">
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Q1: Overall Rating -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">1</span>
                        <h3 class="text-lg font-bold text-dark">How was your overall experience?</h3>
                    </div>
                    <div class="star-rating justify-center" id="star_rating">
                        <i class="fa-solid fa-star star" data-value="1"></i>
                        <i class="fa-solid fa-star star" data-value="2"></i>
                        <i class="fa-solid fa-star star" data-value="3"></i>
                        <i class="fa-solid fa-star star" data-value="4"></i>
                        <i class="fa-solid fa-star star" data-value="5"></i>
                    </div>
                    <input type="hidden" name="overall_rating" id="overall_rating" value="">
                    <p class="text-center text-sm text-gray mt-2" id="rating_text">Tap a star to rate</p>
                </div>

                <!-- Q2: Taste Feedback -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">2</span>
                        <h3 class="text-lg font-bold text-dark">How happy were you with the products?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Excellent', 'Good', 'Average', 'Needs improvement'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="taste_feedback" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q3: Ease of Cooking -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">3</span>
                        <h3 class="text-lg font-bold text-dark">Was it easy to use or play with?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Very Easy', 'Easy', 'Normal', 'Difficult'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="ease_of_cooking" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q4: Restaurant Style Taste -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">4</span>
                        <h3 class="text-lg font-bold text-dark">Did the kids love it?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Yes, they loved it!', 'Somewhat', 'Not really'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="restaurant_taste" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q5: Instructions Clarity -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">5</span>
                        <h3 class="text-lg font-bold text-dark">Was the product information clear?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Yes', 'Somewhat', 'No'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="instructions_clarity" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q6: Packaging -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">6</span>
                        <h3 class="text-lg font-bold text-dark">How was the packaging quality?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Excellent', 'Good', 'Average', 'Poor'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="packaging_feedback" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q7: Value for Money -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">7</span>
                        <h3 class="text-lg font-bold text-dark">Is PIKO POP worth the price?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Yes', 'Maybe', 'No'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="value_for_money" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q8: Recommendation -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">8</span>
                        <h3 class="text-lg font-bold text-dark">Would you recommend PIKO POP?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Definitely', 'Maybe', 'No'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="recommendation" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q9: Repeat Purchase -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">9</span>
                        <h3 class="text-lg font-bold text-dark">Would you shop PIKO POP again?</h3>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2 md:gap-3">
                        <?php foreach (['Yes', 'Maybe', 'No'] as $opt): ?>
                        <label class="option-pill">
                            <input type="radio" name="repeat_purchase" value="<?php echo $opt ?>">
                            <span class="pill-label"><?php echo $opt ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Q10: Liked Most -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">10</span>
                        <h3 class="text-lg font-bold text-dark">What did you like the most?</h3>
                    </div>
                    <textarea name="liked_most" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all resize-none" placeholder="Tell us what you loved about your PIKO POP order..."></textarea>
                </div>

                <!-- Q11: Improvement -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-4">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">11</span>
                        <h3 class="text-lg font-bold text-dark">What can we improve?</h3>
                    </div>
                    <textarea name="improvement" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all resize-none" placeholder="Your suggestions help us get better..."></textarea>
                </div>

                <!-- Q12: Photo Upload -->
                <div class="feedback-section bg-white rounded-2xl shadow-sm p-5 md:p-6 mb-6">
                    <div class="text-center mb-4">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-primary/10 text-primary rounded-full text-sm font-bold mb-2">12</span>
                        <h3 class="text-lg font-bold text-dark">Share a Photo</h3>
                        <p class="text-sm text-gray">Upload a photo of your cute finds (optional)</p>
                    </div>
                    <div class="image-upload-area" id="upload_area" onclick="document.getElementById('review_image').click()">
                        <div id="upload_placeholder">
                            <i class="fa-solid fa-camera text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray">Tap to upload a photo</p>
                        </div>
                        <div id="upload_preview" class="hidden">
                            <img id="preview_img" src="" alt="Preview" class="max-h-48 mx-auto rounded-lg">
                            <p class="text-sm text-green-600 mt-2"><i class="fa-solid fa-check mr-1"></i>Photo selected</p>
                        </div>
                    </div>
                    <input type="file" name="review_image" id="review_image" accept="image/*" class="hidden">
                </div>

                <!-- Submit -->
                <button type="submit" id="submit_btn" class="w-full bg-primary text-white py-4 rounded-full font-bold text-lg transition-all duration-300 hover:bg-primary-dark hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 active:translate-y-0">
                    Submit Feedback
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center">
        <p class="text-sm text-gray">&copy; <?php echo date('Y') ?> <?php echo config_item('application_name') ?>. All rights reserved.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
    var base_url = '<?php echo base_url() ?>';
    $(document).ready(function() {
        // Star rating
        var ratingTexts = { 1: 'Poor', 2: 'Fair', 3: 'Average', 4: 'Good', 5: 'Excellent!' };

        $('.star-rating .star').on('click', function() {
            var val = $(this).data('value');
            $('#overall_rating').val(val);
            $('.star-rating .star').each(function() {
                $(this).toggleClass('active', $(this).data('value') <= val);
            });
            $('#rating_text').text(ratingTexts[val]);
        });

        // Image preview
        $('#review_image').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#preview_img').attr('src', e.target.result);
                    $('#upload_placeholder').addClass('hidden');
                    $('#upload_preview').removeClass('hidden');
                    $('#upload_area').addClass('has-image');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submit
        var isGuest = $('input[name="is_guest"]').val() === '1';

        $('#feedback_form').on('submit', function(e) {
            e.preventDefault();

            if (!$('#overall_rating').val()) {
                alert('Please provide an overall rating by tapping the stars.');
                $('html, body').animate({ scrollTop: $('#star_rating').offset().top - 100 }, 400);
                return;
            }

            if (isGuest && !$('#customer_name').val().trim()) {
                alert('Please enter your name.');
                $('#customer_name').focus();
                return;
            }

            var $btn = $('#submit_btn');
            $btn.prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin mr-2"></i>Submitting...');

            var formData = new FormData(this);
            var submitUrl = isGuest ? base_url + 'feedback/guest_submit' : base_url + 'feedback/submit';

            $.ajax({
                url: submitUrl,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                success: function(result) {
                    var obj = JSON.parse(result);
                    if (obj.status == 200) {
                        if (obj.is_guest) {
                            // Guest: show inline thank you (can submit again)
                            window.location.href = base_url + 'feedback/guest?success=1';
                        } else {
                            window.location.reload();
                        }
                    } else {
                        alert(obj.message);
                        $btn.prop('disabled', false).html('Submit Feedback');
                    }
                },
                error: function() {
                    alert('Something went wrong. Please try again.');
                    $btn.prop('disabled', false).html('Submit Feedback');
                }
            });
        });
    });
    </script>
</body>
</html>
