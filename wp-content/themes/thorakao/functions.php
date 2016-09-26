<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 17/05/2016
 * Time: 10:35 SA
 */

define('THEME_URL', get_template_directory_uri());

if (!isset($content_width)) {
    $content_width = 490;
}


add_action('after_setup_theme', 'themeSetup');
function themeSetup()
{
    add_editor_style('css/editor-style.css');


    add_theme_support('post-formats', [
        'image',
        'gallery',
    ]);
    add_theme_support('post-thumbnails');

    add_image_size('avatar', 100, 100, true); // 1x1
    add_image_size('featured-image', 452, 452, true); // 4x3
    add_image_size('square-image', 300, 300, true); // 4x3

    add_theme_support("title-tag");
}


add_action('wp_enqueue_scripts', 'setupScriptsStyles');
function setupScriptsStyles()
{
    $version = '20160704_1058';

    if (!is_admin()) {
        // comment out the next two lines to load the local copy of jQuery
        // wp_deregister_script('jquery');
        // wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js', false, '1.8.1');
        // wp_enqueue_script('jquery');
    }

    // Styles
    wp_enqueue_style('thorakao-original-style', THEME_URL . '/style.css', [], $version);
    // wp_enqueue_style('thorakao-plugin', THEME_URL . '/css/plugin.css', [], $version);
    wp_enqueue_style('thorakao-style', THEME_URL . '/css/main.css', [], $version);
    wp_enqueue_style('thorakao-style-ext', THEME_URL . '/css/style-ext.css', [], $version);


    // Scripts
    // wp_enqueue_script('thorakao-plugins', THEME_URL . '/js/plugins.js', ['jquery'], $version, true);
    wp_enqueue_script('thorakao-scripts', THEME_URL . '/js/main-source.js', ['jquery'], $version, true);
    // wp_enqueue_script('jquery-scrollstop', THEME_URL . '/js/jquery.scrollstop.js', ['jquery'], $version, true);
    // wp_enqueue_script('jquery-lazyload', THEME_URL . '/js/jquery.lazyload.js', ['jquery'], $version, true);
    wp_enqueue_script('thorakao-scripts-ext', THEME_URL . '/js/app-ext.js', ['jquery'], $version, true);
}

add_action('init', 'myStartSession', 1);
function myStartSession()
{
    if (!session_id()) {
        session_start();
        if (empty($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

}