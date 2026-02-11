<aside class="site-sidebar">
    <!-- 検索 -->
    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title">検索</h3>
        <?php get_search_form(); ?>
    </div>

    <!-- カテゴリー -->
    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title">カテゴリー</h3>
        <div class="sidebar-select-wrap">
            <select id="sidebar-cat-select" class="sidebar-select">
                <option value="">カテゴリーを選択</option>
                <?php
                $categories = get_categories( array( 'hide_empty' => false ) );
                foreach ( $categories as $cat ) : ?>
                    <option value="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
                        <?php echo esc_html( $cat->name ); ?>（<?php echo (int) $cat->count; ?>）
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- タグ -->
    <?php
    $tags = get_tags( array( 'hide_empty' => true ) );
    if ( $tags ) : ?>
    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title">タグ</h3>
        <div class="sidebar-select-wrap">
            <select id="sidebar-tag-select" class="sidebar-select">
                <option value="">タグを選択</option>
                <?php foreach ( $tags as $tag ) : ?>
                    <option value="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>">
                        <?php echo esc_html( $tag->name ); ?>（<?php echo (int) $tag->count; ?>）
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>

    <!-- アーカイブ -->
    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title">アーカイブ</h3>
        <div class="sidebar-select-wrap">
            <select id="sidebar-archive-select" class="sidebar-select">
                <option value="">月を選択</option>
                <?php
                wp_get_archives( array(
                    'type'   => 'monthly',
                    'limit'  => 12,
                    'format' => 'option',
                ) );
                ?>
            </select>
        </div>
    </div>

    <!-- 人気記事 -->
    <?php
    $popular = rikalog_get_popular_posts( 5 );
    if ( $popular->have_posts() ) : ?>
    <div class="sidebar-widget">
        <h3 class="sidebar-widget-title">人気記事</h3>
        <ul class="popular-posts-list">
            <?php while ( $popular->have_posts() ) : $popular->the_post(); ?>
            <li>
                <a href="<?php the_permalink(); ?>" class="popular-post-link">
                    <div class="popular-post-thumbnail">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'rikalog-popular' ); ?>
                        <?php endif; ?>
                    </div>
                    <div class="popular-post-info">
                        <div class="popular-post-title"><?php the_title(); ?></div>
                        <div class="popular-post-views">
                            <?php echo number_format( rikalog_get_post_views( get_the_ID() ) ); ?> views
                        </div>
                    </div>
                </a>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php
    wp_reset_postdata();
    endif;
    ?>
</aside>
