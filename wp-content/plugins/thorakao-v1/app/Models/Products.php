<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 05/23/2016
 * Time: 11:57 PM
 */
namespace TVA\Models;

use WeDevs\ORM\Eloquent\Database;

class Products
{
    protected $_wpdb;
    protected $_table_post_info;
    private $db;

    private static $instance;

    /**
     * Users constructor.
     */
    function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->db = Database::instance();

        $this->_table_post_info = $wpdb->prefix . "post_info";
    }


    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Products();
        }

        return self::$instance;
    }

    /**
     * Lấy danh sách sản phẩm
     *
     * @param $params
     * @return mixed
     */
    public function getProductList($params = [])
    {
        $default = [
            'post_status'          => 'publish',
            'term_taxonomy_id'     => 0,
            'limit'                => 12,
            'order_by'             => ' p.post_date DESC',
            'page'                 => 1,
            'is_homepage_featured' => false,
            'is_favourite'         => false
        ];
        $params = array_merge($default, $params);

        $select = "SELECT SQL_CALC_FOUND_ROWS p.*";
        $from = " FROM {$this->wpdb->posts} AS p";
        $where = " WHERE p.post_type = 'product' AND p.post_status = 'publish'";

        $join = " INNER JOIN {$this->wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id";
        $join .= " INNER JOIN {$this->_table_post_info} pi ON p.ID = pi.ID";

        if (!empty($params['term_taxonomy_id'])) {
            $select .= ", tr.*";
            $where .= " AND tr.term_taxonomy_id = " . $params['term_taxonomy_id'];
        } else {
            //Language
            $current_lang = pll_current_language('slug');
            if ($current_lang == 'vi') {
                $where .= " AND tr.term_taxonomy_id = 14";
            } else {
                $where .= " AND tr.term_taxonomy_id = 17";
            }
        }

        if ($params['is_homepage_featured']) {
            $order_by = "homepage_featured_order DESC";
            if ($params['order_by']) {
                $order_by .= ", " . $params['order_by'];
            }
            $params['order_by'] = $order_by;
        }

        if ($params['is_favourite']) {
            $order_by = "favourite_order DESC";
            if ($params['order_by']) {
                $order_by .= ", " . $params['order_by'];
            }
            $params['order_by'] = $order_by;
        }

        $query = $select . $from . $join . $where;
        // var_dump($query);

        //Order by
        if (!empty($params['order_by'])) {
            $query .= " ORDER BY " . $params['order_by'];
        }

        //Paging
        $page = (empty($params['page'])) ? 1 : intval($params['page']);
        $limit = (empty($params['limit'])) ? 20 : intval($params['limit']);
        $to = ($page - 1) * $limit;
        $query .= " LIMIT $to, $limit";

        $result = $this->wpdb->get_results($query);

        //Total result
        $total = $this->wpdb->get_row("SELECT FOUND_ROWS() as total");
        $total = intval($total->total);

        //WP Pagenavi
        if (!empty($params['is_paging'])) {
            $this->set_paging($result, $total, $params['limit'], $params['page']);
        }

        $products = [];
        if (!empty($result)) {
            $post_ctrl = Posts::init();
            foreach ($result as $item) {
                $item = $post_ctrl->getPost($item->ID);
                $products[] = $item;
            }
        }

        return $products;
    }


    public function getProductRecipes($product_id)
    {
        $query = "SELECT p.*, pr.* FROM {$this->wpdb->prefix}post_related pr INNER JOIN {$this->wpdb->posts} p ON pr.thanhphan_id = p.ID WHERE product_id = {$product_id}";

        $result = $this->wpdb->get_results($query);
        if ($result) {
            foreach ($result as $key => $item) {
                if (has_post_thumbnail($item)) {
                    $item->featured_image = get_the_post_thumbnail_url($item, 'featured-image');
                }
                $item->permalink = get_permalink($item);
            }
        }

        return $result;
    }


    public function getRecipeProducts($recipe_id)
    {
        $recipe_id = pll_get_post_translations($recipe_id)['vi'];

        $query = "SELECT * FROM {$this->wpdb->prefix}post_related WHERE thanhphan_id = {$recipe_id}";
        $result = $this->wpdb->get_results($query);

        if (!empty($result)) {
            $post_ctrl = Posts::init();
            $products = [];
            foreach ($result as $key => $item) {
                if (pll_current_language() == 'en') {
                    $en_id = pll_get_post_translations($item->product_id)['en'];
                    $item = get_post($en_id);
                    if (!empty($item)) {
                        $item->product_id = $item->ID;
                    } else {
                        break;
                    }
                }
                $product = $post_ctrl->getPost($item->product_id);
                $products[] = $product;
            }
            $result = $products;
        }

        return $result;
    }


    public function set_paging($data, $total, $limit, $page)
    {
        global $wp_query;
        $wp_query->set('posts_per_page', $limit);
        $wp_query->posts = $data;
        $wp_query->is_paged = ($page >= 1) ? true : false;
        $wp_query->found_posts = $total;
        $wp_query->max_num_pages = ceil($total / $limit);
    }
}
