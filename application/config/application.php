<?php
defined('BASEPATH') or exit('No direct script access allowed');


$config['application_name'] = 'PIKO POP';
$config['application_name_font_end'] = 'PIKO POP';
$config['author']           = 'ZooBit Infotech';
$config['author_link']      = 'https://zoobitinfotech.com/';

$config['img_extensions'] = array("jpeg", "jpg", "png", "svg", "webp", "mp4");


$config['app_version'] = '1.0.9';

$config['api_authorization'] = 'd14e5fe6050fda6693e8f658a80879f236e36da3173b4009d56b8100e3782646';


//mail config
$config['email_username'] = 'connectbyqr@gmail.com';
$config['email_password'] = '****';
$config['email_host'] = 'smtp.gmail.com';
$config['email_port'] = 465;
$config['email_from'] = 'connectbyqr@gmail.com';
$config['admin_email'] = 'marin8.in@gmail.com';

//Shipping
$config['shipping_charge'] = 59.00; //in amount (INR)
$config['free_shipping_min_amount'] = 399.00; //Free shipping for orders above this amount (0 = no free shipping)
$config['shipping_enabled'] = true; //Enable/disable shipping charges


//Stripe
$config['stripe_publishable_key'] = '****';
$config['stripe_secret_key'] = '***';

//Razorpay (from .env — never commit secrets)
$app_env = static function ($key, $default = '') {
	$value = $_ENV[$key] ?? getenv($key);
	return ($value !== false && $value !== null && $value !== '') ? $value : $default;
};

$config['razorpay_key_id'] = $app_env('RAZORPAY_KEY_ID', '');
$config['razorpay_key_secret'] = $app_env('RAZORPAY_KEY_SECRET', '');

$config['otp_bypass_enabled'] = in_array(strtolower((string) $app_env('OTP_BYPASS', 'false')), array('1', 'true', 'yes'), true);
$config['otp_bypass_code'] = $app_env('OTP_BYPASS_CODE', '123456');

//Shiprocket
$config['shiprocket_email'] = 'chanudnaidu025@gmail.com';
$config['shiprocket_password'] = '*****';
$config['shiprocket_pickup_location'] = 'Home'; // Your pickup location name in Shiprocket
$config['shiprocket_webhook_auth_key'] = '******e'; // X-Api-Key for webhook verification
$config['shiprocket_company_id'] = '9017733';