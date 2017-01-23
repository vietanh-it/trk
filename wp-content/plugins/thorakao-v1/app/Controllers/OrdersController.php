<?php
namespace TVA\Controllers;

use TVA\Models\GHN;
use TVA\Models\Location;
use TVA\Models\Orders;

class OrdersController extends _BaseController
{
    private static $instance;
    private        $wpdb;
    private        $tbl_order_info;
    private        $tbl_order_detail;

    protected function __construct()
    {
        parent::__construct();

        global $wpdb;

        $this->wpdb = $wpdb;
        $this->prefix = $wpdb->prefix;
        $this->tbl_order_info = $wpdb->prefix . 'order_info';
        $this->tbl_order_detail = $wpdb->prefix . 'order_detail';

        add_action("wp_ajax_trk_ajax_handler_order", [
            $this,
            "ajaxHandler"
        ]);
        add_action("wp_ajax_nopriv_trk_ajax_handler_order", [
            $this,
            "ajaxHandler"
        ]);

        // add_action('init', [$this, 'register_post_status'], 1);
    }

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new OrdersController();
        }

        return self::$instance;
    }


    public function ajaxChangeQuantity()
    {
        /**
         * $_POST['product_id']
         * $_POST['quantity']
         * $_POST['district_id']
         */

        if (empty($_POST['product_id']) || empty($_POST['quantity'])) {
            $result = [
                'status' => 'fail',
                'data'   => 'Đã xảy ra lỗi, vui lòng thử lại.'
            ];
        }
        else {
            $m_order = Orders::init();
            $result = $m_order->saveCart($_POST['product_id'], $_POST['quantity'], false);
        }

        return $result;
    }


    public function ajaxSaveOrder($data)
    {
        $result = [
            'status'  => 'error',
            'message' => 'Đã xảy ra lỗi, vui lòng thử lại.'
        ];

        if (empty($_SESSION['subtotal'])) {
            return $result;
        }

        if (!empty($_SESSION['cart'])) {
            $this->validate->rule('required', [
                'name',
                'phone',
                'email',
                'address',
                'payment_method',
                'city_id'
            ])->message('Vui lòng nhập {field}')->labels([
                'name'           => 'họ tên',
                'phone'          => 'số điện thoại',
                'email'          => 'email',
                'address'        => 'địa chỉ',
                'payment_method' => 'phương thức thanh toán',
                'city_id'        => 'tỉnh thành'
            ]);

            if ($this->validate->validate()) {
                $m_order = Orders::init();
                $result = $m_order->saveOrder($data);

            }
            else {
                $result = [
                    'status'  => 'error',
                    'message' => 'Đã xảy ra lỗi, vui lòng thử lại'
                ];
            }
        }

        return $result;
    }


    public function getOrderInfo($order_id)
    {
        $data['ID'] = $order_id;

        $model = Orders::init();
        $result = $model->getOrderInfo($data);

        return $result;
    }


    public function calculateShippingFee($city_id = 0, $district_id = 0, $subtotal = 0)
    {
        $subtotal = valueOrNull($subtotal, $_SESSION['subtotal']);

        if (empty($city_id)) {
            $result = 0;
        }
        else {
            if ($city_id == 1) {

                // TP HCM
                if ($subtotal >= 200000) {
                    $shipping_fee = 0;
                }
                elseif ($subtotal >= 101000) {
                    $district_type = $this->getDistrictType($district_id);
                    if ($district_type == 'ngoai-thanh-1') {
                        $shipping_fee = 30000;
                    }
                    elseif ($district_type == 'ngoai-thanh-2') {
                        $shipping_fee = 40000;
                    }
                    elseif ($district_type == 'noi-thanh') {
                        $shipping_fee = 20000;
                    }
                    else {
                        $shipping_fee = 40000;
                    }
                }
                else {
                    $district_type = $this->getDistrictType($district_id);
                    if ($district_type == 'ngoai-thanh-1') {
                        $shipping_fee = 25000;
                    }
                    elseif ($district_type == 'ngoai-thanh-2') {
                        $shipping_fee = 35000;
                    }
                    elseif ($district_type == 'noi-thanh') {
                        $shipping_fee = 15000;
                    }
                    else {
                        $shipping_fee = 35000;
                    }
                }
            }
            else {

                $location_model = Location::init();
                $city = $location_model->getCityBy('id', $city_id);

                // City khác
                if ($subtotal >= 500000) {
                    $shipping_fee = 0;
                }
                elseif ($subtotal >= 251000) {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 45000;
                    }
                    elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 50000;
                    }
                    elseif ($city->type == 'khac') {
                        $shipping_fee = 60000;
                    }
                    else {
                        $shipping_fee = 60000;
                    }
                }
                elseif ($subtotal >= 101000) {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 40000;
                    }
                    elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 45000;
                    }
                    elseif ($city->type == 'khac') {
                        $shipping_fee = 55000;
                    }
                    else {
                        $shipping_fee = 55000;
                    }
                }
                else {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 30000;
                    }
                    elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 35000;
                    }
                    elseif ($city->type == 'khac') {
                        $shipping_fee = 45000;
                    }
                    else {
                        $shipping_fee = 45000;
                    }
                }
            }

            $result = $shipping_fee;
            $_SESSION['shipping_fee'] = $shipping_fee;
        }

        return $result;
    }


    public function getDistrictType($district_id)
    {
        $location_model = Location::init();
        $district = $location_model->getDistrictBy('id', $district_id);

        return valueOrNull($district->type, '');
    }


    public function ajaxProcessOrder($data)
    {
        $this->wpdb->update($this->wpdb->prefix . 'order_info', [
            'status' => 'processing'
        ], ['ID' => $data['order_id']]);

        $this->wpdb->update($this->wpdb->posts, [
            'post_modified' => current_time('mysql')
        ], [
            'ID' => $data['order_id']
        ]);


        // Create order ghn
        $order_info = $this->getOrderInfo($data['order_id']);

        $ghn = GHN::init();
        $shipping_args = [
            'RecipientName'   => $order_info->name,
            'RecipientPhone'  => $order_info->phone,
            'DeliveryAddress' => $order_info->address,
            'DeliveryDistrictCode',
            'ContentNote',
            'ServiceID'
        ];
        $shipping_order = $ghn->createShippingOrder();

        wp_redirect(WP_SITEURL . '/wp-admin/edit.php?post_type=shop_order');
        exit();
    }


    public function ajaxCompleteOrder($data)
    {
        $this->wpdb->update($this->wpdb->prefix . 'order_info', [
            'status' => 'completed'
        ], ['ID' => $data['order_id']]);

        $this->wpdb->update($this->wpdb->posts, [
            'post_modified' => current_time('mysql')
        ], [
            'ID' => $data['order_id']
        ]);

        wp_publish_post($data['order_id']);

        wp_redirect(WP_SITEURL . '/wp-admin/edit.php?post_type=shop_order');
        exit();
    }


    public function ajaxCreateShippingOrder($data)
    {
        $result = [
            'status'  => 'error',
            'message' => 'Đã xảy ra lỗi, vui lòng thử lại.'
        ];

        if (empty($data['order_id'])) {
            return $result;
        }

        $m_order = Orders::init();
        $rs = $m_order->createShippingOrder($data);

        wp_redirect(WP_SITEURL . '/wp-admin/edit.php?post_type=shop_order');
        exit();
    }


    public function ajaxResendEmailOrder($data)
    {
        $model = Orders::init();
        $location_model = Location::init();

        $order_info = $this->getOrderInfo($data['order_id']);
        $city = $location_model->getCityBy('id', $order_info->city_id);
        $district = $location_model->getDistrictBy('id', $order_info->district_id);

        $order_detail = $model->getOrderDetail($data['order_id']);
        $html_order_detail = '';
        foreach ($order_detail as $key => $item) {
            $html_order_detail .= '<tr><td width="10%" style="background:#f2f2f2; border-bottom:1px solid #fff; text-align:left; padding:2%;"> ' . ($key + 1) . ' </td><td width="40%" style="background:#f2f2f2; border-bottom:1px solid #fff; text-align:left; padding:2%;" colspan="2">' . $item->product_name . '</td><td width="10%" style="background:#f2f2f2; border-bottom:1px solid #fff; text-align:left; padding:2%;">' . $item->quantity . '</td><td width="20%" style="background:#f2f2f2; border-bottom:1px solid #fff; text-align:left; padding:2%;">' . number_format($item->price) . ' đ</td><td width="20%" style="background:#f2f2f2; border-bottom:1px solid #fff; text-align:left; padding:2%;">' . number_format($item->subtotal) . ' đ</td></tr>';
        }

        $template = PATH_VIEW . 'email_template/order.html';

        $html = file_get_contents($template);

        $args_search = [];
        $args_replace = [];

        // replace image path
        $args_string = [
            '[%order_id%]'          => $order_info->code,
            '[%site_url%]'          => WP_SITEURL,
            '[%customer_name%]'     => $order_info->name,
            '[%customer_email%]'    => $order_info->email,
            '[%customer_phone%]'    => $order_info->phone,
            '[%customer_address%]'  => $order_info->address . ', ' . $district->name . ', ' . $city->name,
            '[%order_note%]'        => $order_info->note,
            '[%order_subtotal%]'    => number_format($order_info->subtotal) . ' đ',
            '[%shipping_fee%]'      => number_format($order_info->shipping_fee) . ' đ',
            '[%order_total%]'       => number_format($order_info->total) . ' đ',
            '[%order_detail_list%]' => $html_order_detail
        ];

        foreach ($args_string as $key => $value) {
            $args_search[] = $key;
            $args_replace[] = $value;
        }

        $content = str_replace($args_search, $args_replace, $html);

        $subject = 'Gửi lại Email Đơn Hàng từ ' . $order_info->name . ', mã đơn hàng: ' . $order_info->code;

        if (WP_DEBUG) {
            wp_mail('vietanhtran.it@gmail.com', $subject, $content, 'Content-type: text/html');
        }
        else {
            wp_mail('thorakaoshop@thorakaovn.com', $subject, $content, 'Content-type: text/html');
        }

        wp_redirect(WP_SITEURL . '/wp-admin/edit.php?post_type=shop_order');
        exit();
    }


    //region new version


    /**
     * Lấy danh sách service
     *
     * @param $data
     * @return array
     */
    public function ajaxGetShippingServiceList($data)
    {
        $ghn = GHN::init();
        $rs = $ghn->getServiceList($data['district_id']);

        if (!empty($rs)) {
            $result = [
                'status' => 'success',
                'data'   => $rs
            ];
        }
        else {
            $result = [
                'status' => 'fail'
            ];
        }

        return $result;
    }


    /**
     * Lấy giá ship
     *
     * @param $data
     * @return array
     */
    public function ajaxGetShippingFee($data)
    {
        $m_order = Orders::init();
        $fee = $m_order->calculateShippingFee($data['to_district_code'], $data['service_id'], $_SESSION['total_weight']);

        if (is_numeric($fee) && $fee >= 0) {
            return [
                'status' => 'success',
                'data'   => $fee
            ];
        }
        else {
            return [
                'status' => 'fail',
                'data'   => 'Hình thức giao hàng này không được hỗ trợ.'
            ];
        }
    }


    /**
     * Thêm vào giỏ hàng
     *
     * @return array|void
     */
    public function ajaxAddToCart()
    {
        /**
         * $_POST['product_id']
         * $_POST['quantity']
         * $_POST['is_plus']
         */

        if (empty($_POST['product_id']) || empty($_POST['quantity'])) {
            $result = [
                'status' => 'fail',
                'data'   => 'Đã xảy ra lỗi, vui lòng thử lại.'
            ];
        }
        else {
            $m_order = Orders::init();
            $result = $m_order->saveCart($_POST['product_id'], $_POST['quantity']);
        }

        return $result;
    }


    /**
     * Xóa sản phẩm trong giỏ hàng
     *
     * @return array
     */
    public function ajaxDeleteProductCart()
    {
        $result = [
            'status'  => 'error',
            'message' => 'Đã xảy ra lỗi, vui lòng thử lại',
        ];
        if (!empty($_POST['product_id'])) {
            $m_order = Orders::init();

            $result = $m_order->deleteCartItem($_POST['product_id']);
        }

        return $result;
    }

    //endregion
}
