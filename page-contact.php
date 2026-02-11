<?php
/**
 * Template Name: お問い合わせページ
 * Template Post Type: page
 */
get_header();
?>

<div class="site-main-wrap">
    <main class="site-main full-width">
        <div class="contact-page">
            <header class="contact-header">
                <h1><?php the_title(); ?></h1>
                <p>お気軽にメッセージをお寄せください。</p>
            </header>

            <div class="contact-form-wrap">
                <?php the_content(); ?>
            </div>
        </div>
    </main>
</div>

<?php get_footer(); ?>
