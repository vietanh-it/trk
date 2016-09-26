var $ = jQuery.noConflict();
jQuery(document).ready(function ($) {
    //Menu scrolling
    var menu = jQuery('.menu-wrapper:not(.navbar-fixed-top)');
    var menu_fixed = jQuery('.menu-wrapper.navbar-fixed-top');
    var back_to_top = jQuery('.back-to-top');
    jQuery(window).scroll(function () {
        setTimeout(function () {
            var pos = jQuery(this).scrollTop();
            if (pos > 300) {
                //sidebar scroll
                var main_content_height = jQuery('.main-content').height();
                var sidebar_height = jQuery('.sidebar-scroll').height();
                jQuery('.sidebar-scroll').addClass('scrolling');
                if (pos < (main_content_height - sidebar_height)) {
                    jQuery('.sidebar-scroll').removeClass('stop').css('top', 85);
                } else {
                    jQuery('.sidebar-scroll').addClass('stop').css('top', (main_content_height - sidebar_height) + "px");
                }

                //back to top
                back_to_top.fadeIn();

                //menu
                menu.css({
                    'position': 'fixed',
                    'top': 0,
                    'left': 0,
                    'width': '100%',
                    'z-index': 5
                });
            } else {
                jQuery('.sidebar-scroll').removeClass('scrolling');
                menu.css({
                    'position': 'relative'
                });
                back_to_top.fadeOut();
            }
        }, 200);
    });

    jQuery('.back-to-top').click(function () {
        jQuery('html,body').clearQueue().animate({
            scrollTop: 0
        }, 500);
    });

    //Default open tab
    var active_tab = jQuery('.tab-list .active').attr('href');
    if (!active_tab) {
        active_tab = jQuery('.tab-list a:first-child').addClass('active').attr('href');
    }
    showTab(active_tab);

    //Tab click
    jQuery('.tab-list a').click(function (e) {
        jQuery('.tab-list a').removeClass('active');
        jQuery(this).addClass('active');
        showTab(jQuery(this).attr('href'));
        e.preventDefault();
    });

    // Xem chi tiết
    jQuery('[data-product-id] .view-detail').click(function (e) {
        e.preventDefault();
        // var product_id = jQuery(this).parent('[data-product-id]').data('product-id');
        var href = jQuery(this).parent('a').attr('href');
        window.location.href = href;
    });

    jQuery('.magnific-popup').magnificPopup();

    jQuery('.image-popup').magnificPopup({
        type: 'image',
        closeOnContentClick: true,
        closeBtnInside: false,
        fixedContentPos: true,
        mainClass: 'mfp-no-margins mfp-with-zoom', // class to remove default margin from left and right side
        gallery: {
            enabled: true,
            navigateByImgClick: true,
            preload: [0, 1] // Will preload 0 - before current, and 1 after the current image
        },
        image: {
            tError: '<a href="%url%">The image #%curr%</a> could not be loaded.',
            titleSrc: function (item) {
                return item.el.attr('title');
            },
            verticalFit: true
        }
    });

    jQuery('.owl-carousel.slider').owlCarousel({
        items: 1,
        navigation: true,
        loop: true
    });

    jQuery('.owl-carousel.single').owlCarousel({
        navigation: true,
        slideSpeed: 300,
        paginationSpeed: 400,
        items: 1,
        loop: true
    });

    jQuery('.owl-carousel.carousel').owlCarousel({
        items: 4, //4 items above 1000px browser width
        loop: true,
        navigation: true,
        center: false,
        margin: 25,
        responsive: {
            900: {
                items: 4
            },
            600: {
                items: 2
            },
            320: {
                items: 1
            }
        }
    });

    jQuery('.owl-carousel.carousel3').owlCarousel({
        items: 3, //4 items above 1000px browser width
        loop: true,
        navigation: true,
        center: false,
        margin: 25,
        responsive: {
            900: {
                items: 3
            },
            600: {
                items: 2
            },
            320: {
                items: 1
            }
        }
    });

    jQuery('.detail__desc').mCustomScrollbar({
        theme: 'dark',
        axis: 'y',
        setHeight: 200
    });

});

function showTab(id) {
    $('.tab-contents > div').hide();
    $('.tab-contents ' + id).fadeIn('fast');
}