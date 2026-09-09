<?php
/**
 * 404 template.
 *
 * @package schilliger
 */
get_header();
?>
<main class="page-main">
	<section class="archive-wrap">
		<div class="eyebrow">404</div>
		<h1 class="sec-h">Seite nicht gefunden</h1>
		<p>Die angefragte Seite existiert nicht oder wurde verschoben.</p>
		<p><a class="more-link" href="<?php echo esc_url(home_url('/')); ?>">Zur Startseite</a></p>
	</section>
</main>
<?php get_footer(); ?>
