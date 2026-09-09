<?php
/**
 * Blog archive template.
 *
 * @package schilliger
 */

get_header();
?>
<main class="blog-page">
	<section class="blog-shell">
		<div class="eyebrow blog-eyebrow">Reporterblock</div>
		<h1 class="blog-title">Blog</h1>
		<?php if (have_posts()) : ?>
			<div class="blog-index-list">
				<?php while (have_posts()) : the_post(); ?>
					<article class="blog-index-item">
						<time class="blog-index-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j. M Y')); ?></time>
						<h2 class="blog-index-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<div class="blog-index-excerpt">
							<?php
							$formatted_excerpt = schilliger_formatted_archive_excerpt(get_the_ID(), 3);
							if ($formatted_excerpt) {
								echo wp_kses_post($formatted_excerpt);
							} else {
								echo wp_kses_post(wpautop(get_the_excerpt()));
							}
							?>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="blog-pagination pagination-wrap">
				<?php echo wp_kses_post(paginate_links(['type' => 'list'])); ?>
			</div>
		<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
