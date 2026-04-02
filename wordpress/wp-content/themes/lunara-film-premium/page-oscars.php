<?php
/**
 * Dedicated Oscars landing page.
 */

get_header();

the_post();

$aat                = function_exists( 'lunara_get_oscars_plugin' ) ? lunara_get_oscars_plugin() : null;
$snapshot           = function_exists( 'lunara_get_home_oscars_snapshot' ) ? lunara_get_home_oscars_snapshot() : array();
$database_spotlight = function_exists( 'lunara_get_home_database_spotlight' ) ? lunara_get_home_database_spotlight() : array();
$deep_cuts          = function_exists( 'lunara_get_home_deep_cuts' ) ? lunara_get_home_deep_cuts() : array();
$linked_reviews     = function_exists( 'lunara_oscars_linked_reviews_query' ) ? lunara_oscars_linked_reviews_query( 4 ) : new WP_Query();

$hero_kicker       = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_oscars_portal_kicker', 'The Lunara Oscar Ledger' ) : 'The Lunara Oscar Ledger';
$hero_title        = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_oscars_portal_title', 'Academy Awards history, treated like a living editorial system.' ) : 'Academy Awards history, treated like a living editorial system.';
$hero_copy         = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_oscars_portal_copy', 'Move from a winning film to the people behind it, from one category to the ceremony around it, and from the ledger straight into Lunara criticism without ever hitting a dead wall of data.' ) : 'Move from a winning film to the people behind it, from one category to the ceremony around it, and from the ledger straight into Lunara criticism without ever hitting a dead wall of data.';
$explore_heading   = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_oscars_portal_explore_heading', 'Start anywhere in the ledger.' ) : 'Start anywhere in the ledger.';
$reviews_heading   = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_oscars_portal_reviews_heading', 'Reviews Inside the Ledger' ) : 'Reviews Inside the Ledger';
$deep_cuts_heading = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_oscars_portal_deep_cuts_heading', 'Oscar Deep Cuts' ) : 'Oscar Deep Cuts';

$best_picture      = is_array( $snapshot['best_picture'] ?? null ) ? $snapshot['best_picture'] : array();
$best_visual       = is_array( $best_picture['visual'] ?? null ) ? $best_picture['visual'] : array();
$spotlights        = array_slice( (array) ( $snapshot['spotlights'] ?? array() ), 0, 6 );
$title_cards       = array_slice( (array) ( $database_spotlight['cards'] ?? array() ), 0, 5 );

$database_url      = trim( (string) ( $snapshot['database_url'] ?? ( $database_spotlight['database_url'] ?? home_url( '/oscars/' ) ) ) );
$categories_url    = trim( (string) ( $snapshot['categories_url'] ?? ( $database_spotlight['categories_url'] ?? home_url( '/oscars/categories/' ) ) ) );
$ceremony_url      = trim( (string) ( $snapshot['ceremony_url'] ?? home_url( '/oscars/ceremony/' ) ) );
$ceremony_label    = trim( (string) ( $snapshot['ceremony_label'] ?? 'Latest Ceremony' ) );
$year_label        = trim( (string) ( $snapshot['year_label'] ?? '' ) );
$about_url         = ( $aat && method_exists( $aat, 'get_about_url' ) ) ? $aat->get_about_url() : home_url( '/oscars/about/' );
$ceremonies_url    = ( $aat && method_exists( $aat, 'get_ceremonies_index_url' ) ) ? $aat->get_ceremonies_index_url() : home_url( '/oscars/ceremonies/' );
$hero_backdrop_url = trim( (string) ( $best_visual['backdrop_url'] ?? '' ) );
$hero_style        = '';

if ( '' !== $hero_backdrop_url ) {
    $hero_style = "background-image: linear-gradient(120deg, rgba(7,16,27,.92) 0%, rgba(7,16,27,.86) 48%, rgba(7,16,27,.97) 100%), url('" . esc_url( $hero_backdrop_url ) . "'); background-size: cover; background-position: center;";
}

$hero_title_card = array();
if ( ! empty( $best_picture ) ) {
    $hero_title_card = array(
        'title'     => trim( (string) ( $best_picture['film'] ?? '' ) ),
        'url'       => trim( (string) ( $best_picture['film_url'] ?? $ceremony_url ) ),
        'visual'    => $best_visual,
        'eyebrow'   => 'Latest Best Picture',
        'meta_line' => trim( (string) $ceremony_label . ( $year_label !== '' ? ' / ' . $year_label : '' ) ),
        'body'      => trim( (string) ( $snapshot['summary'] ?? $snapshot['winner_record'] ?? '' ) ),
    );
} elseif ( ! empty( $title_cards[0] ) ) {
    $hero_title_card = array(
        'title'     => trim( (string) ( $title_cards[0]['title'] ?? '' ) ),
        'url'       => trim( (string) ( $title_cards[0]['url'] ?? $database_url ) ),
        'visual'    => is_array( $title_cards[0]['visual'] ?? null ) ? $title_cards[0]['visual'] : array(),
        'eyebrow'   => 'Featured Portal Entry',
        'meta_line' => trim( (string) ( $title_cards[0]['categories_line'] ?? '' ) ),
        'body'      => 'Open a poster-led entry point into the ledger and move outward into categories, ceremonies, and related people pages.',
    );
}

