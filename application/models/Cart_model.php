<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get cart items with product and variant details
     */
    public function get_cart_items($user_id)
    {
        $this->db->select('cart.*, products.product_name, products.images,
                          products.mrp as product_mrp, products.sale_price as product_sale_price,
                          product_variants.variant_name, product_variants.mrp as variant_mrp,
                          product_variants.sale_price as variant_sale_price, product_variants.package_weight as package_weight');
        $this->db->from('cart');
        $this->db->join('products', 'products.id = cart.product_id', 'left');
        $this->db->join('product_variants', 'product_variants.id = cart.variant_id', 'left');
        $this->db->where('cart.user_id', $user_id);
        $this->db->order_by('cart.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get cart items for checkout (simplified)
     */
    public function get_cart_items_simple($user_id)
    {
        $this->db->select('cart.*, products.product_name, products.images,
                          product_variants.variant_name');
        $this->db->from('cart');
        $this->db->join('products', 'products.id = cart.product_id', 'left');
        $this->db->join('product_variants', 'product_variants.id = cart.variant_id', 'left');
        $this->db->where('cart.user_id', $user_id);
        $this->db->order_by('cart.created_at', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get single cart item
     */
    public function get_cart_item($user_id, $product_id, $variant_id = null)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);

        if ($variant_id) {
            $this->db->where('variant_id', $variant_id);
        } else {
            $this->db->where('variant_id IS NULL', null, false);
        }

        return $this->db->get('cart')->row();
    }

    /**
     * Get cart item by ID
     */
    public function get_cart_item_by_id($cart_id, $user_id)
    {
        $this->db->where('id', $cart_id);
        $this->db->where('user_id', $user_id);
        return $this->db->get('cart')->row();
    }

    /**
     * Add item to cart
     */
    public function add_item($data)
    {
        $this->db->insert('cart', $data);
        return $this->db->insert_id();
    }

    /**
     * Update cart item
     */
    public function update_item($cart_id, $data)
    {
        $this->db->where('id', $cart_id);
        return $this->db->update('cart', $data);
    }

    /**
     * Remove cart item
     */
    public function remove_item($cart_id)
    {
        $this->db->where('id', $cart_id);
        return $this->db->delete('cart');
    }

    /**
     * Remove cart item by product
     */
    public function remove_by_product($user_id, $product_id, $variant_id = null)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);

        if ($variant_id) {
            $this->db->where('variant_id', $variant_id);
        }

        return $this->db->delete('cart');
    }

    /**
     * Clear user cart
     */
    public function clear_cart($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->delete('cart');
    }

    /**
     * Get cart count for user
     */
    public function get_cart_count($user_id)
    {
        $this->db->where('user_id', $user_id);
        return $this->db->count_all_results('cart');
    }

    /**
     * Get cart items for dropdown (limited)
     */
    public function get_dropdown_items($user_id, $limit = 3)
    {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('cart')->result();
    }

    /**
     * Get all cart items for user (basic)
     */
    public function get_all_cart_items($user_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('id', 'DESC');
        return $this->db->get('cart')->result();
    }

    /**
     * Get cart items for specific product
     */
    public function get_product_cart_items($user_id, $product_id)
    {
        $this->db->where('user_id', $user_id);
        $this->db->where('product_id', $product_id);
        return $this->db->get('cart')->result();
    }

    /**
     * Get item quantity in cart
     */
    public function get_item_quantity($user_id, $product_id, $variant_id = null)
    {
        $cart_item = $this->get_cart_item($user_id, $product_id, $variant_id);
        return $cart_item ? floatval($cart_item->quantity) : 0;
    }

    /**
     * Calculate cart subtotal
     */
    public function calculate_subtotal($user_id)
    {
        $this->db->select_sum('(unit_price * quantity)', 'subtotal');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get('cart')->row();
        return $result ? floatval($result->subtotal) : 0;
    }
}
