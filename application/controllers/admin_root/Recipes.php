<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Recipes extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    // ==================== LIST ====================

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Recipes || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['datatable'] = true;
            $footer_data['sweet_alert'] = true;

            $where_data = array('status !=' => 3);
            $data['recipes'] = $this->common->getdatabytableall('recipes', $where_data, 'id', 'DESC');

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/recipes/index', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/recipes_list');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // ==================== ADD ====================

    public function add()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Add Recipe || ' . config_item('application_name');
            $header_data['form_validation'] = true;
            $header_data['select_2'] = true;
            $footer_data['form_validation'] = true;
            $footer_data['select_2'] = true;

            $data['products'] = $this->common->getdatabytableall('products', array('status' => 1), 'product_name', 'ASC');

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/recipes/add', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/recipes_add');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function save_recipe()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400, 'message' => 'Please login again.'));
            return;
        }

        $post_data = $this->input->post(null, true);

        if (empty($post_data['title']) || empty($post_data['youtube_url'])) {
            echo json_encode(array('status' => 400, 'message' => 'Title and YouTube URL are required.'));
            return;
        }

        $video_id = $this->_extract_youtube_id($post_data['youtube_url']);
        if (empty($video_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid YouTube URL.'));
            return;
        }

        $slug = $this->_unique_slug(slugify($post_data['title']), 0);

        $thumbnail = $this->_handle_thumbnail_upload();
        if ($thumbnail === false) {
            echo json_encode(array('status' => 400, 'message' => 'Failed to upload thumbnail.'));
            return;
        }

        $recipe_data = array(
            'title'             => $post_data['title'],
            'slug'              => $slug,
            'description'       => !empty($post_data['description']) ? $post_data['description'] : null,
            'youtube_url'       => $post_data['youtube_url'],
            'youtube_video_id'  => $video_id,
            'thumbnail'         => $thumbnail,
            'prep_time_minutes' => !empty($post_data['prep_time_minutes']) ? intval($post_data['prep_time_minutes']) : 0,
            'cook_time_minutes' => !empty($post_data['cook_time_minutes']) ? intval($post_data['cook_time_minutes']) : 0,
            'servings'          => !empty($post_data['servings']) ? intval($post_data['servings']) : 0,
            'difficulty'        => !empty($post_data['difficulty']) ? $post_data['difficulty'] : 'easy',
            'ingredients'       => !empty($post_data['ingredients']) ? $post_data['ingredients'] : null,
            'sort_order'        => !empty($post_data['sort_order']) ? intval($post_data['sort_order']) : 0,
            'status'            => 1,
            'created_by'        => $this->session->userdata('admin_id'),
            'created_at'        => date('Y-m-d H:i:s'),
        );

        $recipe_id = $this->common->insert($recipe_data, 'recipes');

        if (empty($recipe_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Something went wrong.'));
            return;
        }

        $this->_save_recipe_products($recipe_id, $post_data);

        echo json_encode(array('status' => 200, 'message' => 'Recipe created successfully.'));
    }

    // ==================== EDIT ====================

    public function edit($id)
    {
        if (is_loggedin_admin()) {
            $recipe = $this->common->getdatabytable('recipes', array('id' => $id, 'status !=' => 3));
            if (empty($recipe)) {
                redirect('admin_root/recipes');
                return;
            }

            $header_data['title'] = 'Edit Recipe || ' . config_item('application_name');
            $header_data['form_validation'] = true;
            $header_data['select_2'] = true;
            $footer_data['form_validation'] = true;
            $footer_data['select_2'] = true;

            $data['recipe'] = $recipe;
            $data['products'] = $this->common->getdatabytableall('products', array('status' => 1), 'product_name', 'ASC');
            $data['recipe_products'] = $this->common->getdatabytableall('recipe_products', array('recipe_id' => $id), 'sort_order', 'ASC');

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/recipes/edit', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/recipes_edit');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function update_recipe()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400, 'message' => 'Please login again.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $recipe_id = !empty($post_data['recipe_id']) ? intval($post_data['recipe_id']) : 0;

        if (empty($recipe_id) || empty($post_data['title']) || empty($post_data['youtube_url'])) {
            echo json_encode(array('status' => 400, 'message' => 'Title and YouTube URL are required.'));
            return;
        }

        $recipe = $this->common->getdatabytable('recipes', array('id' => $recipe_id, 'status !=' => 3));
        if (empty($recipe)) {
            echo json_encode(array('status' => 400, 'message' => 'Recipe not found.'));
            return;
        }

        $video_id = $this->_extract_youtube_id($post_data['youtube_url']);
        if (empty($video_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid YouTube URL.'));
            return;
        }

        $slug = $recipe->slug;
        if (slugify($post_data['title']) !== $slug) {
            $slug = $this->_unique_slug(slugify($post_data['title']), $recipe_id);
        }

        $thumbnail = $recipe->thumbnail;
        $new_thumb = $this->_handle_thumbnail_upload();
        if ($new_thumb === false) {
            echo json_encode(array('status' => 400, 'message' => 'Failed to upload thumbnail.'));
            return;
        }
        if (!empty($new_thumb)) {
            // delete old upload
            if (!empty($recipe->thumbnail) && file_exists(FCPATH . 'uploads/recipes/' . $recipe->thumbnail)) {
                @unlink(FCPATH . 'uploads/recipes/' . $recipe->thumbnail);
            }
            $thumbnail = $new_thumb;
        } elseif (!empty($post_data['remove_thumbnail'])) {
            if (!empty($recipe->thumbnail) && file_exists(FCPATH . 'uploads/recipes/' . $recipe->thumbnail)) {
                @unlink(FCPATH . 'uploads/recipes/' . $recipe->thumbnail);
            }
            $thumbnail = null;
        }

        $recipe_data = array(
            'title'             => $post_data['title'],
            'slug'              => $slug,
            'description'       => !empty($post_data['description']) ? $post_data['description'] : null,
            'youtube_url'       => $post_data['youtube_url'],
            'youtube_video_id'  => $video_id,
            'thumbnail'         => $thumbnail,
            'prep_time_minutes' => !empty($post_data['prep_time_minutes']) ? intval($post_data['prep_time_minutes']) : 0,
            'cook_time_minutes' => !empty($post_data['cook_time_minutes']) ? intval($post_data['cook_time_minutes']) : 0,
            'servings'          => !empty($post_data['servings']) ? intval($post_data['servings']) : 0,
            'difficulty'        => !empty($post_data['difficulty']) ? $post_data['difficulty'] : 'easy',
            'ingredients'       => !empty($post_data['ingredients']) ? $post_data['ingredients'] : null,
            'sort_order'        => !empty($post_data['sort_order']) ? intval($post_data['sort_order']) : 0,
            'updated_at'        => date('Y-m-d H:i:s'),
        );

        $this->common->update('recipes', $recipe_data, array('id' => $recipe_id));

        // Replace product mappings
        $this->db->delete('recipe_products', array('recipe_id' => $recipe_id));
        $this->_save_recipe_products($recipe_id, $post_data);

        echo json_encode(array('status' => 200, 'message' => 'Recipe updated successfully.'));
    }

    // ==================== STATUS / DELETE ====================

    public function change_status()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400, 'message' => 'Please login again.'));
            return;
        }
        $id = intval($this->input->post('id'));
        $status = intval($this->input->post('status'));
        if (empty($id) || !in_array($status, array(1, 2))) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid request.'));
            return;
        }
        $this->common->update('recipes', array('status' => $status, 'updated_at' => date('Y-m-d H:i:s')), array('id' => $id));
        echo json_encode(array('status' => 200, 'message' => 'Status updated.'));
    }

    public function delete_recipe()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400, 'message' => 'Please login again.'));
            return;
        }
        $id = intval($this->input->post('id'));
        if (empty($id)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid request.'));
            return;
        }
        $this->common->update('recipes', array('status' => 3, 'updated_at' => date('Y-m-d H:i:s')), array('id' => $id));
        echo json_encode(array('status' => 200, 'message' => 'Recipe deleted.'));
    }

    // ==================== HELPERS ====================

    private function _save_recipe_products($recipe_id, $post_data)
    {
        if (empty($post_data['product_ids']) || !is_array($post_data['product_ids'])) {
            return;
        }
        $quantities = isset($post_data['product_quantities']) ? $post_data['product_quantities'] : array();
        $sort = 0;
        foreach ($post_data['product_ids'] as $pid) {
            $pid = intval($pid);
            if (empty($pid)) continue;
            $qty = isset($quantities[$pid]) ? max(1, intval($quantities[$pid])) : 1;
            $this->common->insert(array(
                'recipe_id'        => $recipe_id,
                'product_id'       => $pid,
                'default_quantity' => $qty,
                'sort_order'       => $sort++,
            ), 'recipe_products');
        }
    }

    private function _extract_youtube_id($url)
    {
        if (empty($url)) return '';
        $patterns = array(
            '~youtube\.com/watch\?v=([A-Za-z0-9_-]{6,})~i',
            '~youtu\.be/([A-Za-z0-9_-]{6,})~i',
            '~youtube\.com/embed/([A-Za-z0-9_-]{6,})~i',
            '~youtube\.com/shorts/([A-Za-z0-9_-]{6,})~i',
            '~youtube\.com/v/([A-Za-z0-9_-]{6,})~i',
        );
        foreach ($patterns as $p) {
            if (preg_match($p, $url, $m)) return $m[1];
        }
        return '';
    }

    private function _unique_slug($base, $exclude_id = 0)
    {
        if (empty($base)) $base = 'recipe';
        $slug = $base;
        $i = 1;
        while (true) {
            $this->db->where('slug', $slug);
            if (!empty($exclude_id)) $this->db->where('id !=', $exclude_id);
            $exists = $this->db->get('recipes')->row();
            if (empty($exists)) return $slug;
            $slug = $base . '-' . (++$i);
        }
    }

    private function _handle_thumbnail_upload()
    {
        if (empty($_FILES['thumbnail']['name'])) return '';
        if ($_FILES['thumbnail']['error'] !== UPLOAD_ERR_OK) return '';

        $upload_path = FCPATH . 'uploads/recipes/';
        if (!is_dir($upload_path)) @mkdir($upload_path, 0755, true);

        $this->load->library('upload');
        $this->upload->initialize(array(
            'upload_path'   => $upload_path,
            'allowed_types' => 'jpg|jpeg|png|webp',
            'max_size'      => 4096,
            'encrypt_name'  => true,
        ));

        if (!$this->upload->do_upload('thumbnail')) {
            return false;
        }
        $upload_data = $this->upload->data();
        return $upload_data['file_name'];
    }
}
