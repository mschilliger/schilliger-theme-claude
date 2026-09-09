<?php
/**
 * Now page template.
 *
 * @package schilliger
 */
get_header();
?>
<main class="page-main">
	<section class="sec sec-now">
		<div class="eyebrow">Now</div>
		<h1 class="now-h"><?php the_title(); ?></h1>
		<?php
		$now_q = new WP_Query([
			'post_type' => 'now_item',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		]);
		while ($now_q->have_posts()) :
			$now_q->the_post();
			$now_title = get_the_title();
			$now_text = (string) get_field('now_text');
			?>
			<div class="now-row">
				<div class="now-dash">—</div>
				<div class="now-txt">
					<?php if ($now_title) : ?>
						<strong><?php echo esc_html($now_title); ?>:</strong>
					<?php endif; ?>
					<?php echo wp_kses_post($now_text); ?>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</section>
</main>
<?php get_footer(); ?>
