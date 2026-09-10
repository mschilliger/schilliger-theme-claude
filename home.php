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
			<?php
			global $wp_query;
			$blog_index_position = 0;
			$blog_index_total = $wp_query->post_count;
			?>
			<div class="blog-index-list">
				<?php while (have_posts()) : the_post(); ?>
					<?php $blog_index_position++; ?>
					<article class="blog-index-item" id="blog-post-<?php echo esc_attr((string) $blog_index_position); ?>">
						<div class="blog-index-item-meta">
							<h2 class="blog-index-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<time class="blog-index-date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date('j. M Y')); ?></time>
							<?php if ($blog_index_position < $blog_index_total) : ?>
								<a class="blog-index-next-link" href="#blog-post-<?php echo esc_attr((string) ($blog_index_position + 1)); ?>" aria-label="Zum naechsten Artikel springen">
									<svg viewBox="0 0 16 16" width="14" height="14" fill="none" aria-hidden="true">
										<path d="M8 2v10M8 12l-4-4M8 12l4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
								</a>
							<?php endif; ?>
						</div>
						<div class="blog-index-content">
							<?php the_content(); ?>
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
