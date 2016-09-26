/**
 * Created by VietAnh on 26/05/2016.
 */

function activeMenu(menu) {
    jQuery('[data-menu]').removeClass('active');
    jQuery('[data-menu="' + menu + '"]').addClass('active');
}

var $ = jQuery.noConflict();
jQuery(document).ready(function ($) {
    // var page = $('.main-content');  // set to the main content of the page
    // $(window).mousewheel(function (event, delta, deltaX, deltaY) {
    //     if (delta < 0) page.scrollTop(page.scrollTop() + 65);
    //     else if (delta > 0) page.scrollTop(page.scrollTop() - 65);
    //     return false;
    // });




    $('#form-checkout').validate({
        rules: {
            name: "required",
            phone: "required",
            email: "required",
            address: "required",
            city_id: "required"
        },
        messages: {
            name: "Vui lòng nhập tên của bạn.",
            phone: "Vui lòng nhập số điện thoại của bạn.",
            email: "Vui lòng nhập email của bạn.",
            address: "Vui lòng nhập địa chỉ của bạn.",
            city_id: "Vui lòng chọn tỉnh thành."
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
                    $('input, button[type=submit]', obj).attr('disabled', true).css({'opacity': '0.5'});
                    $('.btn-submit-checkout').attr('disabled', true).css({'opacity': '0.5'});
                },
                success: function (data) {
                    $('input, button[type=submit]', obj).attr('disabled', false).css({'opacity': 1});
                    $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
                    if (data.status == 'success') {
                        ga('send', 'event', 'Số lượng mua hàng', 'Click');
                        ga('event.send', 'event', 'order', 'click');
                        swal({
                                title: 'Thành công',
                                text: "<p style='font-weight: bold;color: black'>Đặt hàng thành công, mã đơn hàng của bạn là: " + data.data.order_id + ".</p><br/>Vui lòng kiểm tra hộp thư đến hoặc hộp thư spam để xem đơn hàng.",
                                confirmButtonColor: "#80b501",
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
                obj.attr('disabled', true).css({'opacity': '0.5'});
                $('.btn-submit-checkout').attr('disabled', true).css({'opacity': '0.5'});
            },
            success: function (data) {
                obj.attr('disabled', false).css({'opacity': 1});
                $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
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

        if (city_id != 1) {
            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_order",
                    method: "GetShippingFee",
                    city_id: city_id,
                    district_id: $('#district_id').val(),
                    subtotal: $('[data-order-total]').attr('data-order-total')
                },
                beforeSend: function () {
                    // obj.attr('disabled', true).css({'opacity': '0.5'});
                    $('.btn-submit-checkout').attr('disabled', true).css({'opacity': '0.5'});
                },
                success: function (data) {
                    // obj.attr('disabled', false).css({'opacity': 1});
                    $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
                    if (data.status == 'success') {

                        $('.shipping-fee').html(data.data.shipping_fee_display);
                        $('.cart-total').html(data.data.total_display);
                        $('#input_shipping_fee').val(data.data.shipping_fee);
                        $('#input_cart_total').val(data.data.total);

                    }
                }
            });
        }
    });


    $(document).delegate('#district_id', 'change', function (e) {
        e.preventDefault();
        var order_total = $('[data-order-total]').attr('data-order-total');
        var district_id = $(this).val();

        if ($('#city_id').val() == 1) {
            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_order",
                    method: "GetShippingFee",
                    city_id: $('#city_id').val(),
                    district_id: $('#district_id').val(),
                    subtotal: $('[data-order-total]').attr('data-order-total')
                },
                beforeSend: function () {
                    // obj.attr('disabled', true).css({'opacity': '0.5'});
                    $('.btn-submit-checkout').attr('disabled', true).css({'opacity': '0.5'});
                },
                success: function (data) {
                    // obj.attr('disabled', false).css({'opacity': 1});
                    $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
                    if (data.status == 'success') {

                        $('.shipping-fee').html(data.data.shipping_fee_display);
                        $('.cart-total').html(data.data.total_display);
                        $('#input_shipping_fee').val(data.data.shipping_fee);
                        $('#input_cart_total').val(data.data.total);

                    }
                }
            });
        }
    });


    $('input, select', '.frm_filter ').change(function (e) {
        $('.frm_filter').submit();
    });

});