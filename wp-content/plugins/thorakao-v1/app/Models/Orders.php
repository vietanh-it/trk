<?php
namespace TVA\Models;

use TVA\Controllers\OrdersController;
use TVA\Controllers\ProductController;

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

    //region new


    /**
     * Thêm vào giỏ hàng
     *
     * @param $product_id
     * @param $quantity
     * @param bool $is_plus
     * @return array
     */
    public function saveCart($product_id, $quantity, $is_plus = true)
    {
        $m_post = Posts::init();
        $product_info = $m_post->getPost($product_id);

        //Chưa hỗ trợ tiếng anh
        if ($product_info->language != 'vi') {
            $result = [
                'status' => 'fail',
                'data'   => 'Thorakao chưa hỗ trợ đặt hàng trên ngôn ngữ tiếng anh, vui lòng quay lại <a href="' . WP_SITEURL . '" style="color: #88b04b;">phiên bản tiếng việt</a>.'
            ];
        } else {
            // Nếu đã có product trong giỏ hàng
            if (isset($_SESSION['cart'][$product_id])) {

                // Số lượng hiện tại
                $current_quantity = $_SESSION['cart'][$product_id]['quantity'];

                // Tính quantity
                if ($is_plus) {
                    $quantity = intval($current_quantity) + $quantity;
                }
            }
            // Thông tin insert vào session
            $p_session_info = [
                'product_id' => $product_id,
                'quantity'   => $quantity,
                'weight'     => $quantity * $product_info->gross_weight,
                'price'      => $product_info->final_price
            ];

            // Update cart product
            $_SESSION['cart'][$product_id] = $p_session_info;

            $result = [
                'status' => 'success',
                'data'   => $this->getCartInfo()
            ];

        }

        return $result;
    }


    public function saveOrder($data)
    {
        $this->wpdb->insert($this->wpdb->posts, [
            'post_title'    => 'Đơn hàng từ - ' . $data['name'],
            'post_type'     => 'shop_order',
            'post_status'   => 'pending',
            'post_date'     => current_time('mysql'),
            'post_modified' => current_time('mysql')
        ]);

        // TODO: generate order_id (TRK . [random_number])

        $id = $this->wpdb->insert_id;
        $data['order_id'] = 'TRK' . $id;

        //Update tên đơn hàng
        $this->wpdb->update($this->wpdb->posts,
            [
                'post_title'    => 'Mã đơn hàng: TRK' . $id . ' - ' . $data['name'],
                'post_modified' => current_time('mysql')
            ],
            ['ID' => $id]);


        //Order info
        $subtotal = $_SESSION['subtotal'];
        $shipping_fee = $this->calculateShippingFee($data['district_id'], $data['service_id'],
            $_SESSION['total_weight']);
        $total = $subtotal + $shipping_fee;

        $this->wpdb->insert($this->tbl_order_info, [
            'ID'             => $id,
            'code'           => 'TRK' . $id,
            'name'           => $data['name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'city_id'        => $data['city_id'],
            'district_id'    => $data['district_id'],
            'address'        => $data['address'],
            'subtotal'       => $subtotal,
            'total'          => $total,
            'shipping_fee'   => $shipping_fee,
            'payment_method' => $data['payment_method'],
            'note'           => $data['note'],
            'status'         => 'pending',
            'created_at'     => current_time('mysql')
        ]);

        //Order detail
        foreach ($_SESSION['cart'] as $key => $item) {
            $this->wpdb->insert($this->tbl_order_detail, [
                'order_id'   => $id,
                'product_id' => $key,
                'quantity'   => $item['quantity'],
                'price'      => $item['price'],
                'subtotal'   => $item['quantity'] * $item['price']
            ]);
        }

        session_destroy();

        $data['url'] = pll_current_language() == 'vi' ? WP_SITEURL : WP_SITEURL . '/en/';

        // Send email
        $this->sendEmailOrder($id);

        return [
            'status' => 'success',
            'data'   => $data
        ];
    }


    public function sendEmailOrder($order_id)
    {
        $model = Orders::init();
        $location_model = Location::init();

        $order_info = $this->getOrderInfo(['ID' => $order_id]);
        $city = $location_model->getCityBy('ProvinceCode', $order_info->city_id);
        $district = $location_model->getDistrictBy('DistrictCode', $order_info->district_id);

        $order_detail = $model->getOrderDetail($order_id);
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
            '[%customer_address%]'  => $order_info->address . ', ' . $district->DistrictName . ', ' . $city->ProvinceName,
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

        $subject = 'Chào ' . $order_info->name . ', chúc mừng bạn đã đặt hàng thành công.';

        if (WP_DEBUG) {
            return wp_mail('vietanhtran.it@gmail.com', $subject, $content, 'Content-type: text/html');
        } else {
            wp_mail('thorakaoshop@thorakaovn.com', $subject, $content, 'Content-type: text/html');
            //wp_mail('vietanhtran.it@gmail.com', $subject, $content, 'Content-type: text/html');
            return wp_mail($order_info->email, $subject, $content, 'Content-type: text/html');
        }
    }


    /**
     * Xóa sản phẩm trong giỏ hàng
     *
     * @param $product_id
     * @return array
     */
    public function deleteCartItem($product_id)
    {
        unset($_SESSION['cart'][$product_id]);

        return [
            'status' => 'success',
            'data'   => $this->getCartInfo()
        ];
    }


    /**
     * Tính phí ship
     *
     * @param $to_district_code
     * @param $service_id
     * @param $total_weight
     * @return array
     */
    public function calculateShippingFee($to_district_code, $service_id, $total_weight)
    {
        $ghn = GHN::init();

        // All parameters are required
        $result = false;
        if (!empty($to_district_code) && !empty($service_id) && !empty($total_weight)) {
            $result = $ghn->calculateFee($total_weight, $to_district_code, $service_id);

            $_SESSION['shipping_fee'] = $result;
        }

        return $result;
    }


    /**
     * Lấy thông tin giỏ hàng
     *
     * @return array
     */
    public function getCartInfo()
    {
        $result = [
            'total_quantity' => 0,
            'total_weight'   => 0,
            'subtotal'       => 0,
            'shipping_fee'   => 0,
            'total'          => 0
        ];

        if ($_SESSION['cart']) {
            $product_ctrl = ProductController::init();

            foreach ($_SESSION['cart'] as $key => $item) {
                // Total quantity
                $result['total_quantity'] += intval($item['quantity']);


                // Total weight
                $result['total_weight'] += intval($item['weight']);


                $product_info = $product_ctrl->getProductInfo($key);
                $result['subtotal'] += (intval($product_info->price) * intval($item['quantity']));
            }

            // Update session
            $_SESSION['total_quantity'] = $result['total_quantity'];
            $_SESSION['total_weight'] = $result['total_weight'];
            $_SESSION['subtotal'] = $result['subtotal'];

            if (!empty($_SESSION['shipping_fee'])) {
                $result['shipping_fee'] = $_SESSION['shipping_fee'];
                $result['total'] = $_SESSION['shipping_fee'] + $result['subtotal'];
            } else {
                $result['shipping_fee'] = 0;
                $result['total'] = $result['subtotal'];
            }

            // Format number
            $result['total_quantity_format'] = number_format($result['total_quantity']);
            $result['total_format'] = number_format($result['total']);
            $result['subtotal_format'] = number_format($result['subtotal']);
        }

        return $result;
    }

    //endregion
}