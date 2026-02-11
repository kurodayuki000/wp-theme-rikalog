<?php
/**
 * Link Card Block
 *
 * Gutenberg block for displaying link cards with OGP data.
 *
 * @package RikaLog
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Get OGP data from URL with 24-hour caching.
 *
 * @param string $url The URL to fetch OGP data from.
 * @return array OGP data array.
 */
function rikalog_get_ogp_data( $url ) {
    $cache_key = 'rikalog_ogp_' . md5( $url );
    $cached    = get_transient( $cache_key );

    if ( false !== $cached ) {
        return $cached;
    }

    $data = array(
        'title'       => '',
        'description' => '',
        'image'       => '',
        'site_name'   => '',
        'favicon'     => '',
    );

    $site_url = home_url();
    $is_internal = ( strpos( $url, $site_url ) === 0 );

    if ( $is_internal ) {
        $post_id = url_to_postid( $url );
        if ( $post_id ) {
            $post = get_post( $post_id );
            $data['title'] = get_the_title( $post_id );
            $data['description'] = wp_trim_words( get_the_excerpt( $post_id ), 40 );
            if ( has_post_thumbnail( $post_id ) ) {
                $thumb = get_the_post_thumbnail_url( $post_id, 'rikalog-card' );
                if ( $thumb ) {
                    $data['image'] = $thumb;
                }
            }
            $data['site_name'] = get_bloginfo( 'name' );
            if ( has_site_icon() ) {
                $data['favicon'] = get_site_icon_url( 32 );
            }
            set_transient( $cache_key, $data, DAY_IN_SECONDS );
            return $data;
        }
    }

    $response = wp_remote_get( $url, array(
        'timeout'    => 10,
        'user-agent' => 'Mozilla/5.0 (compatible; RikaLog OGP Fetcher)',
        'sslverify'  => false,
    ) );

    if ( is_wp_error( $response ) ) {
        return $data;
    }

    $body = wp_remote_retrieve_body( $response );
    if ( empty( $body ) ) {
        return $data;
    }

    // Detect charset and convert to UTF-8.
    $charset = '';
    if ( preg_match( '/<meta[^>]+charset=["\']?([^"\'\s;>]+)/i', $body, $m ) ) {
        $charset = $m[1];
    } elseif ( preg_match( '/<meta[^>]+content=["\'][^"\']*charset=([^"\'\s;]+)/i', $body, $m ) ) {
        $charset = $m[1];
    }
    if ( $charset && ! in_array( strtolower( $charset ), array( 'utf-8', 'utf8' ), true ) ) {
        $body = mb_convert_encoding( $body, 'UTF-8', $charset );
    }

    // Parse OGP tags.
    if ( preg_match_all( '/<meta\s+(?:property|name)=["\']og:([^"\']+)["\']\s+content=["\']([^"\']*)["\'][^>]*>/i', $body, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            switch ( $match[1] ) {
                case 'title':
                    $data['title'] = html_entity_decode( $match[2], ENT_QUOTES, 'UTF-8' );
                    break;
                case 'description':
                    $data['description'] = html_entity_decode( $match[2], ENT_QUOTES, 'UTF-8' );
                    break;
                case 'image':
                    $data['image'] = $match[2];
                    break;
                case 'site_name':
                    $data['site_name'] = html_entity_decode( $match[2], ENT_QUOTES, 'UTF-8' );
                    break;
            }
        }
    }

    // Also check content before property order.
    if ( preg_match_all( '/<meta\s+content=["\']([^"\']*)["\']\s+(?:property|name)=["\']og:([^"\']+)["\'][^>]*>/i', $body, $matches, PREG_SET_ORDER ) ) {
        foreach ( $matches as $match ) {
            switch ( $match[2] ) {
                case 'title':
                    if ( empty( $data['title'] ) ) {
                        $data['title'] = html_entity_decode( $match[1], ENT_QUOTES, 'UTF-8' );
                    }
                    break;
                case 'description':
                    if ( empty( $data['description'] ) ) {
                        $data['description'] = html_entity_decode( $match[1], ENT_QUOTES, 'UTF-8' );
                    }
                    break;
                case 'image':
                    if ( empty( $data['image'] ) ) {
                        $data['image'] = $match[1];
                    }
                    break;
                case 'site_name':
                    if ( empty( $data['site_name'] ) ) {
                        $data['site_name'] = html_entity_decode( $match[1], ENT_QUOTES, 'UTF-8' );
                    }
                    break;
            }
        }
    }

    // Fallback: title tag.
    if ( empty( $data['title'] ) && preg_match( '/<title[^>]*>([^<]+)<\/title>/i', $body, $m ) ) {
        $data['title'] = html_entity_decode( trim( $m[1] ), ENT_QUOTES, 'UTF-8' );
    }

    // Fallback: meta description.
    if ( empty( $data['description'] ) && preg_match( '/<meta\s+name=["\']description["\']\s+content=["\']([^"\']*)["\'][^>]*>/i', $body, $m ) ) {
        $data['description'] = html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' );
    }
    if ( empty( $data['description'] ) && preg_match( '/<meta\s+content=["\']([^"\']*)["\']\s+name=["\']description["\'][^>]*>/i', $body, $m ) ) {
        $data['description'] = html_entity_decode( $m[1], ENT_QUOTES, 'UTF-8' );
    }

    // Favicon.
    $parsed = wp_parse_url( $url );
    $origin = $parsed['scheme'] . '://' . $parsed['host'];
    if ( preg_match( '/<link[^>]+rel=["\'](?:shortcut )?icon["\'][^>]+href=["\']([^"\']+)["\'][^>]*>/i', $body, $m ) ) {
        $favicon = $m[1];
        if ( strpos( $favicon, '//' ) === 0 ) {
            $favicon = $parsed['scheme'] . ':' . $favicon;
        } elseif ( strpos( $favicon, '/' ) === 0 ) {
            $favicon = $origin . $favicon;
        } elseif ( strpos( $favicon, 'http' ) !== 0 ) {
            $favicon = $origin . '/' . $favicon;
        }
        $data['favicon'] = $favicon;
    } else {
        $data['favicon'] = $origin . '/favicon.ico';
    }

    // Site name fallback.
    if ( empty( $data['site_name'] ) ) {
        $data['site_name'] = $parsed['host'];
    }

    set_transient( $cache_key, $data, DAY_IN_SECONDS );

    return $data;
}

