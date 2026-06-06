<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if (is_loggedin_admin()) {

            $header_data['title'] = 'Dashboard || ' . config_item('application_name');
            $header_data['apex_chart'] = true;
            $footer_data['apex_chart'] = true;

            // Get dashboard statistics
            $data = $this->get_dashboard_stats();

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/home/index', $data);
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/home', $data);
        } else {
            //load login files..
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    private function get_dashboard_stats()
    {
        $data = array();

        // Total Revenue (paid orders, excluding cancelled/refunded)
        $this->db->select_sum('total_amount');
        $this->db->where('payment_status', 'paid');
        $this->db->where_not_in('order_status', array('cancelled', 'refunded'));
        $result = $this->db->get('orders')->row();
        $data['total_revenue'] = $result->total_amount ? $result->total_amount : 0;

        // This Month Revenue
        $this->db->select_sum('total_amount');
        $this->db->where('payment_status', 'paid');
        $this->db->where_not_in('order_status', array('cancelled', 'refunded'));
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $result = $this->db->get('orders')->row();
        $data['month_revenue'] = $result->total_amount ? $result->total_amount : 0;

        // Total Orders
        $this->db->where('order_status !=', 'cancelled');
        $data['total_orders'] = $this->db->count_all_results('orders');

        // Today's Orders
        $this->db->where('DATE(created_at)', date('Y-m-d'));
        $data['today_orders'] = $this->db->count_all_results('orders');

        // Pending Orders
        $this->db->where('order_status', 'pending');
        $data['pending_orders'] = $this->db->count_all_results('orders');

        // Processing Orders (confirmed + processing)
        $this->db->where_in('order_status', array('confirmed', 'processing'));
        $data['processing_orders'] = $this->db->count_all_results('orders');

        // Shipped Orders (shipped + out_for_delivery)
        $this->db->where_in('order_status', array('shipped', 'out_for_delivery'));
        $data['shipped_orders'] = $this->db->count_all_results('orders');

        // Delivered Orders
        $this->db->where('order_status', 'delivered');
        $data['delivered_orders'] = $this->db->count_all_results('orders');

        // Total Users
        $this->db->where('status !=', 3); // Exclude deleted
        $data['total_users'] = $this->db->count_all_results('users');

        // New Users This Month
        $this->db->where('status !=', 3);
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $data['new_users'] = $this->db->count_all_results('users');

        // Total Products
        $this->db->where('status', 1);
        $data['total_products'] = $this->db->count_all_results('products');

        // Low Stock Products (stock <= 10)
        $this->db->where('status', 1);
        $this->db->where('stock <=', 10);
        $data['low_stock_products'] = $this->db->count_all_results('products');

        // Total Categories
        $this->db->where('status', 1);
        $data['total_categories'] = $this->db->count_all_results('categories');

        // Pending Payment Orders
        $this->db->where('payment_status', 'pending');
        $this->db->where('order_status !=', 'cancelled');
        $data['pending_payment'] = $this->db->count_all_results('orders');

        return $data;
    }

    // AJAX: Get monthly orders data for chart
    public function get_monthly_orders()
    {
        if (is_loggedin_admin()) {
            $year = date('Y');

            $this->db->select('MONTH(created_at) as month, COUNT(*) as orders, SUM(total_amount) as revenue');
            $this->db->from('orders');
            $this->db->where('payment_status', 'paid');
            $this->db->where_not_in('order_status', array('cancelled', 'refunded'));
            $this->db->where('YEAR(created_at)', $year);
            $this->db->group_by('MONTH(created_at)');
            $this->db->order_by('month', 'ASC');
            $result = $this->db->get()->result();

            echo json_encode(array('status' => 200, 'data' => $result));
        } else {
            echo json_encode(array('status' => 400, 'data' => array()));
        }
    }

    // AJAX: Get order status breakdown for chart
    public function get_order_status_breakdown()
    {
        if (is_loggedin_admin()) {
            $this->db->select('order_status, COUNT(*) as count');
            $this->db->from('orders');
            $this->db->where('order_status !=', 'cancelled');
            $this->db->group_by('order_status');
            $result = $this->db->get()->result();

            echo json_encode(array('status' => 200, 'data' => $result));
        } else {
            echo json_encode(array('status' => 400, 'data' => array()));
        }
    }

    // AJAX: Get top selling products
    public function get_top_products()
    {
        if (is_loggedin_admin()) {
            $this->db->select('p.product_name, SUM(oi.quantity) as total_sold, SUM(oi.total_price) as total_revenue');
            $this->db->from('order_items oi');
            $this->db->join('products p', 'p.id = oi.product_id');
            $this->db->join('orders o', 'o.id = oi.order_id');
            $this->db->where('o.payment_status', 'paid');
            $this->db->where_not_in('o.order_status', array('cancelled', 'refunded'));
            $this->db->group_by('oi.product_id');
            $this->db->order_by('total_sold', 'DESC');
            $this->db->limit(10);
            $result = $this->db->get()->result();

            echo json_encode(array('status' => 200, 'data' => $result));
        } else {
            echo json_encode(array('status' => 400, 'data' => array()));
        }
    }

    // AJAX: Get recent orders
    public function get_recent_orders()
    {
        if (is_loggedin_admin()) {
            $this->db->select('o.*, u.fullname as customer_name');
            $this->db->from('orders o');
            $this->db->join('users u', 'u.id = o.user_id', 'left');
            $this->db->order_by('o.created_at', 'DESC');
            $this->db->limit(10);
            $result = $this->db->get()->result();

            echo json_encode(array('status' => 200, 'data' => $result));
        } else {
            echo json_encode(array('status' => 400, 'data' => array()));
        }
    }

    // AJAX: Get payment method breakdown
    public function get_payment_breakdown()
    {
        if (is_loggedin_admin()) {
            $this->db->select('payment_method, COUNT(*) as count, SUM(total_amount) as total');
            $this->db->from('orders');
            $this->db->where('payment_status', 'paid');
            $this->db->where_not_in('order_status', array('cancelled', 'refunded'));
            $this->db->group_by('payment_method');
            $result = $this->db->get()->result();

            echo json_encode(array('status' => 200, 'data' => $result));
        } else {
            echo json_encode(array('status' => 400, 'data' => array()));
        }
    }
}
