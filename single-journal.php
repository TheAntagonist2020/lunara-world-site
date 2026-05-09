<?php
/**
 * Single Journal Entry - Lunara Film
 *
 * Dedicated template for the `journal` CPT. Without this file, WordPress
 * falls back through single.php -> locate_template -> index.php, which
 * renders a generic archive-list view instead of a proper article page.
 *
 * @package Lunara_Film
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		$post_id     = get_the_ID();
		$kicker      = function_exists( 'lunara_get_journal_kicker' )
			? lunara_get_journal_kicker( $post_id )
			: __( 'Journal', 'lunara-film' );
		$signal_note = function_exists( 'lunara_get_journal_signal_note' )
			? lunara_get_journal_signal_note( $post_id )
			: '';

		$published    = get_the_date( 'F j, Y', $post_id );
		$author_name  = get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) );
		$reading_time = function_exists( 'lunara_get_post_reading_time' )
			? lunara_get_post_reading_time( $post_id )
			: '';

		$has_thumb   = has_post_thumbnail( $post_id );
		$archive_url = get_post_type_archive_link( 'journal' );
		?>

		<main class="lunara-editorial-single-page lunara-journal-single-page">
		<article <?php post_class( 'lunara-journal-single lunara-review-single' ); ?>>

			<section class="lunara-journal-single-hero lunara-journal-cinematic-hero<?php echo $has_thumb ? ' has-hero-image' : ' has-no-hero-image'; ?>">
				<div class="lunara-journal-cinematic-hero-header">
					<div class="lunara-journal-cinematic-hero-inner">
						<p class="lunara-review-single-kicker"><?php echo esc_html( $kicker ); ?></p>
						<h1 class="lunara-review-single-title"><?php the_title(); ?></h1>

						<?php if ( '' !== $signal_note ) : ?>
							<p class="lunara-journal-single-signal"><?php echo esc_html( $signal_note ); ?></p>
						<?php endif; ?>

						<?php
						// Customizer-controlled visibility for byline / date / reading time.
						// Defaults: byline + date ON, reading time OFF (per Dalton 2026-04-29).
						$show_byline       = (bool) get_theme_mod( 'lunara_journal_show_byline', true );
						$show_date         = (bool) get_theme_mod( 'lunara_journal_show_date', true );
						$show_reading_time = (bool) get_theme_mod( 'lunara_journal_show_reading_time', false );

						if ( $show_byline || $show_date || $show_reading_time ) :
							$meta_parts = array();
							if ( $show_byline && '' !== $author_name ) {
								/* translators: %s: author name */
								$meta_parts[] = '<span class="lunara-review-single-meta-byline">' . esc_html( sprintf( __( 'By %s', 'lunara-film' ), $author_name ) ) . '</span>';
							}
							if ( $show_date && '' !== $published ) {
								$meta_parts[] = '<span class="lunara-review-single-meta-date">' . esc_html( $published ) . '</span>';
							}
							if ( $show_reading_time && '' !== $reading_time ) {
								$meta_parts[] = '<span class="lunara-review-single-meta-time">' . esc_html( $reading_time ) . '</span>';
							}
						?>
						<div class="lunara-review-single-meta">
							<?php echo implode( ' <span class="lunara-meta-sep" aria-hidden="true">·</span> ', $meta_parts ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
						<?php endif; ?>
					</div>
				</div>

				<?php if ( $has_thumb ) : ?>
					<div class="lunara-journal-cinematic-hero-frame">
						<figure class="lunara-journal-cinematic-hero-media">
							<?php
							echo get_the_post_thumbnail(
								$post_id,
								'full',
								array(
									'class'         => 'lunara-journal-cinematic-hero-image',
									'loading'       => 'eager',
									'fetchpriority' => 'high',
									'decoding'      => 'async',
									'sizes'         => '100vw',
								)
							);
							?>
						</figure>
					</div>
				<?php endif; ?>
			</section>

			<section class="lunara-journal-single-body lunara-review-single-body">
				<div class="lunara-review-single-body-grid">

					<div class="lunara-review-single-content">
						<?php the_content(); ?>

						<?php
						wp_link_pages(
							array(
								'before' => '<p class="lunara-journal-single-pages">' . esc_html__( 'Pages:', 'lunara-film' ),
								'after'  => '</p>',
							)
						);
						?>
					</div>

					<aside class="lunara-review-single-rail" aria-label="<?php esc_attr_e( 'Journal entry sidebar', 'lunara-film' ); ?>">
						<div class="lunara-review-single-rail-sticky">

							<?php
							$type_terms = get_the_terms( $post_id, 'journal_type' );
							if ( $type_terms && ! is_wp_error( $type_terms ) ) :
								?>
								<div class="lunara-journal-rail-card">
									<p class="lunara-journal-rail-card-label"><?php esc_html_e( 'Type', 'lunara-film' ); ?></p>
									<ul class="lunara-journal-rail-type-list">
										<?php foreach ( $type_terms as $term ) : ?>
											<li>
												<a href="<?php echo esc_url( get_term_link( $term ) ); ?>">
													<?php echo esc_html( $term->name ); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

							<?php
							$tags = get_the_tags( $post_id );
							if ( $tags && ! is_wp_error( $tags ) ) :
								?>
								<div class="lunara-journal-rail-card">
									<p class="lunara-journal-rail-card-label"><?php esc_html_e( 'Tagged', 'lunara-film' ); ?></p>
									<ul class="lunara-journal-rail-tag-list">
										<?php foreach ( $tags as $tag ) : ?>
											<li>
												<a href="<?php echo esc_url( get_term_link( $tag ) ); ?>">
													#<?php echo esc_html( $tag->name ); ?>
												</a>
											</li>
										<?php endforeach; ?>
									</ul>
								</div>
							<?php endif; ?>

							<?php if ( $archive_url ) : ?>
								<div class="lunara-review-single-rail-actions">
									<a class="lunara-btn lunara-btn-ghost" href="<?php echo esc_url( $archive_url ); ?>">
										<?php esc_html_e( 'All Journal Entries', 'lunara-film' ); ?>
									</a>
								</div>
							<?php endif; ?>

						</div>
					</aside>

				</div>
			</section>

			<?php
			// Related journal entries (most recent in same type, excluding current).
			$related_args = array(
				'post_type'           => 'journal',
				'posts_per_page'      => 3,
				'post__not_in'        => array( $post_id ),
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
			);

			if ( ! empty( $type_terms ) && ! is_wp_error( $type_terms ) ) {
				$related_args['tax_query'] = array(
					array(
						'taxonomy' => 'journal_type',
						'field'    => 'term_id',
						'terms'    => wp_list_pluck( $type_terms, 'term_id' ),
					),
				);
			}

			$related_query = new WP_Query( $related_args );

			if ( $related_query->have_posts() ) : ?>
				<section class="lunara-journal-single-related lunara-home-section">
					<div class="lunara-home-section-head">
						<p class="lunara-home-section-kicker"><?php esc_html_e( 'More from the journal', 'lunara-film' ); ?></p>
						<h2 class="lunara-home-section-title"><?php esc_html_e( 'Continue reading', 'lunara-film' ); ?></h2>
					</div>
					<div class="lunara-review-grid lunara-review-related-grid">
						<?php while ( $related_query->have_posts() ) : $related_query->the_post(); ?>
							<?php
							$rid     = get_the_ID();
							$rkicker = function_exists( 'lunara_get_journal_kicker' )
								? lunara_get_journal_kicker( $rid )
								: __( 'Journal', 'lunara-film' );
							?>
							<article class="lunara-review-grid-card lunara-journal-rail-card">
								<a class="lunara-review-grid-link" href="<?php the_permalink(); ?>">
									<?php if ( has_post_thumbnail() ) : ?>
										<div class="lunara-review-grid-poster-wrap">
											<?php the_post_thumbnail( 'medium_large', array( 'class' => 'lunara-review-grid-poster', 'loading' => 'lazy' ) ); ?>
										</div>
									<?php endif; ?>
									<div class="lunara-review-grid-copy">
										<p class="lunara-review-grid-kicker"><?php echo esc_html( $rkicker ); ?></p>
										<h3 class="lunara-review-grid-title"><?php the_title(); ?></h3>
										<p class="lunara-review-grid-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></p>
									</div>
								</a>
							</article>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</section>
			<?php endif; ?>

		</article>
		</main>

	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>
