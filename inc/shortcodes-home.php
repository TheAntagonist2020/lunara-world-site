<?php
/**
 * Legacy homepage shortcodes.
 *
 * These remain available for older page content, but the canonical live
 * homepage path is now the section-based front-page template.
 *
 * @package Lunara_Film
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Shortcode: Homepage Content
 */
function lunara_home_shortcode() {
    ob_start();
    ?>
    <?php echo do_shortcode('[lunara_carousel set="homepage"]'); ?>

    <div class="lunara-tagline">
        <p class="lunara-tagline-text">Film criticism and the living record of the Oscars.</p>
    </div>

    <section class="lunara-section">
        <div class="lunara-section-header">
            <h2 class="lunara-section-title">Latest Reviews</h2>
        </div>
        <?php echo do_shortcode('[lunara_reviews count="3"]'); ?>
        <div class="text-center" style="margin-top: 30px;">
            <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" class="lunara-btn">View All Reviews</a>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'lunara_home', 'lunara_home_shortcode' );

/**
 * Shortcode: Display Reviews
 */
function lunara_reviews_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'count' => 6 ), $atts );
    $count = intval( $atts['count'] );
    if ( $count === 0 ) { $count = 6; }

    $query = new WP_Query( array(
        'post_type'      => 'review',
        'posts_per_page' => $count < 0 ? -1 : $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'ignore_sticky_posts' => true,
    ) );

    if ( ! $query->have_posts() ) {
        return '<p style="text-align:center;color:#888;">No reviews yet.</p>';
    }

    ob_start();
    echo '<div class="lunara-review-grid lunara-review-archive-grid">';
    while ( $query->have_posts() ) {
        $query->the_post();
        $score = get_post_meta( get_the_ID(), '_lunara_score', true );
        $year  = get_post_meta( get_the_ID(), '_lunara_year', true );
        $director = get_post_meta( get_the_ID(), '_lunara_director', true );
        ?>
        <article class="lunara-review-grid-card lunara-review-archive-card">
            <a class="lunara-review-grid-link" href="<?php the_permalink(); ?>">
                <div class="lunara-review-grid-poster-wrap">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium_large', array( 'class' => 'lunara-review-grid-poster', 'loading' => 'lazy' ) ); ?>
                    <?php endif; ?>
                    <?php if ( $score ) : ?><span class="lunara-score-badge"><?php echo wp_kses_post( lunara_render_stars( $score ) ); ?></span><?php endif; ?>
                </div>
                <div class="lunara-review-grid-copy">
                    <h3 class="lunara-review-grid-title"><?php the_title(); ?></h3>
                    <p class="lunara-review-grid-meta"><?php echo esc_html( $year ); ?><?php if ( $director ) : ?> · <?php echo esc_html( $director ); ?><?php endif; ?></p>
                </div>
            </a>
        </article>
        <?php
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'lunara_reviews', 'lunara_reviews_shortcode' );

/**
 * Shortcode: Display Posts by Category
 */
function lunara_posts_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'category' => '',
        'count'    => 6
    ), $atts );

    $query = new WP_Query( array(
        'post_type'      => 'post',
        'category_name'  => sanitize_text_field( $atts['category'] ),
        'posts_per_page' => intval( $atts['count'] ),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
        'ignore_sticky_posts' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );

    if ( ! $query->have_posts() ) {
        return '<p style="text-align:center;color:#888;">No posts found.</p>';
    }

    ob_start();
    echo '<div class="lunara-grid">';
    while ( $query->have_posts() ) {
        $query->the_post();
        ?>
        <article class="lunara-card">
            <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'medium', array( 'class' => 'lunara-card-thumb' ) ); ?>
                </a>
            <?php endif; ?>
            <h3 class="lunara-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <div class="lunara-card-meta"><?php echo get_the_date( 'F j, Y' ); ?></div>
            <div class="lunara-card-excerpt"><?php the_excerpt(); ?></div>
            <a href="<?php the_permalink(); ?>" class="lunara-btn">Read More</a>
        </article>
        <?php
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'lunara_posts', 'lunara_posts_shortcode' );
