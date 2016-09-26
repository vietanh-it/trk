<?php

namespace TVA\Hooks;

use TVA\Controllers\ProductController;

class custom_orders
{
    private static $instance;
    public $_table_orders_products;

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new custom_orders();
        }

        return self::$instance;
    }


    public function __construct(){
        global $wpdb;
        $this->_table_orders_products = $wpdb->prefix . "order_detail";
        add_action( 'add_meta_boxes', array($this,'order_info') );
    }

    public function order_info() {
        add_meta_box('order_info', 'Order info', array($this,'show'), 'shop_order', 'normal', 'high');
    }

    public function show() {
        global $wpdb,$post;
        echo '<h3>Danh sách sản phẩm</h3>';

        $query_pr = 'SELECT * FROM '.$this->_table_orders_products.' WHERE order_id = '.$post->ID;
        $list_order_product = $wpdb->get_results($query_pr);

        $productCtr = ProductController::init();
        if($list_order_product){
            foreach ($list_order_product as $v){
                $product_info = $productCtr->getProductInfo($v->product_id);
                ?>
                <style>
                    .box-products{
                        margin-bottom: 30px;
                    }
                    .box-products .images{
                        float: left;
                        width: 30%;
                    }
                    .box-products .images img{
                        max-width: 200px;
                    }

                </style>
                <div class="box-products">
                    <div class="images">
                        <a href="<?php echo $product_info->permalink  ?>" title="<?php echo $product_info->post_title  ?>" target="_blank">
                            <img src="<?php echo $product_info->featured_image  ?>" alt="<?php echo $product_info->post_title  ?>">
                        </a>
                    </div>
                    <div class="desc">
                        <a href="<?php echo $product_info->permalink  ?>" title="<?php echo $product_info->post_title  ?>" target="_blank">
                            <?php echo $product_info->post_title  ?>
                        </a>
                        <p>Quantity : <b><?php echo $v->quantity ?></b></p>
                        <p>Price : <b><?php echo intval($product_info->price) * intval($v->quantity )?></b> đ</p>
                    </div>
                </div>
                <div class="clearfix"></div>
                <?php
            }

            echo '<h3 style="margin-top: 40px;border-top: 1px solid darkgrey;padding-top: 30px "> Customer info : </h3>';

            $customer_id = get_post_meta($post->ID,'order_customer_id',true);

            $customer_name = get_post_meta($customer_id,'customer_name',true);
            $customer_email = get_post_meta($customer_id,'customer_email',true);
            $customer_phone = get_post_meta($customer_id,'customer_phone',true);
            $customer_address = get_post_meta($customer_id,'customer_address',true);
            $note = get_post_meta($post->ID,'note',true);

            echo '<p>Full name : 	&nbsp;&nbsp;&nbsp; '. $customer_name .'</p>';
            echo '<p>Phone : &nbsp;&nbsp;&nbsp;'. $customer_phone .'</p>';
            echo '<p>Email : &nbsp;&nbsp;&nbsp;'. $customer_email .'</p>';
            echo '<p>Address : &nbsp;&nbsp;&nbsp;'. $customer_address .'</p>';
            echo '<p>Ghi chú : &nbsp;&nbsp;&nbsp;'.$note.'</p>';
        }


    }
}
//$c = custom_orders::init();
