<?php
/**
 * Static page.
 *
 * @package Schematic
 */

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'entry entry--page' ); ?>>
		<?php
		$content = '';
		$has_toc = false;
		if ( ! post_password_required() ) {
			ob_start();
			the_content();
			wp_link_pages(
				array(
					'before' => '<nav class="page-links">' . esc_html__( 'Pages:', 'schematic' ),
					'after'  => '</nav>',
				)
			);
			$content = ob_get_clean();
			$outline = schematic_outline();
			$has_toc = count( $outline ) >= 2;
		}
		?>
		<div class="reading<?php echo $has_toc ? ' reading--toc' : ''; ?>">
			<header class="entry-header">
				<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>
			<?php if ( post_password_required() ) : ?>
				<div class="entry-locked">
					<?php echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core form. ?>
				</div>
			<?php else : ?>
				<?php if ( $has_toc ) : ?>
					<?php schematic_render_toc( $outline ); ?>
				<?php endif; ?>
				<div class="entry-column">
					<div class="entry-content">
						<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- filtered post content. ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</article>
	<?php
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
endwhile;

get_footer();
