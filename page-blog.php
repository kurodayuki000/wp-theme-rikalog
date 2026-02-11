<?php
/**
 * Blog Page Template (slug: blog)
 * 最新記事の一覧を表示
 */
get_header();

$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

$blog_query = new WP_Query( array(
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => get_option( 'posts_per_page' ),
    'paged'          => $paged,
) );
?>

<div class="site-main-wrap">
    
    <main class="site-main">
        <header class="archive-header">
            <h1 class="archive-title">最新記事の一覧を表示</h1>
        </header>

        <?php if ( $blog_query->have_posts() ) : ?>
            <div class="post-list">
                <?php while ( $blog_query->have_posts() ) : $blog_query->the_post(); ?>
                    <?php get_template_part( 'template-parts/content' ); ?>
                <?php endwhile; ?>
            </div>
            <?php
            // Pagination
            $big = 999999999;
            echo '<nav class="pagination">';
            echo paginate_links( array(
                'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                'format'    => '/page/%#%',
                'current'   => $paged,
                'total'     => $blog_query->max_num_pages,
                'prev_text' => '&laquo;',
                'next_text' => '&raquo;',
            ) );
            echo '</nav>';
            ?>
        <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif;
        wp_reset_postdata();
        ?>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
