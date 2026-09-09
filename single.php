<?php
/**
 * Single blog post template.
 *
 * @package schilliger
 */

get_header();
the_post();

$post_permalink = get_permalink();
$post_title_raw = html_entity_decode((string) get_the_title(), ENT_QUOTES, get_bloginfo('charset'));
$share_url = rawurlencode($post_permalink);
$share_title = rawurlencode($post_title_raw);
$share_email_subject = rawurlencode('Interessanter Beitrag: ' . $post_title_raw);
$share_email_body = rawurlencode($post_title_raw . "\n\n" . $post_permalink);
?>
<main class="single-post-main">
	<article class="single-post-wrap">
		<div class="single-post-inner">
			<span class="post-kicker">Blog · <?php echo esc_html(get_the_date('j. F Y')); ?></span>
			<h1 class="post-title"><?php the_title(); ?></h1>
			<div class="post-body">
				<?php the_content(); ?>
			</div>
			<div class="post-share" aria-label="Beitrag teilen">
				<span class="post-share-label">Teilen</span>
				<div class="post-share-links">
					<button type="button" class="post-share-native" data-share-title="<?php echo esc_attr($post_title_raw); ?>" data-share-url="<?php echo esc_url($post_permalink); ?>">Direkt teilen</button>
					<a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo esc_attr($share_url); ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
					<a href="https://twitter.com/intent/tweet?text=<?php echo esc_attr($share_title); ?>&amp;url=<?php echo esc_attr($share_url); ?>" target="_blank" rel="noopener noreferrer">X</a>
					<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo esc_attr($share_url); ?>" target="_blank" rel="noopener noreferrer">Facebook</a>
					<a href="https://wa.me/?text=<?php echo esc_attr($share_title . '%20' . $share_url); ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
					<a href="mailto:?subject=<?php echo esc_attr($share_email_subject); ?>&amp;body=<?php echo esc_attr($share_email_body); ?>">Per E-Mail</a>
				</div>
			</div>
			<nav class="post-nav" aria-label="Beitragsnavigation">
				<div class="post-nav-prev"><?php previous_post_link('%link', '← %title'); ?></div>
				<div class="post-nav-next"><?php next_post_link('%link', '%title →'); ?></div>
			</nav>
			<?php if (comments_open() || get_comments_number()) : ?>
				<section class="post-comments" id="comments">
					<h2 class="post-comments-title">Kommentare</h2>
					<?php if (have_comments()) : ?>
						<ol class="comment-list">
							<?php
							wp_list_comments([
								'style' => 'ol',
								'short_ping' => true,
								'avatar_size' => 44,
							]);
							?>
						</ol>
						<?php
						the_comments_pagination([
							'prev_text' => '← Ältere Kommentare',
							'next_text' => 'Neuere Kommentare →',
						]);
						?>
					<?php endif; ?>
					<?php
					comment_form([
						'title_reply' => 'Kommentar schreiben',
						'label_submit' => 'Kommentar senden',
						'comment_notes_before' => '',
						'comment_notes_after' => '',
					]);
					?>
				</section>
			<?php endif; ?>
		</div>
	</article>
</main>
<script>
  (function () {
    var button = document.querySelector(".post-share-native");
    if (!button) return;
    if (!navigator.share) {
      button.style.display = "none";
      return;
    }
    button.addEventListener("click", function () {
      navigator.share({
        title: button.getAttribute("data-share-title") || document.title,
        url: button.getAttribute("data-share-url") || window.location.href
      }).catch(function () {});
    });
  })();
</script>
<?php get_footer(); ?>
