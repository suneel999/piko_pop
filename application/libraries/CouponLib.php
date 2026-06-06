<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * CouponLib - Coupon Validation and Application Library
 *
 * Handles coupon validation, discount calculation, and usage tracking
 * for e-commerce cart and checkout.
 */
class CouponLib
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();
        $this->CI->load->database();
    }

    /**
     * Validate a coupon for cart/checkout
     *
     * @param string $coupon_code The coupon code
     * @param float $cart_total The cart subtotal before discount
     * @param int $user_id The user ID
     * @return array [
     *   'valid' => bool,
     *   'message' => string,
     *   'coupon' => object|null,
     *   'discount_amount' => float,
     *   'final_amount' => float
     * ]
     */
    public function validateCoupon($coupon_code, $cart_total, $user_id = null)
    {
        $response = array(
            'valid' => false,
            'message' => '',
            'coupon' => null,
            'discount_amount' => 0,
            'final_amount' => $cart_total
        );

        if (empty($coupon_code)) {
            $response['message'] = 'Please enter a coupon code.';
            return $response;
        }

        $coupon_code = strtoupper(trim($coupon_code));
        $cart_total = floatval($cart_total);
        $current_date = date('Y-m-d');

        // Fetch coupon
        $this->CI->db->where('coupon_code', $coupon_code);
        $this->CI->db->where('status', 1);
        $coupon = $this->CI->db->get('coupons')->row();

        if (!$coupon) {
            $response['message'] = 'Invalid coupon code.';
            return $response;
        }

        // Check validity period - start date
        if (!empty($coupon->start_date) && $current_date < $coupon->start_date) {
            $response['message'] = 'This coupon is not yet valid. Valid from: ' . date('d M Y', strtotime($coupon->start_date));
            return $response;
        }

        // Check validity period - end date
        if (!empty($coupon->end_date) && $current_date > $coupon->end_date) {
            $response['message'] = 'This coupon has expired.';
            return $response;
        }

        // Check minimum order amount
        if (!empty($coupon->min_amount) && $cart_total < floatval($coupon->min_amount)) {
            $response['message'] = 'Minimum order amount of $' . number_format($coupon->min_amount, 2) . ' required for this coupon.';
            return $response;
        }

        // Check total usage limit
        if (!empty($coupon->usage_limit) && $coupon->usage_limit > 0) {
            if ($coupon->used_count >= $coupon->usage_limit) {
                $response['message'] = 'This coupon has reached its usage limit.';
                return $response;
            }
        }

        // Check per-user usage limit (if user is logged in)
        if ($user_id && !empty($coupon->per_user_limit) && $coupon->per_user_limit > 0) {
            $user_usage = $this->getUserUsageCount($coupon->id, $user_id);
            if ($user_usage >= $coupon->per_user_limit) {
                $response['message'] = 'You have already used this coupon the maximum number of times.';
                return $response;
            }
        }

        // Calculate discount
        $discount_amount = $this->calculateDiscount($coupon, $cart_total);
        $final_amount = $cart_total - $discount_amount;

        // Ensure final amount is not negative
        if ($final_amount < 0) {
            $final_amount = 0;
            $discount_amount = $cart_total;
        }

        $response['valid'] = true;
        $response['message'] = 'Coupon applied successfully! You saved $' . number_format($discount_amount, 2);
        $response['coupon'] = $coupon;
        $response['discount_amount'] = round($discount_amount, 2);
        $response['final_amount'] = round($final_amount, 2);

        return $response;
    }

    /**
     * Calculate discount amount based on coupon type
     *
     * @param object $coupon
     * @param float $order_amount
     * @return float
     */
    public function calculateDiscount($coupon, $order_amount)
    {
        $discount = 0;

        // coupon_type: 1 = percentage, 2 = fixed amount
        if ($coupon->coupon_type == 1) {
            // Percentage discount
            $discount = ($order_amount * floatval($coupon->discount_value)) / 100;

            // Apply max discount cap if set
            if (!empty($coupon->max_discount) && $coupon->max_discount > 0 && $discount > floatval($coupon->max_discount)) {
                $discount = floatval($coupon->max_discount);
            }
        } else {
            // Fixed amount discount
            $discount = floatval($coupon->discount_value);
        }

        // Discount cannot exceed order amount
        if ($discount > $order_amount) {
            $discount = $order_amount;
        }

        return round($discount, 2);
    }

    /**
     * Get user's usage count for a specific coupon
     *
     * @param int $coupon_id
     * @param int $user_id
     * @return int
     */
    public function getUserUsageCount($coupon_id, $user_id)
    {
        return $this->CI->db
            ->where('coupon_id', $coupon_id)
            ->where('user_id', $user_id)
            ->count_all_results('coupon_usage');
    }

    /**
     * Record coupon usage after successful order
     *
     * @param int $coupon_id
     * @param int $user_id
     * @param int $order_id
     * @param float $discount_amount
     * @return bool
     */
    public function recordUsage($coupon_id, $user_id, $order_id, $discount_amount)
    {
        $this->CI->db->trans_start();

        // Record coupon usage
        $usage_data = array(
            'coupon_id' => $coupon_id,
            'user_id' => $user_id,
            'order_id' => $order_id,
            'discount_amount' => $discount_amount,
            'created_at' => date('Y-m-d H:i:s')
        );

        $this->CI->db->insert('coupon_usage', $usage_data);

        // Increment used_count on coupon
        $this->CI->db
            ->set('used_count', 'used_count + 1', false)
            ->where('id', $coupon_id)
            ->update('coupons');

        $this->CI->db->trans_complete();

        return $this->CI->db->trans_status();
    }

    /**
     * Reverse coupon usage - For order cancellation/refund
     *
     * @param int $order_id
     * @return bool
     */
    public function reverseUsage($order_id)
    {
        // Get usage record
        $usage = $this->CI->db
            ->where('order_id', $order_id)
            ->get('coupon_usage')
            ->row();

        if (!$usage) {
            return true; // No coupon was used
        }

        $this->CI->db->trans_start();

        // Decrement used_count on coupon
        $this->CI->db
            ->set('used_count', 'GREATEST(used_count - 1, 0)', false)
            ->where('id', $usage->coupon_id)
            ->update('coupons');

        // Delete usage record
        $this->CI->db
            ->where('id', $usage->id)
            ->delete('coupon_usage');

        $this->CI->db->trans_complete();

        return $this->CI->db->trans_status();
    }

    /**
     * Get coupon by code
     *
     * @param string $coupon_code
     * @return object|null
     */
    public function getCouponByCode($coupon_code)
    {
        return $this->CI->db
            ->where('coupon_code', strtoupper(trim($coupon_code)))
            ->where('status', 1)
            ->get('coupons')
            ->row();
    }

    /**
     * Get coupon by ID
     *
     * @param int $coupon_id
     * @return object|null
     */
    public function getCouponById($coupon_id)
    {
        return $this->CI->db
            ->where('id', $coupon_id)
            ->where('status', 1)
            ->get('coupons')
            ->row();
    }

    /**
     * Get available coupons for a user
     *
     * @param float $cart_total Cart subtotal
     * @param int|null $user_id User ID (optional)
     * @return array List of available coupons
     */
    public function getAvailableCoupons($cart_total = 0, $user_id = null)
    {
        $current_date = date('Y-m-d');

        $this->CI->db->select('*');
        $this->CI->db->from('coupons');
        $this->CI->db->where('status', 1);

        // Check start date
        $this->CI->db->group_start();
        $this->CI->db->where('start_date IS NULL');
        $this->CI->db->or_where('start_date <=', $current_date);
        $this->CI->db->group_end();

        // Check end date
        $this->CI->db->group_start();
        $this->CI->db->where('end_date IS NULL');
        $this->CI->db->or_where('end_date >=', $current_date);
        $this->CI->db->group_end();

        // Check usage limit
        $this->CI->db->group_start();
        $this->CI->db->where('usage_limit', 0);
        $this->CI->db->or_where('usage_limit IS NULL');
        $this->CI->db->or_where('used_count < usage_limit', null, false);
        $this->CI->db->group_end();

        // Filter by minimum amount if cart total provided
        if ($cart_total > 0) {
            $this->CI->db->group_start();
            $this->CI->db->where('min_amount', 0);
            $this->CI->db->or_where('min_amount IS NULL');
            $this->CI->db->or_where('min_amount <=', $cart_total);
            $this->CI->db->group_end();
        }

        $this->CI->db->order_by('discount_value', 'DESC');

        $coupons = $this->CI->db->get()->result();

        // Filter by per-user limit if user is logged in
        if ($user_id) {
            $available_coupons = array();
            foreach ($coupons as $coupon) {
                if (!empty($coupon->per_user_limit) && $coupon->per_user_limit > 0) {
                    $user_usage = $this->getUserUsageCount($coupon->id, $user_id);
                    if ($user_usage >= $coupon->per_user_limit) {
                        continue;
                    }
                }

                // Calculate potential discount
                if ($cart_total > 0) {
                    $coupon->potential_discount = $this->calculateDiscount($coupon, $cart_total);
                }

                $available_coupons[] = $coupon;
            }
            return $available_coupons;
        }

        return $coupons;
    }

    /**
     * Format discount text for display
     *
     * @param object $coupon
     * @return string
     */
    public function getDiscountText($coupon)
    {
        if ($coupon->coupon_type == 1) {
            // Percentage
            $text = $coupon->discount_value . '% off';
            if (!empty($coupon->max_discount) && $coupon->max_discount > 0) {
                $text .= ' (up to $' . number_format($coupon->max_discount, 2) . ')';
            }
        } else {
            // Fixed amount
            $text = '$' . number_format($coupon->discount_value, 2) . ' off';
        }

        return $text;
    }
}
