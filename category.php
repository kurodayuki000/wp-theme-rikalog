<?php get_header(); ?>

<div class="site-main-wrap">
    <main class="site-main">
        <?php if ( have_posts() ) : ?>
            <header class="archive-header">
                <h1 class="archive-title"><?php single_cat_title(); ?></h1>
                <?php
                $cat_description = category_description();
                if ( $cat_description ) : ?>
                    <div class="archive-description"><?php echo wp_kses_post( $cat_description ); ?></div>
                <?php endif; ?>
            </header>

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