/**
 * Register the link card block.
 */
function rikalog_register_link_card_block() {
    if ( ! function_exists( 'register_block_type' ) ) {
        return;
    }

    $link_card_js = get_template_directory() . '/dist/blocks/link-card.js';
    wp_register_script(
        'rikalog-link-card-editor',
        get_template_directory_uri() . '/dist/blocks/link-card.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-api-fetch' ),
        file_exists( $link_card_js ) ? (string) filemtime( $link_card_js ) : wp_get_theme()->get( 'Version' ),
        true
    );

    // Editor style for link card preview.
    wp_register_style( 'rikalog-link-card-editor-style', false );
    wp_add_inline_style( 'rikalog-link-card-editor-style', rikalog_link_card_editor_css() );

    register_block_type( 'rikalog/link-card', array(
        'editor_script'   => 'rikalog-link-card-editor',
        'editor_style'    => 'rikalog-link-card-editor-style',
        'render_callback' => 'rikalog_render_link_card_block',
        'attributes'      => array(
            'url'         => array( 'type' => 'string', 'default' => '' ),
            'title'       => array( 'type' => 'string', 'default' => '' ),
            'description' => array( 'type' => 'string', 'default' => '' ),
            'image'       => array( 'type' => 'string', 'default' => '' ),
            'site_name'   => array( 'type' => 'string', 'default' => '' ),
            'favicon'     => array( 'type' => 'string', 'default' => '' ),
            'nofollow'    => array( 'type' => 'boolean', 'default' => false ),
        ),
    ) );
}
add_action( 'init', 'rikalog_register_link_card_block' );

/**
 * Server-side render callback for the link card block.
 *
 * @param array $attributes Block attributes.
 * @return string Rendered HTML.
 */
