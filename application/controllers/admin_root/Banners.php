<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Banners extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    // ==================== BANNERS LIST ====================

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Banners || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/banners/index');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/banners_list');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function banners_data_ajax()
    {
        if (is_loggedin_admin()) {
            $where_data = array(
                'status !=' => 3
            );
            $banners = $this->common->getdatabytableall('banners', $where_data, 'sort_order', 'ASC');

            if (!empty($banners)) {
                $data = array();
                foreach ($banners as $banner) {
                    $status = null;
                    if ($banner->status == 1) {
                        $status = '<div class="badge bg-soft-success font-size-12">Active</div>';
                        $next_status = 2;
                    } else {
                        $status = '<div class="badge bg-soft-warning font-size-12">Inactive</div>';
                        $next_status = 1;
                    }

                    // Desktop image thumbnail
                    $desktop_thumb = !empty($banner->desktop_image)
                        ? '<img src="' . base_url('uploads/banners/') . $banner->desktop_image . '" class="rounded" style="max-width:80px;max-height:50px;" title="Desktop">'
                        : '-';

                    // Mobile image thumbnail
                    $mobile_thumb = !empty($banner->mobile_image)
                        ? '<img src="' . base_url('uploads/banners/') . $banner->mobile_image . '" class="rounded" style="max-width:50px;max-height:50px;" title="Mobile">'
                        : '-';

                    $data[] = array(
                        $banner->id,
                        $banner->title,
                        $desktop_thumb,
                        $mobile_thumb,
                        $banner->sort_order,
                        $status,
                        '<a href="' . base_url("admin_root/banners/edit/") . $banner->id . '" class="px-3 text-primary"><i class="uil uil-pen font-size-18"></i></a>
                        <button onclick="delete_banner(' . $banner->id . ')" class="btn px-3 text-danger"><i class="uil uil-trash-alt font-size-18"></i></button>
                        <button onclick="change_status(' . $banner->id . ',' . $next_status . ')" class="btn px-3 text-info"><i class="uil uil-eye font-size-18"></i></button>'
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

    // ==================== ADD BANNER ====================

    public function add()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Add Banner || ' . config_item('application_name');
            $header_data['form_validation'] = true;
            $footer_data['form_validation'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/banners/add');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/banners_add');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function save_banner()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['title'])) {
                $banner_data = array(
                    'title'           => $post_data['title'],
                    'target_link'     => !empty($post_data['target_link']) ? $post_data['target_link'] : '',
                    'sort_order'      => !empty($post_data['sort_order']) ? $post_data['sort_order'] : 0,
                    'status'          => 1
                );

                // Handle image uploads
                $output_dir = FCPATH . 'uploads/banners/';
                if (!is_dir($output_dir)) {
                    mkdir($output_dir, 0755, true);
                }

                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                // Desktop image upload
                if (isset($_FILES["desktop_image"]) && !empty($_FILES["desktop_image"]['name'])) {
                    $file_exe = explode(".", $_FILES["desktop_image"]["name"]);
                    $fileName = date('YmdHis') . '_desktop_' . uniqid() . '.' . end($file_exe);

                    if (in_array(strtolower(end($file_exe)), $allowed_extensions)) {
                        $uploadPath = $output_dir . $fileName;
                        if (move_uploaded_file($_FILES["desktop_image"]["tmp_name"], $uploadPath)) {
                            $banner_data['desktop_image'] = $fileName;
                        }
                    }
                }

                // Mobile image upload
                if (isset($_FILES["mobile_image"]) && !empty($_FILES["mobile_image"]['name'])) {
                    $file_exe = explode(".", $_FILES["mobile_image"]["name"]);
                    $fileName = date('YmdHis') . '_mobile_' . uniqid() . '.' . end($file_exe);

                    if (in_array(strtolower(end($file_exe)), $allowed_extensions)) {
                        $uploadPath = $output_dir . $fileName;
                        if (move_uploaded_file($_FILES["mobile_image"]["tmp_name"], $uploadPath)) {
                            $banner_data['mobile_image'] = $fileName;
                        }
                    }
                }

                if (!empty($banner_data['desktop_image']) && !empty($banner_data['mobile_image'])) {
                    $insert = $this->common->insert($banner_data, 'banners');
                    if (!empty($insert)) {
                        $response = array(
                            'status'    => 200,
                            'message'   => 'Banner added successfully.'
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
                        'message'   => 'Please upload both desktop and mobile banner images.'
                    );
                }
            } else {
                $response = array(
                    'status'    => 400,
                    'message'   => 'Title is required.'
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

    // ==================== EDIT BANNER ====================

    public function edit($id)
    {
        if (is_loggedin_admin()) {
            $where_data = array('id' => $id);
            $data['banner_data'] = $this->common->getdatabytable('banners', $where_data);

            if (!empty($data['banner_data'])) {
                $header_data['title'] = 'Edit Banner || ' . config_item('application_name');
                $header_data['form_validation'] = true;
                $footer_data['form_validation'] = true;

                $this->load->view('admin/common/header', $header_data);
                $this->load->view('admin/common/sidebar');
                $this->load->view('admin/banners/edit', $data);
                $this->load->view('admin/common/footer', $footer_data);
                $this->load->view('admin/validation/banners_edit');
            } else {
                redirect('admin_root/banners');
            }
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    public function update_banner()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['title']) && !empty($post_data['bid'])) {
                $banner_data = array(
                    'title'           => $post_data['title'],
                    'target_link'     => !empty($post_data['target_link']) ? $post_data['target_link'] : '',
                    'sort_order'      => !empty($post_data['sort_order']) ? $post_data['sort_order'] : 0,
                );

                // Handle image uploads
                $output_dir = FCPATH . 'uploads/banners/';
                if (!is_dir($output_dir)) {
                    mkdir($output_dir, 0755, true);
                }

                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

                // Desktop image upload
                if (isset($_FILES["desktop_image"]) && !empty($_FILES["desktop_image"]['name'])) {
                    $file_exe = explode(".", $_FILES["desktop_image"]["name"]);
                    $fileName = date('YmdHis') . '_desktop_' . uniqid() . '.' . end($file_exe);

                    if (in_array(strtolower(end($file_exe)), $allowed_extensions)) {
                        $uploadPath = $output_dir . $fileName;
                        if (move_uploaded_file($_FILES["desktop_image"]["tmp_name"], $uploadPath)) {
                            $banner_data['desktop_image'] = $fileName;
                        }
                    }
                }

                // Mobile image upload
                if (isset($_FILES["mobile_image"]) && !empty($_FILES["mobile_image"]['name'])) {
                    $file_exe = explode(".", $_FILES["mobile_image"]["name"]);
                    $fileName = date('YmdHis') . '_mobile_' . uniqid() . '.' . end($file_exe);

                    if (in_array(strtolower(end($file_exe)), $allowed_extensions)) {
                        $uploadPath = $output_dir . $fileName;
                        if (move_uploaded_file($_FILES["mobile_image"]["tmp_name"], $uploadPath)) {
                            $banner_data['mobile_image'] = $fileName;
                        }
                    }
                }

                $where_data = array('id' => $post_data['bid']);
                $update = $this->common->update('banners', $banner_data, $where_data);

                if ($update !== false) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'Banner updated successfully.'
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
                    'message'   => 'Title is required.'
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

    // ==================== DELETE & STATUS ====================

    public function delete_banner()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['id'])) {
                $banner_data = array('status' => 3);
                $where_data = array('id' => $post_data['id']);

                $update = $this->common->update('banners', $banner_data, $where_data);
                if (!empty($update)) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'Banner deleted successfully.'
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
                    'message'   => 'Banner ID is required.'
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

    public function change_status()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (!empty($post_data['status']) && !empty($post_data['id'])) {
                $banner_data = array('status' => $post_data['status']);
                $where_data = array('id' => $post_data['id']);

                $update = $this->common->update('banners', $banner_data, $where_data);
                if (!empty($update)) {
                    $response = array(
                        'status'    => 200,
                        'message'   => 'Banner status updated.'
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
}
