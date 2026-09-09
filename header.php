<?php
/**
 * Header template.
 *
 * @package schilliger
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(schilliger_has_sidebar() ? 'has-sidebar' : 'no-sidebar'); ?>>
<?php wp_body_open(); ?>

<div id="cur" aria-hidden="true">
	<svg viewBox="0 0 20 20" fill="none">
		<path d="M2 18 L5 11 L15 1 C16 0 18.5 0 18.5 1.5 C20 3 20 5.5 18.5 7 L9 16.5 Z" fill="#18160f"/>
		<path d="M15 1 L18.5 1.5 C20 3 20 5.5 18.5 7 L17 8.5 L13 4.5 Z" fill="rgba(255,255,255,.12)"/>
		<circle cx="2.5" cy="17.5" r="1.3" fill="#2d5c3f"/>
		<path d="M2 18 L1 20.5 Q2.2 21.3 3.4 20.5 Z" fill="#2d5c3f"/>
	</svg>
</div>

<div id="bar" aria-hidden="true"></div>
<?php $header_variant = (string) get_theme_mod('schilliger_header_variant', 'a'); ?>
<?php
$latest_blog = get_posts([
	'post_type' => 'post',
	'posts_per_page' => 1,
	'post_status' => 'publish',
]);
$ticker_title = $latest_blog ? get_the_title($latest_blog[0]) : '';
$ticker_url = $latest_blog ? get_permalink($latest_blog[0]) : '';
?>
<header class="site-topbar header-variant-<?php echo esc_attr($header_variant); ?>">
	<div class="topbar-inner">
		<div class="topbar-row topbar-ticker-row">
			<div class="topbar-ticker">
				<?php if ($ticker_title && $ticker_url) : ?>
					<a href="<?php echo esc_url($ticker_url); ?>"><?php echo esc_html('Neuer Post: ' . $ticker_title); ?></a>
				<?php else : ?>
					<?php esc_html_e('Neuer Post: Aktuell kein Blogbeitrag', 'schilliger'); ?>
				<?php endif; ?>
			</div>
		</div>
		<div class="topbar-row topbar-name-row">
			<a href="<?php echo esc_url(home_url('/')); ?>" class="s-name">Michael Schilliger</a>
			<button id="mobile-toggle" aria-label="<?php esc_attr_e('Navigation', 'schilliger'); ?>">☰</button>
		</div>
		<div class="topbar-row topbar-nav-row">
			<div class="topbar-menu-wrap">
				<nav class="top-nav" aria-label="<?php esc_attr_e('Hauptnavigation', 'schilliger'); ?>">
					<?php
					wp_nav_menu([
						'theme_location' => 'top-nav',
						'container' => false,
						'menu_class' => 'nav-main',
						'fallback_cb' => 'schilliger_primary_menu_fallback',
					]);
					?>
				</nav>
			</div>
		</div>
	</div>
</header>
