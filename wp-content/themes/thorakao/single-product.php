<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 09/05/2016
 * Time: 12:40 SA
 */

use TVA\Controllers\PostController;
use TVA\Controllers\ProductController;

$post_ctrl = PostController::init();
$product_ctrl = ProductController::init();

global $post;

$product_detail = $post_ctrl->getPost($post->ID);

$term = get_queried_object();
//var_dump($term);
$related_products = $product_ctrl->getProductList(['term_taxonomy_id' => $term->term_taxonomy_id, 'limit' => 6]);

$order_ctrl = \TVA\Controllers\OrdersController::init();
//var_dump(pll__('Products'), pll_translate_string('Products', 'vi'));
//var_dump($product_detail);

get_header(); ?>

    <div class="breadcrumb no-padding">
        <div class="container">
            <ul class="no-padding no-margin">
                <li>
                    <a href="<?php echo WP_SITEURL; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Trang chủ' : 'Home'; ?>
                    </a>
                    <span class="slash">/</span>
                </li>
                <li>
                    <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/en/products' : '/tat-ca-san-pham'; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Sản Phẩm' : 'Products'; ?>
                    </a>
                    <span class="slash">/</span>
                </li>
                <li class="last">
                    <a href="<?php echo $product_detail->permalink ?>"><?php echo $product_detail->post_title; ?></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php get_sidebar(); ?>
                </div>

                <div class="col-md-9 detail no-padding-left">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="detail__title mt-0"><?php echo $product_detail->post_title; ?></h3>
                        </div>
                    </div>
                    <div class="row mt-20">
                        <div class="col-md-6">
                            <div class="detail__images">
                                <div class="owl-carousel single">

                                    <div class="item">
                                        <a href="<?php echo $product_detail->featured_image; ?>"
                                           title="Thorakao - Chi tiết sản phẩm" class="image-popup">
                                            <img src="<?php echo $product_detail->featured_image; ?>"
                                                 alt="featured image">
                                        </a>
                                    </div>

                                    <?php if (!empty($product_detail->images)) {
                                        $other_images = unserialize($product_detail->images);
                                        foreach ($other_images as $img) {
                                            $img_src = wp_get_attachment_image_src($img, 'featured-image');
                                            if ($img_src) { ?>
                                                <div class="item">
                                                    <a href="<?php echo $img_src[0]; ?>"
                                                       title="Thorakao - Chi tiết sản phẩm"
                                                       class="image-popup">
                                                        <img src="<?php echo $img_src[0]; ?>" alt="featured image">
                                                    </a>
                                                </div>
                                            <?php }
                                        }
                                    } ?>

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-10">
                                Giá:
                                <b class="produc-detail__price ml-10"><?php echo $product_detail->price_display; ?></b>
                            </div>
                            <div class="mb-10">
                                <?php echo pll_current_language() == 'vi' ? 'Tình trạng' : 'Availability'; ?>:
                                <b class="produc-detail__status ml-10">
                                    <?php if (pll_current_language() == 'vi') {
                                        echo ($product_detail->status == 1) ? 'Còn hàng' : 'Hết hàng';
                                    } else {
                                        echo ($product_detail->status == 1) ? 'Available' : 'Sold out';
                                    } ?>
                                </b>
                            </div>
                            <div class="mb-10">Gross Weight:
                                <b class="produc-detail__weight ml-10"><?php echo $product_detail->gross_weight . 'g'; ?></b>
                            </div>
                            <div class="detail__desc mCustomScrollbar mb-10">
                                <?php echo $product_detail->post_content; ?>
                            </div>
                            <form action="javascript:void(0)" class="mt-20 form-inline form-detail form-single-product">
                                <div style="display: none">
                                    <div class="hr-grey clearfix"></div>
                                    <div class="form-group text-center mt-20">
                                        <label for="quantity">
                                            <?php echo pll_current_language() == 'vi' ? 'Màu' : 'Color'; ?>
                                        </label>

                                        <?php foreach ($product_detail->colors as $color) {
                                            if (!empty($color)) { ?>
                                                <label style="position: relative; cursor:pointer;">
                                                    <input type="radio" value="<?php echo $color; ?>" name="color"
                                                           style="position: absolute; top: 13%; left: 28%; cursor: pointer;">
                                        <span
                                            style="height: 30px; width: 30px; -webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px; background: <?php echo $color; ?>; display: inline-block;">
                                        </span>
                                                </label>
                                            <?php }
                                        } ?>

                                    </div>
                                </div>
                                <div class="hr-grey clearfix"></div>
                                <div class="form-group text-center mt-20">
                                    <label
                                        for="quantity"><?php echo pll_current_language() == 'vi' ? 'Số lượng' : 'Quantity'; ?></label>
                                    <input class="form-control round-form-input quantity-input" type="number"
                                           name="quantity" id="quantity"
                                           value="1"> <?php echo pll_current_language() == 'vi' ? 'cái' : 'items'; ?>
                                </div>
                                <div class="form-group text-center mt-20 pull-right">
                                    <button type="submit" class="btn btn-primary btn-cart">
                                        <i class="fa fa-shopping-basket mr-5"></i><?php echo pll_current_language() == 'vi' ? 'Đặt hàng' : 'Add to cart'; ?>
                                    </button>
                                </div>
                                <div class="hr-grey clearfix mt-20"></div>

                                <input type="hidden" name="action" value="trk_ajax_handler_order">
                                <input type="hidden" name="method" value="AddToCart">
                                <input type="hidden" name="product_id" value="<?php echo $product_detail->ID; ?>">
                            </form>
                        </div>
                    </div>
                    <div class="row mt-20">
                        <div class="col-md-12">
                            <div class="nav tab-list">
                                <a href="#tab_ingredient"><?php echo pll_current_language() == 'vi' ? 'Thành phần chính' : 'Ingredient'; ?></a>
                                <a href="#tab_recipe"><?php echo pll_current_language() == 'vi' ? 'Công thức' : 'Recipe'; ?></a>
                                <a href="#tab_detail"><?php echo pll_current_language() == 'vi' ? 'Thông tin chi tiết' : 'Product Detail'; ?></a>
                            </div>
                            <div class="tab-contents">
                                <div id="tab_ingredient">
                                    <div class="row">
                                        <div class="col-xs-12">

                                            <?php foreach ($product_detail->recipes as $recipe) { ?>
                                                <a href="<?php echo $recipe->permalink; ?>" class="no-text-decoration">
                                                    <img src="<?php echo $recipe->featured_image; ?>" height="100"
                                                         width="100"
                                                         style="float: left; margin-right: 10px;">
                                                    <b><?php echo $recipe->post_title; ?></b>
                                                    <br><br>
                                                    <?php echo limitWords($recipe->post_content); ?>
                                                </a>
                                                <div class="clearfix"></div>
                                            <?php } ?>

                                        </div>
                                    </div>
                                </div>
                                <div id="tab_recipe">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <?php echo $product_detail->recipe; ?>
                                        </div>
                                    </div>
                                </div>
                                <div id="tab_detail">
                                    <div class="row">
                                        <div class="col-xs-12">
                                            <b>THORAKAO đảm bảo:</b><br>100% sản phẩm được sản xuất
                                            tại Việt Nam<br>100% không có Corticoid<br>100% không có Isobutyl
                                            Parabens<br><br><b>Lưu ý khi sử dụng:</b><br>Bảo quản nơi khô thoáng.<br>Tránh
                                            xa tầm tay trẻ em.<br>Tránh vây vào mắt.<br>Kích ứng da không đáng kể.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-20 hr-grey">
                        <div class="col-xs-12">
                            <h3 class="mb-20 text-center"><a
                                    href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/tat-ca-san-pham' : '/en/products' ?>"><?php echo pll_current_language() == 'vi' ? 'Sản phẩm liên quan' : 'Related Products'; ?></a>
                            </h3>
                            <div class="owl-carousel carousel">
                                <?php foreach ($related_products as $key => $item) { ?>

                                    <div class="item">
                                        <div class="product__wrapper text-center">
                                            <a href="<?php echo $item->permalink ?>" class="img-repsonsive">
                                                <img src="<?php echo $item->featured_image ?>" class="br-5">
                                            </a>
                                            <a href="<?php echo $item->permalink ?>" class="product__title">
                                                <?php echo $item->post_title; ?>
                                            </a>
                                        </div>
                                    </div>

                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>

<script>
    var $ = jQuery.noConflict();
    $(document).ready(function () {
        $('.form-single-product').validate({
            rules: {
                quantity: {
                    required: true,
                    min: 1
                }
            },
            messages: {
                quantity: {
                    requrired: "Vui lòng chọn số lượng muốn mua.",
                    min: "Vui lòng chọn số lượng muốn mua."
                }
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
                                        // var win = window.open(location.protocol + '//' + location.host + '/gio-hang', '_blank');
                                        // win.focus();
                                        window.location.href = location.protocol + '//' + location.host + '/gio-hang';
                                    } else {
                                        window.location.reload();
                                    }
                                });
                        }
                        else {
                            if(data.message) {
                                swal({"title": "Error", "text": data.message, "type": "error", html: true});
                            } else if (data.data) {
                                swal({"title": "Thất bại", "text": data.data, "type": "error", html: true});
                            }
                        }
                    }
                });
            }
        });
    });
</script>
