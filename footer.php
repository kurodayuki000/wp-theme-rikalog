</div><!-- .site-container -->

<footer class="site-footer">
    <div class="footer-inner">
        <nav class="footer-nav" aria-label="フッターメニュー">
            <?php
            wp_nav_menu( array(
                'theme_location' => 'footer',
                'container'      => false,
                'fallback_cb'    => false,
                'depth'          => 1,
            ) );
            ?>
        </nav>
        <p class="footer-copyright">
            &copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
        </p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
