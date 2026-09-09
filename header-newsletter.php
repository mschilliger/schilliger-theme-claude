<?php
/**
 * Minimaler Header für die Newsletter-Landingpage (ohne Site-Topbar).
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
<body <?php body_class('newsletter-landing-page no-sidebar'); ?>>
<?php wp_body_open(); ?>
