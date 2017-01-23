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


    $('input, select', '.frm_filter ').change(function (e) {
        $('.frm_filter').submit();
    });


    $('[data-toggle]').on('click', function () {
        var objClass = $(this).data('toggle');
        $('.' + objClass).slideToggle();
    });

});


// Format number
function numberFormat(x) {
    if (isNaN(x))return "";

    n = x.toString().split('.');
    return n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",") + (n.length > 1 ? "." + n[1] : "");
}


function translateServiceName(service_id) {
    var result = '';

    switch (service_id.toString()) {
        case '17':
            result = 'Giao hàng siêu tốc';
            break;
        case '18':
            result = 'Giao hàng tiết kiệm';
            break;
        case '53319':
            result = 'Giao trong 6 giờ';
            break;
        case '53320':
            result = 'Giao trong 1 ngày';
            break;
        case '53321':
            result = 'Giao trong 2 ngày';
            break;
        case '53322':
            result = 'Giao trong 3 ngày';
            break;
        case '53323':
            result = 'Giao trong 4 ngày';
            break;
        case '53324':
            result = 'Giao trong 5 ngày';
            break;
        case '53325':
            result = 'Prime';
            break;
        case '53326':
            result = 'Giao trong 4 giờ';
            break;
        case '53327':
            result = 'Giao trong 6 ngày';
            break;
        case '53329':
            result = 'Giao trong 60 phút';
            break;
        case '53330':
            result = 'Chuyển phát cá nhân';
            break;
        case '53339':
            result = '266';
            break;
        case '53346':
            result = 'Thanh toán khi nhận hàng';
            break;
        case '53347':
            result = 'Dịch vụ ứng tiền 60P';
            break;
    }

    return result;
}