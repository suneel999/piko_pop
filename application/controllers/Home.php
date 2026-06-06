<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Home extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    public function index()
    {
        // Header data
        $header_data['page_title'] = "Home";

        // Get banners - load all active banners sorted by sort_order
        $data['banners'] = $this->common->getdatabytableall('banners', array('status' => 1), 'sort_order', 'ASC');

        // Get all parent categories for "Shop By Categories" section
        $data['categories'] = $this->common->getdatabytableall('categories', array('parent_category_id' => 0, 'status' => 1), 'sort_order', 'ASC');

        // Get featured categories for product sections
        $featured_categories = $this->common->getfeatured_categorys();

        // Get 8 recent products for each featured category (including subcategories)
        $data['featured_category_products'] = array();
        if (!empty($featured_categories)) {
            foreach ($featured_categories as $featured_category) {
                // Build array of category IDs (featured category + all its subcategories)
                $sub_cats_array = array();
                $sub_cats_array[] = $featured_category->id;

                // Get all subcategories
                $get_all_subcats = $this->common->getsubcats($featured_category->id);
                if (!empty($get_all_subcats)) {
                    foreach ($get_all_subcats as $cat) {
                        $sub_cats_array[] = $cat->id;
                    }
                }

                // Get products from all these categories
                $products = $this->common->getFeaturedProducts($sub_cats_array, 8);

                // Get variants for each product
                if (!empty($products)) {
                    foreach ($products as &$product) {
                        $product->variants = $this->common->getdatabytableall('product_variants', array('product_id' => $product->id, 'status' => 1), 'mrp', 'ASC');
                        $product->images_array = json_decode($product->images, true) ?: array();
                    }

                    $data['featured_category_products'][] = array(
                        'category' => $featured_category,
                        'products' => $products
                    );
                }
            }
        }

        // Enable cart functionality
        $footer_data['cart_validation'] = true;
        $footer_data['quick_view_modal'] = true;

        $footer_data['comparison'] = true;

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/home/banner', $data);
        $this->load->view('user/home/categories', $data);
        $this->load->view('user/home/marquee');
        $this->load->view('user/home/usp', $data);
        // $this->load->view('user/home/comparison');
        $this->load->view('user/home/featured_category_products', $data);
        $this->load->view('user/home/testimonials');
        $this->load->view('user/home/faq');
        $this->load->view('user/common/footer', $footer_data);
    }
}
