<?php
/**
 * Template Name: Aboutページ
 * Template Post Type: page
 */
get_header();
?>

<div class="site-main-wrap">
    <main class="site-main full-width">
        <?php while ( have_posts() ) : the_post(); ?>
            <div class="about-page">
                <header class="about-header">
                    <div class="about-avatar">
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
                    <h1><?php the_title(); ?></h1>
                    <p>RikaLogについて</p>
                </header>

                <div class="about-content">
                    <?php the_content(); ?>

                    <?php if ( ! get_the_content() ) : ?>
                    <h2>はじめまして、りかです</h2>
                    <p>
                        <?php echo rikalog_age(); ?>歳、コールセンターで働いています。<br>
                        毎日いろいろな方のお話を聞きながら、「人の気持ちに寄り添う」ことの大切さを日々感じています。
                    </p>

                    <h2>このブログについて</h2>
                    <p>
                        仕事で感じたこと、日常のちょっとした気づき、年齢を重ねて思うこと——<br>
                        そんな日々の気持ちを、ありのままに綴っています。
                    </p>
                    <p>
                        同じように頑張っている方、ちょっと疲れている方、<br>
                        このブログが少しでも心の休憩所になれたら嬉しいです。
                    </p>

                    <h2>カテゴリーのご紹介</h2>
                    <p>
                        <strong>仕事</strong> — コールセンターでの日々や、働くことについて<br>
                        <strong>体験談・実話</strong> — 実際にあった出来事や経験談<br>
                        <strong>心・メンタル・生き方</strong> — 心の持ち方、生き方について思うこと
                    </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </main>
</div>

<?php get_footer(); ?>
