<?php
defined('BASEPATH') or exit('No direct script access allowed');


class Page extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }



    public function about()
    {
        // Prepare data for view
        $header_data['page_title'] = 'About Us | PIKO POP';


        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/page/about');
        $this->load->view('user/common/footer');
    }

    public function contact()
    {
        // Prepare data for view
        $header_data['page_title'] = 'Contact Us | PIKO POP';


        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/page/contact');
        $this->load->view('user/common/footer');
        $this->load->view('user/page/contact_js');
    }

    /**
     * Shipping Policy Page
     */
    public function shipping_policy()
    {
        $header_data['page_title'] = 'Shipping Policy | PIKO POP';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/page/shipping_policy');
        $this->load->view('user/common/footer');
    }

    /**
     * Return & Refund Policy Page
     */
    public function refund_policy()
    {
        $header_data['page_title'] = 'Return & Refund Policy | PIKO POP';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/page/refund_policy');
        $this->load->view('user/common/footer');
    }

    /**
     * Privacy Policy Page
     */
    public function privacy_policy()
    {
        $header_data['page_title'] = 'Privacy Policy | PIKO POP';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/page/privacy_policy');
        $this->load->view('user/common/footer');
    }

    /**
     * Terms & Conditions Page
     */
    public function terms_conditions()
    {
        $header_data['page_title'] = 'Terms & Conditions | PIKO POP';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/page/terms_conditions');
        $this->load->view('user/common/footer');
    }

    public function bulk_order()
    {
        $header_data['page_title'] = 'Party & Gift Orders | PIKO POP';

        $this->load->view('user/common/header', $header_data);
        $this->load->view('user/page/bulk_order');
        $this->load->view('user/common/footer');
        $this->load->view('user/page/bulk_order_js');
    }

    /**
     * Submit bulk order enquiry (AJAX)
     */
    public function submit_bulk_order()
    {
        $post_data = $this->input->post(null, true);

        if (empty($post_data['fullName'])) {
            echo json_encode(array('status' => 400, 'message' => 'Full name is required.'));
            return;
        }

        if (empty($post_data['email'])) {
            echo json_encode(array('status' => 400, 'message' => 'Email is required.'));
            return;
        }

        if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter a valid email address.'));
            return;
        }

        if (empty($post_data['phone'])) {
            echo json_encode(array('status' => 400, 'message' => 'Phone number is required.'));
            return;
        }

        $bulk_data = array(
            'fullName'     => trim($post_data['fullName']),
            'email'        => trim($post_data['email']),
            'phone'        => trim($post_data['phone']),
            'companyName'  => !empty($post_data['companyName']) ? trim($post_data['companyName']) : '',
            'eventType'    => !empty($post_data['eventType']) ? $post_data['eventType'] : '',
            'quantity'     => !empty($post_data['quantity']) ? trim($post_data['quantity']) : '',
            'message'      => !empty($post_data['message']) ? trim($post_data['message']) : ''
        );

        $this->load->library('Mailer');
        $result = $this->mailer->send_bulk_order_email($bulk_data);

        if ($result) {
            echo json_encode(array(
                'status' => 200,
                'message' => 'Thank you! Your bulk order enquiry has been submitted. We will contact you within 24 hours.'
            ));
        } else {
            echo json_encode(array(
                'status' => 500,
                'message' => 'Failed to submit enquiry. Please try again later.'
            ));
        }
    }

    /**
     * Submit contact form (AJAX)
     */
    public function submit_contact()
    {
        $post_data = $this->input->post(null, true);

        // Validation
        if (empty($post_data['firstName'])) {
            echo json_encode(array('status' => 400, 'message' => 'First name is required.'));
            return;
        }

        if (empty($post_data['lastName'])) {
            echo json_encode(array('status' => 400, 'message' => 'Last name is required.'));
            return;
        }

        if (empty($post_data['email'])) {
            echo json_encode(array('status' => 400, 'message' => 'Email is required.'));
            return;
        }

        if (!filter_var($post_data['email'], FILTER_VALIDATE_EMAIL)) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter a valid email address.'));
            return;
        }

        if (empty($post_data['subject'])) {
            echo json_encode(array('status' => 400, 'message' => 'Please select a subject.'));
            return;
        }

        if (empty($post_data['message'])) {
            echo json_encode(array('status' => 400, 'message' => 'Message is required.'));
            return;
        }

        // Prepare data for mailer
        $contact_data = array(
            'firstName' => trim($post_data['firstName']),
            'lastName' => trim($post_data['lastName']),
            'email' => trim($post_data['email']),
            'phone' => !empty($post_data['phone']) ? trim($post_data['phone']) : '',
            'subject' => $post_data['subject'],
            'orderId' => !empty($post_data['orderId']) ? trim($post_data['orderId']) : '',
            'message' => trim($post_data['message'])
        );

        // Send email
        $this->load->library('Mailer');
        $result = $this->mailer->send_contact_email($contact_data);

        if ($result) {
            echo json_encode(array(
                'status' => 200,
                'message' => 'Thank you for contacting us! We will get back to you soon.'
            ));
        } else {
            echo json_encode(array(
                'status' => 500,
                'message' => 'Failed to send message. Please try again later.'
            ));
        }
    }
}