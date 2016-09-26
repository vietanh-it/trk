<?php
namespace TVA\Models;

class Orders
{
    protected $wpdb;
    protected $tbl_order_info;
    protected $tbl_order_detail;

    private static $instance;

    /**
     * Location constructor.
     *
     */
    protected function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;

        $this->tbl_order_info = $this->wpdb->prefix . 'order_info';
        $this->tbl_order_detail = $this->wpdb->prefix . 'order_detail';
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Orders();
        }

        return self::$instance;
    }

    public function getOrderInfo($data)
    {
        $query = "SELECT p.post_status, oi.* FROM {$this->wpdb->posts} p INNER JOIN {$this->tbl_order_info} oi ON p.ID = oi.ID WHERE p.ID = {$data['ID']}";
        $result = $this->wpdb->get_row($query);

        return $result;
    }

    public function getOrderDetail($order_id)
    {
        $query = "SELECT od.order_id, od.product_id, od.quantity, p.post_title AS product_name, pi.price AS price, (pi.price * od.quantity) as subtotal FROM {$this->tbl_order_detail} od INNER JOIN {$this->wpdb->posts} p ON od.product_id = p.ID INNER JOIN {$this->wpdb->prefix}post_info pi ON p.ID = pi.ID WHERE od.order_id = {$order_id}";

        return $this->wpdb->get_results($query);
    }


    public function getCart()
    {
        $session_id = session_id();
        $query = "SELECT * FROM {$this->wpdb->prefix}cart WHERE session_id = '{$session_id}'";
        $cart = $this->wpdb->get_row($query);

        if (empty($cart)) {

            // Initialize empty array
            $cart = [
                'session_id'  => $session_id,
                'name'        => '',
                'phone'       => '',
                'email'       => '',
                'city_id'     => '',
                'district_id' => '',
                'address'     => '',
                'note'        => ''
            ];

        }

        return $cart;
    }

}