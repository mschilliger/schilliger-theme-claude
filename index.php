<?php
/**
 * Fallback index template.
 *
 * @package schilliger
 */
get_header();
?>
<main class="page-main">
	<section class="archive-wrap">
		<?php if (have_posts()) : ?>
			<?php while (have_posts()) : the_post(); ?>
				<article class="blog-item">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="post-body"><?php the_excerpt(); ?></div>
				</article>
			<?php endwhile; ?>
		<?php endif; ?>
	</section>
</main>
<?php get_footer(); ?>
