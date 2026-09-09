<?php
/**
 * Bibliothek page template.
 *
 * @package schilliger
 */
get_header('bibliothek');
?>
<main class="lib-page" data-lib-page>
	<section class="lib-shell" aria-label="Bibliothek">
		<header class="lib-head" aria-label="Titel">
			<h1 class="lib-title">Bibliothek</h1>
			<?php
			$lib_intro = (string) get_theme_mod(
				'schilliger_library_intro',
				'<p><strong><em>Ich habe in von und mit Büchern gelebt - und nirgendwo mehr gelernt als in den Werken, die sich hier aufgelistet finden.</em></strong></p>'
			);
			?>
			<?php if ($lib_intro) : ?>
				<div class="lib-intro"><?php echo wp_kses_post($lib_intro); ?></div>
			<?php endif; ?>
		</header>

		<?php
		$book_tag_terms = get_terms([
			'taxonomy' => 'book_tag',
			'hide_empty' => false,
		]);
		if (is_wp_error($book_tag_terms)) {
			$book_tag_terms = [];
		}

		// Bestimmte Buch-Tags für die Collections-Auswahl ausblenden.
		// Trage hier die Slugs der Tags ein, die NICHT angezeigt werden sollen.
		$excluded_book_tag_slugs = [
			// 'favorite',
		];
		if (! empty($excluded_book_tag_slugs) && ! empty($book_tag_terms)) {
			$book_tag_terms = array_values(array_filter($book_tag_terms, function ($t) use ($excluded_book_tag_slugs) {
				$slug = isset($t->slug) ? (string) $t->slug : '';
				return ! in_array($slug, $excluded_book_tag_slugs, true);
			}));
		}
		?>
		<div class="lib-filter-wrap" data-lib-filter-wrap>
			<nav class="lib-filter" aria-label="Filter" data-lib-filter>
				<button class="lib-filter-btn is-active" type="button" aria-pressed="true" data-lib-type="book">Bücher</button>
				<button class="lib-filter-btn is-active" type="button" aria-pressed="true" data-lib-type="podcast">Podcasts</button>
				<button class="lib-filter-btn is-active" type="button" aria-pressed="true" data-lib-type="film">Filme</button>
				<?php if (! empty($book_tag_terms)) : ?>
					<button class="lib-filter-btn" type="button" aria-pressed="false" data-lib-collections aria-expanded="false">Collections</button>
				<?php endif; ?>
			</nav>
			<?php if (! empty($book_tag_terms)) : ?>
				<div class="lib-subfilter-tags" data-lib-subfilter-tags>
					<?php foreach ($book_tag_terms as $bt_term) : ?>
						<?php
						$tag_desc_text = wp_strip_all_tags((string) ($bt_term->description ?? ''));
						?>
						<button class="lib-tag-btn" type="button" data-lib-tag="<?php echo esc_attr($bt_term->slug); ?>" data-lib-tag-name="<?php echo esc_attr($bt_term->name); ?>" data-lib-tag-description="<?php echo esc_attr($tag_desc_text); ?>" aria-pressed="false">
							<?php echo esc_html($bt_term->name); ?><sup class="lib-tag-count"><?php echo (int) $bt_term->count; ?></sup>
						</button>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
		</div>

		<h2 class="lib-tag-page-heading" data-lib-tag-heading hidden aria-hidden="true"></h2>
		<p class="lib-tag-description" data-lib-tag-description hidden aria-hidden="true"></p>

		<div class="lib-tag-flat-wrap" data-lib-tag-flat-wrap hidden aria-hidden="true">
			<div class="lib-grid lib-grid--tag-flat" data-lib-grid-flat></div>
		</div>

		<?php
		$media_q = new WP_Query([
			'post_type' => 'book',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_key' => 'read_date',
			'orderby' => 'meta_value_num',
			'order' => 'DESC',
		]);

		$by_year = [];
		foreach ($media_q->posts as $p) {
			$pid = (int) $p->ID;
			$ymd = (string) get_post_meta($pid, 'read_date', true);
			$year = '';
			if ($ymd && preg_match('/^\d{8}$/', $ymd)) {
				$year = substr($ymd, 0, 4);
			}
			if (! $year) {
				$year = 'Ohne Jahr';
			}
			if (! isset($by_year[$year])) {
				$by_year[$year] = [];
			}
			$by_year[$year][] = $p;
		}

		$years = array_keys($by_year);
		usort($years, function ($a, $b) {
			if ('Ohne Jahr' === $a) {
				return 1;
			}
			if ('Ohne Jahr' === $b) {
				return -1;
			}
			return (int) $b <=> (int) $a;
		});
		?>

		<?php if (! empty($years)) : ?>
			<div class="lib-years-wrap" data-lib-years-wrap>
			<?php foreach ($years as $year) : ?>
				<section class="lib-year" data-lib-year data-year="<?php echo esc_attr($year); ?>">
					<h2 class="lib-year-title"><?php echo esc_html($year); ?></h2>
					<div class="lib-grid" data-lib-grid>
						<?php foreach ($by_year[$year] as $p) : ?>
							<?php
							setup_postdata($p);
							$book_id = (int) $p->ID;
							$media_type = (string) get_post_meta($book_id, 'media_type', true);
							if (! $media_type) {
								$media_type = 'book';
							}
							$author = trim((string) get_post_meta($book_id, 'author', true));
							$rating = get_field('rating', $book_id);
							$related_blog_post_id = (int) get_field('related_blog_post', $book_id);
							$blog_url = $related_blog_post_id ? get_permalink($related_blog_post_id) : '';
							$buy_links = [];

							if (function_exists('acf_is_pro') && acf_is_pro()) {
								$rows = get_field('buy_links', $book_id);
								if (is_array($rows)) {
									foreach ($rows as $row) {
										$label = isset($row['label']) ? trim((string) $row['label']) : '';
										$url = isset($row['url']) ? trim((string) $row['url']) : '';
										if ($url) {
											$buy_links[] = ['label' => ($label ?: 'Link'), 'url' => $url];
										}
									}
								}
							} else {
								$text = (string) get_field('buy_links_text', $book_id);
								$lines = preg_split('/\R/', $text) ?: [];
								foreach ($lines as $line) {
									$line = trim((string) $line);
									if (! $line) {
										continue;
									}
									$parts = array_map('trim', explode('|', $line, 2));
									$label = $parts[0] ?? '';
									$url = $parts[1] ?? '';
									if ($url) {
										$buy_links[] = ['label' => ($label ?: 'Link'), 'url' => $url];
									}
								}
							}
							?>
							<?php
							$order_url = ! empty($buy_links[0]['url']) ? (string) $buy_links[0]['url'] : '';
							$order_label = ('podcast' === $media_type) ? 'Hören' : 'Bestellen';
							?>

							<?php
							$tag_slugs = wp_get_post_terms($book_id, 'book_tag', ['fields' => 'slugs']);
							if (is_wp_error($tag_slugs)) {
								$tag_slugs = [];
							}
							$data_tags = implode(',', $tag_slugs);
							?>
							<article class="lib-item<?php echo ('podcast' === $media_type) ? ' is-podcast' : ''; ?>" data-lib-item data-lib-media="<?php echo esc_attr($media_type); ?>" data-lib-tags="<?php echo esc_attr($data_tags); ?>" data-lib-year-key="<?php echo esc_attr($year); ?>">
								<div class="lib-card" data-lib-card>
									<div class="lib-face lib-face-front" role="button" tabindex="0" aria-expanded="false" data-lib-toggle>
										<?php if (has_post_thumbnail($book_id)) : ?>
											<?php echo wp_kses_post(get_the_post_thumbnail($book_id, 'medium_large')); ?>
										<?php else : ?>
											<span class="lib-cover-missing" aria-hidden="true"></span>
										<?php endif; ?>
										<?php if ('podcast' === $media_type) : ?>
											<span class="lib-play" aria-hidden="true"></span>
										<?php endif; ?>
										<div class="lib-actions" aria-hidden="true">
											<button class="lib-action-btn" type="button" data-lib-details>Details</button>
											<?php if ($order_url) : ?>
												<a class="lib-action-btn" href="<?php echo esc_url($order_url); ?>" <?php echo (0 === strpos($order_url, 'mailto:')) ? '' : 'target="_blank" rel="noopener noreferrer"'; ?> data-lib-order><?php echo esc_html($order_label); ?></a>
											<?php endif; ?>
											<?php if ($blog_url) : ?>
												<a class="lib-action-btn" href="<?php echo esc_url($blog_url); ?>" data-lib-blog>Blogpost</a>
											<?php endif; ?>
										</div>
									</div>

									<div class="lib-face lib-face-back" data-lib-panel aria-hidden="true">
										<div class="lib-panel-head">
											<div class="lib-panel-title"><?php echo esc_html(get_the_title($book_id)); ?></div>
											<button class="lib-close" type="button" aria-label="Schliessen" data-lib-close>×</button>
										</div>
										<?php if ($author) : ?>
											<div class="lib-panel-author"><em><?php echo esc_html($author); ?></em></div>
										<?php endif; ?>
									</div>
								</div>

								<div class="lib-mobile-row">
									<div class="lib-mobile-title"><?php echo esc_html(get_the_title($book_id)); ?></div>
									<?php if ($author) : ?>
										<div class="lib-mobile-author"><em><?php echo esc_html($author); ?></em></div>
									<?php endif; ?>
									<?php if ($blog_url) : ?>
										<a class="lib-mobile-link" href="<?php echo esc_url($blog_url); ?>">Blogpost</a>
									<?php endif; ?>
									<?php if ($order_url) : ?>
										<a class="lib-mobile-link" href="<?php echo esc_url($order_url); ?>"><?php echo esc_html($order_label); ?></a>
									<?php endif; ?>
								</div>
							</article>
						<?php endforeach; ?>
					</div>
				</section>
			<?php endforeach; wp_reset_postdata(); ?>
			</div>
		<?php else : ?>
			<p class="lib-empty">Noch keine Medien.</p>
		<?php endif; ?>
	</section>
</main>
<?php get_footer('bibliothek'); ?>
