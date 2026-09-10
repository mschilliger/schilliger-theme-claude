<?php
/**
 * Theme bootstrap for schilliger.
 *
 * @package schilliger
 */

if (! defined('SCHILLIGER_VERSION')) {
	define('SCHILLIGER_VERSION', '1.0.0');
}

if (! defined('SCHILLIGER_TURNSTILE_SITE_KEY')) {
	define('SCHILLIGER_TURNSTILE_SITE_KEY', '0x4AAAAAAEuu9D1gmm1X_8pR');
}

function schilliger_asset_version(string $relative_path): string {
	$path = get_theme_file_path($relative_path);
	if ($path && file_exists($path)) {
		return (string) filemtime($path);
	}
	return SCHILLIGER_VERSION;
}

add_action('after_setup_theme', function () {
	add_theme_support('post-thumbnails');
	add_theme_support('title-tag');
	add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
	register_nav_menus([
		'top-nav' => __('Top Navigation', 'schilliger'),
	]);
});

add_action('wp_enqueue_scripts', function () {
	wp_enqueue_style(
		'schilliger-google-fonts',
		'https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,800;1,700&family=Lora:ital,wght@0,400;0,500;0,700;1,400;1,500&family=IBM+Plex+Sans:wght@300;400;500&family=Zilla+Slab:wght@400;500;700&display=swap',
		[],
		null
	);

	wp_enqueue_style('schilliger-base', get_theme_file_uri('/assets/css/base.css'), [], schilliger_asset_version('/assets/css/base.css'));
	wp_enqueue_style('schilliger-sidebar', get_theme_file_uri('/assets/css/sidebar.css'), ['schilliger-base'], schilliger_asset_version('/assets/css/sidebar.css'));
	wp_enqueue_style('schilliger-components', get_theme_file_uri('/assets/css/components.css'), ['schilliger-sidebar'], schilliger_asset_version('/assets/css/components.css'));
	wp_enqueue_style('schilliger-front', get_theme_file_uri('/assets/css/pages/front.css'), ['schilliger-components'], schilliger_asset_version('/assets/css/pages/front.css'));
	wp_enqueue_style('schilliger-archive', get_theme_file_uri('/assets/css/pages/archive.css'), ['schilliger-components'], schilliger_asset_version('/assets/css/pages/archive.css'));
	wp_enqueue_style('schilliger-archives-isolated', get_theme_file_uri('/assets/css/pages/archives-isolated.css'), ['schilliger-components'], schilliger_asset_version('/assets/css/pages/archives-isolated.css'));
	wp_enqueue_style('schilliger-single-reportage', get_theme_file_uri('/assets/css/pages/single-reportage.css'), ['schilliger-components'], schilliger_asset_version('/assets/css/pages/single-reportage.css'));
	wp_enqueue_style('schilliger-single-post', get_theme_file_uri('/assets/css/pages/single-post.css'), ['schilliger-components'], schilliger_asset_version('/assets/css/pages/single-post.css'));
	if (is_page_template('page-about-sticky-sections.php')) {
		wp_enqueue_style(
			'schilliger-about-sticky-sections',
			get_theme_file_uri('/assets/css/pages/about-sticky-sections.css'),
			['schilliger-components'],
			schilliger_asset_version('/assets/css/pages/about-sticky-sections.css')
		);
	}
	if (is_page_template('page-newsletter.php')) {
		wp_enqueue_style(
			'schilliger-newsletter-landing',
			get_theme_file_uri('/assets/css/pages/newsletter-landing.css'),
			['schilliger-components'],
			schilliger_asset_version('/assets/css/pages/newsletter-landing.css')
		);
	}

	wp_enqueue_script('schilliger-sidebar', get_theme_file_uri('/assets/js/sidebar.js'), [], schilliger_asset_version('/assets/js/sidebar.js'), true);
	wp_enqueue_script('schilliger-cursor', get_theme_file_uri('/assets/js/cursor.js'), [], schilliger_asset_version('/assets/js/cursor.js'), true);
	wp_enqueue_script('schilliger-progress', get_theme_file_uri('/assets/js/progress.js'), [], schilliger_asset_version('/assets/js/progress.js'), true);
	wp_enqueue_script('schilliger-newsletter', get_theme_file_uri('/assets/js/newsletter.js'), [], schilliger_asset_version('/assets/js/newsletter.js'), true);
	if (is_page('bibliothek') || is_page_template('page-bibliothek.php')) {
		wp_enqueue_style(
			'schilliger-bibliothek',
			get_theme_file_uri('/assets/css/pages/bibliothek.css'),
			['schilliger-components'],
			schilliger_asset_version('/assets/css/pages/bibliothek.css')
		);
		wp_enqueue_script(
			'schilliger-bibliothek',
			get_theme_file_uri('/assets/js/bibliothek.js'),
			[],
			schilliger_asset_version('/assets/js/bibliothek.js'),
			true
		);
	}
	if (is_singular('post')) {
		wp_enqueue_script(
			'schilliger-post-lightbox',
			get_theme_file_uri('/assets/js/post-lightbox.js'),
			[],
			schilliger_asset_version('/assets/js/post-lightbox.js'),
			true
		);
	}
	if (is_page_template('page-about-sticky-sections.php')) {
		wp_enqueue_script(
			'schilliger-about-sticky-sections',
			get_theme_file_uri('/assets/js/about-sticky-sections.js'),
			[],
			schilliger_asset_version('/assets/js/about-sticky-sections.js'),
			true
		);
	}
	if (is_singular() && comments_open() && get_option('thread_comments')) {
		wp_enqueue_script('comment-reply');
	}

	$has_newsletter_form = is_front_page() || is_page_template('page-newsletter.php');
	if (! $has_newsletter_form && is_singular()) {
		$current_post_id = get_queried_object_id();
		$current_content = $current_post_id ? (string) get_post_field('post_content', $current_post_id) : '';
		$has_newsletter_form = has_shortcode($current_content, 'newsletter_signup');
	}
	if ($has_newsletter_form) {
		wp_enqueue_script(
			'cloudflare-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js',
			[],
			null,
			true
		);
		wp_script_add_data('cloudflare-turnstile', 'async', true);
		wp_script_add_data('cloudflare-turnstile', 'defer', true);
	}

	wp_localize_script('schilliger-newsletter', 'schilligerNewsletter', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('schilliger_newsletter_signup'),
	]);

	$palette = (string) get_theme_mod('schilliger_blog_palette', 'medium');
	$palettes = [
		'soft' => [
			'section' => '#f7dfd4',
			'archive' => '#efd0c2',
			'single' => '#fbede6',
			'accent' => '#cf8a76',
		],
		'medium' => [
			'section' => '#f4d8cc',
			'archive' => '#edc4b3',
			'single' => '#f8e7de',
			'accent' => '#d07c66',
		],
		'strong' => [
			'section' => '#efc8b8',
			'archive' => '#e4af98',
			'single' => '#f4ddd1',
			'accent' => '#c86549',
		],
	];
	$active = $palettes[$palette] ?? $palettes['medium'];
	$accent_color = (string) get_theme_mod('schilliger_accent_color', '#ad0a09');
	if (! $accent_color || ! preg_match('/^#[0-9a-fA-F]{6}$/', $accent_color)) {
		$accent_color = '#ad0a09';
	}
	$lightbox_bg = (string) get_theme_mod('schilliger_lightbox_bg', '#000000');
	if (! $lightbox_bg || ! preg_match('/^#[0-9a-fA-F]{6}$/', $lightbox_bg)) {
		$lightbox_bg = '#000000';
	}

	$archive_width_variant = (string) get_theme_mod('schilliger_archive_width_variant', 'b');
	$archive_widths = [
		'a' => ['reporter' => '1120px', 'blog' => '920px'],
		'b' => ['reporter' => '1160px', 'blog' => '960px'],
		'c' => ['reporter' => '1200px', 'blog' => '1000px'],
	];
	$archive_active = $archive_widths[$archive_width_variant] ?? $archive_widths['b'];

	$inline_css = ':root{'
		. '--accent:' . esc_attr($accent_color) . ';'
		. '--reportage-accent:' . esc_attr($accent_color) . ';'
		. '--blog-section-bg:' . esc_attr($active['section']) . ';'
		. '--blog-archive-bg:' . esc_attr($active['archive']) . ';'
		. '--blog-single-bg:' . esc_attr($active['single']) . ';'
		. '--blog-accent:' . esc_attr($active['accent']) . ';'
		. '--lightbox-bg:' . esc_attr($lightbox_bg) . ';'
		. '--reporter-archive-max:' . esc_attr($archive_active['reporter']) . ';'
		. '--blog-archive-max:' . esc_attr($archive_active['blog']) . ';'
		. '}';
	wp_add_inline_style('schilliger-base', $inline_css);
});

