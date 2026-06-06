<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Common_model extends CI_Model
{

    public function insert(array $details, $table)
    {
        if ($this->db->insert($table, $details)) {
            return $this->db->insert_id();
        } else {
            return false;
        }
    }

    public function delete($id, $table)
    {
        $this->db->where('id', $id);
        $this->db->delete($table);
        return $this->db->affected_rows();
    }

    public function update($table_name, $data, $where)
    {
        $this->db->update($table_name, $data, $where);
        return $this->db->affected_rows();
    }



    // Get Review Details
    public function getnumrows($tableName, array $where)
    {
        $this->db->select("*");
        $this->db->from($tableName);
        if (!empty($where)) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        $return = $this->db->get()->num_rows();

        // echo $this->db->last_query();
        return $return;
    }


    public function getdatabytable($tableName, array $where = null, $index = 'id', $order = 'ASC')
    {
        $this->db->select("*");
        $this->db->from($tableName);
        if ($where != null) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        $this->db->order_by($index, $order);
        $return = $this->db->get()->row();

        return $return;
    }

    public function getdatabytableBinary($tableName, array $where = null)
    {
        $this->db->select("*");
        $this->db->from($tableName);
        if ($where != null) {
            foreach ($where as $key => $value) {
                // Use BINARY keyword to make the comparison case-sensitive
                $this->db->where("BINARY `$key` =", $value, false);
            }
        }

        return $this->db->get()->row();
    }


    public function getdatabytableall($tableName, array $where = null, $index = 'id', $order = 'ASC')
    {
        $this->db->select("*");
        $this->db->from($tableName);
        if ($where != null) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        $this->db->order_by($index, $order);
        $return = $this->db->get()->result();

        return $return;
    }

    /**
     * Get data with limit
     *
     * @param string $tableName Table name
     * @param array $where Where conditions
     * @param string $index Order by column
     * @param string $order Order direction
     * @param int $limit Number of records
     * @return array
     */
    public function getdatabytableallLimit($tableName, array $where = null, $index = 'id', $order = 'ASC', $limit = 10)
    {
        $this->db->select("*");
        $this->db->from($tableName);
        if ($where != null) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }

        $this->db->order_by($index, $order);
        $this->db->limit($limit);
        $return = $this->db->get()->result();

        return $return;
    }


    public function deleteWhere($tableName, array $where)
    {
        foreach ($where as $key => $value) {
            $this->db->where($key, $value);
        }
        $this->db->delete($tableName);
        return $this->db->affected_rows();
    }

    public function check_admin(array $post_data)
    {
        $sql = "select * from admin where (phone_number = ? or email = ? or username = ?) and password = ?";
        $result = $this->db->query($sql, array(
            $post_data['username'],
            $post_data['username'],
            $post_data['username'],
            md5($post_data['userpassword'])
        ))->row();
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Count rows with where conditions
     *
     * @param string $tableName Table name
     * @param array $where Where conditions
     * @return int Number of rows
     */
    public function countWhere($tableName, array $where = null)
    {
        $this->db->from($tableName);
        if ($where != null) {
            foreach ($where as $key => $value) {
                $this->db->where($key, $value);
            }
        }
        return $this->db->count_all_results();
    }

    /**
     * Check user login credentials
     *
     * @param array $post_data Login data (email/phone and password)
     * @return object|false User object if valid, false otherwise
     */
    public function check_user(array $post_data)
    {
        $sql = "SELECT * FROM users WHERE (phone = ? OR email = ?) AND password = ? AND status = 1 AND is_active = 1";
        $result = $this->db->query($sql, array(
            $post_data['username'],
            $post_data['username'],
            $post_data['password'] // Should be hashed password
        ))->row();

        if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * Check user by email only (for password reset, etc.)
     *
     * @param string $email User email
     * @return object|false User object if found, false otherwise
     */
    public function get_user_by_email($email)
    {
        $this->db->where('email', $email);
        $this->db->where('status !=', 3);
        return $this->db->get('users')->row();
    }

    /**
     * Check user by phone only
     *
     * @param string $phone User phone
     * @return object|false User object if found, false otherwise
     */
    public function get_user_by_phone($phone)
    {
        $this->db->where('phone', $phone);
        $this->db->where('status !=', 3);
        return $this->db->get('users')->row();
    }

    /**
     * Check user by reset token
     *
     * @param string $token Reset token
     * @return object|false User object if valid token, false otherwise
     */
    public function get_user_by_reset_token($token)
    {
        $this->db->where('reset_token', $token);
        $this->db->where('reset_token_expires >', date('Y-m-d H:i:s'));
        $this->db->where('status', 1);
        return $this->db->get('users')->row();
    }

    /**
     * Check user by verification token
     *
     * @param string $token Verification token
     * @return object|false User object if valid token, false otherwise
     */
    public function get_user_by_verification_token($token)
    {
        $this->db->where('verification_token', $token);
        $this->db->where('verification_token_expires >', date('Y-m-d H:i:s'));
        $this->db->where('status', 1);
        return $this->db->get('users')->row();
    }

    /**
     * Verify user OTP
     *
     * @param string $phone User phone
     * @param string $otp OTP code
     * @return object|false User object if valid OTP, false otherwise
     */
    public function verify_user_otp($phone, $otp)
    {
        $this->db->where('phone', $phone);
        $this->db->where('otp', $otp);
        $this->db->where('otp_expires >', date('Y-m-d H:i:s'));
        $this->db->where('status', 1);
        return $this->db->get('users')->row();
    }

    /**
     * Update user's last login info
     *
     * @param int $user_id User ID
     * @param string $ip_address IP address
     * @return bool
     */
    public function update_user_login($user_id, $ip_address = null)
    {
        $data = array(
            'last_login' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip_address,
            'login_attempts' => 0,
            'locked_until' => null
        );
        $this->db->where('id', $user_id);
        return $this->db->update('users', $data);
    }

    /**
     * Increment failed login attempts
     *
     * @param string $email_or_phone Email or phone
     * @return bool
     */
    public function increment_login_attempts($email_or_phone)
    {
        $this->db->set('login_attempts', 'login_attempts + 1', false);
        $this->db->group_start();
        $this->db->where('email', $email_or_phone);
        $this->db->or_where('phone', $email_or_phone);
        $this->db->group_end();
        return $this->db->update('users');
    }

    /**
     * Lock user account after too many failed attempts
     *
     * @param string $email_or_phone Email or phone
     * @param int $minutes Minutes to lock
     * @return bool
     */
    public function lock_user_account($email_or_phone, $minutes = 30)
    {
        $locked_until = date('Y-m-d H:i:s', strtotime("+{$minutes} minutes"));
        $this->db->set('locked_until', $locked_until);
        $this->db->group_start();
        $this->db->where('email', $email_or_phone);
        $this->db->or_where('phone', $email_or_phone);
        $this->db->group_end();
        return $this->db->update('users');
    }

    /**
     * Check if user account is locked
     *
     * @param string $email_or_phone Email or phone
     * @return bool True if locked, false otherwise
     */
    public function is_user_locked($email_or_phone)
    {
        $this->db->select('locked_until');
        $this->db->group_start();
        $this->db->where('email', $email_or_phone);
        $this->db->or_where('phone', $email_or_phone);
        $this->db->group_end();
        $user = $this->db->get('users')->row();

        if ($user && $user->locked_until) {
            if (strtotime($user->locked_until) > time()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get featured categories
     *
     * @return array Featured categories
     */
    public function getfeatured_categorys()
    {
        $this->db->select('*');
        $this->db->from('categories');
        $this->db->where('status', 1);
        $this->db->where('featured', 1);
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get subcategories of a parent category
     *
     * @param int $parent_id Parent category ID
     * @return array Subcategories
     */
    public function getsubcats($parent_id)
    {
        $this->db->select('*');
        $this->db->from('categories');
        $this->db->where('parent_category_id', $parent_id);
        $this->db->where('status', 1);
        $this->db->order_by('sort_order', 'ASC');
        return $this->db->get()->result();
    }

    /**
     * Get products by multiple category IDs with limit
     *
     * @param array $category_ids Array of category IDs
     * @param int $limit Number of products to fetch
     * @return array Products
     */
    public function getFeaturedProducts($category_ids, $limit = 8)
    {
        if (empty($category_ids)) {
            return array();
        }

        $this->db->select('*');
        $this->db->from('products');
        $this->db->where_in('category_id', $category_ids);
        $this->db->where('status', 1);
        $this->db->order_by('id', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
}
