<?php
/**
 * Single review template for Lunara criticism.
 */

if ( 'review' !== get_post_type() ) {
    $fallback = locate_template(
        array(
            'single-' . get_post_type() . '.php',
            'singular.php',
            'index.php',
        ),
        false,
        false
    );

    if ( $fallback ) {
        include $fallback;
        return;
    }
}

if ( function_exists( 'lunara_prepend_review_metadata' ) ) {
    remove_filter( 'the_content', 'lunara_prepend_review_metadata', 5 );
}

get_header();

if ( have_posts() ) :
    while ( have_posts() ) :
        the_post();

        $post_id          = get_the_ID();
        $score            = trim( (string) get_post_meta( $post_id, '_lunara_score', true ) );
        $year             = trim( (string) get_post_meta( $post_id, '_lunara_year', true ) );
        $director         = trim( (string) get_post_meta( $post_id, '_lunara_director', true ) );
        $runtime          = trim( (string) get_post_meta( $post_id, '_lunara_runtime', true ) );
        $studio           = trim( (string) get_post_meta( $post_id, '_lunara_studio', true ) );
        $where            = trim( (string) get_post_meta( $post_id, '_lunara_where', true ) );
        $review_meta_line = lunara_get_review_card_meta( $post_id );
        $excerpt          = has_excerpt( $post_id ) ? get_the_excerpt() : lunara_card_excerpt( $post_id, 42 );
        $review_tt        = function_exists( 'lunara_get_review_imdb_title_id' ) ? lunara_get_review_imdb_title_id( $post_id ) : '';
        $ledger_counts    = '' !== $review_tt ? lunara_get_oscar_ledger_counts( $review_tt ) : array();
        $ledger_pill      = '' !== $review_tt ? lunara_render_oscar_ledger_pill( $review_tt, $ledger_counts ) : '';
        $debrief_block    = do_shortcode( '[lunara_debrief]' );
        $related_query    = function_exists( 'lunara_get_related_review_posts' ) ? lunara_get_related_review_posts( $post_id, 4 ) : null;
        $archive_url      = get_post_type_archive_link( 'review' );
        $archive_url      = is_string( $archive_url ) && '' !== $archive_url ? $archive_url : home_url( '/reviews/' );
        $director_url     = '';

        if ( '' !== $director ) {
            $director_term = get_term_by( 'name', $director, 'lunara_director' );
            if ( $director_term instanceof WP_Term ) {
                $term_link = get_term_link( $director_term );
                if ( ! is_wp_error( $term_link ) && is_string( $term_link ) && '' !== $term_link ) {
                    $director_url = $term_link;
                }
            }
        }

        $detail_items = array_filter(
            array(
                'Year'      => $year,
                'Director'  => $director,
                'Runtime'   => $runtime,
                'Studio'    => $studio,
                'Published' => get_the_date( 'F j, Y', $post_id ),
            ),
            static function( $value ) {
                return '' !== trim( (string) $value );
            }
        );
        ?>
        <main id="primary" class="site-main lunara-archive-page lunara-review-single-page">
            <article <?php post_class( 'lunara-journal-single lunara-review-single' ); ?>>
                <section class="lunara-home-section lunara-journal-single-hero lunara-review-single-hero">
                    <div class="lunara-journal-single-grid lunara-review-single-grid">
                        <div class="lunara-journal-single-copy lunara-review-single-copy">
                            <p class="lunara-archive-hero-kicker"><?php esc_html_e( 'Lunara Review', 'lunara-film' ); ?></p>
                            <h1 class="lunara-journal-single-title lunara-review-single-title"><?php the_title(); ?></h1>

                            <?php if ( '' !== trim( $excerpt ) ) : ?>
                                <p class="lunara-journal-single-excerpt lunara-review-single-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                            <?php endif; ?>

                            <div class="lunara-journal-single-meta lunara-review-single-meta">
                                <?php if ( '' !== $review_meta_line ) : ?>
                                    <span><?php echo esc_html( $review_meta_line ); ?></span>
                                <?php endif; ?>
                                <span><?php echo esc_html( get_the_date( 'F j, Y', $post_id ) ); ?></span>
                                <?php if ( '' !== $score ) : ?>
                                    <span><?php echo wp_kses_post( lunara_render_stars( $score ) ); ?></span>
                                <?php endif; ?>
                            </div>

                            <div class="lunara-review-single-hero-tools">
                                <?php if ( '' !== $ledger_pill ) : ?>
                                    <?php echo wp_kses_post( $ledger_pill ); ?>
                                <?php endif; ?>

                                <?php if ( '' !== $where ) : ?>
                                    <div class="lunara-review-single-where">
                                        <strong><?php esc_html_e( 'Where to watch', 'lunara-film' ); ?></strong>
                                        <span><?php echo esc_html( $where ); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="lunara-review-single-actions">
                                <a class="lunara-btn lunara-btn-primary" href="<?php echo esc_url( $archive_url ); ?>">
                                    <?php esc_html_e( 'Browse Reviews', 'lunara-film' ); ?>
                                </a>
                                <?php if ( '' !== $director_url ) : ?>
                                    <a class="lunara-btn lunara-btn-secondary" href="<?php echo esc_url( $director_url ); ?>">
                                        <?php esc_html_e( 'Director Archive', 'lunara-film' ); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="lunara-journal-single-media lunara-review-single-media">
                            <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                                <?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'lunara-journal-single-image lunara-review-single-image', 'loading' => 'eager' ) ); ?>
                            <?php else : ?>
                                <div class="lunara-journal-single-placeholder lunara-review-single-placeholder"><?php the_title(); ?></div>
                            <?php endif; ?>

                            <?php if ( '' !== $score ) : ?>
                                <span class="lunara-score-badge lunara-review-single-score"><?php echo wp_kses_post( lunara_render_stars( $score ) ); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </section>

                <section class="lunara-home-section lunara-journal-single-body lunara-review-single-body">
                    <div class="lunara-journal-single-body-grid lunara-review-single-body-grid">
                        <div class="lunara-journal-single-content lunara-review-single-content">
                            <?php the_content(); ?>
                        </div>

                        <aside class="lunara-journal-single-rail lunara-review-single-rail" aria-label="<?php esc_attr_e( 'Review details', 'lunara-film' ); ?>">
                            <?php if ( ! empty( $detail_items ) ) : ?>
                                <div class="lunara-journal-rail-card lunara-review-single-details">
                                    <p class="lunara-home-section-kicker"><?php esc_html_e( 'Review Details', 'lunara-film' ); ?></p>
                                    <ul class="lunara-journal-rail-meta lunara-review-single-detail-list">
                                        <?php foreach ( $detail_items as $label => $value ) : ?>
                                            <li>
                                                <strong><?php echo esc_html( $label ); ?></strong>
                                                <span><?php echo esc_html( $value ); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if ( '' !== trim( wp_strip_all_tags( $debrief_block ) ) ) : ?>
                                <div class="lunara-review-single-debrief">
                                    <?php echo $debrief_block; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                            <?php endif; ?>
                        </aside>
                    </div>
                </section>

                <?php if ( $related_query instanceof WP_Query && $related_query->have_posts() ) : ?>
                    <section class="lunara-home-section lunara-review-related">
                        <div class="lunara-home-section-head">
                            <div>
                                <p class="lunara-home-section-kicker"><?php esc_html_e( 'Continue Watching', 'lunara-film' ); ?></p>
                                <h2 class="lunara-section-title"><?php esc_html_e( 'More Lunara Criticism', 'lunara-film' ); ?></h2>
                            </div>
                            <a class="lunara-section-link" href="<?php echo esc_url( $archive_url ); ?>">
                                <?php esc_html_e( 'Open Reviews', 'lunara-film' ); ?>
                            </a>
                        </div>

                        <div class="lunara-review-grid lunara-review-related-grid">
                            <?php
                            while ( $related_query->have_posts() ) :
                                $related_query->the_post();
                                echo lunara_render_review_grid_card( get_the_ID() );
                            endwhile;
                            wp_reset_postdata();
                            ?>
                        </div>
                    </section>
                <?php endif; ?>
            </article>
        </main>
        <?php
    endwhile;
endif;

get_footer();