add_action('init', function () {
	register_post_type('reportage', [
		'labels' => [
			'name'          => __('Reportagen', 'schilliger'),
			'singular_name' => __('Reportage', 'schilliger'),
		],
		'public'       => true,
		'has_archive'  => true,
		'rewrite'      => ['slug' => 'reporter'],
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-media-document',
		'supports'     => ['title', 'editor', 'excerpt', 'thumbnail', 'author', 'revisions'],
	]);

	register_post_type('book', [
		'labels' => [
			'name'          => __('Books et al', 'schilliger'),
			'singular_name' => __('Medium', 'schilliger'),
		],
		'public'       => true,
		'has_archive'  => false,
		'rewrite'      => ['slug' => 'buch'],
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-book-alt',
		'supports'     => ['title', 'thumbnail', 'revisions'],
	]);

	register_taxonomy('book_tag', ['book'], [
		'labels' => [
			'name'          => __('Buch-Tags', 'schilliger'),
			'singular_name' => __('Buch-Tag', 'schilliger'),
		],
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'rewrite'           => ['slug' => 'buch-tag'],
	]);

	register_taxonomy('book_genre', ['book'], [
		'labels' => [
			'name'          => __('Genres', 'schilliger'),
			'singular_name' => __('Genre', 'schilliger'),
		],
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'rewrite'           => ['slug' => 'buch-genre'],
	]);

	register_taxonomy('book_topic', ['book'], [
		'labels' => [
			'name'          => __('Topics', 'schilliger'),
			'singular_name' => __('Topic', 'schilliger'),
		],
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'rewrite'           => ['slug' => 'buch-topic'],
	]);

	register_taxonomy('reportage_tag', ['reportage'], [
		'labels' => [
			'name'          => __('Reportage-Tags', 'schilliger'),
			'singular_name' => __('Reportage-Tag', 'schilliger'),
		],
		'public'            => true,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'hierarchical'      => false,
		'rewrite'           => ['slug' => 'reportage-tag'],
	]);

	register_post_type('auszeichnung', [
		'labels' => [
			'name'          => __('Auszeichnungen', 'schilliger'),
			'singular_name' => __('Auszeichnung', 'schilliger'),
		],
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-awards',
		'supports'     => ['title', 'page-attributes'],
	]);

	register_post_type('now_item', [
		'labels' => [
			'name'          => __('Now Items', 'schilliger'),
			'singular_name' => __('Now Item', 'schilliger'),
		],
		'public'       => false,
		'show_ui'      => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-clock',
		'supports'     => ['title', 'page-attributes'],
	]);
});

