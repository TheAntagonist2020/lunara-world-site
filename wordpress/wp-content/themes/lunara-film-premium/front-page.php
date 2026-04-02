<?php
/**
 * Front Page Template for Lunara Film
 */

get_header();

$database_spotlight = function_exists( 'lunara_get_home_database_spotlight' ) ? lunara_get_home_database_spotlight() : array();
$snapshot           = function_exists( 'lunara_get_home_oscars_snapshot' ) ? lunara_get_home_oscars_snapshot() : array();
$ledger_stories     = function_exists( 'lunara_get_home_ledger_story_cards' ) ? lunara_get_home_ledger_story_cards() : array();
$pulse_cards        = function_exists( 'lunara_get_home_pulse_editorial_cards' ) ? lunara_get_home_pulse_editorial_cards( $snapshot ) : array();
$oscar_spotlight    = function_exists( 'lunara_get_home_oscar_spotlight' ) ? lunara_get_home_oscar_spotlight() : array();
$deep_cuts          = function_exists( 'lunara_get_home_deep_cuts' ) ? lunara_get_home_deep_cuts() : array();

$hero_kicker         = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_hero_kicker', 'LUNARA FILM' ) : 'LUNARA FILM';
$hero_title          = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_hero_title', get_bloginfo( 'name' ) ) : get_bloginfo( 'name' );
$hero_copy           = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_hero_copy', 'Film criticism and a living Oscar ledger for readers who want cinema, and the record around it, taken seriously.' ) : 'Film criticism and a living Oscar ledger for readers who want cinema, and the record around it, taken seriously.';
$primary_cta_label   = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_primary_cta_label', 'Browse Reviews' ) : 'Browse Reviews';
$primary_cta_url     = function_exists( 'lunara_theme_mod_url' ) ? lunara_theme_mod_url( 'lunara_home_primary_cta_url', home_url( '/reviews/' ) ) : home_url( '/reviews/' );
$secondary_cta_label = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_secondary_cta_label', 'Explore the Oscar Ledger' ) : 'Explore the Oscar Ledger';
$secondary_cta_url   = function_exists( 'lunara_theme_mod_url' ) ? lunara_theme_mod_url( 'lunara_home_secondary_cta_url', ! empty( $database_spotlight['database_url'] ) ? $database_spotlight['database_url'] : home_url( '/oscars/' ) ) : ( ! empty( $database_spotlight['database_url'] ) ? $database_spotlight['database_url'] : home_url( '/oscars/' ) );
$database_heading    = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_database_heading', 'The Lunara Oscar Ledger' ) : 'The Lunara Oscar Ledger';
$database_copy       = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_database_copy', 'This is not just a review blog. The Lunara Oscar Ledger is a research-driven archive of Academy Awards history, structured so readers can move from iconic films to categories, people, companies, and ceremony context without getting lost in a dead wall of data.' ) : 'This is not just a review blog. The Lunara Oscar Ledger is a research-driven archive of Academy Awards history, structured so readers can move from iconic films to categories, people, companies, and ceremony context without getting lost in a dead wall of data.';
$featured_reviews_kicker  = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_featured_reviews_kicker', 'Featured Reviews' ) : 'Featured Reviews';
$featured_reviews_heading = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_featured_reviews_heading', 'Poster-Driven Criticism' ) : 'Poster-Driven Criticism';
$ledger_kicker            = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_ledger_kicker', 'From the Ledger' ) : 'From the Ledger';
$ledger_heading           = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_ledger_heading', 'The Lunara Oscar Ledger' ) : 'The Lunara Oscar Ledger';
$dispatch_kicker          = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_dispatch_kicker', 'Journal' ) : 'Journal';
$dispatch_heading         = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_dispatch_heading', 'Fresh movement from the Lunara Journal' ) : 'Fresh movement from the Lunara Journal';
$dispatch_copy            = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_dispatch_copy', 'Use this lane for reported movement, quick reactions, larger think pieces, interviews, and audio without flattening everything into review coverage.' ) : 'Use this lane for reported movement, quick reactions, larger think pieces, interviews, and audio without flattening everything into review coverage.';
$dispatch_button_label    = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_dispatch_button_label', 'Open the Journal' ) : 'Open the Journal';
$dispatch_button_url      = function_exists( 'lunara_home_dispatch_archive_url' ) ? lunara_home_dispatch_archive_url() : home_url( '/blog/' );
$latest_reviews_heading   = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_latest_reviews_heading', 'New Writing' ) : 'New Writing';
$latest_reviews_cta_label = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_latest_reviews_button_label', 'View All' ) : 'View All';

if ( 'Explore the Oscar Database' === trim( (string) $secondary_cta_label ) ) {
    $secondary_cta_label = 'Explore the Oscar Ledger';
}
if ( 'Oscar Ledger Highlights' === trim( (string) $ledger_heading ) ) {
    $ledger_heading = 'The Lunara Oscar Ledger';
}
if ( 'Dispatches & Audio' === trim( (string) $dispatch_kicker ) ) {
    $dispatch_kicker = 'Journal';
}
if ( 'News, Reactions, and the Lunara Journal' === trim( (string) $dispatch_heading ) ) {
    $dispatch_heading = 'Fresh movement from the Lunara Journal';
}
if ( 'Use this lane for reported news, quick reactions, larger think pieces, and podcast episodes without flattening everything into review coverage.' === trim( (string) $dispatch_copy ) ) {
    $dispatch_copy = 'Use this lane for reported movement, quick reactions, larger think pieces, interviews, and audio without flattening everything into review coverage.';
}
$show_home_hero           = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'hero' ) : true;
$show_featured_reviews    = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'featured' ) : true;
$show_dispatches          = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'dispatch' ) : true;
$show_oscar_spotlight     = false; // Disabled: date-rotated spotlight lands on obscure categories (e.g. Thalberg) — remove from homepage until replaced with curated content.
$show_database_spotlight  = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'database' ) : true;
$show_ledger_stories      = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'ledger' ) : true;
$show_deep_cuts           = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'deep-cuts' ) : true;
$show_latest_reviews      = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'latest-reviews' ) : true;
// Keep the lore carousel available, but suppress the supporting deep-cut strip until its data is trustworthy.
$show_home_oscar_story_lane = true;
$featured                 = null;
$dispatches               = null;
$latest                   = null;
$featured_review_ids      = array();
$latest_review_posts      = array();
$hero_review_posts        = array();
$archive_review_posts     = array();
$lore_cards               = array();
$home_oscar_story_cards   = array();
$published_review_count   = 0;

if ( post_type_exists( 'review' ) ) {
    $review_counts = wp_count_posts( 'review' );
    if ( isset( $review_counts->publish ) ) {
        $published_review_count = max( 0, intval( $review_counts->publish ) );
    }
}

if ( $show_featured_reviews ) {
    $featured = function_exists( 'lunara_home_featured_reviews_query' ) ? lunara_home_featured_reviews_query( 8 ) : lunara_featured_reviews_query( 8 );
}

if ( $show_dispatches ) {
    $dispatches = function_exists( 'lunara_home_dispatches_query' ) ? lunara_home_dispatches_query( 4 ) : new WP_Query(
        array(
            'post_type'      => 'post',
            'post__in'       => array( 0 ),
            'posts_per_page' => 0,
            'no_found_rows'  => true,
        )
    );
}

