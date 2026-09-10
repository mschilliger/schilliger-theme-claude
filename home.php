<?php
/**
 * Blog archive template.
 *
 * @package schilliger
 */

get_header();

$pinned_ids = schilliger_pinned_blog_posts();
$pinned_posts = [];
if ($pinned_ids) {
	$pinned_posts = get_posts([
		'post_type' => 'post',
		'post_status' => 'publish',
		'post__in' => $pinned_ids,
		'orderby' => 'post__in',
		'posts_per_page' => count($pinned_ids),
	]);
}
?>
<main class="blog-page">
	<section class="blog-shell">
		<div class="eyebrow blog-eyebrow">Reporterblock</div>

		<?php if ($pinned_posts) : ?>
			<section class="blog-pinned" aria-label="Pinned Beitraege">
				<div class="blog-pinned-title">PINNED</div>
				<div class="blog-pinned-list">
					<?php foreach ($pinned_posts as $pinned_post) : ?>
						<?php
						$pinned_id = (int) $pinned_post->ID;
						$pinned_tags = get_the_terms($pinned_id, 'post_tag');
						$pinned_tag = ($pinned_tags && ! is_wp_error($pinned_tags)) ? $pinned_tags[0]->name : '';
						?>
						<div class="blog-pinned-row">
							<a href="<?php echo esc_url(get_permalink($pinned_id)); ?>" class="blog-pinned-item-title"><?php echo esc_html(get_the_title($pinned_id)); ?></a>
							<time class="blog-pinned-item-date" datetime="<?php echo esc_attr(get_the_date('c', $pinned_id)); ?>"><?php echo esc_html(get_the_date('j. M Y', $pinned_id)); ?></time>
							<span class="blog-pinned-item-tag"><?php echo esc_html($pinned_tag); ?></span>
						</div>
					<?php endforeach; ?>
				</div>
			</section>
		<?php endif; ?>

		<h1 class="blog-title">Blog</h1>
		<?php if (have_posts()) : ?>
			<div class="blog-index-list">
				<?php while (have_posts()) : the_post(); ?>
					<article class="blog-index-item">
						<div class="blog-index-item-meta">
							<h2 class="blog-index-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<time class="blog-index-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j. M Y')); ?></time>
						</div>
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