add_action('acf/init', function () {
	if (! function_exists('acf_add_local_field_group')) {
		return;
	}

	if (function_exists('acf_add_options_page')) {
		acf_add_options_page([
			'page_title' => 'Archiv Inhalte',
			'menu_title' => 'Archiv Inhalte',
			'menu_slug' => 'schilliger-archive-content',
			'capability' => 'edit_posts',
			'redirect' => false,
		]);
	}

	acf_add_local_field_group([
		'key' => 'group_reportage_meta',
		'title' => 'Reportage Metadaten',
		'fields' => [
			[
				'key' => 'field_publication',
				'label' => 'Publikation',
				'name' => 'publication',
				'type' => 'text',
			],
			[
				'key' => 'field_publication_date',
				'label' => 'Publikationsdatum',
				'name' => 'publication_date',
				'type' => 'date_picker',
				'display_format' => 'd.m.Y',
				'return_format' => 'Ymd',
			],
			[
				'key' => 'field_coauthor',
				'label' => 'Co-Autor',
				'name' => 'coauthor',
				'type' => 'text',
			],
			[
				'key' => 'field_related_blog_post',
				'label' => 'Verknuepfter Blogbeitrag',
				'name' => 'related_blog_post',
				'type' => 'post_object',
				'post_type' => ['post'],
				'return_format' => 'id',
			],
			[
				'key' => 'field_excerpt_short',
				'label' => 'Kurzbeschreibung',
				'name' => 'excerpt_short',
				'type' => 'textarea',
				'rows' => 3,
			],
		],
		'location' => [
			[
				[
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'reportage',
				],
			],
		],
	]);

	$book_buy_links_field = null;
	if (function_exists('acf_is_pro') && acf_is_pro()) {
		$book_buy_links_field = [
			'key' => 'field_book_buy_links',
			'label' => 'Kaufen Links',
			'name' => 'buy_links',
			'type' => 'repeater',
			'min' => 0,
			'layout' => 'row',
			'button_label' => 'Link hinzufuegen',
			'sub_fields' => [
				[
					'key' => 'field_book_buy_links_label',
					'label' => 'Label',
					'name' => 'label',
					'type' => 'text',
				],
				[
					'key' => 'field_book_buy_links_url',
					'label' => 'URL',
					'name' => 'url',
					'type' => 'text',
					'instructions' => 'Erlaubt z.B. https://... oder mailto:...',
				],
			],
		];
	} else {
		$book_buy_links_field = [
			'key' => 'field_book_buy_links_text',
			'label' => 'Kaufen Links',
			'name' => 'buy_links_text',
			'type' => 'textarea',
			'rows' => 5,
			'instructions' => "ACF Free: kein Repeater. Bitte pro Zeile ein Link im Format:\nLabel | https://...\nBeispiel:\nEx Libris | https://...\nOrell Fuessli | https://...",
		];
	}

	acf_add_local_field_group([
		'key' => 'group_book_meta',
		'title' => 'Books et al',
		'fields' => [
			[
				'key' => 'field_media_type',
				'label' => 'Typ',
				'name' => 'media_type',
				'type' => 'select',
				'choices' => [
					'book' => 'Buch',
					'podcast' => 'Podcast',
					'film' => 'Film',
				],
				'default_value' => 'book',
				'return_format' => 'value',
			],
			[
				'key' => 'field_book_author',
				'label' => 'Autor / Host / Regie',
				'name' => 'author',
				'type' => 'text',
			],
			[
				'key' => 'field_book_isbn',
				'label' => 'ISBN',
				'name' => 'isbn',
				'type' => 'text',
			],
			[
				'key' => 'field_book_publication_year',
				'label' => 'Publikationsjahr',
				'name' => 'publication_year',
				'type' => 'number',
				'min' => 0,
				'step' => 1,
			],
			[
				'key' => 'field_book_rating',
				'label' => 'Rating (1-5)',
				'name' => 'rating',
				'type' => 'number',
				'min' => 1,
				'max' => 5,
				'step' => 0.5,
			],
			[
				'key' => 'field_book_read_date',
				'label' => 'Gelesen / Gehoert / Gesehen am',
				'name' => 'read_date',
				'type' => 'date_picker',
				'display_format' => 'd.m.Y',
				'return_format' => 'Ymd',
			],
			[
				'key' => 'field_book_related_blog_post',
				'label' => 'Verknuepfter Blogbeitrag',
				'name' => 'related_blog_post',
				'type' => 'post_object',
				'post_type' => ['post'],
				'return_format' => 'id',
			],
			$book_buy_links_field,
		],
		'location' => [
			[
				[
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'book',
				],
			],
		],
	]);

	acf_add_local_field_group([
		'key' => 'group_awards_meta',
		'title' => 'Auszeichnung Metadaten',
		'fields' => [
			['key' => 'field_award_year', 'label' => 'Jahr', 'name' => 'award_year', 'type' => 'text'],
			[
				'key' => 'field_award_status',
				'label' => 'Status',
				'name' => 'award_status',
				'type' => 'select',
				'choices' => ['Gewinner' => 'Gewinner', 'Nominiert' => 'Nominiert'],
				'default_value' => 'Nominiert',
			],
			['key' => 'field_award_title', 'label' => 'Titel', 'name' => 'award_title', 'type' => 'text'],
			['key' => 'field_award_text', 'label' => 'Text', 'name' => 'award_text', 'type' => 'text'],
			['key' => 'field_award_category', 'label' => 'In der Kategorie', 'name' => 'award_category', 'type' => 'text'],
		],
		'location' => [
			[
				[
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'auszeichnung',
				],
			],
		],
	]);

	acf_add_local_field_group([
		'key' => 'group_now_meta',
		'title' => 'Now Item Inhalt',
		'fields' => [
			[
				'key' => 'field_now_text',
				'label' => 'Now Text',
				'name' => 'now_text',
				'type' => 'wysiwyg',
				'tabs' => 'visual',
				'toolbar' => 'basic',
				'media_upload' => 0,
			],
		],
		'location' => [
			[
				[
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'now_item',
				],
			],
		],
	]);

	acf_add_local_field_group([
		'key' => 'group_about_sticky_meta',
		'title' => 'About Sticky Sections',
		'fields' => [
			[
				'key' => 'field_about_eyebrow_override',
				'label' => 'Eyebrow Text Override',
				'name' => 'about_eyebrow_override',
				'type' => 'text',
				'instructions' => 'Optional: Ueberschreibt nur den oberen Eyebrow-Text. Die Sticky-Navigation bleibt unveraendert.',
				'default_value' => '',
				'placeholder' => '',
			],
		],
		'location' => [
			[
				[
					'param' => 'page_template',
					'operator' => '==',
					'value' => 'page-about-sticky-sections.php',
				],
			],
		],
	]);

	acf_add_local_field_group([
		'key' => 'group_archive_content_options',
		'title' => 'Archiv Inhalte',
		'fields' => [
			[
				'key' => 'field_reporter_archive_intro',
				'label' => 'Reporter Intro (unter H1)',
				'name' => 'reporter_archive_intro',
				'type' => 'wysiwyg',
				'tabs' => 'visual',
				'toolbar' => 'basic',
				'media_upload' => 0,
			],
			[
				'key' => 'field_pinned_blog_posts',
				'label' => 'PINNED Blogbeitraege',
				'name' => 'pinned_blog_posts',
				'type' => 'post_object',
				'instructions' => 'Bis zu 10 Beitraege. Reihenfolge per Drag & Drop bestimmt die Reihenfolge in der PINNED-Liste.',
				'post_type' => ['post'],
				'multiple' => 1,
				'max' => 10,
				'return_format' => 'id',
			],
		],
		'location' => [
			[
				[
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'schilliger-archive-content',
				],
			],
		],
	]);
});

function schilliger_has_sidebar(): bool {
	return true;
}

function schilliger_sidebar_items(): array {
	$posts_page_id = (int) get_option('page_for_posts');
	$blog_url = $posts_page_id ? get_permalink($posts_page_id) : home_url('/blog');

	return [
		'home' => ['label' => 'Startseite', 'url' => home_url('/')],
		'reporter' => ['label' => 'Reporter', 'url' => get_post_type_archive_link('reportage') ?: home_url('/reporter')],
		'blog' => ['label' => 'Blog', 'url' => $blog_url],
		'now' => ['label' => 'Now', 'url' => home_url('/now')],
		'moderation' => ['label' => 'Moderation & Anfragen', 'url' => home_url('/moderation')],
		'bibliothek' => ['label' => 'Bibliothek', 'url' => home_url('/bibliothek')],
	];
}

function schilliger_is_active_nav(string $key): bool {
	if ('home' === $key) {
		return is_front_page();
	}
	if ('reporter' === $key) {
		return is_post_type_archive('reportage') || is_singular('reportage');
	}
	if ('blog' === $key) {
		return is_home() || is_singular('post');
	}
	return is_page($key);
}

function schilliger_primary_menu_fallback(array $args): void {
	$items = schilliger_sidebar_items();
	$menu_class = isset($args['menu_class']) ? (string) $args['menu_class'] : 'nav-main';
	echo '<ul class="' . esc_attr($menu_class) . '">';
	foreach ($items as $key => $item) {
		$is_active = schilliger_is_active_nav($key);
		echo '<li class="' . ($is_active ? 'current-menu-item' : '') . '">';
		echo '<a href="' . esc_url($item['url']) . '">' . esc_html($item['label']) . '</a>';
		echo '</li>';
	}
	echo '</ul>';
}

