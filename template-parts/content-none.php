<div class="no-results">
    <h2>記事が見つかりませんでした</h2>
    <?php if ( is_search() ) : ?>
        <p>検索キーワードに一致する記事が見つかりませんでした。別のキーワードでお試しください。</p>
    <?php else : ?>
        <p>まだ記事がありません。</p>
    <?php endif; ?>
    <?php get_search_form(); ?>
</div>
