<?php

// ============================================
// ADMIN LOGIN HELPER FUNCTIONS
// ============================================

/**
 * Check if the admin user is logged in.
 *
 * @return bool Returns true if the admin user is logged in, otherwise false.
 */
function is_loggedin_admin()
{
    $CI = &get_instance();
    $user = $CI->session->userdata('user_details_admin');
    return $user ? true : false;
}

/**
 * Retrieve a specific piece of data from the admin session.
 *
 * @param string $row The key of the session data to retrieve.
 * @return mixed Returns the value of the specified session data key, or false if not found.
 */
function get_admin_sessiondata($row)
{
    $CI = &get_instance();
    $user = $CI->session->userdata('user_details_admin');
    return $user ? $user->$row : false;
}

/**
 * Retrieve detailed information about the admin user.
 *
 * @param int|null $uid The user ID to fetch details for. If null, the current logged-in admin's ID is used.
 * @return object|null Returns the admin user details as an associative array, or null if not found.
 */
function get_admin_user_details($uid = null)
{
    if ($uid == null) {
        $uid = get_admin_sessiondata('id');
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'id' => $uid
    );
    $user = $CI->common->getdatabytable('admin', $where);
    return !empty($user) ? $user : null;
}


// ============================================
// USER LOGIN HELPER FUNCTIONS
// ============================================

/**
 * Check if the user is logged in.
 *
 * @return bool Returns true if the user is logged in, otherwise false.
 */
function is_loggedin_user()
{
    $CI = &get_instance();
    $user = $CI->session->userdata('user_details');
    return $user ? true : false;
}

/**
 * Retrieve a specific piece of data from the user session.
 *
 * @param string $row The key of the session data to retrieve.
 * @return mixed Returns the value of the specified session data key, or false if not found.
 */
function get_user_sessiondata($row)
{
    $CI = &get_instance();
    $user = $CI->session->userdata('user_details');
    return $user ? $user->$row : false;
}

/**
 * Retrieve detailed information about the user.
 *
 * @param int|null $uid The user ID to fetch details for. If null, the current logged-in user's ID is used.
 * @return object|null Returns the user details object, or null if not found.
 */
function get_user_details($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'id' => $uid,
        'status !=' => 3
    );
    $user = $CI->common->getdatabytable('users', $where);
    return !empty($user) ? $user : null;
}

/**
 * Get user's default address.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return object|null Returns the default address object, or null if not found.
 */
function get_user_default_address($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'user_id' => $uid,
        'is_default' => 1,
        'status' => 1
    );
    $address = $CI->common->getdatabytable('user_addresses', $where);
    return !empty($address) ? $address : null;
}

/**
 * Get all addresses for a user.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return array Returns array of address objects.
 */
function get_user_addresses($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'user_id' => $uid,
        'status' => 1
    );
    $addresses = $CI->common->getdatabytableall('user_addresses', $where, 'is_default', 'DESC');
    return !empty($addresses) ? $addresses : array();
}

/**
 * Get user's cart count.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return int Returns the number of items in user's cart.
 */
function get_cart_count($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return 0;
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'user_id' => $uid
    );
    $count = $CI->common->countWhere('cart', $where);
    return $count ? $count : 0;
}

/**
 * Get user's wishlist count.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return int Returns the number of items in user's wishlist.
 */
function get_wishlist_count($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return 0;
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'user_id' => $uid
    );
    $count = $CI->common->countWhere('wishlist', $where);
    return $count ? $count : 0;
}

/**
 * Check if a product is in user's wishlist.
 *
 * @param int $product_id The product ID to check.
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return bool Returns true if product is in wishlist, otherwise false.
 */
function is_in_wishlist($product_id, $uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return false;
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'user_id' => $uid,
        'product_id' => $product_id
    );
    $item = $CI->common->getdatabytable('wishlist', $where);
    return !empty($item) ? true : false;
}

/**
 * Check if a product is in user's cart.
 *
 * @param int $product_id The product ID to check.
 * @param int|null $variant_id The variant ID (optional).
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return object|false Returns cart item object if in cart, otherwise false.
 */
function is_in_cart($product_id, $variant_id = null, $uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return false;
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'user_id' => $uid,
        'product_id' => $product_id
    );

    if ($variant_id !== null) {
        $where['variant_id'] = $variant_id;
    }

    $item = $CI->common->getdatabytable('cart', $where);
    return !empty($item) ? $item : false;
}

/**
 * Get user's wallet balance.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return float Returns the wallet balance.
 */
function get_wallet_balance($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return 0.00;
    }

    $user = get_user_details($uid);
    return $user ? floatval($user->wallet_balance) : 0.00;
}

/**
 * Check if user's email is verified.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return bool Returns true if email is verified, otherwise false.
 */
function is_email_verified($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return false;
    }

    $user = get_user_details($uid);
    return $user && $user->is_verified == 1 ? true : false;
}

/**
 * Check if user's phone is verified.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return bool Returns true if phone is verified, otherwise false.
 */
function is_phone_verified($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return false;
    }

    $user = get_user_details($uid);
    return $user && $user->phone_verified == 1 ? true : false;
}

/**
 * Get user's pending orders count.
 *
 * @param int|null $uid The user ID. If null, the current logged-in user's ID is used.
 * @return int Returns count of pending orders.
 */
function get_pending_orders_count($uid = null)
{
    if ($uid == null) {
        $uid = get_user_sessiondata('id');
    }

    if (!$uid) {
        return 0;
    }

    $CI = &get_instance();
    $CI->db->where('user_id', $uid);
    $CI->db->where_in('order_status', array('pending', 'confirmed', 'processing', 'shipped', 'out_for_delivery'));
    return $CI->db->count_all_results('orders');
}

/**
 * Refresh user session data from database.
 * Call this after updating user profile to sync session.
 *
 * @return bool Returns true on success, false on failure.
 */
function refresh_user_session()
{
    $uid = get_user_sessiondata('id');

    if (!$uid) {
        return false;
    }

    $CI = &get_instance();
    $CI->load->model('Common_model', 'common');
    $where = array(
        'id' => $uid,
        'status' => 1
    );
    $user = $CI->common->getdatabytable('users', $where);

    if ($user) {
        $CI->session->set_userdata('user_details', $user);
        return true;
    }

    return false;
}

/**
 * Logout user and destroy session.
 *
 * @return void
 */
function logout_user()
{
    $CI = &get_instance();
    $CI->session->unset_userdata('user_details');
    $CI->session->sess_destroy();
}