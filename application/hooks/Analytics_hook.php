<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Analytics_hook
{
    private $CI;
    private $cookie_vid = 'pp_vid';    // visitor ID cookie - 2 year
    private $cookie_sid = 'pp_sid';    // session ID cookie - 30 min rolling
    private $session_timeout = 1800;   // 30 minutes in seconds

    public function track()
    {
        try {
            $this->_track();
        } catch (Throwable $e) {
            log_message('error', 'Analytics hook skipped: ' . $e->getMessage());
        }
    }

    private function _track()
    {
        $this->CI =& get_instance();

        // Skip admin routes
        $uri = $this->CI->uri->uri_string();
        if (strpos($uri, 'admin_root') === 0) return;

        // Skip analytics_track endpoint (prevents recursion)
        if (strpos($uri, 'analytics_track') === 0) return;

        // Skip static assets and non-page requests
        $request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if ($this->is_static_asset($request_uri) || $this->is_static_asset($uri)) return;

        // Skip bot-only paths (sitemap.xml, robots.txt, /.well-known/*, scanners, etc.)
        if ($this->is_bot_path($request_uri) || $this->is_bot_path($uri)) return;

        // Skip AJAX requests
        if ($this->CI->input->is_ajax_request()) return;

        // Only track real browser navigations (GET requests with HTML accept header)
        $method = isset($_SERVER['REQUEST_METHOD']) ? strtoupper($_SERVER['REQUEST_METHOD']) : 'GET';
        if ($method !== 'GET') return;
        $accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : '';
        if ($accept !== '' && stripos($accept, 'text/html') === false) return;

        // Skip bots/crawlers (CI's built-in list + expanded user-agent patterns)
        $this->CI->load->library('user_agent');
        $ua_string = $this->CI->agent->agent_string();
        if ($this->CI->agent->is_robot()) return;
        if ($this->is_bot_user_agent($ua_string)) return;

        // Load model
        $this->CI->load->model('Analytics_model', 'analytics_model');

        // Step 1: Get or create visitor
        $visitor = $this->get_or_create_visitor();
        if (!$visitor) return;

        // Step 2: Get or create session
        $session = $this->get_or_create_session($visitor);
        if (!$session) return;

        // Step 3: Track page view
        $this->track_pageview($visitor, $session);
    }

    private function get_or_create_visitor()
    {
        $vid = $this->CI->input->cookie($this->cookie_vid);
        $visitor = null;

        if (!empty($vid)) {
            $visitor = $this->CI->analytics_model->get_visitor_by_uuid($vid);
        }

        if ($visitor) {
            // Update last_seen and user_id if logged in
            $update = array('last_seen' => date('Y-m-d H:i:s'));
            if (function_exists('is_loggedin_user') && is_loggedin_user()) {
                $user_id = get_user_sessiondata('id');
                if (!empty($user_id) && empty($visitor->user_id)) {
                    $update['user_id'] = $user_id;
                }
            }
            $this->CI->analytics_model->update_visitor($visitor->id, $update);
            return $visitor;
        }

        // Create new visitor
        $uuid = generate_uuid();
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $current_url = current_url();

        // Extract UTM params
        $utm_source = $this->CI->input->get('utm_source');
        $utm_medium = $this->CI->input->get('utm_medium');
        $utm_campaign = $this->CI->input->get('utm_campaign');

        $data = array(
            'visitor_uuid' => $uuid,
            'first_seen' => date('Y-m-d H:i:s'),
            'last_seen' => date('Y-m-d H:i:s'),
            'first_referrer' => $referrer,
            'first_landing_page' => $current_url,
            'first_utm_source' => $utm_source,
            'first_utm_medium' => $utm_medium,
            'first_utm_campaign' => $utm_campaign,
            'total_sessions' => 0,
            'total_page_views' => 0,
        );

        // Link user if logged in
        if (function_exists('is_loggedin_user') && is_loggedin_user()) {
            $data['user_id'] = get_user_sessiondata('id');
        }

        $visitor_id = $this->CI->analytics_model->create_visitor($data);

        if ($visitor_id) {
            // Set 2-year cookie
            $this->set_cookie($this->cookie_vid, $uuid, 63072000); // 2 years
            $data['id'] = $visitor_id;
            return (object) $data;
        }

        return null;
    }

    private function get_or_create_session($visitor)
    {
        $sid = $this->CI->input->cookie($this->cookie_sid);
        $session = null;

        if (!empty($sid)) {
            $session = $this->CI->analytics_model->get_session_by_uuid($sid);
            if ($session) {
                // Check if session has timed out (30 min since last activity)
                $last_activity = strtotime($session->last_activity);
                if ((time() - $last_activity) > $this->session_timeout) {
                    // Session expired, create new one
                    // Update duration on old session
                    $duration = $last_activity - strtotime($session->started_at);
                    $this->CI->analytics_model->update_session($session->id, array('duration_seconds' => $duration));
                    $session = null;
                } else {
                    // Refresh session cookie (rolling 30 min)
                    $this->set_cookie($this->cookie_sid, $sid, $this->session_timeout);
                    return $session;
                }
            }
        }

        // Create new session
        $uuid = generate_uuid();
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $current_url = current_url();

        // Extract UTM params
        $utm_source = $this->CI->input->get('utm_source');
        $utm_medium = $this->CI->input->get('utm_medium');
        $utm_campaign = $this->CI->input->get('utm_campaign');

        // Detect device
        $device_type = 'desktop';
        if ($this->CI->agent->is_mobile()) {
            // Check for tablet user agents
            $ua = strtolower($this->CI->agent->agent_string());
            if (strpos($ua, 'ipad') !== false || strpos($ua, 'tablet') !== false || strpos($ua, 'kindle') !== false) {
                $device_type = 'tablet';
            } else {
                $device_type = 'mobile';
            }
        }

        $data = array(
            'session_uuid' => $uuid,
            'visitor_id' => $visitor->id,
            'started_at' => date('Y-m-d H:i:s'),
            'last_activity' => date('Y-m-d H:i:s'),
            'landing_page' => $current_url,
            'exit_page' => $current_url,
            'referrer' => $referrer,
            'utm_source' => $utm_source,
            'utm_medium' => $utm_medium,
            'utm_campaign' => $utm_campaign,
            'device_type' => $device_type,
            'browser' => $this->CI->agent->browser() . ' ' . $this->CI->agent->version(),
            'os' => $this->CI->agent->platform(),
            'ip_address' => $this->CI->input->ip_address(),
            'page_views' => 0,
            'is_bounce' => 1,
        );

        // Link user if logged in
        if (function_exists('is_loggedin_user') && is_loggedin_user()) {
            $data['user_id'] = get_user_sessiondata('id');
        }

        $session_id = $this->CI->analytics_model->create_session($data);

        if ($session_id) {
            // Increment visitor's total_sessions
            $this->CI->analytics_model->update_visitor($visitor->id, array(
                'total_sessions' => intval($visitor->total_sessions) + 1
            ));

            // Set 30-min cookie
            $this->set_cookie($this->cookie_sid, $uuid, $this->session_timeout);

            $data['id'] = $session_id;
            return (object) $data;
        }

        return null;
    }

    private function track_pageview($visitor, $session)
    {
        $current_url = current_url();
        $referrer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        // Detect page type and extract IDs
        $page_info = $this->detect_page_type();

        $data = array(
            'session_id' => $session->id,
            'visitor_id' => $visitor->id,
            'page_url' => $current_url,
            'page_type' => $page_info['type'],
            'referrer_url' => $referrer,
            'product_id' => $page_info['product_id'],
            'category_id' => $page_info['category_id'],
            'search_query' => $page_info['search_query'],
            'viewed_at' => date('Y-m-d H:i:s'),
        );

        // Calculate time_on_page for previous pageview
        $last_pv = $this->CI->analytics_model->get_last_pageview($session->id);
        if ($last_pv) {
            $time_on_page = time() - strtotime($last_pv->viewed_at);
            if ($time_on_page > 0 && $time_on_page < 3600) { // cap at 1 hour
                $this->CI->analytics_model->update_pageview_time($last_pv->id, $time_on_page);
            }
        }

        $this->CI->analytics_model->create_pageview($data);

        // Update session
        $new_page_views = intval($session->page_views) + 1;
        $duration = time() - strtotime($session->started_at);
        $session_update = array(
            'page_views' => $new_page_views,
            'exit_page' => $current_url,
            'last_activity' => date('Y-m-d H:i:s'),
            'duration_seconds' => $duration,
        );
        if ($new_page_views > 1) {
            $session_update['is_bounce'] = 0;
        }
        $this->CI->analytics_model->update_session($session->id, $session_update);

        // Update visitor total_page_views
        $this->CI->analytics_model->update_visitor($visitor->id, array(
            'total_page_views' => intval($visitor->total_page_views) + 1
        ));
    }

    private function detect_page_type()
    {
        $result = array('type' => 'other', 'product_id' => null, 'category_id' => null, 'search_query' => null);

        $class = $this->CI->router->fetch_class();
        $method = $this->CI->router->fetch_method();

        switch (strtolower($class)) {
            case 'home':
                $result['type'] = 'home';
                break;
            case 'shop':
                if ($method == 'product') {
                    $result['type'] = 'product';
                    $slug = $this->CI->uri->segment(2);
                    if ($slug && function_exists('get_id_from_slug')) {
                        $result['product_id'] = get_id_from_slug($slug);
                    }
                } elseif ($method == 'category') {
                    $result['type'] = 'category';
                    $slug = $this->CI->uri->segment(2);
                    if ($slug && function_exists('get_id_from_slug')) {
                        $result['category_id'] = get_id_from_slug($slug);
                    }
                } elseif ($method == 'search') {
                    $result['type'] = 'search';
                    $result['search_query'] = $this->CI->input->get('q');
                } else {
                    $result['type'] = 'shop';
                }
                break;
            case 'cart':
                $result['type'] = 'cart';
                break;
            case 'checkout':
                $result['type'] = 'checkout';
                break;
            case 'account':
                $result['type'] = 'account';
                break;
            case 'login':
                $result['type'] = 'login';
                break;
            case 'feedback':
                $result['type'] = 'feedback';
                break;
            case 'page':
                $result['type'] = 'page';
                break;
        }

        return $result;
    }

    private function is_bot_path($uri)
    {
        $path = parse_url($uri, PHP_URL_PATH);
        if ($path === false || $path === null) $path = $uri;
        $path = strtolower($path);

        $bot_paths = array(
            'sitemap.xml', 'sitemap_index', 'sitemap-', 'sitemap.txt',
            'robots.txt', 'ads.txt', 'security.txt', 'humans.txt',
            '/.well-known/', 'wp-admin', 'wp-login', 'wp-content', 'wp-includes',
            'xmlrpc.php', '.env', '.git/', '.svn/', '.htaccess',
            'phpinfo', 'phpmyadmin', '/install/', '/setup/', '/installer/',
            '/server-status', '/server-info',
        );
        foreach ($bot_paths as $bp) {
            if (stripos($path, $bp) !== false) return true;
        }
        return false;
    }

    private function is_bot_user_agent($ua)
    {
        if (empty($ua) || strlen($ua) < 10) return true;
        $ua_lower = strtolower($ua);
        $patterns = array(
            'bot', 'crawler', 'spider', 'crawling', 'slurp', 'fetcher', 'scraper',
            'googlebot', 'bingbot', 'baiduspider', 'yandex', 'duckduckbot',
            'facebookexternalhit', 'twitterbot', 'linkedinbot', 'pinterestbot',
            'slackbot', 'telegrambot', 'whatsapp', 'applebot', 'semrush', 'ahrefs',
            'mj12bot', 'dotbot', 'petalbot', 'gptbot', 'claudebot', 'anthropic',
            'ccbot', 'perplexity', 'headlesschrome', 'phantomjs', 'puppeteer',
            'selenium', 'playwright', 'curl/', 'wget/', 'python-requests',
            'python-urllib', 'java/', 'go-http-client', 'axios/', 'okhttp',
            'libwww', 'lwp::', 'httpclient', 'scrapy', 'apachebench', 'jmeter',
            'masscan', 'nmap', 'sqlmap', 'nikto', 'wpscan', 'zgrab', 'censys',
            'shodan', 'expanse', 'paloalto', 'mauibot', 'serpstatbot', 'bytespider',
            'amazonbot', 'cohere-ai', 'meta-externalagent', 'duckassistbot',
            'monitor', 'uptime', 'pingdom', 'statuscake', 'site24x7',
        );
        foreach ($patterns as $p) {
            if (strpos($ua_lower, $p) !== false) return true;
        }
        return false;
    }

    private function is_static_asset($uri)
    {
        // Skip asset directories
        $skip_paths = array('/assets/', '/user_assets/', '/uploads/', '/vendor/', '/node_modules/');
        foreach ($skip_paths as $path) {
            if (stripos($uri, $path) !== false) return true;
        }

        // Skip static file extensions
        $ext = strtolower(pathinfo(parse_url($uri, PHP_URL_PATH), PATHINFO_EXTENSION));
        $skip_ext = array(
            'js', 'css', 'map', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'ico', 'svg',
            'woff', 'woff2', 'ttf', 'eot', 'otf', 'pdf', 'zip', 'mp4', 'mp3', 'webm'
        );
        if (in_array($ext, $skip_ext)) return true;

        return false;
    }

    private function set_cookie($name, $value, $expire)
    {
        $this->CI->input->set_cookie(array(
            'name'   => $name,
            'value'  => $value,
            'expire' => $expire,
            'path'   => '/',
            'secure' => false,
            'httponly' => true,
        ));
    }
}
