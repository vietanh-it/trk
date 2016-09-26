<?php
namespace TVA\Hooks;

use TVA\Controllers\OrdersController;
use TVA\Controllers\PostController;
use TVA\Library\CPTColumns;
use TVA\Models\Location;
use TVA\Models\Orders;

class BackendUI
{
    private static $instance;

    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new BackendUI();
        }

        return self::$instance;
    }

    function __construct()
    {
        add_filter('wp_headers', [$this, 'wp_headers']);
        add_action('admin_head', [$this, 'admin_head']);
        add_action('admin_footer', [$this, 'admin_footer']);
        add_action('admin_menu', [$this, 'admin_menu']);
        add_filter('show_admin_bar', [$this, 'show_admin_bar']);
        add_action('wp_dashboard_setup', [$this, 'wp_dashboard_setup']);

        add_filter('post_row_actions', [$this, 'page_row_actions_hook'], 10, 2);

        // UI
        $this->add_columns_featured_image();

        add_action('manage_product_posts_custom_column', [$this, 'manage_product_columns'], 10, 2);

        add_action('save_post', [$this, 'savePostInfo'], 10, 3);

        add_action('add_meta_boxes', [$this, 'orderInfoMetaBoxRegister']);
        add_filter('manage_shop_order_posts_columns', [$this, 'shop_order_columns']);
        add_action('manage_shop_order_posts_custom_column', [$this, 'render_shop_order_columns'], 2);
        add_filter('manage_edit-shop_order_sortable_columns', [$this, 'my_cpt_columns']);

        // add_settings_field(
        //     'currency_rate_setting_id',
        //     'Tỉ giá',
        //     'currency_date_setting_callback',
        //     'general',
        //     'default',
        //     ['label_for' => 'currency_rate_setting_id']
        // );
    }

    function my_cpt_columns($columns)
    {
        // $columns['order_date'] = 'order_date';
        return $columns;
    }

    function currency_date_setting_callback()
    {

    }

    function orderInfoMetaBoxRegister()
    {
        add_meta_box('order-info', 'Thông tin đơn hàng', [$this, 'orderInfoMetaBox'], 'shop_order');
    }

    function orderInfoMetaBox($post)
    {
        $order_ctrl = OrdersController::init();
        $order_info = $order_ctrl->getOrderInfo($post->ID); ?>
        <div class="acf_postbox default">
            <div class="inside">
                <div class="field">
                    <p class="label">
                        <label for="customer_name">Họ tên khách hàng
                        </label>
                    </p>
                    <div class="acf-input-wrap">
                        <input id="customer_name" type="text" name="customer_name"
                               value="<?php echo $order_info->name; ?>"
                               placeholder="Nhập tên khách hàng">
                    </div>
                </div>
                <div class="field">
                    <p class="label">
                        <label for="customer_email">Email khách hàng
                        </label>
                    </p>
                    <div class="acf-input-wrap">
                        <input id="customer_email" type="text" name="customer_email"
                               value="<?php echo $order_info->email; ?>"
                               placeholder="Nhập email khách hàng">
                    </div>
                </div>
                <div class="field">
                    <p class="label">
                        <label for="customer_phone">Số điện thoại khách hàng
                        </label>
                    </p>
                    <div class="acf-input-wrap">
                        <input id="customer_phone" type="text" name="customer_phone"
                               value="<?php echo $order_info->phone; ?>"
                               placeholder="Nhập số điện thoại khách hàng">
                    </div>
                </div>
                <div class="field">
                    <p class="label">
                        <label for="customer_phone">Địa chỉ đặt hàng</label>
                    </p>
                    <div class="acf-input-wrap">
                        <?php $location_model = Location::init();
                        $city = $location_model->getCityBy('id', $order_info->city_id);
                        $district = $location_model->getDistrictBy('id', $order_info->district_id); ?>
                        <?php echo $order_info->address . ', ' . $district->name . ', ' . $city->name; ?>
                    </div>
                </div>
                <div class="field">
                    <p class="label">
                        <label for="customer_phone">Giá ship
                        </label>
                    </p>
                    <div class="acf-input-wrap">
                        <?php echo number_format($order_info->shipping_fee) . ' đ'; ?>
                    </div>
                </div>
                <div class="field">
                    <p class="label">
                        <label for="customer_phone">Tổng giá trị đơn hàng
                        </label>
                    </p>
                    <div class="acf-input-wrap">
                        <?php echo number_format($order_info->total) . ' đ'; ?>
                    </div>
                </div>

                <div class="field">
                    <p class="label">
                        <label for="customer_phone">Trạng thái
                        </label>
                    </p>
                    <div class="acf-input-wrap">
                        <?php
                        if (in_array($order_info->status, ['pending'])) {
                            echo '<b style="color: red">Chờ xử lý</b>';
                        } elseif (in_array($order_info->status, ['processing'])) {
                            echo '<b style="color: green;">Đang xử lý</b>';
                        } elseif (in_array($order_info->status, ['completed', 'publish'])) {
                            echo 'Đã hoàn tất';
                        } else {
                            echo '<b style="color: red">Đã hủy</b>';
                        }
                        ?>
                    </div>
                </div>

                <div class="field">
                    <p class="label">
                        <label for="customer_phone">Actions
                        </label>
                    </p>
                    <div class="acf-input-wrap">
                        <?php
                        if (in_array($order_info->status, ['pending'])) {
                            $url = wp_nonce_url(admin_url('admin-ajax.php?action=trk_ajax_handler_order&method=ProcessOrder&order_id=' . $post->ID));
                            echo '<a class="button tips pending" href="' . $url . '" data-tip="Xử lý">Xử lý</a>';
                        } elseif (in_array($order_info->status, ['processing'])) {
                            $url = wp_nonce_url(admin_url('admin-ajax.php?action=trk_ajax_handler_order&method=CompleteOrder&order_id=' . $post->ID));
                            echo '<a class="button tips complete" href="' . $url . '" data-tip="Hoàn tất">Hoàn tất đơn hàng</a>';
                        } elseif (in_array($order_info->status, ['completed', 'publish'])) {
                            echo 'Đã hoàn tất';
                        } else {
                            echo '<b style="color: red">Đã hủy</b>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    <?php }

    function savePostInfo($post_id, $post, $update)
    {
        global $wpdb;

        if ($post->post_type == 'shop_order') {
            if ($post->post_type == 'publish') {
                $wpdb->update($wpdb->prefix . 'order_info', [
                    'status' => 'completed'
                ], [
                    'ID' => $post_id
                ]);
            }
        } elseif (in_array($post->post_type, ['product', 'recipe', 'beauty'])) {
            if (empty($wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'post_info WHERE ID = ' . $post_id))) {
                $wpdb->insert($wpdb->prefix . 'post_info', ['ID' => $post_id]);
            }
        }
    }

    function restrict_manage_comments()
    {
        switch ($_GET['comment_kind']) {
            case 'comment':
                $selectedComments = "selected";
                $selectedReviews = "";
                break;
            case 'review':
                $selectedComments = "";
                $selectedReviews = "selected";
                break;
            default:
                $selectedComments = "";
                $selectedReviews = "";
        }

        $html = '<select name="comment_kind">
                        <option value="all">Show all comment types</option>
                        <option value="comment" ' . $selectedComments . '>Comments</option>
                        <option value="review" ' . $selectedReviews . '>Reviews</option>
                    </select>';
        echo $html;

        switch ($_GET['comment_reported']) {
            case 'reported':
                $selectedComments = "selected";
                break;
            default:
                $selectedComments = "";
        }
        $html = '<select name="comment_reported">
                        <option value="all">No filter reported</option>
                        <option value="reported" ' . $selectedComments . '>Reported comments</option>
                    </select>';
        echo $html;
    }

    /**
     * @param $headers
     * @return mixed
     */
    public function wp_headers($headers)
    {
        unset($headers['X-Pingback']);
        return $headers;
    }

    /**
     * @param $content
     * @return bool
     */
    public function show_admin_bar($content)
    {
        $current_user = wp_get_current_user();

        if (empty($current_user)) {
            return false;
        }

        if (in_array(@$current_user->roles[0], ['subscriber'])) {
            return false;
        } else {
            return $content;
        }
    }

    function admin_head()
    {
        echo "<style type='text/css'>
                #timelinediv  div.tabs-panel{ max-height: 500px}
                .tablenav.top .actions select[name='comment_type'] {display: none !important;}
                </style>";
    }

    function admin_footer()
    {
        global $pagenow;
        $array_page = ['users.php', 'edit-comments.php', 'edit.php'];

        if (is_admin() && in_array($pagenow, $array_page)) {
            ?>
            <link rel="stylesheet" href="<?php echo THEME_URL ?>/css/blitzer/jquery-ui-1.10.4.custom.min.css">
            <script src="<?php echo THEME_URL ?>/js/jquery-ui-1.10.4.custom.min.js"></script>
            <script type="text/javascript">
                jQuery(document).ready(function () {

                    // Filter Date
                    jQuery("#from_date").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        onClose: function (selectedDate) {
                            jQuery("#to_date").datepicker("option", "minDate", selectedDate);
                        }
                    });
                    jQuery("#to_date").datepicker({
                        dateFormat: 'dd/mm/yy',
                        changeMonth: true,
                        changeYear: true,
                        onClose: function (selectedDate) {
                            jQuery("#from_date").datepicker("option", "maxDate", selectedDate);
                        }
                    });

                });
            </script>
            <?php
        }
    }

    /**
     * @param $buttons
     * @return mixed
     */
    function mce_buttons($buttons)
    {
        //array_splice so we can insert the new item without overwriting an existing button
        array_splice($buttons, 15, 0, 'wp_page');
        return $buttons;
    }

    /**
     *
     */
    function wp_dashboard_setup()
    {
        global $wp_meta_boxes;

        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
        //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
        unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
        //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_drafts']);
        //unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
        unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
    }

    /**
     *
     */
    function admin_menu()
    {
        remove_menu_page('link-manager.php');
        remove_menu_page('tools.php');
        remove_menu_page('edit-comments.php');
        remove_submenu_page('index.php', 'update-core.php');

        if (!current_user_can('administrator')) {
            remove_menu_page('edit.php?post_type=shop_order');
            remove_menu_page('edit.php?post_type=contact-form');
            remove_menu_page('edit.php?post_type=page');
            remove_menu_page('edit.php?post_type=banner');
        }

        remove_action('admin_notices', 'update_nag', 3);
    }

    function add_columns_featured_image()
    {
        $post_columns = new CPTColumns('product');
        $post_columns->add_column('post_thumb',
            [
                'label' => 'Hình sản phẩm',
                'type'  => 'thumb',
                'size'  => ['50', '50']
            ]
        );

        $post_columns->add_column('price', [
            'label' => 'Giá',
            'type'  => 'text'
        ]);
    }

    function manage_product_columns($column, $post_id)
    {
        switch ($column) {

            /* If displaying the 'genre' column. */
            case 'price' :

                //TODO: View en price based on rate in admin column
                $choosen_lang = $_GET['lang'];

                $post_ctrl = PostController::init();
                $data = $post_ctrl->getPost($post_id);
                echo $data->price_display;

                break;

            /* Just break out of the switch statement for everything else. */
            default :
                break;
        }
    }

    public function shop_order_columns($existing_columns)
    {
        $columns = [];
        $columns['cb'] = $existing_columns['cb'];
        $columns['order_actions'] = __('Actions', 'woocommerce');
        $columns['order_status'] = 'Trạng thái đơn hàng';
        $columns['order_title'] = __('Mã đơn hàng', 'woocommerce');
        $columns['order_items'] = __('Đã mua', 'woocommerce');
        $columns['billing_address'] = __('Địa chỉ', 'woocommerce');
        $columns['order_date'] = __('Ngày tạo', 'woocommerce');
        $columns['order_total'] = __('Tổng tiền', 'woocommerce');

        return $columns;
    }

    public function render_shop_order_columns($column)
    {
        global $post, $the_order;

        if (empty($the_order) || $the_order->id != $post->ID) {
            $order_ctrl = OrdersController::init();
            $the_order = $order_ctrl->getOrderInfo($post->ID);
        }

        switch ($column) {
            case 'order_status' :
                if (in_array($the_order->status, ['pending'])) {
                    echo '<span style="background: #d0d0d0; padding: 4px 8px;">Chờ xử lý</span>';
                } elseif (in_array($the_order->status, ['processing'])) {
                    echo '<span style="color: red;">Đang xử lý</span>';
                } elseif (in_array($the_order->status, ['completed', 'publish'])) {
                    echo '<span style="color: green;">Đã hoàn tất</span>';
                } else {
                    echo '<span style="color: #777777;">Đã hủy</span>';
                }
                break;
            case 'order_title':
                echo '<a href="' . WP_SITEURL . '/wp-admin/post.php?post=' . $the_order->ID . '&action=edit">' . '#' . $the_order->code . '</a>';
                break;
            case 'order_items':
                $order_model = Orders::init();
                $order_detail = $order_model->getOrderDetail($the_order->ID);

                $total_items = 0;
                foreach ($order_detail as $item) {
                    $total_items += $item->quantity;
                }

                echo $total_items . ' sản phẩm';

                break;
            case 'order_date' :
                if ('0000-00-00 00:00:00' == $post->post_modified) {
                    $t_time = $h_time = __('Chưa hoàn tất', 'woocommerce');
                } else {
                    $t_time = date('Y/m/d g:i:s A', strtotime($post->post_modified));
                    $h_time = date('d-m-Y H:i:s', strtotime($post->post_modified));
                }

                echo '<abbr title="' . esc_attr($t_time) . '">' . esc_html(apply_filters('post_date_column_time',
                        $h_time, $post)) . '</abbr>';

                break;
            case 'billing_address' :

                if ($the_order->phone) {
                    echo '<small class="meta">' . __('SĐT:',
                            'woocommerce') . ' ' . esc_html($the_order->phone) . '</small>';
                }

                break;
            case 'order_total' :
                if ($the_order->total) {
                    echo '<b>' . number_format($the_order->total) . ' đ</b>';
                }

                if ($the_order->payment_method) {
                    echo '<br/><small class="meta">' . 'Thanh toán bằng ' . strtoupper($the_order->payment_method) . '</small>';
                }
                break;
            case 'order_actions' :

                ?>
                <p>
                    <?php
                    $actions = [];

                    $actions['view'] = [
                        'url'    => admin_url('post.php?post=' . $post->ID . '&action=edit'),
                        'name'   => __('View', 'woocommerce'),
                        'action' => "view",
                        'icon'   => '<i class="dashicons dashicons-visibility"></i>'
                    ];

                    if (in_array($the_order->status, ['pending', 'on-hold'])) {
                        $actions['processing'] = [
                            'url'    => wp_nonce_url(admin_url('admin-ajax.php?action=trk_ajax_handler_order&method=ProcessOrder&order_id=' . $post->ID),
                                'processing', 'processing'),
                            'name'   => __('Processing', 'woocommerce'),
                            'action' => "processing",
                            'icon'   => '<i class="dashicons dashicons-update"></i>'
                        ];
                    }

                    if (in_array($the_order->status, ['pending', 'on-hold', 'processing'])) {
                        $actions['complete'] = [
                            'url'    => wp_nonce_url(admin_url('admin-ajax.php?action=trk_ajax_handler_order&method=CompleteOrder&order_id=' . $post->ID),
                                'complete'),
                            'name'   => __('Complete', 'woocommerce'),
                            'action' => "complete",
                            'icon'   => '<i class="dashicons dashicons-yes"></i>'
                        ];
                    }

                    foreach ($actions as $action) {
                        printf('<a class="button tips %s" href="%s" data-tip="%s" style="padding: 4px; margin-right: 5px;">%s</a>',
                            esc_attr($action['action']),
                            esc_url($action['url']), esc_attr($action['name']), ($action['icon']));
                    }

                    $resend_email_url = wp_nonce_url(admin_url('admin-ajax.php?action=trk_ajax_handler_order&method=ResendEmailOrder&order_id=' . $post->ID));

                    echo '<a class="button tips resend-email" href="' . $resend_email_url . '" style="padding: 0 10px; margin-right: 5px; margin-top: 5px;">Gửi lại Email Order</a>';
                    ?>
                </p>
                <?php

                break;
        }
    }

    function page_row_actions_hook($actions, $post)
    {
        if ($post->post_type == 'shop_order') {
            return [];
        }
        return $actions;
    }
}