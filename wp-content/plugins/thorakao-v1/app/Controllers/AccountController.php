<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 08/06/2016
 * Time: 11:55 CH
 */

namespace TVA\Controllers;

use TVA\Models\Posts;

class AccountController extends _BaseController
{
    private static $instance;
    private $wpdb;
    private $tbl_order_info;
    private $tbl_order_detail;

    protected function __construct()
    {
        parent::__construct();

        global $wpdb;

        $this->wpdb = $wpdb;
        $this->prefix = $wpdb->prefix;
        $this->tbl_user_info = $wpdb->prefix . 'user_info';

        add_action("wp_ajax_trk_ajax_handler_account", [$this, "ajaxHandler"]);
        add_action("wp_ajax_nopriv_trk_ajax_handler_account", [$this, "ajaxHandler"]);
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new AccountController();
        }

        return self::$instance;
    }

    public function ajaxContactForm($data)
    {
        $model = Posts::init();
        $result = $model->saveContactForm($data);

        return $result;
    }

}
