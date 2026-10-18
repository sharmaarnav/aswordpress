<?php
/**
 * Comments.
 *
 * @package Schematic
 */

if ( post_password_required() ) {
	return;
}
?>
<div class="wrap comments-wrap">
	<section id="comments" class="comments-area" aria-label="<?php esc_attr_e( 'Discussion', 'schematic' ); ?>">
		<?php if ( have_comments() ) : ?>
			<h2 class="comments-title">
				<?php
				$count = get_comments_number();
				printf(
					esc_html(
						/* translators: %s: comment count */
						_n( '%s note in the margin', '%s notes in the margin', $count, 'schematic' )
					),
					esc_html( number_format_i18n( $count ) )
				);
				?>
			</h2>
			<ol class="comment-list">
				<?php
				wp_list_comments(
					array(
						'style'       => 'ol',
						'short_ping'  => true,
						'avatar_size' => 40,
						'callback'    => 'schematic_comment',
					)
				);
				?>
			</ol>
			<?php
			the_comments_navigation(
				array(
					'prev_text' => __( 'Older notes', 'schematic' ),
					'next_text' => __( 'Newer notes', 'schematic' ),
				)
			);
			?>
		<?php endif; ?>

		<?php if ( ! comments_open() && get_comments_number() ) : ?>
			<p class="comments-closed"><?php esc_html_e( 'Comments are closed.', 'schematic' ); ?></p>
		<?php endif; ?>

		<?php
		comment_form(
			array(
				'title_reply'        => __( 'Leave a note', 'schematic' ),
				'title_reply_before' => '<h2 id="reply-title" class="comment-reply-title">',
				'title_reply_after'  => '</h2>',
				'label_submit'       => __( 'Post note', 'schematic' ),
				'class_submit'       => 'submit button',
			)
		);
		?>
	</section>
</div>
