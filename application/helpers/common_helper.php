<?php

/**
 * Generates a UUID v4 string
 *
 * @return string UUID in format: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
 */
function generate_uuid()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0xffff)
    );
}


/**
 * Calculates the percentage of a number.
 *
 * @param float $number The original number.
 * @param float $percentage The percentage to be calculated.
 * @return float The calculated percentage.
 */
function calculatePercentage($number, $percentage)
{
    return ($percentage / 100) * $number;
}

/**
 * Generates a random code of specified length using alphanumeric characters.
 *
 * @param int $length The length of the generated random code.
 * @return string The generated random code.
 */
function generateRandomCode($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $code = '';

    // Generate random code by selecting characters from the specified set
    for ($i = 0; $i < $length; $i++) {
        $randomIndex = mt_rand(0, strlen($characters) - 1);
        $code .= $characters[$randomIndex];
    }

    return $code;
}

/**
 * Retrieves a system configuration value by key.
 *
 * @param string $key The configuration key.
 * @param mixed $default The default value to return if the config is not found.
 * @return mixed The configuration value or the default value.
 */
function get_system_config($key, $default = null)
{
    $CI =& get_instance();
    $config = $CI->common->getdatabytable('system_config', array('config_key' => $key));
    return $config ? $config->config_value : $default;
}

/**
 * Masks a name by replacing characters with asterisks.
 *
 * @param string $name The original name.
 * @return string The masked name.
 */
function mask_name($name)
{
    $length = strlen($name);
    if ($length <= 4) return str_repeat('*', $length);

    return substr($name, 0, 2) . str_repeat('*', $length - 4) . substr($name, -2);
}

/**
 * Formats the time remaining into a human-readable string.
 *
 * @param int $seconds The time remaining in seconds.
 * @return string The formatted time remaining.
 */
function format_time_remaining($seconds)
{
    if ($seconds <= 0) return 'Expired';

    $days = floor($seconds / (24 * 3600));
    $hours = floor(($seconds % (24 * 3600)) / 3600);
    $minutes = floor(($seconds % 3600) / 60);

    if ($days > 0) {
        return "{$days}d {$hours}h {$minutes}m";
    } else if ($hours > 0) {
        return "{$hours}h {$minutes}m";
    } else {
        return "{$minutes}m";
    }
}

/**
 * Get all parent categories (top level categories with parent_category_id = 0)
 *
 * @return array Array of category objects
 */
function get_parent_categories()
{
    $CI =& get_instance();
    $where_data = array(
        'parent_category_id' => 0,
        'status' => 1
    );
    return $CI->common->getdatabytableall('categories', $where_data, 'sort_order', 'ASC');
}

/**
 * Get all active categories (both parent and sub categories)
 *
 * @return array Array of category objects ordered by parent first then children
 */
function get_all_categories()
{
    $CI =& get_instance();
    $where_data = array(
        'status' => 1
    );
    return $CI->common->getdatabytableall('categories', $where_data, 'sort_order', 'ASC');
}

/**
 * Get category name by ID
 *
 * @param int $category_id The category ID
 * @return string The category name or empty string
 */
function get_category_name($category_id)
{
    $CI =& get_instance();
    $category = $CI->common->getdatabytable('categories', array('id' => $category_id));
    return $category ? $category->category_name : '';
}

/**
 * Get sub categories by parent category ID
 *
 * @param int $parent_id The parent category ID
 * @return array Array of sub category objects
 */
function get_sub_categories($parent_id)
{
    $CI =& get_instance();
    $where_data = array(
        'parent_category_id' => $parent_id,
        'status' => 1
    );
    return $CI->common->getdatabytableall('categories', $where_data, 'sort_order', 'ASC');
}

/**
 * Get all categories with status (for jsTree - includes inactive but not deleted)
 *
 * @return array Array of category objects
 */
function get_all_categories_with_status()
{
    $CI =& get_instance();
    $where_data = array(
        'status !=' => 3
    );
    return $CI->common->getdatabytableall('categories', $where_data, 'sort_order', 'ASC');
}

// ============================================
// SLUG HELPER FUNCTIONS
// ============================================

/**
 * Generate URL-friendly slug from a string
 *
 * @param string $string The string to convert to slug
 * @return string URL-friendly slug
 */
function slugify($string)
{
    // Convert to lowercase
    $slug = strtolower($string);

    // Replace special characters with their equivalents
    $slug = str_replace(
        array('&', '@', '#', '$', '%', '^', '*', '(', ')', '+', '=', '{', '}', '[', ']', '|', '\\', '/', ':', ';', '"', "'", '<', '>', ',', '.', '?', '!'),
        array('and', 'at', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''),
        $slug
    );

    // Replace non-alphanumeric characters with hyphens
    $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);

    // Replace multiple hyphens with single hyphen
    $slug = preg_replace('/-+/', '-', $slug);

    // Remove leading and trailing hyphens
    $slug = trim($slug, '-');

    return $slug;
}

