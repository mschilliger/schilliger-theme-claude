<?php
/**
 * Single template for reportage.
 *
 * @package schilliger
 */

get_header();
the_post();
?>
<main class="single-reportage-main">
	<div class="post-outer">
		<div class="post-wrap">
			<a class="back-link" href="<?php echo esc_url(get_post_type_archive_link('reportage')); ?>">← Reporter</a>
			<span class="post-kicker"><?php echo esc_html((string) get_field('publication')); ?> · Reportage</span>
			<h1 class="post-title"><?php the_title(); ?></h1>
			<?php if (has_excerpt()) : ?>
				<p class="post-lead"><?php echo esc_html(get_the_excerpt()); ?></p>
			<?php endif; ?>
			<div class="post-meta">
				<span class="post-meta-label">Erschienen</span>
				<span class="post-meta-value">
					<?php
					$pub_date = (string) get_field('publication_date');
					echo esc_html($pub_date ? schilliger_format_acf_date($pub_date) : get_the_date('j. F Y'));
					?>
				</span>
				<span class="post-meta-label">Publikation</span>
				<span class="post-meta-value"><?php echo esc_html((string) get_field('publication')); ?></span>
				<span class="post-meta-label">Autor</span>
				<span class="post-meta-value">
					<?php the_author(); ?>
					<?php $coauthor = (string) get_field('coauthor'); ?>
					<?php if ($coauthor) : ?>
						<?php echo esc_html(' + ' . $coauthor); ?>
					<?php endif; ?>
				</span>
			</div>
			<?php if (has_post_thumbnail()) : ?>
				<figure class="post-cover">
					<?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif(get_the_ID(), 'large')); ?>
					<?php if (get_the_post_thumbnail_caption()) : ?>
						<figcaption><?php echo esc_html(get_the_post_thumbnail_caption()); ?></figcaption>
					<?php endif; ?>
				</figure>
			<?php endif; ?>
			<div class="post-body">
				<?php the_content(); ?>
			</div>
			<nav class="post-nav">
				<div><?php previous_post_link('%link', '← Vorheriger Beitrag'); ?></div>
				<div><?php next_post_link('%link', 'Naechster Beitrag →'); ?></div>
			</nav>
		</div>
	</div>
</main>
<?php get_footer(); ?>
