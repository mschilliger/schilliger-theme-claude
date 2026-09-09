<?php
/**
 * Template Name: Newsletter Landing
 *
 * @package schilliger
 */

get_header('newsletter');
?>
<?php
$nl_variant = 'a';
if (isset($_GET['nl_variant'])) {
	$nl_variant = sanitize_key((string) wp_unslash($_GET['nl_variant']));
}
if ('b' !== $nl_variant) {
	$nl_variant = 'a';
}

$preview_url = '';
$preview_post = get_posts([
	'post_type' => 'post',
	'posts_per_page' => 1,
	'post_status' => 'publish',
]);
if (! empty($preview_post)) {
	$preview_url = (string) get_permalink((int) $preview_post[0]->ID);
}
?>
<main class="nl-landing nl-landing--<?php echo esc_attr($nl_variant); ?>">
	<section class="sec sec-newsletter nl-landing-hero">
		<nav class="nl-landing-nav" aria-label="Seitennavigation">
			<a class="nl-landing-nav-link" href="<?php echo esc_url(home_url('/')); ?>">Zur Startseite</a>
		</nav>
		<div class="nl-landing-inner">
			<div class="nl-brand nl-brand--landing">
				<h1 class="nl-brand-title">short story long ...</h1>
				<p class="nl-brand-tagline">and some thoughts about the world</p>
			</div>

			<div class="nl-landing-panel">
				<div class="nl-landing-copy">
					<p class="nl-landing-lead">
						Gute Geschichten brauchen Platz. Und wer die Welt verstehen will, hat nie genug Kontext. Deshalb nicht „long story short“, sondern „short story long“. Einmal im Monat, aus meinem Notizblock in deine Mailbox.
					</p>
					<ul class="nl-landing-list">
						<li>The Reporter’s Cut: Szenen und Recherchen, die es nicht in meine Reportagen geschafft haben.</li>
						<li>Werkstatt: Gedanken zum Handwerk des Erzählens und Weltbeobachtens.</li>
						<li>Hast du schon gelesen, dass…? Was ich las und hörte, um mir die Welt zu erklären. Kind of.</li>
					</ul>
				</div>

				<?php if ($preview_url) : ?>
					<a class="nl-preview-link" href="<?php echo esc_url($preview_url); ?>">Preview lesen</a>
				<?php endif; ?>

				<div class="nl-widget nl-landing-form-wrap">
					<div class="row-form">
						<form class="nl-form is-ajax" method="post" novalidate>
							<input class="nl-input" type="email" name="email" placeholder="deine@email.ch" autocomplete="email" required>
							<button class="nl-btn primary" type="submit">Abonnieren</button>
							<input type="hidden" name="ts" value="<?php echo esc_attr((string) time()); ?>">
							<div style="display:none !important;" aria-hidden="true">
								<input type="text" name="hp" tabindex="-1" autocomplete="off">
							</div>
						</form>
					</div>
					<div class="nl-success row-success" style="display:none;">Danke fuer deine Anmeldung!</div>
					<p class="nl-feedback" aria-live="polite"></p>
				</div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
