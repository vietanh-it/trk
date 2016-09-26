<?php
/**
 * Created by PhpStorm.
 * User: vietanh
 * Date: 01-Aug-16
 * Time: 11:37 PM
 */

namespace TVA\Models;

class Banners
{
    protected $_wpdb;
    protected $_tbl_banner_info;


    private static $instance;


    protected function __construct()
    {
        global $wpdb;
        $this->_wpdb = $wpdb;
        $this->_tbl_banner_info = $wpdb->prefix . 'banner_info';
    }


    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Banners();
        }

        return self::$instance;
    }


    public function getLatestBanner()
    {
        $query = "SELECT * FROM {$this->_wpdb->posts} p INNER JOIN {$this->_tbl_banner_info} bi ON p.ID = bi.object_id WHERE p.post_status='publish' AND p.post_type = 'banner' AND bi.start_date <= NOW() AND NOW() <= bi.end_date AND bi.is_enabled = 1 ORDER BY p.post_modified DESC";
        $result = $this->_wpdb->get_row($query);

        if ($result) {
            $result->filter = 'sample';
            $result->permalink = get_permalink($result);
            $thumb_id = get_post_thumbnail_id($result);
            $image = wp_get_attachment_image_src($thumb_id, 'original');
            $result->image = $image;
        }

        return $result;
    }


    public function getBannerInfo($post_id)
    {
        $query = "SELECT * FROM {$this->_tbl_banner_info} WHERE object_id = {$post_id}";
        $banner_info = $this->_wpdb->get_row($query);
        if (empty($banner_info)) {
            $banner_info = [
                'object_id'  => $post_id,
                'is_enabled' => 0
            ];
            $this->_wpdb->insert($this->_tbl_banner_info, $banner_info);
        }

        return $banner_info;
    }


    public function getBanner($post_id)
    {
        $query = "SELECT * FROM {$this->_wpdb->posts} p LEFT JOIN {$this->_tbl_banner_info} bi ON p.ID = bi.object_id WHERE p.ID = {$post_id}";
        $result = $this->_wpdb->get_row($query);

        return $result;
    }
}