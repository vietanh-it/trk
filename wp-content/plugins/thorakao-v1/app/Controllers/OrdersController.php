<?php
namespace TVA\Controllers;

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

        add_action("wp_ajax_trk_ajax_handler_order", [$this, "ajaxHandler"]);
        add_action("wp_ajax_nopriv_trk_ajax_handler_order", [$this, "ajaxHandler"]);

        // add_action('init', [$this, 'register_post_status'], 1);
    }


    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new OrdersController();
        }

        return self::$instance;
    }


    public function ajaxAddToCart()
    {
        $result = [
            'status'  => 'error',
            'message' => 'Đã xảy ra lỗi, vui lòng thử lại',
        ];
        if (!empty($_POST['product_id'])) {
            $product_id = $_POST['product_id'];
            $quantity = valueOrNull(intval($_POST['quantity']), 1);

            $product_ctrl = ProductController::init();
            $pinfo = $product_ctrl->getProductInfo($product_id);

            //Chưa hỗ trợ tiếng anh
            if ($pinfo->language != 'vi') {
                return [
                    'status' => 'fail',
                    'data'   => 'Thorakao chưa hỗ trợ đặt hàng trên ngôn ngữ tiếng anh, vui lòng quay lại <a href="' . WP_SITEURL . '" style="color: #80b501;">phiên bản tiếng việt</a>.'
                ];
            }

            if (isset($_SESSION['cart'][$product_id])) {
                //Nếu đã có product trong giỏ hàng
                $current_quantity = $_SESSION['cart'][$product_id]['quantity'];
                if (!empty($_POST['plus_quantity'])) {
                    $new_quantity = intval($quantity);
                } else {
                    $new_quantity = intval($current_quantity) + $quantity;
                }

                $_SESSION['cart'][$product_id] = [
                    'quantity'   => $new_quantity,
                    'product_id' => $product_id,
                ];
            } else {
                $_SESSION['cart'][$product_id] = [
                    'quantity'   => $quantity,
                    'product_id' => $product_id,
                ];
            }

            if ($_SESSION['cart']) {
                $total_quantity = 0;
                $subtotal = 0;

                foreach ($_SESSION['cart'] as $key => $item) {
                    $total_quantity += intval($_SESSION['cart'][$key]['quantity']);
                    $product_info = $product_ctrl->getProductInfo($key);
                    $subtotal += (intval($product_info->price) * intval($_SESSION['cart'][$key]['quantity']));
                }
                $_SESSION['total_quantity'] = $total_quantity;
                $_SESSION['subtotal'] = $subtotal;
            }

            $city_id = valueOrNull($_POST['city_id'], 0);
            $district_id = valueOrNull($_POST['district_id'], 0);

            $shipping_fee = $this->calculateShippingFee($city_id, $district_id, $_SESSION['subtotal']);
            $_SESSION['shipping_fee'] = $shipping_fee;

            $result = [
                'status'  => 'success',
                'message' => 'Thêm vào giỏ hàng hành công',
                'data'    => [
                    'url'                      => WP_SITEURL . '/gio-hang/',
                    'current_product_subtotal' => number_format($_SESSION['cart'][$product_id]['quantity'] * $pinfo->price) . ' đ',
                    'current_total_raw'        => $_SESSION['subtotal'],
                    'current_total'            => number_format($_SESSION['subtotal']) . ' đ',
                    'current_total_quantity'   => $_SESSION['total_quantity'],
                    'order_final_total'        => number_format($shipping_fee + $_SESSION['subtotal']) . ' đ',
                    'shipping_fee'             => number_format($shipping_fee) . ' đ'
                ],
            ];

            $this->clearStaticCache();
        }
        return $result;
    }


    public function ajaxDeleteProductCart()
    {
        $result = [
            'status'  => 'error',
            'message' => 'Đã xảy ra lỗi, vui lòng thử lại',
        ];
        if (!empty($_POST['product_id'])) {
            $product_id = $_POST['product_id'];

            $new_total_quantity = intval($_SESSION['total_quantity'] - $_SESSION['cart'][$product_id]['quantity']);
            $_SESSION['total_quantity'] = $new_total_quantity;

            $product_ctrl = ProductController::init();
            $product_info = $product_ctrl->getProductInfo($product_id);
            $new_subtotal = $_SESSION['subtotal'] - ($_SESSION['cart'][$product_id]['quantity'] * $product_info->price);
            $_SESSION['subtotal'] = $new_subtotal;

            if ($new_subtotal > 0) {
                $shipping_fee = $this->calculateShippingFee($_SESSION['city_id'], $_SESSION['district_id'],
                    $new_subtotal);
            } else {
                $_SESSION['shipping_fee'] = $shipping_fee = 0;
            }

            $total = $new_subtotal + valueOrNull($shipping_fee, 0);
            $total_format = number_format($total) . ' đ';

            unset($_SESSION['cart'][$product_id]);
            $result = [
                'status' => 'success',
                'data'   => [
                    'total_quantity' => $new_total_quantity,
                    'subtotal'       => number_format($new_subtotal) . ' đ',
                    'subtotal_raw'   => $new_subtotal,
                    'total'          => $total_format,
                    'total_raw'      => $total,
                    'shipping_fee'   => number_format($shipping_fee) . ' đ'
                ],
            ];

            $this->clearStaticCache();
        }
        return $result;
    }


    public function ajaxSaveOrder($data)
    {
        $data = $_POST;
        $result = [
            'status'  => 'error',
            'message' => 'Đã xảy ra lỗi, vui lòng thử lại.'
        ];

        if (empty($_SESSION['subtotal'])) {
            return $result;
        }

        if (!empty($_SESSION['cart'])) {
            $this->validate->rule('required', ['name', 'phone', 'email', 'address', 'payment_method', 'city_id'])
                ->message('Vui lòng nhập {field}')
                ->labels([
                    'name'           => 'họ tên',
                    'phone'          => 'số điện thoại',
                    'email'          => 'email',
                    'address'        => 'địa chỉ',
                    'payment_method' => 'phương thức thanh toán',
                    'city_id'        => 'tỉnh thành'
                ]);

            if ($this->validate->validate()) {
                $this->wpdb->insert($this->wpdb->posts, [
                    'post_title'    => 'Đơn hàng từ - ' . $data['name'],
                    'post_type'     => 'shop_order',
                    'post_status'   => 'pending',
                    'post_date'     => current_time('mysql'),
                    'post_modified' => current_time('mysql')
                ]);

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

                $this->wpdb->insert($this->tbl_order_info, [
                    'ID'             => $id,
                    'code'           => 'TRK' . $id,
                    'name'           => $data['name'],
                    'email'          => $data['email'],
                    'phone'          => $data['phone'],
                    'city_id'        => $data['city_id'],
                    'district_id'    => valueOrNull($data['district_id'], 0),
                    'address'        => $data['address'],
                    'subtotal'       => $_SESSION['subtotal'],
                    // 'coupon_id'      => $data['address'],
                    'total'          => $data['cart_total'],
                    'shipping_fee'   => $data['shipping_fee'],
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
                        'quantity'   => $item['quantity']
                    ]);
                }

                session_destroy();

                $data['url'] = pll_current_language() == 'vi' ? WP_SITEURL : WP_SITEURL . '/en/';

                $this->sendEmailOrder($id);

                $this->clearStaticCache();
                return [
                    'status' => 'success',
                    'data'   => $data
                ];
            } else {
                $result = [
                    'status'  => 'error',
                    'message' => 'Đã xảy ra lỗi, vui lòng thử lại'
                ];
            }
        }

        return $result;
    }


    public function sendEmailOrder($order_id)
    {
        $model = Orders::init();
        $location_model = Location::init();

        $order_info = $this->getOrderInfo($order_id);
        $city = $location_model->getCityBy('id', $order_info->city_id);
        $district = $location_model->getDistrictBy('id', $order_info->district_id);

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

        $subject = 'Chào ' . $order_info->name . ', chúc mừng bạn đã đặt hàng thành công.';

        if (WP_DEBUG) {
            return wp_mail('vietanhtran.it@gmail.com', $subject, $content, 'Content-type: text/html');
        } else {
            wp_mail('thorakaoshop@thorakaovn.com', $subject, $content, 'Content-type: text/html');
            wp_mail('vietanhtran.it@gmail.com', $subject, $content, 'Content-type: text/html');
            return wp_mail($order_info->email, $subject, $content, 'Content-type: text/html');
        }
    }


    public function getOrderInfo($order_id)
    {
        $data['ID'] = $order_id;

        $model = Orders::init();
        $result = $model->getOrderInfo($data);

        return $result;
    }


    public function ajaxGetShippingFee($data)
    {
        $_SESSION['city_id'] = $data['city_id'];
        $_SESSION['district_id'] = $data['district_id'];

        // $subtotal, $city_id, $district_id = 0s
        $city_id = $data['city_id'];
        $district_id = valueOrNull($data['district_id'], 0);
        $subtotal = valueOrNull($data['subtotal'], $_SESSION['subtotal']);

        if (empty($city_id)) {
            $result = [
                'status'  => 'error',
                'message' => 'Đã xảy ra lỗi, vui lòng thử lại'
            ];
        } else {
            if ($city_id == 1) {

                // TP HCM
                if ($subtotal >= 200000) {
                    $shipping_fee = 0;
                } elseif ($subtotal >= 101000) {
                    $district_type = $this->getDistrictType($district_id);
                    if ($district_type == 'ngoai-thanh-1') {
                        $shipping_fee = 30000;
                    } elseif ($district_type == 'ngoai-thanh-2') {
                        $shipping_fee = 40000;
                    } elseif ($district_type == 'noi-thanh') {
                        $shipping_fee = 20000;
                    } else {
                        $shipping_fee = 40000;
                    }
                } else {
                    $district_type = $this->getDistrictType($district_id);
                    if ($district_type == 'ngoai-thanh-1') {
                        $shipping_fee = 25000;
                    } elseif ($district_type == 'ngoai-thanh-2') {
                        $shipping_fee = 35000;
                    } elseif ($district_type == 'noi-thanh') {
                        $shipping_fee = 15000;
                    } else {
                        $shipping_fee = 35000;
                    }
                }
            } else {

                $location_model = Location::init();
                $city = $location_model->getCityBy('id', $city_id);

                // City khác
                if ($subtotal >= 500000) {
                    $shipping_fee = 0;
                } elseif ($subtotal >= 251000) {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 45000;
                    } elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 50000;
                    } elseif ($city->type == 'khac') {
                        $shipping_fee = 60000;
                    } else {
                        $shipping_fee = 60000;
                    }
                } elseif ($subtotal >= 101000) {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 40000;
                    } elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 45000;
                    } elseif ($city->type == 'khac') {
                        $shipping_fee = 55000;
                    } else {
                        $shipping_fee = 55000;
                    }
                } else {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 30000;
                    } elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 35000;
                    } elseif ($city->type == 'khac') {
                        $shipping_fee = 45000;
                    } else {
                        $shipping_fee = 45000;
                    }
                }
            }

            $_SESSION['shipping_fee'] = $shipping_fee;

            $result = [
                'status' => 'success',
                'data'   => [
                    'shipping_fee'         => $shipping_fee,
                    'shipping_fee_display' => number_format($shipping_fee) . ' đ',
                    'total'                => ($_SESSION['subtotal'] + $shipping_fee),
                    'total_display'        => number_format($_SESSION['subtotal'] + $shipping_fee) . ' đ'
                ]
            ];
        }

        return $result;
    }


    public function calculateShippingFee($city_id = 0, $district_id = 0, $subtotal = 0)
    {
        $subtotal = valueOrNull($subtotal, $_SESSION['subtotal']);

        if (empty($city_id)) {
            $result = 0;
        } else {
            if ($city_id == 1) {

                // TP HCM
                if ($subtotal >= 200000) {
                    $shipping_fee = 0;
                } elseif ($subtotal >= 101000) {
                    $district_type = $this->getDistrictType($district_id);
                    if ($district_type == 'ngoai-thanh-1') {
                        $shipping_fee = 30000;
                    } elseif ($district_type == 'ngoai-thanh-2') {
                        $shipping_fee = 40000;
                    } elseif ($district_type == 'noi-thanh') {
                        $shipping_fee = 20000;
                    } else {
                        $shipping_fee = 40000;
                    }
                } else {
                    $district_type = $this->getDistrictType($district_id);
                    if ($district_type == 'ngoai-thanh-1') {
                        $shipping_fee = 25000;
                    } elseif ($district_type == 'ngoai-thanh-2') {
                        $shipping_fee = 35000;
                    } elseif ($district_type == 'noi-thanh') {
                        $shipping_fee = 15000;
                    } else {
                        $shipping_fee = 35000;
                    }
                }
            } else {

                $location_model = Location::init();
                $city = $location_model->getCityBy('id', $city_id);

                // City khác
                if ($subtotal >= 500000) {
                    $shipping_fee = 0;
                } elseif ($subtotal >= 251000) {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 45000;
                    } elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 50000;
                    } elseif ($city->type == 'khac') {
                        $shipping_fee = 60000;
                    } else {
                        $shipping_fee = 60000;
                    }
                } elseif ($subtotal >= 101000) {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 40000;
                    } elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 45000;
                    } elseif ($city->type == 'khac') {
                        $shipping_fee = 55000;
                    } else {
                        $shipping_fee = 55000;
                    }
                } else {
                    if ($city->type == 'cung-mien') {
                        $shipping_fee = 30000;
                    } elseif ($city->type == 'lien-mien') {
                        $shipping_fee = 35000;
                    } elseif ($city->type == 'khac') {
                        $shipping_fee = 45000;
                    } else {
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

        $this->wpdb->update($this->wpdb->posts,
            [
                'post_modified' => current_time('mysql')
            ], [
                'ID' => $data['order_id']
            ]);

        $this->clearStaticCache();

        wp_redirect(WP_SITEURL . '/wp-admin/edit.php?post_type=shop_order');
        exit();
    }


    public function ajaxCompleteOrder($data)
    {
        $this->wpdb->update($this->wpdb->prefix . 'order_info', [
            'status' => 'completed'
        ], ['ID' => $data['order_id']]);

        $this->wpdb->update($this->wpdb->posts,
            [
                'post_modified' => current_time('mysql')
            ], [
                'ID' => $data['order_id']
            ]);

        wp_publish_post($data['order_id']);

        $this->clearStaticCache();

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
        } else {
            wp_mail('thorakaoshop@thorakaovn.com', $subject, $content, 'Content-type: text/html');
        }

        wp_redirect(WP_SITEURL . '/wp-admin/edit.php?post_type=shop_order');
        exit();
    }
}
