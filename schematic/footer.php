<?php
/**
 * Site footer.
 *
 * @package Schematic
 */
?>
</main>
<footer class="site-footer">
	<div class="wrap site-footer__inner">
		<div class="site-footer__brand">
			<p class="site-footer__name"><?php bloginfo( 'name' ); ?></p>
			<p class="site-footer__tag"><?php echo esc_html( schematic_tagline() ); ?></p>
		</div>
		<?php if ( has_nav_menu( 'footer' ) ) : ?>
			<nav class="footer-nav-wrap" aria-label="<?php esc_attr_e( 'Footer', 'schematic' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'footer-nav',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
			</nav>
		<?php endif; ?>
		<?php if ( is_active_sidebar( 'footer' ) ) : ?>
			<div class="footer-widgets">
				<?php dynamic_sidebar( 'footer' ); ?>
			</div>
		<?php endif; ?>
		<p class="colophon">
			<?php
			printf(
				/* translators: %s: current year */
				esc_html__( '© %s', 'schematic' ),
				esc_html( wp_date( 'Y' ) )
			);
			echo ' · ';
			esc_html_e( 'Set in Fraunces, Source Serif 4, and IBM Plex Mono.', 'schematic' );
			?>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
