<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Mailer Library
 *
 * Handles email sending operations for Bhaarat Groceries application.
 */
class Mailer
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('email');
    }

    /**
     * Configure email settings
     */
    private function configure()
    {
        $config = array(
            'protocol'  => 'smtp',
            'smtp_host' => 'ssl://' . config_item('email_host'),
            'smtp_port' => config_item('email_port'),
            'smtp_user' => config_item('email_username'),
            'smtp_pass' => config_item('email_password'),
            'mailtype'  => 'html',
            'charset'   => 'utf-8',
            'newline'   => "\r\n",
            'wordwrap'  => TRUE
        );

        $this->CI->email->initialize($config);
    }

    /**
     * Send password reset link email
     *
     * @param string $to_email Recipient email address
     * @param string $reset_token The reset token
     * @param string $user_name User's name
     * @return bool True on success, false on failure
     */
    public function send_password_reset_link($to_email, $reset_token, $user_name = '')
    {
        $this->configure();

        $this->CI->email->from(config_item('email_from'), config_item('application_name'));
        $this->CI->email->to($to_email);
        $this->CI->email->subject('Password Reset Request - ' . config_item('application_name'));

        $reset_link = base_url('login/reset_password/' . $reset_token);
        $message = $this->get_reset_link_template($reset_link, $user_name);
        $this->CI->email->message($message);

        if ($this->CI->email->send()) {
            return true;
        } else {
            log_message('error', 'Email sending failed: ' . $this->CI->email->print_debugger(['headers']));
            return false;
        }
    }

    /**
     * Get password reset link email template
     *
     * @param string $reset_link The password reset link
     * @param string $user_name User's name
     * @return string HTML email template
     */
    private function get_reset_link_template($reset_link, $user_name)
    {
        $app_name = config_item('application_name');
        $greeting = !empty($user_name) ? "Hello {$user_name}," : "Hello,";

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Password Reset</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table role='presentation' style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td align='center' style='padding: 40px 0;'>
                        <table role='presentation' style='width: 600px; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='padding: 30px 40px; background-color: #417394; border-radius: 8px 8px 0 0;'>
                                    <h1 style='margin: 0; color: #ffffff; font-size: 24px;'>{$app_name}</h1>
                                </td>
                            </tr>

                            <!-- Content -->
                            <tr>
                                <td style='padding: 40px;'>
                                    <p style='margin: 0 0 20px; color: #333333; font-size: 16px;'>{$greeting}</p>

                                    <p style='margin: 0 0 20px; color: #333333; font-size: 16px;'>
                                        We received a request to reset your password. Click the button below to create a new password:
                                    </p>

                                    <!-- Reset Button -->
                                    <div style='text-align: center; margin: 30px 0;'>
                                        <a href='{$reset_link}' style='display: inline-block; padding: 15px 40px; background-color: #417394; color: #ffffff; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;'>Reset Password</a>
                                    </div>

                                    <p style='margin: 0 0 10px; color: #666666; font-size: 14px;'>
                                        <strong>This link will expire in 1 hour.</strong>
                                    </p>

                                    <p style='margin: 0 0 20px; color: #666666; font-size: 14px;'>
                                        If you did not request a password reset, please ignore this email or contact support if you have concerns.
                                    </p>

                                    <p style='margin: 0 0 10px; color: #666666; font-size: 12px;'>
                                        If the button doesn't work, copy and paste this link into your browser:
                                    </p>
                                    <p style='margin: 0 0 20px; color: #417394; font-size: 12px; word-break: break-all;'>
                                        {$reset_link}
                                    </p>

                                    <hr style='border: none; border-top: 1px solid #eeeeee; margin: 30px 0;'>

                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        This is an automated message. Please do not reply to this email.
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style='padding: 20px 40px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; text-align: center;'>
                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        &copy; " . date('Y') . " {$app_name}. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }

    /**
     * Send password reset success email
     *
     * @param string $to_email Recipient email address
     * @param string $user_name User's name
     * @return bool True on success, false on failure
     */
    public function send_password_reset_success($to_email, $user_name = '')
    {
        $this->configure();

        $this->CI->email->from(config_item('email_from'), config_item('application_name'));
        $this->CI->email->to($to_email);
        $this->CI->email->subject('Password Reset Successful - ' . config_item('application_name'));

        $app_name = config_item('application_name');
        $greeting = !empty($user_name) ? "Hello {$user_name}," : "Hello,";

        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 8px;'>
                <h2 style='color: #417394;'>{$app_name}</h2>
                <p>{$greeting}</p>
                <p>Your password has been successfully reset.</p>
                <p>If you did not make this change, please contact our support team immediately.</p>
                <hr style='border: none; border-top: 1px solid #eeeeee; margin: 20px 0;'>
                <p style='color: #999999; font-size: 12px;'>This is an automated message. Please do not reply.</p>
            </div>
        </body>
        </html>
        ";

        $this->CI->email->message($message);

        return $this->CI->email->send();
    }

    /**
     * Send contact form email
     *
     * @param array $data Contact form data (firstName, lastName, email, phone, subject, orderId, message)
     * @return bool True on success, false on failure
     */
    public function send_contact_email($data)
    {
        $this->configure();

        $app_name = config_item('application_name');
        $admin_email = config_item('admin_email');

        // Subject mapping
        $subject_labels = array(
            'general' => 'General Inquiry',
            'order' => 'Order Related',
            'product' => 'Product Question',
            'partnership' => 'Business Partnership',
            'feedback' => 'Feedback',
            'complaint' => 'Complaint',
            'other' => 'Other'
        );

        $subject_label = isset($subject_labels[$data['subject']]) ? $subject_labels[$data['subject']] : $data['subject'];

        // Send to admin
        $this->CI->email->from($data['email'], $data['firstName'] . ' ' . $data['lastName']);
        $this->CI->email->to($admin_email);
        $this->CI->email->subject('[' . $subject_label . '] Contact Form - ' . $app_name);

        $order_info = '';
        if (!empty($data['orderId'])) {
            $order_info = "<tr>
                <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold; width: 120px;'>Order ID:</td>
                <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$data['orderId']}</td>
            </tr>";
        }

        $phone_info = !empty($data['phone']) ? $data['phone'] : 'Not provided';

        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Contact Form Submission</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table role='presentation' style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td align='center' style='padding: 40px 0;'>
                        <table role='presentation' style='width: 600px; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='padding: 30px 40px; background-color: #ed3237; border-radius: 8px 8px 0 0;'>
                                    <h1 style='margin: 0; color: #ffffff; font-size: 24px;'>New Contact Form Submission</h1>
                                </td>
                            </tr>

                            <!-- Content -->
                            <tr>
                                <td style='padding: 40px;'>
                                    <table style='width: 100%; border-collapse: collapse;'>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold; width: 120px;'>Name:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$data['firstName']} {$data['lastName']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Email:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'><a href='mailto:{$data['email']}' style='color: #ed3237;'>{$data['email']}</a></td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Phone:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$phone_info}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Subject:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$subject_label}</td>
                                        </tr>
                                        {$order_info}
                                    </table>

                                    <div style='margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
                                        <h3 style='margin: 0 0 15px; color: #333333; font-size: 16px;'>Message:</h3>
                                        <p style='margin: 0; color: #666666; line-height: 1.6; white-space: pre-wrap;'>{$data['message']}</p>
                                    </div>

                                    <hr style='border: none; border-top: 1px solid #eeeeee; margin: 30px 0;'>

                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        This message was sent from the contact form on {$app_name}.
                                    </p>
                                </td>
                            </tr>

                            <!-- Footer -->
                            <tr>
                                <td style='padding: 20px 40px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; text-align: center;'>
                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        &copy; " . date('Y') . " {$app_name}. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $this->CI->email->message($message);

        if ($this->CI->email->send()) {
            // Also send confirmation to customer
            $this->send_contact_confirmation($data);
            return true;
        } else {
            log_message('error', 'Contact email sending failed: ' . $this->CI->email->print_debugger(['headers']));
            return false;
        }
    }

    /**
     * Send bulk order enquiry email to admin
     *
     * @param array $data Bulk order form data
     * @return bool True on success, false on failure
     */
    public function send_bulk_order_email($data)
    {
        $this->configure();

        $app_name = config_item('application_name');
        $admin_email = config_item('admin_email');

        $event_labels = array(
            'corporate_gifting' => 'Corporate Gifting',
            'catering'          => 'Catering',
            'cloud_kitchen'     => 'Cloud Kitchen',
            'restaurant'        => 'Restaurant',
            'retail'            => 'Retail / Reseller',
            'other'             => 'Other'
        );

        $event_label = !empty($data['eventType']) && isset($event_labels[$data['eventType']]) ? $event_labels[$data['eventType']] : 'Not specified';
        $company_info = !empty($data['companyName']) ? $data['companyName'] : 'Not provided';
        $quantity_info = !empty($data['quantity']) ? $data['quantity'] : 'Not specified';
        $message_info = !empty($data['message']) ? $data['message'] : 'No additional details provided.';

        $this->CI->email->from($data['email'], $data['fullName']);
        $this->CI->email->to($admin_email);
        $this->CI->email->subject('[Bulk Order Enquiry] ' . $data['fullName'] . ' - ' . $app_name);

        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Bulk Order Enquiry</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table role='presentation' style='width: 100%; border-collapse: collapse;'>
                <tr>
                    <td align='center' style='padding: 40px 0;'>
                        <table role='presentation' style='width: 600px; border-collapse: collapse; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                            <tr>
                                <td style='padding: 30px 40px; background-color: #ed3237; border-radius: 8px 8px 0 0;'>
                                    <h1 style='margin: 0; color: #ffffff; font-size: 24px;'>New Bulk Order Enquiry</h1>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 40px;'>
                                    <table style='width: 100%; border-collapse: collapse;'>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold; width: 150px;'>Name:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$data['fullName']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Email:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'><a href='mailto:{$data['email']}' style='color: #ed3237;'>{$data['email']}</a></td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Phone:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$data['phone']}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Company:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$company_info}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Event Type:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$event_label}</td>
                                        </tr>
                                        <tr>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee; font-weight: bold;'>Est. Quantity:</td>
                                            <td style='padding: 10px; border-bottom: 1px solid #eeeeee;'>{$quantity_info}</td>
                                        </tr>
                                    </table>

                                    <div style='margin-top: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px;'>
                                        <h3 style='margin: 0 0 15px; color: #333333; font-size: 16px;'>Additional Details:</h3>
                                        <p style='margin: 0; color: #666666; line-height: 1.6; white-space: pre-wrap;'>{$message_info}</p>
                                    </div>

                                    <hr style='border: none; border-top: 1px solid #eeeeee; margin: 30px 0;'>

                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        This enquiry was submitted from the Bulk Order page on {$app_name}.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style='padding: 20px 40px; background-color: #f8f9fa; border-radius: 0 0 8px 8px; text-align: center;'>
                                    <p style='margin: 0; color: #999999; font-size: 12px;'>
                                        &copy; " . date('Y') . " {$app_name}. All rights reserved.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";

        $this->CI->email->message($message);

        if ($this->CI->email->send()) {
            return true;
        } else {
            log_message('error', 'Bulk order email sending failed: ' . $this->CI->email->print_debugger(['headers']));
            return false;
        }
    }

    /**
     * Send contact form confirmation to customer
     *
     * @param array $data Contact form data
     * @return bool True on success, false on failure
     */
    private function send_contact_confirmation($data)
    {
        $this->configure();

        $app_name = config_item('application_name');

        $this->CI->email->from(config_item('email_from'), $app_name);
        $this->CI->email->to($data['email']);
        $this->CI->email->subject('We received your message - ' . $app_name);

        $message = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
        </head>
        <body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 8px;'>
                <h2 style='color: #ed3237;'>{$app_name}</h2>
                <p>Hello {$data['firstName']},</p>
                <p>Thank you for contacting us! We have received your message and will get back to you within 24-48 hours.</p>
                <div style='background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 0 0 10px;'><strong>Your message:</strong></p>
                    <p style='margin: 0; color: #666666; font-style: italic;'>\"{$data['message']}\"</p>
                </div>
                <p>If you have any urgent queries, feel free to call us at <a href='tel:+919542200222' style='color: #ed3237;'>+91 95422 00222</a>.</p>
                <hr style='border: none; border-top: 1px solid #eeeeee; margin: 20px 0;'>
                <p style='color: #999999; font-size: 12px;'>This is an automated confirmation. Please do not reply to this email.</p>
            </div>
        </body>
        </html>
        ";

        $this->CI->email->message($message);

        return $this->CI->email->send();
    }
}