if ( $show_latest_reviews ) {
    $latest = lunara_latest_reviews_query( 18 );
}

if ( $featured instanceof WP_Query && ! empty( $featured->posts ) ) {
    $featured_review_ids = array_values(
        array_filter(
            array_map( 'intval', wp_list_pluck( $featured->posts, 'ID' ) )
        )
    );
}

if ( $latest instanceof WP_Query && ! empty( $latest->posts ) ) {
    $latest_review_posts = array_values(
        array_filter(
            $latest->posts,
            static function ( $post_item ) use ( $featured_review_ids ) {
                return $post_item instanceof WP_Post && ! in_array( intval( $post_item->ID ), $featured_review_ids, true );
            }
        )
    );

    if ( empty( $latest_review_posts ) ) {
        $latest_review_posts = array_values(
            array_filter(
                $latest->posts,
                static function ( $post_item ) {
                    return $post_item instanceof WP_Post;
                }
            )
        );
    }

    $latest_review_posts  = array_slice( $latest_review_posts, 0, 9 );
    $hero_review_posts    = array_slice( $latest_review_posts, 0, 4 );
    $archive_review_posts = array_slice( $latest_review_posts, 4, 9 );

    if ( empty( $archive_review_posts ) ) {
        $archive_review_posts = $latest_review_posts;
    }
}

if ( $show_deep_cuts ) {
    $lore_cards = get_transient( 'lunara_home_lore_cards_v2' );

    if ( ! is_array( $lore_cards ) || empty( $lore_cards ) ) {
        $lore_cards = array();
        $pool       = array();

        $build_lore_story = static function ( $imdb_id ) {
            $imdb_id = strtolower( trim( (string) $imdb_id ) );
            if ( '' === $imdb_id || ! preg_match( '/^tt\d{7,8}$/', $imdb_id ) ) {
                return array();
            }

            if ( function_exists( 'lunara_build_home_title_story' ) ) {
                return lunara_build_home_title_story(
                    $imdb_id,
                    array(
                        'preferred_categories' => array( 'BEST PICTURE', 'DIRECTING', 'ACTOR IN A LEADING ROLE', 'ACTRESS IN A LEADING ROLE' ),
                        'max_categories'       => 3,
                    )
                );
            }

            if ( ! class_exists( 'Academy_Awards_Table' ) ) {
                return array();
            }

            $aat = Academy_Awards_Table::get_instance();
            if ( ! $aat ) {
                return array();
            }

            global $wpdb;

            $table_name = $wpdb->prefix . 'academy_awards';
            $like       = '%' . $wpdb->esc_like( $imdb_id ) . '%';
            $rows       = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT ceremony, year, canonical_category, film, winner FROM {$table_name} WHERE (film_id = %s OR film_id LIKE %s) AND canonical_category != '' ORDER BY ceremony DESC, winner DESC, canonical_category ASC",
                    $imdb_id,
                    $like
                ),
                ARRAY_A
            );

            if ( ! is_array( $rows ) || empty( $rows ) ) {
                return array();
            }

            $winner_rows = array_values(
                array_filter(
                    $rows,
                    static function ( $row ) {
                        return ! empty( $row['winner'] );
                    }
                )
            );

            $categories = array();
            foreach ( $winner_rows as $row ) {
                $canonical = trim( (string) ( $row['canonical_category'] ?? '' ) );
                if ( '' === $canonical || in_array( $canonical, $categories, true ) ) {
                    continue;
                }
                $categories[] = method_exists( $aat, 'format_category_display' ) ? $aat->format_category_display( $canonical ) : $canonical;
            }

            $first_row = $rows[0];
            $title     = trim( (string) ( $first_row['film'] ?? '' ) );

            return array(
                'imdb_id'         => $imdb_id,
                'title'           => $title,
                'year'            => trim( (string) ( $first_row['year'] ?? '' ) ),
                'url'             => method_exists( $aat, 'build_entity_url_from_id' ) ? $aat->build_entity_url_from_id( $imdb_id ) : home_url( '/oscars/title/' . $imdb_id . '/' ),
                'visual'          => method_exists( $aat, 'get_title_visual_package' ) ? $aat->get_title_visual_package( $imdb_id, 'medium_large' ) : array(),
                'wins'            => count( $winner_rows ),
                'nominations'     => count( $rows ),
                'categories'      => $categories,
                'categories_line' => implode( ' / ', $categories ),
            );
        };

        $push_lore_card = static function ( $story, $eyebrow, $meta, $body ) use ( &$pool ) {
            if ( empty( $story['title'] ) || empty( $story['url'] ) ) {
                return;
            }

            $pool[] = array(
                'title'   => trim( (string) ( $story['title'] ?? '' ) ),
                'year'    => trim( (string) ( $story['year'] ?? '' ) ),
                'url'     => trim( (string) ( $story['url'] ?? '' ) ),
                'visual'  => is_array( $story['visual'] ?? null ) ? $story['visual'] : array(),
                'eyebrow' => trim( (string) $eyebrow ),
                'meta'    => trim( (string) $meta ),
                'body'    => trim( (string) $body ),
            );
        };

        $curated_lore = array(
            array(
                'imdb_id' => 'tt0120338',
                'eyebrow' => 'Oscar sweep',
                'meta'    => 'One of the Academy\'s signature landslides',
                'body'    => 'Titanic remains shorthand for the all-conquering Oscar epic: huge scale, huge emotion, and a sweep people still measure everything against.',
            ),
            array(
                'imdb_id' => 'tt0138097',
                'eyebrow' => 'Famous upset',
                'meta'    => 'The result people still argue over',
                'body'    => 'Shakespeare in Love lives in Oscar lore because it turned one Best Picture race into a permanent argument about taste, campaigning, and canon.',
            ),
            array(
                'imdb_id' => 'tt6751668',
                'eyebrow' => 'History made',
                'meta'    => 'A Best Picture line in the sand',
                'body'    => 'Parasite did more than win. It changed what kind of film people believed the Academy could elevate all the way to the top.',
            ),
            array(
                'imdb_id' => 'tt5052448',
                'eyebrow' => 'Modern horror canon',
                'meta'    => 'Genre prestige without apology',
                'body'    => 'Get Out pushed horror back into the center of awards conversation and proved the Academy could not ignore a cultural phenomenon forever.',
            ),
            array(
                'imdb_id' => 'tt0070047',
                'eyebrow' => 'Horror in the mainline',
                'meta'    => 'The Academy meets a cultural shockwave',
                'body'    => 'The Exorcist still feels like a glitch in the system in the best way: horror breaking into the serious Oscar conversation because the film was impossible to contain.',
            ),
            array(
                'imdb_id' => 'tt0071562',
                'eyebrow' => 'Sequel mythology',
                'meta'    => 'A rare follow-up that deepened the legend',
                'body'    => 'The Godfather Part II is one of the Academy\'s favorite kinds of legacy statement: bigger, darker, and often treated as proof that a sequel can outrank an original.',
            ),
            array(
                'imdb_id' => 'tt0102926',
                'eyebrow' => 'Genre breakthrough',
                'meta'    => 'Prestige thriller, full Oscar authority',
                'body'    => 'The Silence of the Lambs is part of Oscar folklore because it didn\'t just cross genre lines, it dominated the room like an undeniable classic.',
            ),
            array(
                'imdb_id' => 'tt4975722',
                'eyebrow' => 'Moonlight moment',
                'meta'    => 'An ending no one will forget',
                'body'    => 'Moonlight will always be tied to the envelope chaos, but its real place in Oscar history comes from how fully its emotional precision survived the noise.',
            ),
            array(
                'imdb_id' => 'tt0477348',
                'eyebrow' => 'Cold-blooded winner',
                'meta'    => 'A modern Best Picture that kept its edge',
                'body'    => 'No Country for Old Men sits in the Oscar canon as proof that the Academy will sometimes follow a masterpiece all the way into darkness.',
            ),
            array(
                'imdb_id' => 'tt0109830',
                'eyebrow' => 'Consensus winner',
                'meta'    => 'The kind of result that defines a year',
                'body'    => 'Forrest Gump remains one of those wins that instantly turns into a referendum on an entire era of Oscar taste.',
            ),
            array(
                'imdb_id' => 'tt6710474',
                'eyebrow' => 'Chaos, rewarded',
                'meta'    => 'A maximalist winner the Academy embraced',
                'body'    => 'Everything Everywhere All at Once joined Oscar lore by proving that an eccentric, emotionally overloaded film could become the night\'s center of gravity.',
            ),
            array(
                'imdb_id' => 'tt0075148',
                'eyebrow' => 'Underdog legend',
                'meta'    => 'A populist winner that became permanent',
                'body'    => 'Rocky is woven into Oscar memory because it feels like the Academy betting on momentum, feeling, and myth at exactly the right moment.',
            ),
        );

        foreach ( $curated_lore as $entry ) {
            $story = $build_lore_story( $entry['imdb_id'] );
            if ( empty( $story ) ) {
                continue;
            }

            $push_lore_card( $story, $entry['eyebrow'], $entry['meta'], $entry['body'] );
        }

        if ( count( $pool ) < 4 && ! empty( $ledger_stories ) ) {
            foreach ( (array) $ledger_stories as $story ) {
                if ( empty( $story['title'] ) || empty( $story['url'] ) ) {
                    continue;
                }

                $wins        = intval( $story['wins'] ?? 0 );
                $nominations = intval( $story['nominations'] ?? 0 );
                $meta_bits   = array();

                if ( $wins > 0 || $nominations > 0 ) {
                    $meta_bits[] = trim( sprintf( '%s wins / %s nominations', number_format_i18n( $wins ), number_format_i18n( $nominations ) ) );
                }

                if ( ! empty( $story['categories_line'] ) ) {
                    $meta_bits[] = trim( (string) $story['categories_line'] );
                }

                $push_lore_card(
                    $story,
                    'Ledger story',
                    implode( '  •  ', array_filter( $meta_bits ) ),
                    sprintf( '%s still reads like an Oscar memory capsule, the kind of title people use to explain how the Academy remembers a year.', trim( (string) $story['title'] ) )
                );
            }
        }

        if ( ! empty( $pool ) ) {
            $pool_count = count( $pool );
            $start      = intval( date( 'z' ) ) % $pool_count;
            $selected   = array();
            $used_urls  = array();

            for ( $i = 0; $i < $pool_count && count( $selected ) < 4; $i++ ) {
                $card     = $pool[ ( $start + $i ) % $pool_count ];
                $card_url = trim( (string) ( $card['url'] ?? '' ) );

                if ( '' !== $card_url && isset( $used_urls[ $card_url ] ) ) {
                    continue;
                }

                if ( '' !== $card_url ) {
                    $used_urls[ $card_url ] = true;
                }

                $selected[] = $card;
            }

            $lore_cards = $selected;
        set_transient( 'lunara_home_lore_cards_v2', $lore_cards, 6 * HOUR_IN_SECONDS );
    }
}