/**
 * Generate product URL with slug and ID
 * Format: /product/{slug}-{id}
 *
 * @param object|array $product Product object or array with id and product_name
 * @return string Full product URL
 */
function product_url($product)
{
    $CI =& get_instance();

    if (is_array($product)) {
        $id = $product['id'];
        $name = $product['product_name'];
    } else {
        $id = $product->id;
        $name = $product->product_name;
    }

    $slug = slugify($name);

    return base_url('product/' . $slug . '-' . $id);
}

/**
 * Extract product ID from slug URL
 * Format: {slug}-{id} -> returns {id}
 *
 * @param string $slug_with_id The slug string containing ID at the end
 * @return int|null Product ID or null if not found
 */
function get_id_from_slug($slug_with_id)
{
    // ID is always at the end after the last hyphen
    $parts = explode('-', $slug_with_id);

    if (empty($parts)) {
        return null;
    }

    $last_part = end($parts);

    // Check if the last part is numeric
    if (is_numeric($last_part)) {
        return intval($last_part);
    }

    return null;
}

/**
 * Generate category URL with slug and ID
 * Format: /category/{slug}-{id}
 *
 * @param object|array $category Category object or array with id and category_name
 * @return string Full category URL
 */
function category_url($category)
{
    $CI =& get_instance();

    if (is_array($category)) {
        $id = $category['id'];
        $name = $category['category_name'];
    } else {
        $id = $category->id;
        $name = $category->category_name;
    }

    $slug = slugify($name);

    return base_url('category/' . $slug . '-' . $id);
}

function recipe_url($recipe)
{
    if (is_array($recipe)) {
        $id = $recipe['id'];
        $slug = !empty($recipe['slug']) ? $recipe['slug'] : slugify($recipe['title']);
    } else {
        $id = $recipe->id;
        $slug = !empty($recipe->slug) ? $recipe->slug : slugify($recipe->title);
    }
    return base_url('recipe/' . $slug . '-' . $id);
}

function recipe_thumbnail_url($recipe)
{
    if (is_array($recipe)) {
        $thumb = !empty($recipe['thumbnail']) ? $recipe['thumbnail'] : null;
        $video_id = !empty($recipe['youtube_video_id']) ? $recipe['youtube_video_id'] : '';
    } else {
        $thumb = !empty($recipe->thumbnail) ? $recipe->thumbnail : null;
        $video_id = !empty($recipe->youtube_video_id) ? $recipe->youtube_video_id : '';
    }
    if (!empty($thumb)) {
        return base_url('uploads/recipes/' . $thumb);
    }
    if (!empty($video_id)) {
        return 'https://img.youtube.com/vi/' . $video_id . '/hqdefault.jpg';
    }
    return 'https://placehold.co/400x225/f3f4f6/9ca3af?text=Recipe';
}

// ============================================
// SHIPPING HELPER FUNCTIONS
// ============================================

/**
 * Calculate shipping charge based on cart subtotal
 *
 * @param float $subtotal Cart subtotal
 * @return float Shipping charge (0 if free shipping applies)
 */
function calculate_shipping($subtotal)
{
    // Check if shipping is enabled
    if (!config_item('shipping_enabled')) {
        return 0;
    }

    $shipping_charge = floatval(config_item('shipping_charge'));
    $free_shipping_min = floatval(config_item('free_shipping_min_amount'));

    // Free shipping if subtotal meets minimum or if free_shipping_min is 0 (no threshold)
    if ($free_shipping_min > 0 && $subtotal >= $free_shipping_min) {
        return 0;
    }

    return $shipping_charge;
}

/**
 * Get shipping display info
 *
 * @param float $subtotal Cart subtotal
 * @return array ['charge' => float, 'is_free' => bool, 'free_threshold' => float, 'amount_to_free' => float]
 */
function get_shipping_info($subtotal)
{
    $shipping_enabled = config_item('shipping_enabled');
    $shipping_charge = floatval(config_item('shipping_charge'));
    $free_shipping_min = floatval(config_item('free_shipping_min_amount'));

    if (!$shipping_enabled) {
        return array(
            'charge' => 0,
            'is_free' => true,
            'free_threshold' => 0,
            'amount_to_free' => 0,
            'message' => 'Free Delivery'
        );
    }

    $is_free = ($free_shipping_min > 0 && $subtotal >= $free_shipping_min);
    $amount_to_free = $is_free ? 0 : max(0, $free_shipping_min - $subtotal);

    $message = '';
    if ($is_free) {
        $message = 'Free Delivery';
    } else if ($free_shipping_min > 0 && $amount_to_free > 0) {
        $message = 'Add $' . number_format($amount_to_free, 2) . ' more for free delivery';
    }

    return array(
        'charge' => $is_free ? 0 : $shipping_charge,
        'is_free' => $is_free,
        'free_threshold' => $free_shipping_min,
        'amount_to_free' => $amount_to_free,
        'message' => $message
    );
}


