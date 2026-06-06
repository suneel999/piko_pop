<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Shop extends CI_Controller
{
    private $per_page = 22; // Products per page

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Shop_model', 'shop_model');
        $this->load->model('Common_model', 'common');
        $this->load->library('pagination');
    }

    /**
     * Shop main page - All products with pagination
     * URL: /shop or /shop?page=1
     */
    public function index()
    {
        $page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
        $page = max(1, $page); // Ensure page is at least 1
        $offset = ($page - 1) * $this->per_page;

        // Count total products
        $total_products = $this->shop_model->count_all_products();

        // Pagination config
        $config = $this->get_pagination_config(base_url('shop'), $total_products, $page);
        $this->pagination->initialize($config);

        // Get products with pagination
        $products = $this->shop_model->get_products($this->per_page, $offset);

        // Get all categories for sidebar
        $all_categories = $this->shop_model->get_all_categories();

        // Prepare data for view
        $header_data['title'] = 'Shop - Bhaarat Groceries';
        $data['products'] = $products;
        $data['all_categories'] = $all_categories;
        $data['page_title'] = 'All Products';
        $data['pagination_links'] = $this->pagination->create_links();
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_products / $this->per_page);
        $data['showing_from'] = $total_products > 0 ? $offset + 1 : 0;
        $data['showing_to'] = min($offset + $this->per_page, $total_products);

        // Enable cart functionality
        $footer_data['cart_validation'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/shop/index', $data);
        $this->load->view('user/common/footer', $footer_data);
    }

    /**
     * Search products with pagination
     * URL: /shop/search?q=keyword or /shop/search?q=keyword&page=2
     */
    public function search()
    {
        $keyword = $this->input->get('q', true);
        $page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
        $page = max(1, $page);
        $offset = ($page - 1) * $this->per_page;

        if (empty($keyword)) {
            redirect('shop');
        }

        // Count total search results
        $total_products = $this->shop_model->count_search_products($keyword);

        // Pagination config
        $config = $this->get_pagination_config(base_url('shop/search'), $total_products, $page);
        $config['suffix'] = '&q=' . urlencode($keyword);
        $config['first_url'] = base_url('shop/search') . '?page=1&q=' . urlencode($keyword);
        $this->pagination->initialize($config);

        // Search products with pagination
        $products = $this->shop_model->search_products($keyword, $this->per_page, $offset);

        // Get all categories for sidebar
        $all_categories = $this->shop_model->get_all_categories();

        // Prepare data for view
        $header_data['title'] = 'Search: ' . htmlspecialchars($keyword) . ' - Bhaarat Groceries';
        $data['products'] = $products;
        $data['all_categories'] = $all_categories;
        $data['page_title'] = 'Search Results for "' . htmlspecialchars($keyword) . '"';
        $data['search_keyword'] = $keyword;
        $data['pagination_links'] = $this->pagination->create_links();
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_products / $this->per_page);
        $data['showing_from'] = $total_products > 0 ? $offset + 1 : 0;
        $data['showing_to'] = min($offset + $this->per_page, $total_products);

        // Enable cart functionality
        $footer_data['cart_validation'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/shop/index', $data);
        $this->load->view('user/common/footer', $footer_data);
    }

    /**
     * Product detail page
     * URL: /product/{slug}-{id}
     */
    public function product($slug_with_id = null)
    {
        if (empty($slug_with_id)) {
            redirect('home');
        }

        // Extract product ID from slug
        $product_id = get_id_from_slug($slug_with_id);

        if (!$product_id) {
            show_404();
        }

        // Get product details
        $product = $this->shop_model->get_product($product_id);

        if (empty($product)) {
            show_404();
        }

        // Verify the slug matches (for SEO canonical URL)
        $expected_slug = slugify($product->product_name) . '-' . $product_id;
        if ($slug_with_id !== $expected_slug) {
            // Redirect to correct URL for SEO
            redirect('product/' . $expected_slug);
        }

        // Get product images
        $product->images_array = json_decode($product->images, true) ?: array();

        // Get product category
        $product->category = $this->shop_model->get_category($product->category_id);

        // Get product variants
        $product->variants = $this->shop_model->get_product_variants($product_id);

        // Calculate available stock for variants
        if (!empty($product->variants)) {
            foreach ($product->variants as &$variant) {
                $stock_deduct_qty = floatval($variant->stock_qty);
                if ($stock_deduct_qty > 0) {
                    $variant->available_stock = floor($product->stock / $stock_deduct_qty);
                } else {
                    $variant->available_stock = 0;
                }
            }
            unset($variant);
        }

        // Get related products (same category)
        $related_products = $this->shop_model->get_related_products($product->category_id, $product_id, 8);

        // Get recipes that feature this product
        $this->db->select('r.*');
        $this->db->from('recipes r');
        $this->db->join('recipe_products rp', 'rp.recipe_id = r.id', 'inner');
        $this->db->where('rp.product_id', $product_id);
        $this->db->where('r.status', 1);
        $this->db->group_by('r.id');
        $this->db->order_by('r.sort_order', 'ASC');
        $this->db->limit(8);
        $product_recipes = $this->db->get()->result();

        // Prepare data for view
        $header_data['title'] = $product->product_name . ' - Bhaarat Groceries';
        $data['product'] = $product;
        $data['related_products'] = $related_products;
        $data['product_recipes'] = $product_recipes;

        // Enable cart functionality
        $footer_data['cart_validation'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/shop/product_detail', $data);
        $this->load->view('user/common/footer', $footer_data);
        $this->load->view('user/shop/product_detail_js');
    }

    /**
     * Category page with products and pagination
     * URL: /category/{slug}-{id} or /category/{slug}-{id}?page=2
     */
    public function category($slug_with_id = null)
    {
        if (empty($slug_with_id)) {
            redirect('home');
        }

        $page = $this->input->get('page') ? intval($this->input->get('page')) : 1;
        $page = max(1, $page);
        $offset = ($page - 1) * $this->per_page;

        // Extract category ID from slug
        $category_id = get_id_from_slug($slug_with_id);

        if (!$category_id) {
            show_404();
        }

        // Get category details
        $category = $this->shop_model->get_category($category_id);

        if (empty($category)) {
            show_404();
        }

        // Verify the slug matches
        $expected_slug = slugify($category->category_name) . '-' . $category_id;
        if ($slug_with_id !== $expected_slug) {
            redirect('category/' . $expected_slug);
        }

        // Get subcategories
        $subcategories = $this->shop_model->get_subcategories($category_id);

        // Build array of category IDs to fetch products from (this category + subcategories)
        $category_ids = array($category_id);
        if (!empty($subcategories)) {
            foreach ($subcategories as $subcat) {
                $category_ids[] = $subcat->id;
            }
        }

        // Count total products in category
        $total_products = $this->shop_model->count_category_products($category_ids);

        // Pagination config
        $config = $this->get_pagination_config(base_url('category/' . $slug_with_id), $total_products, $page);
        $this->pagination->initialize($config);

        // Get products with pagination
        $products = $this->shop_model->get_category_products($category_ids, $this->per_page, $offset);

        // Prepare data for view
        $header_data['title'] = $category->category_name . ' - Bhaarat Groceries';
        $data['category'] = $category;
        $data['subcategories'] = $subcategories;
        $data['products'] = $products;
        $data['pagination_links'] = $this->pagination->create_links();
        $data['total_products'] = $total_products;
        $data['current_page'] = $page;
        $data['total_pages'] = ceil($total_products / $this->per_page);
        $data['showing_from'] = $total_products > 0 ? $offset + 1 : 0;
        $data['showing_to'] = min($offset + $this->per_page, $total_products);

        // Enable cart functionality
        $footer_data['cart_validation'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/shop/category', $data);
        $this->load->view('user/common/footer', $footer_data);
    }

    /**
     * Get pagination configuration
     */
    private function get_pagination_config($base_url, $total_rows, $current_page = 1)
    {
        return array(
            'base_url' => $base_url,
            'total_rows' => $total_rows,
            'per_page' => $this->per_page,
            'page_query_string' => true,
            'query_string_segment' => 'page',
            'use_page_numbers' => true,
            'reuse_query_string' => true,

            // Pagination styling (Bootstrap 5)
            'full_tag_open' => '<nav><ul class="pagination justify-content-center">',
            'full_tag_close' => '</ul></nav>',

            'first_link' => '<i class="fa-solid fa-angles-left"></i>',
            'first_tag_open' => '<li class="page-item">',
            'first_tag_close' => '</li>',

            'last_link' => '<i class="fa-solid fa-angles-right"></i>',
            'last_tag_open' => '<li class="page-item">',
            'last_tag_close' => '</li>',

            'next_link' => '<i class="fa-solid fa-angle-right"></i>',
            'next_tag_open' => '<li class="page-item">',
            'next_tag_close' => '</li>',

            'prev_link' => '<i class="fa-solid fa-angle-left"></i>',
            'prev_tag_open' => '<li class="page-item">',
            'prev_tag_close' => '</li>',

            'cur_tag_open' => '<li class="page-item active"><a class="page-link" href="#">',
            'cur_tag_close' => '</a></li>',

            'num_tag_open' => '<li class="page-item">',
            'num_tag_close' => '</li>',

            'attributes' => array('class' => 'page-link'),
        );
    }
}
