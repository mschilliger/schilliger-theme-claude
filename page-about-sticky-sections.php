<?php
/**
 * Template Name: About Sticky Sections
 * Description: Schmaler 2-Spalten-Layout mit sticky Bereichs-Eyebrow.
 *
 * @package schilliger
 */

get_header();

$eyebrow_override = '';
if (function_exists('get_field')) {
	$eyebrow_override = (string) get_field('about_eyebrow_override');
}
if (! $eyebrow_override) {
	$eyebrow_override = (string) get_post_meta(get_queried_object_id(), 'about_eyebrow_override', true);
}
$eyebrow_override = trim(wp_strip_all_tags($eyebrow_override));
?>
<main class="about-future-main">
	<section class="about-future-shell">
		<div class="about-future-eyebrow" data-about-tracker data-about-override="<?php echo esc_attr($eyebrow_override); ?>">
			<div class="about-future-eyebrow-current-wrap">
				<a class="about-future-eyebrow-current" data-about-current href="#"><?php echo esc_html($eyebrow_override); ?></a>
			</div>
			<div class="about-future-eyebrow-list" data-about-upcoming></div>
		</div>

		<div class="about-future-grid">
			<div class="about-future-content-col">
				<div class="about-future-content">
				<?php while (have_posts()) : the_post(); ?>
					<h1><?php the_title(); ?></h1>
					<div class="post-body"><?php the_content(); ?></div>
				<?php endwhile; ?>
				</div>
			</div>

			<aside class="about-future-side">
				<nav class="about-future-scrollnav" data-about-scrollnav></nav>
			</aside>
		</div>
	</section>
</main>
<?php get_footer(); ?>
