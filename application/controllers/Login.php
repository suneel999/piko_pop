<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    // ==================== LOGIN PAGE ====================

    public function index()
    {
        if (is_loggedin_user()) {
            redirect('');
        }

        $header_data['page_title'] = 'Login';
        $header_data['otp_bypass_enabled'] = otp_bypass_enabled();
        $header_data['otp_bypass_code'] = otp_bypass_code();

        $this->load->view('user/auth/login_header', $header_data);
        $this->load->view('user/auth/login', $header_data);
        $this->load->view('user/auth/login_footer');
    }

    // ==================== SEND OTP ====================

    public function send_otp()
    {
        if (is_loggedin_user()) {
            echo json_encode(array('status' => 400, 'message' => 'Already logged in.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $phone = isset($post_data['phone']) ? trim($post_data['phone']) : '';

        if (empty($phone) || !preg_match('/^[6-9]\d{9}$/', $phone)) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter a valid 10 digit mobile number.'));
            return;
        }

        $otp = otp_bypass_enabled()
            ? otp_bypass_code()
            : str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $user = $this->common->get_user_by_phone($phone);

        if ($user) {
            $this->common->update('users', array(
                'otp' => $otp,
                'otp_expires' => $otp_expires
            ), array('id' => $user->id));
        } else {
            $this->session->set_userdata('temp_phone', $phone);
            $this->session->set_userdata('temp_otp', $otp);
            $this->session->set_userdata('temp_otp_expires', $otp_expires);
        }

        if (otp_bypass_enabled()) {
            echo json_encode(array(
                'status' => 200,
                'message' => 'Test mode: use OTP ' . otp_bypass_code() . '.',
                'is_new_user' => empty($user) ? 1 : 0,
                'test_mode' => 1
            ));
            return;
        }

        $message = 'Your OTP for login is ' . $otp . '. Do not share it with anyone. - PIKO POP';
        $sms_sent = send_sms($phone, $message);

        if ($sms_sent) {
            echo json_encode(array(
                'status' => 200,
                'message' => 'OTP sent successfully.',
                'is_new_user' => empty($user) ? 1 : 0
            ));
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Failed to send OTP. Please try again.'));
        }
    }

    // ==================== VERIFY OTP ====================

    public function verify_otp()
    {
        if (is_loggedin_user()) {
            echo json_encode(array('status' => 400, 'message' => 'Already logged in.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $phone = isset($post_data['phone']) ? trim($post_data['phone']) : '';
        $otp = isset($post_data['otp']) ? trim($post_data['otp']) : '';

        if (empty($phone) || empty($otp)) {
            echo json_encode(array('status' => 400, 'message' => 'Phone and OTP are required.'));
            return;
        }

        if (strlen($otp) != 6) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter a valid 6-digit OTP.'));
            return;
        }

        $user = $this->common->get_user_by_phone($phone);

        if ($user) {
            if (!is_valid_login_otp($otp, $user->otp, $user->otp_expires)) {
                echo json_encode(array('status' => 400, 'message' => 'Invalid OTP. Please try again.'));
                return;
            }

            if ($user->status != 1 || $user->is_active != 1) {
                echo json_encode(array('status' => 400, 'message' => 'Your account is not active. Please contact support.'));
                return;
            }

            $this->common->update('users', array(
                'otp' => null,
                'otp_expires' => null,
                'phone_verified' => 1,
                'last_login' => date('Y-m-d H:i:s'),
                'last_login_ip' => $this->input->ip_address(),
                'login_attempts' => 0,
                'locked_until' => null
            ), array('id' => $user->id));

            $user = $this->common->getdatabytable('users', array('id' => $user->id));
            $this->session->set_userdata('user_details', $user);

            echo json_encode(array(
                'status' => 200,
                'message' => 'Login successful!',
                'is_new_user' => 0,
                'redirect' => base_url()
            ));
        } else {
            if (otp_bypass_enabled() && $otp === otp_bypass_code()) {
                $this->session->set_userdata('temp_phone', $phone);
                $this->session->set_userdata('temp_otp', $otp);
                $this->session->set_userdata('temp_otp_expires', date('Y-m-d H:i:s', strtotime('+1 hour')));
            }

            $temp_phone = $this->session->userdata('temp_phone');
            $temp_otp = $this->session->userdata('temp_otp');
            $temp_otp_expires = $this->session->userdata('temp_otp_expires');

            if (!is_valid_login_otp($otp, $temp_otp, $temp_otp_expires) || $temp_phone !== $phone) {
                echo json_encode(array('status' => 400, 'message' => 'Invalid OTP. Please try again.'));
                return;
            }

            echo json_encode(array(
                'status' => 200,
                'message' => 'OTP verified! Please complete your registration.',
                'is_new_user' => 1
            ));
        }
    }

    // ==================== REGISTER NEW USER ====================

    public function register()
    {
        if (is_loggedin_user()) {
            echo json_encode(array('status' => 400, 'message' => 'Already logged in.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $phone = isset($post_data['phone']) ? trim($post_data['phone']) : '';
        $fullname = isset($post_data['fullname']) ? trim($post_data['fullname']) : '';
        $email = isset($post_data['email']) ? trim($post_data['email']) : '';

        if (empty($phone) || empty($fullname)) {
            echo json_encode(array('status' => 400, 'message' => 'Name and phone number are required.'));
            return;
        }

        if (strlen($fullname) < 2) {
            echo json_encode(array('status' => 400, 'message' => 'Please enter a valid name.'));
            return;
        }

        $temp_phone = $this->session->userdata('temp_phone');
        if ($temp_phone !== $phone) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid session. Please start again.'));
            return;
        }

        $check_phone = $this->common->get_user_by_phone($phone);
        if (!empty($check_phone)) {
            echo json_encode(array('status' => 400, 'message' => 'Phone number already registered.'));
            return;
        }

        if (!empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(array('status' => 400, 'message' => 'Please enter a valid email address.'));
                return;
            }
            $check_email = $this->common->get_user_by_email($email);
            if (!empty($check_email)) {
                echo json_encode(array('status' => 400, 'message' => 'Email address already exists.'));
                return;
            }
        }

        $user_data = array(
            'fullname' => $fullname,
            'email' => $email ?: null,
            'phone' => $phone,
            'password' => '',
            'status' => 1,
            'is_active' => 1,
            'is_verified' => 0,
            'phone_verified' => 1,
            'wallet_balance' => 0.00,
            'last_login' => date('Y-m-d H:i:s'),
            'last_login_ip' => $this->input->ip_address(),
            'created_at' => date('Y-m-d H:i:s')
        );

        $insert = $this->common->insert($user_data, 'users');

        if (!empty($insert)) {
            $this->session->unset_userdata('temp_phone');
            $this->session->unset_userdata('temp_otp');
            $this->session->unset_userdata('temp_otp_expires');

            $user = $this->common->getdatabytable('users', array('id' => $insert));

            if ($user) {
                $this->session->set_userdata('user_details', $user);

                echo json_encode(array(
                    'status' => 200,
                    'message' => 'Account created successfully!',
                    'redirect' => base_url()
                ));
            } else {
                echo json_encode(array('status' => 400, 'message' => 'Something went wrong.'));
            }
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Failed to create account. Please try again.'));
        }
    }

    // ==================== RESEND OTP ====================

    public function resend_otp()
    {
        if (is_loggedin_user()) {
            echo json_encode(array('status' => 400, 'message' => 'Already logged in.'));
            return;
        }

        $post_data = $this->input->post(null, true);
        $phone = isset($post_data['phone']) ? trim($post_data['phone']) : '';

        if (empty($phone) || !preg_match('/^[6-9]\d{9}$/', $phone)) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid phone number.'));
            return;
        }

        $otp = otp_bypass_enabled()
            ? otp_bypass_code()
            : str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $otp_expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $user = $this->common->get_user_by_phone($phone);

        if ($user) {
            $this->common->update('users', array(
                'otp' => $otp,
                'otp_expires' => $otp_expires
            ), array('id' => $user->id));
        } else {
            $this->session->set_userdata('temp_phone', $phone);
            $this->session->set_userdata('temp_otp', $otp);
            $this->session->set_userdata('temp_otp_expires', $otp_expires);
        }

        if (otp_bypass_enabled()) {
            echo json_encode(array(
                'status' => 200,
                'message' => 'Test mode: use OTP ' . otp_bypass_code() . '.',
                'test_mode' => 1
            ));
            return;
        }

        $message = 'Your OTP for login is ' . $otp . '. Do not share it with anyone. - PIKO POP';
        $sms_sent = send_sms($phone, $message);

        if ($sms_sent) {
            echo json_encode(array('status' => 200, 'message' => 'OTP sent successfully.'));
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Failed to send OTP. Please try again.'));
        }
    }

    // ==================== LOGOUT ====================

    public function logout()
    {
        $this->session->unset_userdata('user_details');
        redirect('login');
    }
}
