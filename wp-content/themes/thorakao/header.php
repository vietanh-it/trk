<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 09/05/2016
 * Time: 12:40 SA
 */

$cart = $_SESSION['cart'];
$product_ctrl = \TVA\Controllers\ProductController::init();

?><!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="INDEX,FOLLOW"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="dc.language" content="VN">
    <meta name="dc.source" content="http://www.thorakao.vn">
    <meta name="dc.creator" content="Thorakao"/>
    <meta name="distribution" content="Global"/>
    <meta name="revisit" content="1 days"/>
    <meta name="geo.placename" content="Vietnamese"/>
    <meta name="geo.region" content="Vietnamese"/>
    <meta name="generator" content="http://www.thorakao.vn"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta http-equiv="content-language" content="vi"/>
    <meta name="theme-color" content="#b5d96f">
    <title><?php wp_title('-', true, 'right'); ?></title>
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <!--[if lt IE 9]>
    <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css">
    <?php wp_head(); ?>
    <link rel="apple-touch-icon" sizes="57x57" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="60x60" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="76x76" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="120x120" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <!-- Place favicon.ico and apple-touch-icon.png in the root directory-->
    <script src="<?php echo THEME_URL; ?>/js/modernizr.js"></script>

    <?php if (is_admin_bar_showing()) { ?>
        <!--        <style>-->
        <!--            .menu-wrapper.navbar {-->
        <!--                top: 32px !important;-->
        <!--            }-->
        <!--        </style>-->
    <?php } ?>

    <?php $menu = 'home';
    if (is_page_template('templates/all-product.php') || is_singular('product')) {
        $menu = 'products';
    }
    elseif (is_page_template('templates/recipe.php') || is_single('recipe')) {
        $menu = 'recipe';
    }
    elseif (is_page_template('templates/beauty.php') || is_single('beauty')) {
        $menu = 'beauty';
    }
    elseif (is_page(['gioi-thieu', 'about-us'])) {
        $menu = 'aboutus';
    } ?>

    <script>
        var $ = jQuery.noConflict();
        jQuery(document).ready(function ($) {
            activeMenu('<?php echo $menu ?>');
        });
    </script>

    <!--@formatter:off-->
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,400italic,300,300italic,700&subset=latin,vietnamese' rel='stylesheet' type='text/css'>
    <script>
        (function (i, s, o, g, r, a, m) {
            i['GoogleAnalyticsObject'] = r;
            i[r] = i[r] || function () {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
        })(window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

        ga('create', 'UA-74444133-2', 'thorakao.com');
        ga('require', 'displayfeatures');
        ga('send', 'pageview');

    </script>

    <!-- Facebook Pixel Code -->
    <script>
        !function (f, b, e, v, n, t, s) {
            if (f.fbq)return;
            n = f.fbq = function () {
                n.callMethod ?
                    n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq)f._fbq = n;
            n.push = n;
            n.loaded = !0;
            n.version = '2.0';
            n.queue = [];
            t = b.createElement(e);
            t.async = !0;
            t.src = v;
            s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window,
            document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

        fbq('init', '1301984116497345');
        fbq('track', "PageView");</script>
    <noscript>
        <img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1301984116497345&ev=PageView&noscript=1"/>
    </noscript>
    <!-- End Facebook Pixel Code -->
    <!--@formatter:on-->

</head>
<body <?php body_class(); ?>>
<!--[if lt IE 10]>
<p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
                                                                                                                       your browser</a> to improve your experience.
</p>
<![endif]-->
<header>

    <div class="container">
        <div class="row">

            <div class="col-md-4">
                <ul class="nav navbar-nav top-left-menu">
                    <?php pll_the_languages(['display_names_as' => 'slug']); ?>
                </ul>
            </div>

            <div class="col-md-4 text-center">
                <div class="logo"><a href="<?php echo WP_SITEURL; ?>" title="logo"><img
                            src="<?php echo THEME_URL; ?>/images/logo.png" alt="logo" height="100"></a></div>
            </div>

            <div class="col-md-4">
                <div class="cart-top">
                    <a href="<?php echo WP_SITEURL . (pll_current_language('slug') == 'vi') ? '/gio-hang' : '/en/shopping-cart'; ?>"
                       class="cart-contents">
                        <span
                            class="cart-total-item"><?php echo empty($_SESSION['total_quantity']) ? 0 : intval($_SESSION['total_quantity']); ?></span>
                        <span class="cart-total">
                            <span class="amount">
                                <?php echo empty($_SESSION['subtotal']) ? 0 . 'đ' : number_format($_SESSION['subtotal']) . 'đ'; ?>
                            </span>
                        </span>
                    </a>
                    <div class="cart-top-items">
                        <table class="table table-striped table-hover">

                            <?php
                            $price_total = 0;
                            $price_subtotal = [];
                            if ($cart) {
                                foreach ($cart as $v) {
                                    $product_info = $product_ctrl->getProductInfo($v['product_id']);
                                    $price_subtotal[] = (intval($product_info->price) * intval($v['quantity']));
                                    ?>
                                    <tr>

                                        <td><img src="<?php echo $product_info->featured_image ?>" width="60"
                                                 height="60"></td>

                                        <td>
                                            <div><i><?php echo $product_info->post_title ?></i></div>
                                            <div style="font-weight: bold;">
                                                <span class="quantity">
                                                    <?php echo $v['quantity'] ?>
                                                </span>
                                                <span class="cart-item-divide">
                                                    x
                                                </span>
                                                <span class="price">
                                                    <?php echo number_format($product_info->price) . 'đ' ?>
                                                </span>
                                            </div>
                                        </td>

                                        <td>
                                            <a href="javascript:void(0)" data-delete-product-cart="<?php echo $product_info->ID; ?>">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                        </td>

                                    </tr>

                                <?php }
                            }

                            $price_total = array_sum($price_subtotal);
                            ?>

                            <tr>
                                <td colspan="2"><b>Tổng tiền</b></td>
                                <td><b class="cart-total-value"><?php echo number_format($price_total) . 'đ' ?></b></td>
                            </tr>

                        </table>
                        <div class="text-center">
                            <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/gio-hang' : '/en/shopping-cart'; ?>"
                               class="btn btn-primary">
                                <i class="fa fa-shopping-cart"></i>Thanh toán
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="menu-wrapper navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <a href="<?php echo WP_SITEURL . '/gio-hang'; ?>" class="btn btn-primary cart-btn-mobile">Giỏ hàng</a>
                <button type="button" data-toggle="collapse" data-target="#main_nav" class="navbar-toggle collapsed">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <div class="main_nav collapse navbar-collapse">
                <?php if (pll_current_language('slug') == 'vi') { ?>
                    <ul class="nav navbar-nav">
                        <li data-menu="home"><a href="<?php echo WP_SITEURL; ?>">Trang chủ</a></li>
                        <li data-menu="products"><a href="<?php echo WP_SITEURL . '/tat-ca-san-pham'; ?>">Sản phẩm</a>
                        </li>
                        <li data-menu="recipe"><a href="<?php echo WP_SITEURL . '/thanh-phan'; ?>">Thành phần</a></li>
                        <li data-menu="beauty"><a href="<?php echo WP_SITEURL . '/lam-dep'; ?>">Làm đẹp</a></li>
                        <li data-menu="aboutus"><a href="<?php echo WP_SITEURL . '/gioi-thieu'; ?>">Giới thiệu</a>
                        </li>
                    </ul>
                    <form action="<?php echo esc_url(home_url('/')) ?>" method="get" role="search"
                          accept-charset="utf-8"
                          class="navbar-form navbar-right form-search">
                        <div class="form-group">
                            <input name="s" type="text" placeholder="Nhập từ khóa tìm kiếm" class="form-control"
                                   value="<?php echo get_search_query() ?>">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </form>
                <?php }
                else { ?>
                    <ul class="nav navbar-nav">
                        <li data-menu="home"><a href="<?php echo WP_SITEURL . '/en/'; ?>">Home</a></li>
                        <li data-menu="products"><a href="<?php echo WP_SITEURL . '/en/products'; ?>">All Product</a>
                        </li>
                        <li data-menu="recipe"><a href="<?php echo WP_SITEURL . '/en/recipe'; ?>">Recipe</a></li>
                        <li data-menu="beauty"><a href="<?php echo WP_SITEURL . '/en/beauty'; ?>">Beauty</a></li>
                        <li data-menu="aboutus"><a href="<?php echo WP_SITEURL . '/en/about-us'; ?>">About us</a></li>
                    </ul>
                    <form method="get" class="navbar-form navbar-right form-search">
                        <div class="form-group">
                            <input name="s" type="text" placeholder="Search" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-search"></i></button>
                    </form>
                <?php } ?>
            </div>
        </div>
    </div>

</header>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function ($) {

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
            // End Ajax

        });


        // Menu
        $('[data-toggle]').on('click', function () {
            var objClass = $(this).data('toggle');
            $('.' + objClass).slideToggle();
        });

    });
</script>