add_filter('wp_nav_menu_objects', function (array $items, stdClass $args): array {
	if (! isset($args->theme_location) || 'top-nav' !== $args->theme_location) {
		return $items;
	}

	foreach ($items as $item) {
		$title = (string) $item->title;
		if (function_exists('mb_strtoupper')) {
			$item->title = mb_strtoupper($title, 'UTF-8');
		} else {
			$item->title = strtoupper($title);
		}
	}

	return $items;
}, 10, 2);

add_filter('nav_menu_css_class', function (array $classes, WP_Post $item, stdClass $args): array {
	if (! isset($args->theme_location) || 'top-nav' !== $args->theme_location) {
		return $classes;
	}

	$classes = array_values(array_filter($classes, function ($class) {
		return 0 !== strpos($class, 'current_') && 0 !== strpos($class, 'current-');
	}));

	$is_active = false;

	if ('post_type_archive' === $item->type && 'reportage' === $item->object) {
		$is_active = is_post_type_archive('reportage') || is_singular('reportage');
	} elseif ('post_type_archive' === $item->type && 'post' === $item->object) {
		$is_active = is_home() || is_singular('post');
	} elseif ('page' === $item->object) {
		$posts_page_id = (int) get_option('page_for_posts');
		if ($posts_page_id && (int) $item->object_id === $posts_page_id) {
			$is_active = is_home() || is_singular('post');
		} else {
			$is_active = is_page((int) $item->object_id);
		}
	} elseif ('custom' === $item->type) {
		$path = (string) wp_parse_url($item->url, PHP_URL_PATH);
		$path = trim($path, '/');
		if ('reporter' === $path) {
			$is_active = is_post_type_archive('reportage') || is_singular('reportage');
		} elseif ('blog' === $path) {
			$is_active = is_home() || is_singular('post');
		} elseif ('' === $path) {
			$is_active = is_front_page();
		}
	}

	if ($is_active) {
		$classes[] = 'schilliger-active';
	}

	return array_values(array_unique($classes));
}, 10, 3);

function schilliger_excerpt_from_post(int $post_id, int $length = 160): string {
	$excerpt = get_field('excerpt_short', $post_id);
	if (! $excerpt) {
		$excerpt = get_the_excerpt($post_id);
	}
	$excerpt = wp_strip_all_tags((string) $excerpt);
	return wp_html_excerpt($excerpt, $length, ' ...');
}

function schilliger_post_thumbnail_preserve_gif($post = null, string $size = 'large', array $attrs = []): string {
	$post_obj = get_post($post);
	if (! $post_obj) {
		return '';
	}
	$thumb_id = (int) get_post_thumbnail_id($post_obj);
	if (! $thumb_id) {
		return '';
	}

	$mime = (string) get_post_mime_type($thumb_id);
	$is_gif = ('image/gif' === $mime);
	if (! $is_gif) {
		$url = (string) wp_get_attachment_url($thumb_id);
		$ext = strtolower((string) pathinfo($url, PATHINFO_EXTENSION));
		$is_gif = ('gif' === $ext);
	}

	if ($is_gif) {
		$src = (string) wp_get_attachment_url($thumb_id);
		if (! $src) {
			return '';
		}
		$alt = get_post_meta($thumb_id, '_wp_attachment_image_alt', true);
		$classes = [];
		if (isset($attrs['class'])) {
			$classes[] = (string) $attrs['class'];
			unset($attrs['class']);
		}
		$classes[] = 'attachment-' . sanitize_html_class((string) $size);
		$classes[] = 'size-' . sanitize_html_class((string) $size);
		$classes[] = 'wp-post-image';
		$attr_html = '';
		foreach ($attrs as $key => $value) {
			if (null === $value || '' === $value) {
				continue;
			}
			$attr_html .= ' ' . esc_attr((string) $key) . '="' . esc_attr((string) $value) . '"';
		}
		return '<img src="' . esc_url($src) . '" alt="' . esc_attr((string) $alt) . '" class="' . esc_attr(trim(implode(' ', $classes))) . '"' . $attr_html . '>';
	}

	return (string) get_the_post_thumbnail($post_obj, $size, $attrs);
}

function schilliger_formatted_archive_excerpt(int $post_id, int $max_paragraphs = 2): string {
	$content = (string) get_post_field('post_content', $post_id);
	if (! $content) {
		return '';
	}
	$content = (string) preg_replace('/\[newsletter_signup[^\]]*\]/i', '', $content);

	$blocks = parse_blocks($content);
	$allowed = ['core/paragraph', 'core/list', 'core/quote', 'core/heading'];
	$parts = [];
	foreach ($blocks as $block) {
		$name = isset($block['blockName']) ? (string) $block['blockName'] : '';
		if (! in_array($name, $allowed, true)) {
			continue;
		}
		$parts[] = render_block($block);
		if (count($parts) >= $max_paragraphs) {
			break;
		}
	}

	if (! $parts) {
		$text = wp_strip_all_tags($content);
		return wpautop(wp_html_excerpt($text, 520, ' ...'));
	}

	return implode("\n", $parts);
}

function schilliger_format_acf_date(string $ymd): string {
	if (! $ymd) {
		return '';
	}
	$dt = DateTime::createFromFormat('Ymd', $ymd);
	if (! $dt) {
		return $ymd;
	}
	return date_i18n('j. F Y', $dt->getTimestamp());
}

add_action('pre_get_posts', function (WP_Query $query) {
	if (is_admin() || ! $query->is_main_query() || ! $query->is_post_type_archive('reportage')) {
		return;
	}

	$query->set('meta_key', 'publication_date');
	$query->set('orderby', 'meta_value_num');
	$query->set('order', 'DESC');

	$slug = '';
	if (isset($_GET['rt'])) {
		$slug = sanitize_text_field(wp_unslash($_GET['rt']));
	}
	if (! $slug) {
		return;
	}

	$query->set('tax_query', [
		[
			'taxonomy' => 'reportage_tag',
			'field' => 'slug',
			'terms' => $slug,
		],
	]);
});

