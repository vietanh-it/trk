<?php
/**
 * Created by PhpStorm.
 * User: Vo sy dao
 * Date: 5/25/2016
 * Time: 2:09 PM
 * Template name: Shopping bag - step 2 cart info
 */
get_header();

$productCtr = \TVA\Controllers\ProductController::init();
$post_ctrl = \TVA\Controllers\PostController::init();
//$orderCtr = \TVA\Controllers\Orders::init();
$m_order = \TVA\Models\Orders::init();

$cart_guest_info = $m_order->getCart();
if (empty($cart_guest_info->city_id)) {
    wp_redirect(WP_SITEURL . '/gio-hang');
    exit;
}

// Calculate shipping fee
$subtotal = $_SESSION['subtotal'];
// $shipping_fee = $m_or

$city_list = $post_ctrl->getCityList();


$cart = $_SESSION['cart']; ?>

    <div class="breadcrumb no-padding">
        <div class="container">
            <ul class="no-padding no-margin">
                <li>
                    <a href="<?php echo WP_SITEURL; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Trang chủ' : 'Home'; ?>
                    </a>
                    <span class="slash">/</span>
                </li>
                <li class="last">
                    <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/gio-hang' : '/shopping-cart'; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Giỏ hàng' : 'Shopping Cart'; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form action="javascript:void(0)" method="post" id="form-checkout">
                        <h2 class="title page-title">Thanh Toán</h2>

                        <div class="nav tab-list">
                            <a href="javascript:void(0)">Bước 2: Thông tin đơn hàng</a>
                        </div>

                        <div class="row">

                            <div class="col-md-12">
                                <div class="checkout-info">
                                    <div class="title checkout-title">
                                        <i class="fa fa-map-marker mr-10"></i>
                                        Đơn hàng
                                        (<span class="order-total-quantity">
                                                <?php echo empty($_SESSION['total_quantity']) ? 0 : $_SESSION['total_quantity']; ?>
                                            </span> sản phẩm)
                                    </div>
                                    <div class="checkout-content">
                                        <?php
                                        $price_total = 0;
                                        $price_subtotal = [];
                                        if ($cart) {
                                            foreach ($cart as $v) {
                                                $product_info = $productCtr->getProductInfo($v['product_id']);
                                                $price_subtotal[] = (intval($product_info->price) * intval($v['quantity']));
                                                ?>

                                                <div class="row cart-item-wrapper" data-pid="<?php echo $product_info->ID; ?>">

                                                    <!--Featured image-->
                                                    <div class="col-md-2">
                                                        <img src="<?php echo $product_info->featured_image ?>" class="img-responsive">
                                                    </div>


                                                    <!--Title-->
                                                    <div class="col-md-6"><?php echo $product_info->post_title ?></div>


                                                    <!--Price-->
                                                    <div class="col-md-3 text-right" style="white-space: nowrap;">

                                                        <span data-current-price="<?php echo $product_info->price; ?>">
                                                            <?php echo $product_info->price_display ?>
                                                        </span>

                                                        <span>
                                                        x
                                                        <label>
                                                            <input type="number" class="cart-item-quantity"
                                                                   value="<?php echo $v['quantity']; ?>"
                                                                   style="width: 45px; line-height: 14px; padding: 2px 0 2px 5px;"
                                                                   min="1">
                                                        </label>
                                                        </span>
                                                        &#61;

                                                        <span class="cart-item-calculated-total">
                                                            <?php echo number_format(intval($v['quantity']) * $product_info->price) . ' đ' ?>
                                                        </span>

                                                    </div>


                                                    <!--Delete button-->
                                                    <div class="col-md-1">
                                                        <a href="javascript:void(0)"
                                                           data-delete-product-cart="<?php echo $product_info->ID; ?>">
                                                            <i class="fa fa-close"></i>
                                                        </a>
                                                    </div>

                                                </div>
                                            <?php }
                                        }

                                        $price_total = array_sum($price_subtotal);
                                        ?>

                                        <div class="row">
                                            <div class="col-md-12 total"><span>Tổng tiền</span>
                                                <span class="pull-right" data-order-total="<?php echo $price_total; ?>">
                                                <?php echo number_format($price_total) ?> đ
                                            </span>
                                            </div>
                                        </div>
                                        <div class="row no-border">
                                            <div class="col-md-12 total">
                                                <span>Phí vận chuyển</span>
                                                <span class="pull-right shipping-fee">...</span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 total">
                                                <span>Số tiền cần thanh toán</span>
                                                <span class="pull-right clr-red cart-total order-final-total">
                                                    <?php echo number_format($price_total) ?> đ
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="nav tab-list">
                            <a href="javascript:void(0)">Bước 2: Hình thức thanh toán</a>
                        </div>
                        <div class="row mt-55 payment_method_wrapper">
                            <div class="col-md-6 col-xs-12">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="cod" checked>
                                        <div class="radio-group-addon delivery-option">
                                            <div class="row">
                                                <div class="col-md-3 col-xs-4">
                                                    <img src="<?php echo THEME_URL ?>/images/delivery_icon.png" height="50" width="50">
                                                </div>
                                                <div class="col-md-9 col-xs-8">
                                                    <b>Thanh toán khi nhận hàng</b>
                                                    <br>Quý khách sẽ thanh toán bằng tiền mặt hoặc thẻ khi Thorakao giao hàng cho quý khách
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <div class="radio">
                                    <label>
                                        <input type="radio" name="payment_method" value="atm">
                                        <div class="radio-group-addon delivery-option">
                                            <div class="row">
                                                <div class="col-md-3 col-xs-4">
                                                    <img src="<?php echo THEME_URL ?>/images/atm-card.png" height="50" width="50">
                                                </div>
                                                <div class="col-md-9 col-xs-8">
                                                    <b>Thanh toán bằng thẻ ATM</b>
                                                    <br>Thẻ ATM của quý khách cần đăng ký sử dụng dịch vụ internet banking
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="text-center mt-20">
                                <a href="<?php echo WP_SITEURL . '/gio-hang'; ?>" class="btn btn-default" style="margin-right: 10px; text-shadow: none;"> Quay lại bước 1</a>
                                <input type="submit" class="btn btn-primary btn-submit-checkout" value="Thanh toán">
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        var $ = jQuery.noConflict();
        $(document).ready(function ($) {
            $('.cart-item-quantity').on('focusout', function () {

                var current_quantity = $(this).val();
                var cart_item_wrapper = $(this).parents('.cart-item-wrapper');
                var current_price = cart_item_wrapper.find('[data-current-price]').data('current-price');
                var cart_item_calculated_total = cart_item_wrapper.find('.cart-item-calculated-total');
                var current_total = current_price * current_quantity;
                var product_id = cart_item_wrapper.data('pid');

                // clearTimeout(timeout);
                // var timeout = setTimeout(function () {
                $.ajax({
                    url: ajaxurl,
                    type: "post",
                    dataType: 'json',
                    data: {
                        action: "trk_ajax_handler_order",
                        method: "AddToCart",
                        product_id: product_id,
                        quantity: current_quantity,
                        plus_quantity: false,
                        city_id: $('#city_id').val(),
                        district_id: $('#district_id').val()
                    },
                    beforeSend: function () {
                        $('.cart-item-quantity').attr('disabled', true).css({'opacity': '0.5'});
                        $('.btn-submit-checkout').attr('disabled', true).css({'opacity': 0.5});
                    },
                    success: function (data) {
                        $('.cart-item-quantity').attr('disabled', false).css({'opacity': 1});
                        $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
                        if (data.status == 'success') {
                            cart_item_calculated_total.html(data.data.current_product_subtotal);
                            $('[data-order-total]').html(data.data.current_total);
                            $('[data-order-total]').attr('data-order-total', data.data.current_total_raw);
                            $('.order-final-total').html(data.data.order_final_total);
                            $('.shipping-fee').html(data.data.shipping_fee);
                            $('.order-total-quantity').html(data.data.current_total_quantity);
                        }
                        else {
                            swal({"title": "Error", "text": data.message, "type": "error", html: true});
                        }
                    }
                });
                // , 0);
            });

            $('.btn-cart-continue').on('click', function (e) {
                e.preventDefault();

                console.log($('.checkout-info-form').serialize());
            });
        });
    </script>

<?php get_footer();