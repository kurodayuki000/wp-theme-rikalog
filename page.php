<?php get_header(); ?>

<div class="site-main-wrap">
    <main class="site-main">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="single-post-header">
                    <h1 class="single-post-title"><?php the_title(); ?></h1>
                </header>
                <div class="post-content">
                    <?php the_content(); ?>
                </div>
            </article>

            <?php
            if ( comments_open() || get_comments_number() ) :
                comments_template();
            endif;
            ?>
        <?php endwhile; ?>
    </main>

    <?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
