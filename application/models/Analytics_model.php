<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Analytics_model extends CI_Model
{
    // =============================================
    // TRACKING METHODS
    // =============================================

    public function get_visitor_by_uuid($uuid)
    {
        return $this->db->get_where('analytics_visitors', array('visitor_uuid' => $uuid))->row();
    }

    public function get_visitor_by_fingerprint($fingerprint)
    {
        if (empty($fingerprint)) return null;
        return $this->db->get_where('analytics_visitors', array('fingerprint' => $fingerprint))->row();
    }

    public function create_visitor($data)
    {
        $this->db->insert('analytics_visitors', $data);
        return $this->db->insert_id();
    }

    public function update_visitor($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('analytics_visitors', $data);
    }

    public function get_session_by_uuid($uuid)
    {
        return $this->db->get_where('analytics_sessions', array('session_uuid' => $uuid))->row();
    }

    public function create_session($data)
    {
        $this->db->insert('analytics_sessions', $data);
        return $this->db->insert_id();
    }

    public function update_session($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('analytics_sessions', $data);
    }

    public function create_pageview($data)
    {
        $this->db->insert('analytics_pageviews', $data);
        return $this->db->insert_id();
    }

    public function update_pageview_time($id, $seconds)
    {
        $this->db->where('id', $id);
        return $this->db->update('analytics_pageviews', array('time_on_page' => $seconds));
    }

    public function get_last_pageview($session_id)
    {
        $this->db->where('session_id', $session_id);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        return $this->db->get('analytics_pageviews')->row();
    }

    public function create_event($data)
    {
        $this->db->insert('analytics_events', $data);
        return $this->db->insert_id();
    }

    // =============================================
    // DASHBOARD QUERY METHODS
    // =============================================

    public function count_sessions($date_from, $date_to)
    {
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        return $this->db->count_all_results('analytics_sessions');
    }

    public function count_unique_visitors($date_from, $date_to)
    {
        $this->db->select('COUNT(DISTINCT visitor_id) as cnt');
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        $row = $this->db->get('analytics_sessions')->row();
        return $row ? intval($row->cnt) : 0;
    }

    public function avg_session_duration($date_from, $date_to)
    {
        $this->db->select('AVG(duration_seconds) as avg_dur');
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        $this->db->where('duration_seconds >', 0);
        $row = $this->db->get('analytics_sessions')->row();
        return $row ? round(floatval($row->avg_dur)) : 0;
    }

    public function bounce_rate($date_from, $date_to)
    {
        $this->db->select('COUNT(*) as total, SUM(CASE WHEN is_bounce = 1 THEN 1 ELSE 0 END) as bounces', false);
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        $row = $this->db->get('analytics_sessions')->row();
        if ($row && $row->total > 0) {
            return round(($row->bounces / $row->total) * 100, 1);
        }
        return 0;
    }

    public function sessions_over_time($date_from, $date_to)
    {
        $this->db->select('DATE(started_at) as date, COUNT(*) as sessions', false);
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        $this->db->group_by('DATE(started_at)');
        $this->db->order_by('date', 'ASC');
        return $this->db->get('analytics_sessions')->result();
    }

    public function new_vs_returning($date_from, $date_to)
    {
        $sql = "SELECT
            SUM(CASE WHEN v.first_seen >= ? AND v.first_seen <= ? THEN 1 ELSE 0 END) as new_visitors,
            SUM(CASE WHEN v.first_seen < ? THEN 1 ELSE 0 END) as returning_visitors
            FROM analytics_sessions s
            JOIN analytics_visitors v ON v.id = s.visitor_id
            WHERE s.started_at >= ? AND s.started_at <= ?";
        $result = $this->db->query($sql, array(
            $date_from . ' 00:00:00', $date_to . ' 23:59:59',
            $date_from . ' 00:00:00',
            $date_from . ' 00:00:00', $date_to . ' 23:59:59'
        ))->row();
        return $result;
    }

    public function sessions_by_device($date_from, $date_to)
    {
        $this->db->select('device_type, COUNT(*) as count');
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        $this->db->group_by('device_type');
        $this->db->order_by('count', 'DESC');
        return $this->db->get('analytics_sessions')->result();
    }

    public function sessions_by_source($date_from, $date_to)
    {
        $sql = "SELECT
            CASE
                WHEN utm_source IS NOT NULL AND utm_source != '' THEN utm_source
                WHEN referrer IS NULL OR referrer = '' THEN 'Direct'
                WHEN referrer LIKE '%google%' OR referrer LIKE '%bing%' OR referrer LIKE '%yahoo%' OR referrer LIKE '%duckduckgo%' THEN 'Search'
                WHEN referrer LIKE '%facebook%' OR referrer LIKE '%instagram%' OR referrer LIKE '%twitter%' OR referrer LIKE '%youtube%' OR referrer LIKE '%linkedin%' THEN 'Social'
                ELSE 'Referral'
            END as source,
            COUNT(*) as count
            FROM analytics_sessions
            WHERE started_at >= ? AND started_at <= ?
            GROUP BY source
            ORDER BY count DESC";
        return $this->db->query($sql, array(
            $date_from . ' 00:00:00', $date_to . ' 23:59:59'
        ))->result();
    }

    public function avg_sessions_by_day_of_week($date_from, $date_to)
    {
        $sql = "SELECT
            DAYOFWEEK(started_at) as dow,
            COUNT(*) as total_sessions,
            COUNT(DISTINCT DATE(started_at)) as num_days
            FROM analytics_sessions
            WHERE started_at >= ? AND started_at <= ?
            GROUP BY DAYOFWEEK(started_at)
            ORDER BY dow ASC";
        return $this->db->query($sql, array(
            $date_from . ' 00:00:00', $date_to . ' 23:59:59'
        ))->result();
    }

    public function top_pages($date_from, $date_to, $limit = 20)
    {
        $this->db->select('page_url, page_title, page_type, COUNT(*) as views, ROUND(AVG(CASE WHEN time_on_page IS NOT NULL THEN time_on_page ELSE 0 END)) as avg_time', false);
        $this->db->where('viewed_at >=', $date_from . ' 00:00:00');
        $this->db->where('viewed_at <=', $date_to . ' 23:59:59');
        $this->db->group_by('page_url');
        $this->db->order_by('views', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('analytics_pageviews')->result();
    }

    public function sessions_by_country($date_from, $date_to, $limit = 10)
    {
        $this->db->select('country, COUNT(*) as count');
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        $this->db->where('country IS NOT NULL', null, false);
        $this->db->where('country !=', '');
        $this->db->group_by('country');
        $this->db->order_by('count', 'DESC');
        $this->db->limit($limit);
        return $this->db->get('analytics_sessions')->result();
    }

    public function conversion_funnel($date_from, $date_to)
    {
        $this->db->select('COUNT(*) as total_sessions,
            SUM(CASE WHEN has_cart_activity = 1 THEN 1 ELSE 0 END) as cart_adds,
            SUM(CASE WHEN has_checkout = 1 THEN 1 ELSE 0 END) as checkouts,
            SUM(CASE WHEN has_purchase = 1 THEN 1 ELSE 0 END) as purchases', false);
        $this->db->where('started_at >=', $date_from . ' 00:00:00');
        $this->db->where('started_at <=', $date_to . ' 23:59:59');
        return $this->db->get('analytics_sessions')->row();
    }

    public function get_session_journey($session_id)
    {
        $sql = "(SELECT 'pageview' as type, page_url as label, page_type as detail, NULL as event_value, viewed_at as timestamp
                 FROM analytics_pageviews WHERE session_id = ?)
                UNION ALL
                (SELECT 'event' as type, event_type as label, event_label as detail, event_value, occurred_at as timestamp
                 FROM analytics_events WHERE session_id = ?)
                ORDER BY timestamp ASC";
        return $this->db->query($sql, array($session_id, $session_id))->result();
    }

    public function count_total_pageviews($date_from, $date_to)
    {
        $this->db->where('viewed_at >=', $date_from . ' 00:00:00');
        $this->db->where('viewed_at <=', $date_to . ' 23:59:59');
        return $this->db->count_all_results('analytics_pageviews');
    }

    // =============================================
    // SESSIONS LIST & JOURNEY
    // =============================================

    public function get_sessions_list($date_from, $date_to, $filters = array(), $search = '', $order_by = 's.started_at', $order_dir = 'DESC', $start = 0, $length = 25)
    {
        $this->_apply_sessions_list_query($date_from, $date_to, $filters, $search);
        $this->db->select('s.*, v.visitor_uuid, v.fingerprint, v.user_id as v_user_id, v.total_sessions as visitor_total_sessions, v.first_seen as visitor_first_seen, u.fullname as user_name, u.phone as user_phone, u.email as user_email');
        $this->db->order_by($order_by, $order_dir);
        if ($length > 0) {
            $this->db->limit($length, $start);
        }
        return $this->db->get()->result();
    }

    public function count_sessions_list($date_from, $date_to, $filters = array(), $search = '')
    {
        $this->_apply_sessions_list_query($date_from, $date_to, $filters, $search);
        return $this->db->count_all_results();
    }

    private function _apply_sessions_list_query($date_from, $date_to, $filters, $search)
    {
        $this->db->from('analytics_sessions s');
        $this->db->join('analytics_visitors v', 'v.id = s.visitor_id', 'left');
        $this->db->join('users u', 'u.id = s.user_id', 'left');
        $this->db->where('s.started_at >=', $date_from . ' 00:00:00');
        $this->db->where('s.started_at <=', $date_to . ' 23:59:59');

        if (!empty($filters['device_type'])) {
            $this->db->where('s.device_type', $filters['device_type']);
        }
        if (!empty($filters['country'])) {
            $this->db->where('s.country', $filters['country']);
        }
        if (isset($filters['is_bounce']) && $filters['is_bounce'] !== '') {
            $this->db->where('s.is_bounce', intval($filters['is_bounce']));
        }
        if (!empty($filters['has_purchase'])) {
            $this->db->where('s.has_purchase', 1);
        }

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('u.fullname', $search);
            $this->db->or_like('u.phone', $search);
            $this->db->or_like('u.email', $search);
            $this->db->or_like('s.landing_page', $search);
            $this->db->or_like('s.country', $search);
            $this->db->or_like('s.city', $search);
            $this->db->or_like('s.utm_source', $search);
            $this->db->or_like('s.browser', $search);
            $this->db->or_like('s.device_type', $search);
            $this->db->group_end();
        }
    }

    public function get_session_detail($session_id)
    {
        $this->db->select('s.*, v.visitor_uuid, v.fingerprint, v.total_sessions as visitor_total_sessions, v.total_page_views as visitor_total_pageviews, v.first_seen as visitor_first_seen, v.first_referrer as visitor_first_referrer, u.fullname as user_name, u.phone as user_phone, u.email as user_email');
        $this->db->from('analytics_sessions s');
        $this->db->join('analytics_visitors v', 'v.id = s.visitor_id', 'left');
        $this->db->join('users u', 'u.id = s.user_id', 'left');
        $this->db->where('s.id', $session_id);
        return $this->db->get()->row();
    }

    public function get_session_pageviews($session_id)
    {
        $this->db->where('session_id', $session_id);
        $this->db->order_by('viewed_at', 'ASC');
        return $this->db->get('analytics_pageviews')->result();
    }

    public function get_session_events($session_id)
    {
        $this->db->where('session_id', $session_id);
        $this->db->order_by('occurred_at', 'ASC');
        return $this->db->get('analytics_events')->result();
    }

    // =============================================
    // LIVE SESSIONS
    // =============================================

    public function count_live_sessions($minutes = 5)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        $this->db->where('last_activity >=', $threshold);
        return $this->db->count_all_results('analytics_sessions');
    }

    public function get_live_sessions($minutes = 5)
    {
        $threshold = date('Y-m-d H:i:s', strtotime("-{$minutes} minutes"));
        $this->db->select('s.id, s.session_uuid, s.visitor_id, s.user_id, s.started_at, s.last_activity, s.page_views, s.landing_page, s.exit_page, s.device_type, s.browser, s.country, s.city, s.has_cart_activity, s.has_checkout, u.fullname as user_name');
        $this->db->from('analytics_sessions s');
        $this->db->join('users u', 'u.id = s.user_id', 'left');
        $this->db->where('s.last_activity >=', $threshold);
        $this->db->order_by('s.last_activity', 'DESC');
        return $this->db->get()->result();
    }
}