/**
 * Sends an SMS to the specified phone number with the given message.
 *
 * @param string $phone The phone number to which the SMS will be sent.
 * @param string $message The content of the SMS message.
 * @param bool $fakeSend If true, simulates sending the SMS without making an actual request (useful for testing).
 * @param string|null $defaultCountryCode The default country code to be used if not provided in the $phone parameter.
 * @return bool Returns true if the SMS was successfully sent (or simulated), false otherwise.
 */
function send_sms($phone, $message, $fakeSend = false, $defaultCountryCode = '91')
{
    if ($fakeSend) {
        return true;
    }

    $apiKey = '0FVv8oYANkqsylRkCvxn1g';
    $senderId = 'MARNTE';
    $channel = 'Trans';
    $dcs = '0';
    $flashSms = '0';
    $route = '16';

    // Construct the full phone number with the default country code if not provided
    if (substr($phone, 0, strlen($defaultCountryCode)) !== $defaultCountryCode) {
        $phone = $defaultCountryCode . $phone;
    }

    //remove + from phone number
    $phone = str_replace('+', '', $phone);

    // Replace spaces in the message with URL encoding
    $message = urlencode($message);

    // Create the SMS API URL
    $url = "http://sms1.crct.in/api/mt/SendSMS?APIKey=$apiKey&senderid=$senderId&channel=$channel&DCS=$dcs&flashsms=$flashSms&number=$phone&text=$message&route=$route";

    // Initialize cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60); // Set maximum timeout to 60 seconds

    // Execute cURL request
    $response = curl_exec($ch);

    // Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    // Close cURL session
    curl_close($ch);

    return true;
}

/**
 * Hero showcase images (hero1.jpeg + hero2.jpeg).
 */
function hero_showcase_images()
{
    $images = array();
    $sets = array(
        array('hero1.jpeg', 'hero1.jpg'),
        array('hero2.jpeg', 'hero2.jpg'),
    );
    foreach ($sets as $candidates) {
        foreach ($candidates as $name) {
            $url = brand_image_url($name);
            if ($url) {
                $images[] = $url;
                break;
            }
        }
    }
    return $images;
}

/**
 * Resolve a brand image under assets/images or user_assets/images.
 */
function brand_image_url($filename)
{
    $paths = array(
        'assets/images/' . $filename,
        'user_assets/images/' . $filename,
    );
    foreach ($paths as $relative) {
        if (file_exists(FCPATH . $relative)) {
            return base_url($relative);
        }
    }
    return null;
}

/**
 * Brand logo URL (prefers logo.png from assets or user_assets).
 */
function brand_logo_url($variant = 'default')
{
    if ($variant === 'white') {
        foreach (array('logo-white.png', 'piko-pop-logo-white.png', 'piko-pop-logo-white.svg') as $name) {
            $url = brand_image_url($name);
            if ($url) {
                return $url;
            }
        }
    }

    $url = brand_image_url('logo.png');
    if ($url) {
        return $url;
    }

    foreach (array('piko-pop-logo.png', 'piko-pop-logo.svg') as $name) {
        $url = brand_image_url($name);
        if ($url) {
            return $url;
        }
    }

    return base_url('assets/images/logo.png');
}

/**
 * Razorpay Key ID from config (.env).
 */
function razorpay_key_id()
{
    return trim((string) config_item('razorpay_key_id'));
}

/**
 * Razorpay Key Secret from config (.env).
 */
function razorpay_key_secret()
{
    return trim((string) config_item('razorpay_key_secret'));
}

/**
 * Razorpay webhook secret from config (.env), if set in dashboard.
 */
function razorpay_webhook_secret()
{
    return trim((string) config_item('razorpay_webhook_secret'));
}

/**
 * Detect Razorpay mode from Key ID prefix: live, test, or unknown.
 */
function razorpay_mode()
{
    $key_id = razorpay_key_id();
    if (strpos($key_id, 'rzp_live_') === 0) {
        return 'live';
    }
    if (strpos($key_id, 'rzp_test_') === 0) {
        return 'test';
    }
    return 'unknown';
}

function razorpay_is_live()
{
    return razorpay_mode() === 'live';
}

function razorpay_is_test()
{
    return razorpay_mode() === 'test';
}

/**
 * Validate Razorpay keys before taking payment. Returns error message or null if OK.
 */
function validate_razorpay_config()
{
    $key_id = razorpay_key_id();
    $key_secret = razorpay_key_secret();

    if ($key_id === '' || $key_secret === '') {
        return 'Payment gateway is not configured. Please contact support.';
    }

    if (!preg_match('/^rzp_(test|live)_[A-Za-z0-9]+$/', $key_id)) {
        return 'Invalid RAZORPAY_KEY_ID in .env. It must start with rzp_live_ or rzp_test_.';
    }

    if (ENVIRONMENT === 'production' && razorpay_is_test()) {
        return 'Test Razorpay keys cannot be used in production. Set live keys (rzp_live_...) in server .env.';
    }

    return null;
}

/**
 * Create a configured Razorpay API client.
 */
function razorpay_api()
{
    return new Razorpay\Api\Api(razorpay_key_id(), razorpay_key_secret());
}