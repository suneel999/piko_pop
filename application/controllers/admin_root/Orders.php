<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Razorpay\Api\Api;

class Orders extends CI_Controller
{
    private $logger;

    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
        $this->load->model('Orders_model', 'orders_model');
        $this->load->library('Monolog');
        $this->logger = $this->monolog;
    }

    // Order status configuration
    private function get_order_statuses()
    {
        return array(
            'pending' => array('label' => 'Pending', 'badge' => 'warning', 'next' => 'confirmed'),
            'confirmed' => array('label' => 'Confirmed', 'badge' => 'info', 'next' => 'processing'),
            'processing' => array('label' => 'Processing', 'badge' => 'primary', 'next' => 'shipped'),
            'shipped' => array('label' => 'Shipped', 'badge' => 'secondary', 'next' => 'out_for_delivery'),
            'out_for_delivery' => array('label' => 'Out for Delivery', 'badge' => 'dark', 'next' => 'delivered'),
            'delivered' => array('label' => 'Delivered', 'badge' => 'success', 'next' => null),
            'cancelled' => array('label' => 'Cancelled', 'badge' => 'danger', 'next' => null),
            'refunded' => array('label' => 'Refunded', 'badge' => 'danger', 'next' => null)
        );
    }

    // Active statuses (not completed/cancelled)
    private function get_active_statuses()
    {
        return array('pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery');
    }

    // ==================== PENDING ORDERS (ACTIVE ORDERS) ====================

    public function pending()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Pending Orders || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $data['order_statuses'] = $this->get_order_statuses();
            $data['active_statuses'] = $this->get_active_statuses();

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/orders/pending', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/orders');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Get pending orders data for DataTable
    public function pending_data()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);
            $status_filter = !empty($post_data['status']) ? $post_data['status'] : null;

            // Get active orders
            $orders = $this->orders_model->get_active_orders($status_filter);
            $statuses = $this->get_order_statuses();

            $data = array();
            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $status_config = isset($statuses[$order->order_status]) ? $statuses[$order->order_status] : $statuses['pending'];
                    $status_badge = '<span class="badge bg-soft-' . $status_config['badge'] . ' text-' . $status_config['badge'] . '">' . $status_config['label'] . '</span>';

                    // Payment status badge
                    $payment_badge = $order->payment_status == 'paid'
                        ? '<span class="badge bg-soft-success text-success">Paid</span>'
                        : '<span class="badge bg-soft-warning text-warning">Pending</span>';

                    // View button
                    $view_btn = '<a href="' . base_url('admin_root/orders/view/' . $order->id) . '" class="btn btn-sm btn-primary"><i class="uil uil-eye"></i> View</a>';

                    $data[] = array(
                        $order->order_number,
                        date('d M Y, h:i A', strtotime($order->created_at)),
                        $order->delivery_fullname,
                        $order->delivery_phone,
                        '₹' . number_format($order->total_amount, 2),
                        $payment_badge,
                        $status_badge,
                        $view_btn
                    );
                }
            }

            $response = array(
                'status' => 200,
                'data' => $data
            );
        } else {
            $response = array(
                'status' => 400,
                'data' => array()
            );
        }
        echo json_encode($response);
    }

    // ==================== ORDER VIEW ====================

    public function view($order_id)
    {
        if (is_loggedin_admin()) {
            $order = $this->orders_model->get_order_with_details($order_id);

            if (!$order) {
                redirect('admin_root/orders/pending');
            }

            $header_data['title'] = 'Order #' . $order->order_number . ' || ' . config_item('application_name');
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $data['order'] = $order;
            $data['order_items'] = $this->orders_model->get_order_items($order_id);
            $data['status_history'] = $this->orders_model->get_status_history($order_id);
            $data['order_statuses'] = $this->get_order_statuses();
            $data['active_statuses'] = $this->get_active_statuses();

            // Determine if order can be cancelled/refunded
            $data['can_cancel'] = in_array($order->order_status, array('pending', 'confirmed', 'processing'));
            $data['can_refund'] = ($order->payment_status == 'paid' && in_array($order->order_status, array('pending', 'confirmed', 'cancelled')));

            // Get next status
            $current_status = $order->order_status;
            $status_config = isset($this->get_order_statuses()[$current_status]) ? $this->get_order_statuses()[$current_status] : null;
            $data['next_status'] = $status_config ? $status_config['next'] : null;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/orders/view', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/order_view');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // ==================== ORDER STATUS ACTIONS ====================

    // AJAX: Update order status to next status
    public function update_status()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (empty($post_data['order_id']) || empty($post_data['new_status'])) {
                echo json_encode(array('status' => 400, 'message' => 'Order ID and new status are required.'));
                return;
            }

            $order_id = intval($post_data['order_id']);
            $new_status = $post_data['new_status'];
            $notes = !empty($post_data['notes']) ? $post_data['notes'] : null;

            // Validate order exists
            $order = $this->orders_model->get_order($order_id);
            if (!$order) {
                echo json_encode(array('status' => 400, 'message' => 'Order not found.'));
                return;
            }

            // Validate status transition
            $statuses = $this->get_order_statuses();
            $current_status = $order->order_status;

            if (!isset($statuses[$new_status])) {
                echo json_encode(array('status' => 400, 'message' => 'Invalid status.'));
                return;
            }

            // Update order status
            $update_data = array(
                'order_status' => $new_status,
                'updated_at' => date('Y-m-d H:i:s')
            );

            // If delivered, set delivery date
            if ($new_status == 'delivered') {
                $update_data['delivered_at'] = date('Y-m-d H:i:s');
            }

            $this->orders_model->update_order($order_id, $update_data);

            // Add to status history
            $this->orders_model->add_status_history(array(
                'order_id' => $order_id,
                'status' => $new_status,
                'notes' => $notes ? $notes : 'Status updated to ' . $statuses[$new_status]['label'],
                'updated_by' => $this->session->userdata('admin_id'),
                'created_at' => date('Y-m-d H:i:s')
            ));

            // If status is processing, create Shiprocket order
            if ($new_status == 'processing') {
                $shiprocket_result = $this->createShiprocketOrder($order);
                if ($shiprocket_result['status'] != 200) {
                    // Log error but don't fail the status update
                    $this->logger->error('Shiprocket order creation failed', [
                        'order_number' => $order->order_number,
                        'error' => $shiprocket_result['message']
                    ], 'shiprocket');
                }
            }

            echo json_encode(array(
                'status' => 200,
                'message' => 'Order status updated to ' . $statuses[$new_status]['label'] . '.',
                'new_status' => $new_status,
                'status_label' => $statuses[$new_status]['label'],
                'status_badge' => $statuses[$new_status]['badge']
            ));
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Please login again.'));
        }
    }

    // AJAX: Cancel order
    public function cancel_order()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (empty($post_data['order_id'])) {
                echo json_encode(array('status' => 400, 'message' => 'Order ID is required.'));
                return;
            }

            $order_id = intval($post_data['order_id']);
            $reason = !empty($post_data['reason']) ? $post_data['reason'] : 'Cancelled by admin';

            // Get order
            $order = $this->orders_model->get_order($order_id);
            if (!$order) {
                echo json_encode(array('status' => 400, 'message' => 'Order not found.'));
                return;
            }

            // Check if order can be cancelled
            if (!in_array($order->order_status, array('pending', 'confirmed', 'processing'))) {
                echo json_encode(array('status' => 400, 'message' => 'This order cannot be cancelled. Only pending, confirmed or processing orders can be cancelled.'));
                return;
            }

            // If order has Shiprocket shipment, cancel it first
            if (!empty($order->sr_shipment_id)) {
                $shiprocket_cancel = $this->cancelShiprocketOrder($order);
                if ($shiprocket_cancel['status'] != 200) {
                    $this->logger->error('Failed to cancel Shiprocket order', [
                        'order_number' => $order->order_number,
                        'error' => $shiprocket_cancel['message']
                    ], 'shiprocket');
                }
            }

            // Update order
            $update_data = array(
                'order_status' => 'cancelled',
                'cancellation_reason' => $reason,
                'updated_at' => date('Y-m-d H:i:s')
            );

            $this->orders_model->update_order($order_id, $update_data);

            // Add to status history
            $this->orders_model->add_status_history(array(
                'order_id' => $order_id,
                'status' => 'cancelled',
                'notes' => 'Order cancelled. Reason: ' . $reason,
                'updated_by' => $this->session->userdata('admin_id'),
                'created_at' => date('Y-m-d H:i:s')
            ));

            // Restore stock
            $this->restore_stock($order_id);

            echo json_encode(array(
                'status' => 200,
                'message' => 'Order has been cancelled successfully.'
            ));
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Please login again.'));
        }
    }

    // AJAX: Issue refund via Razorpay
    public function issue_refund()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (empty($post_data['order_id'])) {
                echo json_encode(array('status' => 400, 'message' => 'Order ID is required.'));
                return;
            }

            $order_id = intval($post_data['order_id']);
            $reason = !empty($post_data['reason']) ? $post_data['reason'] : 'Refund issued by admin';

            // Get order
            $order = $this->orders_model->get_order($order_id);
            if (!$order) {
                echo json_encode(array('status' => 400, 'message' => 'Order not found.'));
                return;
            }

            // Check if order can be refunded
            if ($order->payment_status != 'paid') {
                echo json_encode(array('status' => 400, 'message' => 'Cannot refund - payment was not completed.'));
                return;
            }

            if (!in_array($order->order_status, array('pending', 'confirmed', 'cancelled'))) {
                echo json_encode(array('status' => 400, 'message' => 'Cannot refund - order is already in processing or later stage.'));
                return;
            }

            if (empty($order->payment_id)) {
                echo json_encode(array('status' => 400, 'message' => 'Cannot refund - no payment ID found.'));
                return;
            }

            // Process Razorpay refund
            try {
                $api = razorpay_api();

                // Create refund - amount in paise (multiply by 100)
                $refund = $api->payment->fetch($order->payment_id)->refund(array(
                    'amount' => intval($order->total_amount * 100),
                    'speed' => 'normal',
                    'notes' => array(
                        'reason' => $reason,
                        'order_id' => $order_id,
                        'order_number' => $order->order_number
                    )
                ));

                if ($refund->id) {
                    // Update order - add admin_notes for refund info
                    $refund_note = 'Refund ID: ' . $refund->id . ' | Amount: ₹' . number_format($order->total_amount, 2) . ' | Reason: ' . $reason;
                    $update_data = array(
                        'order_status' => 'cancelled',
                        'payment_status' => 'refunded',
                        'admin_notes' => $refund_note,
                        'updated_at' => date('Y-m-d H:i:s')
                    );

                    $this->orders_model->update_order($order_id, $update_data);

                    // Add to status history
                    $this->orders_model->add_status_history(array(
                        'order_id' => $order_id,
                        'status' => 'refunded',
                        'notes' => 'Refund issued via Razorpay. Amount: ₹' . number_format($order->total_amount, 2) . '. Reason: ' . $reason . '. Refund ID: ' . $refund->id,
                        'updated_by' => $this->session->userdata('admin_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ));

                    // Restore stock if not already done
                    $this->restore_stock($order_id);

                    echo json_encode(array(
                        'status' => 200,
                        'message' => 'Refund of ₹' . number_format($order->total_amount, 2) . ' has been processed successfully.',
                        'refund_id' => $refund->id
                    ));
                } else {
                    echo json_encode(array(
                        'status' => 400,
                        'message' => 'Refund processing failed. Please try again.'
                    ));
                }
            } catch (\Exception $e) {
                $this->logger->error('Razorpay refund failed', [
                    'order_id' => $order_id,
                    'error' => $e->getMessage()
                ], 'razorpay');
                echo json_encode(array(
                    'status' => 500,
                    'message' => 'Razorpay error: ' . $e->getMessage()
                ));
            }
        } else {
            echo json_encode(array('status' => 400, 'message' => 'Please login again.'));
        }
    }

    // ==================== INDEX (ORDER HISTORY/REPORT) ====================

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Order Report || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['datatable_buttons'] = true;
            $footer_data['datatable_buttons'] = true;
            $header_data['flatpickr'] = true;
            $footer_data['flatpickr'] = true;

            $data['order_statuses'] = $this->get_order_statuses();

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/orders/index', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/orders_report');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Get all orders data for DataTable (Order Report)
    public function report_data()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            // Build filters
            $filters = array();

            if (!empty($post_data['status'])) {
                $filters['status'] = $post_data['status'];
            }

            if (!empty($post_data['payment_status'])) {
                $filters['payment_status'] = $post_data['payment_status'];
            }

            if (!empty($post_data['date_from'])) {
                $filters['date_from'] = $post_data['date_from'];
            }

            if (!empty($post_data['date_to'])) {
                $filters['date_to'] = $post_data['date_to'];
            }

            // Get orders
            $orders = $this->orders_model->get_all_orders($filters);
            $statuses = $this->get_order_statuses();

            $data = array();
            $total_orders = 0;
            $total_amount = 0;
            $total_paid = 0;

            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $total_orders++;

                    // Order status badge
                    $status_config = isset($statuses[$order->order_status]) ? $statuses[$order->order_status] : $statuses['pending'];
                    $status_badge = '<span class="badge bg-soft-' . $status_config['badge'] . ' text-' . $status_config['badge'] . '">' . $status_config['label'] . '</span>';

                    // Payment status badge
                    $payment_class = $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'refunded' ? 'danger' : ($order->payment_status == 'failed' ? 'danger' : 'warning'));
                    $payment_badge = '<span class="badge bg-soft-' . $payment_class . ' text-' . $payment_class . '">' . ucwords($order->payment_status) . '</span>';

                    // Calculate totals
                    $total_amount += $order->total_amount;
                    if ($order->payment_status == 'paid' && !in_array($order->order_status, array('cancelled', 'refunded'))) {
                        $total_paid += $order->total_amount;
                    }

                    // View button
                    $view_btn = '<a href="' . base_url('admin_root/orders/view/' . $order->id) . '" class="btn btn-sm btn-primary"><i class="uil uil-eye"></i> View</a>';

                    $data[] = array(
                        $order->order_number,
                        date('d M Y', strtotime($order->created_at)),
                        date('h:i A', strtotime($order->created_at)),
                        htmlspecialchars($order->delivery_fullname),
                        $order->delivery_phone,
                        '₹' . number_format($order->total_amount, 2),
                        $payment_badge,
                        $status_badge,
                        $view_btn
                    );
                }
            }

            $response = array(
                'status' => 200,
                'data' => $data,
                'totals' => array(
                    'total_orders' => $total_orders,
                    'total_amount' => number_format($total_amount, 2),
                    'total_paid' => number_format($total_paid, 2)
                )
            );
        } else {
            $response = array(
                'status' => 400,
                'data' => array(),
                'totals' => array(
                    'total_orders' => 0,
                    'total_amount' => '0.00',
                    'total_paid' => '0.00'
                )
            );
        }
        echo json_encode($response);
    }

    // ==================== HELPER METHODS ====================

    // Restore stock after order cancellation/refund
    private function restore_stock($order_id)
    {
        $order_items = $this->orders_model->get_order_items($order_id);

        foreach ($order_items as $item) {
            if ($item->stock_deducted) {
                $product = $this->common->getdatabytable('products', array('id' => $item->product_id));
                if ($product) {
                    $restore_qty = $item->quantity;

                    // If variant, calculate actual stock to restore
                    if ($item->variant_id) {
                        $variant = $this->common->getdatabytable('product_variants', array('id' => $item->variant_id));
                        if ($variant && $variant->stock_qty > 0) {
                            $restore_qty = $item->quantity * $variant->stock_qty;
                        }
                    }

                    $new_stock = $product->stock + $restore_qty;

                    $this->common->update('products', array('stock' => $new_stock), array('id' => $item->product_id));
                    $this->orders_model->update_order_item($item->id, array('stock_deducted' => 0));

                    // Log stock restoration
                    $log_data = array(
                        'product_id' => $item->product_id,
                        'previous_stock' => $product->stock,
                        'added_qty' => $restore_qty,
                        'new_stock' => $new_stock,
                        'notes' => 'Order #' . $order_id . ' - Stock restored (cancelled/refunded)',
                        'added_at' => date('Y-m-d H:i:s')
                    );
                    $this->common->insert($log_data, 'stock_logs');
                }
            }
        }
    }

    // ==================== SHIPROCKET INTEGRATION ====================

    /**
     * Create Shiprocket order when status changes to processing
     */
    private function createShiprocketOrder($order)
    {
        try {
            $this->load->library('Shiprocket');

            // Get order items
            $order_items = $this->orders_model->get_order_items($order->id);

            // Build order items array for Shiprocket
            $shiprocket_items = array();
            $total_weight = 0;

            foreach ($order_items as $item) {
                $shiprocket_items[] = array(
                    'name' => $item->product_name,
                    'sku' => 'SKU-' . $item->product_id . ($item->variant_id ? '-' . $item->variant_id : ''),
                    'units' => intval($item->quantity),
                    'selling_price' => floatval($item->unit_price),
                    'discount' => 0,
                    'tax' => 0,
                    'hsn' => ''
                );
                // Estimate weight (0.25 kg per item if not specified)
                $total_weight += $item->total_package_weight;
            }

            $total_weight += 0.025; // Add base weight for packaging

            // Build billing/shipping address
            $billing_address = $order->delivery_address;
            if (!empty($order->delivery_landmark)) {
                $billing_address .= ', ' . $order->delivery_landmark;
            }

            // Determine payment method for Shiprocket
            $payment_method = ($order->payment_method == 'cod') ? 'COD' : 'Prepaid';

            // Build order data for Shiprocket
            $orderData = array(
                'order_id' => $order->order_number,
                'order_date' => date('Y-m-d H:i', strtotime($order->created_at)),
                'pickup_location' => config_item('shiprocket_pickup_location'),
                'channel_id' => '',
                'comment' => !empty($order->customer_notes) ? $order->customer_notes : '',
                'billing_customer_name' => $order->delivery_fullname,
                'billing_last_name' => '',
                'billing_address' => $billing_address,
                'billing_address_2' => '',
                'billing_city' => $order->delivery_city,
                'billing_pincode' => $order->delivery_pincode,
                'billing_state' => $order->delivery_state,
                'billing_country' => 'India',
                'billing_email' => '', // Will be fetched from user if needed
                'billing_phone' => $order->delivery_phone,
                'shipping_is_billing' => true,
                'order_items' => $shiprocket_items,
                'payment_method' => $payment_method,
                'shipping_charges' => floatval($order->delivery_charge),
                'giftwrap_charges' => 0,
                'transaction_charges' => 0,
                'total_discount' => floatval($order->discount_amount),
                'sub_total' => floatval($order->subtotal),
                'length' => 8,
                'breadth' => 6,
                'height' => 10,
                'weight' => $total_weight
            );

            // Get user email if available
            $user = $this->common->getdatabytable('users', array('id' => $order->user_id));
            if ($user && !empty($user->email)) {
                $orderData['billing_email'] = $user->email;
            }

            // Create order in Shiprocket
            $shiprocket_response = $this->shiprocket->createOrder($orderData);

            if (isset($shiprocket_response['order_id']) && isset($shiprocket_response['shipment_id'])) {
                // Update order with Shiprocket IDs
                $this->orders_model->update_order($order->id, array(
                    'sr_order_id' => $shiprocket_response['order_id'],
                    'sr_shipment_id' => $shiprocket_response['shipment_id']
                ));

                // Add to status history
                $this->orders_model->add_status_history(array(
                    'order_id' => $order->id,
                    'status' => 'processing',
                    'notes' => 'Shiprocket order created. Order ID: ' . $shiprocket_response['order_id'] . ', Shipment ID: ' . $shiprocket_response['shipment_id'],
                    'updated_by' => $this->session->userdata('admin_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ));

                return array(
                    'status' => 200,
                    'message' => 'Shiprocket order created successfully',
                    'sr_order_id' => $shiprocket_response['order_id'],
                    'sr_shipment_id' => $shiprocket_response['shipment_id']
                );
            } else {
                return array(
                    'status' => 400,
                    'message' => 'Shiprocket order creation failed: ' . json_encode($shiprocket_response)
                );
            }
        } catch (Exception $e) {
            $this->logger->error('Shiprocket order creation exception', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ], 'shiprocket');
            return array(
                'status' => 500,
                'message' => $e->getMessage()
            );
        }
    }

    /**
     * Cancel Shiprocket order
     */
    private function cancelShiprocketOrder($order)
    {
        if (empty($order->sr_shipment_id)) {
            return array('status' => 400, 'message' => 'No Shiprocket shipment ID found');
        }

        try {
            $this->load->library('Shiprocket');
            $response = $this->shiprocket->cancelOrder($order->sr_shipment_id);

            return array(
                'status' => 200,
                'message' => 'Shiprocket order cancelled',
                'response' => $response
            );
        } catch (Exception $e) {
            $this->logger->error('Shiprocket cancel order exception', [
                'order_id' => $order->id,
                'sr_shipment_id' => $order->sr_shipment_id,
                'error' => $e->getMessage()
            ], 'shiprocket');
            return array(
                'status' => 500,
                'message' => $e->getMessage()
            );
        }
    }
}
