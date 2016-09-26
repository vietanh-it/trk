<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 26/05/2016
 * Time: 11:27 SA
 */

$product_ctrl = \TVA\Controllers\ProductController::init();

$term = get_queried_object();
$term->permalink = get_term_link($term);

if (!empty($_GET)) {
    // var_dump($_GET);
}

$paged = get_query_var('paged');
$paged = valueOrNull($paged, 1);

$limit = valueOrNull($_GET['limit'], 9);

$products = $product_ctrl->getProductList([
    'limit'            => $limit,
    'term_taxonomy_id' => $term->term_taxonomy_id,
    'page'             => $paged,
    'is_paging'        => 1
]);

get_header(); ?>

    <script>
        $(document).ready(function () {
            <?php if(!empty($_GET)) { ?>

            $('select[name="order_by"]').val('<?php echo valueOrNull($_GET['order_by'], ""); ?>');
            $('select[name="limit"]').val('<?php echo valueOrNull($limit) ?>');

            <?php } ?>
        });
    </script>

    <div class="breadcrumb no-padding">
        <div class="container">
            <ul class="no-padding no-margin">
                <li>
                    <a href="<?php echo pll_home_url(); ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Trang chủ' : 'Home'; ?>
                    </a>
                    <span class="slash">/</span>
                </li>
                <li>
                    <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/tat-ca-san-pham' : '/en/products'; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Tất cả sản phẩm' : 'Products'; ?>
                    </a>
                    <span class="slash">/</span>
                </li>
                <li class="last"><a href="<?php echo $term->permalink; ?>"><?php echo $term->name; ?></a></li>
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
                            <!--<nav>-->
                            <!--    <ul class="pagination">-->
                            <!--        <li><a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>-->
                            <!--        </li>-->
                            <!--        <li><a href="#">1</a></li>-->
                            <!--        <li><a href="#">2</a></li>-->
                            <!--        <li><a href="#">3</a></li>-->
                            <!--        <li><a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>-->
                            <!--    </ul>-->
                            <!--</nav>-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 hr-grey">
                            <h3 class="mb-20 text-center"><a
                                    href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/tat-ca-san-pham' : '/en/products' ?>"><?php echo (pll_current_language() == 'vi') ? 'Sản phẩm nổi bật' : 'Hot Products'; ?></a>
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


    <script>
        var $ = jQuery.noConflict();
        $(document).ready(function () {

            $(document).delegate('.add-to-cart', 'click', function (e) {
                e.preventDefault();
                var obj = $(this);

                var product_id = obj.attr('data-product-id');
                console.log(product_id);
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
                        obj.attr('disabled', true).css({'opacity': '0.5'});
                    },
                    success: function (data) {
                        obj.attr('disabled', false).css({'opacity': 1});
                        if (data.status == 'success') {
                            swal({
                                    title: data.message,
                                    text: "<p style='font-weight: bold;color: #80b501'>Bạn có muốn xem giỏ hàng?</p>",
                                    type: "success",
                                    showCancelButton: true,
                                    confirmButtonColor: "#80b501",
                                    confirmButtonText: "Xem giỏ hàng",
                                    closeOnConfirm: false,
                                    cancelButtonText: "Mua tiếp",
                                    html: true
                                },
                                function (is_confirm) {
                                    if (is_confirm) {
                                        window.location.href = data.data.url;
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            );

                        }
                        else {
                            swal({"title": "Error", "text": data.message, "type": "error", html: true});
                        }
                    }
                });
            });

            <?php if ($is_opening) { ?>

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

<?php get_footer();