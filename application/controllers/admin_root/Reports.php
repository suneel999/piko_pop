<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Common_model', 'common');
    }

    // ==================== USERS REPORT ====================

    public function users()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Users Report || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['datatable_buttons'] = true;
            $footer_data['datatable_buttons'] = true;
            $header_data['flatpickr'] = true;
            $footer_data['flatpickr'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/reports/users');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/users_report');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Get users data for DataTable
    public function users_data()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            // Build query
            $this->db->select('users.*,
                (SELECT COUNT(*) FROM orders WHERE orders.user_id = users.id) as total_orders,
                (SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE orders.user_id = users.id AND orders.payment_status = "paid" AND orders.order_status NOT IN ("cancelled", "refunded")) as total_spent');
            $this->db->from('users');
            $this->db->where('users.status !=', 3); // Exclude deleted users

            // Status filter
            if (!empty($post_data['status'])) {
                if ($post_data['status'] == 'active') {
                    $this->db->where('users.status', 1);
                    $this->db->where('users.is_active', 1);
                } elseif ($post_data['status'] == 'inactive') {
                    $this->db->where('users.is_active', 0);
                } elseif ($post_data['status'] == 'verified') {
                    $this->db->where('users.is_verified', 1);
                } elseif ($post_data['status'] == 'unverified') {
                    $this->db->where('users.is_verified', 0);
                }
            }

            // Date filters
            if (!empty($post_data['date_from'])) {
                $this->db->where('DATE(users.created_at) >=', $post_data['date_from']);
            }

            if (!empty($post_data['date_to'])) {
                $this->db->where('DATE(users.created_at) <=', $post_data['date_to']);
            }

            $this->db->order_by('users.created_at', 'DESC');
            $users = $this->db->get()->result();

            $data = array();
            $total_users = 0;
            $total_active = 0;
            $total_verified = 0;

            if (!empty($users)) {
                foreach ($users as $user) {
                    $total_users++;

                    if ($user->is_active == 1 && $user->status == 1) {
                        $total_active++;
                    }

                    if ($user->is_verified == 1) {
                        $total_verified++;
                    }

                    // Status badge
                    if ($user->status == 1 && $user->is_active == 1) {
                        $status_badge = '<span class="badge bg-soft-success text-success">Active</span>';
                    } elseif ($user->status == 4) {
                        $status_badge = '<span class="badge bg-soft-danger text-danger">Banned</span>';
                    } else {
                        $status_badge = '<span class="badge bg-soft-warning text-warning">Inactive</span>';
                    }

                    // Verified badge
                    $verified_badge = $user->is_verified == 1
                        ? '<span class="badge bg-soft-info text-info">Verified</span>'
                        : '<span class="badge bg-soft-secondary text-secondary">Unverified</span>';

                    // Phone display
                    $phone = !empty($user->phone) ? $user->phone : '-';

                    // Last login
                    $last_login = !empty($user->last_login) ? date('d M Y, h:i A', strtotime($user->last_login)) : 'Never';

                    // Total orders and spent
                    $total_orders = intval($user->total_orders);
                    $total_spent = floatval($user->total_spent);

                    $data[] = array(
                        $user->id,
                        htmlspecialchars($user->fullname),
                        htmlspecialchars($user->email),
                        $phone,
                        date('d M Y', strtotime($user->created_at)),
                        $last_login,
                        $total_orders,
                        '₹' . number_format($total_spent, 2),
                        $verified_badge,
                        $status_badge
                    );
                }
            }

            $response = array(
                'status' => 200,
                'data' => $data,
                'totals' => array(
                    'total_users' => $total_users,
                    'total_active' => $total_active,
                    'total_verified' => $total_verified
                )
            );
        } else {
            $response = array(
                'status' => 400,
                'data' => array(),
                'totals' => array(
                    'total_users' => 0,
                    'total_active' => 0,
                    'total_verified' => 0
                )
            );
        }
        echo json_encode($response);
    }

    // ==================== SALES REPORT ====================

    public function sales()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Sales Report || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['datatable_buttons'] = true;
            $footer_data['datatable_buttons'] = true;
            $header_data['flatpickr'] = true;
            $footer_data['flatpickr'] = true;
            $header_data['apex_chart'] = true;
            $footer_data['apex_chart'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/reports/sales');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/sales_report');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Get sales data for DataTable
    public function sales_data()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            // Build query for completed/paid orders
            $this->db->select('orders.*, users.fullname as customer_name, users.email as customer_email,
                (SELECT COUNT(*) FROM order_items WHERE order_items.order_id = orders.id) as item_count');
            $this->db->from('orders');
            $this->db->join('users', 'users.id = orders.user_id', 'left');
            $this->db->where('orders.payment_status', 'paid');
            $this->db->where_not_in('orders.order_status', array('cancelled', 'refunded'));

            // Date filters
            if (!empty($post_data['date_from'])) {
                $this->db->where('DATE(orders.created_at) >=', $post_data['date_from']);
            }

            if (!empty($post_data['date_to'])) {
                $this->db->where('DATE(orders.created_at) <=', $post_data['date_to']);
            }

            // Payment method filter
            if (!empty($post_data['payment_method'])) {
                $this->db->where('orders.payment_method', $post_data['payment_method']);
            }

            $this->db->order_by('orders.created_at', 'DESC');
            $orders = $this->db->get()->result();

            $data = array();
            $total_sales = 0;
            $total_orders = 0;
            $total_subtotal = 0;
            $total_discount = 0;
            $total_shipping = 0;
            $total_items = 0;

            if (!empty($orders)) {
                foreach ($orders as $order) {
                    $total_orders++;
                    $total_sales += floatval($order->total_amount);
                    $total_subtotal += floatval($order->subtotal);
                    $total_discount += floatval($order->discount_amount);
                    $total_shipping += floatval($order->delivery_charge);
                    $total_items += intval($order->item_count);

                    // Order status badge
                    $status_class = '';
                    switch ($order->order_status) {
                        case 'delivered':
                            $status_class = 'success';
                            break;
                        case 'shipped':
                        case 'out_for_delivery':
                            $status_class = 'info';
                            break;
                        case 'processing':
                        case 'confirmed':
                            $status_class = 'warning';
                            break;
                        default:
                            $status_class = 'secondary';
                    }
                    $status_badge = '<span class="badge bg-soft-' . $status_class . ' text-' . $status_class . '">' . ucfirst(str_replace('_', ' ', $order->order_status)) . '</span>';

                    // Payment method badge
                    $payment_badge = '<span class="badge bg-soft-primary text-primary">' . ucfirst(str_replace('_', ' ', $order->payment_method)) . '</span>';

                    // Customer name
                    $customer = !empty($order->customer_name) ? htmlspecialchars($order->customer_name) : 'Guest';

                    $data[] = array(
                        '#' . $order->order_number,
                        date('d M Y, h:i A', strtotime($order->created_at)),
                        $customer,
                        $order->item_count . ' items',
                        '₹' . number_format($order->subtotal, 2),
                        '₹' . number_format($order->discount_amount, 2),
                        '₹' . number_format($order->delivery_charge, 2),
                        '₹' . number_format($order->total_amount, 2),
                        $payment_badge,
                        $status_badge
                    );
                }
            }

            // Calculate average order value
            $avg_order_value = $total_orders > 0 ? $total_sales / $total_orders : 0;

            $response = array(
                'status' => 200,
                'data' => $data,
                'totals' => array(
                    'total_sales' => $total_sales,
                    'total_orders' => $total_orders,
                    'total_subtotal' => $total_subtotal,
                    'total_discount' => $total_discount,
                    'total_shipping' => $total_shipping,
                    'total_items' => $total_items,
                    'avg_order_value' => $avg_order_value
                )
            );
        } else {
            $response = array(
                'status' => 400,
                'data' => array(),
                'totals' => array(
                    'total_sales' => 0,
                    'total_orders' => 0,
                    'total_subtotal' => 0,
                    'total_discount' => 0,
                    'total_shipping' => 0,
                    'total_items' => 0,
                    'avg_order_value' => 0
                )
            );
        }
        echo json_encode($response);
    }

    // ==================== EXPENSES REPORT ====================

    public function expenses()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Expenses Report || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $footer_data['datatable'] = true;
            $header_data['select_2'] = true;
            $footer_data['select_2'] = true;
            $header_data['datatable_buttons'] = true;
            $footer_data['datatable_buttons'] = true;
            $header_data['flatpickr'] = true;
            $footer_data['flatpickr'] = true;
            $header_data['sweet_alert'] = true;
            $footer_data['sweet_alert'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/reports/expenses');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/expenses_report');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Get expenses data for DataTable
    public function expenses_data()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            $this->db->select('expenses.*, admin.full_name as added_by_name');
            $this->db->from('expenses');
            $this->db->join('admin', 'admin.id = expenses.created_by', 'left');
            $this->db->where('expenses.status', 1);

            // Date filters
            if (!empty($post_data['date_from'])) {
                $this->db->where('expenses.expense_date >=', $post_data['date_from']);
            }

            if (!empty($post_data['date_to'])) {
                $this->db->where('expenses.expense_date <=', $post_data['date_to']);
            }

            // Category filter
            if (!empty($post_data['category'])) {
                $this->db->where('expenses.category', $post_data['category']);
            }

            $this->db->order_by('expenses.expense_date', 'DESC');
            $expenses = $this->db->get()->result();

            $data = array();
            $total_expenses = 0;
            $total_count = 0;
            $highest_expense = 0;

            if (!empty($expenses)) {
                $counter = 0;
                foreach ($expenses as $expense) {
                    $counter++;
                    $total_count++;
                    $amount = floatval($expense->amount);
                    $total_expenses += $amount;

                    if ($amount > $highest_expense) {
                        $highest_expense = $amount;
                    }

                    // Category badge colors
                    $category_colors = array(
                        'Masalas' => 'danger',
                        'Packing' => 'info',
                        'Transport' => 'warning',
                        'Covers' => 'primary',
                        'Salary' => 'success',
                        'Other' => 'secondary'
                    );
                    $cat_color = isset($category_colors[$expense->category]) ? $category_colors[$expense->category] : 'secondary';
                    $category_badge = '<span class="badge bg-soft-' . $cat_color . ' text-' . $cat_color . '">' . htmlspecialchars($expense->category) . '</span>';

                    $added_by = !empty($expense->added_by_name) ? htmlspecialchars($expense->added_by_name) : 'Unknown';
                    $comments = !empty($expense->comments) ? htmlspecialchars($expense->comments) : '-';

                    $delete_btn = '<button class="btn btn-sm btn-soft-danger delete-expense" data-id="' . $expense->id . '" title="Delete"><i class="uil-trash-alt"></i></button>';

                    $data[] = array(
                        $counter,
                        htmlspecialchars($expense->title),
                        $category_badge,
                        date('d M Y', strtotime($expense->expense_date)),
                        '₹' . number_format($expense->amount, 2),
                        $comments,
                        $added_by,
                        $delete_btn
                    );
                }
            }

            $avg_expense = $total_count > 0 ? $total_expenses / $total_count : 0;

            $response = array(
                'status' => 200,
                'data' => $data,
                'totals' => array(
                    'total_expenses' => $total_expenses,
                    'total_count' => $total_count,
                    'avg_expense' => $avg_expense,
                    'highest_expense' => $highest_expense
                )
            );
        } else {
            $response = array(
                'status' => 400,
                'data' => array(),
                'totals' => array(
                    'total_expenses' => 0,
                    'total_count' => 0,
                    'avg_expense' => 0,
                    'highest_expense' => 0
                )
            );
        }
        echo json_encode($response);
    }

    // AJAX: Save new expense
    public function save_expense()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (empty($post_data['title']) || empty($post_data['category']) || empty($post_data['expense_date']) || empty($post_data['amount'])) {
                $response = array(
                    'status' => 400,
                    'message' => 'Please fill all required fields'
                );
                echo json_encode($response);
                return;
            }

            $data = array(
                'title' => $post_data['title'],
                'category' => $post_data['category'],
                'expense_date' => $post_data['expense_date'],
                'amount' => floatval($post_data['amount']),
                'comments' => !empty($post_data['comments']) ? $post_data['comments'] : null,
                'created_by' => get_admin_sessiondata('id'),
                'status' => 1
            );

            $insert = $this->common->insert($data, 'expenses');

            if (!empty($insert)) {
                $response = array(
                    'status' => 200,
                    'message' => 'Expense added successfully'
                );
            } else {
                $response = array(
                    'status' => 400,
                    'message' => 'Failed to add expense. Please try again.'
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

    // AJAX: Delete expense (soft delete)
    public function delete_expense()
    {
        if (is_loggedin_admin()) {
            $post_data = $this->input->post(null, true);

            if (empty($post_data['id'])) {
                $response = array(
                    'status' => 400,
                    'message' => 'Invalid expense ID'
                );
                echo json_encode($response);
                return;
            }

            $update = $this->common->update('expenses', array('status' => 3), array('id' => $post_data['id']));

            if ($update) {
                $response = array(
                    'status' => 200,
                    'message' => 'Expense deleted successfully'
                );
            } else {
                $response = array(
                    'status' => 400,
                    'message' => 'Failed to delete expense'
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
