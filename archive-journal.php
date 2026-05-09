<?php
/**
 * Journal Archive — Lunara Film
 *
 * Renders /journal/ (the journal CPT archive) as a deliberate Lunara lane:
 * type filters, lead entry, supporting grid.  Without this template, the site
 * falls through to archive.php which expects standard WordPress posts.
 *
 * @package Lunara_Film
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$kicker      = function_exists( 'lunara_theme_mod_text' )
	? lunara_theme_mod_text( 'lunara_journal_archive_kicker', __( 'The Lunara Journal', 'lunara-film' ) )
	: __( 'The Lunara Journal', 'lunara-film' );
$title       = function_exists( 'lunara_theme_mod_text' )
	? lunara_theme_mod_text( 'lunara_journal_archive_title', __( 'Journal', 'lunara-film' ) )
	: __( 'Journal', 'lunara-film' );
$copy        = function_exists( 'lunara_theme_mod_text' )
	? lunara_theme_mod_text( 'lunara_journal_archive_copy', __( 'News, reactions, essays, podcasts, and dispatches — the live editorial lane that runs alongside the reviews.', 'lunara-film' ) )
	: __( 'News, reactions, essays, podcasts, and dispatches — the live editorial lane that runs alongside the reviews.', 'lunara-film' );

$show_hero       = function_exists( 'lunara_journal_archive_section_is_enabled' ) ? lunara_journal_archive_section_is_enabled( 'hero' ) : true;
$show_filters    = function_exists( 'lunara_journal_archive_section_is_enabled' ) ? lunara_journal_archive_section_is_enabled( 'filters' ) : true;
$show_grid       = function_exists( 'lunara_journal_archive_section_is_enabled' ) ? lunara_journal_archive_section_is_enabled( 'grid' ) : true;
$show_pagination = function_exists( 'lunara_journal_archive_section_is_enabled' ) ? lunara_journal_archive_section_is_enabled( 'pagination' ) : true;
$current_sort    = function_exists( 'lunara_get_editorial_archive_sort' ) ? lunara_get_editorial_archive_sort() : 'date_desc';
$sort_options    = function_exists( 'lunara_get_editorial_archive_sort_options' ) ? lunara_get_editorial_archive_sort_options() : array();
$sort_base_url   = remove_query_arg( array( 'sort', 'paged' ), get_pagenum_link( 1 ) );

// Build a type-filter row from journal_type terms.
$type_terms = get_terms( array(
	'taxonomy'   => 'journal_type',
	'hide_empty' => true,
	'orderby'    => 'count',
	'order'      => 'DESC',
) );
?>

<main class="lunara-archive-page lunara-journal-archive-page">

	<?php if ( $show_hero ) : ?>
	<header class="lunara-archive-hero lunara-journal-archive-hero lunara-journal-archive-slot-hero">
		<p class="lunara-archive-hero-kicker"><?php echo esc_html( $kicker ); ?></p>
		<h1 class="lunara-archive-hero-title"><?php echo esc_html( $title ); ?></h1>
		<?php if ( '' !== $copy ) : ?>
			<p class="lunara-archive-hero-copy"><?php echo esc_html( $copy ); ?></p>
		<?php endif; ?>

	</header>
	<?php endif; ?>

	<?php if ( $show_filters && $type_terms && ! is_wp_error( $type_terms ) ) : ?>
		<nav class="lunara-journal-archive-filters lunara-journal-archive-slot-filters" aria-label="<?php esc_attr_e( 'Filter by type', 'lunara-film' ); ?>">
			<a class="lunara-journal-filter-pill <?php echo is_post_type_archive( 'journal' ) ? 'is-active' : ''; ?>"
			   href="<?php echo esc_url( get_post_type_archive_link( 'journal' ) ); ?>">
				<?php esc_html_e( 'All', 'lunara-film' ); ?>
			</a>
			<?php
			$current_term = is_tax( 'journal_type' ) ? get_queried_object() : null;
			foreach ( $type_terms as $term ) :
				$is_active = $current_term && $current_term->term_id === $term->term_id;
				?>
				<a class="lunara-journal-filter-pill <?php echo $is_active ? 'is-active' : ''; ?>"
				   href="<?php echo esc_url( get_term_link( $term ) ); ?>">
					<?php echo esc_html( $term->name ); ?>
					<span class="lunara-journal-filter-count">(<?php echo intval( $term->count ); ?>)</span>
				</a>
			<?php endforeach; ?>
		</nav>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>

		<?php if ( ! empty( $sort_options ) ) : ?>
		<div class="lunara-editorial-archive-toolbar lunara-journal-archive-toolbar">
			<div class="lunara-home-section-head lunara-editorial-archive-toolbar-head">
				<div>
					<p class="lunara-home-section-kicker"><?php esc_html_e( 'Archive Order', 'lunara-film' ); ?></p>
					<h2 class="lunara-section-title"><?php esc_html_e( 'Filed Chronology Or Real Editing Activity', 'lunara-film' ); ?></h2>
				</div>
			</div>
			<div class="lunara-archive-sort" aria-label="<?php esc_attr_e( 'Sort journal archive', 'lunara-film' ); ?>">
				<?php foreach ( $sort_options as $sort_key => $sort_label ) : ?>
					<?php
					$is_active = $sort_key === $current_sort;
					$sort_url  = 'date_desc' === $sort_key ? $sort_base_url : add_query_arg( 'sort', rawurlencode( $sort_key ), $sort_base_url );
					?>
					<a class="lunara-archive-sort-link <?php echo $is_active ? 'is-active' : ''; ?>" href="<?php echo esc_url( $sort_url ); ?>"<?php echo $is_active ? ' aria-current="page"' : ''; ?>>
						<?php echo esc_html( $sort_label ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php if ( $show_grid ) : ?>
		<section class="lunara-journal-archive-grid lunara-review-grid lunara-review-archive-uniform lunara-journal-archive-slot-grid">
			<?php while ( have_posts() ) : the_post();
				$pid          = get_the_ID();
				$entry_kicker = function_exists( 'lunara_get_journal_kicker' )
					? lunara_get_journal_kicker( $pid )
					: __( 'Journal', 'lunara-film' );
				$entry_excerpt = has_excerpt( $pid )
					? wp_trim_words( get_the_excerpt( $pid ), 28 )
					: wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $pid ) ), 28 );
				$updated_label = function_exists( 'lunara_get_editorial_card_updated_label' )
					? lunara_get_editorial_card_updated_label( $pid )
					: '';
				?>
				<article class="lunara-review-grid-card lunara-journal-archive-card">
					<a class="lunara-review-grid-link" href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="lunara-review-grid-poster-wrap">
								<?php the_post_thumbnail( 'medium_large', array( 'class' => 'lunara-review-grid-poster', 'loading' => 'lazy' ) ); ?>
							</div>
						<?php else : ?>
							<div class="lunara-review-grid-poster-wrap">
								<div class="lunara-review-grid-poster-placeholder" aria-hidden="true"></div>
							</div>
						<?php endif; ?>
						<div class="lunara-review-grid-copy">
							<p class="lunara-review-grid-kicker"><?php echo esc_html( $entry_kicker ); ?></p>
							<h3 class="lunara-review-grid-title"><?php the_title(); ?></h3>
							<?php if ( $entry_excerpt ) : ?>
								<p class="lunara-review-grid-excerpt"><?php echo esc_html( $entry_excerpt ); ?></p>
							<?php endif; ?>
							<div class="lunara-review-grid-footer">
								<span class="lunara-review-grid-meta"><?php echo esc_html( get_the_date( 'F j, Y' ) ); ?></span>
								<?php if ( '' !== $updated_label ) : ?>
									<span class="lunara-review-grid-updated"><?php echo esc_html( $updated_label ); ?></span>
								<?php endif; ?>
							</div>
						</div>
					</a>
				</article>
			<?php endwhile; ?>
		</section>
		<?php endif; ?>

		<?php if ( $show_pagination ) : ?>
		<nav class="lunara-archive-pagination lunara-journal-archive-slot-pagination" aria-label="<?php esc_attr_e( 'Journal pagination', 'lunara-film' ); ?>">
			<?php
			the_posts_pagination( array(
				'mid_size'  => 1,
				'add_args'  => 'date_desc' === $current_sort ? false : array( 'sort' => $current_sort ),
				'prev_text' => __( '← Newer', 'lunara-film' ),
				'next_text' => __( 'Older →', 'lunara-film' ),
			) );
			?>
		</nav>
		<?php endif; ?>

	<?php else : ?>

		<div class="lunara-archive-empty lunara-journal-archive-slot-grid">
			<p><?php esc_html_e( 'No journal entries yet. Check back soon.', 'lunara-film' ); ?></p>
		</div>

	<?php endif; ?>

</main>

<?php
get_footer();