add_action('customize_register', function (WP_Customize_Manager $wp_customize) {
	$wp_customize->add_section('schilliger_frontpage_intro', [
		'title' => __('Startseite Intro', 'schilliger'),
		'priority' => 150,
	]);

	$wp_customize->add_setting('schilliger_intro_text', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'wp_kses_post',
		'default' => '',
	]);
	$wp_customize->add_control('schilliger_intro_text', [
		'label' => __('Hero Intro Text (HTML erlaubt)', 'schilliger'),
		'section' => 'schilliger_frontpage_intro',
		'type' => 'textarea',
	]);

	$wp_customize->add_setting('schilliger_intro_cta_text', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => __('Mehr ueber mich', 'schilliger'),
	]);
	$wp_customize->add_control('schilliger_intro_cta_text', [
		'label' => __('Hero CTA Text', 'schilliger'),
		'section' => 'schilliger_frontpage_intro',
		'type' => 'text',
	]);

	$wp_customize->add_setting('schilliger_intro_cta_url', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'esc_url_raw',
		'default' => home_url('/ueber-mich'),
	]);
	$wp_customize->add_control('schilliger_intro_cta_url', [
		'label' => __('Hero CTA URL', 'schilliger'),
		'section' => 'schilliger_frontpage_intro',
		'type' => 'url',
	]);

	$wp_customize->add_setting('schilliger_mobile_reporter_layout', [
		'type' => 'theme_mod',
		'sanitize_callback' => function ($value) {
			$allowed = ['default', 'a', 'b'];
			return in_array($value, $allowed, true) ? $value : 'default';
		},
		'default' => 'default',
	]);
	$wp_customize->add_control('schilliger_mobile_reporter_layout', [
		'label' => __('Startseite: Mobile-Layout Reportagen', 'schilliger'),
		'section' => 'schilliger_frontpage_intro',
		'type' => 'select',
		'choices' => [
			'default' => __('Standard (bisher)', 'schilliger'),
			'a' => __('Variante A (kompakte Liste, 4 Eintraege)', 'schilliger'),
			'b' => __('Variante B (Feature + Liste)', 'schilliger'),
		],
	]);

	$wp_customize->add_section('schilliger_newsletter', [
		'title' => __('Newsletter', 'schilliger'),
		'priority' => 160,
	]);

	$wp_customize->add_section('schilliger_design', [
		'title' => __('Design', 'schilliger'),
		'priority' => 155,
	]);

	$wp_customize->add_section('schilliger_bibliothek', [
		'title' => __('Bibliothek', 'schilliger'),
		'priority' => 170,
	]);

	$wp_customize->add_setting('schilliger_library_intro', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'wp_kses_post',
		'default' => '<p><strong><em>Ich habe in von und mit Büchern gelebt - und nirgendwo mehr gelernt als in den Werken, die sich hier aufgelistet finden.</em></strong></p>',
	]);
	$wp_customize->add_control('schilliger_library_intro', [
		'label' => __('Bibliothek Intro (HTML erlaubt)', 'schilliger'),
		'section' => 'schilliger_bibliothek',
		'type' => 'textarea',
	]);

	$wp_customize->add_setting('schilliger_blog_palette', [
		'type' => 'theme_mod',
		'sanitize_callback' => function ($value) {
			$allowed = ['soft', 'medium', 'strong'];
			return in_array($value, $allowed, true) ? $value : 'medium';
		},
		'default' => 'medium',
	]);
	$wp_customize->add_control('schilliger_blog_palette', [
		'label' => __('Blog-Lachsfarbton', 'schilliger'),
		'section' => 'schilliger_design',
		'type' => 'select',
		'choices' => [
			'soft' => __('Dezent', 'schilliger'),
			'medium' => __('Mittel', 'schilliger'),
			'strong' => __('Kraeftig', 'schilliger'),
		],
	]);

	$wp_customize->add_setting('schilliger_accent_color', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'sanitize_hex_color',
		'default' => '#ad0a09',
	]);
	$wp_customize->add_control(new WP_Customize_Color_Control(
		$wp_customize,
		'schilliger_accent_color',
		[
			'label' => __('Akzentfarbe (global)', 'schilliger'),
			'section' => 'schilliger_design',
			'settings' => 'schilliger_accent_color',
		]
	));

	$wp_customize->add_setting('schilliger_lightbox_bg', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'sanitize_hex_color',
		'default' => '#000000',
	]);
	$wp_customize->add_control(new WP_Customize_Color_Control(
		$wp_customize,
		'schilliger_lightbox_bg',
		[
			'label' => __('Lightbox Hintergrundfarbe', 'schilliger'),
			'section' => 'schilliger_design',
			'settings' => 'schilliger_lightbox_bg',
		]
	));

	$wp_customize->add_setting('schilliger_header_variant', [
		'type' => 'theme_mod',
		'sanitize_callback' => function ($value) {
			$allowed = ['a', 'b', 'c', 'd', 'e'];
			return in_array($value, $allowed, true) ? $value : 'a';
		},
		'default' => 'a',
	]);
	$wp_customize->add_control('schilliger_header_variant', [
		'label' => __('Header Designvariante', 'schilliger'),
		'section' => 'schilliger_design',
		'type' => 'select',
		'choices' => [
			'a' => __('Variante A (Editorial Leiste)', 'schilliger'),
			'b' => __('Variante B (Minimal Bold)', 'schilliger'),
			'c' => __('Variante C (Radikal / Manifest)', 'schilliger'),
			'd' => __('Variante D (Kompakt)', 'schilliger'),
			'e' => __('Variante E (Kompakt, Nav neben Name)', 'schilliger'),
		],
	]);

	$wp_customize->add_setting('schilliger_archive_width_variant', [
		'type' => 'theme_mod',
		'sanitize_callback' => function ($value) {
			$allowed = ['a', 'b', 'c'];
			return in_array($value, $allowed, true) ? $value : 'b';
		},
		'default' => 'b',
	]);
	$wp_customize->add_control('schilliger_archive_width_variant', [
		'label' => __('Archivbreite Reporter/Blog', 'schilliger'),
		'section' => 'schilliger_design',
		'type' => 'select',
		'choices' => [
			'a' => __('Variante A (1120 / 920)', 'schilliger'),
			'b' => __('Variante B (1160 / 960)', 'schilliger'),
			'c' => __('Variante C (1200 / 1000)', 'schilliger'),
		],
	]);

	$wp_customize->add_setting('schilliger_mailchimp_action', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'esc_url_raw',
		'default' => 'https://assets.mailerlite.com/jsonp/2184895/forms/181751966978802783/subscribe',
	]);
	$wp_customize->add_control('schilliger_mailchimp_action', [
		'label' => __('MailerLite Action URL', 'schilliger'),
		'section' => 'schilliger_newsletter',
		'type' => 'url',
	]);

	$wp_customize->add_setting('schilliger_newsletter_recipient', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'sanitize_email',
		'default' => get_option('admin_email'),
	]);
	$wp_customize->add_control('schilliger_newsletter_recipient', [
		'label' => __('Fallback E-Mail Empfaenger', 'schilliger'),
		'section' => 'schilliger_newsletter',
		'type' => 'email',
	]);

	$wp_customize->add_setting('schilliger_newsletter_title', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'sanitize_text_field',
		'default' => __('Into the Lore', 'schilliger'),
	]);
	$wp_customize->add_control('schilliger_newsletter_title', [
		'label' => __('Newsletter Titel', 'schilliger'),
		'section' => 'schilliger_newsletter',
		'type' => 'text',
	]);

	$wp_customize->add_setting('schilliger_newsletter_text', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'sanitize_textarea_field',
		'default' => __('Neue Texte, Lektuereempfehlungen und gelegentliche Gedanken direkt ins Postfach.', 'schilliger'),
	]);
	$wp_customize->add_control('schilliger_newsletter_text', [
		'label' => __('Newsletter Beschreibung', 'schilliger'),
		'section' => 'schilliger_newsletter',
		'type' => 'textarea',
	]);

	$wp_customize->add_setting('schilliger_newsletter_note', [
		'type' => 'theme_mod',
		'sanitize_callback' => 'sanitize_textarea_field',
		'default' => __('Jederzeit abmeldbar. Keine Weitergabe an Dritte.', 'schilliger'),
	]);
	$wp_customize->add_control('schilliger_newsletter_note', [
		'label' => __('Newsletter Hinweiszeile', 'schilliger'),
		'section' => 'schilliger_newsletter',
		'type' => 'textarea',
	]);
});

