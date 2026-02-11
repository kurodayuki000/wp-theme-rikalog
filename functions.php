<?php
/**
 * RikaLog Theme Functions
 *
 * @package RikaLog
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Setup
 */
function rikalog_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'custom-logo' );

    set_post_thumbnail_size( 800, 450, true );
    add_image_size( 'rikalog-card', 480, 320, true );
    add_image_size( 'rikalog-popular', 140, 140, true );

    register_nav_menus( array(
        'primary' => 'メインメニュー',
        'footer'  => 'フッターメニュー',
    ) );
}
add_action( 'after_setup_theme', 'rikalog_setup' );

/**
 * Enqueue Scripts and Styles
 */
function rikalog_enqueue_scripts() {
    wp_enqueue_style(
        'rikalog-google-fonts',
        'https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&display=swap',
        array(),
        null
    );

    $css_file = get_template_directory() . '/dist/theme.css';
    wp_enqueue_style(
        'rikalog-style',
        get_template_directory_uri() . '/dist/theme.css',
        array( 'rikalog-google-fonts' ),
        file_exists( $css_file ) ? (string) filemtime( $css_file ) : wp_get_theme()->get( 'Version' )
    );

    $js_file = get_template_directory() . '/dist/theme.js';
    wp_enqueue_script(
        'rikalog-theme',
        get_template_directory_uri() . '/dist/theme.js',
        array(),
        file_exists( $js_file ) ? (string) filemtime( $js_file ) : wp_get_theme()->get( 'Version' ),
        true
    );
}
add_action( 'wp_enqueue_scripts', 'rikalog_enqueue_scripts' );

/**
 * Register Sidebar / Widget Area
 */
