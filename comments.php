<?php
if ( post_password_required() ) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if ( have_comments() ) : ?>
        <h2 class="comments-title">
            コメント（<?php echo get_comments_number(); ?>件）
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments( array(
                'style'      => 'ol',
                'short_ping' => true,
                'callback'   => 'rikalog_comment_callback',
            ) );
            ?>
        </ol>

        <?php
        the_comments_navigation( array(
            'prev_text' => '&laquo; 古いコメント',
            'next_text' => '新しいコメント &raquo;',
        ) );
        ?>
    <?php endif; ?>

    <?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
        <p class="no-comments">コメントは受け付けていません。</p>
    <?php endif; ?>

    <?php
    comment_form( array(
        'title_reply'        => 'コメントを残す',
        'label_submit'       => 'コメントを送信',
        'comment_notes_before' => '',
    ) );
    ?>
</div>
