<?php
/**
 * Category archive template for Lunara editorial archives.
 */

get_header();

$term           = get_queried_object();
$archive_title  = $term instanceof WP_Term ? single_cat_title( '', false ) : __( 'Category Archive', 'lunara-film' );
$archive_copy   = '';
$archive_kicker = __( 'Category Archive', 'lunara-film' );
$source_label   = __( 'Category file', 'lunara-film' );
$run_title      = __( 'More From This Category', 'lunara-film' );
$run_copy       = __( 'The wider archive stays browseable, but the throughline here is the selected category and the signal it keeps collecting.', 'lunara-film' );
$rail_title     = __( 'What This Category Is Holding Beside The Lead', 'lunara-film' );
$rail_copy      = __( 'A tighter support stack so the category page feels like a deliberate desk, not a taxonomic leftover.', 'lunara-film' );

if ( $term instanceof WP_Term ) {
    $archive_copy = trim( wp_strip_all_tags( term_description( $term, 'category' ) ) );
    $source_label = $term->name;

    if ( lunara_is_editorial_category_term( $term ) ) {
        $archive_kicker = __( 'Lunara Journal', 'lunara-film' );
        $run_title      = sprintf(
            /* translators: %s: Category name. */
            __( 'More %s Signal', 'lunara-film' ),
            $term->name
        );
        $run_copy       = __( 'This category stays live and current, but now reads with the same controlled editorial rhythm as the rest of Lunara.', 'lunara-film' );
        $rail_title     = __( 'What This Editorial Lane Is Holding Beside The Lead', 'lunara-film' );
        $rail_copy      = __( 'A tighter support stack so the category reads like a living editorial lane instead of a filing cabinet.', 'lunara-film' );
    }
}

if ( '' === $archive_copy ) {
    if ( $term instanceof WP_Term && lunara_is_editorial_category_term( $term ) ) {
        $archive_copy = lunara_theme_mod_text(
            'lunara_journal_archive_copy',
            'This is the live editorial lane for news, quick reactions, longer think pieces, interviews, and podcast writing that should stand beside the reviews without being mistaken for them.'
        );
    } else {
        $archive_copy = __( 'This archive collects writing filed under the selected category.', 'lunara-film' );
    }
}

echo lunara_render_editorial_archive_shell(
    array(
        'classes'     => 'lunara-editorial-archive-page lunara-category-archive-page',
        'kicker'      => $archive_kicker,
        'title'       => $archive_title,
        'copy'        => $archive_copy,
        'posts'       => lunara_get_loop_posts(),
        'source_label'=> $source_label,
        'lead_rail_title' => $rail_title,
        'lead_rail_copy'  => $rail_copy,
        'run_title'       => $run_title,
        'run_copy'        => $run_copy,
        'empty_title' => __( 'Nothing has been filed in this archive yet.', 'lunara-film' ),
    )
);

get_footer();
