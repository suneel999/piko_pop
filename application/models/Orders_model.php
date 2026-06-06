<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Orders_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // ==================== ORDER RETRIEVAL ====================

    /**
     * Get active orders (pending, confirmed, processing, shipped, out_for_delivery)
     */
    public function get_active_orders($status_filter = null)
    {
        $active_statuses = array('pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery');

        $this->db->select('*');
        $this->db->from('orders');

        if (!empty($status_filter) && in_array($status_filter, $active_statuses)) {
            $this->db->where('order_status', $status_filter);
        } else {
            $this->db->where_in('order_status', $active_statuses);
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get all orders with optional filters
     */
    public function get_all_orders($filters = array())
    {
        $this->db->select('*');
        $this->db->from('orders');

        if (!empty($filters['status'])) {
            $this->db->where('order_status', $filters['status']);
        }

        if (!empty($filters['payment_status'])) {
            $this->db->where('payment_status', $filters['payment_status']);
        }

        if (!empty($filters['date_from'])) {
            $this->db->where('DATE(created_at) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $this->db->where('DATE(created_at) <=', $filters['date_to']);
        }

        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    /**
     * Get single order by ID
     */
    public function get_order($order_id)
    {
        $this->db->where('id', $order_id);
        return $this->db->get('orders')->row();
    }

    /**
     * Get order with user details
     */
    public function get_order_with_details($order_id)
    {
        $this->db->select('orders.*, users.fullname as user_name, users.email as user_email, users.phone as user_phone');
        $this->db->from('orders');
        $this->db->join('users', 'users.id = orders.user_id', 'left');
        $this->db->where('orders.id', $order_id);
        return $this->db->get()->row();
    }

    /**
     * Get order by order number
     */
    public function get_order_by_number($order_number)
    {
        $this->db->where('order_number', $order_number);
        return $this->db->get('orders')->row();
    }

    // ==================== ORDER ITEMS ====================

    /**
     * Get order items with product details
     */
    public function get_order_items($order_id)
    {
        $this->db->select('order_items.*, products.images, products.product_weight, products.weight_unit');
        $this->db->from('order_items');
        $this->db->join('products', 'products.id = order_items.product_id', 'left');
        $this->db->where('order_items.order_id', $order_id);
        return $this->db->get()->result();
    }

    /**
     * Update order item
     */
    public function update_order_item($item_id, $data)
    {
        $this->db->where('id', $item_id);
        return $this->db->update('order_items', $data);
    }

    // ==================== ORDER STATUS HISTORY ====================

    /**
     * Get status history for an order
     */
    public function get_status_history($order_id)
    {
        $this->db->where('order_id', $order_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('order_status_history')->result();
    }

    /**
     * Add status history entry
     */
    public function add_status_history($data)
    {
        $this->db->insert('order_status_history', $data);
        return $this->db->insert_id();
    }

    // ==================== ORDER UPDATE ====================

    /**
     * Update order
     */
    public function update_order($order_id, $data)
    {
        $this->db->where('id', $order_id);
        return $this->db->update('orders', $data);
    }

    // ==================== ORDER STATISTICS ====================

    /**
     * Get order count by status
     */
    public function get_order_count_by_status($status)
    {
        $this->db->where('order_status', $status);
        return $this->db->count_all_results('orders');
    }

    /**
     * Get total sales amount
     */
    public function get_total_sales($date_from = null, $date_to = null)
    {
        $this->db->select_sum('total_amount');
        $this->db->from('orders');
        $this->db->where('payment_status', 'paid');
        $this->db->where_not_in('order_status', array('cancelled', 'refunded'));

        if ($date_from) {
            $this->db->where('DATE(created_at) >=', $date_from);
        }

        if ($date_to) {
            $this->db->where('DATE(created_at) <=', $date_to);
        }

        $result = $this->db->get()->row();
        return $result->total_amount ? $result->total_amount : 0;
    }

    /**
     * Get active orders count
     */
    public function get_active_orders_count()
    {
        $active_statuses = array('pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery');
        $this->db->where_in('order_status', $active_statuses);
        return $this->db->count_all_results('orders');
    }
}
