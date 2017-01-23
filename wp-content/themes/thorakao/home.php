<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 23/05/2016
 * Time: 11:28 CH
 */

use \TVA\Controllers\ProductController;
use TVA\Controllers\PostController;

$product_ctrl = ProductController::init();
$post_ctrl = PostController::init();


$combo_term_taxonomy_id = pll_get_term_translations(5);
$duongda_term_taxonomy_id = pll_get_term_translations(2);
$duongtoc_term_taxonomy_id = pll_get_term_translations(3);
$duongthe_term_taxonomy_id = pll_get_term_translations(4);

$combo_list = $product_ctrl->getProductList([
    'term_taxonomy_id'     => $combo_term_taxonomy_id[pll_current_language()],
    'is_homepage_featured' => true,
    'limit'                => 8
]);
$duongda_list = $product_ctrl->getProductList([
    'term_taxonomy_id'     => $duongda_term_taxonomy_id[pll_current_language()],
    'is_homepage_featured' => true,
    'limit'                => 8
]);
$duongtoc_list = $product_ctrl->getProductList([
    'term_taxonomy_id'     => $duongtoc_term_taxonomy_id[pll_current_language()],
    'is_homepage_featured' => true,
    'limit'                => 8
]);
$duongthe_list = $product_ctrl->getProductList([
    'term_taxonomy_id'     => $duongthe_term_taxonomy_id[pll_current_language()],
    'is_homepage_featured' => true,
    'limit'                => 8
]);

if (pll_current_language() == 'vi') {
    $duongda_link = get_term_link(2);
    $duongtoc_link = get_term_link(3);
    $duongthe_link = get_term_link(4);
    $combo_link = get_term_link(5);
}
else {
    $duongda_link = get_term_link(pll_get_term(2));
    $duongtoc_link = get_term_link(pll_get_term(3));
    $duongthe_link = get_term_link(pll_get_term(4));
    $combo_link = get_term_link(pll_get_term(5));
}

$best_sale_product_list = $product_ctrl->getProductList([
    'limit'        => 8,
    'is_favourite' => true
]);

$beauty_list = $post_ctrl->getList(['limit' => 8]);

get_header(); ?>

<div class="slider owl-carousel">
    <!--<div class="item"><a href="javascript:void(0)"><img-->
    <!--src="--><?php //echo THEME_URL . '/images/banner/banner-trungthu.jpg'; ?><!--"></a></div>-->
    <div class="item">
        <a href="<?php echo WP_SITEURL . '/theo-loai/that-don-gian-de-dat-hang-va-so-huu-san-pham-thorakao-chinh-hang-chat-luong'; ?>">
            <img src="<?php echo THEME_URL . '/images/banner/banner3.png'; ?>"></a></div>
</div>

