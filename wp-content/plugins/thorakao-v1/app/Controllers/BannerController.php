<?php
/**
 * Created by PhpStorm.
 * User: vietanh
 * Date: 01-Aug-16
 * Time: 11:37 PM
 */

namespace TVA\Controllers;

use TVA\Models\Banners;

class BannerController extends _BaseController
{
    private static $instance;


    protected function __construct()
    {
        parent::__construct();

        add_action("wp_ajax_trk_ajax_handler_banner", [$this, "ajaxHandler"]);
        add_action("wp_ajax_nopriv_trk_ajax_handler_banner", [$this, "ajaxHandler"]);
    }


    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new BannerController();
        }

        return self::$instance;
    }


    public function ajaxGetLatestBanner()
    {
        $model = Banners::init();
        $result = $model->getLatestBanner();
        $this->clearStaticCache();

        return $result;
    }

}