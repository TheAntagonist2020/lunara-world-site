<?php
/**
 * Dedicated Reviews hub page.
 */

get_header();

$paged = max(
    1,
    intval( get_query_var( 'paged' ) ),
    intval( get_query_var( 'page' ) )
);

$reviews_query = new WP_Query(
    array(
        'post_type'              => 'review',
        'post_status'            => 'publish',
        'posts_per_page'         => 9,
        'paged'                  => $paged,
        'ignore_sticky_posts'    => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    )
);

$archive_kicker = function_exists( 'lunara_theme_mod_text' )
    ? lunara_theme_mod_text( 'lunara_reviews_archive_kicker', 'Review Archive' )
    : 'Review Archive';
$archive_title = function_exists( 'lunara_theme_mod_text' )
    ? lunara_theme_mod_text( 'lunara_reviews_archive_title', 'The Review Archive' )
    : 'The Review Archive';
$archive_copy = function_exists( 'lunara_theme_mod_text' )
    ? lunara_theme_mod_text( 'lunara_reviews_archive_copy', '' )
    : '';

$pagination = paginate_links(
    array(
        'total'   => max( 1, intval( $reviews_query->max_num_pages ) ),
        'current' => $paged,
    )
);

echo lunara_render_review_archive_shell(
    array(
        'classes'     => 'lunara-review-archive-page',
        'kicker'      => $archive_kicker,
        'title'       => $archive_title,
        'copy'        => $archive_copy,
        'posts'       => lunara_get_loop_posts( $reviews_query ),
        'empty_title' => __( 'No reviews yet.', 'lunara-film' ),
        'empty_copy'  => '',
        'pagination'  => $pagination,
    )
);

wp_reset_postdata();
get_footer();
