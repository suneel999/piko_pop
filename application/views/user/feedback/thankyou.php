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
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-3 flex items-center justify-center">
            <a href="<?php echo base_url() ?>">
                <img src="<?php echo base_url('user_assets/images/piko-pop-logo.png') ?>" alt="<?php echo config_item('application_name') ?>" class="h-10 md:h-12">
            </a>
        </div>
    </header>

    <!-- Thank You Content -->
    <div class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="max-w-md w-full text-center">
            <!-- Success Animation -->
            <div class="success-icon mb-6">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto success-pulse">
                    <svg class="w-12 h-12" viewBox="0 0 52 52">
                        <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none" stroke="#22c55e" stroke-width="2"/>
                        <path class="checkmark-check" fill="none" stroke="#22c55e" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                    </svg>
                </div>
            </div>

            <h1 class="text-2xl md:text-3xl font-bold text-dark mb-3">Thank You!</h1>
            <p class="text-gray text-base md:text-lg mb-2">Your feedback has been submitted successfully.</p>
            <p class="text-gray text-sm mb-1">
                Order <span class="font-semibold text-dark">#<?php echo htmlspecialchars($order->order_number) ?></span>
            </p>

            <?php if (!empty($review->overall_rating)): ?>
            <div class="flex items-center justify-center gap-1 my-4">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                <i class="fa-solid fa-star text-2xl <?php echo $i <= $review->overall_rating ? 'text-primary' : 'text-gray-200' ?>"></i>
                <?php endfor; ?>
            </div>
            <?php endif; ?>

            <p class="text-gray text-sm mb-8">Your feedback helps us make PIKO POP even better!</p>

            <a href="<?php echo base_url() ?>" class="btn-primary inline-block">
                <i class="fa-solid fa-store mr-2"></i>Visit Our Store
            </a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-100 py-6 text-center">
        <p class="text-sm text-gray">&copy; <?php echo date('Y') ?> <?php echo config_item('application_name') ?>. All rights reserved.</p>
    </footer>
</body>
</html>
