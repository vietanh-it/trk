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