<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Account extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model');
        $this->load->model('Orders_model');
    }

    public function index()
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        redirect('account/profile');
    }

    public function orders()
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        $user_id = get_user_sessiondata('id');

        $header_data['title'] = 'My Orders || ' . config_item('application_name');

        // Get user details
        $data['user'] = $this->Common_model->getdatabytable('users', array('id' => $user_id));

        // Get all orders for this user
        $data['orders'] = $this->get_user_orders($user_id);

        // Get dashboard statistics for sidebar
        $data['total_orders'] = $this->get_order_count($user_id);
        $data['pending_orders'] = $this->get_order_count($user_id, 'pending');
        $data['wishlist_count'] = $this->get_wishlist_count($user_id);

        // Set active page for sidebar
        $data['active_page'] = 'orders';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/account/orders', $data);
        $this->load->view('user/common/footer');
        $this->load->view('user/account/orders_js');
    }

    public function order_detail($order_id = null)
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        if (!$order_id) {
            redirect('account/orders');
        }

        $user_id = get_user_sessiondata('id');

        // Get order details - verify it belongs to this user
        $order = $this->Common_model->getdatabytable('orders', array(
            'id' => $order_id,
            'user_id' => $user_id
        ));

        if (!$order) {
            redirect('account/orders');
        }

        $header_data['title'] = 'Order #' . $order->order_number . ' || ' . config_item('application_name');

        $data['order'] = $order;

        // Get order items
        $this->db->select('oi.*, p.product_name, p.images, pv.variant_name');
        $this->db->from('order_items oi');
        $this->db->join('products p', 'p.id = oi.product_id', 'left');
        $this->db->join('product_variants pv', 'pv.id = oi.variant_id', 'left');
        $this->db->where('oi.order_id', $order_id);
        $data['order_items'] = $this->db->get()->result();

        // Get order status history
        $this->db->select('*');
        $this->db->from('order_status_history');
        $this->db->where('order_id', $order_id);
        $this->db->order_by('created_at', 'DESC');
        $data['status_history'] = $this->db->get()->result();

        // Get user details for sidebar
        $data['user'] = $this->Common_model->getdatabytable('users', array('id' => $user_id));
        $data['total_orders'] = $this->get_order_count($user_id);
        $data['pending_orders'] = $this->get_order_count($user_id, 'pending');
        $data['wishlist_count'] = $this->get_wishlist_count($user_id);

        // Set active page for sidebar
        $data['active_page'] = 'order_detail';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/account/order_detail', $data);
        $this->load->view('user/common/footer');
        $this->load->view('user/account/order_detail_js');
    }

    // Helper functions
    private function get_order_count($user_id, $status = null)
    {
        $this->db->where('user_id', $user_id);
        if ($status) {
            $this->db->where('order_status', $status);
        }
        return $this->db->count_all_results('orders');
    }

    private function get_wishlist_count($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results('wishlist');
    }

    private function get_recent_orders($user_id, $limit = 5)
    {
        $this->db->select('*');
        $this->db->from('orders');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    private function get_user_orders($user_id)
    {
        $this->db->select('*');
        $this->db->from('orders');
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        $orders = $this->db->get()->result();

        // Get order items for each order
        foreach ($orders as &$order) {
            $this->db->select('oi.*, p.product_name, p.images, pv.variant_name');
            $this->db->from('order_items oi');
            $this->db->join('products p', 'p.id = oi.product_id', 'left');
            $this->db->join('product_variants pv', 'pv.id = oi.variant_id', 'left');
            $this->db->where('oi.order_id', $order->id);
            $order->items = $this->db->get()->result();
            $order->item_count = count($order->items);
        }

        return $orders;
    }

    public function addresses()
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        $user_id = get_user_sessiondata('id');

        $header_data['title'] = 'My Addresses || ' . config_item('application_name');

        // Get user details
        $data['user'] = $this->Common_model->getdatabytable('users', array('id' => $user_id));

        // Get all addresses for this user (status = 1 means active)
        $this->db->select('*');
        $this->db->from('user_addresses');
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1);
        $this->db->order_by('is_default', 'DESC');
        $this->db->order_by('created_at', 'DESC');
        $data['addresses'] = $this->db->get()->result();

        // Get dashboard statistics for sidebar
        $data['total_orders'] = $this->get_order_count($user_id);
        $data['pending_orders'] = $this->get_order_count($user_id, 'pending');
        $data['wishlist_count'] = $this->get_wishlist_count($user_id);

        // Set active page for sidebar
        $data['active_page'] = 'addresses';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/account/addresses', $data);
        $this->load->view('user/common/footer');
    }

    public function profile()
    {
        if (!is_loggedin_user()) {
            redirect('login');
        }

        $user_id = get_user_sessiondata('id');

        $header_data['title'] = 'My Profile || ' . config_item('application_name');

        // Get user details
        $data['user'] = $this->Common_model->getdatabytable('users', array('id' => $user_id));

        // Get default address
        $data['default_address'] = $this->Common_model->getdatabytable('user_addresses', array(
            'user_id' => $user_id,
            'is_default' => 1,
            'status' => 1
        ));

        // Get dashboard statistics for sidebar
        $data['total_orders'] = $this->get_order_count($user_id);
        $data['pending_orders'] = $this->get_order_count($user_id, 'pending');
        $data['wishlist_count'] = $this->get_wishlist_count($user_id);

        // Set active page for sidebar
        $data['active_page'] = 'profile';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/account/profile', $data);
        $this->load->view('user/common/footer');
    }

    /**
     * Update profile (AJAX)
     */
    public function update_profile()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        // Validation
        if (empty($post_data['fullname'])) {
            echo json_encode(array('status' => 400, 'message' => 'Full name is required.'));
            return;
        }

        if (empty($post_data['email'])) {
            echo json_encode(array('status' => 400, 'message' => 'Email is required.'));
            return;
        }

        if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter a valid email address.'));
            return;
        }

        // Check if email is already used by another user
        $existing_user = $this->Common_model->getdatabytable('users', array('email' => $post_data['email']));
        if ($existing_user && $existing_user->id != $user_id) {
            echo json_encode(array('status' => 400, 'message' => 'This email is already registered.'));
            return;
        }

        $update_data = array(
            'fullname' => $post_data['fullname'],
            'email' => $post_data['email'],
            'phone' => !empty($post_data['phone']) ? $post_data['phone'] : null,
            'gender' => !empty($post_data['gender']) ? $post_data['gender'] : null,
            'date_of_birth' => !empty($post_data['date_of_birth']) ? $post_data['date_of_birth'] : null,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('id', $user_id);
        $this->db->update('users', $update_data);

        // Update session data
        $this->session->set_userdata('user_fullname', $post_data['fullname']);
        $this->session->set_userdata('user_email', $post_data['email']);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Profile updated successfully.'
        ));
    }

    /**
     * Change password (AJAX)
     */
    public function change_password()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $user_id = get_user_sessiondata('id');

        // Validation
        if (empty($post_data['current_password'])) {
            echo json_encode(array('status' => 400, 'message' => 'Current password is required.'));
            return;
        }

        if (empty($post_data['new_password'])) {
            echo json_encode(array('status' => 400, 'message' => 'New password is required.'));
            return;
        }

        if (strlen($post_data['new_password']) < 6) {
            echo json_encode(array('status' => 400, 'message' => 'Password must be at least 6 characters.'));
            return;
        }

        if ($post_data['new_password'] !== $post_data['confirm_password']) {
            echo json_encode(array('status' => 400, 'message' => 'Passwords do not match.'));
            return;
        }

        // Get current user
        $user = $this->Common_model->getdatabytable('users', array('id' => $user_id));

        // Verify current password
        if (!password_verify($post_data['current_password'], $user->password)) {
            echo json_encode(array('status' => 400, 'message' => 'Current password is incorrect.'));
            return;
        }

        // Update password
        $new_password_hash = password_hash($post_data['new_password'], PASSWORD_DEFAULT);

        $this->db->where('id', $user_id);
        $this->db->update('users', array(
            'password' => $new_password_hash,
            'updated_at' => date('Y-m-d H:i:s')
        ));

        echo json_encode(array(
            'status' => 200,
            'message' => 'Password changed successfully.'
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
        if (empty($post_data['fullname']) || empty($post_data['phone']) || empty($post_data['address_line1']) || empty($post_data['city']) || empty($post_data['state']) || empty($post_data['pincode'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please fill all required fields.'));
            return;
        }

        // If setting as default, remove default from other addresses
        if (!empty($post_data['is_default']) && $post_data['is_default'] == 1) {
            $this->db->where('user_id', $user_id);
            $this->db->update('user_addresses', array('is_default' => 0));
        }

        $address_data = array(
            'user_id' => $user_id,
            'address_type' => !empty($post_data['address_type']) ? $post_data['address_type'] : 'home',
            'fullname' => $post_data['fullname'],
            'phone' => $post_data['phone'],
            'address_line1' => $post_data['address_line1'],
            'address_line2' => !empty($post_data['address_line2']) ? $post_data['address_line2'] : null,
            'city' => $post_data['city'],
            'state' => $post_data['state'],
            'pincode' => $post_data['pincode'],
            'is_default' => !empty($post_data['is_default']) ? 1 : 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->insert('user_addresses', $address_data);
        $address_id = $this->db->insert_id();

        echo json_encode(array(
            'status' => 200,
            'message' => 'Address added successfully.',
            'address_id' => $address_id
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
            echo json_encode(array('status' => 400, 'message' => 'Invalid address.'));
            return;
        }

        // Verify address belongs to user
        $address = $this->Common_model->getdatabytable('user_addresses', array(
            'id' => $post_data['address_id'],
            'user_id' => $user_id
        ));

        if (!$address) {
            echo json_encode(array('status' => 400, 'message' => 'Address not found.'));
            return;
        }

        // Validation
        if (empty($post_data['fullname']) || empty($post_data['phone']) || empty($post_data['address_line1']) || empty($post_data['city']) || empty($post_data['state']) || empty($post_data['pincode'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please fill all required fields.'));
            return;
        }

        // If setting as default, remove default from other addresses
        if (!empty($post_data['is_default']) && $post_data['is_default'] == 1) {
            $this->db->where('user_id', $user_id);
            $this->db->update('user_addresses', array('is_default' => 0));
        }

        $update_data = array(
            'address_type' => !empty($post_data['address_type']) ? $post_data['address_type'] : 'home',
            'fullname' => $post_data['fullname'],
            'phone' => $post_data['phone'],
            'address_line1' => $post_data['address_line1'],
            'address_line2' => !empty($post_data['address_line2']) ? $post_data['address_line2'] : null,
            'city' => $post_data['city'],
            'state' => $post_data['state'],
            'pincode' => $post_data['pincode'],
            'is_default' => !empty($post_data['is_default']) ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s')
        );

        $this->db->where('id', $post_data['address_id']);
        $this->db->update('user_addresses', $update_data);

        echo json_encode(array(
            'status' => 200,
            'message' => 'Address updated successfully.'
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

        $address_id = $this->input->post('address_id', true);
        $user_id = get_user_sessiondata('id');

        if (empty($address_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid address.'));
            return;
        }

        // Verify address belongs to user
        $address = $this->Common_model->getdatabytable('user_addresses', array(
            'id' => $address_id,
            'user_id' => $user_id
        ));

        if (!$address) {
            echo json_encode(array('status' => 400, 'message' => 'Address not found.'));
            return;
        }

        // Remove default from all addresses
        $this->db->where('user_id', $user_id);
        $this->db->update('user_addresses', array('is_default' => 0));

        // Set new default
        $this->db->where('id', $address_id);
        $this->db->update('user_addresses', array('is_default' => 1));

        echo json_encode(array(
            'status' => 200,
            'message' => 'Default address updated successfully.'
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

        $address_id = $this->input->post('address_id', true);
        $user_id = get_user_sessiondata('id');

        if (empty($address_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid address.'));
            return;
        }

        // Verify address belongs to user and is not default
        $address = $this->Common_model->getdatabytable('user_addresses', array(
            'id' => $address_id,
            'user_id' => $user_id
        ));

        if (!$address) {
            echo json_encode(array('status' => 400, 'message' => 'Address not found.'));
            return;
        }

        if ($address->is_default == 1) {
            echo json_encode(array('status' => 400, 'message' => 'Cannot delete default address. Please set another address as default first.'));
            return;
        }

        // Soft delete - set status to 0
        $this->db->where('id', $address_id);
        $this->db->update('user_addresses', array('status' => 0));

        echo json_encode(array(
            'status' => 200,
            'message' => 'Address deleted successfully.'
        ));
    }

    /**
     * Reorder - Add all items from a previous order to cart (AJAX)
     */
    public function reorder()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login to reorder.'));
            return;
        }

        $order_id = $this->input->post('order_id', true);
        $user_id = get_user_sessiondata('id');

        if (empty($order_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid order.'));
            return;
        }

        // Verify order belongs to user
        $order = $this->Common_model->getdatabytable('orders', array(
            'id' => $order_id,
            'user_id' => $user_id
        ));

        if (!$order) {
            echo json_encode(array('status' => 400, 'message' => 'Order not found.'));
            return;
        }

        // Get order items
        $this->db->select('oi.*, p.status as product_status, p.stock as product_stock');
        $this->db->from('order_items oi');
        $this->db->join('products p', 'p.id = oi.product_id', 'left');
        $this->db->where('oi.order_id', $order_id);
        $order_items = $this->db->get()->result();

        if (empty($order_items)) {
            echo json_encode(array('status' => 400, 'message' => 'No items found in this order.'));
            return;
        }

        $this->load->model('Cart_model', 'cart_model');
        $added_count = 0;
        $skipped_items = array();

        foreach ($order_items as $item) {
            // Check if product is still available
            if (empty($item->product_status) || $item->product_status != 1) {
                $skipped_items[] = $item->product_name . ' (unavailable)';
                continue;
            }

            // Get current price
            $product = $this->Common_model->getdatabytable('products', array('id' => $item->product_id, 'status' => 1));
            if (!$product) {
                $skipped_items[] = $item->product_name . ' (unavailable)';
                continue;
            }

            $variant_id = !empty($item->variant_id) ? $item->variant_id : null;
            $quantity = intval($item->quantity);

            // Get current price based on variant or product
            if ($variant_id) {
                $variant = $this->Common_model->getdatabytable('product_variants', array('id' => $variant_id, 'product_id' => $item->product_id));
                if (!$variant) {
                    $skipped_items[] = $item->product_name . ' (variant unavailable)';
                    continue;
                }
                $unit_price = !empty($variant->sale_price) ? $variant->sale_price : $variant->mrp;
                $stock_deduct_qty = floatval($variant->stock_qty);
                $stock_available = floor($product->stock / $stock_deduct_qty);
            } else {
                $unit_price = !empty($product->sale_price) ? $product->sale_price : $product->mrp;
                $stock_available = $product->stock;
            }

            // Check stock
            if ($stock_available < 1) {
                $skipped_items[] = $item->product_name . ' (out of stock)';
                continue;
            }

            // Adjust quantity if needed
            if ($quantity > $stock_available) {
                $quantity = $stock_available;
            }

            // Check if item already in cart
            $existing_cart = $this->cart_model->get_cart_item($user_id, $item->product_id, $variant_id);

            if (!empty($existing_cart)) {
                // Update quantity (don't exceed stock)
                $new_quantity = min($existing_cart->quantity + $quantity, $stock_available);
                $this->cart_model->update_item($existing_cart->id, array(
                    'quantity' => $new_quantity,
                    'unit_price' => $unit_price,
                    'updated_at' => date('Y-m-d H:i:s')
                ));
            } else {
                // Insert new cart item
                $cart_data = array(
                    'user_id' => $user_id,
                    'product_id' => $item->product_id,
                    'variant_id' => $variant_id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'created_at' => date('Y-m-d H:i:s')
                );
                $this->cart_model->add_item($cart_data);
            }

            $added_count++;
        }

        if ($added_count > 0) {
            $message = $added_count . ' item(s) added to cart.';
            if (!empty($skipped_items)) {
                $message .= ' Some items were skipped: ' . implode(', ', $skipped_items);
            }
            echo json_encode(array(
                'status' => 200,
                'message' => $message,
                'cart_count' => get_cart_count(),
                'redirect' => base_url('cart')
            ));
        } else {
            echo json_encode(array(
                'status' => 400,
                'message' => 'Could not add any items. ' . implode(', ', $skipped_items)
            ));
        }
    }

    /**
     * Buy Again - Add a single item from a previous order to cart (AJAX)
     */
    public function buy_again()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $product_id = $this->input->post('product_id', true);
        $variant_id = $this->input->post('variant_id', true);
        $user_id = get_user_sessiondata('id');

        if (empty($product_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid product.'));
            return;
        }

        // Check if product is available
        $product = $this->Common_model->getdatabytable('products', array('id' => $product_id, 'status' => 1));
        if (!$product) {
            echo json_encode(array('status' => 400, 'message' => 'Product is no longer available.'));
            return;
        }

        $variant_id = !empty($variant_id) ? intval($variant_id) : null;

        // Get current price
        if ($variant_id) {
            $variant = $this->Common_model->getdatabytable('product_variants', array('id' => $variant_id, 'product_id' => $product_id));
            if (!$variant) {
                echo json_encode(array('status' => 400, 'message' => 'Variant is no longer available.'));
                return;
            }
            $unit_price = !empty($variant->sale_price) ? $variant->sale_price : $variant->mrp;
            $stock_deduct_qty = floatval($variant->stock_qty);
            $stock_available = floor($product->stock / $stock_deduct_qty);
        } else {
            $unit_price = !empty($product->sale_price) ? $product->sale_price : $product->mrp;
            $stock_available = $product->stock;
        }

        if ($stock_available < 1) {
            echo json_encode(array('status' => 400, 'message' => 'Product is out of stock.'));
            return;
        }

        $this->load->model('Cart_model', 'cart_model');

        // Check if item already in cart
        $existing_cart = $this->cart_model->get_cart_item($user_id, $product_id, $variant_id);

        if (!empty($existing_cart)) {
            // Update quantity
            $new_quantity = $existing_cart->quantity + 1;
            if ($new_quantity > $stock_available) {
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
                'quantity' => 1,
                'unit_price' => $unit_price,
                'created_at' => date('Y-m-d H:i:s')
            );
            $this->cart_model->add_item($cart_data);
            $message = 'Item added to cart.';
        }

        echo json_encode(array(
            'status' => 200,
            'message' => $message,
            'cart_count' => get_cart_count()
        ));
    }
}