function rikalog_widgets_init() {
    register_sidebar( array(
        'name'          => 'サイドバー',
        'id'            => 'sidebar-1',
        'description'   => 'メインサイドバーのウィジェットエリア',
        'before_widget' => '<div id="%1$s" class="sidebar-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="sidebar-widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'rikalog_widgets_init' );

/**
 * Post Views Counter
 */
function rikalog_set_post_views( $post_id ) {
    $count_key = 'post_views_count';
    $count = get_post_meta( $post_id, $count_key, true );
    if ( $count === '' ) {
        delete_post_meta( $post_id, $count_key );
        add_post_meta( $post_id, $count_key, '1' );
    } else {
        $count++;
        update_post_meta( $post_id, $count_key, (string) $count );
    }
}

function rikalog_track_post_views( $post_id ) {
    if ( ! is_single() ) {
        return;
    }
    if ( empty( $post_id ) ) {
        global $post;
        $post_id = $post->ID;
    }
    if ( ! is_user_logged_in() ) {
        rikalog_set_post_views( $post_id );
    }
}
add_action( 'wp_head', 'rikalog_track_post_views' );

function rikalog_get_post_views( $post_id ) {
    $count = get_post_meta( $post_id, 'post_views_count', true );
    return $count ? (int) $count : 0;
}

/**
 * Get Popular Posts
 */
function rikalog_get_popular_posts( $num = 5 ) {
    return new WP_Query( array(
        'posts_per_page' => $num,
        'meta_key'       => 'post_views_count',
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'post_status'    => 'publish',
    ) );
}

/**
 * Search: 投稿（post）のみを検索対象にする（固定ページを除外）
 */
function rikalog_search_only_posts( $query ) {
    if ( ! is_admin() && $query->is_search() && $query->is_main_query() ) {
        $query->set( 'post_type', 'post' );
    }
}
add_action( 'pre_get_posts', 'rikalog_search_only_posts' );

/**
 * Custom Excerpt Length
 */
function rikalog_excerpt_length( $length ) {
    return 60;
}
add_filter( 'excerpt_length', 'rikalog_excerpt_length' );

function rikalog_excerpt_more( $more ) {
    return '...';
}
add_filter( 'excerpt_more', 'rikalog_excerpt_more' );

/**
 * パンくずリスト
 */
function rikalog_breadcrumb() {
    if ( is_front_page() ) {
        return;
    }

    $home_label = 'Home';
    $items = array();
    $items[] = '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html( $home_label ) . '</a>';

    if ( is_single() ) {
        $cats = get_the_category();
        if ( ! empty( $cats ) ) {
            $cat = $cats[0];
            $items[] = '<a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a>';
        }
        $items[] = '<span>' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_category() ) {
        $items[] = '<span>' . esc_html( single_cat_title( '', false ) ) . '</span>';
    } elseif ( is_tag() ) {
        $items[] = '<span>' . esc_html( single_tag_title( '', false ) ) . '</span>';
    } elseif ( is_page() ) {
        global $post;
        if ( $post->post_parent ) {
            $ancestors = get_post_ancestors( $post->ID );
            $ancestors = array_reverse( $ancestors );
            foreach ( $ancestors as $ancestor ) {
                $items[] = '<a href="' . esc_url( get_permalink( $ancestor ) ) . '">' . esc_html( get_the_title( $ancestor ) ) . '</a>';
            }
        }
        $items[] = '<span>' . esc_html( get_the_title() ) . '</span>';
    } elseif ( is_search() ) {
        $items[] = '<span>「' . esc_html( get_search_query() ) . '」の検索結果</span>';
    } elseif ( is_archive() ) {
        $items[] = '<span>' . wp_strip_all_tags( get_the_archive_title() ) . '</span>';
    } elseif ( is_404() ) {
        $items[] = '<span>ページが見つかりません</span>';
    }

    echo '<nav class="breadcrumb" aria-label="パンくずリスト">';
    echo '<ol>';
    foreach ( $items as $i => $item ) {
        echo '<li>' . $item . '</li>';
    }
    echo '</ol>';
    echo '</nav>';
}

/**
 * OGP (Open Graph Protocol) Meta Tags
 */
function rikalog_ogp_meta() {
    $og_title       = '';
    $og_description = '';
    $og_url         = '';
    $og_image       = '';
    $og_type        = 'website';

    if ( is_singular() ) {
        global $post;
        setup_postdata( $post );

        $og_title = get_the_title();
        $og_url   = get_permalink();
        $og_type  = is_single() ? 'article' : 'website';

        // Description: excerpt or trimmed content
        if ( has_excerpt() ) {
            $og_description = get_the_excerpt();
        } else {
            $og_description = $post->post_content;
        }
        $og_description = wp_strip_all_tags( $og_description );
        $og_description = mb_substr( $og_description, 0, 120, 'UTF-8' );
        if ( mb_strlen( wp_strip_all_tags( $post->post_content ), 'UTF-8' ) > 120 ) {
            $og_description .= '...';
        }

        // Image: featured image
        if ( has_post_thumbnail() ) {
            $og_image = get_the_post_thumbnail_url( $post->ID, 'large' );
        }

        wp_reset_postdata();
    } elseif ( is_front_page() || is_home() ) {
        $og_title       = get_bloginfo( 'name' );
        $og_description = get_bloginfo( 'description' );
        $og_url         = home_url( '/' );
    } elseif ( is_category() ) {
        $og_title       = single_cat_title( '', false );
        $og_description = category_description();
        $og_description = $og_description ? wp_strip_all_tags( $og_description ) : $og_title . ' の記事一覧';
        $og_url         = get_category_link( get_queried_object_id() );
    } elseif ( is_tag() ) {
        $og_title       = single_tag_title( '', false );
        $og_description = tag_description();
        $og_description = $og_description ? wp_strip_all_tags( $og_description ) : $og_title . ' の記事一覧';
        $og_url         = get_tag_link( get_queried_object_id() );
    } elseif ( is_archive() ) {
        $og_title       = get_the_archive_title();
        $og_description = get_the_archive_description();
        $og_description = $og_description ? wp_strip_all_tags( $og_description ) : $og_title;
        $og_url         = home_url( add_query_arg( null, null ) );
    } elseif ( is_search() ) {
        $og_title       = '「' . get_search_query() . '」の検索結果';
        $og_description = get_bloginfo( 'name' ) . ' の検索結果';
        $og_url         = get_search_link();
    } else {
        $og_title       = get_bloginfo( 'name' );
        $og_description = get_bloginfo( 'description' );
        $og_url         = home_url( '/' );
    }

    // Fallback image: site icon or theme screenshot
    if ( ! $og_image ) {
        if ( has_site_icon() ) {
            $og_image = get_site_icon_url( 512 );
        } else {
            $og_image = get_template_directory_uri() . '/screenshot.png';
        }
    }

    $site_name = get_bloginfo( 'name' );

    // Output OGP tags
    echo "\n<!-- OGP -->\n";
    echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url( $og_url ) . '">' . "\n";
    echo '<meta property="og:image" content="' . esc_url( $og_image ) . '">' . "\n";
    echo '<meta property="og:type" content="' . esc_attr( $og_type ) . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr( $site_name ) . '">' . "\n";
    echo '<meta property="og:locale" content="ja_JP">' . "\n";

    // Twitter Card
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr( $og_title ) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr( $og_description ) . '">' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url( $og_image ) . '">' . "\n";
    echo "<!-- /OGP -->\n";
}
add_action( 'wp_head', 'rikalog_ogp_meta', 5 );

/**
 * Favicon
 */
function rikalog_favicon() {
    if ( has_site_icon() ) {
        return; // WordPress Customizer で設定済みの場合はスキップ
    }
    $assets = get_template_directory_uri() . '/assets';
    echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( $assets . '/favicon.svg' ) . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $assets . '/favicon-32.png' ) . '">' . "\n";
    echo '<link rel="icon" type="image/png" sizes="192x192" href="' . esc_url( $assets . '/favicon-192.png' ) . '">' . "\n";
    echo '<link rel="apple-touch-icon" href="' . esc_url( $assets . '/apple-touch-icon.png' ) . '">' . "\n";
}
add_action( 'wp_head', 'rikalog_favicon', 2 );

/**
 * Dark Mode Anti-Flicker Script (inline in head)
 */
function rikalog_dark_mode_inline_script() {
    ?>
    <script>
    (function() {
        var theme = localStorage.getItem('rikalog-theme');
        if (!theme) {
            theme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', theme);
    })();
    </script>
    <?php
}
add_action( 'wp_head', 'rikalog_dark_mode_inline_script', 1 );

/**
 * Contact Form 7 - テーマのスタイルに合わせるCSS追加
 */
function rikalog_cf7_styles() {
    if ( ! class_exists( 'WPCF7' ) ) {
        return;
    }
    $css = '
    .wpcf7 { max-width: 100%; }
    .wpcf7-form p { margin-bottom: 20px; }
    .wpcf7-form label { display: block; font-size: 0.9rem; font-weight: 600; margin-bottom: 8px; color: var(--color-text); }
    .wpcf7-form input[type="text"],
    .wpcf7-form input[type="email"],
    .wpcf7-form input[type="tel"],
    .wpcf7-form input[type="url"],
    .wpcf7-form textarea {
        width: 100%; padding: 13px 18px; border: 1px solid var(--color-input-border);
        border-radius: 10px; background: var(--color-input-bg); color: var(--color-text);
        font-size: 0.95rem; font-family: inherit; outline: none;
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }
    .wpcf7-form input:focus,
    .wpcf7-form textarea:focus {
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px var(--color-primary-light);
    }
    .wpcf7-form textarea { min-height: 160px; resize: vertical; }
    .wpcf7-form input[type="submit"] {
        width: 100%; padding: 15px; background: var(--gradient-primary); color: #fff;
        border: none; border-radius: 12px; font-size: 1rem; font-weight: 600;
        cursor: pointer; font-family: inherit;
        transition: opacity 0.25s ease, transform 0.3s cubic-bezier(0.22, 1, 0.36, 1), box-shadow 0.3s ease;
    }
    .wpcf7-form input[type="submit"]:hover {
        opacity: 0.92; transform: translateY(-2px);
        box-shadow: 0 6px 24px rgba(212, 160, 160, 0.3);
    }
    .wpcf7-form .wpcf7-not-valid-tip { color: #C62828; font-size: 0.85rem; margin-top: 4px; }
    .wpcf7-form .wpcf7-response-output {
        padding: 16px 20px; border-radius: 12px; margin: 20px 0 0; font-size: 0.95rem;
    }
    .wpcf7-form.sent .wpcf7-response-output {
        background: rgba(76, 175, 80, 0.08); border-color: rgba(76, 175, 80, 0.25); color: #2E7D32;
    }
    .wpcf7-form.failed .wpcf7-response-output,
    .wpcf7-form.invalid .wpcf7-response-output {
        background: rgba(244, 67, 54, 0.08); border-color: rgba(244, 67, 54, 0.25); color: #C62828;
    }
    ';
    wp_add_inline_style( 'rikalog-style', $css );
}
add_action( 'wp_enqueue_scripts', 'rikalog_cf7_styles', 20 );

/**
 * Pagination
 */
function rikalog_pagination() {
    the_posts_pagination( array(
        'mid_size'  => 2,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
    ) );
}

/**
 * Custom Comment Callback
 */
function rikalog_comment_callback( $comment, $args, $depth ) {
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment-item' ); ?>>
        <div class="comment-meta">
            <div class="comment-author-avatar">
                <?php echo get_avatar( $comment, 40 ); ?>
            </div>
            <div>
                <div class="comment-author-name"><?php comment_author(); ?></div>
                <div class="comment-date"><?php comment_date(); ?> <?php comment_time(); ?></div>
            </div>
        </div>
        <div class="comment-body">
            <?php comment_text(); ?>
        </div>
        <?php
        comment_reply_link( array_merge( $args, array(
            'depth'     => $depth,
            'max_depth' => $args['max_depth'],
            'class'     => 'comment-reply-link',
        ) ) );
        ?>
    <?php
}

/**
 * Add body class for page templates
 */
function rikalog_body_classes( $classes ) {
    if ( is_singular() && ! is_page_template() ) {
        $classes[] = 'singular';
    }
    return $classes;
}
add_filter( 'body_class', 'rikalog_body_classes' );

/**
 * りかさんの年齢を自動計算（1970/6/21生まれ）
 */
function rikalog_age() {
    $birthday = new DateTime( '1970-06-21' );
    $today    = new DateTime( 'today' );
    return (int) $birthday->diff( $today )->y;
}

/**
 * Reading Time (日本語400文字/分)
 */
function rikalog_reading_time( $post_id = null ) {
    if ( ! $post_id ) {
        $post_id = get_the_ID();
    }
    $content = get_post_field( 'post_content', $post_id );
    $content = wp_strip_all_tags( strip_shortcodes( $content ) );
    $length  = mb_strlen( $content, 'UTF-8' );
    $minutes = max( 1, (int) ceil( $length / 400 ) );
    return $minutes;
}

/**
 * SNS Share Buttons
 */
function rikalog_share_buttons() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    $url   = rawurlencode( get_permalink() );
    $title = rawurlencode( get_the_title() );

    $links = array(
        'x' => array(
            'url'   => 'https://x.com/intent/tweet?url=' . $url . '&text=' . $title,
            'label' => 'X',
            'icon'  => '<svg viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>',
        ),
        'facebook' => array(
            'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
            'label' => 'Facebook',
            'icon'  => '<svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>',
        ),
        'line' => array(
            'url'   => 'https://social-plugins.line.me/lineit/share?url=' . $url,
            'label' => 'LINE',
            'icon'  => '<svg viewBox="0 0 24 24"><path d="M19.365 9.864c.018 0 .049.002.081.007C19.553 4.334 15.424 0 12.001 0 5.372 0 .001 4.474.001 9.98c0 4.66 3.717 8.768 8.837 9.761l-.454 2.14c-.07.33.25.59.546.417l3.19-1.864c.12.006.24.012.362.012h.065c.106.006.213.008.32.008 5.412 0 10.134-3.744 10.134-9.154 0-.34-.016-.675-.046-1.006-.09-.964-.66-1.465-1.278-1.465h-2.31v-.965zm-12.216 2.89H5.147a.498.498 0 01-.497-.497V8.37a.498.498 0 01.993 0v3.39h1.506a.498.498 0 010 .993zm2.127-.497a.498.498 0 01-.993 0v-3.89a.498.498 0 01.993 0zm4.438.497h-.003a.498.498 0 01-.396-.2l-2.14-2.877v2.58a.497.497 0 01-.992 0V8.37a.497.497 0 01.497-.497h.004c.156 0 .31.075.397.2l2.138 2.877V8.37a.498.498 0 01.993 0v3.887a.498.498 0 01-.498.497zm3.69-.966a.498.498 0 010 .993h-2.002a.498.498 0 01-.497-.497V8.37a.498.498 0 01.497-.497h2.002a.498.498 0 010 .993h-1.506v.989h1.506a.498.498 0 010 .992h-1.506v.99z"/></svg>',
        ),
        'hatena' => array(
            'url'   => 'https://b.hatena.ne.jp/add?mode=confirm&url=' . $url . '&title=' . $title,
            'label' => 'はてブ',
            'icon'  => '<svg viewBox="0 0 24 24"><path d="M20.47 2H3.53A1.45 1.45 0 002 3.47v17.06A1.45 1.45 0 003.47 22h17.06c.8 0 1.47-.65 1.47-1.47V3.47A1.52 1.52 0 0020.53 2zM8.8 17.12H6.56V6.88H8.8zM15.44 17.12h-4.28l2.14-3.93-2.14-3.93h4.28l-2.14 3.93z"/></svg>',
        ),
    );

    echo '<div class="share-buttons">';
    echo '<span class="share-buttons-label">Share</span>';
    foreach ( $links as $key => $data ) {
        echo '<a href="' . esc_url( $data['url'] ) . '" class="share-btn share-btn--' . esc_attr( $key ) . '" target="_blank" rel="noopener noreferrer" aria-label="' . esc_attr( $data['label'] ) . 'でシェア">';
        echo '<span class="share-btn-icon">' . $data['icon'] . '</span>';
        echo '<span class="share-btn-text">' . esc_html( $data['label'] ) . '</span>';
        echo '</a>';
    }
    echo '</div>';
}

/**
 * Get Blog page URL
 */
function rikalog_get_blog_url() {
    $blog_page_id = (int) get_option( 'page_for_posts' );
    if ( $blog_page_id ) {
        return get_permalink( $blog_page_id );
    }
    $page = get_page_by_path( 'blog' );
    return $page ? get_permalink( $page->ID ) : home_url( '/blog/' );
}

/**
 * Fallback Menu (when no menu is assigned)
 */
function rikalog_fallback_menu() {
    $blog_url = rikalog_get_blog_url();

    $categories = get_categories( array( 'hide_empty' => false ) );

    echo '<ul>';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
    echo '<li><a href="' . esc_url( $blog_url ) . '">Blog</a></li>';

    // Categories with dropdown
    echo '<li class="menu-item-has-children">';
    echo '<a href="#">Categories</a>';
    if ( $categories ) {
        echo '<ul class="sub-menu">';
        foreach ( $categories as $cat ) {
            echo '<li><a href="' . esc_url( get_category_link( $cat->term_id ) ) . '">' . esc_html( $cat->name ) . '</a></li>';
        }
        echo '</ul>';
    }
    echo '</li>';

    echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">About</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact</a></li>';
    echo '</ul>';
}

/**
 * Link Card Block
 */
require get_template_directory() . '/inc/blocks/link-card.php';

/**
 * Marker (Highlighter) Format
 */
require get_template_directory() . '/inc/blocks/marker.php';

/* =========================================================================
   Login Page Customization
   ========================================================================= */

/**
 * Custom login page styles.
 */
function rikalog_login_styles() {
    $logo_url = '';
    $logo_css = '';
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    if ( $custom_logo_id ) {
        $logo_url = wp_get_attachment_image_url( $custom_logo_id, 'medium' );
    }

    if ( $logo_url ) {
        $logo_css = '
        #login h1 a {
            background-image: url(' . esc_url( $logo_url ) . ');
            background-size: contain;
            background-position: center;
            width: 240px;
            height: 80px;
        }';
    } else {
        $logo_css = '
        #login h1 a {
            background: none;
            width: auto;
            height: auto;
            font-size: 2.2rem;
            font-weight: 700;
            font-family: "Noto Sans JP", sans-serif;
            text-indent: 0;
            color: transparent;
            background-image: linear-gradient(135deg, #D4A0A0 0%, #C4B0D4 100%);
            -webkit-background-clip: text;
            background-clip: text;
            line-height: 1.4;
            padding-bottom: 16px;
        }';
    }
    ?>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;600;700&display=swap');

        body.login {
            background: #FAF8F6;
            font-family: "Noto Sans JP", sans-serif;
        }

        <?php echo $logo_css; ?>

        #loginform {
            background: #fff;
            border: 1px solid #EBE4E1;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(212, 160, 160, 0.08);
            padding: 28px 24px;
        }

        #loginform label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #3C3C3C;
        }

        #loginform input[type="text"],
        #loginform input[type="password"] {
            border: 1px solid #E0D8D5;
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.95rem;
            font-family: inherit;
            transition: border-color 0.25s ease, box-shadow 0.25s ease;
        }

        #loginform input[type="text"]:focus,
        #loginform input[type="password"]:focus {
            border-color: #D4A0A0;
            box-shadow: 0 0 0 3px rgba(212, 160, 160, 0.13);
            outline: none;
        }

        #loginform .button-primary {
            background: linear-gradient(135deg, #D4A0A0 0%, #C4B0D4 100%);
            border: none;
            border-radius: 10px;
            padding: 8px 20px;
            font-size: 0.95rem;
            font-weight: 600;
            font-family: inherit;
            color: #fff;
            text-shadow: none;
            transition: opacity 0.25s ease, transform 0.3s cubic-bezier(0.22, 1, 0.36, 1),
                        box-shadow 0.3s ease;
            height: auto;
            line-height: 1.6;
        }

        #loginform .button-primary:hover,
        #loginform .button-primary:focus {
            background: linear-gradient(135deg, #D4A0A0 0%, #C4B0D4 100%);
            opacity: 0.9;
            transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(212, 160, 160, 0.3);
            color: #fff;
        }

        #loginform .forgetmenot label {
            font-weight: 400;
            font-size: 0.85rem;
            color: #6B6B6B;
        }

        #nav, #backtoblog {
            text-align: center;
        }

        #nav a, #backtoblog a {
            color: #D4A0A0;
            font-size: 0.88rem;
            transition: color 0.25s ease;
        }

        #nav a:hover, #backtoblog a:hover {
            color: #B07878;
        }

        .login .message,
        .login .success {
            border-left-color: #D4A0A0;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(212, 160, 160, 0.08);
        }

        .login #login_error {
            border-left-color: #C62828;
            border-radius: 10px;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'rikalog_login_styles' );

