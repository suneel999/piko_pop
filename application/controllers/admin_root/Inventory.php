<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Inventory extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    // ==================== INVENTORY REPORT ====================

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Inventory Report || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['datatable_buttons'] = true;
            $footer_data['datatable_buttons'] = true;

            // Get distinct brands for filter dropdown
            $data['brands'] = $this->get_distinct_brands();

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/inventory/index', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/inventory');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function report_data()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            // Build query conditions
            $where = array('status !=' => 3);

            // Category filter
            if (!empty($post_data['category']) && $post_data['category'] != 'all') {
                $category_id = $post_data['category'];
                // Get category and its subcategories
                $category_ids = array($category_id);

                // Get all child categories
                $children = $this->get_child_categories($category_id);
                if (!empty($children)) {
                    $category_ids = array_merge($category_ids, $children);
                }

                // For products with multiple categories (comma-separated)
                // We'll filter in PHP after fetching
            }

            // Brand filter
            $brand_filter = !empty($post_data['brand']) ? $post_data['brand'] : null;

            // Stock status filter
            $stock_filter = !empty($post_data['stock_status']) ? $post_data['stock_status'] : null;

            // Get all products
            $products = $this->common->getdatabytableall('products', $where, 'product_name', 'ASC');

            $data = array();
            $total_stock = 0;
            $total_value = 0;

            if (!empty($products)) {
                foreach ($products as $product) {
                    // Category filter check
                    if (!empty($post_data['category']) && $post_data['category'] != 'all') {
                        $product_cats = explode(',', $product->category_id);
                        $match = false;
                        foreach ($product_cats as $pcat) {
                            if (in_array($pcat, $category_ids)) {
                                $match = true;
                                break;
                            }
                        }
                        if (!$match) continue;
                    }

                    // Brand filter check
                    if (!empty($brand_filter)) {
                        if (empty($product->brand) || strtolower($product->brand) != strtolower($brand_filter)) {
                            continue;
                        }
                    }

                    // Stock filter check
                    $stock = isset($product->stock) ? floatval($product->stock) : 0;
                    if (!empty($stock_filter)) {
                        switch ($stock_filter) {
                            case 'in_stock':
                                if ($stock <= 0) continue 2;
                                break;
                            case 'low_stock':
                                if ($stock > 10 || $stock <= 0) continue 2;
                                break;
                            case 'out_of_stock':
                                if ($stock > 0) continue 2;
                                break;
                        }
                    }

                    // Get category names
                    $cat_names = '-';
                    if (!empty($product->category_id)) {
                        $cat_ids = explode(',', $product->category_id);
                        $names = array();
                        foreach ($cat_ids as $cid) {
                            $cat = $this->common->getdatabytable('categories', array('id' => $cid));
                            if ($cat) {
                                $names[] = $cat->category_name;
                            }
                        }
                        if (!empty($names)) {
                            $cat_names = implode(', ', $names);
                        }
                    }

                    // Weight display
                    $weight = '';
                    if (!empty($product->product_weight)) {
                        $weight = $product->product_weight;
                        if (!empty($product->weight_unit)) {
                            $weight .= ' ' . $product->weight_unit;
                        }
                    }

                    // Stock display with badge
                    $weight_unit = isset($product->weight_unit) ? $product->weight_unit : '';
                    if ($stock <= 0) {
                        $stock_display = '<span class="badge bg-soft-danger text-danger">Out of Stock</span>';
                    } elseif ($stock <= 10) {
                        $stock_display = '<span class="badge bg-soft-warning text-warning">' . $stock . ' ' . $weight_unit . '</span>';
                    } else {
                        $stock_display = '<span class="badge bg-soft-success text-success">' . $stock . ' ' . $weight_unit . '</span>';
                    }

                    // Calculate stock value (stock * sale_price)
                    $sale_price = isset($product->sale_price) ? floatval($product->sale_price) : 0;
                    $stock_value = $stock * $sale_price;

                    $total_stock += $stock;
                    $total_value += $stock_value;

                    $data[] = array(
                        $product->id,
                        $product->product_name,
                        $cat_names,
                        !empty($product->brand) ? $product->brand : '-',
                        !empty($weight) ? $weight : '-',
                        '₹' . number_format($product->mrp, 2),
                        '₹' . number_format($sale_price, 2),
                        $stock_display,
                        '₹' . number_format($stock_value, 2)
                    );
                }

                $response = array(
                    'status' => 200,
                    'message' => 'Data Found',
                    'data' => $data,
                    'totals' => array(
                        'total_stock' => $total_stock,
                        'total_value' => number_format($total_value, 2)
                    )
                );
            } else {
                $response = array(
                    'status' => 400,
                    'message' => 'No Data Found',
                    'data' => array(),
                    'totals' => array(
                        'total_stock' => 0,
                        'total_value' => '0.00'
                    )
                );
            }
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Please login again',
                'data' => array()
            );
        }
        echo json_encode($response);
    }

    // Get distinct brands from products
    private function get_distinct_brands()
    {
        $this->db->select('brand');
        $this->db->from('products');
        $this->db->where('status !=', 3);
        $this->db->where('brand !=', '');
        $this->db->group_by('brand');
        $this->db->order_by('brand', 'ASC');
        $query = $this->db->get();

        $brands = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                if (!empty($row->brand)) {
                    $brands[] = $row->brand;
                }
            }
        }
        return $brands;
    }

    // Get all child category IDs recursively
    private function get_child_categories($parent_id)
    {
        $children = array();
        $cats = $this->common->getdatabytableall('categories', array('parent_category_id' => $parent_id));

        if (!empty($cats)) {
            foreach ($cats as $cat) {
                $children[] = $cat->id;
                // Recursively get children of this category
                $grandchildren = $this->get_child_categories($cat->id);
                if (!empty($grandchildren)) {
                    $children = array_merge($children, $grandchildren);
                }
            }
        }
        return $children;
    }

    // ==================== ADD STOCK ====================

    public function add()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Add Stock || ' . config_item('application_name');
            $header_data['form_validation'] = true;
            $footer_data['form_validation'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;
            $header_data['typeahead'] = true;
            $footer_data['typeahead'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/inventory/add');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/add_stock');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // Search products for autocomplete
    public function search_products()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['query'])) {
                $query = $post_data['query'];
                $category_id = !empty($post_data['category_id']) ? $post_data['category_id'] : null;

                $this->db->select('id, product_name, stock, weight_unit');
                $this->db->from('products');
                $this->db->where('status !=', 3);
                $this->db->like('product_name', $query);

                if (!empty($category_id)) {
                    $this->db->like('category_id', $category_id);
                }

                $this->db->order_by('product_name', 'ASC');
                $this->db->limit(15);
                $result = $this->db->get();

                $products = array();
                if ($result->num_rows() > 0) {
                    foreach ($result->result() as $row) {
                        $products[] = array(
                            'product_id' => $row->id,
                            'product_name' => $row->product_name,
                            'current_stock' => floatval($row->stock),
                            'weight_unit' => $row->weight_unit
                        );
                    }
                }

                echo json_encode($products);
            } else {
                echo json_encode(array());
            }
        } else {
            echo json_encode(array());
        }
    }

    // Get product by ID
    public function get_product()
    {
        if (is_loggedin_admin()) {
            $product_id = $this->input->post('product_id');

            if (!empty($product_id)) {
                $product = $this->common->getdatabytable('products', array('id' => $product_id, 'status !=' => 3));

                if ($product) {
                    $response = array(
                        'status' => 200,
                        'data' => array(
                            'product_id' => $product->id,
                            'product_name' => $product->product_name,
                            'current_stock' => floatval($product->stock),
                            'weight_unit' => $product->weight_unit
                        )
                    );
                } else {
                    $response = array(
                        'status' => 400,
                        'message' => 'Product not found'
                    );
                }
            } else {
                $response = array(
                    'status' => 400,
                    'message' => 'Product ID is required'
                );
            }
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Please login again'
            );
        }

        echo json_encode($response);
    }

    // Update stock for a single product (AJAX row-by-row)
    public function update_stock_single()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['product_id']) && isset($post_data['add_qty'])) {
                $product_id = $post_data['product_id'];
                $add_qty = floatval($post_data['add_qty']);

                if ($add_qty <= 0) {
                    $response = array(
                        'status' => 400,
                        'message' => 'Quantity must be greater than 0'
                    );
                    echo json_encode($response);
                    return;
                }

                // Get current product stock
                $product = $this->common->getdatabytable('products', array('id' => $product_id));

                if ($product) {
                    $current_stock = floatval($product->stock);
                    $new_stock = $current_stock + $add_qty;

                    // Update product stock
                    $update_data = array('stock' => $new_stock);
                    $where_data = array('id' => $product_id);

                    $update = $this->common->update('products', $update_data, $where_data);

                    if ($update !== false) {
                        // Log the stock addition
                        $log_data = array(
                            'product_id' => $product_id,
                            'previous_stock' => $current_stock,
                            'added_qty' => $add_qty,
                            'new_stock' => $new_stock,
                            'added_by' => $this->session->userdata('admin_id'),
                            'added_at' => date('Y-m-d H:i:s')
                        );
                        $this->common->insert($log_data, 'stock_logs');

                        $response = array(
                            'status' => 200,
                            'message' => 'Stock updated successfully',
                            'new_stock' => $new_stock
                        );
                    } else {
                        $response = array(
                            'status' => 400,
                            'message' => 'Failed to update stock'
                        );
                    }
                } else {
                    $response = array(
                        'status' => 400,
                        'message' => 'Product not found'
                    );
                }
            } else {
                $response = array(
                    'status' => 400,
                    'message' => 'Product ID and quantity are required'
                );
            }
        } else {
            $response = array(
                'status' => 400,
                'message' => 'Please login again'
            );
        }

        echo json_encode($response);
    }
}
