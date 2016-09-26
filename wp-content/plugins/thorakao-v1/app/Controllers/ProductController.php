<?php
namespace TVA\Controllers;

use TVA\Models\Posts;
use TVA\Models\Products;

class ProductController extends _BaseController
{
    private static $instance;

    protected function __construct()
    {
        parent::__construct();

        add_action("wp_ajax_trk_ajax_handler_product", [$this, "ajaxHandler"]);
        add_action("wp_ajax_nopriv_trk_ajax_handler_product", [$this, "ajaxHandler"]);
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new ProductController();
        }

        return self::$instance;
    }

    public function getProductList($params = [])
    {
        $model = Products::init();
        $result = $model->getProductList($params);

        return $result;
    }

    public function getProductRecipes($product_id)
    {
        $model = Products::init();
        $result = $model->getProductRecipes($product_id);

        return $result;
    }

    public function getRecipeProducts($product_id)
    {
        $model = Products::init();
        $result = $model->getRecipeProducts($product_id);

        return $result;
    }

    public function getProductInfo($product_id)
    {
        $model = Posts::init();
        $result = $model->getPost($product_id);

        return $result;
    }
}
