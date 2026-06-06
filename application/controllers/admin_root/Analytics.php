<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Analytics extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Analytics_model', 'analytics_model');
    }

    public function index()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Traffic Analytics || ' . config_item('application_name');
            $header_data['apex_chart'] = true;
            $header_data['flatpickr'] = true;
            $header_data['datatable'] = true;
            $header_data['datatable_buttons'] = true;
            $footer_data['apex_chart'] = true;
            $footer_data['flatpickr'] = true;
            $footer_data['datatable'] = true;
            $footer_data['datatable_buttons'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/analytics/index');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/analytics');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Overview stats (4 cards)
    public function get_overview_stats()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $total_sessions = $this->analytics_model->count_sessions($date_from, $date_to);
        $unique_visitors = $this->analytics_model->count_unique_visitors($date_from, $date_to);
        $avg_duration = $this->analytics_model->avg_session_duration($date_from, $date_to);
        $bounce_rate = $this->analytics_model->bounce_rate($date_from, $date_to);
        $total_pageviews = $this->analytics_model->count_total_pageviews($date_from, $date_to);

        echo json_encode(array(
            'status' => 200,
            'data' => array(
                'total_sessions' => $total_sessions,
                'unique_visitors' => $unique_visitors,
                'avg_duration' => $avg_duration,
                'bounce_rate' => $bounce_rate,
                'total_pageviews' => $total_pageviews,
            )
        ));
    }

    // AJAX: Sessions over time (area chart)
    public function get_sessions_over_time()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->sessions_over_time($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: New vs Returning visitors (donut chart)
    public function get_new_vs_returning()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->new_vs_returning($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: Sessions by device (donut chart)
    public function get_sessions_by_device()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->sessions_by_device($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: Traffic sources (donut chart)
    public function get_sessions_by_source()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->sessions_by_source($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: Avg sessions by day of week (bar chart)
    public function get_avg_sessions_by_day()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->avg_sessions_by_day_of_week($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: Top pages (DataTable)
    public function get_top_pages()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->top_pages($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: Sessions by country (bar chart)
    public function get_sessions_by_country()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->sessions_by_country($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: Conversion funnel
    public function get_conversion_funnel()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $date_from = $this->input->post('date_from', true);
        $date_to = $this->input->post('date_to', true);

        if (empty($date_from)) $date_from = date('Y-m-d', strtotime('-30 days'));
        if (empty($date_to)) $date_to = date('Y-m-d');

        $data = $this->analytics_model->conversion_funnel($date_from, $date_to);

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // AJAX: Session journey detail
    public function get_session_journey()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $session_id = $this->input->post('session_id', true);
        if (empty($session_id)) {
            echo json_encode(array('status' => 400, 'message' => 'Missing session_id'));
            return;
        }

        $data = $this->analytics_model->get_session_journey(intval($session_id));

        echo json_encode(array('status' => 200, 'data' => $data));
    }

    // =============================================
    // SESSIONS LIST PAGE
    // =============================================

    public function sessions()
    {
        if (is_loggedin_admin()) {
            $header_data['title'] = 'Sessions || ' . config_item('application_name');
            $header_data['datatable'] = true;
            $header_data['datatable_buttons'] = true;
            $header_data['flatpickr'] = true;
            $header_data['select_2'] = true;
            $footer_data['datatable'] = true;
            $footer_data['datatable_buttons'] = true;
            $footer_data['flatpickr'] = true;
            $footer_data['select_2'] = true;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/analytics/sessions');
            $this->load->view('admin/common/footer', $footer_data);
            $this->load->view('admin/validation/analytics_sessions');
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // AJAX: Sessions list data for DataTable (server-side processing)
    public function sessions_data()
    {
        $draw = intval($this->input->post('draw', true));

        if (!is_loggedin_admin()) {
            echo json_encode(array('draw' => $draw, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => array()));
            return;
        }

        $post = $this->input->post(null, true);
        $date_from = !empty($post['date_from']) ? $post['date_from'] : date('Y-m-d', strtotime('-7 days'));
        $date_to = !empty($post['date_to']) ? $post['date_to'] : date('Y-m-d');

        $filters = array();
        if (!empty($post['device_type'])) $filters['device_type'] = $post['device_type'];
        if (!empty($post['country'])) $filters['country'] = $post['country'];
        if (isset($post['is_bounce']) && $post['is_bounce'] !== '') $filters['is_bounce'] = $post['is_bounce'];
        if (!empty($post['has_purchase'])) $filters['has_purchase'] = $post['has_purchase'];

        $start = isset($post['start']) ? max(0, intval($post['start'])) : 0;
        $length = isset($post['length']) ? intval($post['length']) : 25;
        if ($length < 1) $length = 25;
        if ($length > 500) $length = 500; // hard cap to protect memory
        $search = isset($post['search']['value']) ? trim($post['search']['value']) : '';

        $columns_map = array(
            0 => 's.started_at',
            1 => 'u.fullname',
            2 => 's.device_type',
            3 => 's.landing_page',
            4 => 's.page_views',
            5 => 's.duration_seconds',
            6 => 's.utm_source',
            7 => 's.country',
            8 => 's.has_purchase',
            9 => 's.started_at',
        );
        $order_col_idx = isset($post['order'][0]['column']) ? intval($post['order'][0]['column']) : 9;
        $order_by = isset($columns_map[$order_col_idx]) ? $columns_map[$order_col_idx] : 's.started_at';
        $order_dir = (isset($post['order'][0]['dir']) && strtolower($post['order'][0]['dir']) === 'asc') ? 'ASC' : 'DESC';

        $records_total = $this->analytics_model->count_sessions_list($date_from, $date_to, $filters, '');
        $records_filtered = ($search === '')
            ? $records_total
            : $this->analytics_model->count_sessions_list($date_from, $date_to, $filters, $search);

        $sessions = $this->analytics_model->get_sessions_list(
            $date_from, $date_to, $filters, $search, $order_by, $order_dir, $start, $length
        );

        $data = array();
        $counter = $start;
        foreach ($sessions as $s) {
            $counter++;

            // Visitor label
            $visitor_label = '';
            if (!empty($s->user_name)) {
                $visitor_label = '<strong>' . htmlspecialchars($s->user_name) . '</strong>';
                if (!empty($s->user_phone)) {
                    $visitor_label .= '<br><small class="text-muted">' . htmlspecialchars($s->user_phone) . '</small>';
                }
            } else {
                $visitor_label = '<span class="text-muted">Guest</span>';
            }
            // New vs returning badge
            if ($s->visitor_first_seen >= $date_from . ' 00:00:00') {
                $visitor_label .= ' <span class="badge bg-soft-success text-success ms-1">New</span>';
            } else {
                $visitor_label .= ' <span class="badge bg-soft-primary text-primary ms-1">Returning</span>';
            }

            // Device icon
            $device_icon = 'uil-monitor';
            if ($s->device_type == 'mobile') $device_icon = 'uil-mobile-android';
            elseif ($s->device_type == 'tablet') $device_icon = 'uil-tablet';
            $device_html = '<i class="' . $device_icon . ' me-1"></i>' . ucfirst($s->device_type);
            if (!empty($s->browser)) {
                $device_html .= '<br><small class="text-muted">' . htmlspecialchars($s->browser) . '</small>';
            }

            // Landing page (truncated)
            $landing = !empty($s->landing_page) ? preg_replace('/^https?:\/\/[^\/]+/', '', $s->landing_page) : '/';
            if (strlen($landing) > 40) $landing = substr($landing, 0, 40) . '...';

            // Duration
            $dur = intval($s->duration_seconds);
            $duration_str = $dur > 0 ? ($dur >= 60 ? floor($dur / 60) . 'm ' . ($dur % 60) . 's' : $dur . 's') : '-';

            // Source
            $source = 'Direct';
            if (!empty($s->utm_source)) {
                $source = htmlspecialchars($s->utm_source);
            } elseif (!empty($s->referrer)) {
                $ref = strtolower($s->referrer);
                if (strpos($ref, 'google') !== false || strpos($ref, 'bing') !== false) $source = 'Search';
                elseif (strpos($ref, 'facebook') !== false || strpos($ref, 'instagram') !== false || strpos($ref, 'twitter') !== false) $source = 'Social';
                else $source = 'Referral';
            }

            // Location
            $location = '';
            if (!empty($s->city) && !empty($s->country)) {
                $location = htmlspecialchars($s->city) . ', ' . htmlspecialchars($s->country);
            } elseif (!empty($s->country)) {
                $location = htmlspecialchars($s->country);
            } else {
                $location = '<span class="text-muted">-</span>';
            }

            // Status badges
            $badges = '';
            if ($s->is_bounce) $badges .= '<span class="badge bg-soft-warning text-warning me-1">Bounce</span>';
            if ($s->has_cart_activity) $badges .= '<span class="badge bg-soft-info text-info me-1">Cart</span>';
            if ($s->has_checkout) $badges .= '<span class="badge bg-soft-primary text-primary me-1">Checkout</span>';
            if ($s->has_purchase) $badges .= '<span class="badge bg-soft-success text-success me-1">Purchase</span>';

            // Actions
            $actions = '<a href="' . base_url('admin_root/analytics/journey/' . $s->id) . '" class="btn btn-sm btn-soft-primary" title="View Journey"><i class="uil-rss-alt me-1"></i>Journey</a>';

            $data[] = array(
                $counter,
                $visitor_label,
                $device_html,
                '<span title="' . htmlspecialchars($s->landing_page) . '">' . htmlspecialchars($landing) . '</span>',
                '<strong>' . intval($s->page_views) . '</strong>',
                $duration_str,
                $source,
                $location,
                $badges,
                date('d M Y, h:i A', strtotime($s->started_at)),
                $actions
            );
        }

        echo json_encode(array(
            'draw' => $draw,
            'recordsTotal' => $records_total,
            'recordsFiltered' => $records_filtered,
            'data' => $data,
            'total' => $records_filtered,
            'status' => 200,
        ));
    }

    // =============================================
    // SESSION JOURNEY DETAIL PAGE
    // =============================================

    public function journey($session_id = null)
    {
        if (is_loggedin_admin()) {
            if (empty($session_id)) {
                redirect(base_url('admin_root/analytics/sessions'));
                return;
            }

            $session = $this->analytics_model->get_session_detail(intval($session_id));
            if (empty($session)) {
                redirect(base_url('admin_root/analytics/sessions'));
                return;
            }

            $header_data['title'] = 'Session Journey || ' . config_item('application_name');
            $footer_data = array();

            $data['session'] = $session;
            $data['pageviews'] = $this->analytics_model->get_session_pageviews($session->id);
            $data['events'] = $this->analytics_model->get_session_events($session->id);

            // Merge pageviews and events into a single timeline
            $timeline = array();
            foreach ($data['pageviews'] as $pv) {
                $timeline[] = array(
                    'type' => 'pageview',
                    'timestamp' => $pv->viewed_at,
                    'data' => $pv
                );
            }
            foreach ($data['events'] as $ev) {
                $timeline[] = array(
                    'type' => 'event',
                    'timestamp' => $ev->occurred_at,
                    'data' => $ev
                );
            }
            // Sort by timestamp
            usort($timeline, function ($a, $b) {
                return strtotime($a['timestamp']) - strtotime($b['timestamp']);
            });
            $data['timeline'] = $timeline;

            $this->load->view('admin/common/header', $header_data);
            $this->load->view('admin/common/sidebar');
            $this->load->view('admin/analytics/journey', $data);
            $this->load->view('admin/common/footer', $footer_data);
        } else {
            $data['title'] = 'Login || ' . config_item('application_name');
            $this->load->view('admin/auth/login', $data);
        }
    }

    // =============================================
    // LIVE SESSIONS
    // =============================================

    // AJAX: Get live sessions count + list
    public function live_sessions()
    {
        if (!is_loggedin_admin()) {
            echo json_encode(array('status' => 400));
            return;
        }

        $count = $this->analytics_model->count_live_sessions(5);
        $sessions = $this->analytics_model->get_live_sessions(5);

        $live_list = array();
        foreach ($sessions as $s) {
            $current_page = !empty($s->exit_page) ? preg_replace('/^https?:\/\/[^\/]+/', '', $s->exit_page) : '/';

            $live_list[] = array(
                'id' => $s->id,
                'user_name' => !empty($s->user_name) ? $s->user_name : 'Guest',
                'current_page' => $current_page,
                'page_views' => intval($s->page_views),
                'device_type' => $s->device_type,
                'country' => $s->country,
                'city' => $s->city,
                'has_cart' => intval($s->has_cart_activity),
                'last_activity' => $s->last_activity,
                'seconds_ago' => time() - strtotime($s->last_activity),
            );
        }

        echo json_encode(array(
            'status' => 200,
            'count' => $count,
            'sessions' => $live_list
        ));
    }
}
