-- PIKO POP / CodeIgniter 3 ecommerce — fresh local schema
-- Database name must stay pakodi_and_politics (app config)
-- Import: phpMyAdmin → pakodi_and_politics → Import, or:
--   C:\xampp\mysql\bin\mysql.exe -u root pakodi_and_politics < database/schema.sql

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------------
-- Admin
-- Default login: username admin / password admin123
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_admin_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admin` (`username`, `full_name`, `email`, `phone_number`, `password`, `status`) VALUES
('admin', 'PIKO POP Admin', 'admin@pikopop.local', '9999999999', MD5('admin123'), 1);

-- ---------------------------------------------------------------------------
-- Customers
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `fullname` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expires` datetime DEFAULT NULL,
  `reset_token` varchar(100) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `verification_token` varchar(100) DEFAULT NULL,
  `verification_token_expires` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `is_active` tinyint NOT NULL DEFAULT 1,
  `is_verified` tinyint NOT NULL DEFAULT 0,
  `phone_verified` tinyint NOT NULL DEFAULT 0,
  `wallet_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `login_attempts` int NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_users_phone` (`phone`),
  KEY `idx_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `user_addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `address_type` varchar(30) DEFAULT 'home',
  `fullname` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) NOT NULL,
  `landmark` varchar(255) DEFAULT NULL,
  `is_default` tinyint NOT NULL DEFAULT 0,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_addresses_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Catalog
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `category_name` varchar(150) NOT NULL,
  `parent_category_id` int unsigned NOT NULL DEFAULT 0,
  `icon` varchar(255) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT 0,
  `featured` tinyint NOT NULL DEFAULT 0,
  `is_menu_item` tinyint NOT NULL DEFAULT 0,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_categories_parent` (`parent_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) NOT NULL,
  `mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_variant` tinyint NOT NULL DEFAULT 0,
  `brand` varchar(100) DEFAULT NULL,
  `category_id` int unsigned NOT NULL DEFAULT 0,
  `product_weight` varchar(50) DEFAULT NULL,
  `weight_unit` varchar(20) DEFAULT NULL,
  `short_description` text,
  `description` longtext,
  `meta_data` longtext,
  `images` longtext,
  `instructions_image_desktop` varchar(255) DEFAULT NULL,
  `instructions_image_mobile` varchar(255) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_products_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `product_variants` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `variant_name` varchar(150) NOT NULL,
  `mrp` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `sku` varchar(100) DEFAULT NULL,
  `servers_text` varchar(255) DEFAULT NULL,
  `package_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_variants_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `banners` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `desktop_image` varchar(255) DEFAULT NULL,
  `mobile_image` varchar(255) DEFAULT NULL,
  `target_link` varchar(500) DEFAULT NULL,
  `sort_order` int NOT NULL DEFAULT 0,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Cart & wishlist
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cart` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `variant_id` int unsigned DEFAULT NULL,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_cart_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wishlist_user_product` (`user_id`,`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Orders
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `user_id` int unsigned NOT NULL,
  `subtotal` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `coupon_id` int unsigned DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `delivery_charge` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_fullname` varchar(150) DEFAULT NULL,
  `delivery_phone` varchar(20) DEFAULT NULL,
  `delivery_address` text,
  `delivery_city` varchar(100) DEFAULT NULL,
  `delivery_state` varchar(100) DEFAULT NULL,
  `delivery_pincode` varchar(10) DEFAULT NULL,
  `delivery_landmark` varchar(255) DEFAULT NULL,
  `order_status` varchar(30) NOT NULL DEFAULT 'pending',
  `payment_status` varchar(30) NOT NULL DEFAULT 'pending',
  `payment_method` varchar(30) DEFAULT 'online',
  `payment_id` varchar(100) DEFAULT NULL,
  `refund_status` varchar(30) DEFAULT NULL,
  `refund_id` varchar(100) DEFAULT NULL,
  `sr_order_id` varchar(50) DEFAULT NULL,
  `sr_shipment_id` varchar(50) DEFAULT NULL,
  `sr_awb` varchar(50) DEFAULT NULL,
  `sr_courier_name` varchar(100) DEFAULT NULL,
  `customer_notes` text,
  `admin_notes` text,
  `cancellation_reason` text,
  `processed_at` datetime DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `out_for_delivery_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_orders_number` (`order_number`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`order_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `variant_id` int unsigned DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `variant_name` varchar(150) DEFAULT NULL,
  `product_image` varchar(255) DEFAULT NULL,
  `unit_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(10,2) NOT NULL DEFAULT 1.00,
  `total_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `package_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `total_package_weight` decimal(10,3) NOT NULL DEFAULT 0.000,
  `stock_deducted` tinyint NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_items_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned NOT NULL,
  `status` varchar(30) NOT NULL,
  `notes` text,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_order_history_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `order_reviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int unsigned DEFAULT NULL,
  `review_token` varchar(64) NOT NULL,
  `customer_name` varchar(150) DEFAULT NULL,
  `overall_rating` tinyint DEFAULT NULL,
  `taste_feedback` varchar(50) DEFAULT NULL,
  `ease_of_cooking` varchar(50) DEFAULT NULL,
  `restaurant_taste` varchar(50) DEFAULT NULL,
  `instructions_clarity` varchar(50) DEFAULT NULL,
  `packaging_feedback` varchar(50) DEFAULT NULL,
  `value_for_money` varchar(50) DEFAULT NULL,
  `recommendation` varchar(50) DEFAULT NULL,
  `repeat_purchase` varchar(50) DEFAULT NULL,
  `liked_most` text,
  `improvement` text,
  `guest_phone` varchar(20) DEFAULT NULL,
  `review_image` varchar(255) DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_review_token` (`review_token`),
  KEY `idx_reviews_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Coupons
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `coupons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `coupon_code` varchar(50) NOT NULL,
  `coupon_type` varchar(20) NOT NULL DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `min_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `max_discount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `usage_limit` int NOT NULL DEFAULT 0,
  `used_count` int NOT NULL DEFAULT 0,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_coupon_code` (`coupon_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `coupon_usage` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int unsigned NOT NULL,
  `user_id` int unsigned NOT NULL,
  `order_id` int unsigned NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `used_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_coupon_usage_coupon` (`coupon_id`),
  KEY `idx_coupon_usage_order` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Inventory logs
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `stock_logs` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int unsigned NOT NULL,
  `previous_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `added_qty` decimal(10,2) NOT NULL DEFAULT 0.00,
  `new_stock` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` varchar(500) DEFAULT NULL,
  `added_by` int unsigned DEFAULT NULL,
  `added_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_stock_logs_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Recipes (Ideas & Inspiration)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `recipes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text,
  `youtube_url` varchar(500) DEFAULT NULL,
  `youtube_video_id` varchar(50) DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `prep_time_minutes` int NOT NULL DEFAULT 0,
  `cook_time_minutes` int NOT NULL DEFAULT 0,
  `servings` int NOT NULL DEFAULT 0,
  `difficulty` varchar(20) DEFAULT 'easy',
  `ingredients` longtext,
  `sort_order` int NOT NULL DEFAULT 0,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_recipes_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `recipe_products` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `recipe_id` int unsigned NOT NULL,
  `product_id` int unsigned NOT NULL,
  `default_quantity` int NOT NULL DEFAULT 1,
  `sort_order` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_recipe_products_recipe` (`recipe_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Admin reports / B2B clients
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `expenses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `expense_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `comments` text,
  `created_by` int unsigned DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `legal_name` varchar(255) DEFAULT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `alternate_phone` varchar(20) DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `pincode` varchar(20) DEFAULT NULL,
  `gst_number` varchar(50) DEFAULT NULL,
  `pan_number` varchar(50) DEFAULT NULL,
  `fssai_license` varchar(50) DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT 1,
  `is_verified` tinyint NOT NULL DEFAULT 0,
  `verified_at` datetime DEFAULT NULL,
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_clients_code` (`client_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `client_users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int unsigned NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_client_users_client` (`client_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- API tokens (Shiprocket)
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `tokens` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `api_name` varchar(50) NOT NULL,
  `token` text NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tokens_api` (`api_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- Analytics
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `analytics_visitors` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `visitor_uuid` varchar(64) NOT NULL,
  `fingerprint` varchar(255) DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `first_seen` datetime DEFAULT NULL,
  `last_seen` datetime DEFAULT NULL,
  `first_referrer` text,
  `first_landing_page` text,
  `first_utm_source` varchar(100) DEFAULT NULL,
  `first_utm_medium` varchar(100) DEFAULT NULL,
  `first_utm_campaign` varchar(100) DEFAULT NULL,
  `total_sessions` int NOT NULL DEFAULT 0,
  `total_page_views` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_visitor_uuid` (`visitor_uuid`),
  KEY `idx_visitor_fingerprint` (`fingerprint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `analytics_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_uuid` varchar(64) NOT NULL,
  `visitor_id` int unsigned NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `last_activity` datetime DEFAULT NULL,
  `landing_page` text,
  `exit_page` text,
  `referrer` text,
  `utm_source` varchar(100) DEFAULT NULL,
  `utm_medium` varchar(100) DEFAULT NULL,
  `utm_campaign` varchar(100) DEFAULT NULL,
  `device_type` varchar(20) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `os` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `screen_resolution` varchar(30) DEFAULT NULL,
  `page_views` int NOT NULL DEFAULT 0,
  `duration_seconds` int NOT NULL DEFAULT 0,
  `is_bounce` tinyint NOT NULL DEFAULT 1,
  `has_cart_activity` tinyint NOT NULL DEFAULT 0,
  `has_checkout` tinyint NOT NULL DEFAULT 0,
  `has_purchase` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_session_uuid` (`session_uuid`),
  KEY `idx_sessions_visitor` (`visitor_id`),
  KEY `idx_sessions_started` (`started_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `analytics_pageviews` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int unsigned NOT NULL,
  `visitor_id` int unsigned NOT NULL,
  `page_url` text,
  `page_type` varchar(30) DEFAULT NULL,
  `referrer_url` text,
  `product_id` int unsigned DEFAULT NULL,
  `category_id` int unsigned DEFAULT NULL,
  `search_query` varchar(255) DEFAULT NULL,
  `time_on_page` int NOT NULL DEFAULT 0,
  `viewed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pageviews_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `analytics_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int unsigned NOT NULL,
  `visitor_id` int unsigned NOT NULL,
  `event_type` varchar(50) NOT NULL,
  `event_label` varchar(255) DEFAULT NULL,
  `event_value` decimal(10,2) DEFAULT NULL,
  `page_url` text,
  `product_id` int unsigned DEFAULT NULL,
  `variant_id` int unsigned DEFAULT NULL,
  `order_id` int unsigned DEFAULT NULL,
  `metadata` text,
  `occurred_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_events_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- Starter PIKO POP catalog (optional — edit/remove in admin)
-- ---------------------------------------------------------------------------
INSERT INTO `categories` (`category_name`, `parent_category_id`, `sort_order`, `featured`, `is_menu_item`, `status`) VALUES
('Stickers', 0, 1, 1, 1, 1),
('Stationery', 0, 2, 1, 1, 1),
('Toys & Collectibles', 0, 3, 1, 1, 1),
('Gifts & Party', 0, 4, 0, 1, 1);

INSERT INTO `products` (`product_name`, `mrp`, `sale_price`, `stock`, `category_id`, `short_description`, `description`, `images`, `status`) VALUES
('Rainbow Sticker Pack', 199.00, 149.00, 50, 1, '12 cute rainbow stickers for notebooks & bottles.', 'A cheerful PIKO POP sticker pack — peel, stick, smile!', '[]', 1),
('Kawaii Pen Set', 299.00, 249.00, 30, 2, 'Set of 4 pastel gel pens with cute toppers.', 'Smooth-writing pens perfect for school and journaling.', '[]', 1),
('Mini Plush Keychain', 349.00, 299.00, 25, 3, 'Soft mini plush — clip on bag or keys.', 'Collectible plush keychain from PIKO POP.', '[]', 1);

INSERT INTO `coupons` (`title`, `coupon_code`, `coupon_type`, `discount_value`, `min_amount`, `max_discount`, `usage_limit`, `used_count`, `start_date`, `status`, `created_by`) VALUES
('Welcome 10% Off', 'PIKOPOP10', 'percent', 10.00, 299.00, 100.00, 100, 0, CURDATE(), 1, 1);
