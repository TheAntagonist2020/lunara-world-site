<?php
/**
 * Dedicated Journal page template on the /news/ route.
 */

get_header();

$paged = max(
    1,
    intval( get_query_var( 'paged' ) ),
    intval( get_query_var( 'page' ) )
);

$news_query = new WP_Query(
    array(
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => 9,
        'paged'                  => $paged,
        'ignore_sticky_posts'    => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
        'tax_query'              => array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => array( 'news' ),
                'operator' => 'IN',
            ),
        ),
    )
);

$news_page      = get_queried_object();
$archive_kicker = __( 'Lunara Journal', 'lunara-film' );
$archive_title  = $news_page instanceof WP_Post ? get_the_title( $news_page ) : __( 'Journal', 'lunara-film' );

if ( 'News' === trim( (string) $archive_title ) ) {
    $archive_title = __( 'Journal', 'lunara-film' );
}
$archive_copy   = $news_page instanceof WP_Post && function_exists( 'lunara_get_archive_intro_from_post' )
    ? lunara_get_archive_intro_from_post( $news_page )
    : '';

if ( '' === $archive_copy ) {
    $archive_copy = lunara_theme_mod_text(
        'lunara_journal_archive_copy',
        'This is the live editorial lane for news, quick reactions, longer think pieces, interviews, and podcast writing that should stand beside the reviews without being mistaken for them.'
    );
}

$pagination = paginate_links(
    array(
        'total'   => max( 1, intval( $news_query->max_num_pages ) ),
        'current' => $paged,
    )
);

echo lunara_render_news_archive_shell(
    array(
        'classes'     => 'lunara-editorial-archive-page lunara-news-archive-page',
        'kicker'      => $archive_kicker,
        'title'       => $archive_title,
        'copy'        => $archive_copy,
        'posts'       => lunara_get_loop_posts( $news_query ),
        'empty_title' => __( 'The desk is on standby, not off.', 'lunara-film' ),
        'empty_copy'  => __( 'When the next dispatch lands, it will appear here. Until then, the rest of Lunara is still moving.', 'lunara-film' ),
        'pagination'  => $pagination,
        'source_label'=> __( 'Breaking / Industry / Festival', 'lunara-film' ),
    )
);

wp_reset_postdata();
get_footer();
