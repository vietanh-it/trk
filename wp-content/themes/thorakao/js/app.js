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


    //Delete product row at shopping cart
    $(document).delegate('[data-delete-product-cart]', 'click', function (e) {
        e.preventDefault();
        var obj = $(this);

        var product_id = obj.data('delete-product-cart');
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
                obj.attr('disabled', true).css({'opacity': '0.5'});
                $('.btn-submit-checkout').attr('disabled', true).css({'opacity': '0.5'});
            },
            success: function (data) {
                obj.attr('disabled', false).css({'opacity': 1});
                $('.btn-submit-checkout').attr('disabled', false).css({'opacity': 1});
                if (data.status == 'success') {

                    obj.parents('tr').remove();

                    $('.cart-total-item').html(data.data.total_quantity);
                    $('.shipping-fee').html(data.data.shipping_fee);
                    $('.order-total-quantity').html(data.data.total_quantity);
                    $('.cart-total .amount, .cart-total-value').html(data.data.subtotal);

                    $('[data-order-total]').data('order-total', data.data.subtotal_raw).html(data.data.subtotal);
                    $('.order-final-total').html(data.data.total);

                    $('[data-pid="' + product_id + '"]').remove();

                }
                else {
                    swal({"title": "Error", "text": data.message, "type": "error", html: true});
                }
            }
        });
    });


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


    $('input, select', '.frm_filter ').change(function (e) {
        $('.frm_filter').submit();
    });


    $('[data-toggle]').on('click', function () {
        var objClass = $(this).data('toggle');
        $('.' + objClass).slideToggle();
    });


});