add_filter('body_class', function (array $classes): array {
	$variant = (string) get_theme_mod('schilliger_header_variant', 'a');
	$classes[] = 'header-variant-' . $variant;
	$mobile_reporter_layout = (string) get_theme_mod('schilliger_mobile_reporter_layout', 'default');
	if ('a' === $mobile_reporter_layout || 'b' === $mobile_reporter_layout) {
		$classes[] = 'mobile-reporter-layout-' . $mobile_reporter_layout;
	}
	if (is_page('bibliothek')) {
		$classes[] = 'bibliothek-world';
	}
	return $classes;
});

add_action('wp_ajax_schilliger_newsletter_signup', 'schilliger_newsletter_signup');
add_action('wp_ajax_nopriv_schilliger_newsletter_signup', 'schilliger_newsletter_signup');

add_action('admin_enqueue_scripts', function (string $hook): void {
	if (! in_array($hook, ['post.php', 'post-new.php'], true)) {
		return;
	}
	$screen = function_exists('get_current_screen') ? get_current_screen() : null;
	if (! $screen || 'book' !== $screen->post_type) {
		return;
	}

	wp_enqueue_script(
		'schilliger-book-cover-fetch',
		get_theme_file_uri('/assets/js/book-cover-fetch.js'),
		[],
		schilliger_asset_version('/assets/js/book-cover-fetch.js'),
		true
	);
	wp_localize_script('schilliger-book-cover-fetch', 'schilligerBookCover', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('schilliger_book_cover_fetch'),
	]);
});

add_action('add_meta_boxes', function (): void {
	add_meta_box(
		'schilliger_book_cover',
		__('Cover', 'schilliger'),
		function (WP_Post $post): void {
			if ('book' !== $post->post_type) {
				return;
			}
			echo '<p><button type="button" class="button" data-schilliger-cover-fetch>Cover suchen</button></p>';
			echo '<p class="description">Sucht via Open Library + Google Books und setzt das Cover als Beitragsbild.</p>';
			echo '<div data-schilliger-cover-status style="margin-top:8px;"></div>';
		},
		'book',
		'side',
		'high'
	);
});

