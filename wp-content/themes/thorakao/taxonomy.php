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
                            } else {
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

<?php get_footer();