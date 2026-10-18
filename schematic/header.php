<?php
/**
 * Document head and site header.
 *
 * @package Schematic
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script>
		(function () {
			var theme = 'light';
			try {
				var stored = localStorage.getItem('schematic-theme');
				if (stored === 'dark' || stored === 'light') {
					theme = stored;
				} else if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
					theme = 'dark';
				}
			} catch (e) {}
			document.documentElement.setAttribute('data-theme', theme);
			document.documentElement.classList.add('js');
		}());
	</script>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#content"><?php esc_html_e( 'Skip to content', 'schematic' ); ?></a>
<?php if ( is_singular( 'post' ) ) : ?>
	<div class="progress" data-progress aria-hidden="true"><div class="progress__bar" data-progress-bar></div></div>
<?php endif; ?>
<header class="site-header">
	<div class="wrap site-header__inner">
		<a class="site-mark" href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php
			if ( has_custom_logo() ) {
				echo wp_get_attachment_image(
					(int) get_theme_mod( 'custom_logo' ),
					'thumbnail',
					false,
					array(
						'class' => 'site-mark__logo',
						'alt'   => '',
					)
				);
			} else {
				echo schematic_mark(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
			}
			?>
			<span class="site-mark__name"><?php bloginfo( 'name' ); ?></span>
		</a>
		<button type="button" class="nav-toggle" data-nav-toggle aria-expanded="false" aria-controls="site-navigation">
			<span data-nav-label><?php esc_html_e( 'Menu', 'schematic' ); ?></span>
		</button>
		<nav id="site-navigation" class="primary-nav" data-nav aria-label="<?php esc_attr_e( 'Primary', 'schematic' ); ?>">
			<?php schematic_primary_nav(); ?>
		</nav>
		<div class="site-tools">
			<?php get_search_form(); ?>
			<button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false">
				<span class="screen-reader-text"><?php esc_html_e( 'Toggle color theme', 'schematic' ); ?></span>
				<span class="theme-toggle__icon" aria-hidden="true"></span>
			</button>
		</div>
	</div>
</header>
<main id="content" class="site-main">
