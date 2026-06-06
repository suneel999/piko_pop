<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Shiprocket
{
    private $CI;
    private $base_url = "https://apiv2.shiprocket.in/v1/external";
    private $email;
    private $password;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();

        // Load credentials from config
        $this->email = config_item('shiprocket_email');
        $this->password = config_item('shiprocket_password');
    }

    // Function to get the token from the database or regenerate if expired
    private function getToken()
    {
        $query = $this->CI->db->get_where('tokens', ['api_name' => 'shiprocket']);
        $token_data = $query->row();

        if ($token_data && strtotime($token_data->expires_at) > time()) {
            return $token_data->token;
        }

        return $this->regenerateToken();
    }

    // Function to regenerate the token
    private function regenerateToken()
    {
        $url = $this->base_url . "/auth/login";
        $payload = json_encode([
            "email" => $this->email,
            "password" => $this->password
        ]);

        $response = $this->makeRequest($url, "POST", $payload);

        // print_r($response); // Debugging line to check the response

        if (isset($response['token'])) {
            $expires_at = date("Y-m-d H:i:s", time() + 777600); // Assume token is valid for 9 days # 86400 = 1 day * 9 = 777600

            $this->CI->db->replace('tokens', [
                'api_name' => 'shiprocket',
                'token' => $response['token'],
                'expires_at' => $expires_at
            ]);

            return $response['token'];
        }

        throw new Exception("Unable to generate Shiprocket token.");
    }

    // Function to create an order
    public function createOrder($orderData)
    {
        $url = $this->base_url . "/orders/create/adhoc";
        $token = $this->getToken();

        $response = $this->makeRequest($url, "POST", json_encode($orderData), $token);
        if (isset($response['order_id'])) {
            //return orderid and shipment id
            return $response;
        }

        throw new Exception("Order creation failed: " . json_encode($response));
    }

    // Function to cancel an order
    public function cancelOrder($shipment_id)
    {
        $url = $this->base_url . "/orders/cancel";
        $token = $this->getToken();

        $payload = json_encode(["ids" => [$shipment_id]]);
        $response = $this->makeRequest($url, "POST", $payload, $token);

        return $response;
    }

    // Function to track an order
    public function trackOrder($awb)
    {
        $url = $this->base_url . "/courier/track/shipment/$awb";
        $token = $this->getToken();

        $response = $this->makeRequest($url, "GET", null, $token);

        return $response;
    }

    // Generic function to make HTTP requests
    private function makeRequest($url, $method, $payload = null, $token = null)
    {
        $headers = ['Content-Type: application/json'];

        if ($token) {
            $headers[] = "Authorization: Bearer $token";
        }

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120); // Set timeout to 2 minutes

        if ($payload) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        }

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Get HTTP status code
        curl_close($curl);

        $decoded_response = json_decode($response, true);

        // If token is expired, regenerate it and retry
        if ($http_code === 401 && isset($decoded_response['message']) && strpos($decoded_response['message'], 'invalid token') !== false) {
            $new_token = $this->regenerateToken();
            return $this->makeRequest($url, $method, $payload, $new_token);
        }

        return $decoded_response;
    }
}