<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shop_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Count all active products
     */
    public function count_all_products()
    {
        $this->db->where('status', 1);
        return $this->db->count_all_results('products');
    }

    /**
     * Get products with pagination
     */
    public function get_products($limit, $offset)
    {
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);

        $products = $this->db->get()->result();

        // Fetch variants for each product
        foreach ($products as &$product) {
            $product->images_array = json_decode($product->images, true) ?: array();
            $product->variants = $this->get_product_variants($product->id);
        }
        unset($product);

        return $products;
    }

    /**
     * Count search results
     */
    public function count_search_products($keyword)
    {
        $this->db->where('status', 1);
        $this->db->group_start();
        $this->db->like('product_name', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('short_description', $keyword);
        $this->db->group_end();

        return $this->db->count_all_results('products');
    }

    /**
     * Search products with pagination
     */
    public function search_products($keyword, $limit, $offset)
    {
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('status', 1);
        $this->db->group_start();
        $this->db->like('product_name', $keyword);
        $this->db->or_like('description', $keyword);
        $this->db->or_like('short_description', $keyword);
        $this->db->group_end();
        $this->db->order_by('product_name', 'ASC');
        $this->db->limit($limit, $offset);

        $products = $this->db->get()->result();

        // Fetch variants for each product
        foreach ($products as &$product) {
            $product->images_array = json_decode($product->images, true) ?: array();
            $product->variants = $this->get_product_variants($product->id);
        }
        unset($product);

        return $products;
    }

    /**
     * Get product by ID
     */
    public function get_product($product_id)
    {
        $this->db->where('id', $product_id);
        $this->db->where('status', 1);
        return $this->db->get('products')->row();
    }

    /**
     * Get product variants
     */
    public function get_product_variants($product_id)
    {
        $this->db->where('product_id', $product_id);
        $this->db->where('status', 1);
        $this->db->order_by('mrp', 'ASC');
        return $this->db->get('product_variants')->result();
    }

    /**
     * Get category by ID
     */
    public function get_category($category_id)
    {
        $this->db->where('id', $category_id);
        $this->db->where('status', 1);
        return $this->db->get('categories')->row();
    }

    /**
     * Get subcategories
     */
    public function get_subcategories($parent_category_id)
    {
        $this->db->where('parent_category_id', $parent_category_id);
        $this->db->where('status', 1);
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get('categories')->result();
    }

    /**
     * Get all parent categories
     */
    public function get_all_categories()
    {
        $this->db->where('parent_category_id', 0);
        $this->db->where('status', 1);
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get('categories')->result();
    }

    /**
     * Count products in category (including subcategories)
     */
    public function count_category_products($category_ids)
    {
        $this->db->where_in('category_id', $category_ids);
        $this->db->where('status', 1);
        return $this->db->count_all_results('products');
    }

    /**
     * Get products by category with pagination
     */
    public function get_category_products($category_ids, $limit, $offset)
    {
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where_in('category_id', $category_ids);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit, $offset);

        $products = $this->db->get()->result();

        // Fetch variants for each product
        foreach ($products as &$product) {
            $product->images_array = json_decode($product->images, true) ?: array();
            $product->variants = $this->get_product_variants($product->id);
        }
        unset($product);

        return $products;
    }

    /**
     * Get related products from same category
     */
    public function get_related_products($category_id, $exclude_product_id, $limit = 8)
    {
        $this->db->select('*');
        $this->db->from('products');
        $this->db->where('category_id', $category_id);
        $this->db->where('id !=', $exclude_product_id);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);

        $products = $this->db->get()->result();

        // Fetch variants for each product
        foreach ($products as &$product) {
            $product->images_array = json_decode($product->images, true) ?: array();
            $product->variants = $this->get_product_variants($product->id);
        }
        unset($product);

        return $products;
    }
}