<div class="main-content">
    <section class="container">

        <div class="nav tab-list text-center">
            <a href="#tab_duongda"><?php echo pll_current_language() == 'vi' ? 'Dưỡng da' : 'Skin care' ?></a>
            <a href="#tab_duongtoc"><?php echo pll_current_language() == 'vi' ? 'Dưỡng tóc' : 'Hair care' ?></a>
            <a href="#tab_duongthe"><?php echo pll_current_language() == 'vi' ? 'Dưỡng thể' : 'Body care' ?></a>
            <a href="#tab_combo"><?php echo pll_current_language() == 'vi' ? 'Bộ Sản Phẩm' : 'Combo' ?></a>
        </div>

        <div class="tab-contents">

            <div id="tab_duongda">
                <?php
                foreach ($duongda_list as $key => $item) {
                    if ($key == 0) {
                        echo '<div class="row">';
                    }
                    else {
                        if ($key % 4 == 0) {
                            echo '</div>';
                            echo '<div class="row">';
                        }
                    } ?>

                    <div class="col-md-3">
                        <div class="product__wrapper text-center">
                            <a href="<?php echo $item->permalink; ?>" class="product__img">
                                <img src="<?php echo $item->featured_image; ?>" class="br-5 img-responsive">
                                <span class="view-detail btn"><i
                                        class="fa fa-search"></i><?php echo pll_current_language() == 'vi' ? 'Xem chi tiết' : 'View detail' ?></span>
                                <span class="add-to-cart btn" data-product-id="<?php echo $item->ID; ?>">
                                        <i class="fa fa-shopping-cart"></i><?php echo pll_current_language() == 'vi' ? 'Thêm vào giỏ hàng' : 'Add to cart' ?>
                                    </span>
                            </a>
                            <a href="<?php echo $item->permalink ?>"
                               class="product__title"><?php echo $item->post_title; ?></a>
                            <div class="product__desc">
                                <i><?php echo $item->post_excerpt; ?></i>
                            </div>
                            <div class="product__price"><?php echo $item->price_display ?></div>
                        </div>
                    </div>

                    <?php
                    if ($key == (count($duongda_list) - 1)) {
                        echo '</div>'; ?>
                        <div class="clearfix"></div>
                        <div class="text-center" style="margin-top: -10px;">
                            <a href="<?php echo $duongda_link ?>" class="btn btn-default"
                               style="text-shadow: none;"><?php echo pll_current_language() == 'vi' ? 'Xem tiếp' : 'View more' ?></a>
                        </div>
                    <?php }
                } ?>

            </div>

            <div id="tab_duongtoc">
                <?php foreach ($duongtoc_list as $key => $item) {
                    if ($key == 0) {
                        echo '<div class="row">';
                    }
                    else {
                        if ($key % 4 == 0) {
                            echo '</div>';
                            echo '<div class="row">';
                        }
                    } ?>

                    <div class="col-md-3">
                        <div class="product__wrapper text-center">
                            <a href="<?php echo $item->permalink; ?>" class="product__img">
                                <img src="<?php echo $item->featured_image; ?>" class="br-5 img-responsive">
                                <span class="view-detail btn"><i
                                        class="fa fa-search"></i><?php echo pll_current_language() == 'vi' ? 'Xem chi tiết' : 'View detail' ?></span>
                                <span class="add-to-cart btn" data-product-id="<?php echo $item->ID; ?>">
                                        <i class="fa fa-shopping-cart"></i><?php echo pll_current_language() == 'vi' ? 'Thêm vào giỏ hàng' : 'Add to cart' ?>
                                    </span>
                            </a>
                            <a href="<?php echo $item->permalink ?>"
                               class="product__title"><?php echo $item->post_title; ?></a>
                            <div class="product__desc">
                                <i><?php echo $item->post_excerpt; ?></i>
                            </div>
                            <div class="product__price"><?php echo $item->price_display ?></div>
                        </div>
                    </div>

                    <?php
                    if ($key == (count($duongtoc_list) - 1)) {
                        echo '</div>'; ?>
                        <div class="clearfix"></div>
                        <div class="text-center" style="margin-top: -10px;">
                            <a href="<?php echo $duongtoc_link ?>" class="btn btn-default"
                               style="text-shadow: none;"><?php echo pll_current_language() == 'vi' ? 'Xem tiếp' : 'View more' ?></a>
                        </div>
                    <?php }
                } ?>

            </div>

            <div id="tab_duongthe">
                <?php foreach ($duongthe_list as $key => $item) {
                    if ($key == 0) {
                        echo '<div class="row">';
                    }
                    else {
                        if ($key % 4 == 0) {
                            echo '</div>';
                            echo '<div class="row">';
                        }
                    } ?>

                    <div class="col-md-3">
                        <div class="product__wrapper text-center">
                            <a href="<?php echo $item->permalink; ?>" class="product__img">
                                <img src="<?php echo $item->featured_image; ?>" class="br-5 img-responsive">
                                <span class="view-detail btn"><i class="fa fa-search"></i>
                                    <?php echo pll_current_language() == 'vi' ? 'Xem chi tiết' : 'View detail' ?>
                                    </span>
                                <span class="add-to-cart btn" data-product-id="<?php echo $item->ID; ?>">
                                        <i class="fa fa-shopping-cart"></i>
                                    <?php echo pll_current_language() == 'vi' ? 'Thêm vào giỏ hàng' : 'Add to cart' ?>
                                    </span>
                            </a>

                            <a href="<?php echo $item->permalink ?>" class="product__title">
                                <?php echo $item->post_title; ?>
                            </a>

                            <div class="product__desc">
                                <i><?php echo $item->post_excerpt; ?></i>
                            </div>
                            <div class="product__price"><?php echo $item->price_display ?></div>
                        </div>
                    </div>

                    <?php
                    if ($key == (count($duongthe_list) - 1)) {
                        echo '</div>'; ?>
                        <div class="clearfix"></div>
                        <div class="text-center" style="margin-top: -10px;">
                            <a href="<?php echo $duongthe_link ?>" class="btn btn-default"
                               style="text-shadow: none;"><?php echo pll_current_language() == 'vi' ? 'Xem tiếp' : 'View more' ?></a>
                        </div>
                    <?php }
                } ?>

            </div>

            <div id="tab_combo">
                <?php foreach ($combo_list as $key => $item) {
                    if ($key == 0) {
                        echo '<div class="row">';
                    }
                    else {
                        if ($key % 4 == 0) {
                            echo '</div>';
                            echo '<div class="row">';
                        }
                    } ?>

                    <div class="col-md-3">
                        <div class="product__wrapper text-center">
                            <a href="<?php echo $item->permalink; ?>" class="product__img">
                                <img src="<?php echo $item->featured_image; ?>" class="br-5 img-responsive">
                                <span class="view-detail btn"><i
                                        class="fa fa-search"></i><?php echo pll_current_language() == 'vi' ? 'Xem chi tiết' : 'View detail' ?></span>
                                <span class="add-to-cart btn" data-product-id="<?php echo $item->ID; ?>">
                                        <i class="fa fa-shopping-cart"></i><?php echo pll_current_language() == 'vi' ? 'Thêm vào giỏ hàng' : 'Add to cart' ?>
                                    </span>
                            </a>
                            <a href="<?php echo $item->permalink ?>"
                               class="product__title"><?php echo $item->post_title; ?></a>
                            <div class="product__desc">
                                <i><?php echo $item->post_excerpt; ?></i>
                            </div>
                            <div class="product__price"><?php echo $item->price_display ?></div>
                        </div>
                    </div>

                    <?php
                    if ($key == (count($combo_list) - 1)) {
                        echo '</div>'; ?>
                        <div class="clearfix"></div>
                        <div class="text-center" style="margin-top: -10px;">
                            <a href="<?php echo $combo_link ?>" class="btn btn-default" style="text-shadow: none;">
                                <?php echo pll_current_language() == 'vi' ? 'Xem tiếp' : 'View more' ?>
                            </a>
                        </div>
                    <?php }
                } ?>

            </div>

        </div>
    </section>

    <section class="container">
        <h3 class="cross-title">
            <?php if (pll_current_language('slug') == 'vi') { ?>
                <a href="<?php echo WP_SITEURL . '/tat-ca-san-pham' ?>">Sản phẩm yêu thích</a>
            <?php }
            else { ?>
                <a href="<?php echo WP_SITEURL . '/en/products' ?>">Featured Products</a>
            <?php } ?>
        </h3>
        <div class="row">
            <div class="col-xs-12">
                <div class="owl-carousel carousel">

                    <?php foreach ($best_sale_product_list as $key => $item) { ?>
                        <div class="item">
                            <div class="product__wrapper text-center">
                                <a href="<?php echo $item->permalink; ?>" class="img-repsonsive">
                                    <img src="<?php echo $item->square_image; ?>" class="br-5">
                                </a>
                                <a href="<?php echo $item->permalink; ?>"
                                   class="product__title"><?php echo $item->post_title; ?></a>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>
    </section>

    <section class="container">
        <?php if (pll_current_language('slug') == 'vi') { ?>
            <h3 class="cross-title"><a href="<?php echo WP_SITEURL . '/lam-dep' ?>">Tin Tức</a></h3>
        <?php }
        else { ?>
            <h3 class="cross-title"><a href="<?php echo WP_SITEURL . '/en/beauty' ?>">Beauty News</a></h3>
        <?php } ?>

        <div class="col-md-12">
            <?php foreach ($beauty_list as $key => $item) {
                if ($key == 0) {
                    echo '<div class="row no-padding no-margin">';
                }
                else {
                    if ($key % 4 == 0) {
                        echo '</div>';
                        echo '<div class="row no-padding no-margin">';
                    }
                } ?>

                <div class="col-md-3 no-padding outline-normal home-news-image">
                    <a href="<?php echo $item->permalink; ?>">
                        <img src="<?php echo $item->square_image; ?>" height="300" width="300"
                             class="img-responsive">
                    </a>
                </div>

                <?php
                if ($key == (count($beauty_list) - 1)) {
                    echo '</div>';
                }
            } ?>
        </div>
    </section>

</div>

<?php get_footer(); ?>

<?php
$is_opening = true;
$expired_at = time() + 120;

if (!isset($_SESSION['is_opened_banner'])) {
    $_SESSION['is_opened_banner'] = $expired_at;
}
elseif (date('His') < date('His', $_SESSION['is_opened_banner'])) {
    $is_opening = false;
}
else {
    $_SESSION['is_opened_banner'] = $expired_at;
}
?>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function () {


        // Add to cart
        $(document).delegate('.add-to-cart', 'click', function (e) {
            e.preventDefault();
            var obj = $(this);

            var product_id = obj.attr('data-product-id');
            $.ajax({
                url: ajaxurl,
                type: "post",
                dataType: 'json',
                data: {
                    action: "trk_ajax_handler_order",
                    method: "AddToCart",
                    product_id: product_id,
                    quantity: 1
                },
                beforeSend: function () {
                    show_loading();
                },
                success: function (data) {
                    hide_loading();

                    if (data.status == 'success') {
                        swal({
                                title: "Thêm vào giỏ hàng thành công.",
                                text: "<p style='font-weight: bold;color: #88b04b'>Bạn có muốn xem giỏ hàng?</p>",
                                type: "success",
                                showCancelButton: true,
                                confirmButtonColor: "#88b04b",
                                confirmButtonText: "Xem giỏ hàng",
                                closeOnConfirm: false,
                                cancelButtonText: "Mua tiếp",
                                html: true
                            },
                            function (is_confirm) {
                                if (is_confirm) {
                                    swal.close();
                                    window.location.href = location.protocol + '//' + location.host + '/gio-hang';
                                    // var win = window.open(location.protocol + '//' + location.host + '/gio-hang', '_blank');
                                    // win.focus();
                                } else {
                                    window.location.reload();
                                }
                            }
                        );

                    }
                    else {
                        if (data.message) {
                            swal({"title": "Error", "text": data.message, "type": "error", html: true});
                        } else if (data.data) {
                            swal({"title": "Thất bại", "text": data.data, "type": "error", html: true});
                        }
                    }
                }
            });
        });


        <?php if ($is_opening) { ?>

        // Get latest banner
        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'post',
            dataType: 'json',
            data: {
                action: 'trk_ajax_handler_banner',
                method: 'GetLatestBanner'
            },
            success: function (data) {
                if (data) {
                    if (data.is_enabled == 1) {
                        if (data.image[0]) {
                            var html = "<a id='popup_banner' href='" + data.link + "'><img style='max-width: 80vw; height: auto;' width=" + data.image[1] + " src='" + data.image[0] + "'></a>";
                            $.fancybox({
                                content: html,
                                type: 'image',
                                padding: 0,
                                overlay: {
                                    showEarly: false
                                }
                            });
                        }
                    }
                }
            }
        });

        <?php } ?>

    });
</script>
