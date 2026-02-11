<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="header-inner">
        <div class="site-branding">
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>">
                Rika<span>Log</span>
            </a>
        </div>

        <div class="header-right">
            <nav class="main-navigation" aria-label="メインメニュー">
                <?php
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'fallback_cb'    => 'rikalog_fallback_menu',
                    'depth'          => 2,
                ) );
                ?>
            </nav>

            <button class="theme-toggle" aria-label="テーマ切替">
                <svg class="icon-sun" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="5"/>
                    <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/>
                </svg>
                <svg class="icon-moon" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>

            <button class="hamburger" aria-label="メニュー" aria-expanded="false">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    <div class="mobile-nav-overlay" aria-hidden="true">
        <?php
        wp_nav_menu( array(
            'theme_location' => 'primary',
            'container'      => false,
            'fallback_cb'    => 'rikalog_fallback_menu',
            'depth'          => 2,
        ) );
        ?>
    </div>
</header>

<div class="site-container">
<?php rikalog_breadcrumb(); ?>
