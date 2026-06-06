<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Recipes extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    public function index()
    {
        $data['page_title'] = 'Recipes';
        $data['meta_description'] = 'Quick, authentic recipes you can shop in one click.';
        $data['recipes'] = $this->common->getdatabytableall('recipes', array('status' => 1), 'sort_order', 'ASC');

        $this->load->view('user/common/header', $data);
        $this->load->view('user/recipes/index', $data);
        $this->load->view('user/common/footer');
    }

    public function detail($slug_id = null)
    {
        if (empty($slug_id)) {
            redirect('recipes');
            return;
        }

        // slug-<id>
        $parts = explode('-', $slug_id);
        $id = intval(array_pop($parts));
        if (empty($id)) {
            redirect('recipes');
            return;
        }

        $recipe = $this->common->getdatabytable('recipes', array('id' => $id, 'status' => 1));
        if (empty($recipe)) {
            redirect('recipes');
            return;
        }

        // Linked products with their data + first image + variants
        $this->db->select('rp.default_quantity, p.*');
        $this->db->from('recipe_products rp');
        $this->db->join('products p', 'p.id = rp.product_id', 'inner');
        $this->db->where('rp.recipe_id', $recipe->id);
        $this->db->where('p.status', 1);
        $this->db->order_by('rp.sort_order', 'ASC');
        $products = $this->db->get()->result();

        foreach ($products as $p) {
            $p->images_array = !empty($p->images) ? json_decode($p->images, true) : array();
            if (!is_array($p->images_array)) $p->images_array = array();
            $p->variants = $this->common->getdatabytableall('product_variants', array('product_id' => $p->id), 'id', 'ASC');
        }

        // Related recipes (other active recipes)
        $this->db->where('status', 1);
        $this->db->where('id !=', $recipe->id);
        $this->db->order_by('sort_order', 'ASC');
        $this->db->limit(4);
        $related = $this->db->get('recipes')->result();

        $data['page_title'] = $recipe->title;
        $data['meta_description'] = !empty($recipe->description) ? mb_substr(strip_tags($recipe->description), 0, 160) : ('Watch and shop: ' . $recipe->title);
        $data['recipe'] = $recipe;
        $data['products'] = $products;
        $data['related'] = $related;

        $this->load->view('user/common/header', $data);
        $this->load->view('user/recipes/detail', $data);
        $this->load->view('user/common/footer');
        $this->load->view('user/recipes/recipes_js');
    }
}
