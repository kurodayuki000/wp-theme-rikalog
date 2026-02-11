<?php get_header(); ?>

<div class="site-main-wrap">
    <main class="site-main">
        <?php while ( have_posts() ) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="single-post-header">
                    <div class="single-post-meta">
                        <?php
                        $categories = get_the_category();
                        if ( ! empty( $categories ) ) : ?>
                            <span class="single-post-category">
                                <a href="<?php echo esc_url( get_category_link( $categories[0]->term_id ) ); ?>">
                                    <?php echo esc_html( $categories[0]->name ); ?>
                                </a>
                            </span>
                        <?php endif; ?>
                        <time datetime="<?php echo get_the_date( 'Y-m-d' ); ?>"><?php echo get_the_date(); ?></time>
                        <?php if ( get_the_date() !== get_the_modified_date() ) : ?>
                            <span>（更新: <?php echo get_the_modified_date(); ?>）</span>
                        <?php endif; ?>
                        <span class="single-post-reading-time"><?php echo rikalog_reading_time(); ?>分で読めます</span>
                    </div>
                    <h1 class="single-post-title"><?php the_title(); ?></h1>
                </header>

                <?php if ( has_post_thumbnail() ) : ?>
                    <div class="single-post-thumbnail">
                        <?php the_post_thumbnail( 'large' ); ?>
                    </div>
                <?php endif; ?>

                <?php get_template_part( 'template-parts/toc' ); ?>

                <div class="post-content">
                    <?php the_content(); ?>
                </div>

                <?php
                $tags = get_the_tags();
                if ( $tags ) : ?>
                    <div class="post-tags">
                        <?php foreach ( $tags as $tag ) : ?>
                            <a href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
                                #<?php echo esc_html( $tag->name ); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php rikalog_share_buttons(); ?>
            </article>

            <nav class="post-navigation">
                <?php
                $prev = get_previous_post();
                $next = get_next_post();
                ?>
                <?php if ( $prev ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" class="nav-prev">
                        <span class="nav-label">&laquo; 前の記事</span>
                        <span class="nav-title"><?php echo esc_html( $prev->post_title ); ?></span>
                    </a>
                <?php else : ?>
                    <div></div>
                <?php endif; ?>

                <?php if ( $next ) : ?>
                    <a href="<?php echo esc_url( get_permalink( $next ) ); ?>" class="nav-next">
                        <span class="nav-label">次の記事 &raquo;</span>
                        <span class="nav-title"><?php echo esc_html( $next->post_title ); ?></span>
                    </a>
                <?php else : ?>
                    <div></div>
                <?php endif; ?>
            </nav>

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
