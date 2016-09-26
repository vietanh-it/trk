<?php
/*
Plugin Name: Thorakao v1
Plugin URI: http://www.vietanh.photography
Description: Thorakao v1
Author: Viet Anh <vietanhtran.it@gmail.com>
Version: 1.0
Author URI: http://www.vietanh.photography
*/

define("PATH_VIEW", __DIR__ . '/app/Views/');

if (!defined('CACHEGROUP')) {
    define('CACHEGROUP', 'default');
}
if (!defined('CACHETIME')) {
    define('CACHETIME', '3600');
}
if (!defined('VI_COMBO_TT_ID')) {
    define('VI_COMBO_TT_ID', 5);
}

add_action('plugins_loaded', 'trk_load', 500, 1);

function trk_load()
{
    require_once __DIR__ . '/vendor/autoload.php';

    //call hooks
    TVA\Hooks\BackendUI::init();
    \TVA\Hooks\Rewrite::init();
    // \TVA\Hooks\MenuSettings::init();

    \TVA\Controllers\OrdersController::init();
    \TVA\Controllers\PostController::init();
    \TVA\Controllers\AccountController::init();
    \TVA\Controllers\BannerController::init();
}