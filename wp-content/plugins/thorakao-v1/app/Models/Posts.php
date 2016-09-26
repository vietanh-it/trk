<?php
/**
 * Created by PhpStorm.
 * User: Vo sy dao
 * Date: 3/21/2016
 * Time: 4:31 PM
 */
namespace TVA\Models;

use TVA\Controllers\ProductController;
use WeDevs\ORM\Eloquent\Database;

class Posts
{
    protected $wpdb;
    protected $tbl_post_info;
    protected $tbl_post_info_raw;
    protected $tbl_location_city;
    protected $tbl_location_district;
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

        $this->tbl_post_raw = "posts";
        $this->tbl_post = $wpdb->prefix . $this->tbl_post_raw;

        $this->tbl_post_info_raw = "post_info";
        $this->tbl_post_info = $wpdb->prefix . $this->tbl_post_info_raw;

        $this->tbl_term_relationships_raw = "term_relationships";
        $this->tbl_term_relationships = $wpdb->prefix . $this->tbl_term_relationships_raw;

        $this->tbl_location_city = $wpdb->prefix . 'location_city';
        $this->tbl_location_district = $wpdb->prefix . 'location_district';
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Posts();
        }

        return self::$instance;
    }


    /**
     * Lay bai viet nhap
     *
     * @param $user_id
     * @param $post_type
     * @return array|null|object|void|\WP_Post
     */
    public function getPostDraft($user_id, $post_type = 'post')
    {
        $query = "SELECT * FROM " . $this->wpdb->posts . " WHERE post_status='auto-draft' AND post_author={$user_id} AND post_type='{$post_type}'";
        $result = $this->wpdb->get_row($query);

        if (empty($result)) {
            $post_id = wp_insert_post(
                [
                    'post_title'  => 'Auto Draft',
                    'post_author' => $user_id,
                    'post_status' => 'auto-draft',
                    'post_type'   => $post_type
                ]
            );
            $result = get_post($post_id);
        }

        return $result;
    }


    public function savePostInfo($post_id, $data)
    {
        $post_info = $this->db->table($this->tbl_post_info_raw)->where('ID', $post_id)->first();

        if (empty($post_info)) {
            $data['ID'] = $post_id;
            $this->db->table($this->tbl_post_info_raw)->insert($data);
        } else {
            $this->db->table($this->tbl_post_info_raw)
                ->where('ID', $post_id)
                ->update($data);
        }

        return true;
    }


    /**
     * @param $post_id
     * @return object {{ID: int, post_author: string, post_date: string, post_content: string, post_title: string, post_excerpt: string, post_status: string, post_name: string, post_modified: string, post_parent: int, post_type: string, price: float, recipe: string, gross_weight: string, status: string, is_color: int}}
     */
    public function getPost($post_id)
    {
        $cache_id = __METHOD__ . $post_id;
        $result = wp_cache_get($cache_id);
        if (false === $result) {
            $result = $this->db->table($this->tbl_post_raw . ' AS p')
                ->join($this->tbl_post_info . ' AS pi', 'p.ID', '=', 'pi.ID')
                ->where('p.ID', $post_id)
                ->select([
                    'p.ID',
                    'p.post_author',
                    'p.post_date',
                    'p.post_content',
                    'p.post_title',
                    'p.post_excerpt',
                    'p.post_status',
                    'p.post_name',
                    'p.post_modified',
                    'p.post_parent',
                    'p.post_type',
                    'pi.price',
                    'pi.recipe',
                    'pi.gross_weight',
                    'pi.status',
                    'pi.images',
                    'pi.colors'
                ])
                ->first();

            if (!empty($result)) {
                //Get language
                $post_language = get_the_terms($result->ID, 'language');
                if ($post_language) {
                    $post_language = array_shift($post_language);
                }
                $result->language = valueOrNull($post_language->slug, pll_default_language());
                $result->translation_vi = pll_get_post($result->ID, 'vi');
                $result->translation_en = pll_get_post($result->ID, 'en');

                //vi post info
                $vi_post_info = $this->db->table('post_info')->where('ID', $result->translation_vi)->first();
                unset($vi_post_info->ID);
                $result = (object)array_merge((array)$result, (array)$vi_post_info);

                //Price display
                if ($result->post_type == 'product') {
                    $vi_currency = get_option('vi_currency');
                    $en_currency = get_option('en_currency');
                    if (!empty($result->price)) {
                        if ($result->language == 'vi') {
                            $result->price_display = number_format($result->price) . $vi_currency;
                        } else {
                            $currency_rate = get_option('currency_rate');
                            if (empty($currency_rate)) {
                                $currency_rate = '22367';
                                add_option('currency_rate', $currency_rate);
                            }
                            $result->price = $result->price / $currency_rate;
                            $result->price_display = $en_currency . number_format($result->price, 2);
                        }
                    } else {
                        $result->price_display = '';
                    }
                }

                //Images
                $thumbnail_id = get_post_thumbnail_id($result->ID);
                $result->featured_image = wp_get_attachment_image_src($thumbnail_id, 'featured-image', true)[0];
                $result->square_image = wp_get_attachment_image_src($thumbnail_id, 'square-image', true)[0];

                //Other Images
                if (!empty($result->other_images)) {
                    $result->other_images = unserialize($result->images);
                }

                //Colors
                if (!empty($result->colors)) {
                    $result->colors = unserialize($result->colors);
                }

                //Permalink
                $result->permalink = get_permalink($result->ID);

                //Terms
                $result->taxonomy_theo_loai = wp_get_post_terms($result->ID, 'theo-loai');
                $result->taxonomy_theo_cong_dung = wp_get_post_terms($result->ID, 'theo-cong-dung');

                //Recipes
                $product_ctrl = ProductController::init();
                $result->recipes = $product_ctrl->getProductRecipes($result->ID);
            }

            wp_cache_set($cache_id, $result);
        }

        return $result;
    }


    public function getList($params = [])
    {
        $default = [
            'post_type'        => 'beauty',
            'post_status'      => 'publish',
            'term_taxonomy_id' => 0,
            'limit'            => 12,
            'order_by'         => ' p.post_date DESC'
        ];
        $params = array_merge($default, $params);

        $select = "SELECT SQL_CALC_FOUND_ROWS p.*";
        $from = " FROM {$this->wpdb->posts} AS p";
        $where = " WHERE p.post_type IN ('" . $params['post_type'] . "') AND p.post_status IN ('" . $params['post_status'] . "')";
        $join = " INNER JOIN {$this->wpdb->prefix}term_relationships AS tr ON p.ID = tr.object_id";

        if (!empty($params['term_taxonomy_id'])) {
            // $join .= " INNER JOIN {$this->wpdb->prefix}term_taxonomy AS tt ON tr.term_taxonomy_id = tt.term_taxonomy_id";
            // $join .= " INNER JOIN {$this->wpdb->prefix}terms AS t ON tt.term_id = t.term_id";
            // $select .= ", tt.*, t.*";
            $where .= " AND tr.term_taxonomy_id = " . $params['term_taxonomy_id'];
        } else {
            $where .= " AND tr.term_taxonomy_id = " . (pll_current_language() == 'vi' ? 14 : 17);
        }

        $query = $select . $from . $join . $where;


        if (!empty($params['order_by'])) {
            $query .= " ORDER BY " . $params['order_by'];
        }


        if (!empty($params['is_paging'])) {
            //Paging
            $page = (empty($params['page'])) ? 1 : intval($params['page']);
            $limit = (empty($params['limit'])) ? 20 : intval($params['limit']);
            $to = ($page - 1) * $limit;
            $query .= " LIMIT $to, $limit";
        } elseif (!empty($params['limit'])) {
            $query .= " LIMIT " . $params['limit'];
        }

        $result = $this->wpdb->get_results($query);

        //Total result
        $total = $this->wpdb->get_row("SELECT FOUND_ROWS() as total");
        $total = intval($total->total);

        //WP Pagenavi
        if (!empty($params['is_paging'])) {
            $this->set_paging($result, $total, $params['limit'], $params['page']);
        }

        if ((is_array($params['post_type']) and in_array('product',
                    $params['post_type'])) or ($params['post_type'] == 'product')
        ) {
            $products = [];
            if (!empty($result)) {
                $post_ctrl = Posts::init();
                foreach ($result as $item) {
                    $item = $post_ctrl->getPost($item->ID);
                    $products[] = $item;
                }
            }

//        var_dump($products);
            return $products;
        } else {
            foreach ($result as $item) {
                //Image
                $thumbnail_id = get_post_thumbnail_id($item->ID);
                $item->thumbnail = wp_get_attachment_image_src($thumbnail_id, 'thumbnail', true)[0];
                $item->featured_image = wp_get_attachment_image_src($thumbnail_id, 'featured-image', true)[0];
                $item->square_image = wp_get_attachment_image_src($thumbnail_id, 'square-image', true)[0];
                // var_dump($item->thumbnail, $item->square_image);
                //Permalink
                $item->permalink = get_permalink($item);
            }

            return $result;
        }
    }


    public function getTermList($taxonomy, $is_lang = true)
    {
        $query = "SELECT * FROM {$this->wpdb->term_taxonomy} tt INNER JOIN {$this->wpdb->terms} t ON tt.term_id = t.term_id WHERE tt.taxonomy = '{$taxonomy}'";

        $result = $this->wpdb->get_results($query);

        if ($is_lang) {
            foreach ($result as $key => $term) {
                if (pll_get_term_language($term->term_id) != pll_current_language('slug')) {
                    unset($result[$key]);
                }
            }
        }

        return $result;
    }


    public function getCityList()
    {
        $query = "SELECT * FROM {$this->tbl_location_city}";
        $result = $this->wpdb->get_results($query);

        return $result;
    }


    public function getDistrictList($city_id)
    {
        $query = "SELECT * FROM {$this->tbl_location_district} WHERE city_id = {$city_id}";
        $result = $this->wpdb->get_results($query);

        return $result;
    }


    public function getDistrictInfo($district_id)
    {
        $query = "SELECT * FROM {$this->tbl_location_district} WHERE id = {$district_id}";
        $result = $this->wpdb->get_row($query);

        return $result;
    }


    public function set_paging($data, $total, $limit, $page)
    {
        global $wp_query;
        $wp_query->posts = $data;
        $wp_query->is_paged = ($page >= 1) ? true : false;
        $wp_query->found_posts = $total;
        $wp_query->max_num_pages = ceil($total / $limit);
    }


    public function saveContactForm($data)
    {
        wp_insert_post([
            'post_author'  => get_user_by('login', 'contact_form'),
            'post_content' => $data['note'],
            'post_title'   => 'Yêu cầu liên hệ từ ' . $data['name'] . ' - ' . $data['phone'],
            'post_type'    => 'contact-form',
            'post_status'  => 'publish'
        ]);

        $this->wpdb->insert($this->wpdb->prefix . 'post_info', [
            'ID'            => $this->wpdb->insert_id,
            'contact_name'  => $data['name'],
            'contact_email' => $data['email'],
            'contact_phone' => $data['phone']
        ]);

        $result = [
            'status' => 'success',
            'data'   => true
        ];

        return $result;
    }
}