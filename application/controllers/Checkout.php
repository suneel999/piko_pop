<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Razorpay\Api\Api;

class Checkout extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Checkout_model', 'checkout_model');
        $this->load->model('Cart_model', 'cart_model');
        $this->load->model('Common_model', 'common');
        $this->load->library('CouponLib');
    }

    /**
     * Checkout page
     * URL: /checkout
     */
    public function index()
    {
        if (!is_loggedin_user()) {
            redirect('login?redirect=checkout');
        }

        $user_id = get_user_sessiondata('id');

        // Get cart items
        $cart_items = $this->cart_model->get_cart_items($user_id);

        // If cart is empty, redirect to cart page
        if (empty($cart_items)) {
            redirect('cart');
        }

        // Get user addresses
        $addresses = $this->checkout_model->get_user_addresses($user_id);

        // Calculate totals
        $subtotal = 0;
        $item_count = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
            $item_count += $item->quantity;
        }

        // Get shipping info
        $shipping_info = get_shipping_info($subtotal);
        $shipping = $shipping_info['charge'];

        // Check for applied coupon
        $coupon_discount = 0;
        $applied_coupon = $this->session->userdata('applied_coupon');
        if (!empty($applied_coupon)) {
            // Re-validate coupon
            $result = $this->couponlib->validateCoupon($applied_coupon['coupon_code'], $subtotal, $user_id);
            if ($result['valid']) {
                $coupon_discount = $result['discount_amount'];
                // Update session with recalculated discount
                $this->session->set_userdata('applied_coupon', array(
                    'coupon_id' => $result['coupon']->id,
                    'coupon_code' => $result['coupon']->coupon_code,
                    'discount_amount' => $result['discount_amount'],
                    'coupon_title' => $result['coupon']->title
                ));
            } else {
                // Coupon no longer valid
                $this->session->unset_userdata('applied_coupon');
            }
        }

        $total = $subtotal - $coupon_discount + $shipping;

        // Prepare data for view
        $header_data['title'] = 'Checkout';
        $data['cart_items'] = $cart_items;
        $data['item_count'] = $item_count;
        $data['addresses'] = $addresses;
        $data['subtotal'] = $subtotal;
        $data['shipping'] = $shipping;
        $data['shipping_info'] = $shipping_info;
        $data['coupon_discount'] = $coupon_discount;
        $data['applied_coupon'] = $applied_coupon;
        $data['total'] = $total;

        // Razorpay key for JS
        $data['razorpay_key_id'] = config_item('razorpay_key_id');

        $footer_data['cart_validation'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/checkout/index', $data);
        $this->load->view('user/common/footer', $footer_data);
        $this->load->view('user/checkout/checkout_js', $data);
    }

    // ==================== ADDRESS MANAGEMENT ====================

    /**
     * Get user addresses (AJAX)
     */
    public function get_addresses()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $user_id = get_user_sessiondata('id');
        $addresses = $this->checkout_model->get_user_addresses($user_id);

        echo json_encode(array(
            'status' => 200,
            'addresses' => $addresses
        ));
    }

    /**
     * Add new address (AJAX)
     */
    public function add_address()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        // Validation
        if (empty($post_data['fullname']) || empty($post_data['phone']) || empty($post_data['address_line1']) || empty($post_data['city']) || empty($post_data['pincode'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please fill all required fields.'));
            return;
        }

        $is_default = !empty($post_data['is_default']) ? 1 : 0;

        // If this is default, remove default from others
        if ($is_default) {
            $this->checkout_model->clear_default_addresses($user_id);
        }

        // Check if this is first address - make it default
        if (!$this->checkout_model->has_address($user_id)) {
            $is_default = 1;
        }

        $address_data = array(
            'user_id' => $user_id,
            'address_type' => !empty($post_data['address_type']) ? $post_data['address_type'] : 'home',
            'fullname' => $post_data['fullname'],
            'phone' => $post_data['phone'],
            'address_line1' => $post_data['address_line1'],
            'address_line2' => !empty($post_data['address_line2']) ? $post_data['address_line2'] : null,
            'city' => $post_data['city'],
            'state' => !empty($post_data['state']) ? $post_data['state'] : null,
            'pincode' => $post_data['pincode'],
            'landmark' => !empty($post_data['landmark']) ? $post_data['landmark'] : null,
            'is_default' => $is_default,
            'created_at' => date('Y-m-d H:i:s')
        );

        $new_id = $this->checkout_model->add_address($address_data);

        // Get the newly created address
        $new_address = $this->checkout_model->get_address($new_id);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Address added successfully.',
            'address' => $new_address
        ));
    }

    /**
     * Update address (AJAX)
     */
    public function update_address()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        if (empty($post_data['address_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Address ID is required.'));
            return;
        }

        // Verify address belongs to user
        $address = $this->checkout_model->get_address(intval($post_data['address_id']), $user_id);

        if (!$address) {
            echo json_encode(array('status' => 400, 'message' => 'Address not found.'));
            return;
        }

        // Validation
        if (empty($post_data['fullname']) || empty($post_data['phone']) || empty($post_data['address_line1']) || empty($post_data['city']) || empty($post_data['pincode'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please fill all required fields.'));
            return;
        }

        $is_default = !empty($post_data['is_default']) ? 1 : 0;

        // If this is default, remove default from others
        if ($is_default) {
            $this->checkout_model->clear_default_addresses($user_id);
        }

        $update_data = array(
            'address_type' => !empty($post_data['address_type']) ? $post_data['address_type'] : 'home',
            'fullname' => $post_data['fullname'],
            'phone' => $post_data['phone'],
            'address_line1' => $post_data['address_line1'],
            'address_line2' => !empty($post_data['address_line2']) ? $post_data['address_line2'] : null,
            'city' => $post_data['city'],
            'state' => !empty($post_data['state']) ? $post_data['state'] : null,
            'pincode' => $post_data['pincode'],
            'landmark' => !empty($post_data['landmark']) ? $post_data['landmark'] : null,
            'is_default' => $is_default,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->checkout_model->update_address($address->id, $update_data);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Address updated successfully.'
        ));
    }

    /**
     * Delete address (AJAX)
     */
    public function delete_address()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        if (empty($post_data['address_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Address ID is required.'));
            return;
        }

        // Verify address belongs to user
        $address = $this->checkout_model->get_address(intval($post_data['address_id']), $user_id);

        if (!$address) {
            echo json_encode(array('status' => 400, 'message' => 'Address not found.'));
            return;
        }

        $was_default = $address->is_default;

        // Soft delete
        $this->checkout_model->update_address($address->id, array('status' => 3, 'is_default' => 0));

        // If deleted address was default, set another one as default
        $new_default_id = null;
        if ($was_default) {
            $this->db->select('id');
            $this->db->from('user_addresses');
            $this->db->where('user_id', $user_id);
            $this->db->where('status', 1);
            $this->db->order_by('created_at', 'DESC');
            $this->db->limit(1);
            $new_default = $this->db->get()->row();

            if ($new_default) {
                $this->checkout_model->update_address($new_default->id, array('is_default' => 1));
                $new_default_id = $new_default->id;
            }
        }

        echo json_encode(array(
            'status' => 200,
            'message' => 'Address deleted successfully.',
            'new_default_id' => $new_default_id
        ));
    }

    /**
     * Set default address (AJAX)
     */
    public function set_default_address()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        if (empty($post_data['address_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Address ID is required.'));
            return;
        }

        // Verify address belongs to user
        $address = $this->checkout_model->get_address(intval($post_data['address_id']), $user_id);

        if (!$address) {
            echo json_encode(array('status' => 400, 'message' => 'Address not found.'));
            return;
        }

        // Remove default from all addresses
        $this->checkout_model->clear_default_addresses($user_id);

        // Set this as default
        $this->checkout_model->update_address($address->id, array('is_default' => 1));

        echo json_encode(array(
            'status' => 200,
            'message' => 'Default address updated.'
        ));
    }

    // ==================== RAZORPAY PAYMENT ====================

    /**
     * Create Razorpay order (AJAX)
     */
    public function create_order()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        // Validate address selection
        if (empty($post_data['address_id'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please select a delivery address.'));
            return;
        }

        // Verify address
        $address = $this->checkout_model->get_active_address(intval($post_data['address_id']), $user_id);

        if (!$address) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid address selected.'));
            return;
        }

        // Get cart items
        $cart_items = $this->cart_model->get_cart_items($user_id);

        if (empty($cart_items)) {
            echo json_encode(array('status' => 400, 'message' => 'Your cart is empty.'));
            return;
        }

        // Calculate totals
        $subtotal = 0;
        foreach ($cart_items as $item) {
            $subtotal += $item->quantity * $item->unit_price;
        }

        $shipping_info = get_shipping_info($subtotal);
        $shipping = $shipping_info['charge'];

        $coupon_discount = 0;
        $coupon_id = null;
        $coupon_code = null;
        $applied_coupon = $this->session->userdata('applied_coupon');
        if (!empty($applied_coupon)) {
            $result = $this->couponlib->validateCoupon($applied_coupon['coupon_code'], $subtotal, $user_id);
            if ($result['valid']) {
                $coupon_discount = $result['discount_amount'];
                $coupon_id = $result['coupon']->id;
                $coupon_code = $result['coupon']->coupon_code;
            }
        }

        $total = $subtotal - $coupon_discount + $shipping;

        $amount_paise = intval(round($total * 100));
        if ($amount_paise < 100) {
            echo json_encode(array('status' => 400, 'message' => 'Minimum order amount is ₹1.'));
            return;
        }

        // Generate order number
        $order_number = 'PP' . date('Ymd') . strtoupper(substr(uniqid(), -6));

        // Create pending order in database
        $order_data = array(
            'order_number' => $order_number,
            'user_id' => $user_id,
            'subtotal' => $subtotal,
            'discount_amount' => $coupon_discount,
            'coupon_id' => $coupon_id,
            'coupon_code' => $coupon_code,
            'delivery_charge' => $shipping,
            'total_amount' => $total,
            'delivery_fullname' => $address->fullname,
            'delivery_phone' => $address->phone,
            'delivery_address' => $address->address_line1 . ($address->address_line2 ? ', ' . $address->address_line2 : ''),
            'delivery_city' => $address->city,
            'delivery_state' => $address->state,
            'delivery_pincode' => $address->pincode,
            'delivery_landmark' => $address->landmark,
            'order_status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'online',
            'customer_notes' => !empty($post_data['notes']) ? $post_data['notes'] : null,
            'created_at' => date('Y-m-d H:i:s')
        );

        $order_id = $this->checkout_model->create_order($order_data);

        // Insert order items
        foreach ($cart_items as $item) {
            $images = json_decode($item->images);
            $product_image = !empty($images[0]) ? $images[0] : null;

            $order_item = array(
                'order_id' => $order_id,
                'product_id' => $item->product_id,
                'variant_id' => $item->variant_id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'product_image' => $product_image,
                'unit_price' => $item->unit_price,
                'quantity' => $item->quantity,
                'total_price' => $item->quantity * $item->unit_price,
                'package_weight' => $item->package_weight ? $item->package_weight : 0,
                'total_package_weight' => $item->quantity * ($item->package_weight ? $item->package_weight : 0),
                'created_at' => date('Y-m-d H:i:s')
            );

            $this->checkout_model->add_order_item($order_item);
        }

        // Create Razorpay Order
        try {
            $key_id = config_item('razorpay_key_id');
            $key_secret = config_item('razorpay_key_secret');

            if (empty($key_id) || empty($key_secret)) {
                echo json_encode(array('status' => 500, 'message' => 'Payment gateway is not configured. Please contact support.'));
                return;
            }

            $api = new Api($key_id, $key_secret);

            $razorpay_order = $api->order->create(array(
                'receipt' => $order_number,
                'amount' => $amount_paise,
                'currency' => 'INR',
                'payment_capture' => 1,
                'notes' => array(
                    'order_id' => $order_id,
                    'order_number' => $order_number
                )
            ));

            // Store Razorpay order ID
            $this->checkout_model->update_order($order_id, array(
                'payment_id' => $razorpay_order->id
            ));

            // Get user info for prefill
            $user = $this->common->getdatabytable('users', array('id' => $user_id));

            echo json_encode(array(
                'status' => 200,
                'order_id' => $order_id,
                'order_number' => $order_number,
                'razorpay_order_id' => $razorpay_order->id,
                'amount' => $amount_paise,
                'currency' => 'INR',
                'prefill' => array(
                    'name' => $address->fullname,
                    'email' => $user->email,
                    'contact' => $address->phone
                ),
                'key_id' => $key_id
            ));

        } catch (\Razorpay\Api\Errors\BadRequestError $e) {
            $this->checkout_model->delete_order_items($order_id);
            $this->checkout_model->delete_order($order_id);

            log_message('error', 'Razorpay order creation failed (bad request): ' . $e->getMessage());

            $http_status = 500;
            if (stripos($e->getMessage(), 'authentication') !== false) {
                $http_status = 401;
            }

            echo json_encode(array(
                'status' => $http_status,
                'message' => 'Payment initialization failed. Please try again.'
            ));
        } catch (\Exception $e) {
            // Delete the pending order on error
            $this->checkout_model->delete_order_items($order_id);
            $this->checkout_model->delete_order($order_id);

            log_message('error', 'Razorpay order creation failed: ' . $e->getMessage());

            echo json_encode(array(
                'status' => 500,
                'message' => 'Payment initialization failed. Please try again.'
            ));
        }
    }

    /**
     * Verify Razorpay payment (AJAX)
     */
    public function verify_payment()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        // Required parameters
        $razorpay_payment_id = !empty($post_data['razorpay_payment_id']) ? $post_data['razorpay_payment_id'] : '';
        $razorpay_order_id = !empty($post_data['razorpay_order_id']) ? $post_data['razorpay_order_id'] : '';
        $razorpay_signature = !empty($post_data['razorpay_signature']) ? $post_data['razorpay_signature'] : '';
        $order_id = !empty($post_data['order_id']) ? intval($post_data['order_id']) : 0;

        if (empty($razorpay_payment_id) || empty($razorpay_order_id) || empty($razorpay_signature) || empty($order_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid payment data.'));
            return;
        }

        // Verify order belongs to user
        $order = $this->checkout_model->get_order($order_id, $user_id);

        if (!$order) {
            echo json_encode(array('status' => 400, 'message' => 'Order not found.'));
            return;
        }

        if ($order->payment_status === 'paid') {
            // Already processed
            echo json_encode(array(
                'status' => 200,
                'message' => 'Payment already verified.',
                'redirect_url' => base_url('checkout/success/' . $order->order_number)
            ));
            return;
        }

        // Verify signature
        try {
            $key_id = config_item('razorpay_key_id');
            $key_secret = config_item('razorpay_key_secret');

            if (empty($key_id) || empty($key_secret)) {
                echo json_encode(array('status' => 500, 'message' => 'Payment gateway is not configured.'));
                return;
            }

            $api = new Api($key_id, $key_secret);

            $attributes = array(
                'razorpay_order_id' => $razorpay_order_id,
                'razorpay_payment_id' => $razorpay_payment_id,
                'razorpay_signature' => $razorpay_signature
            );

            $api->utility->verifyPaymentSignature($attributes);

            // Payment verified successfully
            // Update order status
            $this->checkout_model->update_order($order_id, array(
                'payment_status' => 'paid',
                'order_status' => 'confirmed',
                'payment_id' => $razorpay_payment_id,
                'updated_at' => date('Y-m-d H:i:s')
            ));

            // Add to order status history
            $this->checkout_model->add_status_history(array(
                'order_id' => $order_id,
                'status' => 'confirmed',
                'notes' => 'Payment received via Razorpay. Payment ID: ' . $razorpay_payment_id,
                'created_at' => date('Y-m-d H:i:s')
            ));

            // Deduct stock
            $this->deduct_stock($order_id);

            // Record coupon usage if applicable
            if ($order->coupon_id) {
                if (!$this->checkout_model->coupon_usage_exists($order->coupon_id, $order_id)) {
                    $this->checkout_model->add_coupon_usage(array(
                        'coupon_id' => $order->coupon_id,
                        'user_id' => $order->user_id,
                        'order_id' => $order_id,
                        'discount_amount' => $order->discount_amount,
                        'used_at' => date('Y-m-d H:i:s')
                    ));

                    $this->checkout_model->increment_coupon_usage($order->coupon_id);
                }
            }

            // Clear cart
            $this->cart_model->clear_cart($user_id);

            // Clear applied coupon from session
            $this->session->unset_userdata('applied_coupon');

            echo json_encode(array(
                'status' => 200,
                'message' => 'Payment successful!',
                'redirect_url' => base_url('checkout/success/' . $order->order_number)
            ));

        } catch (\Exception $e) {
            log_message('error', 'Razorpay signature verification failed: ' . $e->getMessage());

            // Update order as failed
            $this->checkout_model->update_order($order_id, array(
                'payment_status' => 'failed',
                'updated_at' => date('Y-m-d H:i:s')
            ));

            echo json_encode(array(
                'status' => 400,
                'message' => 'Payment verification failed. Please contact support.'
            ));
        }
    }

    /**
     * Payment success page
     */
    public function success($order_number = null)
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        if (empty($order_number)) {
            redirect('');
        }

        $user_id = get_user_sessiondata('id');

        // Get order
        $order = $this->checkout_model->get_order_where(array(
            'order_number' => $order_number,
            'user_id' => $user_id
        ));

        if (!$order || $order->payment_status !== 'paid') {
            redirect('');
        }

        // Get order items
        $order_items = $this->checkout_model->get_order_items($order->id);

        // Prepare data
        $header_data['title'] = 'Order Confirmed';
        $data['order'] = $order;
        $data['order_items'] = $order_items;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/checkout/success', $data);
        $this->load->view('user/common/footer');
    }

    /**
     * Payment failed/cancelled
     */
    public function failed($order_number = null)
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        $user_id = get_user_sessiondata('id');

        if ($order_number) {
            // Get and update order
            $order = $this->checkout_model->get_order_where(array(
                'order_number' => $order_number,
                'user_id' => $user_id,
                'payment_status' => 'pending'
            ));

            if ($order) {
                $this->checkout_model->update_order($order->id, array(
                    'order_status' => 'cancelled',
                    'payment_status' => 'failed',
                    'cancellation_reason' => 'Payment failed or cancelled by user',
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            }
        }

        $this->session->set_flashdata('error', 'Payment failed. Please try again.');
        redirect('checkout');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Deduct stock after successful order
     */
    private function deduct_stock($order_id)
    {
        $order_items = $this->checkout_model->get_order_items($order_id);

        foreach ($order_items as $item) {
            if ($item->stock_deducted) continue;

            $product = $this->checkout_model->get_product($item->product_id);
            if (!$product) continue;

            $deduct_qty = $item->quantity;

            // If variant, use variant's stock_qty as multiplier
            if ($item->variant_id) {
                $variant = $this->checkout_model->get_variant($item->variant_id);
                if ($variant && $variant->stock_qty > 0) {
                    $deduct_qty = $item->quantity * $variant->stock_qty;
                }
            }

            $new_stock = max(0, $product->stock - $deduct_qty);

            $this->checkout_model->update_product_stock($item->product_id, $new_stock);
            $this->checkout_model->update_order_item($item->id, array('stock_deducted' => 1));

            // Log stock change
            $this->checkout_model->add_stock_log(array(
                'product_id' => $item->product_id,
                'previous_stock' => $product->stock,
                'added_qty' => -$deduct_qty,
                'new_stock' => $new_stock,
                'notes' => 'Order #' . $order_id . ' - Stock deducted',
                'added_at' => date('Y-m-d H:i:s')
            ));
        }
    }
}
