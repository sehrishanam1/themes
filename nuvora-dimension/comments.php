<?php
/**
 * Comments Template
 *
 * @package NuvoraDimension
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="nuvora-comments-area">

	<?php if ( have_comments() ) : ?>

		<h3 class="comments-title">
			<?php
			$nuvora_comment_count = get_comments_number();
			if ( '1' === $nuvora_comment_count ) {
				printf(
					/* translators: %s: post title */
					esc_html__( 'One comment on &ldquo;%s&rdquo;', 'nuvora-dimension' ),
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: post title */
					esc_html( _nx( '%1$s comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', $nuvora_comment_count, 'comments title', 'nuvora-dimension' ) ),
					number_format_i18n( $nuvora_comment_count ),
					'<span>' . esc_html( get_the_title() ) . '</span>'
				);
			}
			?>
		</h3>

		<ol class="comment-list">
			<?php
			wp_list_comments( array(
				'style'       => 'ol',
				'short_ping'  => true,
				'avatar_size' => 42,
			) );
			?>
		</ol>

		<?php the_comments_pagination( array(
			'prev_text' => esc_html__( '&larr; Older Comments', 'nuvora-dimension' ),
			'next_text' => esc_html__( 'Newer Comments &rarr;', 'nuvora-dimension' ),
		) ); ?>

	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'nuvora-dimension' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
