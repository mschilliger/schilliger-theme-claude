<?php
/**
 * Front page template.
 *
 * @package schilliger
 */

get_header();
?>
<main class="front-main">
	<section class="sec sec-intro" id="intro">
		<div>
			<div class="intro-body">
				<?php
				$custom_intro = (string) get_theme_mod('schilliger_intro_text', '');
				if ($custom_intro) {
					echo wp_kses_post(wpautop($custom_intro));
				} elseif (has_excerpt(get_queried_object_id())) {
					echo wp_kses_post(wpautop(get_the_excerpt(get_queried_object_id())));
				} elseif (get_post_field('post_content', get_queried_object_id())) {
					echo wp_kses_post(wpautop(wp_trim_words((string) get_post_field('post_content', get_queried_object_id()), 95, ' ...')));
				} else {
					echo wp_kses_post('<p>Guten Tag, ich bin Michael Schilliger, Reporter aus der Schweiz.</p>');
				}
				?>
			</div>
			<?php
			$cta_text = (string) get_theme_mod('schilliger_intro_cta_text', 'Mehr ueber mich');
			$cta_url = (string) get_theme_mod('schilliger_intro_cta_url', home_url('/ueber-mich'));
			?>
			<div class="intro-cta"><a href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta_text); ?></a></div>
			<div class="intro-links" aria-label="Kontakt und Links">
				<a class="intro-link" href="mailto:mail@michaelschilliger.ch">
					<span class="intro-link-label intro-link-label--desktop">mail@michaelschilliger.ch</span>
					<span class="intro-link-label intro-link-label--mobile">Mail</span>
				</a>
				<span class="intro-links-sep" aria-hidden="true"></span>
				<a class="intro-link" href="https://www.linkedin.com/in/michael-schilliger-6b326b86/" target="_blank" rel="noopener noreferrer">
					<span class="intro-link-label intro-link-label--desktop">LinkedIn</span>
					<span class="intro-link-label intro-link-label--mobile">Linkedin</span>
				</a>
				<span class="intro-links-sep" aria-hidden="true"></span>
				<a class="intro-link" href="<?php echo esc_url(home_url('/newsletter')); ?>">
					<span class="intro-link-label intro-link-label--desktop">Newsletter</span>
					<span class="intro-link-label intro-link-label--mobile">Newsletter</span>
				</a>
			</div>
		</div>
		<?php if (has_post_thumbnail()) : ?>
			<?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif(get_queried_object_id(), 'medium_large', ['class' => 'intro-portrait'])); ?>
		<?php endif; ?>
	</section>

	<section class="sec sec-portfolio" id="reporter">
		<div class="eyebrow">Meine Arbeit</div>
		<h2 class="sec-h">Reportagen</h2>
		<?php
		$featured_ids = array_values(array_unique(array_filter([
			(int) get_option('schilliger_featured_reportage_main', 0),
			(int) get_option('schilliger_featured_reportage_second', 0),
			(int) get_option('schilliger_featured_reportage_third', 0),
			(int) get_option('schilliger_featured_reportage_fourth', 0),
		])));
		$featured_posts = [];
		if (! empty($featured_ids)) {
			$featured_posts = get_posts([
				'post_type' => 'reportage',
				'post_status' => 'publish',
				'posts_per_page' => 4,
				'post__in' => $featured_ids,
				'orderby' => 'post__in',
			]);
		}

		$reportage_q = new WP_Query([
			'post_type' => 'reportage',
			'posts_per_page' => 4,
			'post__not_in' => $featured_ids,
			'meta_key' => 'publication_date',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
		]);
		$latest_reportage_posts = $reportage_q->posts;

		$mobile_q = new WP_Query([
			'post_type' => 'reportage',
			'posts_per_page' => 8,
			'post__not_in' => $featured_ids,
			'meta_key' => 'publication_date',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
		]);
		$reportage_posts = $mobile_q->posts;
		?>
		<?php if (! empty($featured_posts)) : ?>
			<div class="reporter-favorites-label">Meine Favoriten</div>
			<div class="reporter-curated">
				<?php
				$main_feature = $featured_posts[0];
				$side_features = array_slice($featured_posts, 1, 3);
				$main_id = (int) $main_feature->ID;
				$main_lead = (string) get_the_excerpt($main_id);
				?>
				<article class="reporter-curated-main">
					<a href="<?php echo esc_url(get_permalink($main_id)); ?>" class="reporter-curated-main-image"><?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif($main_id, 'large')); ?></a>
					<div class="reporter-curated-main-content">
						<a href="<?php echo esc_url(get_permalink($main_id)); ?>" class="reporter-curated-main-title"><?php echo esc_html(get_the_title($main_id)); ?></a>
						<p class="reporter-curated-main-lead"><?php echo esc_html($main_lead); ?></p>
						<a href="<?php echo esc_url(get_permalink($main_id)); ?>" class="reporter-curated-read">Lesen</a>
					</div>
				</article>
				<div class="reporter-curated-side">
					<?php foreach ($side_features as $reportage_post) : ?>
						<?php
						$post_id = (int) $reportage_post->ID;
						$side_lead = (string) get_the_excerpt($post_id);
						?>
						<article class="reporter-curated-side-item">
							<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="reporter-curated-side-image"><?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif($post_id, 'medium')); ?></a>
							<div class="reporter-curated-side-content">
								<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="reporter-curated-side-title"><?php echo esc_html(get_the_title($post_id)); ?></a>
								<p class="reporter-curated-side-lead"><?php echo esc_html($side_lead); ?></p>
								<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="reporter-curated-read">Lesen</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
			<details class="reporter-favorites-mobile">
				<summary>Favoriten anzeigen</summary>
				<div class="reporter-favorites-mobile-list">
					<?php foreach ($featured_posts as $reportage_post) : ?>
						<?php $post_id = (int) $reportage_post->ID; ?>
						<div class="reporter-favorites-mobile-item">
							<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="reporter-favorites-mobile-title"><?php echo esc_html(get_the_title($post_id)); ?></a>
						</div>
					<?php endforeach; ?>
				</div>
			</details>
		<?php endif; ?>
		<div class="reporter-latest-label">Neueste Reportagen</div>
		<div class="grid-4 reporter-latest-grid">
			<?php foreach ($latest_reportage_posts as $reportage_post) : ?>
				<?php
				setup_postdata($reportage_post);
				$post_id = $reportage_post->ID;
				?>
				<article <?php post_class('card', $post_id); ?>>
					<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="card-img">
						<?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif($post_id, 'large')); ?>
						<div class="card-face">
							<div class="c-pub"><?php echo esc_html((string) get_field('publication', $post_id)); ?></div>
						</div>
					</a>
					<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="c-ttl"><?php echo esc_html(get_the_title($post_id)); ?></a>
					<p class="c-excerpt"><?php echo esc_html(schilliger_excerpt_from_post($post_id, 130)); ?></p>
					<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="c-read">Lesen</a>
				</article>
			<?php endforeach; wp_reset_postdata(); ?>
		</div>
		<div class="reporter-mobile-a" aria-label="Reportagen kompakt">
			<?php foreach (array_slice($reportage_posts, 0, 4) as $reportage_post) : ?>
				<?php $post_id = $reportage_post->ID; ?>
				<article class="reporter-mobile-a-item">
					<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="reporter-mobile-a-thumb"><?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif($post_id, 'medium')); ?></a>
					<div class="reporter-mobile-a-content">
						<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="reporter-mobile-a-title"><?php echo esc_html(get_the_title($post_id)); ?></a>
						<div class="reporter-mobile-a-pub"><?php echo esc_html((string) get_field('publication', $post_id)); ?></div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<div class="reporter-mobile-b" aria-label="Reportagen Feature">
			<?php if (! empty($reportage_posts)) : ?>
				<?php $feature_id = (int) $reportage_posts[0]->ID; ?>
				<article class="reporter-mobile-b-feature">
					<a href="<?php echo esc_url(get_permalink($feature_id)); ?>" class="reporter-mobile-b-image"><?php echo wp_kses_post(schilliger_post_thumbnail_preserve_gif($feature_id, 'large')); ?></a>
					<a href="<?php echo esc_url(get_permalink($feature_id)); ?>" class="reporter-mobile-b-title"><?php echo esc_html(get_the_title($feature_id)); ?></a>
					<div class="reporter-mobile-b-pub"><?php echo esc_html((string) get_field('publication', $feature_id)); ?></div>
				</article>
			<?php endif; ?>
			<div class="reporter-mobile-b-list">
				<?php foreach (array_slice($reportage_posts, 1, 4) as $reportage_post) : ?>
					<?php $post_id = $reportage_post->ID; ?>
					<div class="reporter-mobile-b-row">
						<a href="<?php echo esc_url(get_permalink($post_id)); ?>" class="reporter-mobile-b-row-title"><?php echo esc_html(get_the_title($post_id)); ?></a>
						<span class="reporter-mobile-b-row-pub"><?php echo esc_html((string) get_field('publication', $post_id)); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<a href="<?php echo esc_url(get_post_type_archive_link('reportage')); ?>" class="more-link">Alle Reportagen</a>
	</section>

	<section class="sec sec-blog" id="blog">
		<div class="eyebrow">Aus dem Reporterblock</div>
		<?php $posts_page_id = (int) get_option('page_for_posts'); ?>
		<h2 class="sec-h"><a href="<?php echo esc_url($posts_page_id ? get_permalink($posts_page_id) : home_url('/blog')); ?>">Blog</a></h2>
		<?php
		$blog_q = new WP_Query(['post_type' => 'post', 'posts_per_page' => 5]);
		if ($blog_q->have_posts()) :
			$blog_q->the_post();
			?>
			<article class="b-feat">
				<span class="b-kicker">
					<span class="b-kicker-date"><?php echo esc_html(get_the_date('j. F Y')); ?></span>
				</span>
				<a href="<?php the_permalink(); ?>" class="b-title"><?php the_title(); ?></a>
				<div class="b-excerpt">
					<?php
					$formatted_front_excerpt = schilliger_formatted_archive_excerpt(get_the_ID(), 2);
					if ($formatted_front_excerpt) {
						echo wp_kses_post($formatted_front_excerpt);
					} else {
						echo wp_kses_post(wpautop(get_the_excerpt()));
					}
					?>
				</div>
				<a href="<?php the_permalink(); ?>" class="b-cta">Weiterlesen</a>
			</article>
			<div class="b-list">
				<?php while ($blog_q->have_posts()) : $blog_q->the_post(); ?>
					<div class="b-row">
						<a href="<?php the_permalink(); ?>" class="b-ttl"><?php the_title(); ?></a>
						<span class="b-dt"><?php echo esc_html(get_the_date('j. M Y')); ?></span>
					</div>
				<?php endwhile; ?>
			</div>
		<?php endif; wp_reset_postdata(); ?>
		<a href="<?php echo esc_url($posts_page_id ? get_permalink($posts_page_id) : home_url('/blog')); ?>" class="more-link">Alle Beitraege</a>
	</section>

	<section class="sec sec-newsletter">
		<div class="eyebrow">Newsletter</div>
		<div class="nl-brand">
			<h2 class="nl-brand-title">short story long ...</h2>
			<p class="nl-brand-tagline">and some thoughts about the world</p>
		</div>
		<p class="nl-sub"><?php echo esc_html((string) get_theme_mod('schilliger_newsletter_text', 'Neue Texte, Lektuereempfehlungen und gelegentliche Gedanken direkt ins Postfach.')); ?></p>
		<div class="row-form">
			<form class="nl-form is-ajax" method="post" novalidate>
				<input class="nl-input" type="email" name="email" placeholder="deine@email.ch" autocomplete="email" required>
				<button class="nl-btn primary" type="submit">Abonnieren</button>
				<input type="hidden" name="ts" value="<?php echo esc_attr((string) time()); ?>">
				<div class="cf-turnstile" data-sitekey="<?php echo esc_attr(SCHILLIGER_TURNSTILE_SITE_KEY); ?>" data-appearance="interaction-only"></div>
				<div style="display:none !important;" aria-hidden="true">
					<input type="text" name="hp" tabindex="-1" autocomplete="off">
				</div>
			</form>
		</div>
		<div class="nl-success row-success" style="display:none;">Danke fuer deine Anmeldung!</div>
		<p class="nl-feedback" aria-live="polite"></p>
		<span class="nl-note"><?php echo esc_html((string) get_theme_mod('schilliger_newsletter_note', 'Jederzeit abmeldbar. Keine Weitergabe an Dritte.')); ?></span>
	</section>

	<section class="sec sec-now" id="now">
		<div class="eyebrow">Now</div>
		<h2 class="now-h">Was mich momentan beschäftigt</h2>
		<?php
		$now_q = new WP_Query([
			'post_type' => 'now_item',
			'posts_per_page' => -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
		]);
		while ($now_q->have_posts()) :
			$now_q->the_post();
			$now_title = get_the_title();
			$now_text = (string) get_field('now_text');
			?>
			<div class="now-row">
				<div class="now-dash">—</div>
				<div class="now-txt">
					<?php if ($now_title) : ?>
						<strong><?php echo esc_html($now_title); ?></strong>
					<?php endif; ?>
					<?php echo wp_kses_post($now_text); ?>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</section>

	<section class="sec sec-awards" id="auszeichnungen">
		<div class="eyebrow">Auszeichnungen</div>
		<h2 class="awards-h">Ausgezeichnet &amp; <span class="awards-mobile-break">nominiert</span></h2>
		<?php
		$award_q = new WP_Query([
			'post_type' => 'auszeichnung',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => 'meta_value_num',
			'meta_key' => 'award_year',
			'order' => 'DESC',
		]);
		while ($award_q->have_posts()) :
			$award_q->the_post();
			$status = (string) get_field('award_status');
			$is_winner = false !== stripos($status, 'gewinner');
			$is_nominated = false !== stripos($status, 'nominiert');
			$row_classes = ['a-row'];
			if ($is_winner) {
				$row_classes[] = 'is-winner';
			}
			if ($is_nominated) {
				$row_classes[] = 'is-nominated';
			}
			if ($is_winner && ! $is_nominated) {
				$row_classes[] = 'is-winner-only';
			}
			?>
			<div class="<?php echo esc_attr(implode(' ', $row_classes)); ?>">
				<div class="a-yr"><?php echo esc_html((string) get_field('award_year')); ?></div>
				<div class="a-st"><?php echo esc_html($status); ?></div>
				<div class="a-ttl">
					<strong><?php echo esc_html((string) get_field('award_title')); ?></strong>
					<?php $award_text = (string) get_field('award_text'); ?>
					<?php if ($award_text) : ?>
						<span class="award-for">f&uuml;r</span>
						<em>&bdquo;<?php echo esc_html($award_text); ?>&ldquo;</em>
					<?php endif; ?>
					<?php $award_category = (string) get_field('award_category'); ?>
					<?php if ($award_category) : ?>
						<span>in der Kategorie <?php echo esc_html($award_category); ?></span>
					<?php endif; ?>
				</div>
			</div>
		<?php endwhile; wp_reset_postdata(); ?>
	</section>

	<section class="sec sec-photo-break" aria-label="Foto">
		<div class="photo-break-grid">
			<div class="photo-break-left" aria-hidden="true"></div>
			<img
				class="photo-break-image"
				src="<?php echo esc_url(get_theme_file_uri('/ba23314e-9656-4c31-b644-5ed35a5ffe80.jpg')); ?>"
				alt="Michael Schilliger unterwegs"
				loading="lazy"
			>
			<div class="photo-break-right">
				<div class="photo-break-meta">
					<p class="photo-break-line">Michael Schilliger</p>
					<p class="photo-break-line">Z&uuml;rich</p>
					<p class="photo-break-line"><a href="mailto:mail@michaelschilliger.ch">Email</a></p>
					<p class="photo-break-line"><a href="<?php echo esc_url(home_url('/impressum')); ?>">Impressum</a></p>
					<p class="photo-break-line">(c) 2026</p>
				</div>
			</div>
		</div>
	</section>
</main>
<?php get_footer(); ?>
