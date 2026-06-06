<?php
$is_logged_in = is_loggedin_user();
if ($is_logged_in) {
    redirect('');
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' - ' : ''; ?>PIKO POP</title>
    <meta name="description" content="Login to your PIKO POP account and shop cute kids products.">
    <link rel="icon" type="image/png" href="<?php echo base_url('user_assets/images/piko-pop-favicon.png'); ?>">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
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
    <link href="<?php echo base_url('user_assets/') ?>css/output.css" rel="stylesheet">
</head>

<body class="bg-brand-bg min-h-screen flex flex-col">

    <!-- Header (Minimal) -->
    <header class="site-header">
        <div class="container mx-auto px-4">
            <div class="flex items-center h-16 md:h-20">
                <a href="<?php echo base_url(); ?>" class="flex-shrink-0">
                    <img src="<?php echo base_url('user_assets/images/piko-pop-logo.png'); ?>" alt="PIKO POP" class="h-10 md:h-12 w-auto">
                </a>
            </div>
        </div>
    </header>