function rikalog_render_link_card_block( $attributes ) {
    $url         = ! empty( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '';
    $title       = ! empty( $attributes['title'] ) ? $attributes['title'] : '';
    $description = ! empty( $attributes['description'] ) ? $attributes['description'] : '';
    $image       = ! empty( $attributes['image'] ) ? esc_url( $attributes['image'] ) : '';
    $site_name   = ! empty( $attributes['site_name'] ) ? $attributes['site_name'] : '';
    $favicon     = ! empty( $attributes['favicon'] ) ? esc_url( $attributes['favicon'] ) : '';
    $nofollow    = ! empty( $attributes['nofollow'] );

    if ( empty( $url ) ) {
        return '';
    }

    // If no title, try fetching OGP.
    if ( empty( $title ) ) {
        $ogp = rikalog_get_ogp_data( $url );
        $title       = $ogp['title'];
        $description = $ogp['description'];
        $image       = $ogp['image'];
        $site_name   = $ogp['site_name'];
        $favicon     = $ogp['favicon'];
    }

    if ( empty( $title ) ) {
        $title = $url;
    }

    $rel = 'noopener noreferrer';
    if ( $nofollow ) {
        $rel .= ' nofollow';
    }

    $parsed  = wp_parse_url( $url );
    $domain  = isset( $parsed['host'] ) ? $parsed['host'] : '';

    if ( empty( $site_name ) ) {
        $site_name = $domain;
    }

    $is_internal = ( strpos( $url, home_url() ) === 0 );
    $target      = $is_internal ? '' : ' target="_blank"';

    ob_start();
    ?>
    <div class="link-card-wrap">
        <a href="<?php echo $url; ?>" class="link-card"<?php echo $target; ?> rel="<?php echo esc_attr( $rel ); ?>">
            <?php if ( $image ) : ?>
                <div class="link-card-thumbnail">
                    <img src="<?php echo $image; ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                </div>
            <?php endif; ?>
            <div class="link-card-content">
                <div class="link-card-title"><?php echo esc_html( $title ); ?></div>
                <?php if ( $description ) : ?>
                    <div class="link-card-description"><?php echo esc_html( $description ); ?></div>
                <?php endif; ?>
                <div class="link-card-meta">
                    <?php if ( $favicon ) : ?>
                        <img class="link-card-favicon" src="<?php echo $favicon; ?>" alt="" width="16" height="16" loading="lazy">
                    <?php endif; ?>
                    <span class="link-card-domain"><?php echo esc_html( $site_name ); ?></span>
                </div>
            </div>
        </a>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Register REST API route for OGP fetching.
 */
function rikalog_register_ogp_rest_route() {
    register_rest_route( 'rikalog/v1', '/ogp', array(
        'methods'             => 'GET',
        'callback'            => 'rikalog_rest_get_ogp',
        'permission_callback' => function () {
            return current_user_can( 'edit_posts' );
        },
        'args'                => array(
            'url' => array(
                'required'          => true,
                'sanitize_callback' => 'esc_url_raw',
            ),
        ),
    ) );
}
add_action( 'rest_api_init', 'rikalog_register_ogp_rest_route' );

/**
 * REST API callback to get OGP data.
 *
 * @param WP_REST_Request $request REST request object.
 * @return WP_REST_Response
 */
function rikalog_rest_get_ogp( $request ) {
    $url  = $request->get_param( 'url' );
    $data = rikalog_get_ogp_data( $url );
    return rest_ensure_response( $data );
}

/**
 * Shortcode: [linkcard url="..." title="..." nofollow="true"]
 *
 * @param array $atts Shortcode attributes.
 * @return string Rendered HTML.
 */
function rikalog_linkcard_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'url'         => '',
        'title'       => '',
        'description' => '',
        'image'       => '',
        'site_name'   => '',
        'favicon'     => '',
        'nofollow'    => 'false',
    ), $atts, 'linkcard' );

    $atts['nofollow'] = filter_var( $atts['nofollow'], FILTER_VALIDATE_BOOLEAN );

    return rikalog_render_link_card_block( $atts );
}
add_shortcode( 'linkcard', 'rikalog_linkcard_shortcode' );

/**
 * Inline CSS for the link card block in the editor.
 *
 * @return string CSS string.
 */
function rikalog_link_card_editor_css() {
    return '
/* Link Card - Editor Preview */
.link-card-editor {
    margin: 1em 0;
}

.link-card-editor .link-card {
    display: flex;
    border-radius: 12px;
    border: 1px solid #EBE4E1;
    background: #fff;
    overflow: hidden;
    text-decoration: none;
    color: #3C3C3C;
    transition: box-shadow 0.35s cubic-bezier(0.22, 1, 0.36, 1),
                transform 0.35s cubic-bezier(0.22, 1, 0.36, 1),
                border-color 0.35s ease;
}

.link-card-editor .link-card:hover {
    box-shadow: 0 6px 24px rgba(212, 160, 160, 0.22);
    transform: translateY(-2px);
    border-color: #D4A0A0;
}

.link-card-editor .link-card-thumbnail {
    width: 200px;
    flex-shrink: 0;
    overflow: hidden;
    background: linear-gradient(135deg, rgba(212,160,160,0.08) 0%, rgba(196,176,212,0.08) 50%, rgba(160,184,212,0.08) 100%);
}

.link-card-editor .link-card-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    transition: transform 0.4s cubic-bezier(0.22, 1, 0.36, 1);
}

.link-card-editor .link-card:hover .link-card-thumbnail img {
    transform: scale(1.05);
}

.link-card-editor .link-card-content {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 16px 20px;
    min-width: 0;
    flex: 1;
}

.link-card-editor .link-card-title {
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.5;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 6px;
    color: #3C3C3C;
}

.link-card-editor .link-card:hover .link-card-title {
    color: #B07878;
}

.link-card-editor .link-card-description {
    font-size: 0.82rem;
    color: #6B6B6B;
    line-height: 1.6;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    margin-bottom: 8px;
}

.link-card-editor .link-card-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    color: #999;
}

.link-card-editor .link-card-favicon {
    width: 16px;
    height: 16px;
    border-radius: 2px;
    flex-shrink: 0;
}

.link-card-editor .link-card-domain {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

/* Link Card - Placeholder */
.link-card-placeholder {
    border: 2px dashed #EBE4E1;
    border-radius: 12px;
    padding: 40px 24px;
    text-align: center;
    background: #fff;
}

.link-card-placeholder-inner {
    max-width: 440px;
    margin: 0 auto;
}

.link-card-placeholder-inner > svg {
    width: 40px;
    height: 40px;
}

.link-card-placeholder-inner p:first-of-type {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 12px 0 4px;
    color: #3C3C3C;
}
    ';
}
