<?php get_header(); ?>

<div class="site-main-wrap">
    <main class="site-main full-width">
        <div class="error-404-page">
            <h1>404</h1>
            <h2>ページが見つかりませんでした</h2>
            <p>お探しのページは存在しないか、移動した可能性があります。</p>
            <?php get_search_form(); ?>
            <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="back-home">トップページへ戻る</a>
        </div>
    </main>
</div>

<?php get_footer(); ?>
