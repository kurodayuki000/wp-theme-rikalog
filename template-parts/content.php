<article id="post-<?php the_ID(); ?>" <?php post_class( 'post-card' ); ?>>
    <a href="<?php the_permalink(); ?>" class="post-card-link">
        <div class="post-card-thumbnail">
            <?php if ( has_post_thumbnail() ) : ?>
                <?php the_post_thumbnail( 'rikalog-card' ); ?>
            <?php else : ?>
                <div class="no-thumbnail">
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                    </svg>
                </div>
            <?php endif; ?>
        </div>

        <div class="post-card-body">
            <div class="post-card-meta">
                <?php
                $categories = get_the_category();
                if ( ! empty( $categories ) ) : ?>
                    <span class="post-card-category"><?php echo esc_html( $categories[0]->name ); ?></span>
                <?php endif; ?>
                <time datetime="<?php echo get_the_date( 'Y-m-d' ); ?>"><?php echo get_the_date(); ?></time>
            </div>
            <h2 class="post-card-title"><?php the_title(); ?></h2>
            <div class="post-card-excerpt"><?php echo get_the_excerpt(); ?></div>
        </div>
    </a>
</article>
