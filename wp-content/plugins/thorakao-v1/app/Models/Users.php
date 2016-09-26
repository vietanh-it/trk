<?php
/**
 * Created by PhpStorm.
 * User: Vo sy dao
 * Date: 3/21/2016
 * Time: 4:31 PM
 */
namespace TVA\Models;

class Users
{
    protected $_wpdb;
    protected $_table_info;

    private static $instance;

    /**
     * Users constructor.
     */
    function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;

        $this->_table_info = $wpdb->prefix . "user_info";
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Users();
        }

        return self::$instance;
    }

    /**
     * Tao moi user
     *
     * @param $data
     * @return array
     */
    public function create($data)
    {
        $message = '';
        $status = 'error';

        // kiem tra email ton tai
        if (username_exists($data['email'])) {
            $message .= 'Email này đã là thành viên tại Marry.vn, vui lòng đăng nhập để tham gia.';
        } else {
            wp_create_user($data["email"], $data["password"], $data["email"]);

            $user_id = $this->_wpdb->get_var('SELECT ID FROM ' . $this->_wpdb->users . ' WHERE user_login = "' . $data['email'] . '"');

            if ($user_id) {

                //cap nhap user nicename va display name
                $user_nicename = $this->builtUserNicename($data["email"]);
                wp_update_user(array(
                    "ID" => $user_id,
                    "user_nicename" => $user_nicename,
                    "display_name" => $data['full_name']
                ));

                // tao user info cho member
                $user_info = array(
                    'user_id' => $user_id,
                    'wedding_date' => $data['wedding_date'],
                    'gender' => $data['gender'],
                    'city_id' => $data['location'],
                    'phone' => $data['phone'],
                    'updated_at' => current_time('mysql')
                );
                $this->_wpdb->insert($this->_wpdb->prefix . 'user_info', $user_info);

                // add vao list sendy
                if (function_exists('sendy_action')) {
                    sendy_action(array(
                        'name' => $data['full_name'],
                        'email' => $data['email'],
                        'list' => SENDY_LIST
                    ), 1);
                }

                // Dang nhap sau khi dang ky xong
                $login = $this->login($data);
                if ($login['status'] == 'error') {
                    $message = $login['message'];
                } else {
                    $status = 'success';
                    $message = 'Đăng ký thành viên thành công';
                }

            } else {
                $message = 'Có lỗi xảy ra, vui lòng thử lại.';
            }
        }


        return array(
            'status' => $status,
            'message' => $message
        );

    }

    /**
     * User Dang nhap
     *
     * @param $data
     * @return array
     */
    public function login($data)
    {
        $status = 'error';
        $creds = array(
            'user_login' => $data['email'],
            'user_password' => $data['password'],
            'remember' => false
        );

        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            $message = $user->get_error_message();

        } else {
            $status = 'success';
            $message = 'Đăng nhập thành công';
        }

        return array(
            'status' => $status,
            'message' => $message
        );
    }

    /**
     * Tạo user nicename
     *
     * @param $user_login
     * @return string
     */
    public function builtUserNicename($user_login)
    {
        $user_nicename = $user_login;
        $user_nicename_array = explode('@', $user_nicename);
        $user_nicename = $user_nicename_array[0];

        $query = "SELECT ID FROM " . $this->_wpdb->users . " WHERE user_nicename = %s AND user_login != %s LIMIT 1";
        $user_nicename_check = $this->_wpdb->get_var(
            $this->_wpdb->prepare($query, $user_nicename, $user_login)
        );
        if ($user_nicename_check) {
            $suffix = 2;
            $alt_user_nicename = '';
            while ($user_nicename_check) {
                $alt_user_nicename = $user_nicename . "-$suffix";
                $user_nicename_check = $this->_wpdb->get_var(
                    $this->_wpdb->prepare($query, $alt_user_nicename, $user_login)
                );
                $suffix++;
            }
            $user_nicename = $alt_user_nicename;
        }
        return sanitize_title($user_nicename);
    }

    /**
     * Lay thong tin user trong bang user info
     *
     * @param $user_id
     * @return array|bool|false|mixed|null|object|void
     */
    public function getUserInfo($user_id)
    {
        $avatar = AVATAR_DEFAULT;

        $cacheId = __CLASS__ . 'getUserInfo' . $user_id;
        $result = wp_cache_get($cacheId, CACHEGROUP);
        if (false === $result) {
            global $wpdb;

            if (!empty($user_id)) {
                //Nếu chưa có row trong table user_info
                if (empty($this->_wpdb->get_row("SELECT * FROM {$this->_table_info} WHERE user_id = {$user_id}"))) {
                    $this->_wpdb->insert($this->_table_info,
                        ['user_id' => $user_id, 'updated_at' => current_time('mysql')]);
                }
            }

            $query = $wpdb->prepare("SELECT *
                FROM " . $this->_wpdb->users . " u
                INNER JOIN $this->_table_info ui ON u.ID=ui.user_id
                WHERE u.ID = %d",
                $user_id);

            $result = $wpdb->get_row($query);

            if (!empty($result)) {
                if ($result->avatar) {
                    $avatar = $result->avatar;
                    if (strpos($avatar, 'facebook') !== false) {
                        $avatar = $avatar . "?type=normal";
                    }
                }
                $result->avatar = $avatar;

                $result->points = intval($result->points);

                $result->user_link = WP_SITEURL . '/u/' . $result->user_nicename . '/';
                // @todo calc message
                /* $objMessage = new Marry_Message();
                 $unread = $objMessage->check_unread($user_id);
                 $result->message = $unread;*/

                wp_cache_set($cacheId, $result, CACHEGROUP, CACHETIME);
            }
        }
        return $result;
    }

    /**
     * Lấy thông tin user
     *
     * @param $key (id | ID | slug | email | login)
     * @param $value
     * @return bool
     */
    public function getUserBy($key, $value)
    {
        $user = get_user_by($key, $value);
        if (!empty($user)) {
            $user = $this->getUserInfo($user->ID);
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Update user info
     *
     * @param $user_id
     * @param $data
     * @return bool|false|int
     */
    public function saveUserInfo($user_id, $data)
    {
        if ($user_id && !empty($data)) {
            $user_info = $this->getUserInfo($user_id);
            if (empty($user_info)) {
                $data['user_id'] = $user_id;
                $result = $this->_wpdb->insert(
                    $this->_table_info,
                    $data
                );
            } else {
                $result = $this->_wpdb->update(
                    $this->_table_info,
                    $data,
                    ['user_id' => $user_id]
                );
            }

            wp_cache_delete(__CLASS__ . 'getUserInfo' . $user_id);
            return $result;
        }
        return false;
    }

    /**
     * Create/ Update user & user info
     * @param $data
     * @param bool $is_login
     * @return bool
     */
    public function saveUser($data, $is_login = true)
    {
        if (!empty($data)) {
            $user_id = email_exists($data['email']);
            if (empty($user_id)) {
                //Create user
                wp_create_user($data["email"], $data["password"], $data["email"]);

                $user_id = $this->_wpdb->get_var('SELECT ID FROM ' . $this->_wpdb->users . ' WHERE user_login = "' . $data['email'] . '"');

                // Tạo user thất bại => return false
                if (empty($user_id)) {
                    return false;
                }
            }

            //cap nhap user nicename va display name
            $user_nicename = $this->builtUserNicename($data["email"]);
            wp_update_user(array(
                "ID" => $user_id,
                "user_nicename" => $user_nicename,
                "display_name" => $data['name']
            ));

            // tao user info cho member
            $user_info = array(
                'wedding_date' => $data['wedding_date'],
                'gender' => $data['gender'],
                'phone' => $data['phone'],
                'updated_at' => current_time('mysql')
            );
            if (!empty($data['city_id'])) {
                $user_info['city_id'] = $data['city_id'];
            }
            $this->saveUserInfo($user_id, $user_info);

            // add vao list sendy
            if (function_exists('sendy_action')) {
                sendy_action(array(
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'list' => SENDY_LIST
                ), 1);
            }

            // set login
            if ($is_login) {
                wp_set_auth_cookie($user_id);
            }

            return true;
        } else {
            return false;
        }
    }
}