if ( $show_ledger_stories || $show_deep_cuts ) {
    $home_oscar_story_seen = array();

    if ( ! empty( $lore_cards ) ) {
        foreach ( $lore_cards as $card ) {
            if ( empty( $card['url'] ) || empty( $card['title'] ) ) {
                continue;
            }

            $story_key = strtolower( trim( (string) wp_parse_url( $card['url'], PHP_URL_PATH ) ) );
            if ( '' === $story_key ) {
                $story_key = sanitize_title( $card['title'] );
            }

            if ( isset( $home_oscar_story_seen[ $story_key ] ) ) {
                continue;
            }

            $home_oscar_story_seen[ $story_key ] = true;
            $home_oscar_story_cards[]            = array(
                'url'     => $card['url'],
                'title'   => $card['title'],
                'eyebrow' => ! empty( $card['eyebrow'] ) ? $card['eyebrow'] : 'Oscar Lore',
                'meta'    => ! empty( $card['meta'] ) ? $card['meta'] : '',
                'body'    => ! empty( $card['body'] ) ? $card['body'] : '',
                'visual'  => ! empty( $card['visual'] ) && is_array( $card['visual'] ) ? $card['visual'] : array(),
            );
        }
    }

    if ( ! empty( $ledger_stories ) ) {
        foreach ( $ledger_stories as $story ) {
            if ( empty( $story['url'] ) || empty( $story['title'] ) ) {
                continue;
            }

            $story_key = strtolower( trim( (string) wp_parse_url( $story['url'], PHP_URL_PATH ) ) );
            if ( '' === $story_key ) {
                $story_key = sanitize_title( $story['title'] );
            }

            if ( isset( $home_oscar_story_seen[ $story_key ] ) ) {
                continue;
            }

            $wins        = isset( $story['wins'] ) ? intval( $story['wins'] ) : 0;
            $nominations = isset( $story['nominations'] ) ? intval( $story['nominations'] ) : 0;

            $home_oscar_story_seen[ $story_key ] = true;
            $home_oscar_story_cards[]            = array(
                'url'     => $story['url'],
                'title'   => $story['title'],
                'eyebrow' => 'Inside the Ledger',
                'meta'    => ! empty( $story['categories_line'] ) ? $story['categories_line'] : ( ! empty( $story['year'] ) ? (string) $story['year'] : '' ),
                'body'    => trim( number_format_i18n( $wins ) . ' wins / ' . number_format_i18n( $nominations ) . ' nominations' ),
                'visual'  => ! empty( $story['visual'] ) && is_array( $story['visual'] ) ? $story['visual'] : array(),
            );
        }
    }

    if ( count( $home_oscar_story_cards ) > 10 ) {
        $home_oscar_story_cards = array_slice( $home_oscar_story_cards, 0, 10 );
    }
}
}
?>
<main id="primary" class="site-main lunara-front-page">
    <?php if ( $show_home_hero ) : ?>
    <section class="lunara-home-hero lunara-home-slot-hero is-minimal<?php echo ! empty( $snapshot ) ? ' has-pulse' : ''; ?>">
        <div class="lunara-home-hero-shell">
            <div class="lunara-home-hero-inner lunara-home-hero-copy-panel">
                <?php if ( ! empty( $hero_review_posts ) ) : ?>
                    <div class="lunara-home-hero-review-head">
                        <p class="lunara-home-hero-kicker"><?php echo esc_html( $hero_kicker ); ?></p>
                        <h1 class="lunara-home-hero-review-title"><?php esc_html_e( 'Latest Reviews', 'lunara-film' ); ?></h1>
                        <p class="lunara-home-hero-review-summary"><?php esc_html_e( 'The criticism driving Lunara right now: posters first, arguments intact, and each card opening into the full review.', 'lunara-film' ); ?></p>
                    </div>
                    <div class="lunara-home-hero-review-grid" aria-label="Latest review front">
                        <?php foreach ( $hero_review_posts as $hero_review_post ) : ?>
                            <?php
                            if ( ! ( $hero_review_post instanceof WP_Post ) ) {
                                continue;
                            }
                            $hero_review_id    = intval( $hero_review_post->ID );
                            $hero_review_year  = get_post_meta( $hero_review_id, '_lunara_year', true );
                            $hero_review_score = get_post_meta( $hero_review_id, '_lunara_score', true );
                            ?>
                            <article class="lunara-review-grid-card lunara-review-grid-card-hero">
                                <a class="lunara-review-grid-link" href="<?php echo esc_url( get_permalink( $hero_review_id ) ); ?>">
                                    <div class="lunara-review-grid-poster-wrap">
                                        <?php if ( has_post_thumbnail( $hero_review_id ) ) : ?>
                                            <?php echo get_the_post_thumbnail( $hero_review_id, 'medium_large', array( 'class' => 'lunara-review-grid-poster', 'loading' => 'lazy' ) ); ?>
                                        <?php else : ?>
                                            <div class="lunara-review-grid-poster-placeholder"><span><?php echo esc_html( get_the_title( $hero_review_id ) ); ?></span></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="lunara-review-grid-copy">
                                        <p class="lunara-review-grid-kicker"><?php esc_html_e( 'Lunara Review', 'lunara-film' ); ?></p>
                                        <h3 class="lunara-review-grid-title"><?php echo esc_html( get_the_title( $hero_review_id ) ); ?></h3>
                                        <p class="lunara-review-grid-meta"><?php echo esc_html( $hero_review_year ); ?><?php if ( $hero_review_score ) : ?> <span class="lunara-inline-score"><?php echo wp_kses_post( lunara_render_stars( $hero_review_score ) ); ?></span><?php endif; ?></p>
                                    </div>
                                </a>
                            </article>
                        <?php endforeach; ?>
                    </div>
                    <div class="lunara-home-hero-actions">
                        <a class="lunara-btn" href="<?php echo esc_url( $primary_cta_url ); ?>"><?php echo esc_html( $primary_cta_label ); ?></a>
                        <a class="lunara-btn lunara-btn-secondary" href="<?php echo esc_url( $secondary_cta_url ); ?>"><?php echo esc_html( $secondary_cta_label ); ?></a>
                    </div>
                <?php else : ?>
                    <?php
                    $hero_detail_items = array(
                        array(
                            'label' => 'Criticism',
                            'value' => $published_review_count > 0 ? number_format_i18n( $published_review_count ) . '+' : 'Live',
                            'note'  => 'Poster-led reviews and signature debriefs',
                        ),
                        array(
                            'label' => 'Ledger',
                            'value' => ! empty( $snapshot['ceremonies_total'] ) ? number_format_i18n( intval( $snapshot['ceremonies_total'] ) ) : 'Deep',
                            'note'  => ! empty( $snapshot['ceremonies_total'] ) ? 'Ceremonies tracked inside the Oscar ledger' : 'Academy records, people, titles, and categories',
                        ),
                        array(
                            'label' => 'Current Pulse',
                            'value' => ! empty( $snapshot['year_label'] ) ? trim( (string) $snapshot['year_label'] ) : 'Current',
                            'note'  => ! empty( $snapshot['best_picture']['film'] ) ? trim( (string) ( $snapshot['best_picture']['film'] ) ) : 'New writing, Oscar lore, and editorial movement',
                        ),
                    );
                    ?>
                    <p class="lunara-home-hero-kicker"><?php echo esc_html( $hero_kicker ); ?></p>
                    <h1 class="lunara-home-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
                    <p class="lunara-home-hero-copy"><?php echo esc_html( $hero_copy ); ?></p>
                    <div class="lunara-home-hero-actions">
                        <a class="lunara-btn" href="<?php echo esc_url( $primary_cta_url ); ?>"><?php echo esc_html( $primary_cta_label ); ?></a>
                        <a class="lunara-btn lunara-btn-secondary" href="<?php echo esc_url( $secondary_cta_url ); ?>"><?php echo esc_html( $secondary_cta_label ); ?></a>
                    </div>
                    <div class="lunara-home-hero-details" aria-label="Lunara overview">
                        <?php foreach ( $hero_detail_items as $hero_detail ) : ?>
                            <div class="lunara-home-hero-detail-card">
                                <span class="lunara-home-hero-detail-label"><?php echo esc_html( $hero_detail['label'] ); ?></span>
                                <strong class="lunara-home-hero-detail-value"><?php echo esc_html( $hero_detail['value'] ); ?></strong>
                                <span class="lunara-home-hero-detail-note"><?php echo esc_html( $hero_detail['note'] ); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ( ! empty( $snapshot ) ) : ?>
                <?php
                $best_picture          = ! empty( $snapshot['best_picture'] ) && is_array( $snapshot['best_picture'] ) ? $snapshot['best_picture'] : array();
                $best_picture_visual   = ! empty( $best_picture['visual'] ) && is_array( $best_picture['visual'] ) ? $best_picture['visual'] : array();
                $pulse_titles          = array_slice( (array) ( $snapshot['top_titles'] ?? array() ), 0, 3 );
                $pulse_metrics         = array(
                    array(
                        'label' => 'Ceremony',
                        'value' => trim( (string) ( $snapshot['year_label'] ?? $snapshot['ceremony_label'] ?? '' ) ),
                        'note'  => trim( (string) ( $snapshot['ceremony_label'] ?? '' ) ),
                    ),
                    array(
                        'label' => 'Categories',
                        'value' => number_format_i18n( intval( $snapshot['categories_total'] ?? 0 ) ),
                        'note'  => 'Winner-tracked categories',
                    ),
                    array(
                        'label' => 'Winners',
                        'value' => number_format_i18n( intval( $snapshot['winner_rows_total'] ?? 0 ) ),
                        'note'  => ! empty( $snapshot['winner_record'] ) ? $snapshot['winner_record'] : 'Latest ceremony rows',
                    ),
                );
                $pulse_summary = ! empty( $snapshot['summary'] ) ? $snapshot['summary'] : 'The latest ceremony is already live in the ledger with winners, linked people, and film pages ready to explore.';
                ?>
                <aside class="lunara-home-pulse-card" aria-label="Latest Oscar pulse">
                    <div class="lunara-home-pulse-card-top">
                        <div class="lunara-home-pulse-card-copy">
                            <p class="lunara-home-pulse-kicker">Latest Oscar Pulse</p>
                            <h2 class="lunara-home-pulse-title"><?php echo esc_html( $snapshot['ceremony_label'] ?? 'The latest ceremony' ); ?></h2>
                            <p class="lunara-home-pulse-summary"><?php echo esc_html( $pulse_summary ); ?></p>
                            <?php if ( ! empty( $best_picture['film'] ) ) : ?>
                                <div class="lunara-home-pulse-feature">
                                    <span class="lunara-home-pulse-feature-label">Best Picture</span>
                                    <span class="lunara-home-pulse-feature-title"><?php echo esc_html( $best_picture['film'] ); ?></span>
                                    <span class="lunara-home-pulse-feature-meta"><?php echo esc_html( trim( (string) ( $snapshot['year_label'] ?? '' ) ) ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <a class="lunara-home-pulse-poster" href="<?php echo esc_url( ! empty( $best_picture['film_url'] ) ? $best_picture['film_url'] : ( $snapshot['ceremony_url'] ?? home_url( '/oscars/' ) ) ); ?>">
                            <?php if ( ! empty( $best_picture_visual['poster_html'] ) ) : ?>
                                <?php echo $best_picture_visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php elseif ( ! empty( $best_picture_visual['poster_url'] ) ) : ?>
                                <img src="<?php echo esc_url( $best_picture_visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $best_picture['film'] ?? 'Latest Best Picture' ); ?> poster" loading="lazy" decoding="async" />
                            <?php else : ?>
                                <div class="lunara-home-standout-poster-placeholder"><span><?php echo esc_html( $best_picture['film'] ?? 'Oscar Pulse' ); ?></span></div>
                            <?php endif; ?>
                        </a>
                    </div>

                    <div class="lunara-home-pulse-metrics">
                        <?php foreach ( $pulse_metrics as $metric ) : ?>
                            <div class="lunara-home-pulse-metric">
                                <span class="lunara-home-pulse-metric-label"><?php echo esc_html( $metric['label'] ); ?></span>
                                <span class="lunara-home-pulse-metric-value"><?php echo esc_html( $metric['value'] ); ?></span>
                                <?php if ( ! empty( $metric['note'] ) ) : ?>
                                    <span class="lunara-home-pulse-metric-note"><?php echo esc_html( $metric['note'] ); ?></span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <?php if ( ! empty( $pulse_titles ) ) : ?>
                        <div class="lunara-home-title-chip-grid">
                            <?php foreach ( $pulse_titles as $pulse_title ) : ?>
                                <?php
                                $pulse_title_name = trim( (string) ( $pulse_title['film'] ?? '' ) );
                                $pulse_title_url  = trim( (string) ( $pulse_title['url'] ?? '' ) );
                                if ( '' === $pulse_title_name || '' === $pulse_title_url ) {
                                    continue;
                                }
                                ?>
                                <a class="lunara-home-title-chip" href="<?php echo esc_url( $pulse_title_url ); ?>">
                                    <strong><?php echo esc_html( $pulse_title_name ); ?></strong>
                                    <span><?php echo esc_html( ! empty( $pulse_title['winning_categories_line'] ) ? $pulse_title['winning_categories_line'] : sprintf( '%s wins', number_format_i18n( intval( $pulse_title['wins'] ?? 0 ) ) ) ); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </aside>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $show_latest_reviews && ! empty( $archive_review_posts ) ) : ?>
    <section class="lunara-home-section lunara-home-slot-latest-reviews lunara-latest-reviews-section" aria-label="Latest Reviews">
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker">Latest Reviews</p>
                <h2 class="lunara-home-section-title"><?php echo esc_html( $latest_reviews_heading ); ?></h2>
            </div>
            <a class="lunara-section-link" href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>"><?php echo esc_html( $latest_reviews_cta_label ); ?></a>
        </div>
        <div class="lunara-review-grid lunara-review-archive-uniform">
            <?php foreach ( $archive_review_posts as $latest_post ) : ?>
                <?php
                setup_postdata( $latest_post );
                $latest_post_id = intval( $latest_post->ID );
                $latest_year    = get_post_meta( $latest_post_id, '_lunara_year', true );
                $latest_score   = get_post_meta( $latest_post_id, '_lunara_score', true );
                $latest_url     = get_permalink( $latest_post_id );
                $latest_title   = get_the_title( $latest_post_id );
                ?>
                <article class="lunara-review-grid-card">
                    <a class="lunara-review-grid-link" href="<?php echo esc_url( $latest_url ); ?>">
                        <div class="lunara-review-grid-poster-wrap">
                            <?php if ( has_post_thumbnail( $latest_post_id ) ) : ?>
                                <?php echo get_the_post_thumbnail( $latest_post_id, 'medium_large', array( 'class' => 'lunara-review-grid-poster', 'loading' => 'lazy' ) ); ?>
                            <?php endif; ?>
                            <?php if ( $latest_score ) : ?>
                                <span class="lunara-score-badge"><?php echo wp_kses_post( lunara_render_stars( $latest_score ) ); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="lunara-review-grid-copy">
                            <p class="lunara-review-grid-kicker">Lunara Review</p>
                            <h3 class="lunara-review-grid-title"><?php echo esc_html( $latest_title ); ?></h3>
                            <?php if ( $latest_year ) : ?>
                                <p class="lunara-review-grid-meta"><?php echo esc_html( $latest_year ); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endforeach; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $show_featured_reviews && $featured instanceof WP_Query && $featured->have_posts() ) : ?>
    <section class="lunara-home-section lunara-home-slot-featured lunara-featured-reviews-section" aria-label="Featured Reviews" data-lunara-carousel>
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker"><?php echo esc_html( $featured_reviews_kicker ); ?></p>
                <h2 class="lunara-home-section-title"><?php echo esc_html( $featured_reviews_heading ); ?></h2>
                <p class="lunara-home-section-summary">A rotating shelf of the criticism driving Lunara right now: posters first, arguments intact, and every card opening into the full review.</p>
            </div>
            <div class="lunara-poster-carousel-controls">
                <button type="button" class="lunara-poster-carousel-btn lunara-poster-carousel-prev" data-lunara-carousel-prev aria-label="Previous featured reviews">&#8592;</button>
                <button type="button" class="lunara-poster-carousel-btn lunara-poster-carousel-next" data-lunara-carousel-next aria-label="Next featured reviews">&#8594;</button>
            </div>
        </div>
        <div class="lunara-poster-carousel-wrap">
            <div class="lunara-poster-carousel-track" data-lunara-carousel-track>
                <?php while ( $featured->have_posts() ) : $featured->the_post(); ?>
                    <?php
                    $featured_post_id = get_the_ID();
                    $featured_year    = get_post_meta( $featured_post_id, '_lunara_year', true );
                    $featured_score   = get_post_meta( $featured_post_id, '_lunara_score', true );
                    $featured_teaser  = function_exists( 'lunara_get_review_card_teaser' ) ? trim( (string) lunara_get_review_card_teaser() ) : '';
                    ?>
                    <article class="lunara-poster-card lunara-poster-card-featured">
                        <a class="lunara-poster-card-link" href="<?php the_permalink(); ?>">
                            <div class="lunara-poster-card-image-wrap">
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'large', array( 'class' => 'lunara-poster-card-image', 'loading' => 'lazy' ) ); ?>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-poster-card-copy">
                                <p class="lunara-poster-card-kicker">LUNARA FILM REVIEW</p>
                                <h3 class="lunara-poster-card-title"><?php the_title(); ?></h3>
                                <p class="lunara-poster-card-meta"><?php echo esc_html( $featured_year ); ?><?php if ( $featured_score ) : ?> <span class="lunara-inline-score"><?php echo wp_kses_post( lunara_render_stars( $featured_score ) ); ?></span><?php endif; ?></p>
                                <?php if ( '' !== $featured_teaser ) : ?>
                                    <p class="lunara-poster-card-excerpt"><?php echo esc_html( $featured_teaser ); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $show_dispatches && $dispatches instanceof WP_Query && $dispatches->have_posts() ) :
        $dispatch_posts = is_array( $dispatches->posts ) ? $dispatches->posts : array();
        $dispatch_lead  = ! empty( $dispatch_posts ) ? array_shift( $dispatch_posts ) : null;
        $dispatch_mix   = array();

        if ( $dispatch_lead instanceof WP_Post ) {
            $lead_type_label = function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $dispatch_lead->ID ) : 'Dispatch';
            if ( ! empty( $lead_type_label ) ) {
                $dispatch_mix[ sanitize_title( $lead_type_label ) ] = $lead_type_label;
            }
        }

        foreach ( $dispatch_posts as $dispatch_mix_post ) {
            if ( ! ( $dispatch_mix_post instanceof WP_Post ) ) {
                continue;
            }

            $dispatch_type_label = function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $dispatch_mix_post->ID ) : 'Dispatch';
            if ( ! empty( $dispatch_type_label ) ) {
                $dispatch_mix[ sanitize_title( $dispatch_type_label ) ] = $dispatch_type_label;
            }
        }

        $dispatch_mix_labels = array_slice( array_values( $dispatch_mix ), 0, 3 );
        $dispatch_overview   = array(
            array(
                'label' => 'Live now',
                'value' => number_format_i18n( count( array_filter( array_merge( array( $dispatch_lead ), $dispatch_posts ) ) ) ),
                'note'  => 'Stories currently surfaced on the homepage',
            ),
            array(
                'label' => 'Signal mix',
                'value' => ! empty( $dispatch_mix_labels ) ? implode( ' / ', $dispatch_mix_labels ) : 'Journal / Audio / Reactions',
                'note'  => 'One lane for reporting, reaction, essays, and audio',
            ),
            array(
                'label' => 'Latest filed',
                'value' => $dispatch_lead instanceof WP_Post ? get_the_date( 'M j', $dispatch_lead->ID ) : 'Current',
                'note'  => $dispatch_lead instanceof WP_Post ? get_the_title( $dispatch_lead->ID ) : 'Editorial signal from the Lunara desk',
            ),
        );
    ?>
    <section class="lunara-home-section lunara-home-slot-dispatch lunara-dispatches-section" aria-label="Dispatches and Audio">
        <div class="lunara-home-section-head is-with-summary">
            <div>
                <p class="lunara-home-section-kicker"><?php echo esc_html( $dispatch_kicker ); ?></p>
                <h2 class="lunara-home-section-title"><?php echo esc_html( $dispatch_heading ); ?></h2>
                <?php if ( ! empty( $dispatch_copy ) ) : ?>
                    <p class="lunara-home-section-summary"><?php echo esc_html( $dispatch_copy ); ?></p>
                <?php endif; ?>
            </div>
            <a class="lunara-section-link" href="<?php echo esc_url( $dispatch_button_url ); ?>"><?php echo esc_html( $dispatch_button_label ); ?></a>
        </div>
        <div class="lunara-dispatch-overview" aria-label="Editorial signal overview">
            <?php foreach ( $dispatch_overview as $dispatch_overview_item ) : ?>
                <div class="lunara-dispatch-overview-card">
                    <span class="lunara-dispatch-overview-label"><?php echo esc_html( $dispatch_overview_item['label'] ); ?></span>
                    <strong class="lunara-dispatch-overview-value"><?php echo esc_html( $dispatch_overview_item['value'] ); ?></strong>
                    <span class="lunara-dispatch-overview-note"><?php echo esc_html( $dispatch_overview_item['note'] ); ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="lunara-dispatch-shell">
            <?php if ( $dispatch_lead instanceof WP_Post ) : ?>
                <?php
                setup_postdata( $dispatch_lead );
                $lead_post_id = $dispatch_lead->ID;
                ?>
                <article class="lunara-dispatch-lead">
                    <a class="lunara-dispatch-lead-link" href="<?php echo esc_url( get_permalink( $lead_post_id ) ); ?>">
                        <div class="lunara-dispatch-lead-media">
                            <?php if ( has_post_thumbnail( $lead_post_id ) ) : ?>
                                <?php echo get_the_post_thumbnail( $lead_post_id, 'large', array( 'class' => 'lunara-dispatch-lead-image', 'loading' => 'lazy' ) ); ?>
                            <?php else : ?>
                                <div class="lunara-dispatch-lead-placeholder">
                                    <span><?php echo esc_html( function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $lead_post_id ) : 'Dispatch' ); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="lunara-dispatch-lead-copy">
                            <p class="lunara-dispatch-lead-kicker">Signal Lead</p>
                            <p class="lunara-dispatch-type"><?php echo esc_html( function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $lead_post_id ) : 'Dispatch' ); ?></p>
                            <h3 class="lunara-dispatch-lead-title"><?php echo esc_html( get_the_title( $lead_post_id ) ); ?></h3>
                            <p class="lunara-dispatch-lead-excerpt"><?php echo esc_html( function_exists( 'lunara_card_excerpt' ) ? lunara_card_excerpt( $lead_post_id, 34 ) : wp_trim_words( get_the_excerpt( $lead_post_id ), 34 ) ); ?></p>
                            <div class="lunara-dispatch-lead-meta">
                                <span><?php echo esc_html( get_the_date( 'F j, Y', $lead_post_id ) ); ?></span>
                                <span class="lunara-dispatch-meta-link">Read or listen</span>
                            </div>
                        </div>
                    </a>
                </article>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

            <div class="lunara-dispatch-rail">
                <?php foreach ( $dispatch_posts as $dispatch_post ) : ?>
                    <?php
                    if ( ! ( $dispatch_post instanceof WP_Post ) ) {
                        continue;
                    }
                    setup_postdata( $dispatch_post );
                    $dispatch_post_id = $dispatch_post->ID;
                    ?>
                    <article class="lunara-dispatch-rail-card">
                        <a class="lunara-dispatch-rail-link" href="<?php echo esc_url( get_permalink( $dispatch_post_id ) ); ?>">
                            <div class="lunara-dispatch-rail-thumb-wrap">
                                <?php if ( has_post_thumbnail( $dispatch_post_id ) ) : ?>
                                    <?php echo get_the_post_thumbnail( $dispatch_post_id, 'medium_large', array( 'class' => 'lunara-dispatch-rail-thumb', 'loading' => 'lazy' ) ); ?>
                                <?php else : ?>
                                    <div class="lunara-dispatch-rail-thumb-placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-dispatch-rail-copy">
                                <p class="lunara-dispatch-type"><?php echo esc_html( function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $dispatch_post_id ) : 'Dispatch' ); ?></p>
                                <h3 class="lunara-dispatch-rail-title"><?php echo esc_html( get_the_title( $dispatch_post_id ) ); ?></h3>
                                <p class="lunara-dispatch-rail-excerpt"><?php echo esc_html( function_exists( 'lunara_card_excerpt' ) ? lunara_card_excerpt( $dispatch_post_id, 18 ) : wp_trim_words( get_the_excerpt( $dispatch_post_id ), 18 ) ); ?></p>
                                <p class="lunara-dispatch-rail-meta"><?php echo esc_html( get_the_date( 'F j, Y', $dispatch_post_id ) ); ?> <span class="lunara-dispatch-rail-meta-sep">/</span> Open signal</p>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
                <?php wp_reset_postdata(); ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $show_oscar_spotlight && ! empty( $oscar_spotlight ) ) : ?>
    <section class="lunara-home-section lunara-home-slot-oscar-spotlight lunara-oscar-spotlight-section" aria-label="Oscar Spotlight">
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker"><?php echo esc_html( $oscar_spotlight['kicker'] ?? 'Oscar Spotlight' ); ?></p>
                <h2 class="lunara-home-section-title"><?php echo esc_html( $oscar_spotlight['title'] ?? '' ); ?></h2>
            </div>
            <?php if ( ! empty( $oscar_spotlight['url'] ) ) : ?>
                <a class="lunara-section-link" href="<?php echo esc_url( $oscar_spotlight['url'] ); ?>">Explore</a>
            <?php endif; ?>
        </div>
        <div class="lunara-oscar-spotlight-layout">
            <?php
            $spotlight_film   = ! empty( $oscar_spotlight['featured_film'] ) && is_array( $oscar_spotlight['featured_film'] ) ? $oscar_spotlight['featured_film'] : array();
            $spotlight_visual = ! empty( $spotlight_film['visual'] ) && is_array( $spotlight_film['visual'] ) ? $spotlight_film['visual'] : array();
            ?>
            <?php if ( ! empty( $spotlight_film ) ) : ?>
                <a class="lunara-oscar-spotlight-poster" href="<?php echo esc_url( ! empty( $spotlight_film['url'] ) ? $spotlight_film['url'] : ( $oscar_spotlight['url'] ?? home_url( '/oscars/' ) ) ); ?>">
                    <?php if ( ! empty( $spotlight_visual['poster_html'] ) ) : ?>
                        <?php echo $spotlight_visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php elseif ( ! empty( $spotlight_visual['poster_url'] ) ) : ?>
                        <img src="<?php echo esc_url( $spotlight_visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $spotlight_film['title'] ?? 'Oscar Spotlight' ); ?> poster" loading="lazy" decoding="async" />
                    <?php else : ?>
                        <div class="lunara-home-standout-poster-placeholder"><span><?php echo esc_html( $spotlight_film['title'] ?? 'Oscar Spotlight' ); ?></span></div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
            <div class="lunara-oscar-spotlight-text-panel">
                <?php if ( ! empty( $oscar_spotlight['copy'] ) ) : ?>
                    <p class="lunara-oscar-spotlight-copy"><?php echo esc_html( $oscar_spotlight['copy'] ); ?></p>
                <?php endif; ?>
                <?php if ( ! empty( $oscar_spotlight['stats'] ) && is_array( $oscar_spotlight['stats'] ) ) : ?>
                    <div class="lunara-oscar-spotlight-pills">
                        <?php foreach ( $oscar_spotlight['stats'] as $stat ) : ?>
                            <span class="lunara-oscar-spotlight-pill">
                                <span class="lunara-oscar-spotlight-pill-label"><?php echo esc_html( $stat['label'] ?? '' ); ?></span>
                                <span class="lunara-oscar-spotlight-pill-value"><?php echo esc_html( $stat['value'] ?? '' ); ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ( ! empty( $oscar_spotlight['url'] ) ) : ?>
                    <div class="lunara-oscar-spotlight-actions">
                        <a class="lunara-btn" href="<?php echo esc_url( $oscar_spotlight['url'] ); ?>">Explore</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $show_database_spotlight && ! empty( $database_spotlight ) ) : ?>
    <section class="lunara-home-section lunara-home-slot-database lunara-database-spotlight-section" aria-label="Oscar Ledger Spotlight">
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker">Oscar Ledger Spotlight</p>
                <h2 class="lunara-home-section-title"><?php echo esc_html( $database_heading ); ?></h2>
            </div>
            <a class="lunara-section-link" href="<?php echo esc_url( $database_spotlight['database_url'] ); ?>">Enter the Ledger</a>
        </div>
        <div class="lunara-home-pulse-layout">
            <div class="lunara-home-pulse-feature-card">
                <p class="lunara-home-section-kicker">Research Engine</p>
                <h3 class="lunara-home-pulse-feature-heading"><?php echo esc_html( $database_heading ); ?></h3>
                <p class="lunara-home-pulse-feature-copy"><?php echo esc_html( $database_copy ); ?></p>
                <?php if ( ! empty( $snapshot['summary'] ) ) : ?>
                    <p class="lunara-home-pulse-feature-copy is-secondary"><?php echo esc_html( $snapshot['summary'] ); ?></p>
                <?php endif; ?>
                <div class="lunara-database-spotlight-stats">
                    <span class="lunara-database-stat"><?php echo esc_html( number_format_i18n( intval( $database_spotlight['ceremonies_total'] ?? 0 ) ) ); ?> ceremonies</span>
                    <span class="lunara-database-stat"><?php echo esc_html( number_format_i18n( intval( $database_spotlight['categories_total'] ?? 0 ) ) ); ?> categories</span>
                    <span class="lunara-database-stat"><?php echo esc_html( number_format_i18n( intval( $database_spotlight['records_total'] ?? 0 ) ) ); ?> records</span>
                </div>
                <div class="lunara-home-pulse-actions">
                    <a class="lunara-btn" href="<?php echo esc_url( $database_spotlight['database_url'] ); ?>">Explore the Ledger</a>
                    <a class="lunara-btn lunara-btn-secondary" href="<?php echo esc_url( $database_spotlight['categories_url'] ); ?>">Browse Categories</a>
                </div>
            </div>

            <?php if ( ! empty( $snapshot['spotlights'] ) ) : ?>
                <div class="lunara-home-winner-grid">
                    <?php
                    $aat_for_visuals = class_exists( 'Academy_Awards_Table' ) ? Academy_Awards_Table::get_instance() : null;
                    foreach ( array_slice( (array) $snapshot['spotlights'], 0, 6 ) as $spotlight ) :
                        $winner_visual = array();
                        if ( $aat_for_visuals && method_exists( $aat_for_visuals, 'get_title_visual_package' ) && ! empty( $spotlight['film_id'] ) ) {
                            $winner_visual = $aat_for_visuals->get_title_visual_package( $spotlight['film_id'], 'medium' );
                        }
                    ?>
                        <article class="lunara-home-winner-card<?php echo ! empty( $winner_visual['poster_url'] ) || ! empty( $winner_visual['poster_html'] ) ? ' has-poster' : ''; ?>">
                            <a class="lunara-home-winner-card-link" href="<?php echo esc_url( ! empty( $spotlight['url'] ) ? $spotlight['url'] : $database_spotlight['database_url'] ); ?>">
                                <?php if ( ! empty( $winner_visual['poster_html'] ) ) : ?>
                                    <div class="lunara-home-winner-poster-wrap">
                                        <?php echo $winner_visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                <?php elseif ( ! empty( $winner_visual['poster_url'] ) ) : ?>
                                    <div class="lunara-home-winner-poster-wrap">
                                        <img src="<?php echo esc_url( $winner_visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $spotlight['film'] ?? 'Winner' ); ?>" class="lunara-home-winner-poster" loading="lazy" />
                                    </div>
                                <?php endif; ?>
                                <div class="lunara-home-winner-card-copy">
                                    <p class="lunara-home-winner-category"><?php echo esc_html( $spotlight['category_label'] ?? $spotlight['canonical_category'] ?? 'Winner' ); ?></p>
                                    <h3 class="lunara-home-winner-title"><?php echo esc_html( $spotlight['primary_label'] ?? $spotlight['film'] ?? 'Oscar winner' ); ?></h3>
                                    <?php if ( ! empty( $spotlight['secondary_label'] ) ) : ?>
                                        <p class="lunara-home-winner-meta"><?php echo esc_html( $spotlight['secondary_label'] ); ?></p>
                                    <?php elseif ( ! empty( $spotlight['film'] ) ) : ?>
                                        <p class="lunara-home-winner-meta"><?php echo esc_html( $spotlight['film'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $pulse_cards ) ) : ?>
            <div class="lunara-home-pulse-notes">
                <?php foreach ( $pulse_cards as $pulse_card ) : ?>
                    <article class="lunara-home-pulse-note">
                        <p class="lunara-home-pulse-note-kicker"><?php echo esc_html( $pulse_card['kicker'] ?? 'Oscar Pulse' ); ?></p>
                        <h3 class="lunara-home-pulse-note-title"><?php echo esc_html( $pulse_card['title'] ?? '' ); ?></h3>
                        <?php if ( ! empty( $pulse_card['copy'] ) ) : ?>
                            <p class="lunara-home-pulse-note-copy"><?php echo esc_html( $pulse_card['copy'] ); ?></p>
                        <?php endif; ?>
                        <?php if ( ! empty( $pulse_card['url'] ) ) : ?>
                            <a class="lunara-home-pulse-note-link" href="<?php echo esc_url( $pulse_card['url'] ); ?>"><?php echo esc_html( $pulse_card['link_label'] ?? 'Explore' ); ?></a>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( ! empty( $database_spotlight['cards'] ) ) : ?>
            <div class="lunara-database-spotlight-grid">
                <?php foreach ( $database_spotlight['cards'] as $card ) : ?>
                    <?php $visual = ! empty( $card['visual'] ) && is_array( $card['visual'] ) ? $card['visual'] : array(); ?>
                    <article class="lunara-database-spotlight-card">
                        <a class="lunara-database-spotlight-link" href="<?php echo esc_url( $card['url'] ); ?>">
                            <div class="lunara-database-spotlight-poster">
                                <?php if ( ! empty( $visual['poster_html'] ) ) : ?>
                                    <?php echo $visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif ( ! empty( $visual['poster_url'] ) ) : ?>
                                    <img src="<?php echo esc_url( $visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?> poster" loading="lazy" decoding="async" />
                                <?php elseif ( ! empty( $visual['card_fallback_html'] ) ) : ?>
                                    <?php echo $visual['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <div class="lunara-home-standout-poster-placeholder"><span><?php echo esc_html( $card['title'] ); ?></span></div>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-database-spotlight-card-copy">
                                <h3 class="lunara-database-spotlight-card-title"><?php echo esc_html( $card['title'] ); ?></h3>
                                <p class="lunara-database-spotlight-card-meta"><?php echo esc_html( $card['year'] ); ?></p>
                                <?php if ( ! empty( $card['categories_line'] ) ) : ?>
                                    <p class="lunara-database-spotlight-card-context"><?php echo esc_html( $card['categories_line'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

    <?php if ( $show_home_oscar_story_lane && ! empty( $home_oscar_story_cards ) ) : ?>
    <section class="lunara-home-section lunara-home-slot-deep-cuts lunara-deep-cut-section lunara-home-oscar-story-section" aria-label="Oscar Lore"<?php echo ! empty( $home_oscar_story_cards ) ? ' data-lunara-carousel data-lunara-carousel-autoplay="7600"' : ''; ?>>
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker">Oscar Lore</p>
                <h2 class="lunara-home-section-title">Legends, records, and Oscar arguments that still live on.</h2>
            </div>
            <?php if ( ! empty( $home_oscar_story_cards ) ) : ?>
                <div class="lunara-poster-carousel-controls">
                    <button type="button" class="lunara-poster-carousel-btn lunara-poster-carousel-prev" data-lunara-carousel-prev aria-label="Previous Oscar lore">&#8592;</button>
                    <button type="button" class="lunara-poster-carousel-btn lunara-poster-carousel-next" data-lunara-carousel-next aria-label="Next Oscar lore">&#8594;</button>
                </div>
            <?php else : ?>
                <a class="lunara-section-link" href="<?php echo esc_url( home_url( '/oscars/' ) ); ?>">Explore the Ledger</a>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $home_oscar_story_cards ) ) : ?>
            <div class="lunara-ledger-carousel-wrap">
                <div class="lunara-ledger-carousel-track lunara-lore-carousel-track" data-lunara-carousel-track>
                    <?php foreach ( $home_oscar_story_cards as $card ) : ?>
                        <?php $visual = ! empty( $card['visual'] ) && is_array( $card['visual'] ) ? $card['visual'] : array(); ?>
                        <article class="lunara-ledger-story-card lunara-lore-card">
                            <a class="lunara-ledger-story-link" href="<?php echo esc_url( $card['url'] ); ?>">
                                <div class="lunara-ledger-story-poster lunara-lore-card-poster">
                                    <?php if ( ! empty( $visual['poster_html'] ) ) : ?>
                                        <?php echo $visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php elseif ( ! empty( $visual['poster_url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $card['title'] ); ?> poster" loading="lazy" decoding="async" />
                                    <?php elseif ( ! empty( $visual['card_fallback_html'] ) ) : ?>
                                        <?php echo $visual['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php else : ?>
                                        <div class="lunara-home-standout-poster-placeholder"><span><?php echo esc_html( $card['title'] ); ?></span></div>
                                    <?php endif; ?>
                                </div>
                                <div class="lunara-ledger-story-copy lunara-lore-card-copy">
                                    <p class="lunara-ledger-story-year"><?php echo esc_html( $card['eyebrow'] ?? '' ); ?></p>
                                    <h3 class="lunara-ledger-story-title"><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
                                    <?php if ( ! empty( $card['meta'] ) ) : ?>
                                        <p class="lunara-ledger-story-categories"><?php echo esc_html( $card['meta'] ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $card['body'] ) ) : ?>
                                        <p class="lunara-ledger-story-summary"><?php echo esc_html( $card['body'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </section>
    <?php endif; ?>

</main>
<?php get_footer(); ?>
