<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Products extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    // ==================== CATEGORIES ====================

    public function categories()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Categories || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/categories/index');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/common');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function add_category()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Add Category || ' . config_item('application_name');
            $header_data['form_validation'] = true;
            $footer_data['form_validation'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/categories/add');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/categories');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function save_category()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['category_name'])) {
                $category_data = array(
                    'category_name'       => $post_data['category_name'],
                    'parent_category_id'  => !empty($post_data['parent_category']) ? $post_data['parent_category'] : 0,
                    'sort_order'          => !empty($post_data['sort_order']) ? $post_data['sort_order'] : 0,
                    'featured'            => !empty($post_data['featured']) ? 1 : 0,
                    'is_menu_item'        => !empty($post_data['is_menu_item']) ? 1 : 0,
                    'status'              => 1
                );

                // Handle icon upload
                $output_dir = FCPATH . 'uploads/categories/';
                if (!is_dir($output_dir)) {
                    mkdir($output_dir, 0755, true);
                }

                if (isset($_FILES["icon"]) && !empty($_FILES["icon"]['name'])) {
                    $file_exe = explode(".", $_FILES["icon"]["name"]);
                    $fileName = date('YmdHis') . '.' . end($file_exe);

                    if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                        $uploadPath = $output_dir . $fileName;
                        if (move_uploaded_file($_FILES["icon"]["tmp_name"], $uploadPath)) {
                            $category_data['icon'] = $fileName;
                        }
                    }
                }

                $insert = $this->common->insert($category_data, 'categories');
                if (!empty($insert)) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'New Category added successfully.'
                    );
                } else {
                    $response = array(
                        'status'    => 400,
                        'message'   => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 400,
                    'message'   => 'Category Name is required.'
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'message'   => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    public function categories_data_ajax()
    {
        if (is_loggedin_admin()) {
            $where_data = array(
                'status !=' => 3
            );
            $categories = $this->common->getdatabytableall('categories', $where_data, 'sort_order', 'ASC');

            if (!empty($categories)) {
                $data = array();
                foreach ($categories as $category) {
                    $status = null;
                    if ($category->status == 1) {
                        $status = '<div class="badge bg-soft-success font-size-12">Active</div>';
                        $next_status = 2;
                    } else {
                        $status = '<div class="badge bg-soft-warning font-size-12">Inactive</div>';
                        $next_status = 1;
                    }

                    // Get parent category name
                    $cat_name = $category->category_name;
                    if ($category->parent_category_id != 0) {
                        $parent = $this->common->getdatabytable('categories', array('id' => $category->parent_category_id));
                        if ($parent) {
                            $cat_name = $parent->category_name . ' / ' . $category->category_name;
                        }
                    }

                    $icon_img = !empty($category->icon) ? '<img src="' . base_url('uploads/categories/') . $category->icon . '" class="rounded avatar-sm">' : '-';

                    // Featured badge
                    $featured_badge = ($category->featured == 1) ? '<div class="badge bg-soft-info font-size-12">Featured</div>' : '<div class="badge bg-soft-secondary font-size-12">No</div>';

                    // Menu item badge
                    $menu_badge = ($category->is_menu_item == 1) ? '<div class="badge bg-soft-primary font-size-12">Yes</div>' : '<div class="badge bg-soft-secondary font-size-12">No</div>';

                    $data[] = array(
                        $category->id,
                        $cat_name,
                        $icon_img,
                        $category->sort_order,
                        $featured_badge,
                        $menu_badge,
                        $status,
                        '<a href="' . base_url("admin_root/products/edit_category/") . $category->id . '" class="px-3 text-primary"><i class="uil uil-pen font-size-18"></i></a>
                        <button onclick="change_status_category(' . $category->id . ', 3)" class="btn px-3 text-danger"><i class="uil uil-trash-alt font-size-18"></i></button>
                        <button onclick="change_status_category(' . $category->id . ',' . $next_status . ')" class="btn px-3 text-info"><i class="uil uil-eye font-size-18"></i></button>'
                    );
                }

                $response = array(
                    'status'    => 200,
                    'data'      => $data
                );
            } else {
                $response = array(
                    'status'    => 400,
                    'data'      => array()
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'data'      => array()
            );
        }
        echo json_encode($response);
    }

    public function change_category_status()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['status']) && !empty($post_data['id'])) {
                $category_data = array(
                    'status' => $post_data['status'],
                );

                $where_data = array(
                    'id' => $post_data['id']
                );

                $update = $this->common->update('categories', $category_data, $where_data);
                if (!empty($update)) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'Category status updated.'
                    );
                } else {
                    $response = array(
                        'status'    => 400,
                        'message'   => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 400,
                    'message'   => 'Some data is missing.'
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'message'   => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    public function edit_category($id)
    {
        if (is_loggedin_admin()) {
            $where_data = array('id' => $id);
            $data['category_data'] = $this->common->getdatabytable('categories', $where_data);

            if (!empty($data['category_data'])) {
                $header_data['title'] = 'Edit Category || ' . config_item('application_name');
                $header_data['form_validation'] = true;
                $footer_data['form_validation'] = true;
                $header_data['select_2'] = true;
                $footer_data['select_2'] = true;

                $this->load->view('admin/common/header', $header_data);
                $this->load->view('admin/common/sidebar');
                $this->load->view('admin/categories/edit', $data);
                $this->load->view('admin/common/footer', $footer_data);
                $this->load->view('admin/validation/categories');
            } else {
                redirect('admin_root/products/categories');
            }
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function update_category()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['category_name']) && !empty($post_data['cid'])) {
                $category_data = array(
                    'category_name'       => $post_data['category_name'],
                    'parent_category_id'  => !empty($post_data['parent_category']) ? $post_data['parent_category'] : 0,
                    'sort_order'          => !empty($post_data['sort_order']) ? $post_data['sort_order'] : 0,
                    'featured'            => !empty($post_data['featured']) ? 1 : 0,
                    'is_menu_item'        => !empty($post_data['is_menu_item']) ? 1 : 0,
                );

                // Handle icon upload
                $output_dir = FCPATH . 'uploads/categories/';
                if (!is_dir($output_dir)) {
                    mkdir($output_dir, 0755, true);
                }

                if (isset($_FILES["icon"]) && !empty($_FILES["icon"]['name'])) {
                    $file_exe = explode(".", $_FILES["icon"]["name"]);
                    $fileName = date('YmdHis') . '.' . end($file_exe);

                    if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
                        $uploadPath = $output_dir . $fileName;
                        if (move_uploaded_file($_FILES["icon"]["tmp_name"], $uploadPath)) {
                            $category_data['icon'] = $fileName;
                        }
                    }
                }

                $where_data = array('id' => $post_data['cid']);
                $update = $this->common->update('categories', $category_data, $where_data);

                if ($update !== false) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'Category updated successfully.'
                    );
                } else {
                    $response = array(
                        'status'    => 400,
                        'message'   => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 400,
                    'message'   => 'Category Name is required.'
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'message'   => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    // ==================== PRODUCTS ====================

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'All Products || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/products/index');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/common');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function add()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Add Product || ' . config_item('application_name');
            $header_data['form_validation'] = true;
            $footer_data['form_validation'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['js_tree'] = true;
            $footer_data['js_tree'] = true;
            $header_data['jq_ui'] = true;
            $footer_data['jq_ui'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/products/add');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/products');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function save_product()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['product_name']) && !empty($post_data['mrp']) && !empty($post_data['category_id'])) {

                $product_data = array(
                    'product_name'      => $post_data['product_name'],
                    'mrp'               => $post_data['mrp'],
                    'sale_price'        => !empty($post_data['sale_price']) ? $post_data['sale_price'] : $post_data['mrp'],
                    'stock'             => isset($post_data['stock']) ? floatval($post_data['stock']) : 0,
                    'is_variant'        => !empty($post_data['is_variant']) ? $post_data['is_variant'] : 0,
                    'brand'             => !empty($post_data['brand']) ? $post_data['brand'] : '',
                    'category_id'       => $post_data['category_id'],
                    'product_weight'    => !empty($post_data['product_weight']) ? $post_data['product_weight'] : '',
                    'weight_unit'       => !empty($post_data['weight_unit']) ? $post_data['weight_unit'] : '',
                    'short_description' => !empty($post_data['short_description']) ? $post_data['short_description'] : '',
                    'description'       => !empty($post_data['description']) ? $post_data['description'] : '',
                    'status'            => 1
                );

                // Handle meta data (Additional Information)
                if (!empty($post_data['meta_title']) && is_array($post_data['meta_title'])) {
                    $meta_data = array();
                    foreach ($post_data['meta_title'] as $index => $title) {
                        $content = isset($post_data['meta_content'][$index]) ? $post_data['meta_content'][$index] : '';
                        if (!empty(trim($title)) && !empty(trim($content))) {
                            $meta_data[trim($title)] = trim($content);
                        }
                    }
                    if (!empty($meta_data)) {
                        $product_data['meta_data'] = json_encode($meta_data);
                    }
                }

                // Handle multiple images upload
                $output_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
                if (!is_dir($output_dir)) {
                    mkdir($output_dir, 0755, true);
                }

                $images = array();
                if (isset($_FILES["product_images"]) && !empty($_FILES["product_images"]['name'][0])) {
                    $file_count = count($_FILES["product_images"]['name']);

                    for ($i = 0; $i < $file_count; $i++) {
                        // Check for upload errors
                        if ($_FILES["product_images"]["error"][$i] === UPLOAD_ERR_OK) {
                            if (!empty($_FILES["product_images"]['name'][$i])) {
                                $file_exe = explode(".", $_FILES["product_images"]["name"][$i]);
                                $fileName = date('YmdHis') . '_' . uniqid() . '.' . end($file_exe);

                                if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    $uploadPath = $output_dir . $fileName;
                                    if (move_uploaded_file($_FILES["product_images"]["tmp_name"][$i], $uploadPath)) {
                                        $images[] = $fileName;
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($images)) {
                    $product_data['images'] = json_encode($images);
                }

                // Handle instructions images upload
                $instructions_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . 'instructions' . DIRECTORY_SEPARATOR;
                if (!is_dir($instructions_dir)) {
                    mkdir($instructions_dir, 0755, true);
                }

                // Desktop instructions image
                if (isset($_FILES["instructions_image_desktop"]) && !empty($_FILES["instructions_image_desktop"]['name'])) {
                    if ($_FILES["instructions_image_desktop"]["error"] === UPLOAD_ERR_OK) {
                        $file_exe = explode(".", $_FILES["instructions_image_desktop"]["name"]);
                        $fileName = 'desktop_' . date('YmdHis') . '_' . uniqid() . '.' . end($file_exe);

                        if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $uploadPath = $instructions_dir . $fileName;
                            if (move_uploaded_file($_FILES["instructions_image_desktop"]["tmp_name"], $uploadPath)) {
                                $product_data['instructions_image_desktop'] = $fileName;
                            }
                        }
                    }
                }

                // Mobile instructions image
                if (isset($_FILES["instructions_image_mobile"]) && !empty($_FILES["instructions_image_mobile"]['name'])) {
                    if ($_FILES["instructions_image_mobile"]["error"] === UPLOAD_ERR_OK) {
                        $file_exe = explode(".", $_FILES["instructions_image_mobile"]["name"]);
                        $fileName = 'mobile_' . date('YmdHis') . '_' . uniqid() . '.' . end($file_exe);

                        if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $uploadPath = $instructions_dir . $fileName;
                            if (move_uploaded_file($_FILES["instructions_image_mobile"]["tmp_name"], $uploadPath)) {
                                $product_data['instructions_image_mobile'] = $fileName;
                            }
                        }
                    }
                }

                $insert = $this->common->insert($product_data, 'products');
                if (!empty($insert)) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'New Product added successfully.'
                    );
                } else {
                    $response = array(
                        'status'    => 400,
                        'message'   => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 400,
                    'message'   => 'Product Name, MRP and Category are required.'
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'message'   => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    public function products_data_ajax()
    {
        if (is_loggedin_admin()) {
            $where_data = array(
                'status !=' => 3
            );
            $products = $this->common->getdatabytableall('products', $where_data);

            if (!empty($products)) {
                $data = array();
                foreach ($products as $product) {
                    $status = null;
                    if ($product->status == 1) {
                        $status = '<div class="badge bg-soft-success font-size-12">Active</div>';
                        $next_status = 2;
                    } else {
                        $status = '<div class="badge bg-soft-warning font-size-12">Inactive</div>';
                        $next_status = 1;
                    }

                    // Get category name
                    $cat_name = '-';
                    if (!empty($product->category_id)) {
                        $category = $this->common->getdatabytable('categories', array('id' => $product->category_id));
                        if ($category) {
                            $cat_name = $category->category_name;
                        }
                    }

                    // Get first image
                    $images = json_decode($product->images);
                    $thumb = !empty($images[0]) ? '<img src="' . base_url('uploads/products/') . $images[0] . '" class="rounded avatar-sm">' : '-';

                    // Stock display
                    $stock = isset($product->stock) ? floatval($product->stock) : 0;
                    $weight_unit = isset($product->weight_unit) ? $product->weight_unit : '';

                    if ($stock <= 0) {
                        $stock_display = '<span class="badge bg-soft-danger">Out of Stock</span>';
                    } elseif ($stock <= 10) {
                        $stock_display = '<span class="badge bg-soft-warning">' . $stock . ' ' . $weight_unit . '</span>';
                    } else {
                        $stock_display = '<span class="badge bg-soft-success">' . $stock . ' ' . $weight_unit . '</span>';
                    }

                    $data[] = array(
                        $product->id,
                        $product->product_name,
                        $cat_name,
                        number_format($product->mrp, 2),
                        number_format($product->sale_price, 2),
                        $stock_display,
                        $thumb,
                        $status,
                        '<a href="' . base_url("admin_root/products/edit/") . $product->id . '" class="px-3 text-primary"><i class="uil uil-pen font-size-18"></i></a>
                        <button onclick="change_status_product(' . $product->id . ', 3)" class="btn px-3 text-danger"><i class="uil uil-trash-alt font-size-18"></i></button>
                        <button onclick="change_status_product(' . $product->id . ',' . $next_status . ')" class="btn px-3 text-info"><i class="uil uil-eye font-size-18"></i></button>'
                    );
                }

                $response = array(
                    'status'    => 200,
                    'data'      => $data
                );
            } else {
                $response = array(
                    'status'    => 400,
                    'data'      => array()
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'data'      => array()
            );
        }
        echo json_encode($response);
    }

    public function change_product_status()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['status']) && !empty($post_data['id'])) {
                $product_data = array(
                    'status' => $post_data['status'],
                );

                $where_data = array(
                    'id' => $post_data['id']
                );

                $update = $this->common->update('products', $product_data, $where_data);
                if (!empty($update)) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'Product status updated.'
                    );
                } else {
                    $response = array(
                        'status'    => 400,
                        'message'   => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 400,
                    'message'   => 'Some data is missing.'
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'message'   => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    public function edit($id)
    {
        if (is_loggedin_admin()) {
            $where_data = array('id' => $id);
            $data['product_data'] = $this->common->getdatabytable('products', $where_data);

            if (!empty($data['product_data'])) {
                // Load product variants
                $data['product_variants'] = $this->common->getdatabytableall('product_variants', array('product_id' => $id), 'id', 'ASC');

                $header_data['title'] = 'Edit Product || ' . config_item('application_name');
                $header_data['form_validation'] = true;
                $footer_data['form_validation'] = true;
                $header_data['select_2'] = true;
                $footer_data['select_2'] = true;
                $header_data['js_tree'] = true;
                $footer_data['js_tree'] = true;
                $header_data['jq_ui'] = true;
                $footer_data['jq_ui'] = true;
                $header_data['sweet_alert'] = true;
                $footer_data['sweet_alert'] = true;

                $this->load->view('admin/common/header', $header_data);
                $this->load->view('admin/common/sidebar');
                $this->load->view('admin/products/edit', $data);
                $this->load->view('admin/common/footer', $footer_data);
                $this->load->view('admin/validation/products');
            } else {
                redirect('admin_root/products');
            }
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function update_product()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['product_name']) && !empty($post_data['mrp']) && !empty($post_data['pid'])) {

                $product_data = array(
                    'product_name'      => $post_data['product_name'],
                    'mrp'               => $post_data['mrp'],
                    'sale_price'        => !empty($post_data['sale_price']) ? $post_data['sale_price'] : $post_data['mrp'],
                    'stock'             => isset($post_data['stock']) ? floatval($post_data['stock']) : 0,
                    'is_variant'        => isset($post_data['is_variant']) ? $post_data['is_variant'] : 0,
                    'brand'             => !empty($post_data['brand']) ? $post_data['brand'] : '',
                    'category_id'       => !empty($post_data['category_id']) ? $post_data['category_id'] : 0,
                    'product_weight'    => !empty($post_data['product_weight']) ? $post_data['product_weight'] : '',
                    'weight_unit'       => !empty($post_data['weight_unit']) ? $post_data['weight_unit'] : '',
                    'short_description' => !empty($post_data['short_description']) ? $post_data['short_description'] : '',
                    'description'       => !empty($post_data['description']) ? $post_data['description'] : '',
                );

                // Handle meta data (Additional Information)
                if (!empty($post_data['meta_title']) && is_array($post_data['meta_title'])) {
                    $meta_data = array();
                    foreach ($post_data['meta_title'] as $index => $title) {
                        $content = isset($post_data['meta_content'][$index]) ? $post_data['meta_content'][$index] : '';
                        if (!empty(trim($title)) && !empty(trim($content))) {
                            $meta_data[trim($title)] = trim($content);
                        }
                    }
                    $product_data['meta_data'] = json_encode($meta_data);
                }

                // Handle images
                $output_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;
                if (!is_dir($output_dir)) {
                    mkdir($output_dir, 0755, true);
                }

                // Start with existing images in their new order (from hidden inputs)
                $images = array();
                if (!empty($post_data['existing_images']) && is_array($post_data['existing_images'])) {
                    $images = $post_data['existing_images'];
                }

                // Add new uploaded images
                if (isset($_FILES["product_images"]) && !empty($_FILES["product_images"]['name'][0])) {
                    $file_count = count($_FILES["product_images"]['name']);

                    for ($i = 0; $i < $file_count; $i++) {
                        // Check for upload errors
                        if ($_FILES["product_images"]["error"][$i] === UPLOAD_ERR_OK) {
                            if (!empty($_FILES["product_images"]['name'][$i])) {
                                $file_exe = explode(".", $_FILES["product_images"]["name"][$i]);
                                $fileName = date('YmdHis') . '_' . uniqid() . '.' . end($file_exe);

                                if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                    $uploadPath = $output_dir . $fileName;
                                    if (move_uploaded_file($_FILES["product_images"]["tmp_name"][$i], $uploadPath)) {
                                        $images[] = $fileName;
                                    }
                                }
                            }
                        }
                    }
                }

                $product_data['images'] = json_encode($images);

                // Handle instructions images upload
                $instructions_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR . 'instructions' . DIRECTORY_SEPARATOR;
                if (!is_dir($instructions_dir)) {
                    mkdir($instructions_dir, 0755, true);
                }

                // Desktop instructions image
                if (isset($_FILES["instructions_image_desktop"]) && !empty($_FILES["instructions_image_desktop"]['name'])) {
                    if ($_FILES["instructions_image_desktop"]["error"] === UPLOAD_ERR_OK) {
                        $file_exe = explode(".", $_FILES["instructions_image_desktop"]["name"]);
                        $fileName = 'desktop_' . date('YmdHis') . '_' . uniqid() . '.' . end($file_exe);

                        if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $uploadPath = $instructions_dir . $fileName;
                            if (move_uploaded_file($_FILES["instructions_image_desktop"]["tmp_name"], $uploadPath)) {
                                $product_data['instructions_image_desktop'] = $fileName;
                            }
                        }
                    }
                } elseif (!empty($post_data['existing_instructions_desktop'])) {
                    $product_data['instructions_image_desktop'] = $post_data['existing_instructions_desktop'];
                } elseif (isset($post_data['remove_instructions_desktop']) && $post_data['remove_instructions_desktop'] == '1') {
                    $product_data['instructions_image_desktop'] = null;
                }

                // Mobile instructions image
                if (isset($_FILES["instructions_image_mobile"]) && !empty($_FILES["instructions_image_mobile"]['name'])) {
                    if ($_FILES["instructions_image_mobile"]["error"] === UPLOAD_ERR_OK) {
                        $file_exe = explode(".", $_FILES["instructions_image_mobile"]["name"]);
                        $fileName = 'mobile_' . date('YmdHis') . '_' . uniqid() . '.' . end($file_exe);

                        if (in_array(strtolower(end($file_exe)), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                            $uploadPath = $instructions_dir . $fileName;
                            if (move_uploaded_file($_FILES["instructions_image_mobile"]["tmp_name"], $uploadPath)) {
                                $product_data['instructions_image_mobile'] = $fileName;
                            }
                        }
                    }
                } elseif (!empty($post_data['existing_instructions_mobile'])) {
                    $product_data['instructions_image_mobile'] = $post_data['existing_instructions_mobile'];
                } elseif (isset($post_data['remove_instructions_mobile']) && $post_data['remove_instructions_mobile'] == '1') {
                    $product_data['instructions_image_mobile'] = null;
                }

                $where_data = array('id' => $post_data['pid']);
                $update = $this->common->update('products', $product_data, $where_data);

                if ($update !== false) {
                    $product_id = $post_data['pid'];

                    // Handle deleted variants
                    if (!empty($post_data['deleted_variant_ids']) && is_array($post_data['deleted_variant_ids'])) {
                        foreach ($post_data['deleted_variant_ids'] as $variant_id) {
                            $this->common->deleteWhere('product_variants', array('id' => $variant_id, 'product_id' => $product_id));
                        }
                    }

                    // Handle existing variants update
                    if (!empty($post_data['existing_variant_id']) && is_array($post_data['existing_variant_id'])) {
                        foreach ($post_data['existing_variant_id'] as $index => $variant_id) {
                            $variant_data = array(
                                'variant_name'      => isset($post_data['existing_variant_name'][$index]) ? $post_data['existing_variant_name'][$index] : '',
                                'mrp'               => isset($post_data['existing_variant_mrp'][$index]) ? $post_data['existing_variant_mrp'][$index] : 0,
                                'sale_price'        => isset($post_data['existing_variant_sale_price'][$index]) ? $post_data['existing_variant_sale_price'][$index] : 0,
                                'stock_qty'         => isset($post_data['existing_variant_stock_qty'][$index]) ? floatval($post_data['existing_variant_stock_qty'][$index]) : 0,
                                'sku'               => isset($post_data['existing_variant_sku'][$index]) ? $post_data['existing_variant_sku'][$index] : '',
                                'servers_text'      => isset($post_data['existing_variant_servers_text'][$index]) ? $post_data['existing_variant_servers_text'][$index] : '',
                                'package_weight'    => isset($post_data['existing_variant_package_weight'][$index]) ? floatval($post_data['existing_variant_package_weight'][$index]) : 0
                            );
                            $this->common->update('product_variants', $variant_data, array('id' => $variant_id));
                        }
                    }

                    // Handle new variants
                    if (!empty($post_data['new_variant_name']) && is_array($post_data['new_variant_name'])) {
                        foreach ($post_data['new_variant_name'] as $index => $variant_name) {
                            if (!empty(trim($variant_name))) {
                                $new_variant = array(
                                    'product_id'        => $product_id,
                                    'variant_name'      => $variant_name,
                                    'mrp'               => isset($post_data['new_variant_mrp'][$index]) ? $post_data['new_variant_mrp'][$index] : 0,
                                    'sale_price'        => isset($post_data['new_variant_sale_price'][$index]) ? $post_data['new_variant_sale_price'][$index] : 0,
                                    'stock_qty'         => isset($post_data['new_variant_stock_qty'][$index]) ? floatval($post_data['new_variant_stock_qty'][$index]) : 0,
                                    'sku'               => isset($post_data['new_variant_sku'][$index]) ? $post_data['new_variant_sku'][$index] : '',
                                    'servers_text'      => isset($post_data['new_variant_servers_text'][$index]) ? $post_data['new_variant_servers_text'][$index] : '',
                                    'package_weight'    => isset($post_data['new_variant_package_weight'][$index]) ? floatval($post_data['new_variant_package_weight'][$index]) : 0,
                                    'status'       => 1
                                );
                                $this->common->insert($new_variant, 'product_variants');
                            }
                        }
                    }

                    $response = array(
                        'status'    => 200,
                        'message'   => 'Product updated successfully.'
                    );
                } else {
                    $response = array(
                        'status'    => 400,
                        'message'   => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 400,
                    'message'   => 'Product Name and MRP are required.'
                );
            }
        } else {
            $response = array(
                'status'    => 400,
                'message'   => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    // Get all parent categories for dropdown
    public function get_parent_categories()
    {
        if (is_loggedin_admin()) {
            $where_data = array(
                'parent_category_id' => 0,
                'status' => 1
            );
            $categories = $this->common->getdatabytableall('categories', $where_data, 'sort_order', 'ASC');

            $response = array(
                'status'    => 200,
                'data'      => $categories
            );
        } else {
            $response = array(
                'status'    => 400,
                'data'      => array()
            );
        }
        echo json_encode($response);
    }
}
