<?php get_header(); ?>

<div class="site-main-wrap">
    <main class="site-main">
        <header class="search-results-header">
            <h1>「<span><?php echo esc_html( get_search_query() ); ?></span>」の検索結果</h1>
        </header>

        <?php if ( have_posts() ) : ?>
            <div class="post-list">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php get_template_part( 'template-parts/content' ); ?>
                <?php endwhile; ?>
            </div>

            <?php rikalog_pagination(); ?>
        <?php else : ?>
            <?php get_template_part( 'template-parts/content', 'none' ); ?>
        <?php endif; ?>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
