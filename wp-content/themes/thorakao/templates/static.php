<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 25/05/2016
 * Time: 2:57 CH
 * Template Name: Static Page
 */

if (have_posts()) : the_post();
    get_header(); ?>

    <div class="breadcrumb no-padding">
        <div class="container">
            <ul class="no-padding no-margin">
                <?php if (pll_current_language('slug') == 'vi') { ?>
                    <li><a href="<?php echo WP_SITEURL; ?>">Trang Chủ</a><span class="slash">/</span></li>
                    <li class="last"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php } else { ?>
                    <li><a href="<?php echo WP_SITEURL; ?>">Home</a><span class="slash">/</span></li>
                    <li class="last"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12 detail">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="text-center">
                                <h3 class="detail__title mt-0">
                                    <?php the_title(); ?>
                                </h3>
                            </div>
                            <div class="content-wrapper" style="margin-top: 20px;">
                                <?php the_post_thumbnail(['featured-image']); ?>
                                <?php the_content(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php get_footer();
endif;