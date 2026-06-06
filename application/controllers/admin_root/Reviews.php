<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reviews extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
        $this->load->model('Orders_model', 'orders');
    }

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Customer Reviews || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['datatable_buttons'] = true;
            $footer_data['datatable_buttons'] = true;
            $header_data['flatpickr'] = true;
            $footer_data['flatpickr'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/reviews/index');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/reviews');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Get reviews data for DataTable
    public function reviews_data()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            $this->db->select('order_reviews.*, orders.order_number, orders.delivery_fullname, orders.delivery_phone, orders.created_at as order_date');
            $this->db->from('order_reviews');
            $this->db->join('orders', 'orders.id = order_reviews.order_id', 'left');
            $this->db->where('order_reviews.status !=', 3);
            // Only show submitted reviews (overall_rating is not null)
            $this->db->where('order_reviews.overall_rating IS NOT NULL', null, false);

            if (!empty($post_data['date_from'])) {
                $this->db->where('order_reviews.created_at >=', $post_data['date_from'] . ' 00:00:00');
            }
            if (!empty($post_data['date_to'])) {
                $this->db->where('order_reviews.created_at <=', $post_data['date_to'] . ' 23:59:59');
            }
            if (!empty($post_data['rating'])) {
                $this->db->where('order_reviews.overall_rating', intval($post_data['rating']));
            }

            $this->db->order_by('order_reviews.created_at', 'DESC');
            $reviews = $this->db->get()->result();

            $data = array();
            $total_reviews = 0;
            $total_rating = 0;
            $five_star_count = 0;
            $recommend_count = 0;

            if (!empty($reviews)) {
                $counter = 0;
                foreach ($reviews as $review) {
                    $counter++;
                    $total_reviews++;
                    $rating = intval($review->overall_rating);
                    $total_rating += $rating;

                    if ($rating == 5) $five_star_count++;
                    if ($review->recommendation == 'Definitely') $recommend_count++;

                    // Star display (using HTML entity ★ since uil-star is outline-only)
                    $stars = '<span style="font-size: 1.1rem; letter-spacing: 2px;">';
                    for ($i = 1; $i <= 5; $i++) {
                        $stars .= $i <= $rating
                            ? '<span style="color: #f59e0b;">&#9733;</span>'
                            : '<span style="color: #e5e7eb;">&#9733;</span>';
                    }
                    $stars .= '</span> <span class="badge bg-soft-primary text-primary ms-1">' . $rating . '/5</span>';

                    $is_guest_review = empty($review->order_id);
                    $customer = !empty($review->customer_name) ? htmlspecialchars($review->customer_name) : (!empty($review->delivery_fullname) ? htmlspecialchars($review->delivery_fullname) : 'Guest');
                    $phone = !empty($review->delivery_phone) ? htmlspecialchars($review->delivery_phone) : (!empty($review->guest_phone) ? htmlspecialchars($review->guest_phone) : '-');

                    $order_col = $is_guest_review
                        ? '<span class="badge bg-soft-secondary text-secondary">Guest</span>'
                        : '<a href="' . base_url('admin_root/orders/view/' . $review->order_id) . '" class="text-primary fw-bold">#' . htmlspecialchars($review->order_number) . '</a>';

                    $actions = '<a href="' . base_url('admin_root/reviews/view_review/' . $review->id) . '" class="btn btn-sm btn-soft-primary me-1" title="View"><i class="uil-eye"></i></a>';
                    if (!$is_guest_review) {
                        $actions .= '<button class="btn btn-sm btn-soft-info copy-review-link me-1" data-token="' . $review->review_token . '" title="Copy Link"><i class="uil-link"></i></button>';
                    }
                    $actions .= '<button class="btn btn-sm btn-soft-danger delete-review" data-id="' . $review->id . '" title="Delete"><i class="uil-trash-alt"></i></button>';

                    $data[] = array(
                        $counter,
                        $order_col,
                        $customer,
                        $phone,
                        $stars,
                        date('d M Y', strtotime($review->created_at)),
                        $actions
                    );
                }
            }

            $avg_rating = $total_reviews > 0 ? round($total_rating / $total_reviews, 1) : 0;
            $recommend_rate = $total_reviews > 0 ? round(($recommend_count / $total_reviews) * 100) : 0;

            $response = array(
                'status' => 200,
                'data' => $data,
                'totals' => array(
                    'total_reviews' => $total_reviews,
                    'avg_rating' => $avg_rating,
                    'five_star_count' => $five_star_count,
                    'recommend_rate' => $recommend_rate
                )
            );
        } else {
            $response = array(
                'status' => 400,
                'data' => array(),
                'totals' => array('total_reviews' => 0, 'avg_rating' => 0, 'five_star_count' => 0, 'recommend_rate' => 0)
            );
        }
        echo json_encode($response);
    }

    // View single review detail
    public function view_review($id = null)
    {
        if (is_loggedin_admin()) {
            if (empty($id)) {
                redirect(base_url('admin_root/reviews'));
                return;
            }

            $this->db->select('order_reviews.*, orders.order_number, orders.delivery_fullname, orders.delivery_phone, orders.total_amount, orders.created_at as order_date');
            $this->db->from('order_reviews');
            $this->db->join('orders', 'orders.id = order_reviews.order_id', 'left');
            $this->db->where('order_reviews.id', $id);
            $review = $this->db->get()->row();

            if (empty($review)) {
                redirect(base_url('admin_root/reviews'));
                return;
            }

            $header_data['title'] = 'Review Detail || ' . config_item('application_name');
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $data['review'] = $review;
            $data['order_items'] = $this->orders->get_order_items($review->order_id);

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/reviews/view', $data);
            $this->load->view('admin/common/footer', $footer_data);
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Generate review link for an order
    public function generate_link()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (empty($post_data['order_id'])) {
                echo json_encode(array('status' => 400, 'message' => 'Invalid order ID'));
                return;
            }

            $order_id = intval($post_data['order_id']);

            // Check if review already exists for this order
            $existing = $this->common->getdatabytable('order_reviews', array('order_id' => $order_id, 'status !=' => 3));

            if (!empty($existing)) {
                $link = base_url('feedback/' . $existing->review_token);
                $submitted = !empty($existing->overall_rating);
                echo json_encode(array(
                    'status' => 200,
                    'message' => 'Review link already exists',
                    'link' => $link,
                    'review_id' => $existing->id,
                    'submitted' => $submitted
                ));
                return;
            }

            // Generate new token
            $token = generate_uuid();

            $insert_data = array(
                'order_id' => $order_id,
                'review_token' => $token,
                'status' => 1
            );

            $insert = $this->common->insert($insert_data, 'order_reviews');

            if (!empty($insert)) {
                $link = base_url('feedback/' . $token);
                echo json_encode(array(
                    'status' => 200,
                    'message' => 'Review link generated successfully',
                    'link' => $link,
                    'review_id' => $insert,
                    'submitted' => false
                ));
            } else {
                echo json_encode(array('status' => 400, 'message' => 'Failed to generate link'));
            }
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Please login again'));
        }
    }

    // AJAX: Delete review (soft delete)
    public function delete_review()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (empty($post_data['id'])) {
                echo json_encode(array('status' => 400, 'message' => 'Invalid review ID'));
                return;
            }

            $update = $this->common->update('order_reviews', array('status' => 3), array('id' => $post_data['id']));

            if ($update) {
                echo json_encode(array('status' => 200, 'message' => 'Review deleted successfully'));
            } else {
                echo json_encode(array('status' => 400, 'message' => 'Failed to delete review'));
            }
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Please login again'));
        }
    }
}
