<?php
/**
 * Archive template for reportage.
 *
 * @package schilliger
 */

get_header();
?>
<main class="reporter-page">
	<section class="reporter-shell">
		<div class="eyebrow reporter-eyebrow">Reporter</div>
		<h1 class="reporter-title">Reportagen</h1>
		<?php
		$reporter_intro = '';
		if (function_exists('get_field')) {
			$reporter_intro = (string) get_field('reporter_archive_intro', 'option');
		}
		if (! $reporter_intro) {
			$reporter_intro = (string) get_option('schilliger_reporter_archive_intro', '');
		}
		?>
		<?php if ($reporter_intro) : ?>
			<div class="reporter-intro"><?php echo wp_kses_post($reporter_intro); ?></div>
		<?php endif; ?>
		<?php if (have_posts()) : ?>
			<div class="reporter-list">
				<?php while (have_posts()) : the_post(); ?>
					<article <?php post_class('reporter-item'); ?>>
						<a class="reporter-thumb" href="<?php the_permalink(); ?>">
							<?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif(get_the_ID(), 'large')); ?>
						</a>
						<div class="reporter-content">
							<div class="reporter-pub"><?php echo esc_html((string) get_field('publication')); ?></div>
							<h2 class="reporter-item-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p class="reporter-excerpt"><?php echo esc_html(schilliger_excerpt_from_post(get_the_ID(), 220)); ?></p>
							<?php
							$terms = get_the_terms(get_the_ID(), 'reportage_tag');
							$tags = '—';
							if ($terms && ! is_wp_error($terms)) {
								$tags = implode(', ', wp_list_pluck($terms, 'name'));
							}
							?>
							<div class="reporter-meta">
								<span>Autor: <?php the_author(); ?></span>
								<span>Datum: <?php echo esc_html(get_field('publication_date') ? schilliger_format_acf_date((string) get_field('publication_date')) : get_the_date('j. F Y')); ?></span>
								<span>Publikation: <?php echo esc_html((string) get_field('publication')); ?></span>
								<span>Tags: <?php echo esc_html($tags); ?></span>
							</div>
							<div class="reporter-links">
								<a href="<?php the_permalink(); ?>">Text lesen →</a>
								<?php $related_blog = (int) get_field('related_blog_post'); ?>
								<?php if ($related_blog) : ?>
									<a href="<?php echo esc_url(get_permalink($related_blog)); ?>">Making-of →</a>
								<?php endif; ?>
							</div>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
			<div class="reporter-pagination pagination-wrap">
				<?php echo wp_kses_post(paginate_links(['type' => 'list'])); ?>
			</div>
		<?php else : ?>
			<p>Keine Reportagen gefunden.</p>
		<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
