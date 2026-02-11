<?php
/**
 * Front Page Template
 * トップページ専用テンプレート
 */
get_header();
?>
</div><!-- /.site-container -->

<div class="front-page">

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-inner">
            <p class="hero-subtitle"><?php echo rikalog_age(); ?>歳、コールセンターで働く私の日々</p>
            <h1 class="hero-title">Rika<span>Log</span></h1>
            <p class="hero-description">仕事のこと、暮らしのこと、心のこと。<br>ありのままの日常を綴っています。</p>
            <a href="<?php echo esc_url( rikalog_get_blog_url() ); ?>" class="hero-btn">ブログを読む</a>
        </div>
    </section>

    <!-- Latest Posts -->
    <section class="front-section">
        <div class="front-section-inner">
            <h2 class="front-section-title">新着記事</h2>
            <?php
            $latest = new WP_Query( array(
                'posts_per_page' => 6,
                'post_status'    => 'publish',
            ) );
            if ( $latest->have_posts() ) : ?>
                <div class="front-post-grid">
                    <?php while ( $latest->have_posts() ) : $latest->the_post(); ?>
                        <article class="front-post-card">
                            <a href="<?php the_permalink(); ?>" class="front-post-card-link">
                                <div class="front-post-card-thumb">
                                    <?php if ( has_post_thumbnail() ) : ?>
                                        <?php the_post_thumbnail( 'rikalog-card' ); ?>
                                    <?php else : ?>
                                        <div class="no-thumbnail">
                                            <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/></svg>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="front-post-card-body">
                                    <div class="front-post-card-meta">
                                        <?php
                                        $cats = get_the_category();
                                        if ( ! empty( $cats ) ) : ?>
                                            <span class="post-card-category"><?php echo esc_html( $cats[0]->name ); ?></span>
                                        <?php endif; ?>
                                        <time datetime="<?php echo get_the_date( 'Y-m-d' ); ?>"><?php echo get_the_date(); ?></time>
                                    </div>
                                    <h3 class="front-post-card-title"><?php the_title(); ?></h3>
                                </div>
                            </a>
                        </article>
                    <?php endwhile; ?>
                </div>
                <div class="front-section-more">
                    <a href="<?php echo esc_url( rikalog_get_blog_url() ); ?>" class="front-more-btn">記事一覧を見る</a>
                </div>
            <?php endif;
            wp_reset_postdata();
            ?>
        </div>
    </section>

    <!-- Categories -->
    <section class="front-section front-section--alt">
        <div class="front-section-inner">
            <h2 class="front-section-title">カテゴリー</h2>
            <div class="front-category-grid">
                <?php
                $categories = get_categories( array( 'hide_empty' => false ) );
                $cat_icons = array(
                    '仕事'             => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>',
                    '体験談・実話'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
                    '心・メンタル・生き方' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>',
                );
                $cat_descriptions = array(
                    '仕事'             => 'コールセンターでの日々や、働くことについて',
                    '体験談・実話'     => '実際にあった出来事や経験談',
                    '心・メンタル・生き方' => '心の持ち方、生き方について思うこと',
                );
                foreach ( $categories as $cat ) :
                    $icon = isset( $cat_icons[ $cat->name ] ) ? $cat_icons[ $cat->name ] : '';
                    $desc = isset( $cat_descriptions[ $cat->name ] ) ? $cat_descriptions[ $cat->name ] : $cat->description;
                ?>
                    <a href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>" class="front-category-card">
                        <?php if ( $icon ) : ?>
                            <div class="front-category-icon"><?php echo wp_kses( $icon, array( 'svg' => array( 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'xmlns' => true ), 'rect' => array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ), 'path' => array( 'd' => true ), 'circle' => array( 'cx' => true, 'cy' => true, 'r' => true ), 'ellipse' => array( 'cx' => true, 'cy' => true, 'rx' => true, 'ry' => true ), ) ); ?></div>
                        <?php endif; ?>
                        <h3><?php echo esc_html( $cat->name ); ?></h3>
                        <?php if ( $desc ) : ?>
                            <p><?php echo esc_html( $desc ); ?></p>
                        <?php endif; ?>
                        <span class="front-category-count"><?php echo (int) $cat->count; ?>件の記事</span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- About Teaser -->
    <section class="front-section">
        <div class="front-section-inner front-about-teaser">
            <div class="front-about-avatar">
                <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="100" cy="100" r="100" fill="#F3EDEB"/>
                    <circle cx="100" cy="82" r="35" fill="#D4A0A0"/>
                    <ellipse cx="100" cy="160" rx="55" ry="40" fill="#D4A0A0"/>
                    <circle cx="100" cy="82" r="30" fill="#F3EDEB"/>
                    <circle cx="100" cy="78" r="26" fill="#D4A0A0" opacity="0.15"/>
                    <circle cx="90" cy="76" r="2.5" fill="#3C3C3C"/>
                    <circle cx="110" cy="76" r="2.5" fill="#3C3C3C"/>
                    <path d="M93 86 Q100 92 107 86" stroke="#3C3C3C" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <ellipse cx="84" cy="79" rx="5" ry="3" fill="#D4A0A0" opacity="0.35"/>
                    <ellipse cx="116" cy="79" rx="5" ry="3" fill="#D4A0A0" opacity="0.35"/>
                </svg>
            </div>
            <div class="front-about-text">
                <h2 class="front-section-title">このブログについて</h2>
                <p>はじめまして、りかです。<?php echo rikalog_age(); ?>歳、コールセンターで働いています。</p>
                <p>毎日いろいろな方のお話を聞きながら、「人の気持ちに寄り添う」ことの大切さを日々感じています。このブログが少しでも心の休憩所になれたら嬉しいです。</p>
                <a href="<?php echo esc_url( home_url( '/about/' ) ); ?>" class="front-more-btn">もっと読む</a>
            </div>
        </div>
    </section>

</div>

<div class="site-container">
<?php get_footer(); ?>
