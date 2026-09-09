<?php
/**
 * Ueber mich page template.
 *
 * @package schilliger
 */
get_header();
?>
<main class="page-main">
	<section class="archive-wrap">
		<?php while (have_posts()) : the_post(); ?>
			<div class="eyebrow">Über mich</div>
			<h1 class="sec-h"><?php the_title(); ?></h1>
			<div class="post-body"><?php the_content(); ?></div>
		<?php endwhile; ?>
	</section>
</main>
<?php get_footer(); ?>
