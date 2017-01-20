<?php
/**
 * Created by PhpStorm.
 * User: Vo sy dao
 * Date: 5/25/2016
 * Time: 2:09 PM
 * Template name: Shopping bag
 */

$productCtr = \TVA\Controllers\ProductController::init();
$post_ctrl = \TVA\Controllers\PostController::init();
//$orderCtr = \TVA\Controllers\Orders::init();

$city_list = $post_ctrl->getCityList();

$cart = $_SESSION['cart'];
if (empty($cart)) {
    wp_redirect(WP_SITEURL);
    exit;
}

get_header();
?>

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
                    <div class="nav tab-list"><a href="#tab_combo">Bước 1: Thông tin đơn hàng</a></div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="checkout-info">
                                <div class="title checkout-title">
                                    <i class="fa fa-map-marker mr-10"></i>Địa chỉ nhận hàng
                                </div>
                                <div class="checkout-content">
                                    <div class="checkout-info-form">
                                        <div class="uk-form">
                                            <div class="uk-form-row">
                                                <label>Họ tên:</label>
                                                <input type="text" placeholder="Nhập họ tên" name="name">
                                            </div>
                                            <div class="uk-form-row">
                                                <label>Số điện thoại:</label>
                                                <input type="text" placeholder="Nhập số điện thoại" name="phone">
                                            </div>
                                            <div class="uk-form-row">
                                                <label>Email:</label>
                                                <input type="text" placeholder="Nhập email" name="email">
                                            </div>
                                            <div class="uk-form-row">
                                                <label for="city_id">Thành phố:</label>
                                                <select id="city_id" name="city_id">
                                                    <option value="">--- Chọn thành phố ---</option>
                                                    <?php foreach ($city_list as $key => $item) {
                                                        echo "<option value='{$item->id}'>{$item->name}</option>";
                                                    } ?>
                                                </select>
                                            </div>
                                            <div class="uk-form-row district-wrapper" style="display: none;">
                                                <label>Quận / huyện:</label>
                                                <select id="district_id" name="district_id">
                                                    <option value="">--- Chọn quận/huyện ---</option>
                                                </select>
                                            </div>

                                            <div
                                                style="display: block; border-top: 1px dashed #dddddd; margin-top: 20px; margin-bottom: 20px;"></div>

                                            <div class="uk-form-row shipping-wrapper" style="display: none;">
                                                <label>Phương thức giao hàng:</label>
                                                <select id="shipping_method" name="shipping_method">
                                                    <option value="">--- Chọn phương thức giao hàng ---</option>
                                                </select>
                                            </div>

                                            <div class="uk-form-row">
                                                <label>Địa chỉ:</label>
                                                <input type="text" placeholder="Nhập địa chỉ giao hàng" name="address">
                                            </div>
                                            <div class="uk-form-row">
                                                <label>Lời nhắn:</label>
                                                <input type="text" placeholder="Ví dụ: Giao hàng trong giờ hành chính"
                                                       name="note">
                                            </div>
                                        </div>
                                        <div class="value-hidden">
                                            <input type="hidden" name="payment_method" value="">
                                            <input type="hidden" name="coupon_code" value="">
                                            <input id="input_shipping_fee" type="hidden" name="shipping_fee" value="0">
                                            <input id="input_cart_total" type="hidden" name="cart_total" value="0">
                                            <input type="hidden" name="action" value="trk_ajax_handler_order">
                                            <input type="hidden" name="method" value="SaveOrder">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
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
                                            <div class="row cart-item-wrapper"
                                                 data-pid="<?php echo $product_info->ID; ?>">
                                                <div class="col-md-3">
                                                    <a href="<?php echo $product_info->permalink; ?>" target="_blank">
                                                        <img src="<?php echo $product_info->featured_image ?>"
                                                             class="img-responsive">
                                                    </a>
                                                </div>
                                                <div class="col-md-5">
                                                    <a href="<?php echo $product_info->permalink; ?>" target="_blank">
                                                        <?php echo $product_info->post_title ?>
                                                    </a>
                                                </div>
                                                <div class="col-md-3">
                                                    <span style="white-space: nowrap;"
                                                          data-current-price="<?php echo $product_info->price; ?>">
                                                        <?php echo $product_info->price_display ?>
                                                    </span>
                                                    <br>
                                                    <span>
                                                        x
                                                        <label>
                                                            <input type="number" class="cart-item-quantity"
                                                                   value="<?php echo $v['quantity']; ?>"
                                                                   style="width: 45px; line-height: 14px; padding: 2px 0 2px 5px;"
                                                                   min="1">
                                                        </label>
                                                    </span>
                                                    <br>
                                                    &#61;
                                                    <span style="white-space: nowrap;"
                                                          class="cart-item-calculated-total"><?php echo number_format(intval($v['quantity']) * $product_info->price) . ' đ' ?></span>
                                                </div>
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
                                        <div class="col-md-12 total"><span>Số tiền cần thanh toán</span><span
                                                class="pull-right clr-red cart-total order-final-total"><?php echo number_format($price_total) ?>
                                                đ</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mã giảm giá-->
                    <div class="row hide">
                        <div class="col-md-12">
                            <div class="checkout-info mt-20">
                                <div class="title checkout-title"><i class="fa fa-map-marker mr-10"></i>Mã giảm giá
                                </div>
                                <div class="checkout-content">
                                    <div class="checkout-info-form">
                                        <div class="uk-form">
                                            <div class="uk-form-row">
                                                <div class="input-group">
                                                    <input type="text" placeholder="Nhập mã giảm giá"
                                                           class="form-control"><span class="input-group-btn">
                              <button class="btn btn-primary group-btn">Áp dụng</button></span>
                                                </div>
                                                <i class="mt-10 block fs-12 text-center">Sau khi áp dụng, mã giảm giá có
                                                    thể
                                                    không dùng được trong vòng<b class="pl-5">15 phút</b><a href="#"
                                                                                                            chi tiết</a>
                                                </i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Mã giảm giá-->

                    <div class="nav tab-list"><a href="javascript:void(0)">Bước 2: Hình thức thanh toán</a></div>
                    <div class="row mt-55 payment_method_wrapper">
                        <div class="col-md-6 col-xs-12">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="payment_method" value="cod" checked>
                                    <div class="radio-group-addon delivery-option">
                                        <div class="row">
                                            <div class="col-md-3 col-xs-4"><img
                                                    src="<?php echo THEME_URL ?>/images/delivery_icon.png" height="50"
                                                    width="50"></div>
                                            <div class="col-md-9 col-xs-8"><b>Thanh toán khi nhận hàng</b><br>Quý khách
                                                sẽ thanh
                                                toán
                                                bằng tiền mặt khi Thorakao giao hàng cho quý khách
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6 col-xs-12" style="display: none;">
                            <div class="radio">
                                <label>
                                    <input type="radio" name="payment_method" value="atm">
                                    <div class="radio-group-addon delivery-option">
                                        <div class="row">
                                            <div class="col-md-3 col-xs-4">
                                                <img src="<?php echo THEME_URL ?>/images/atm-card.png" height="50"
                                                     width="50">
                                            </div>
                                            <div class="col-md-9 col-xs-8"><b>Thanh toán bằng thẻ ATM</b><br>Thẻ ATM của
                                                bạn cần
                                                đăng
                                                ký sử dụng dịch vụ internet banking
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div>
                        <div class="text-center mt-20">
                            <!--<a href="javascript:void(0)"-->
                            <!--class="btn btn-primary btn-submit-checkout">Thanh toán</a>-->
                            <input type="submit" class="btn btn-primary btn-submit-checkout" value="Thanh toán">
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php get_footer() ?>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function ($) {

        // City change
        $(document).delegate('#city_id', 'change', function (e) {
            e.preventDefault();
            var obj = $(this);
            var city_id = obj.val();

            $('#district_id').parent('.district-wrapper').fadeIn();
            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_post",
                    method: "GetDistrictList",
                    city_id: city_id
                },
                beforeSend: function () {
                    show_loading();
                },
                success: function (data) {
                    hide_loading();

                    if (data.status == 'success') {

                        //Clear current districts
                        $('#district_id option:gt(0)').remove();

                        var options = [];
                        $.each(data.data, function (k, v) {
                            var item = new Option(v.name, v.id);
                            options.push(item);
                        });
                        $('#district_id').append(options);
                    }
                    else {
                        swal({"title": "Error", "text": data.message, "type": "error", html: true});
                    }
                }
            });

        });


        $(document).delegate('#district_id', 'change', function (e) {
            e.preventDefault();
            var district_id = $(this).val();

            // Show service list
            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_order",
                    method: "GetShippingServiceList",
                    district_id: district_id
                },
                beforeSend: function () {
                    show_loading();
                },
                success: function (data) {
                    hide_loading();

                    if (data.status == 'success') {

                        //Clear current districts
                        $('#shipping_method option:gt(0)').remove();

                        var options = [];
                        $.each(data.data, function (k, v) {
                            var item2 = new Option(v.Name, v.ShippingServiceID);
                            options.push(item2);
                        });
                        $('#shipping_method').append(options);

                        $('.shipping-wrapper').fadeIn();
                    }
                }
            });

        });


        $(document).delegate('#shipping_method', 'change', function (e) {
            // e.preventDefault();
            calculateShippingFee();
        });


        $('.cart-item-quantity').on('focusout', function () {

            show_loading();

            // Wrapper selector
            var cart_item_wrapper = $(this).parents('.cart-item-wrapper');

            // Current quantity
            var current_quantity = $(this).val();
            // Current price
            var current_price = cart_item_wrapper.find('[data-current-price]').data('current-price');

            // Update subtotal of item
            cart_item_wrapper.find('.cart-item-calculated-total').html(numberFormat(current_price * current_quantity) + ' đ');

            // Product id
            var product_id = cart_item_wrapper.attr('data-pid');

            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_order",
                    method: "ChangeQuantity",
                    product_id: product_id,
                    quantity: current_quantity,
                    district_id: $('#district_id').val(),
                    service_id: $('#shipping_method').val()
                },
                beforeSend: function () {
                    show_loading();
                },
                success: function (data) {
                    hide_loading();

                    if (data.status == 'success') {
                        updateStatistic(data);
                    }
                    else {
                        swal({"title": "Error", "text": data.message, "type": "error", html: true});
                    }
                }
            });

        });


        //Delete product row at shopping cart
        $(document).delegate('[data-delete-product-cart]', 'click', function (e) {
            e.preventDefault();
            var obj = $(this);

            var product_id = obj.attr('data-delete-product-cart');
            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_order",
                    method: "DeleteProductCart",
                    product_id: product_id
                },
                beforeSend: function () {
                    show_loading();
                },
                success: function (data) {
                    hide_loading();
                    if (data.status == 'success') {
                        obj.parents('tr').remove();

                        deleteCarItem(product_id, data);
                    }
                    else {
                        swal({"title": "Error", "text": data.message, "type": "error", html: true});
                    }
                }
            });
        });


        // Submit form
        $('#form-checkout').validate({
            ignore: ":disabled",
            rules: {
                name: "required",
                phone: {
                    required: true,
                    number: true
                },
                email: {
                    required: true,
                    email: true
                },
                address: "required",
                city_id: "required",
                district_id: "required",
                shipping_method: "required"
            },
            messages: {
                name: "Vui lòng nhập tên của bạn.",
                phone: {
                    required: "Vui lòng nhập số điện thoại",
                    number: "Số điện thoại không đúng"
                },
                email: {
                    required: "Vui lòng nhập email",
                    email: "Email không đúng"
                },
                address: "Vui lòng nhập địa chỉ của bạn.",
                city_id: "Vui lòng chọn tỉnh thành.",
                district_id: "Vui lòng chọn quận huyện.",
                shipping_method: "Vui lòng chọn phương thức giao hàng."
            },
            errorPlacement: function (error, element) {
                element.attr('data-original-title', error.text())
                    .attr('data-toggle', 'tooltip')
                    .attr('data-placement', 'top');
                $(element).tooltip('show');
            },
            unhighlight: function (element) {
                $(element)
                    .removeAttr('data-toggle')
                    .removeAttr('data-original-title')
                    .removeAttr('data-placement')
                    .removeClass('error');
                $(element).unbind("tooltip");
            },
            submitHandler: function (form) {
                var obj = $(form);
                $.ajax({
                    url: ajaxurl,
                    type: "post",
                    dataType: 'json',
                    data: obj.serialize(),
                    beforeSend: function () {
                        show_loading();
                    },
                    success: function (data) {
                        hide_loading();

                        if (data.status == 'success') {
                            ga('send', 'event', 'Số lượng mua hàng', 'Click');
                            ga('event.send', 'event', 'order', 'click');
                            // text: "<p style='font-weight: bold;color: black'>Đặt hàng thành công, mã đơn hàng của bạn là: " + data.data.order_id + ".</p><br/>Vui lòng kiểm tra hộp thư đến hoặc hộp thư spam để xem đơn hàng.",

                            swal({
                                    title: 'Thành công',
                                    text: "<p style='font-weight: bold;color: black'>Đặt hàng thành công, mã đơn hàng của bạn là: " + data.data.order_id + ".</p><br/>Vui lòng kiểm tra hộp thư đến hoặc hộp thư spam để xem đơn hàng.",
                                    confirmButtonColor: "#88b04b",
                                    type: "success",
                                    html: true
                                },
                                function () {
                                    window.location.href = data.data.url;
                                });
                        }
                        else {
                            swal({"title": "Error", "text": data.message, "type": "error", html: true});
                        }
                    }
                });
            }
        });

    });


    function deleteCarItem(product_id, data) {
        updateStatistic(data);

        // Remove item
        $('[data-pid="' + product_id + '"]').remove();
    }


    function updateStatistic(data) {
        var total_quantity = data.data.total_quantity;
        var subtotal = data.data.subtotal;
        var shipping_fee = data.data.shipping_fee;

        // --- Header Cart
        $('.cart-total-item').html(numberFormat(total_quantity));
        // total
        $('.cart-total .amount, .cart-total-value').html(numberFormat(subtotal));

        if (!$('#district_id').val() || !$('#shipping_method').val()) {
            var total = subtotal;
            // Reset shipping fee
            $('.shipping-fee').html('...');
        } else {
            total = subtotal + shipping_fee;

            $('.shipping-fee').html(numberFormat(shipping_fee));
        }

        // Quantity
        $('.order-total-quantity').html(numberFormat(total_quantity));

        // Subtotal
        $('[data-order-total]').attr('data-order-total', subtotal).html(numberFormat(subtotal) + ' đ');

        // Final total
        $('.order-final-total').html(numberFormat(total) + ' đ');
    }


    function calculateShippingFee() {
        var to_district_code = $('#district_id').val();
        var service_id = $('#shipping_method').val();

        if (to_district_code && service_id) {
            // Get service fee
            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_order",
                    method: "GetShippingFee",
                    to_district_code: to_district_code,
                    service_id: service_id
                },
                beforeSend: function () {
                    show_loading();
                },
                success: function (data) {
                    hide_loading();

                    var current_total = $('[data-order-total]').attr('data-order-total');

                    if (data.status == 'success') {
                        $('.shipping-fee').html(numberFormat(data.data) + ' đ');

                        // Total
                        current_total = parseFloat(current_total) + parseFloat(data.data);

                        // Showing final total
                        $('.order-final-total').html(numberFormat(current_total) + ' đ');

                    } else {
                        $('.shipping-fee').html('...');
                    }

                }
            });
        }
    }
</script>

