<?php
/**
 * Review archive template.
 */

get_header();

$archive_kicker = function_exists( 'lunara_theme_mod_text' )
    ? lunara_theme_mod_text( 'lunara_reviews_archive_kicker', 'Review Archive' )
    : 'Review Archive';
$archive_title = function_exists( 'lunara_theme_mod_text' )
    ? lunara_theme_mod_text( 'lunara_reviews_archive_title', 'The Review Archive' )
    : 'The Review Archive';
$archive_copy = function_exists( 'lunara_theme_mod_text' )
    ? lunara_theme_mod_text( 'lunara_reviews_archive_copy', '' )
    : '';

echo lunara_render_review_archive_shell(
    array(
        'classes'     => 'lunara-review-archive-page',
        'kicker'      => $archive_kicker,
        'title'       => $archive_title,
        'copy'        => $archive_copy,
        'posts'       => lunara_get_loop_posts(),
        'empty_title' => __( 'No reviews yet.', 'lunara-film' ),
        'empty_copy'  => '',
        'pagination'  => paginate_links(),
    )
);

get_footer();