/**
 * Login logo links to home page.
 */
function rikalog_login_logo_url() {
    return home_url( '/' );
}
add_filter( 'login_headerurl', 'rikalog_login_logo_url' );

/**
 * Login logo title shows site name.
 */
function rikalog_login_logo_title() {
    return 'RikaLog';
}
add_filter( 'login_headertext', 'rikalog_login_logo_title' );

/* =========================================================================
   Security Hardening
   ========================================================================= */

/**
 * 1. /?author=N によるユーザー名漏洩を防止
 */
function rikalog_block_author_scanning() {
    if ( ! is_admin() && isset( $_GET['author'] ) ) {
        wp_safe_redirect( home_url( '/' ), 301 );
        exit;
    }
}
add_action( 'init', 'rikalog_block_author_scanning' );

/**
 * 2. REST API の /wp/v2/users エンドポイントを無効化（ユーザー名漏洩防止）
 */
function rikalog_restrict_rest_users( $result, $server, $request ) {
    $route = $request->get_route();
    if ( preg_match( '/\/wp\/v2\/users/', $route ) && ! current_user_can( 'list_users' ) ) {
        return new WP_Error(
            'rest_forbidden',
            'アクセスが拒否されました。',
            array( 'status' => 403 )
        );
    }
    return $result;
}
add_filter( 'rest_pre_dispatch', 'rikalog_restrict_rest_users', 10, 3 );

