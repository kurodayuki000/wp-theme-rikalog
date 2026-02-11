<?php
/**
 * Marker (Highlighter) — RichText Inline Format
 *
 * Registers a custom RichText format that lets users highlight text
 * with 5 colors: yellow, pink, blue, green, orange.
 *
 * @package RikaLog
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register editor assets for the marker format.
 */
function rikalog_register_marker_format() {
    $marker_js = get_template_directory() . '/dist/blocks/marker.js';
    wp_register_script(
        'rikalog-marker-editor',
        get_template_directory_uri() . '/dist/blocks/marker.js',
        array( 'wp-rich-text', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
        file_exists( $marker_js ) ? (string) filemtime( $marker_js ) : wp_get_theme()->get( 'Version' ),
        true
    );

    // Editor inline styles so highlights are visible while editing.
    wp_register_style( 'rikalog-marker-editor-style', false );
    wp_add_inline_style( 'rikalog-marker-editor-style', rikalog_marker_css() );
}
add_action( 'init', 'rikalog_register_marker_format' );

/**
 * Enqueue marker editor script & style on block editor screens.
 */
function rikalog_enqueue_marker_editor_assets() {
    wp_enqueue_script( 'rikalog-marker-editor' );
    wp_enqueue_style( 'rikalog-marker-editor-style' );
}
add_action( 'enqueue_block_editor_assets', 'rikalog_enqueue_marker_editor_assets' );

/**
 * Return marker CSS (shared between editor and frontend).
 *
 * @return string CSS.
 */
function rikalog_marker_css() {
    return '
.rikalog-marker-yellow  { background: linear-gradient(transparent 60%, rgba(255, 224, 102, 0.45) 60%); }
.rikalog-marker-pink    { background: linear-gradient(transparent 60%, rgba(248, 165, 194, 0.45) 60%); }
.rikalog-marker-blue    { background: linear-gradient(transparent 60%, rgba(130, 196, 255, 0.45) 60%); }
.rikalog-marker-green   { background: linear-gradient(transparent 60%, rgba(129, 212, 150, 0.45) 60%); }
.rikalog-marker-orange  { background: linear-gradient(transparent 60%, rgba(255, 183, 77, 0.45) 60%); }
    ';
}
