<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 23/05/2016
 * Time: 11:59 CH
 */

get_header(); ?>

    <div class="breadcrumb no-padding">
        <div class="container">
            <ul class="no-padding no-margin">
                <?php if (pll_current_language('slug') == 'vi') { ?>
                    <li><a href="<?php echo WP_SITEURL; ?>">Trang Chủ</a><span class="slash">/</span></li>
                    <li><a href="<?php echo WP_SITEURL . '/lam-dep'; ?>">Làm Đẹp</a><span class="slash">/</span></li>
                    <li class="last"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php } else { ?>
                    <li><a href="<?php echo WP_SITEURL; ?>">Home</a><span class="slash">/</span></li>
                    <li><a href="<?php echo WP_SITEURL . '/beauty'; ?>">Beauty</a><span class="slash">/</span></li>
                    <li class="last"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php get_sidebar(); ?>
                </div>
                <div class="col-md-9 detail left-divider">
                    <div class="row">
                        <div class="col-md-12">
                            <h3 class="detail__title mt-0"><?php the_title(); ?></h3>
                            <div class="content-wrapper">
                                <!--<img src="--><?php //the_post_thumbnail_url(); ?><!--" height="300" width="300"-->
                                <!--     class="img-responsive">-->
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($post->post_type == 'recipe') {
                        $product_ctrl = \TVA\Controllers\ProductController::init();
                        $related_products = $product_ctrl->getRecipeProducts($post->ID);
                        if ($related_products) { ?>
                            <div class="row mt-20 hr-grey">
                                <div class="col-xs-12">
                                    <h3 class="mb-20 text-center"><a
                                            href="<?php echo WP_SITEURL . (pll_current_language() == 'vi') ? '/tat-ca-san-pham' : '/en/products' ?>">
                                            <?php echo (pll_current_language() == 'vi') ? 'Sản phẩm liên quan' : 'Related products' ?>
                                        </a>
                                    </h3>
                                    <div class="owl-carousel carousel3">
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
                        <?php }
                    } ?>
                </div>
            </div>
        </div>
    </div>

<?php get_footer();