$portal_stats = array(
    array(
        'label' => 'Ceremony',
        'value' => $ceremony_label,
    ),
    array(
        'label' => 'Year',
        'value' => $year_label !== '' ? $year_label : 'Live',
    ),
    array(
        'label' => 'Rows',
        'value' => number_format_i18n( intval( $database_spotlight['records_total'] ?? 0 ) ),
    ),
    array(
        'label' => 'Categories',
        'value' => number_format_i18n( intval( $database_spotlight['categories_total'] ?? 0 ) ),
    ),
);

$portal_links = array(
    array(
        'kicker' => 'Ceremonies',
        'title'  => 'Move through the awards one ceremony at a time.',
        'copy'   => 'Jump into the latest winners, older races, and the shape of each year.',
        'url'    => $ceremonies_url,
    ),
    array(
        'kicker' => 'Categories',
        'title'  => 'Track the history of every Oscar category.',
        'copy'   => 'Go from Best Picture to supporting races, crafts, and international wins.',
        'url'    => $categories_url,
    ),
    array(
        'kicker' => 'Ledger',
        'title'  => 'Search the full ledger directly.',
        'copy'   => 'Use the full ledger when you want filters, tables, and the entire record at once.',
        'url'    => $database_url,
    ),
    array(
        'kicker' => 'About',
        'title'  => 'See how Lunara structures the archive.',
        'copy'   => 'Read the editorial and data philosophy behind the Oscar ledger.',
        'url'    => $about_url,
    ),
);
?>
<main id="primary" class="site-main lunara-oscars-portal">
    <?php if ( empty( $snapshot ) && empty( $database_spotlight ) ) : ?>
        <section class="lunara-home-section lunara-archive-hero">
            <p class="lunara-archive-hero-kicker"><?php echo esc_html( $hero_kicker ); ?></p>
            <h1 class="lunara-archive-hero-title"><?php echo esc_html( get_the_title() ); ?></h1>
            <p class="lunara-archive-hero-copy"><?php esc_html_e( 'The Oscars portal is ready for data, but the ledger data layer is not currently returning any live material.', 'lunara-film' ); ?></p>
        </section>
        <section class="lunara-home-section">
            <div class="lunara-journal-single-content">
                <?php the_content(); ?>
            </div>
        </section>
    <?php else : ?>
        <section class="lunara-home-section lunara-oscars-portal-hero"<?php if ( '' !== $hero_style ) : ?> style="<?php echo esc_attr( $hero_style ); ?>"<?php endif; ?>>
            <div class="lunara-oscars-portal-hero-grid">
                <div class="lunara-oscars-portal-copy">
                    <p class="lunara-home-section-kicker"><?php echo esc_html( $hero_kicker ); ?></p>
                    <h1 class="lunara-home-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
                    <p class="lunara-home-hero-copy"><?php echo esc_html( $hero_copy ); ?></p>

                    <div class="lunara-oscars-portal-actions">
                        <a class="lunara-button lunara-button-primary" href="<?php echo esc_url( $ceremony_url ); ?>">Latest Ceremony</a>
                        <a class="lunara-button lunara-button-secondary" href="<?php echo esc_url( $database_url ); ?>">Open Full Ledger</a>
                        <a class="lunara-button-ghost" href="<?php echo esc_url( $categories_url ); ?>">Browse Categories</a>
                    </div>

                    <div class="lunara-oscars-portal-stat-grid">
                        <?php foreach ( $portal_stats as $stat ) : ?>
                            <div class="lunara-oscars-portal-stat">
                                <span class="lunara-oscars-portal-stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
                                <strong class="lunara-oscars-portal-stat-value"><?php echo esc_html( $stat['value'] ); ?></strong>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php if ( ! empty( $hero_title_card ) ) : ?>
                    <a class="lunara-oscars-portal-feature-card" href="<?php echo esc_url( $hero_title_card['url'] ?? $database_url ); ?>">
                        <div class="lunara-oscars-portal-feature-poster">
                            <?php if ( ! empty( $hero_title_card['visual']['poster_html'] ) ) : ?>
                                <?php echo $hero_title_card['visual']['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php elseif ( ! empty( $hero_title_card['visual']['poster_url'] ) ) : ?>
                                <img src="<?php echo esc_url( $hero_title_card['visual']['poster_url'] ); ?>" alt="<?php echo esc_attr( $hero_title_card['title'] ); ?>" loading="lazy" decoding="async" />
                            <?php elseif ( ! empty( $hero_title_card['visual']['card_fallback_html'] ) ) : ?>
                                <?php echo $hero_title_card['visual']['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php else : ?>
                                <div class="aat-filmography-poster-placeholder"><div class="aat-fallback-inner"><div class="aat-fallback-kicker">Oscar Portal</div><div class="aat-fallback-title small"><?php echo esc_html( $hero_title_card['title'] ); ?></div></div></div>
                            <?php endif; ?>
                        </div>
                        <div class="lunara-oscars-portal-feature-copy">
                            <p class="lunara-oscars-portal-feature-kicker"><?php echo esc_html( $hero_title_card['eyebrow'] ); ?></p>
                            <h2><?php echo esc_html( $hero_title_card['title'] ); ?></h2>
                            <?php if ( '' !== trim( (string) $hero_title_card['meta_line'] ) ) : ?>
                                <p class="lunara-oscars-portal-feature-meta"><?php echo esc_html( $hero_title_card['meta_line'] ); ?></p>
                            <?php endif; ?>
                            <?php if ( '' !== trim( (string) $hero_title_card['body'] ) ) : ?>
                                <p class="lunara-oscars-portal-feature-body"><?php echo esc_html( $hero_title_card['body'] ); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <section class="lunara-home-section lunara-oscars-portal-links-section">
            <div class="lunara-home-section-header">
                <div>
                    <p class="lunara-home-section-kicker">Explore the Portal</p>
                    <h2 class="lunara-home-section-title"><?php echo esc_html( $explore_heading ); ?></h2>
                </div>
                    <p class="lunara-home-section-summary"><?php echo esc_html( $snapshot['winner_record'] ?? 'The Oscars portal now points readers into ceremonies, categories, and title pages instead of leaving the ledger isolated from the rest of Lunara.' ); ?></p>
            </div>

            <div class="lunara-oscars-portal-link-grid">
                <?php foreach ( $portal_links as $portal_link ) : ?>
                    <a class="lunara-oscars-portal-link-card" href="<?php echo esc_url( $portal_link['url'] ); ?>">
                        <p class="lunara-oscars-portal-link-kicker"><?php echo esc_html( $portal_link['kicker'] ); ?></p>
                        <h3><?php echo esc_html( $portal_link['title'] ); ?></h3>
                        <p><?php echo esc_html( $portal_link['copy'] ); ?></p>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <?php if ( ! empty( $spotlights ) ) : ?>
            <section class="lunara-home-section lunara-oscars-portal-spotlights">
                <div class="lunara-home-section-header">
                    <div>
                        <p class="lunara-home-section-kicker"><?php echo esc_html( $ceremony_label ); ?></p>
                        <h2 class="lunara-home-section-title">Latest Ceremony, category by category.</h2>
                    </div>
                    <p class="lunara-home-section-summary"><?php echo esc_html( $snapshot['summary'] ?? 'The latest winners now connect directly to film pages, category pages, and the wider Lunara archive.' ); ?></p>
                </div>

                <div class="lunara-oscars-portal-spotlight-grid">
                    <?php foreach ( $spotlights as $spotlight ) :
                        $sl_visual = is_array( $spotlight['visual'] ?? null ) ? $spotlight['visual'] : array();
                        $sl_winner = intval( $spotlight['winner'] ?? 0 );
                    ?>
                        <a class="lunara-oscars-portal-spotlight-card<?php echo $sl_winner ? ' is-winner' : ''; ?>" href="<?php echo esc_url( $spotlight['url'] ?? $database_url ); ?>">
                            <?php if ( ! empty( $sl_visual['poster_html'] ) ) : ?>
                                <div class="lunara-oscars-spotlight-poster">
                                    <?php echo $sl_visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                </div>
                            <?php elseif ( ! empty( $sl_visual['poster_url'] ) ) : ?>
                                <div class="lunara-oscars-spotlight-poster">
                                    <img src="<?php echo esc_url( $sl_visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $spotlight['primary_label'] ?? '' ); ?>" loading="lazy" decoding="async" />
                                </div>
                            <?php else : ?>
                                <div class="lunara-oscars-spotlight-poster lunara-oscars-spotlight-poster--fallback">
                                    <span><?php echo esc_html( $spotlight['primary_label'] ?? '' ); ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="lunara-oscars-spotlight-card-copy">
                                <p class="lunara-oscars-portal-spotlight-category"><?php echo esc_html( $spotlight['category_label'] ?? 'Category' ); ?></p>
                                <h3><?php echo esc_html( $spotlight['primary_label'] ?? $spotlight['film'] ?? '' ); ?></h3>
                                <?php if ( ! empty( $spotlight['secondary_label'] ) ) : ?>
                                    <p class="lunara-oscars-portal-spotlight-secondary"><?php echo esc_html( $spotlight['secondary_label'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $spotlight['year'] ) ) : ?>
                                    <p class="lunara-oscars-portal-spotlight-meta"><?php echo esc_html( $spotlight['year'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ( ! empty( $title_cards ) ) : ?>
            <section class="lunara-home-section lunara-oscars-portal-titles">
                <div class="lunara-home-section-header">
                    <div>
                        <p class="lunara-home-section-kicker">Poster-Led Entry Points</p>
                        <h2 class="lunara-home-section-title">Open the ledger through the films themselves.</h2>
                    </div>
                    <p class="lunara-home-section-summary">These title pages are where posters, category history, review links, and ceremony context all start to braid together.</p>
                </div>

                <div class="lunara-oscars-portal-title-grid">
                    <?php foreach ( $title_cards as $card ) : ?>
                        <?php $card_visual = is_array( $card['visual'] ?? null ) ? $card['visual'] : array(); ?>
                        <a class="lunara-oscars-portal-title-card" href="<?php echo esc_url( $card['url'] ?? $database_url ); ?>">
                            <div class="lunara-oscars-portal-title-media">
                                <?php if ( ! empty( $card_visual['poster_html'] ) ) : ?>
                                    <?php echo $card_visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif ( ! empty( $card_visual['poster_url'] ) ) : ?>
                                    <img src="<?php echo esc_url( $card_visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $card['title'] ?? 'Oscar title' ); ?>" loading="lazy" decoding="async" />
                                <?php elseif ( ! empty( $card_visual['card_fallback_html'] ) ) : ?>
                                    <?php echo $card_visual['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <div class="aat-filmography-poster-placeholder"><div class="aat-fallback-inner"><div class="aat-fallback-kicker">Oscar Title</div><div class="aat-fallback-title small"><?php echo esc_html( $card['title'] ?? '' ); ?></div></div></div>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-oscars-portal-title-copy">
                                <h3><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
                                <?php if ( ! empty( $card['year'] ) ) : ?>
                                    <p class="lunara-oscars-portal-title-year"><?php echo esc_html( $card['year'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( ! empty( $card['categories_line'] ) ) : ?>
                                    <p class="lunara-oscars-portal-title-line"><?php echo esc_html( $card['categories_line'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if ( $linked_reviews instanceof WP_Query && $linked_reviews->have_posts() ) : ?>
            <section class="lunara-home-section lunara-oscars-portal-reviews">
                <div class="lunara-home-section-header">
                    <div>
                        <p class="lunara-home-section-kicker">Criticism Meets the Ledger</p>
                        <h2 class="lunara-home-section-title"><?php echo esc_html( $reviews_heading ); ?></h2>
                    </div>
                    <p class="lunara-home-section-summary">These reviews already point back into the Oscars film pages, so the writing and the data are starting to behave like one system.</p>
                </div>

                <div class="lunara-review-grid lunara-review-archive-grid">
                    <?php while ( $linked_reviews->have_posts() ) : $linked_reviews->the_post(); ?>
                        <?php echo lunara_render_review_grid_card( get_the_ID() ); ?>
                    <?php endwhile; ?>
                </div>
            </section>
            <?php wp_reset_postdata(); ?>
        <?php endif; ?>

        <?php if ( ! empty( $deep_cuts ) ) : ?>
            <section class="lunara-home-section lunara-oscars-portal-deep-cuts">
                <div class="lunara-home-section-header">
                    <div>
                        <p class="lunara-home-section-kicker">Rotating Stats</p>
                        <h2 class="lunara-home-section-title"><?php echo esc_html( $deep_cuts_heading ); ?></h2>
                    </div>
                    <p class="lunara-home-section-summary">This section keeps the history moving. It rotates through milestones, outliers, and useful context pulled straight from the ledger.</p>
                </div>

                <div class="lunara-oscars-portal-facts-grid">
                    <?php foreach ( $deep_cuts as $cut ) : ?>
                        <a class="lunara-oscars-portal-fact-card" href="<?php echo esc_url( $cut['url'] ?? $database_url ); ?>">
                            <p class="lunara-oscars-portal-fact-label"><?php echo esc_html( $cut['label'] ?? '' ); ?></p>
                            <strong class="lunara-oscars-portal-fact-value"><?php echo esc_html( $cut['value'] ?? '' ); ?></strong>
                            <?php if ( ! empty( $cut['context'] ) ) : ?>
                                <p class="lunara-oscars-portal-fact-context"><?php echo esc_html( $cut['context'] ); ?></p>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    <?php endif; ?>
</main>
<?php
wp_reset_postdata();
get_footer();