add_action('wp_ajax_schilliger_book_cover_fetch', function (): void {
	check_ajax_referer('schilliger_book_cover_fetch', 'nonce');

	if (! current_user_can('edit_posts')) {
		wp_send_json_error(['message' => 'Keine Berechtigung.'], 403);
	}

	$post_id = isset($_POST['postId']) ? absint($_POST['postId']) : 0;
	if (! $post_id || 'book' !== get_post_type($post_id)) {
		wp_send_json_error(['message' => 'Ungueltiges Buch.'], 400);
	}
	if (! current_user_can('edit_post', $post_id)) {
		wp_send_json_error(['message' => 'Keine Berechtigung fuer dieses Buch.'], 403);
	}

	$title = get_the_title($post_id);
	$author = (string) get_post_meta($post_id, 'author', true);
	$isbn = (string) get_post_meta($post_id, 'isbn', true);

	$isbn = preg_replace('/[^0-9Xx]/', '', $isbn ?: '');
	$query_title = trim((string) $title);
	$query_author = trim((string) $author);

	if (! $query_title && ! $isbn) {
		wp_send_json_error(['message' => 'Bitte mindestens einen Titel (Post-Titel) oder ISBN erfassen.'], 400);
	}

	$candidate_urls = [];

	// Open Library first
	if ($isbn) {
		$candidate_urls[] = 'https://covers.openlibrary.org/b/isbn/' . rawurlencode($isbn) . '-L.jpg?default=false';
	}
	if ($query_title) {
		$ol_url = add_query_arg([
			'title' => $query_title,
			'author' => $query_author,
		], 'https://openlibrary.org/search.json');
		$resp = wp_remote_get($ol_url, ['timeout' => 12]);
		if (! is_wp_error($resp) && 200 === (int) wp_remote_retrieve_response_code($resp)) {
			$body = (string) wp_remote_retrieve_body($resp);
			$data = json_decode($body, true);
			if (is_array($data) && ! empty($data['docs'][0]['cover_i'])) {
				$cover_id = (int) $data['docs'][0]['cover_i'];
				if ($cover_id) {
					$candidate_urls[] = 'https://covers.openlibrary.org/b/id/' . $cover_id . '-L.jpg?default=false';
				}
			}
		}
	}

	// Google Books second
	$gb_q = '';
	if ($isbn) {
		$gb_q = 'isbn:' . $isbn;
	} elseif ($query_title) {
		$gb_q = 'intitle:' . $query_title;
		if ($query_author) {
			$gb_q .= '+inauthor:' . $query_author;
		}
	}
	if ($gb_q) {
		$gb_url = add_query_arg(['q' => $gb_q, 'maxResults' => 5], 'https://www.googleapis.com/books/v1/volumes');
		$resp = wp_remote_get($gb_url, ['timeout' => 12]);
		if (! is_wp_error($resp) && 200 === (int) wp_remote_retrieve_response_code($resp)) {
			$body = (string) wp_remote_retrieve_body($resp);
			$data = json_decode($body, true);
			if (is_array($data) && ! empty($data['items'][0]['volumeInfo']['imageLinks'])) {
				$links = $data['items'][0]['volumeInfo']['imageLinks'];
				$gb_img = '';
				if (isset($links['thumbnail'])) {
					$gb_img = (string) $links['thumbnail'];
				} elseif (isset($links['smallThumbnail'])) {
					$gb_img = (string) $links['smallThumbnail'];
				}
				if ($gb_img) {
					$gb_img = preg_replace('/^http:/i', 'https:', $gb_img);
					$candidate_urls[] = $gb_img;
				}
			}
		}
	}

	$candidate_urls = array_values(array_unique(array_filter($candidate_urls)));
	if (! $candidate_urls) {
		wp_send_json_error(['message' => 'Kein Cover gefunden.'], 404);
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';

	$last_error = '';
	foreach ($candidate_urls as $url) {
		$tmp = download_url($url, 15);
		if (is_wp_error($tmp)) {
			$last_error = $tmp->get_error_message();
			continue;
		}

		$filesize = @filesize($tmp);
		if (! $filesize || $filesize < 1500) {
			@unlink($tmp);
			$last_error = 'Download war zu klein/leer.';
			continue;
		}
		$mime = function_exists('wp_get_image_mime') ? (string) wp_get_image_mime($tmp) : '';
		if (! $mime || 0 !== strpos($mime, 'image/')) {
			@unlink($tmp);
			$last_error = 'Download war kein Bild.';
			continue;
		}

		$ext = 'jpg';
		if ('image/png' === $mime) {
			$ext = 'png';
		} elseif ('image/webp' === $mime) {
			$ext = 'webp';
		}

		$filename = 'book-cover-' . $post_id . '-' . wp_generate_password(6, false, false) . '.' . $ext;
		$file_array = [
			'name' => $filename,
			'tmp_name' => $tmp,
		];

		$att_id = media_handle_sideload($file_array, $post_id);
		if (is_wp_error($att_id)) {
			@unlink($tmp);
			$last_error = $att_id->get_error_message();
			continue;
		}

		set_post_thumbnail($post_id, (int) $att_id);
		update_post_meta($post_id, 'schilliger_cover_source', $url);
		wp_send_json_success([
			'message' => 'Cover importiert und gesetzt.',
			'attachmentId' => (int) $att_id,
			'url' => $url,
		]);
	}

	wp_send_json_error(['message' => 'Cover konnte nicht importiert werden.' . ($last_error ? ' ' . $last_error : '')], 500);
});

add_action('admin_menu', function () {
	if (function_exists('acf_add_options_page')) {
		return;
	}
	add_menu_page(
		'Archiv Inhalte',
		'Archiv Inhalte',
		'edit_posts',
		'schilliger-archive-content',
		'schilliger_render_archive_content_page',
		'dashicons-welcome-write-blog',
		60
	);
});

add_action('admin_init', function () {
	register_setting('schilliger_archive_content_options', 'schilliger_reporter_archive_intro', [
		'type' => 'string',
		'sanitize_callback' => 'wp_kses_post',
		'default' => '',
	]);
	register_setting('schilliger_archive_content_options', 'schilliger_featured_reportage_main', [
		'type' => 'integer',
		'sanitize_callback' => 'absint',
		'default' => 0,
	]);
	register_setting('schilliger_archive_content_options', 'schilliger_featured_reportage_second', [
		'type' => 'integer',
		'sanitize_callback' => 'absint',
		'default' => 0,
	]);
	register_setting('schilliger_archive_content_options', 'schilliger_featured_reportage_third', [
		'type' => 'integer',
		'sanitize_callback' => 'absint',
		'default' => 0,
	]);
	register_setting('schilliger_archive_content_options', 'schilliger_featured_reportage_fourth', [
		'type' => 'integer',
		'sanitize_callback' => 'absint',
		'default' => 0,
	]);
	for ($i = 1; $i <= 10; $i++) {
		register_setting('schilliger_archive_content_options', 'schilliger_pinned_post_' . $i, [
			'type' => 'integer',
			'sanitize_callback' => 'absint',
			'default' => 0,
		]);
	}
});

function schilliger_render_archive_content_page(): void {
	if (! current_user_can('edit_posts')) {
		return;
	}
	$value = (string) get_option('schilliger_reporter_archive_intro', '');
	$featured_main = (int) get_option('schilliger_featured_reportage_main', 0);
	$featured_second = (int) get_option('schilliger_featured_reportage_second', 0);
	$featured_third = (int) get_option('schilliger_featured_reportage_third', 0);
	$featured_fourth = (int) get_option('schilliger_featured_reportage_fourth', 0);
	$reportage_choices = get_posts([
		'post_type' => 'reportage',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	]);
	$pinned_post_choices = get_posts([
		'post_type' => 'post',
		'post_status' => 'publish',
		'numberposts' => -1,
		'orderby' => 'date',
		'order' => 'DESC',
	]);
	$pinned_post_values = [];
	for ($i = 1; $i <= 10; $i++) {
		$pinned_post_values[$i] = (int) get_option('schilliger_pinned_post_' . $i, 0);
	}
	?>
	<div class="wrap">
		<h1>Archiv Inhalte</h1>
		<form method="post" action="options.php">
			<?php settings_fields('schilliger_archive_content_options'); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="schilliger_reporter_archive_intro">Reporter Intro (unter H1)</label></th>
					<td>
						<?php
						wp_editor($value, 'schilliger_reporter_archive_intro_editor', [
							'textarea_name' => 'schilliger_reporter_archive_intro',
							'textarea_rows' => 8,
							'media_buttons' => false,
							'teeny' => true,
						]);
						?>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="schilliger_featured_reportage_main">Lieblingsreportage 1 (Hauptreportage)</label></th>
					<td>
						<select id="schilliger_featured_reportage_main" name="schilliger_featured_reportage_main">
							<option value="0">— Keine Auswahl —</option>
							<?php foreach ($reportage_choices as $choice) : ?>
								<option value="<?php echo esc_attr((string) $choice->ID); ?>" <?php selected($featured_main, (int) $choice->ID); ?>>
									<?php echo esc_html(get_the_title($choice)); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="schilliger_featured_reportage_second">Lieblingsreportage 2</label></th>
					<td>
						<select id="schilliger_featured_reportage_second" name="schilliger_featured_reportage_second">
							<option value="0">— Keine Auswahl —</option>
							<?php foreach ($reportage_choices as $choice) : ?>
								<option value="<?php echo esc_attr((string) $choice->ID); ?>" <?php selected($featured_second, (int) $choice->ID); ?>>
									<?php echo esc_html(get_the_title($choice)); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="schilliger_featured_reportage_third">Lieblingsreportage 3</label></th>
					<td>
						<select id="schilliger_featured_reportage_third" name="schilliger_featured_reportage_third">
							<option value="0">— Keine Auswahl —</option>
							<?php foreach ($reportage_choices as $choice) : ?>
								<option value="<?php echo esc_attr((string) $choice->ID); ?>" <?php selected($featured_third, (int) $choice->ID); ?>>
									<?php echo esc_html(get_the_title($choice)); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="schilliger_featured_reportage_fourth">Lieblingsreportage 4</label></th>
					<td>
						<select id="schilliger_featured_reportage_fourth" name="schilliger_featured_reportage_fourth">
							<option value="0">— Keine Auswahl —</option>
							<?php foreach ($reportage_choices as $choice) : ?>
								<option value="<?php echo esc_attr((string) $choice->ID); ?>" <?php selected($featured_fourth, (int) $choice->ID); ?>>
									<?php echo esc_html(get_the_title($choice)); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
					<?php for ($i = 1; $i <= 10; $i++) : ?>
					<tr>
						<th scope="row"><label for="schilliger_pinned_post_<?php echo esc_attr((string) $i); ?>">PINNED Beitrag <?php echo esc_html((string) $i); ?></label></th>
						<td>
							<select id="schilliger_pinned_post_<?php echo esc_attr((string) $i); ?>" name="schilliger_pinned_post_<?php echo esc_attr((string) $i); ?>">
								<option value="0">— Keine Auswahl —</option>
								<?php foreach ($pinned_post_choices as $choice) : ?>
									<option value="<?php echo esc_attr((string) $choice->ID); ?>" <?php selected($pinned_post_values[$i], (int) $choice->ID); ?>>
										<?php echo esc_html(get_the_title($choice)); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<?php endfor; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}

function schilliger_pinned_blog_posts(): array {
	$ids = [];
	if (function_exists('get_field')) {
		$acf_ids = get_field('pinned_blog_posts', 'option');
		if (is_array($acf_ids)) {
			$ids = array_map('intval', $acf_ids);
		}
	}
	if (! $ids) {
		for ($i = 1; $i <= 10; $i++) {
			$id = (int) get_option('schilliger_pinned_post_' . $i, 0);
			if ($id) {
				$ids[] = $id;
			}
		}
	}
	return array_values(array_unique(array_filter($ids)));
}

function schilliger_newsletter_client_ip(): string {
	$ip = isset($_SERVER['REMOTE_ADDR']) ? (string) $_SERVER['REMOTE_ADDR'] : '';
	return preg_match('/^[0-9a-fA-F.:]+$/', $ip) ? $ip : '';
}

function schilliger_verify_turnstile(string $token, string $remote_ip): bool {
	if (! defined('SCHILLIGER_TURNSTILE_SECRET_KEY') || ! SCHILLIGER_TURNSTILE_SECRET_KEY) {
		// Secret key not set up on the server yet - don't block real signups in the meantime.
		return true;
	}
	if (! $token) {
		return false;
	}

	$response = wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
		'timeout' => 8,
		'body' => [
			'secret' => SCHILLIGER_TURNSTILE_SECRET_KEY,
			'response' => $token,
			'remoteip' => $remote_ip,
		],
	]);

	if (is_wp_error($response)) {
		return false;
	}

	$body = json_decode((string) wp_remote_retrieve_body($response), true);
	return is_array($body) && ! empty($body['success']);
}

