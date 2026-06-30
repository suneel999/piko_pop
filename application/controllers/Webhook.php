<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Webhook extends CI_Controller
{
    private $logger;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
        $this->load->model('Orders_model', 'orders_model');
        $this->load->library('Monolog');
        $this->logger = $this->monolog;
    }

    /**
     * Shiprocket Webhook Handler
     * Receives order status updates from Shiprocket
     */
    public function shiprocket()
    {
        // Get request headers and body
        $headers = $this->input->request_headers();
        $raw_data = file_get_contents('php://input');
        $data = json_decode($raw_data, true);

        // Log incoming webhook
        $this->logger->info('Shiprocket webhook received', ['body' => $data], 'shiprocket');

        // Verify the X-Api-Key header
        $api_key = isset($headers['X-Api-Key']) ? $headers['X-Api-Key'] : (isset($headers['x-api-key']) ? $headers['x-api-key'] : null);

        if ($api_key != config_item('shiprocket_webhook_auth_key')) {
            $this->logger->error('Shiprocket webhook unauthorized', ['headers' => $headers], 'shiprocket');
            echo json_encode(array('status' => 401, 'message' => 'Unauthorized'));
            return;
        }

        // Extract data from webhook payload
        $order_id = isset($data['order_id']) ? $data['order_id'] : null;
        $sr_order_id = isset($data['sr_order_id']) ? $data['sr_order_id'] : null;
        $current_status = isset($data['current_status']) ? strtolower($data['current_status']) : '';
        $awb = isset($data['awb']) ? $data['awb'] : null;
        $courier_name = isset($data['courier_name']) ? $data['courier_name'] : null;

        if (empty($order_id)) {
            $this->logger->error('Shiprocket webhook: No order_id in payload', ['data' => $data], 'shiprocket');
            echo json_encode(array('status' => 400, 'message' => 'No order_id provided'));
            return;
        }

        // Find order by order_number
        $order = $this->common->getdatabytable('orders', array('order_number' => $order_id));

        if (empty($order)) {
            // Try finding by sr_order_id
            if (!empty($sr_order_id)) {
                $order = $this->common->getdatabytable('orders', array('sr_order_id' => $sr_order_id));
            }

            if (empty($order)) {
                $this->logger->error('Shiprocket webhook: Order not found', ['order_id' => $order_id, 'sr_order_id' => $sr_order_id], 'shiprocket');
                echo json_encode(array('status' => 404, 'message' => 'Order not found'));
                return;
            }
        }

        $this->logger->info('Shiprocket webhook processing', ['order_number' => $order->order_number, 'status' => $current_status], 'shiprocket');

        // Map Shiprocket status to our order status
        $new_status = null;
        $status_notes = '';

        switch ($current_status) {
            case 'canceled':
            case 'cancelled':
                $new_status = 'cancelled';
                $status_notes = 'Order cancelled via Shiprocket';
                break;

            case 'pickup scheduled':
            case 'pickup queued':
                // Keep as processing
                $status_notes = 'Pickup scheduled with courier';
                break;

            case 'picked up':
            case 'shipped':
            case 'in transit':
                $new_status = 'shipped';
                $status_notes = 'Package picked up and in transit';
                break;

            case 'out for delivery':
            case 'ofd':
                $new_status = 'out_for_delivery';
                $status_notes = 'Package is out for delivery';
                break;

            case 'delivered':
                $new_status = 'delivered';
                $status_notes = 'Package delivered successfully';
                break;

            case 'undelivered':
            case 'ndr':
                $status_notes = 'Delivery attempted but undelivered';
                break;

            case 'rto initiated':
            case 'rto in transit':
                $status_notes = 'Return to origin initiated';
                break;

            case 'rto delivered':
                $new_status = 'cancelled';
                $status_notes = 'Package returned to origin';
                break;

            default:
                $status_notes = 'Shiprocket status update: ' . $current_status;
                break;
        }

        // Prepare update data
        $update_data = array(
            'updated_at' => date('Y-m-d H:i:s')
        );

        // Save AWB and courier name if available
        if (!empty($awb) && empty($order->sr_awb)) {
            $update_data['sr_awb'] = $awb;
        }
        if (!empty($courier_name) && empty($order->sr_courier_name)) {
            $update_data['sr_courier_name'] = $courier_name;
        }

        // Update order status if we have a new status
        if ($new_status && $new_status != $order->order_status) {
            $update_data['order_status'] = $new_status;

            // Set delivered_at if delivered
            if ($new_status == 'delivered') {
                $update_data['delivered_at'] = date('Y-m-d H:i:s');
            }
        }

        // Update order
        $this->common->update('orders', $update_data, array('id' => $order->id));

        // Add to status history
        $history_data = array(
            'order_id' => $order->id,
            'status' => $new_status ? $new_status : $order->order_status,
            'notes' => $status_notes . (!empty($awb) ? ' (AWB: ' . $awb . ')' : ''),
            'updated_by' => null, // System update
            'created_at' => date('Y-m-d H:i:s')
        );
        $this->orders_model->add_status_history($history_data);

        $this->logger->info('Shiprocket webhook processed successfully', ['order_number' => $order->order_number, 'new_status' => $new_status], 'shiprocket');

        echo json_encode(array(
            'status' => 200,
            'message' => 'Webhook processed successfully'
        ));
    }

    /**
     * Razorpay Webhook Handler
     * Receives payment status updates from Razorpay
     */
    public function razorpay()
    {
        $raw_data = file_get_contents('php://input');
        $data = json_decode($raw_data, true);

        $this->logger->info('Razorpay webhook received', ['body' => $data], 'razorpay');

        $headers = $this->input->request_headers();
        $webhook_signature = isset($headers['X-Razorpay-Signature']) ? $headers['X-Razorpay-Signature'] : '';
        if ($webhook_signature === '' && isset($headers['x-razorpay-signature'])) {
            $webhook_signature = $headers['x-razorpay-signature'];
        }

        $webhook_secret = razorpay_webhook_secret();
        if ($webhook_secret !== '') {
            try {
                $api = razorpay_api();
                $api->utility->verifyWebhookSignature($raw_data, $webhook_signature, $webhook_secret);
            } catch (\Exception $e) {
                $this->logger->error('Razorpay webhook signature invalid', ['error' => $e->getMessage()], 'razorpay');
                http_response_code(400);
                echo json_encode(array('status' => 400, 'message' => 'Invalid webhook signature'));
                return;
            }
        }

        // Process based on event type
        $event = isset($data['event']) ? $data['event'] : '';
        $payload = isset($data['payload']) ? $data['payload'] : array();

        switch ($event) {
            case 'payment.captured':
                // Payment was successful
                if (isset($payload['payment']['entity'])) {
                    $payment = $payload['payment']['entity'];
                    $payment_id = $payment['id'];
                    $order_id = isset($payment['notes']['order_id']) ? $payment['notes']['order_id'] : null;

                    if ($order_id) {
                        $order = $this->common->getdatabytable('orders', array('id' => $order_id));
                        if ($order && $order->payment_status != 'paid') {
                            $this->common->update('orders', array(
                                'payment_status' => 'paid',
                                'payment_id' => $payment_id,
                                'updated_at' => date('Y-m-d H:i:s')
                            ), array('id' => $order_id));

                            $this->logger->info('Razorpay payment captured', ['order_number' => $order->order_number, 'payment_id' => $payment_id], 'razorpay');
                        }
                    }
                }
                break;

            case 'payment.failed':
                // Payment failed
                if (isset($payload['payment']['entity'])) {
                    $payment = $payload['payment']['entity'];
                    $order_id = isset($payment['notes']['order_id']) ? $payment['notes']['order_id'] : null;

                    if ($order_id) {
                        $order = $this->common->getdatabytable('orders', array('id' => $order_id));
                        if ($order) {
                            $this->common->update('orders', array(
                                'payment_status' => 'failed',
                                'updated_at' => date('Y-m-d H:i:s')
                            ), array('id' => $order_id));

                            $this->logger->warning('Razorpay payment failed', ['order_number' => $order->order_number], 'razorpay');
                        }
                    }
                }
                break;

            case 'refund.created':
            case 'refund.processed':
                // Refund processed
                if (isset($payload['refund']['entity'])) {
                    $refund = $payload['refund']['entity'];
                    $payment_id = $refund['payment_id'];

                    // Find order by payment_id
                    $order = $this->common->getdatabytable('orders', array('payment_id' => $payment_id));
                    if ($order) {
                        $this->common->update('orders', array(
                            'payment_status' => 'refunded',
                            'updated_at' => date('Y-m-d H:i:s')
                        ), array('id' => $order->id));

                        $this->logger->info('Razorpay refund processed', ['order_number' => $order->order_number, 'refund_id' => $refund['id']], 'razorpay');
                    }
                }
                break;

            default:
                $this->logger->info('Razorpay webhook event not handled', ['event' => $event], 'razorpay');
                break;
        }

        echo json_encode(array('status' => 200, 'message' => 'Webhook received'));
    }
}
