<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Coupons extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    // ==================== COUPONS LIST ====================

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Coupons || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $where_data = array('status !=' => 3);
            $data['coupons'] = $this->common->getdatabytableall('coupons', $where_data, 'id', 'DESC');

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/coupons/index', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/coupons_list');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // ==================== ADD COUPON ====================

    public function add()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Add Coupon || ' . config_item('application_name');
            $header_data['form_validation'] = true;
            $footer_data['form_validation'] = true;
            $header_data['datepicker'] = true;
            $footer_data['datepicker'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/coupons/add');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/coupons_add');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function save_coupon()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['title']) && !empty($post_data['coupon_code'])) {
                // Check if coupon code already exists
                $existing = $this->common->getdatabytable('coupons', array(
                    'coupon_code' => strtoupper($post_data['coupon_code']),
                    'status !=' => 3
                ));

                if ($existing) {
                    $response = array(
                        'status' => 400,
                        'message' => 'Coupon code already exists.'
                    );
                    echo json_encode($response);
                    return;
                }

                $coupon_data = array(
                    'title'          => $post_data['title'],
                    'coupon_code'    => strtoupper($post_data['coupon_code']),
                    'coupon_type'    => $post_data['coupon_type'],
                    'discount_value' => floatval($post_data['discount_value']),
                    'min_amount'     => !empty($post_data['min_amount']) ? floatval($post_data['min_amount']) : 0,
                    'max_discount'   => !empty($post_data['max_discount']) ? floatval($post_data['max_discount']) : 0,
                    'usage_limit'    => !empty($post_data['usage_limit']) ? intval($post_data['usage_limit']) : 0,
                    'used_count'     => 0,
                    'start_date'     => !empty($post_data['start_date']) ? $post_data['start_date'] : date('Y-m-d'),
                    'end_date'       => !empty($post_data['end_date']) ? $post_data['end_date'] : null,
                    'status'         => 1,
                    'created_by'     => $this->session->userdata('admin_id'),
                    'created_at'     => date('Y-m-d H:i:s')
                );

                $insert = $this->common->insert($coupon_data, 'coupons');

                if (!empty($insert)) {
                    $response = array(
                        'status'  => 200,
                        'message' => 'Coupon created successfully.'
                    );
                } else {
                    $response = array(
                        'status'  => 400,
                        'message' => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'  => 400,
                    'message' => 'Title and Coupon Code are required.'
                );
            }
        } else {
            $response = array(
                'status'  => 400,
                'message' => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    // ==================== EDIT COUPON ====================

    public function edit($id)
    {
        if (is_loggedin_admin()) {
            $where_data = array('id' => $id, 'status !=' => 3);
            $data['coupon_data'] = $this->common->getdatabytable('coupons', $where_data);

            if (!empty($data['coupon_data'])) {
                $header_data['title'] = 'Edit Coupon || ' . config_item('application_name');
                $header_data['form_validation'] = true;
                $footer_data['form_validation'] = true;
                $header_data['datepicker'] = true;
                $footer_data['datepicker'] = true;

                $this->load->view('admin/common/header', $header_data);
                $this->load->view('admin/common/sidebar');
                $this->load->view('admin/coupons/edit', $data);
                $this->load->view('admin/common/footer', $footer_data);
                $this->load->view('admin/validation/coupons_edit');
            } else {
                redirect('admin_root/coupons');
            }
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function update_coupon()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['title']) && !empty($post_data['coupon_code']) && !empty($post_data['cid'])) {
                // Check if coupon code already exists (excluding current coupon)
                $this->db->where('coupon_code', strtoupper($post_data['coupon_code']));
                $this->db->where('id !=', $post_data['cid']);
                $this->db->where('status !=', 3);
                $existing = $this->db->get('coupons')->row();

                if ($existing) {
                    $response = array(
                        'status' => 400,
                        'message' => 'Coupon code already exists.'
                    );
                    echo json_encode($response);
                    return;
                }

                $coupon_data = array(
                    'title'          => $post_data['title'],
                    'coupon_code'    => strtoupper($post_data['coupon_code']),
                    'coupon_type'    => $post_data['coupon_type'],
                    'discount_value' => floatval($post_data['discount_value']),
                    'min_amount'     => !empty($post_data['min_amount']) ? floatval($post_data['min_amount']) : 0,
                    'max_discount'   => !empty($post_data['max_discount']) ? floatval($post_data['max_discount']) : 0,
                    'usage_limit'    => !empty($post_data['usage_limit']) ? intval($post_data['usage_limit']) : 0,
                    'start_date'     => !empty($post_data['start_date']) ? $post_data['start_date'] : date('Y-m-d'),
                    'end_date'       => !empty($post_data['end_date']) ? $post_data['end_date'] : null,
                );

                $where_data = array('id' => $post_data['cid']);
                $update = $this->common->update('coupons', $coupon_data, $where_data);

                if ($update !== false) {
                    $response = array(
                        'status'  => 200,
                        'message' => 'Coupon updated successfully.'
                    );
                } else {
                    $response = array(
                        'status'  => 400,
                        'message' => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'  => 400,
                    'message' => 'Title and Coupon Code are required.'
                );
            }
        } else {
            $response = array(
                'status'  => 400,
                'message' => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    // ==================== DELETE / STATUS CHANGE ====================

    public function delete_coupon()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['id'])) {
                $coupon_data = array('status' => 3);
                $where_data = array('id' => $post_data['id']);

                $update = $this->common->update('coupons', $coupon_data, $where_data);

                if (!empty($update)) {
                    $response = array(
                        'status'  => 200,
                        'message' => 'Coupon deleted successfully.'
                    );
                } else {
                    $response = array(
                        'status'  => 400,
                        'message' => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'  => 400,
                    'message' => 'Coupon ID is required.'
                );
            }
        } else {
            $response = array(
                'status'  => 400,
                'message' => 'Please login again.'
            );
        }

        echo json_encode($response);
    }

    public function change_status()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['id']) && isset($post_data['status'])) {
                $coupon_data = array('status' => $post_data['status']);
                $where_data = array('id' => $post_data['id']);

                $update = $this->common->update('coupons', $coupon_data, $where_data);

                if (!empty($update)) {
                    $response = array(
                        'status'  => 200,
                        'message' => 'Coupon status updated.'
                    );
                } else {
                    $response = array(
                        'status'  => 400,
                        'message' => 'Something went wrong.'
                    );
                }
            } else {
                $response = array(
                    'status'  => 400,
                    'message' => 'Data is missing.'
                );
            }
        } else {
            $response = array(
                'status'  => 400,
                'message' => 'Please login again.'
            );
        }

        echo json_encode($response);
    }
}
