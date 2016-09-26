<?php
namespace TVA\Controllers;

use TVA\Models\Posts;

class PostController extends _BaseController
{
    private static $instance;

    protected function __construct()
    {
        parent::__construct();

        add_action("wp_ajax_trk_ajax_handler_post", [$this, "ajaxHandler"]);
        add_action("wp_ajax_nopriv_trk_ajax_handler_post", [$this, "ajaxHandler"]);
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new PostController();
        }

        return self::$instance;
    }

    public function savePostInfo($post_id, $data)
    {
        $model = Posts::init();
        return $model->savePostInfo($post_id, $data);
    }

    public function getPost($post_id)
    {
        $model = Posts::init();
        return $model->getPost($post_id);
    }

    public function getList($params = [])
    {
        $model = Posts::init();
        return $model->getList($params);
    }

    public function getTermList($params = [])
    {
        $model = Posts::init();
        return $model->getTermList($params);
    }

    public function getCityList()
    {
        $model = Posts::init();
        return $model->getCityList();
    }

    public function getDistrictList($city_id)
    {
        $model = Posts::init();
        return $model->getDistrictList($city_id);
    }

    public function ajaxGetDistrictList($data)
    {
        $model = Posts::init();
        $result = $model->getDistrictList($data['city_id']);

        return [
            'status' => 'success',
            'data'   => $result
        ];
    }

    public function ajaxGetDistrictInfo($district_id)
    {
        $model = Posts::init();
        return $model->getDistrictInfo($district_id);
    }
}
