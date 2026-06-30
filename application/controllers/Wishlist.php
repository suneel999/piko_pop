<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wishlist extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    /**
     * Wishlist page
     * URL: /wishlist
     */
    public function index()
    {
        if (!is_loggedin_user()) {
            redirect('login?redirect=wishlist');
        }

        $user_id = get_user_sessiondata('id');

        $header_data['title'] = 'My Wishlist - ' . config_item('application_name');

        // Get user details for sidebar
        $data['user'] = $this->common->getdatabytable('users', array('id' => $user_id));

        // Get wishlist items with product details
        $data['wishlist_items'] = $this->get_wishlist_products($user_id);

        // Get dashboard statistics for sidebar
        $data['total_orders'] = $this->get_order_count($user_id);
        $data['pending_orders'] = $this->get_order_count($user_id, 'pending');
        $data['wishlist_count'] = count($data['wishlist_items']);

        // Set active page for sidebar
        $data['active_page'] = 'wishlist';

        // Enable cart validation for add to cart functionality
        $footer_data['cart_validation'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/account/wishlist', $data);
        $this->load->view('user/common/footer', $footer_data);
        $this->load->view('user/account/wishlist_js');
    }

    /**
     * Add to wishlist (AJAX)
     */
    public function add()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login to add items to your wishlist.'));
            return;
        }

        $product_id = intval($this->input->post('product_id'));
        $user_id = get_user_sessiondata('id');

        if (!$product_id) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid product.'));
            return;
        }

        // Check if product exists
        $product = $this->common->getdatabytable('products', array('id' => $product_id, 'status' => 1));
        if (!$product) {
            echo json_encode(array('status' => 400, 'message' => 'Product not found.'));
            return;
        }

        // Check if already in wishlist
        $existing = $this->common->getdatabytable('wishlist', array(
            'user_id' => $user_id,
            'product_id' => $product_id
        ));

        if ($existing) {
            echo json_encode(array(
                'status' => 200,
                'message' => 'Already in your wishlist.',
                'action' => 'already_exists',
                'wishlist_count' => $this->get_wishlist_count($user_id)
            ));
            return;
        }

        // Add to wishlist
        $this->db->insert('wishlist', array(
            'user_id' => $user_id,
            'product_id' => $product_id,
            'created_at' => date('Y-m-d H:i:s')
        ));

        echo json_encode(array(
            'status' => 200,
            'message' => 'Added to wishlist!',
            'action' => 'added',
            'wishlist_count' => $this->get_wishlist_count($user_id)
        ));
    }

    /**
     * Remove from wishlist (AJAX)
     */
    public function remove()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login.'));
            return;
        }

        $product_id = intval($this->input->post('product_id'));
        $user_id = get_user_sessiondata('id');

        if (!$product_id) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid product.'));
            return;
        }

        // Remove from wishlist
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);
        $this->db->delete('wishlist');

        echo json_encode(array(
            'status' => 200,
            'message' => 'Removed from wishlist.',
            'wishlist_count' => $this->get_wishlist_count($user_id)
        ));
    }

    /**
     * Toggle wishlist (AJAX) - Add if not exists, remove if exists
     */
    public function toggle()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 401, 'message' => 'Please login to add items to your wishlist.'));
            return;
        }

        $product_id = intval($this->input->post('product_id'));
        $user_id = get_user_sessiondata('id');

        if (!$product_id) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid product.'));
            return;
        }

        // Check if product exists
        $product = $this->common->getdatabytable('products', array('id' => $product_id, 'status' => 1));
        if (!$product) {
            echo json_encode(array('status' => 400, 'message' => 'Product not found.'));
            return;
        }

        // Check if in wishlist
        $existing = $this->common->getdatabytable('wishlist', array(
            'user_id' => $user_id,
            'product_id' => $product_id
        ));

        if ($existing) {
            // Remove from wishlist
            $this->db->where('user_id', $user_id);
            $this->db->where('product_id', $product_id);
            $this->db->delete('wishlist');

            echo json_encode(array(
                'status' => 200,
                'message' => 'Removed from wishlist.',
                'action' => 'removed',
                'in_wishlist' => false,
                'wishlist_count' => $this->get_wishlist_count($user_id)
            ));
        } else {
            // Add to wishlist
            $this->db->insert('wishlist', array(
                'user_id' => $user_id,
                'product_id' => $product_id,
                'created_at' => date('Y-m-d H:i:s')
            ));

            echo json_encode(array(
                'status' => 200,
                'message' => 'Added to wishlist!',
                'action' => 'added',
                'in_wishlist' => true,
                'wishlist_count' => $this->get_wishlist_count($user_id)
            ));
        }
    }

    /**
     * Get wishlist count (AJAX)
     */
    public function get_count()
    {
        if (!is_loggedin_user()) {
            echo json_encode(array('status' => 200, 'count' => 0));
            return;
        }

        $user_id = get_user_sessiondata('id');
        echo json_encode(array(
            'status' => 200,
            'count' => $this->get_wishlist_count($user_id)
        ));
    }

    // ==================== HELPER METHODS ====================

    /**
     * Get wishlist products with full details
     */
    private function get_wishlist_products($user_id)
    {
        $this->db->select('w.id as wishlist_id, w.product_id, w.created_at as added_at, p.*, c.category_name');
        $this->db->from('wishlist w');
        $this->db->join('products p', 'p.id = w.product_id');
        $this->db->join('categories c', 'c.id = p.category_id', 'left');
        $this->db->where('w.user_id', $user_id);
        $this->db->where('p.status', 1);
        $this->db->order_by('w.created_at', 'DESC');
        $products = $this->db->get()->result();

        // Get variants for each product
        foreach ($products as &$product) {
            $product->images_array = json_decode($product->images, true) ?: array();
            $product->variants = $this->common->getdatabytableall('product_variants', array('product_id' => $product->product_id), 'mrp', 'ASC');
        }

        return $products;
    }

    /**
     * Get wishlist count for a user
     */
    private function get_wishlist_count($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results('wishlist');
    }

    /**
     * Get order count for a user
     */
    private function get_order_count($user_id, $status = null)
    {
        $this->db->where('user_id', $user_id);
        if ($status) {
            $this->db->where('order_status', $status);
        }
        return $this->db->count_all_results('orders');
    }
}
