<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 25/05/2016
 * Time: 10:43 SA
 * Template Name: All Product
 */

$product_ctrl = \TVA\Controllers\ProductController::init();

$paged = get_query_var('paged');

$products = $product_ctrl->getProductList([
    'limit'     => valueOrNull($_GET['limit'], 9),
    'page'      => valueOrNull($paged, 1),
    'is_paging' => 1
]);

get_header(); ?>

<script>
    $(document).ready(function () {
        <?php if(!empty($_GET)) { ?>

        $('select[name="order_by"]').val('<?php echo valueOrNull($_GET['order_by'], ""); ?>');
        $('select[name="limit"]').val('<?php echo valueOrNull($_GET['limit'], 9) ?>');

        <?php } ?>
    });
</script>

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
                <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/en/products' : '/tat-ca-san-pham'; ?>">
                    <?php echo (pll_current_language() == 'vi') ? 'Sản Phẩm' : 'Products'; ?>
                </a>
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
            <div class="col-md-9 product-list left-divider left-padding">
                <div class="row">
                    <form method="get" class="form-inline frm_filter filters-wrapper">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lọc theo</label>
                                <select name="order_by" class="form-control">
                                    <option value="">Mặc định</option>
                                    <option value="price_desc">Giá từ cao đến thấp</option>
                                    <option value="price_asc">Giá từ thấp đến cao</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group pull-right">
                                <label>Số sản phẩm trên 1 trang</label>
                                <select name="limit" class="form-control">
                                    <option value="6">6</option>
                                    <option value="9">9</option>
                                    <option value="12">12</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <?php foreach ($products as $key => $item) {
                        if ($key == 0) {
                            echo '<div class="row">';
                        }
                        else {
                            if ($key % 3 == 0) {
                                echo '</div>';
                                echo '<div class="row">';
                            }
                        } ?>

                        <div class="col-md-4">
                            <div class="product__wrapper text-center">
                                <a href="<?php echo $item->permalink; ?>" class="product__img">
                                    <img src="<?php echo $item->featured_image; ?>" class="br-5 img-responsive">
                                    <span class="view-detail btn"><i class="fa fa-search"></i>Xem chi tiết</span>
                                    <span class="add-to-cart btn" data-product-id="<?php echo $item->ID; ?>">
                                        <i class="fa fa-shopping-cart"></i>Thêm vào giỏ hàng
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
                        if ($key == (count($products) - 1)) {
                            echo '</div>';
                        }
                    } ?>
                </div>
                <div class="clearfix"></div>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <?php if (function_exists('wp_pagenavi')) {
                            wp_pagenavi([
                                'before' => '<div class="text-center wrap-pagination">',
                                'after'  => '</div>'
                            ]);
                        } ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 hr-grey">
                        <h3 class="mb-20 text-center">
                            <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/tat-ca-san-pham' : '/en/products' ?>"><?php echo (pll_current_language() == 'vi') ? 'Sản phẩm nổi bật' : 'Hot Products'; ?>
                        </h3>
                        <div class="owl-carousel carousel">
                            <?php foreach ($products as $key => $item) { ?>

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
                                    // var win = window.open(location.protocol + '//' + location.host + '/gio-hang', '_blank');
                                    // win.focus();
                                    window.location.href = location.protocol + '//' + location.host + '/gio-hang';
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


    });
</script>
