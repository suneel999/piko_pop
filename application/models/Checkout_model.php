<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Checkout_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== ADDRESS METHODS ====================

    /**
     * Get user addresses
     */
    public function get_user_addresses($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1);
        $this->db->order_by('is_default', 'DESC');
        return $this->db->get('user_addresses')->result();
    }

    /**
     * Get address by ID
     */
    public function get_address($address_id, $user_id = null)
    {
        $this->db->where('id', $address_id);
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        return $this->db->get('user_addresses')->row();
    }

    /**
     * Get address with status check
     */
    public function get_active_address($address_id, $user_id)
    {
        $this->db->where('id', $address_id);
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1);
        return $this->db->get('user_addresses')->row();
    }

    /**
     * Add new address
     */
    public function add_address($data)
    {
        $this->db->insert('user_addresses', $data);
        return $this->db->insert_id();
    }

    /**
     * Update address
     */
    public function update_address($address_id, $data)
    {
        $this->db->where('id', $address_id);
        return $this->db->update('user_addresses', $data);
    }

    /**
     * Remove default from all user addresses
     */
    public function clear_default_addresses($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->update('user_addresses', array('is_default' => 0));
    }

    /**
     * Check if user has any address
     */
    public function has_address($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('status', 1);
        return $this->db->count_all_results('user_addresses') > 0;
    }

    // ==================== ORDER METHODS ====================

    /**
     * Create order
     */
    public function create_order($data)
    {
        $this->db->insert('orders', $data);
        return $this->db->insert_id();
    }

    /**
     * Get order by ID
     */
    public function get_order($order_id, $user_id = null)
    {
        $this->db->where('id', $order_id);
        if ($user_id) {
            $this->db->where('user_id', $user_id);
        }
        return $this->db->get('orders')->row();
    }

    /**
     * Get order by conditions
     */
    public function get_order_where($conditions)
    {
        foreach ($conditions as $key => $value) {
            $this->db->where($key, $value);
        }
        return $this->db->get('orders')->row();
    }

    /**
     * Update order
     */
    public function update_order($order_id, $data)
    {
        $this->db->where('id', $order_id);
        return $this->db->update('orders', $data);
    }

    /**
     * Add order item
     */
    public function add_order_item($data)
    {
        $this->db->insert('order_items', $data);
        return $this->db->insert_id();
    }

    /**
     * Get order items
     */
    public function get_order_items($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->get('order_items')->result();
    }

    /**
     * Update order item
     */
    public function update_order_item($item_id, $data)
    {
        $this->db->where('id', $item_id);
        return $this->db->update('order_items', $data);
    }

    /**
     * Delete order items
     */
    public function delete_order_items($order_id)
    {
        $this->db->where('order_id', $order_id);
        return $this->db->delete('order_items');
    }

    /**
     * Delete order
     */
    public function delete_order($order_id)
    {
        $this->db->where('id', $order_id);
        return $this->db->delete('orders');
    }

    /**
     * Add order status history
     */
    public function add_status_history($data)
    {
        $this->db->insert('order_status_history', $data);
        return $this->db->insert_id();
    }

    // ==================== COUPON METHODS ====================

    /**
     * Check if coupon usage exists
     */
    public function coupon_usage_exists($coupon_id, $order_id)
    {
        $this->db->where('coupon_id', $coupon_id);
        $this->db->where('order_id', $order_id);
        return $this->db->count_all_results('coupon_usage') > 0;
    }

    /**
     * Add coupon usage
     */
    public function add_coupon_usage($data)
    {
        $this->db->insert('coupon_usage', $data);
        return $this->db->insert_id();
    }

    /**
     * Increment coupon used count
     */
    public function increment_coupon_usage($coupon_id)
    {
        $this->db->set('used_count', 'used_count + 1', false);
        $this->db->where('id', $coupon_id);
        return $this->db->update('coupons');
    }

    // ==================== STOCK METHODS ====================

    /**
     * Get product
     */
    public function get_product($product_id)
    {
        $this->db->where('id', $product_id);
        return $this->db->get('products')->row();
    }

    /**
     * Get product variant
     */
    public function get_variant($variant_id)
    {
        $this->db->where('id', $variant_id);
        return $this->db->get('product_variants')->row();
    }

    /**
     * Update product stock
     */
    public function update_product_stock($product_id, $new_stock)
    {
        $this->db->where('id', $product_id);
        return $this->db->update('products', array('stock' => $new_stock));
    }

    /**
     * Add stock log
     */
    public function add_stock_log($data)
    {
        $this->db->insert('stock_logs', $data);
        return $this->db->insert_id();
    }
}
