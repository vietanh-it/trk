<?php
/**
 * Created by PhpStorm.
 * User: VietAnh
 * Date: 01/06/2016
 * Time: 5:57 CH
 */

get_header(); ?>

    <div class="main-content container">
        <?php if (have_posts()) : ?>

            <header class="page-header">
                <h1 class="page-title">Kết quả cho từ khóa: <?php echo get_search_query(); ?></h1>
            </header><!-- .page-header -->

            <?php
            // Start the loop.
            while (have_posts()) : the_post();
                if (in_array($post->post_type, ['product', 'beauty', 'recipe'])) { ?>

                    <?php
                    /*
                     * Run the loop for the search to output the results.
                     * If you want to overload this in a child theme then include a file
                     * called content-search.php and that will be used instead.
                     */
                    ?>

                    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <?php the_post_thumbnail('thumbnail'); ?>

                        <header class="entry-header">
                            <?php the_title(sprintf('<h2 class="entry-title"><a href="%s" rel="bookmark">',
                                esc_url(get_permalink())), '</a></h2>'); ?>
                        </header><!-- .entry-header -->

                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div><!-- .entry-summary -->

                        <?php if ('post' == get_post_type()) : ?>

                            <footer class="entry-footer">
                                <?php //edit_post_link(__('Edit', 'twentyfifteen'), '<span class="edit-link">', '</span>'); ?>
                            </footer><!-- .entry-footer -->
                        <?php else : ?>

                            <?php //edit_post_link(__('Edit', 'twentyfifteen'),
                                // '<footer class="entry-footer"><span class="edit-link">',
                                // '</span></footer><!-- .entry-footer -->'); ?>

                        <?php endif; ?>

                    </article><!-- #post-## -->

                    <div class="hr-grey" style="margin: 20px 0;"></div>
                <?php }

                // End the loop.
            endwhile;

        // Previous/next page navigation.
        // the_posts_pagination([
        //     'prev_text'          => __('Previous page', 'twentyfifteen'),
        //     'next_text'          => __('Next page', 'twentyfifteen'),
        //     'before_page_number' => '<span class="meta-nav screen-reader-text">' . __('Page',
        //             'twentyfifteen') . ' </span>',
        // ]);

// If no content, include the "No posts found" template.
        else :
            get_template_part('content', 'none');

        endif;
        ?>
    </div>

<?php get_footer();