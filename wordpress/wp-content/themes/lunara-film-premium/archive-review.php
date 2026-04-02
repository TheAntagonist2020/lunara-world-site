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
    ? lunara_theme_mod_text( 'lunara_reviews_archive_copy', 'Poster-led criticism, cataloged so readers can move through the writing as an evolving record instead of a pile of disconnected posts.' )
    : 'Poster-led criticism, cataloged so readers can move through the writing as an evolving record instead of a pile of disconnected posts.';

echo lunara_render_review_archive_shell(
    array(
        'classes'     => 'lunara-review-archive-page',
        'kicker'      => $archive_kicker,
        'title'       => $archive_title,
        'copy'        => $archive_copy,
        'posts'       => lunara_get_loop_posts(),
        'empty_title' => __( 'No reviews yet.', 'lunara-film' ),
        'empty_copy'  => __( 'When new criticism is published, it will appear here automatically.', 'lunara-film' ),
        'pagination'  => paginate_links(),
    )
);

get_footer();
