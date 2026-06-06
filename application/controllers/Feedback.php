<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Feedback extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
        $this->load->model('Orders_model', 'orders');
    }

    public function index($token = null)
    {
        if (empty($token)) {
            redirect(base_url());
            return;
        }

        // Look up the review by token
        $review = $this->common->getdatabytable('order_reviews', array('review_token' => $token));

        if (empty($review)) {
            // Invalid token
            $data['title'] = 'Invalid Link || ' . config_item('application_name');
            $data['error'] = true;
            $this->load->view('user/feedback/index', $data);
            return;
        }

        // Get order details
        $order = $this->orders->get_order_with_details($review->order_id);
        if (empty($order)) {
            $data['title'] = 'Order Not Found || ' . config_item('application_name');
            $data['error'] = true;
            $this->load->view('user/feedback/index', $data);
            return;
        }

        // Check if already submitted
        if (!empty($review->overall_rating)) {
            $data['title'] = 'Thank You || ' . config_item('application_name');
            $data['order'] = $order;
            $data['review'] = $review;
            $this->load->view('user/feedback/thankyou', $data);
            return;
        }

        // Show feedback form
        $data['title'] = 'Share Your Feedback || ' . config_item('application_name');
        $data['order'] = $order;
        $data['review'] = $review;
        $data['order_items'] = $this->orders->get_order_items($review->order_id);
        $data['error'] = false;

        $this->load->view('user/feedback/index', $data);
    }

    // Universal guest feedback page (no order linked)
    public function guest()
    {
        $data['title'] = 'Share Your Feedback || ' . config_item('application_name');
        $data['is_guest'] = true;
        $data['error'] = false;
        $data['success'] = $this->input->get('success') == '1';

        $this->load->view('user/feedback/index', $data);
    }

    // AJAX: Submit guest feedback
    public function guest_submit()
    {
        $post_data = $this->input->post(null, true);

        if (empty($post_data['overall_rating'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please provide an overall rating'));
            return;
        }

        if (empty($post_data['customer_name'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter your name'));
            return;
        }

        $token = generate_uuid();

        $insert_data = array(
            'order_id' => null,
            'review_token' => $token,
            'customer_name' => $post_data['customer_name'],
            'overall_rating' => intval($post_data['overall_rating']),
            'taste_feedback' => !empty($post_data['taste_feedback']) ? $post_data['taste_feedback'] : null,
            'ease_of_cooking' => !empty($post_data['ease_of_cooking']) ? $post_data['ease_of_cooking'] : null,
            'restaurant_taste' => !empty($post_data['restaurant_taste']) ? $post_data['restaurant_taste'] : null,
            'instructions_clarity' => !empty($post_data['instructions_clarity']) ? $post_data['instructions_clarity'] : null,
            'packaging_feedback' => !empty($post_data['packaging_feedback']) ? $post_data['packaging_feedback'] : null,
            'value_for_money' => !empty($post_data['value_for_money']) ? $post_data['value_for_money'] : null,
            'recommendation' => !empty($post_data['recommendation']) ? $post_data['recommendation'] : null,
            'repeat_purchase' => !empty($post_data['repeat_purchase']) ? $post_data['repeat_purchase'] : null,
            'liked_most' => !empty($post_data['liked_most']) ? $post_data['liked_most'] : null,
            'improvement' => !empty($post_data['improvement']) ? $post_data['improvement'] : null,
            'guest_phone' => !empty($post_data['guest_phone']) ? $post_data['guest_phone'] : null,
            'status' => 1
        );

        // Handle image upload
        if (isset($_FILES['review_image']) && !empty($_FILES['review_image']['name'])) {
            $upload_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'reviews' . DIRECTORY_SEPARATOR;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if ($_FILES['review_image']['error'] === UPLOAD_ERR_OK) {
                $file_ext = pathinfo($_FILES['review_image']['name'], PATHINFO_EXTENSION);
                $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                if (in_array(strtolower($file_ext), $allowed)) {
                    $fileName = $token . '_review.' . strtolower($file_ext);
                    $uploadPath = $upload_dir . $fileName;
                    if (move_uploaded_file($_FILES['review_image']['tmp_name'], $uploadPath)) {
                        $insert_data['review_image'] = $fileName;
                    }
                }
            }
        }

        $result = $this->common->insert($insert_data, 'order_reviews');

        if ($result) {
            echo json_encode(array('status' => 200, 'message' => 'Thank you for your feedback!', 'is_guest' => true));
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Something went wrong. Please try again.'));
        }
    }

    public function submit()
    {
        $post_data = $this->input->post(null, true);

        if (empty($post_data['review_token'])) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid request'));
            return;
        }

        $review = $this->common->getdatabytable('order_reviews', array('review_token' => $post_data['review_token']));

        if (empty($review)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid review link'));
            return;
        }

        // Already submitted
        if (!empty($review->overall_rating)) {
            echo json_encode(array('status' => 400, 'message' => 'Feedback already submitted for this order'));
            return;
        }

        // Validate required field
        if (empty($post_data['overall_rating'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please provide an overall rating'));
            return;
        }

        // Build update data
        $update_data = array(
            'customer_name' => !empty($post_data['customer_name']) ? $post_data['customer_name'] : null,
            'overall_rating' => intval($post_data['overall_rating']),
            'taste_feedback' => !empty($post_data['taste_feedback']) ? $post_data['taste_feedback'] : null,
            'ease_of_cooking' => !empty($post_data['ease_of_cooking']) ? $post_data['ease_of_cooking'] : null,
            'restaurant_taste' => !empty($post_data['restaurant_taste']) ? $post_data['restaurant_taste'] : null,
            'instructions_clarity' => !empty($post_data['instructions_clarity']) ? $post_data['instructions_clarity'] : null,
            'packaging_feedback' => !empty($post_data['packaging_feedback']) ? $post_data['packaging_feedback'] : null,
            'value_for_money' => !empty($post_data['value_for_money']) ? $post_data['value_for_money'] : null,
            'recommendation' => !empty($post_data['recommendation']) ? $post_data['recommendation'] : null,
            'repeat_purchase' => !empty($post_data['repeat_purchase']) ? $post_data['repeat_purchase'] : null,
            'liked_most' => !empty($post_data['liked_most']) ? $post_data['liked_most'] : null,
            'improvement' => !empty($post_data['improvement']) ? $post_data['improvement'] : null,
        );

        // Handle image upload
        if (isset($_FILES['review_image']) && !empty($_FILES['review_image']['name'])) {
            $upload_dir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'reviews' . DIRECTORY_SEPARATOR;
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if ($_FILES['review_image']['error'] === UPLOAD_ERR_OK) {
                $file_ext = pathinfo($_FILES['review_image']['name'], PATHINFO_EXTENSION);
                $allowed = array('jpg', 'jpeg', 'png', 'gif', 'webp');
                if (in_array(strtolower($file_ext), $allowed)) {
                    $fileName = $post_data['review_token'] . '_review.' . strtolower($file_ext);
                    $uploadPath = $upload_dir . $fileName;
                    if (move_uploaded_file($_FILES['review_image']['tmp_name'], $uploadPath)) {
                        $update_data['review_image'] = $fileName;
                    }
                }
            }
        }

        $result = $this->common->update('order_reviews', $update_data, array('id' => $review->id));

        if ($result) {
            echo json_encode(array('status' => 200, 'message' => 'Thank you for your feedback!'));
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Something went wrong. Please try again.'));
        }
    }
}
