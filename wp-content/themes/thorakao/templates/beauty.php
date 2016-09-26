<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 25/05/2016
 * Time: 11:36 SA
 * Template Name: Beauty
 */

$post_ctrl = \TVA\Controllers\PostController::init();
$product_ctrl = \TVA\Controllers\ProductController::init();

$paged = get_query_var('paged');
$paged = valueOrNull($paged, 1);

$recipes = $post_ctrl->getList([
    'post_type' => 'beauty',
    'limit'     => 8,
    'is_paging' => 1,
    'page'      => $paged
]);

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
                <li class="last">
                    <a href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/thanh-phan' : '/recipe'; ?>">
                        <?php echo (pll_current_language() == 'vi') ? 'Làm đẹp' : 'Beauty'; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h3 class="mt-0 mb-20"><?php echo (pll_current_language() == 'vi') ? 'Làm đẹp' : 'Beauty'; ?></h3>
                        </div>
                    </div>

                    <!--News-->
                    <div class="row">
                        <?php foreach ($recipes as $key => $item) {
                            if ($key > 0 && ($key % 4 == 0)) {
                                echo '<div class="clearfix"></div>';
                            } ?>

                            <div class="col-md-3">
                                <div class="news__wrapper text-center">
                                    <a href="<?php echo $item->permalink; ?>" class="news__img">
                                        <img src="<?php echo $item->featured_image ?>" class="img-responsive br-5"
                                             height="223" width="223">
                                    </a>
                                    <a href="<?php echo $item->permalink; ?>" class="news__title">
                                        <?php echo $item->post_title; ?>
                                    </a>
                                    <div class="news__desc">
                                        <i>
                                            <?php echo limitWords($item->post_content, 20); ?>
                                        </i>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    </div>

                    <!--Pagination-->
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

                </div>
            </div>
        </div>
    </div>

<?php get_footer();