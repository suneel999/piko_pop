<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Analytics_track extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('Analytics_model', 'analytics_model');
    }

    // AJAX: Track client-side events (cart_add, checkout_start, purchase, etc.)
    public function event()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid request'));
            return;
        }

        $event_type = $this->input->post('event_type', true);
        if (empty($event_type)) {
            echo json_encode(array('status' => 400, 'message' => 'Missing event_type'));
            return;
        }

        // Get current session from cookie
        $sid = $this->input->cookie('pp_sid');
        $vid = $this->input->cookie('pp_vid');
        if (empty($sid) || empty($vid)) {
            echo json_encode(array('status' => 400, 'message' => 'No active session'));
            return;
        }

        $session = $this->analytics_model->get_session_by_uuid($sid);
        $visitor = $this->analytics_model->get_visitor_by_uuid($vid);
        if (empty($session) || empty($visitor)) {
            echo json_encode(array('status' => 400, 'message' => 'Session not found'));
            return;
        }

        // Build event data
        $data = array(
            'session_id' => $session->id,
            'visitor_id' => $visitor->id,
            'event_type' => $event_type,
            'event_label' => $this->input->post('event_label', true),
            'event_value' => $this->input->post('event_value') ? floatval($this->input->post('event_value')) : null,
            'page_url' => $this->input->post('page_url', true),
            'product_id' => $this->input->post('product_id') ? intval($this->input->post('product_id')) : null,
            'variant_id' => $this->input->post('variant_id') ? intval($this->input->post('variant_id')) : null,
            'order_id' => $this->input->post('order_id') ? intval($this->input->post('order_id')) : null,
            'metadata' => $this->input->post('metadata', true),
            'occurred_at' => date('Y-m-d H:i:s'),
        );

        $this->analytics_model->create_event($data);

        // Update session flags based on event type
        $session_update = array('last_activity' => date('Y-m-d H:i:s'));
        switch ($event_type) {
            case 'cart_add':
            case 'cart_remove':
                $session_update['has_cart_activity'] = 1;
                break;
            case 'checkout_start':
                $session_update['has_checkout'] = 1;
                break;
            case 'purchase':
                $session_update['has_purchase'] = 1;
                if (!empty($data['order_id'])) {
                    $session_update['order_id'] = $data['order_id'];
                }
                break;
        }
        $this->analytics_model->update_session($session->id, $session_update);

        echo json_encode(array('status' => 200, 'message' => 'Event tracked'));
    }

    // AJAX: Update visitor with fingerprint and/or geo data
    public function update_visitor()
    {
        if (!$this->input->is_ajax_request()) {
            echo json_encode(array('status' => 400, 'message' => 'Invalid request'));
            return;
        }

        $vid = $this->input->cookie('pp_vid');
        $sid = $this->input->cookie('pp_sid');
        if (empty($vid)) {
            echo json_encode(array('status' => 400, 'message' => 'No visitor'));
            return;
        }

        $visitor = $this->analytics_model->get_visitor_by_uuid($vid);
        if (empty($visitor)) {
            echo json_encode(array('status' => 400, 'message' => 'Visitor not found'));
            return;
        }

        // Update fingerprint on visitor
        $fingerprint = $this->input->post('fingerprint', true);
        if (!empty($fingerprint)) {
            $this->analytics_model->update_visitor($visitor->id, array('fingerprint' => $fingerprint));
        }

        // Update geo data on session
        if (!empty($sid)) {
            $session = $this->analytics_model->get_session_by_uuid($sid);
            if ($session && empty($session->country)) {
                $geo_update = array();
                $country = $this->input->post('country', true);
                $city = $this->input->post('city', true);
                $region = $this->input->post('region', true);
                $screen = $this->input->post('screen_resolution', true);

                if (!empty($country)) $geo_update['country'] = $country;
                if (!empty($city)) $geo_update['city'] = $city;
                if (!empty($region)) $geo_update['region'] = $region;
                if (!empty($screen)) $geo_update['screen_resolution'] = $screen;

                if (!empty($geo_update)) {
                    $this->analytics_model->update_session($session->id, $geo_update);
                }
            }
        }

        echo json_encode(array('status' => 200, 'message' => 'Updated'));
    }
}