function schilliger_newsletter_signup(): void {
	check_ajax_referer('schilliger_newsletter_signup', 'nonce');

	$ip = schilliger_newsletter_client_ip();

	// Honeypot: bots that fill hidden fields get a fake success, nothing is sent anywhere.
	$honeypot = isset($_POST['hp']) ? sanitize_text_field(wp_unslash($_POST['hp'])) : '';
	if ($honeypot) {
		wp_send_json_success(['message' => __('Danke! Die Anmeldung ist eingegangen.', 'schilliger')]);
	}

	// Timing gate: the form carries its own render time, a real visitor needs at least a couple of seconds.
	$rendered_at = isset($_POST['ts']) ? (int) $_POST['ts'] : 0;
	if (! $rendered_at || (time() - $rendered_at) < 2) {
		wp_send_json_success(['message' => __('Danke! Die Anmeldung ist eingegangen.', 'schilliger')]);
	}

	// Cloudflare Turnstile: a real check, so a genuine visitor gets a real error and can retry.
	$turnstile_token = isset($_POST['cf-turnstile-response']) ? sanitize_text_field(wp_unslash($_POST['cf-turnstile-response'])) : '';
	if (! schilliger_verify_turnstile($turnstile_token, $ip)) {
		wp_send_json_error(['message' => __('Sicherheitspruefung fehlgeschlagen. Bitte Seite neu laden und erneut versuchen.', 'schilliger')], 403);
	}

	// Rate limit per IP so a script can't just keep hammering this endpoint.
	if ($ip) {
		$rate_key = 'schilliger_nl_rl_' . md5($ip);
		$attempts = (int) get_transient($rate_key);
		if ($attempts >= 5) {
			wp_send_json_error(['message' => __('Zu viele Anmeldungen. Bitte spaeter erneut versuchen.', 'schilliger')], 429);
		}
		set_transient($rate_key, $attempts + 1, 10 * MINUTE_IN_SECONDS);
	}

	$email = '';
	if (isset($_POST['email'])) {
		$email = sanitize_email(wp_unslash($_POST['email']));
	}

	if (! $email || ! is_email($email)) {
		wp_send_json_error(['message' => __('Bitte eine gueltige E-Mail-Adresse eingeben.', 'schilliger')], 400);
	}

	$mailerlite_action = (string) get_theme_mod('schilliger_mailchimp_action', 'https://assets.mailerlite.com/jsonp/2184895/forms/181751966978802783/subscribe');
	$mailerlite_response = wp_remote_post($mailerlite_action, [
		'timeout' => 10,
		'body' => [
			'fields[email]' => $email,
			'ml-submit' => '1',
			'anticsrf' => 'true',
		],
	]);

	if (is_wp_error($mailerlite_response) || (int) wp_remote_retrieve_response_code($mailerlite_response) >= 400) {
		wp_send_json_error(['message' => __('Die Anmeldung ist fehlgeschlagen. Bitte spaeter erneut versuchen.', 'schilliger')], 502);
	}

	$recipient = get_theme_mod('schilliger_newsletter_recipient', get_option('admin_email'));
	$recipient = sanitize_email((string) $recipient);
	if (! $recipient) {
		$recipient = get_option('admin_email');
	}

	$subject = sprintf(__('Neue Newsletter-Anmeldung auf %s', 'schilliger'), wp_parse_url(home_url('/'), PHP_URL_HOST));
	$body = "Neue Newsletter-Anmeldung:\n\n" . $email;
	$headers = ['Content-Type: text/plain; charset=UTF-8'];
	wp_mail($recipient, $subject, $body, $headers);

	wp_send_json_success(['message' => __('Danke! Die Anmeldung ist eingegangen.', 'schilliger')]);
}

function schilliger_newsletter_signup_shortcode($atts = []): string {
	$atts = shortcode_atts([
		'title' => (string) get_theme_mod('schilliger_newsletter_title', 'Into the Lore'),
		'text' => (string) get_theme_mod('schilliger_newsletter_text', 'Neue Texte, Lektuereempfehlungen und gelegentliche Gedanken direkt ins Postfach.'),
	], $atts, 'newsletter_signup');

	ob_start();
	?>
	<div class="newsletter-embed nl-widget">
		<div class="eyebrow">Newsletter</div>
		<h3 class="newsletter-embed-title"><?php echo esc_html((string) $atts['title']); ?></h3>
		<p class="newsletter-embed-text"><?php echo esc_html((string) $atts['text']); ?></p>
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
	</div>
	<?php
	return (string) ob_get_clean();
}
add_shortcode('newsletter_signup', 'schilliger_newsletter_signup_shortcode');
