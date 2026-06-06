<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Cart_model', 'cart_model');
        $this->load->model('Common_model', 'common');
    }

    /**
     * Cart page
     * URL: /cart
     */
    public function index()
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        $user_id = get_user_sessiondata('id');

        // Get cart items with product and variant details
        $cart_items = $this->cart_model->get_cart_items($user_id);

        // Prepare data for view
        $header_data['page_title'] = 'Shopping Cart';
        $data['cart_items'] = $cart_items;

        // Enable cart functionality
        $footer_data['cart_validation'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/cart/index', $data);
        $this->load->view('user/common/footer', $footer_data);
        $this->load->view('user/cart/cart_js');
    }

    /**
     * Get cart data (for AJAX)
     */
    public function get_cart()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login to view cart.'));
            return;
        }

        $user_id = get_user_sessiondata('id');
        $cart_items = $this->cart_model->get_cart_items($user_id);

        echo json_encode(array(
            'status' => 200,
            'cart_items' => $cart_items,
            'cart_count' => count($cart_items),
            'cart_total' => $this->calculate_cart_total($cart_items)
        ));
    }

    /**
     * Add to cart (AJAX)
     */
    public function add()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login to add items to cart.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        if (empty($post_data['product_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Product ID is required.'));
            return;
        }

        $product_id = intval($post_data['product_id']);
        $variant_id = !empty($post_data['variant_id']) ? intval($post_data['variant_id']) : null;
        $quantity = !empty($post_data['quantity']) ? floatval($post_data['quantity']) : 1;

        // Get product details
        $product = $this->common->getdatabytable('products', array('id' => $product_id, 'status' => 1));

        if (empty($product)) {
            echo json_encode(array('status' => 400, 'message' => 'Product not found.'));
            return;
        }

        // Check if product has variants but variant_id not provided
        $product_variants = $this->common->getdatabytableall('product_variants', array('product_id' => $product_id), 'id', 'ASC');

        if (!empty($product_variants) && empty($variant_id)) {
            // Return variants for selection
            echo json_encode(array(
                'status' => 300,
                'message' => 'Please select a variant.',
                'variants' => $product_variants,
                'product' => $product
            ));
            return;
        }

        // Get price and stock info based on variant or product
        $stock_deduct_qty = 1; // How much to deduct from main stock per unit
        if (!empty($variant_id)) {
            $variant = $this->common->getdatabytable('product_variants', array('id' => $variant_id, 'product_id' => $product_id));
            if (empty($variant)) {
                echo json_encode(array('status' => 400, 'message' => 'Variant not found.'));
                return;
            }
            $unit_price = !empty($variant->sale_price) ? $variant->sale_price : $variant->mrp;
            // stock_qty is the deduction amount from main product stock
            $stock_deduct_qty = floatval($variant->stock_qty);
            // Available units = main product stock / deduction per unit
            $stock_available = floor($product->stock / $stock_deduct_qty);
        } else {
            $unit_price = !empty($product->sale_price) ? $product->sale_price : $product->mrp;
            $stock_available = $product->stock;
        }

        // Check stock availability
        if ($stock_available < $quantity) {
            echo json_encode(array('status' => 400, 'message' => 'Insufficient stock. Only ' . $stock_available . ' available.'));
            return;
        }

        // Check if item already in cart
        $existing_cart = $this->cart_model->get_cart_item($user_id, $product_id, $variant_id);

        if (!empty($existing_cart)) {
            // Update quantity
            $new_quantity = $existing_cart->quantity + $quantity;

            // Check stock for new quantity
            if ($stock_available < $new_quantity) {
                echo json_encode(array('status' => 400, 'message' => 'Cannot add more. Only ' . $stock_available . ' available.'));
                return;
            }

            $this->cart_model->update_item($existing_cart->id, array(
                'quantity' => $new_quantity,
                'unit_price' => $unit_price,
                'updated_at' => date('Y-m-d H:i:s')
            ));

            $message = 'Cart updated successfully.';
        } else {
            // Insert new cart item
            $cart_data = array(
                'user_id' => $user_id,
                'product_id' => $product_id,
                'variant_id' => $variant_id,
                'quantity' => $quantity,
                'unit_price' => $unit_price,
                'created_at' => date('Y-m-d H:i:s')
            );

            $this->cart_model->add_item($cart_data);
            $message = 'Item added to cart.';
        }

        // Get updated cart info
        $cart_count = get_cart_count();
        $cart_items = $this->cart_model->get_cart_items($user_id);

        echo json_encode(array(
            'status' => 200,
            'message' => $message,
            'cart_count' => $cart_count,
            'cart_total' => $this->calculate_cart_total($cart_items),
            'item_quantity' => $this->cart_model->get_item_quantity($user_id, $product_id, $variant_id)
        ));
    }

    /**
     * Update cart quantity (AJAX)
     */
    public function update_quantity()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        if (empty($post_data['product_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Product ID is required.'));
            return;
        }

        $product_id = intval($post_data['product_id']);
        $variant_id = !empty($post_data['variant_id']) ? intval($post_data['variant_id']) : null;
        $action = !empty($post_data['action']) ? $post_data['action'] : 'increment';
        $quantity = !empty($post_data['quantity']) ? floatval($post_data['quantity']) : null;

        // Get cart item
        $cart_item = $this->cart_model->get_cart_item($user_id, $product_id, $variant_id);

        if (empty($cart_item)) {
            echo json_encode(array('status' => 400, 'message' => 'Item not in cart.'));
            return;
        }

        // Calculate new quantity
        if ($quantity !== null) {
            $new_quantity = $quantity;
        } else if ($action === 'increment') {
            $new_quantity = $cart_item->quantity + 1;
        } else if ($action === 'decrement') {
            $new_quantity = $cart_item->quantity - 1;
        } else {
            $new_quantity = $cart_item->quantity;
        }

        // If quantity is 0 or less, remove item
        if ($new_quantity <= 0) {
            $this->cart_model->remove_item($cart_item->id);

            $cart_count = get_cart_count();
            $cart_items = $this->cart_model->get_cart_items($user_id);

            echo json_encode(array(
                'status' => 200,
                'message' => 'Item removed from cart.',
                'cart_count' => $cart_count,
                'cart_total' => $this->calculate_cart_total($cart_items),
                'item_quantity' => 0,
                'item_removed' => true
            ));
            return;
        }

        // Check stock availability
        $product = $this->common->getdatabytable('products', array('id' => $product_id));
        if (!$product) {
            echo json_encode(array('status' => 400, 'message' => 'Product not found.'));
            return;
        }

        if ($variant_id) {
            $variant = $this->common->getdatabytable('product_variants', array('id' => $variant_id));
            if (!$variant) {
                echo json_encode(array('status' => 400, 'message' => 'Variant not found.'));
                return;
            }
            // stock_qty is deduction amount, calculate available units
            $stock_deduct_qty = floatval($variant->stock_qty);
            $stock_available = floor($product->stock / $stock_deduct_qty);
        } else {
            $stock_available = $product->stock;
        }

        if ($stock_available < $new_quantity) {
            echo json_encode(array('status' => 400, 'message' => 'Insufficient stock. Only ' . $stock_available . ' available.'));
            return;
        }

        // Update quantity
        $this->cart_model->update_item($cart_item->id, array(
            'quantity' => $new_quantity,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        $cart_count = get_cart_count();
        $cart_items = $this->cart_model->get_cart_items($user_id);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Cart updated.',
            'cart_count' => $cart_count,
            'cart_total' => $this->calculate_cart_total($cart_items),
            'item_quantity' => $new_quantity,
            'item_total' => $new_quantity * $cart_item->unit_price
        ));
    }

    /**
     * Remove from cart (AJAX)
     */
    public function remove()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        if (empty($post_data['product_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Product ID is required.'));
            return;
        }

        $product_id = intval($post_data['product_id']);
        $variant_id = !empty($post_data['variant_id']) ? intval($post_data['variant_id']) : null;

        $this->cart_model->remove_by_product($user_id, $product_id, $variant_id);

        $cart_count = get_cart_count();
        $cart_items = $this->cart_model->get_cart_items($user_id);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Item removed from cart.',
            'cart_count' => $cart_count,
            'cart_total' => $this->calculate_cart_total($cart_items)
        ));
    }

    /**
     * Remove from cart by cart ID (AJAX)
     * Used for header dropdown remove buttons
     */
    public function remove_by_id()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        if (empty($post_data['cart_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Cart ID is required.'));
            return;
        }

        $cart_id = intval($post_data['cart_id']);

        // Verify the cart item belongs to this user
        $cart_item = $this->cart_model->get_cart_item_by_id($cart_id, $user_id);

        if (empty($cart_item)) {
            echo json_encode(array('status' => 400, 'message' => 'Item not found.'));
            return;
        }

        $this->cart_model->remove_item($cart_id);

        $cart_count = get_cart_count();
        $cart_items = $this->cart_model->get_cart_items($user_id);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Item removed from cart.',
            'cart_count' => $cart_count,
            'cart_total' => $this->calculate_cart_total($cart_items)
        ));
    }

    /**
     * Get item quantity in cart
     */
    public function get_item_info()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'quantity' => 0));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        $product_id = !empty($post_data['product_id']) ? intval($post_data['product_id']) : 0;
        $variant_id = !empty($post_data['variant_id']) ? intval($post_data['variant_id']) : null;

        $quantity = $this->cart_model->get_item_quantity($user_id, $product_id, $variant_id);

        echo json_encode(array(
            'status' => 200,
            'quantity' => $quantity,
            'in_cart' => $quantity > 0
        ));
    }

    /**
     * Get product variants (AJAX)
     */
    public function get_variants()
    {
        $post_data = $this->input->post(null, true);

        if (empty($post_data['product_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Product ID is required.'));
            return;
        }

        $product_id = intval($post_data['product_id']);

        $product = $this->common->getdatabytable('products', array('id' => $product_id, 'status' => 1));
        $variants = $this->common->getdatabytableall('product_variants', array('product_id' => $product_id), 'mrp', 'ASC');

        echo json_encode(array(
            'status' => 200,
            'product' => $product,
            'variants' => $variants,
            'has_variants' => !empty($variants)
        ));
    }

    /**
     * Get cart dropdown HTML (AJAX)
     * Returns rendered HTML for the header cart dropdown
     */
    public function get_dropdown_html()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'html' => ''));
            return;
        }

        $user_id = get_user_sessiondata('id');
        $cart_items = $this->cart_model->get_all_cart_items($user_id);
        $cart_subtotal = 0;

        $html = '';

        if (!empty($cart_items)) {
            $html .= '<ul class="cart-list">';

            // Show max 3 items
            $display_items = array_slice($cart_items, 0, 3);

            foreach ($display_items as $cart_item) {
                $product = $this->common->getdatabytable('products', array('id' => $cart_item->product_id));
                if ($product) {
                    $images = json_decode($product->images);
                    $product_image = !empty($images[0]) ? base_url('uploads/products/') . $images[0] : base_url('user_assets/images/placeholder.png');
                    $product_link = product_url($product);

                    $html .= '<li class="product-box-contain">';
                    $html .= '<div class="drop-cart">';
                    $html .= '<a href="' . $product_link . '" class="drop-image">';
                    $html .= '<img src="' . $product_image . '" class="blur-up lazyload" alt="' . htmlspecialchars($product->product_name) . '">';
                    $html .= '</a>';
                    $html .= '<div class="drop-contain">';
                    $html .= '<a href="' . $product_link . '">';
                    $html .= '<h5>' . htmlspecialchars($product->product_name) . '</h5>';
                    $html .= '</a>';
                    $html .= '<h6><span>' . number_format($cart_item->quantity, 0) . ' x</span> $' . number_format($cart_item->unit_price, 2) . '</h6>';
                    $html .= '<button class="close-button close_button" onclick="removeFromCart(' . $cart_item->id . ')">';
                    $html .= '<i class="fa-solid fa-xmark"></i>';
                    $html .= '</button>';
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</li>';
                }
            }

            $html .= '</ul>';

            // Calculate subtotal
            foreach ($cart_items as $item) {
                $cart_subtotal += ($item->unit_price * $item->quantity);
            }

            // Check for applied coupon
            $coupon_discount = 0;
            $applied_coupon = $this->session->userdata('applied_coupon');
            if (!empty($applied_coupon) && isset($applied_coupon['discount_amount'])) {
                // Re-validate coupon with current subtotal
                $this->load->library('CouponLib');
                $result = $this->couponlib->validateCoupon($applied_coupon['coupon_code'], $cart_subtotal, $user_id);
                if ($result['valid']) {
                    $coupon_discount = $result['discount_amount'];
                }
            }

            // Calculate shipping based on subtotal
            $shipping_info = get_shipping_info($cart_subtotal);
            $shipping = $shipping_info['charge'];

            // Final total
            $cart_total = $cart_subtotal - $coupon_discount + $shipping;

            $html .= '<div class="price-box">';
            $html .= '<h5>Total :</h5>';
            $html .= '<h4 class="theme-color fw-bold" id="cart-total">$' . number_format($cart_total, 2) . '</h4>';
            $html .= '</div>';

            $html .= '<div class="button-group">';
            $html .= '<a href="' . base_url('cart') . '" class="btn btn-sm cart-button">View Cart</a>';
            $html .= '<a href="' . base_url('checkout') . '" class="btn btn-sm cart-button theme-bg-color text-white">Checkout</a>';
            $html .= '</div>';
        } else {
            $html .= '<div class="text-center p-3">';
            $html .= '<p class="text-muted">Your cart is empty</p>';
            $html .= '<a href="' . base_url('shop') . '" class="btn btn-sm theme-bg-color text-white">Shop Now</a>';
            $html .= '</div>';
            $cart_total = 0;
        }

        echo json_encode(array(
            'status' => 200,
            'html' => $html,
            'cart_count' => count($cart_items),
            'cart_total' => number_format($cart_total, 2)
        ));
    }

    /**
     * Get product variants with cart info (AJAX)
     */
    public function get_variants_with_cart()
    {
        $post_data = $this->input->post(null, true);

        if (empty($post_data['product_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Product ID is required.'));
            return;
        }

        $product_id = intval($post_data['product_id']);

        $product = $this->common->getdatabytable('products', array('id' => $product_id, 'status' => 1));
        $variants = $this->common->getdatabytableall('product_variants', array('product_id' => $product_id), 'mrp', 'ASC');

        // Calculate available stock for each variant based on main product stock
        if (!empty($variants) && $product) {
            foreach ($variants as &$variant) {
                $stock_deduct_qty = floatval($variant->stock_qty);
                if ($stock_deduct_qty > 0) {
                    $variant->available_stock = floor($product->stock / $stock_deduct_qty);
                } else {
                    $variant->available_stock = 0;
                }
            }
            unset($variant);
        }

        // Get cart items for this product (if user is logged in)
        $cart_items = array();
        if (is_loggedin_user()) {
            $user_id = get_user_sessiondata('id');
            $cart_items = $this->cart_model->get_product_cart_items($user_id, $product_id);
        }

        echo json_encode(array(
            'status' => 200,
            'product' => $product,
            'variants' => $variants,
            'has_variants' => !empty($variants),
            'cart_items' => $cart_items
        ));
    }

    // ==================== COUPON METHODS ====================

    /**
     * Apply coupon to cart (AJAX)
     */
    public function apply_coupon()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login to apply coupon.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');
        $coupon_code = !empty($post_data['coupon_code']) ? trim($post_data['coupon_code']) : '';

        if (empty($coupon_code)) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter a coupon code.'));
            return;
        }

        // Get cart items and calculate subtotal
        $cart_items = $this->cart_model->get_cart_items($user_id);
        if (empty($cart_items)) {
            echo json_encode(array('status' => 400, 'message' => 'Your cart is empty.'));
            return;
        }

        $subtotal = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
        }

        // Load coupon library and validate
        $this->load->library('CouponLib');
        $result = $this->couponlib->validateCoupon($coupon_code, $subtotal, $user_id);

        if ($result['valid']) {
            // Store coupon in session for checkout
            $this->session->set_userdata('applied_coupon', array(
                'coupon_id' => $result['coupon']->id,
                'coupon_code' => $result['coupon']->coupon_code,
                'discount_amount' => $result['discount_amount'],
                'coupon_title' => $result['coupon']->title
            ));

            // Calculate shipping based on original subtotal (before coupon discount)
            $shipping_info = get_shipping_info($subtotal);
            $total_with_shipping = $result['final_amount'] + $shipping_info['charge'];

            echo json_encode(array(
                'status' => 200,
                'message' => $result['message'],
                'coupon_code' => $result['coupon']->coupon_code,
                'coupon_title' => $result['coupon']->title,
                'discount_amount' => $result['discount_amount'],
                'subtotal' => number_format($subtotal, 2),
                'shipping' => $shipping_info['charge'],
                'shipping_display' => $shipping_info['charge'] > 0 ? '$' . number_format($shipping_info['charge'], 2) : 'Free',
                'shipping_message' => $shipping_info['message'],
                'total' => number_format($total_with_shipping, 2)
            ));
        } else {
            // Clear any previously applied coupon
            $this->session->unset_userdata('applied_coupon');

            echo json_encode(array(
                'status' => 400,
                'message' => $result['message']
            ));
        }
    }

    /**
     * Remove applied coupon (AJAX)
     */
    public function remove_coupon()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $user_id = get_user_sessiondata('id');

        // Clear coupon from session
        $this->session->unset_userdata('applied_coupon');

        // Get updated cart totals
        $cart_items = $this->cart_model->get_cart_items($user_id);
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
        }

        // Calculate shipping
        $shipping_info = get_shipping_info($subtotal);
        $total = $subtotal + $shipping_info['charge'];

        echo json_encode(array(
            'status' => 200,
            'message' => 'Coupon removed successfully.',
            'subtotal' => number_format($subtotal, 2),
            'shipping' => $shipping_info['charge'],
            'shipping_display' => $shipping_info['charge'] > 0 ? '$' . number_format($shipping_info['charge'], 2) : 'Free',
            'shipping_message' => $shipping_info['message'],
            'total' => number_format($total, 2)
        ));
    }

    /**
     * Get applied coupon info (AJAX)
     */
    public function get_applied_coupon()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'has_coupon' => false));
            return;
        }

        $applied_coupon = $this->session->userdata('applied_coupon');

        if (!empty($applied_coupon)) {
            // Re-validate coupon (in case cart changed)
            $user_id = get_user_sessiondata('id');
            $cart_items = $this->cart_model->get_cart_items($user_id);
            $subtotal = 0;
            foreach ($cart_items as $item) {
                $subtotal += $item->quantity * $item->unit_price;
            }

            $this->load->library('CouponLib');
            $result = $this->couponlib->validateCoupon($applied_coupon['coupon_code'], $subtotal, $user_id);

            if ($result['valid']) {
                // Update session with new discount amount (cart may have changed)
                $this->session->set_userdata('applied_coupon', array(
                    'coupon_id' => $result['coupon']->id,
                    'coupon_code' => $result['coupon']->coupon_code,
                    'discount_amount' => $result['discount_amount'],
                    'coupon_title' => $result['coupon']->title
                ));

                // Calculate shipping based on original subtotal (before coupon discount)
                $shipping_info = get_shipping_info($subtotal);
                $total_with_shipping = $result['final_amount'] + $shipping_info['charge'];

                echo json_encode(array(
                    'status' => 200,
                    'has_coupon' => true,
                    'coupon_code' => $result['coupon']->coupon_code,
                    'coupon_title' => $result['coupon']->title,
                    'discount_amount' => $result['discount_amount'],
                    'subtotal' => number_format($subtotal, 2),
                    'shipping' => $shipping_info['charge'],
                    'shipping_display' => $shipping_info['charge'] > 0 ? '$' . number_format($shipping_info['charge'], 2) : 'Free',
                    'shipping_message' => $shipping_info['message'],
                    'total' => number_format($total_with_shipping, 2)
                ));
            } else {
                // Coupon no longer valid, remove it
                $this->session->unset_userdata('applied_coupon');
                echo json_encode(array(
                    'status' => 200,
                    'has_coupon' => false,
                    'message' => $result['message']
                ));
            }
        } else {
            echo json_encode(array('status' => 200, 'has_coupon' => false));
        }
    }

    /**
     * Get available coupons for cart (AJAX)
     */
    public function get_available_coupons()
    {
        $user_id = is_loggedin_user() ? get_user_sessiondata('id') : null;

        // Get cart subtotal
        $subtotal = 0;
        if ($user_id) {
            $cart_items = $this->cart_model->get_cart_items($user_id);
            foreach ($cart_items as $item) {
                $subtotal += $item->quantity * $item->unit_price;
            }
        }

        $this->load->library('CouponLib');
        $coupons = $this->couponlib->getAvailableCoupons($subtotal, $user_id);

        $formatted_coupons = array();
        foreach ($coupons as $coupon) {
            $formatted_coupons[] = array(
                'coupon_code' => $coupon->coupon_code,
                'title' => $coupon->title,
                'discount_text' => $this->couponlib->getDiscountText($coupon),
                'min_amount' => $coupon->min_amount,
                'potential_discount' => isset($coupon->potential_discount) ? $coupon->potential_discount : 0
            );
        }

        echo json_encode(array(
            'status' => 200,
            'coupons' => $formatted_coupons,
            'subtotal' => $subtotal
        ));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get cart summary (AJAX)
     * Returns cart totals for refreshing the summary section
     */
    public function get_cart_summary()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $user_id = get_user_sessiondata('id');
        $cart_items = $this->cart_model->get_cart_items($user_id);

        // Calculate subtotal and item count
        $subtotal = 0;
        $item_count = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
            $item_count += $item->quantity;
        }

        // Get applied coupon discount
        $discount = 0;
        $applied_coupon = $this->session->userdata('applied_coupon');
        if (!empty($applied_coupon)) {
            // Re-validate coupon with current subtotal
            $this->load->library('CouponLib');
            $result = $this->couponlib->validateCoupon($applied_coupon['coupon_code'], $subtotal, $user_id);
            if ($result['valid']) {
                $discount = $result['discount_amount'];
                // Update session with new discount amount
                $this->session->set_userdata('applied_coupon', array(
                    'coupon_id' => $result['coupon']->id,
                    'coupon_code' => $result['coupon']->coupon_code,
                    'discount_amount' => $result['discount_amount'],
                    'coupon_title' => $result['coupon']->title
                ));
            } else {
                // Coupon no longer valid, remove it
                $this->session->unset_userdata('applied_coupon');
            }
        }

        // Calculate shipping using helper function
        $shipping_info = get_shipping_info($subtotal);
        $shipping = $shipping_info['charge'];
        $amount_for_free_shipping = $shipping_info['amount_to_free'];

        // Calculate total
        $total = $subtotal - $discount + $shipping;

        echo json_encode(array(
            'status' => 200,
            'item_count' => $item_count,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'amount_for_free_shipping' => $amount_for_free_shipping,
            'total' => $total
        ));
    }

    /**
     * Calculate cart total
     */
    private function calculate_cart_total($cart_items)
    {
        $total = 0;
        foreach ($cart_items as $item) {
            $total += $item->quantity * $item->unit_price;
        }
        return number_format($total, 2);
    }
}
