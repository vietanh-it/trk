<?php
/**
 * Created by PhpStorm.
 * User: Vo sy dao
 * Date: 5/25/2016
 * Time: 2:09 PM
 * Template name: Shopping bag - step 1 guest info
 */
get_header();

$productCtr = \TVA\Controllers\ProductController::init();
$post_ctrl = \TVA\Controllers\PostController::init();
$m_order = \TVA\Models\Orders::init();

$cart = $m_order->getCart();

$city_list = $post_ctrl->getCityList(); ?>

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

                    <form action="javascript:void(0)" method="post" id="form-checkout-step-1">
                        <h2 class="title page-title">Thanh Toán</h2>


                        <div class="nav tab-list">
                            <a href="javascript:void(0)">Bước 1: Thông tin khách hàng</a>
                        </div>

                        <div class="row">

                            <div class="col-md-12">
                                <div class="checkout-info">
                                    <div class="title checkout-title">
                                        <i class="fa fa-map-marker mr-10"></i>Địa chỉ nhận hàng
                                    </div>
                                    <div class="checkout-content">
                                        <div class="checkout-info-form">
                                            <div class="uk-form">
                                                <div class="uk-form-row">
                                                    <label>Họ tên:</label>
                                                    <input type="text" placeholder="Nhập họ tên" name="name" value="<?php echo $cart->name; ?>">
                                                </div>
                                                <div class="uk-form-row">
                                                    <label>Số điện thoại:</label>
                                                    <input type="text" placeholder="Nhập số điện thoại" name="phone" value="<?php echo $cart->phone; ?>">
                                                </div>
                                                <div class="uk-form-row">
                                                    <label>Email:</label>
                                                    <input type="text" placeholder="Nhập email" name="email" value="<?php echo $cart->email; ?>">
                                                </div>
                                                <div class="uk-form-row">
                                                    <label for="city_id">Thành phố:</label>
                                                    <select id="city_id" name="city_id">
                                                        <option value="">--- Chọn thành phố ---</option>
                                                        <?php foreach ($city_list as $key => $item) {
                                                            $selected = ($item->id == $cart->city_id) ? 'selected' : '';
                                                            echo "<option value='{$item->id}' {$selected}>{$item->name}</option>";
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="uk-form-row district-wrapper" style="display: none;">
                                                    <label for="district_id">Quận / huyện:</label>
                                                    <select id="district_id" name="district_id">
                                                        <option value="">--- Chọn quận/huyện ---</option>
                                                    </select>
                                                </div>
                                                <div class="uk-form-row">
                                                    <label>Địa chỉ:</label>
                                                    <input type="text" placeholder="Nhập địa chỉ giao hàng" name="address" value="<?php echo $cart->address; ?>">
                                                </div>
                                                <div class="uk-form-row">
                                                    <label>Lời nhắn:</label>
                                                    <input type="text" placeholder="Ví dụ: Giao hàng trong giờ hành chính"
                                                           name="note" value="<?php echo $cart->note; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="text-center">
                            <input type="submit" value="Tiếp theo" class="btn btn-primary" style="margin-top: 50px;">
                        </div>

                        <input type="hidden" name="action" value="trk_ajax_handler_order">
                        <input type="hidden" name="method" value="CartGuestInfo">

                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        var $ = jQuery.noConflict();
        $(document).ready(function ($) {

            // Form checkout - guest info
            $('#form-checkout-step-1').validate({
                rules: {
                    name: "required",
                    phone: "required",
                    email: "required",
                    address: "required",
                    city_id: "required",
                    district_id: "required"
                },
                messages: {
                    name: "Vui lòng nhập tên của bạn.",
                    phone: "Vui lòng nhập số điện thoại của bạn.",
                    email: "Vui lòng nhập email của bạn.",
                    address: "Vui lòng nhập địa chỉ của bạn.",
                    city_id: "Vui lòng chọn tỉnh thành.",
                    district_id: "Vui lòng chọn quận huyện."
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
                            switch_loading(true);
                        },
                        success: function (data) {
                            switch_loading(false);
                            if (data.status == 'success') {
                                // ga('send', 'event', 'Số lượng mua hàng', 'Click');
                                // ga('event.send', 'event', 'order', 'click');

                                window.location.href = '<?php echo WP_SITEURL ?>/gio-hang/tao-don-hang';
                            }
                            else {
                                swal({"title": "Error", "text": data.message, "type": "error", html: true});
                            }
                        }
                    });
                }
            });


            // City change
            var is_changing = false;
            $(document).delegate('#city_id', 'change', function (e) {
                e.preventDefault();
                var obj = $(this);
                var city_id = obj.val();

                if (city_id == 1) {
                    $('#district_id').parent('.district-wrapper').fadeIn();
                } else {
                    $('#district_id').parent('.district-wrapper').fadeOut();
                }

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
                        switch_loading(true);
                        is_changing = true;
                    },
                    success: function (data) {
                        switch_loading(false);
                        if (data.status == 'success') {

                            //Clear current districts
                            $('#district_id option:gt(0)').remove();

                            var options = [];
                            $.each(data.data, function (k, v) {
                                var item = new Option(v.name, v.id);
                                options.push(item);
                            });
                            $('#district_id').append(options);
                            is_changing = false;
                        }
                        else {
                            swal({"title": "Error", "text": data.message, "type": "error", html: true});
                        }
                    }
                });

            });


            // District change
            // $(document).delegate('#district_id', 'change', function (e) {
            //     e.preventDefault();
            //     var order_total = $('[data-order-total]').attr('data-order-total');
            //     var district_id = $(this).val();
            //
            //     if ($('#city_id').val() == 1) {
            //         $.ajax({
            //             url: ajaxurl,
            //             type: "post",
            //             dataType: 'json',
            //             data: {
            //                 action: "trk_ajax_handler_order",
            //                 method: "GetShippingFee",
            //                 city_id: $('#city_id').val(),
            //                 district_id: $('#district_id').val(),
            //                 subtotal: $('[data-order-total]').attr('data-order-total')
            //             },
            //             beforeSend: function () {
            //                 // obj.attr('disabled', true).css({'opacity': '0.5'});
            //                 $('.btn-submit-checkout').attr('disabled', true).css({'opacity': '0.5'});
            //             },
            //             success: function (data) {
            //                 // obj.attr('disabled', false).css({'opacity': 1});
            //                 $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
            //                 if (data.status == 'success') {
            //
            //                     $('.shipping-fee').html(data.data.shipping_fee_display);
            //                     $('.cart-total').html(data.data.total_display);
            //                     $('#input_shipping_fee').val(data.data.shipping_fee);
            //                     $('#input_cart_total').val(data.data.total);
            //
            //                 }
            //             }
            //         });
            //     }
            // });


            <?php if (!empty($cart->district_id)) { ?>
            $('#city_id').change();
            var interval = setInterval(function () {
                if (!is_changing) {
                    $('#district_id').val(<?php echo $cart->district_id; ?>);
                    clearInterval(interval);
                }
            }, 1000);
            <?php } ?>
        });
    </script>

<?php get_footer();