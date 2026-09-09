<?php
/**
 * Footer template.
 *
 * @package schilliger
 */
?>
<?php if (! is_front_page()) : ?>
<footer class="site-footer">
	<div class="f-row f-row-1">
		<span class="f-name">Michael Schilliger</span>
	</div>
	<div class="f-row f-row-2">
		<a href="<?php echo esc_url(home_url('/ueber-mich')); ?>">Über mich</a>
		<span class="f-sep">-</span>
		<a href="<?php echo esc_url(get_post_type_archive_link('reportage') ?: home_url('/reporter')); ?>">Reporter</a>
		<span class="f-sep">-</span>
		<?php $posts_page_id = (int) get_option('page_for_posts'); ?>
		<a href="<?php echo esc_url($posts_page_id ? get_permalink($posts_page_id) : home_url('/blog')); ?>">Blog</a>
	</div>
	<div class="f-row f-row-3">
		<a href="mailto:mail@michaelschilliger.ch">mail@michaelschilliger.ch</a>
	</div>
	<div class="f-row f-row-4">
		<a href="<?php echo esc_url(home_url('/impressum')); ?>">Impressum</a>
		<span class="f-sep">-</span>
		<a href="<?php echo esc_url(home_url('/datenschutz')); ?>">Datenschutz</a>
		<span class="f-sep">-</span>
		<span class="f-copy"><?php echo esc_html('© ' . gmdate('Y')); ?></span>
	</div>
</footer>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