/**
 * 3. WordPress バージョン情報を削除
 */
remove_action( 'wp_head', 'wp_generator' );

function rikalog_remove_version_from_assets( $src ) {
    if ( strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
        $src = remove_query_arg( 'ver', $src );
    }
    return $src;
}
add_filter( 'style_loader_src', 'rikalog_remove_version_from_assets' );
add_filter( 'script_loader_src', 'rikalog_remove_version_from_assets' );

/**
 * 4. 不要な head タグを削除
 */
remove_action( 'wp_head', 'rsd_link' );                    // Really Simple Discovery
remove_action( 'wp_head', 'wlwmanifest_link' );            // Windows Live Writer
remove_action( 'wp_head', 'wp_shortlink_wp_head' );        // 短縮URL
remove_action( 'wp_head', 'feed_links_extra', 3 );         // 追加フィード
remove_action( 'wp_head', 'rest_output_link_wp_head' );    // REST API リンク
remove_action( 'wp_head', 'wp_oembed_add_discovery_links' ); // oEmbed

/**
 * 5. XML-RPC を無効化（ブルートフォース攻撃防止）
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

function rikalog_remove_xmlrpc_pingback( $headers ) {
    unset( $headers['X-Pingback'] );
    return $headers;
}
add_filter( 'wp_headers', 'rikalog_remove_xmlrpc_pingback' );

/**
 * 6. ログインエラーメッセージを曖昧化（ユーザー名の存在を隠す）
 */
function rikalog_login_error_message() {
    return 'ログイン情報が正しくありません。';
}
add_filter( 'login_errors', 'rikalog_login_error_message' );

/**
 * 7. セキュリティヘッダーを追加
 */
function rikalog_security_headers() {
    if ( is_admin() ) {
        return;
    }
    header( 'X-Content-Type-Options: nosniff' );
    header( 'X-Frame-Options: SAMEORIGIN' );
    header( 'X-XSS-Protection: 1; mode=block' );
    header( 'Referrer-Policy: strict-origin-when-cross-origin' );
    header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'rikalog_security_headers' );

/**
 * 8. コメント欄の HTML タグを制限（XSS対策）
 */
function rikalog_allowed_comment_tags( $allowed ) {
    $allowed = array(
        'a'      => array( 'href' => true, 'title' => true ),
        'em'     => array(),
        'strong' => array(),
        'code'   => array(),
        'br'     => array(),
    );
    return $allowed;
}
add_filter( 'pre_comment_content', 'wp_filter_kses' );

/**
 * 9. 管理画面のファイルエディタを無効化
 */
if ( ! defined( 'DISALLOW_FILE_EDIT' ) ) {
    define( 'DISALLOW_FILE_EDIT', true );
}