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
$featured_reviews_heading = function_exists( 'lunara_theme_mod_text' ) ? lunara_theme_mod_text( 'lunara_home_featured_reviews_heading', 'Featured Criticism' ) : 'Featured Criticism';
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
if (
    false !== strpos( strtolower( preg_replace( '/[^a-z]+/', ' ', (string) $featured_reviews_heading ) ), 'poster' )
    && false !== strpos( strtolower( preg_replace( '/[^a-z]+/', ' ', (string) $featured_reviews_heading ) ), 'driven' )
) {
    $featured_reviews_heading = 'Featured Criticism';
}
if ( 'Dispatches & Audio' === trim( (string) $dispatch_kicker ) || 'Dispatches' === trim( (string) $dispatch_kicker ) ) {
    $dispatch_kicker = 'Journal';
}
if ( 'News, Reactions, and the Lunara Journal' === trim( (string) $dispatch_heading ) ) {
    $dispatch_heading = 'Fresh movement from the Lunara Journal';
}
if ( 'Use this lane for reported news, quick reactions, larger think pieces, and podcast episodes without flattening everything into review coverage.' === trim( (string) $dispatch_copy ) ) {
    $dispatch_copy = 'Use this lane for reported movement, quick reactions, larger think pieces, interviews, and audio without flattening everything into review coverage.';
}
$normalize_journal_lane_label = static function( $label ) {
    $label = trim( (string) $label );
    if ( in_array( $label, array( 'Dispatch', 'Dispatches', 'Dispatches & Audio' ), true ) ) {
        return 'Journal';
    }

    return $label;
};
$show_home_hero           = function_exists( 'lunara_home_section_is_enabled' ) ? lunara_home_section_is_enabled( 'hero' ) : true;
$show_featured_reviews    = false; // Disabled: redundant with latest-reviews grid.
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
$hero_review_count        = max( 2, absint( get_theme_mod( 'lunara_home_hero_review_count', 4 ) ) );

if ( post_type_exists( 'review' ) ) {
    $review_counts = wp_count_posts( 'review' );
    if ( isset( $review_counts->publish ) ) {
        $published_review_count = max( 0, intval( $review_counts->publish ) );
    }
}

if ( $show_featured_reviews ) {
    $featured_count = absint( get_theme_mod( 'lunara_home_featured_count', 8 ) );
    $featured = function_exists( 'lunara_home_featured_reviews_query' ) ? lunara_home_featured_reviews_query( $featured_count ) : lunara_featured_reviews_query( $featured_count );
}

if ( $show_home_hero ) {
    $hero_query = function_exists( 'lunara_home_hero_reviews_query' )
        ? lunara_home_hero_reviews_query( $hero_review_count )
        : ( function_exists( 'lunara_home_featured_reviews_query' ) ? lunara_home_featured_reviews_query( $hero_review_count ) : null );

    if ( $hero_query instanceof WP_Query && ! empty( $hero_query->posts ) ) {
        $hero_review_posts = array_values(
            array_filter(
                $hero_query->posts,
                static function ( $post_item ) {
                    return $post_item instanceof WP_Post;
                }
            )
        );
    }
}

if ( $show_dispatches ) {
    $dispatch_count = absint( get_theme_mod( 'lunara_home_dispatch_count', 4 ) );
    $dispatches = function_exists( 'lunara_home_dispatches_query' ) ? lunara_home_dispatches_query( $dispatch_count ) : new WP_Query(
        array(
            'post_type'      => 'post',
            'post__in'       => array( 0 ),
            'posts_per_page' => 0,
            'no_found_rows'  => true,
        )
    );
}

if ( $show_latest_reviews ) {
    $latest = lunara_latest_reviews_query( absint( get_theme_mod( 'lunara_home_latest_count', 18 ) ) );
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

    if ( empty( $hero_review_posts ) ) {
        $hero_review_posts = array_slice( $latest_review_posts, 0, $hero_review_count );
    }

    $hero_review_ids = array_values(
        array_filter(
            array_map( 'intval', wp_list_pluck( $hero_review_posts, 'ID' ) )
        )
    );

    if ( $show_home_hero && ! empty( $hero_review_ids ) ) {
        $archive_review_posts = array_values(
            array_filter(
                $latest_review_posts,
                static function ( $post_item ) use ( $hero_review_ids ) {
                    return $post_item instanceof WP_Post && ! in_array( intval( $post_item->ID ), $hero_review_ids, true );
                }
            )
        );
    } else {
        $archive_review_posts = $latest_review_posts;
    }

    $archive_review_posts = array_slice( $archive_review_posts, 0, 9 );

    if ( empty( $archive_review_posts ) && ! empty( $hero_review_posts ) ) {
        $archive_review_posts = array_slice( $hero_review_posts, 0, 9 );
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

        // ── Oscar Facts & Trivia — each tied to a related film for poster imagery ──
        $curated_facts = array(
            array(
                'imdb_id' => 'tt0070510',
                'eyebrow' => 'Oscar record',
                'meta'    => 'Youngest acting winner — Best Supporting Actress, 1973',
                'body'    => 'Tatum O\'Neal won for Paper Moon at just 10 years old. That record has stood for over fifty years and shows no signs of falling.',
            ),
            array(
                'imdb_id' => 'tt10272386',
                'eyebrow' => 'Oscar record',
                'meta'    => 'Oldest acting winner — Best Actor, 2020',
                'body'    => 'At 83, Anthony Hopkins won for The Father in one of the biggest surprise announcements the ceremony has ever delivered.',
            ),
            array(
                'imdb_id' => 'tt0086250',
                'eyebrow' => 'All-time record',
                'meta'    => '21 nominations, three wins across two categories',
                'body'    => 'Meryl Streep\'s record is the benchmark against which every career performance total is measured. No other actor has been nominated more.',
            ),
            array(
                'imdb_id' => 'tt0064665',
                'eyebrow' => 'Oscar anomaly',
                'meta'    => 'The only X-rated Best Picture winner',
                'body'    => 'Midnight Cowboy was quickly re-rated R in 1971. The X rating was less about the content and more about the era\'s discomfort with its themes.',
            ),
            array(
                'imdb_id' => 'tt0022958',
                'eyebrow' => 'Oscar anomaly',
                'meta'    => 'Best Picture with zero other nominations',
                'body'    => 'Grand Hotel wasn\'t even nominated for directing, acting, or anything else. Over ninety years later, it remains a total outlier.',
            ),
            array(
                'imdb_id' => 'tt0074958',
                'eyebrow' => 'Oscar record',
                'meta'    => 'Shortest winning performance — five minutes on screen',
                'body'    => 'Beatrice Straight won for Network with five minutes and two seconds of screen time. One devastating monologue and a permanent place in the record book.',
            ),
            array(
                'imdb_id' => 'tt0068646',
                'eyebrow' => 'Oscar pattern',
                'meta'    => 'Brando/De Niro, Ledger/Phoenix, Moreno/DeBose',
                'body'    => 'Don Vito Corleone, the Joker, and Anita — three characters whose performances won Oscars for two different actors in two different films.',
            ),
            array(
                'imdb_id' => 'tt0031381',
                'eyebrow' => 'Oscar first',
                'meta'    => 'First Black winner — Best Supporting Actress, 1940',
                'body'    => 'Hattie McDaniel won for Gone with the Wind but had to sit at a segregated table at the ceremony. Historic and heartbreaking.',
            ),
            array(
                'imdb_id' => 'tt0887912',
                'eyebrow' => 'Oscar first',
                'meta'    => 'First woman to win Best Director, 2010',
                'body'    => 'Kathryn Bigelow became the first woman to win the directing prize after over eighty years of ceremonies. She beat her ex-husband James Cameron to do it.',
            ),
            array(
                'imdb_id' => 'tt0076759',
                'eyebrow' => 'Living legend',
                'meta'    => '54 nominations — second most in Oscar history',
                'body'    => 'Only Walt Disney has more nominations than John Williams. He has been scoring the sound of American cinema for over five decades and counting.',
            ),
            array(
                'imdb_id' => 'tt0046250',
                'eyebrow' => 'Oscar history',
                'meta'    => 'Blacklist era — fake names and stand-ins',
                'body'    => 'Dalton Trumbo won twice under fake names. Carl Foreman and Michael Wilson were credited as a French author who couldn\'t speak English. The Academy later corrected the record.',
            ),
            array(
                'imdb_id' => 'tt0062994',
                'eyebrow' => 'Oscar rarity',
                'meta'    => 'Best Actress tie — Streisand and Hepburn, 1969',
                'body'    => 'One of the rarest results in Oscar history. Barbra Streisand and Katharine Hepburn tied for Best Actress. Both took home a trophy that year.',
            ),
            array(
                'imdb_id' => 'tt0469494',
                'eyebrow' => 'Oscar record',
                'meta'    => 'The only three-time Best Actor winner',
                'body'    => 'Daniel Day-Lewis won for My Left Foot, There Will Be Blood, and Lincoln. No other actor has won the lead category three times. His method and his results remain unmatched.',
            ),
            array(
                'imdb_id' => 'tt0029583',
                'eyebrow' => 'All-time record',
                'meta'    => '22 competitive wins from 59 nominations',
                'body'    => 'Walt Disney is the most awarded individual in Oscar history. His dominance across animation and short subjects remains unmatched by any single person.',
            ),
            array(
                'imdb_id' => 'tt0043278',
                'eyebrow' => 'Oscar dynasty',
                'meta'    => 'The Newman family — 95 nominations and counting',
                'body'    => 'Alfred (43), Lionel (11), Thomas (15), Randy (22), and others. No family has left a deeper mark on the Oscar music categories.',
            ),
        );

        foreach ( $curated_facts as $fentry ) {
            $fstory = $build_lore_story( $fentry['imdb_id'] );
            if ( ! empty( $fstory ) ) {
                $push_lore_card( $fstory, $fentry['eyebrow'], $fentry['meta'], $fentry['body'] );
            } else {
                // Film not in Oscar database — get poster from TMDB directly.
                $fvisual = array();
                if ( class_exists( 'Academy_Awards_Table' ) ) {
                    $faat = Academy_Awards_Table::get_instance();
                    if ( $faat && method_exists( $faat, 'get_title_visual_package' ) ) {
                        $fvisual = $faat->get_title_visual_package( $fentry['imdb_id'], 'medium_large' );
                    }
                }
                $pool[] = array(
                    'title'   => $fentry['meta'],
                    'year'    => '',
                    'url'     => home_url( '/oscars/' ),
                    'visual'  => $fvisual,
                    'eyebrow' => $fentry['eyebrow'],
                    'meta'    => $fentry['meta'],
                    'body'    => $fentry['body'],
                );
            }
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

            $lore_visible = 8;
            for ( $i = 0; $i < $pool_count && count( $selected ) < $lore_visible; $i++ ) {
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
    <?php if ( $show_home_hero && ! empty( $hero_review_posts ) ) : ?>
    <?php
    /* ── Review Poster Carousel hero ─────────────────────────────────
       Same carousel format as featured reviews at the bottom,
       pulling published + pending reviews with posters.               */
    ?>
    <section class="lunara-home-hero lunara-home-slot-hero lunara-hero-carousel-section" aria-label="Featured reviews" data-lunara-carousel data-lunara-carousel-autoplay="<?php echo absint( get_theme_mod( 'lunara_home_hero_autoplay', 4000 ) ); ?>">
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker"><?php echo esc_html( $featured_reviews_kicker ); ?></p>
                <h1 class="lunara-home-section-title"><?php echo esc_html( $featured_reviews_heading ); ?></h1>
            </div>
            <?php if ( count( $hero_review_posts ) > 1 ) : ?>
                <div class="lunara-poster-carousel-controls">
                    <button type="button" class="lunara-poster-carousel-btn lunara-poster-carousel-prev" data-lunara-carousel-prev aria-label="Previous reviews">&#8592;</button>
                    <button type="button" class="lunara-poster-carousel-btn lunara-poster-carousel-next" data-lunara-carousel-next aria-label="Next reviews">&#8594;</button>
                </div>
            <?php endif; ?>
        </div>
        <div class="lunara-poster-carousel-wrap lunara-hero-carousel-wrap">
            <div class="lunara-poster-carousel-track" data-lunara-carousel-track>
                <?php foreach ( $hero_review_posts as $hero_post ) : ?>
                    <?php
                    setup_postdata( $hero_post );
                    $hc_id    = intval( $hero_post->ID );
                    $hc_year  = get_post_meta( $hc_id, '_lunara_year', true );
                    $hc_score = get_post_meta( $hc_id, '_lunara_score', true );
                    $hc_pub   = ( get_post_status( $hc_id ) === 'publish' );
                    $hc_excerpt = function_exists( 'lunara_card_excerpt' ) ? lunara_card_excerpt( $hc_id, 12 ) : wp_trim_words( get_the_excerpt( $hc_id ), 12 );
                    ?>
                    <article class="lunara-poster-card lunara-poster-card-hero<?php echo $hc_pub ? '' : ' is-upcoming'; ?>">
                        <a class="lunara-poster-card-link" href="<?php echo $hc_pub ? esc_url( get_permalink( $hc_id ) ) : '#'; ?>"<?php echo $hc_pub ? '' : ' tabindex="-1" aria-disabled="true"'; ?>>
                            <div class="lunara-poster-card-image-wrap">
                                <span class="lunara-poster-card-title-overlay" aria-hidden="true"><?php echo esc_html( get_the_title( $hc_id ) ); ?></span>
                                <?php if ( has_post_thumbnail( $hc_id ) ) : ?>
                                    <?php echo get_the_post_thumbnail( $hc_id, 'large', array( 'class' => 'lunara-poster-card-image', 'loading' => 'lazy' ) ); ?>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-poster-card-copy">
                                <p class="lunara-poster-card-kicker"><?php echo $hc_pub ? 'LUNARA REVIEW' : 'COMING SOON'; ?></p>
                                <h3 class="lunara-screen-reader-text"><?php echo esc_html( get_the_title( $hc_id ) ); ?></h3>
                                <p class="lunara-poster-card-meta"><?php echo esc_html( $hc_year ); ?><?php if ( $hc_score ) : ?> <span class="lunara-inline-score"><?php echo wp_kses_post( lunara_render_stars( $hc_score ) ); ?></span><?php endif; ?></p>
                                <?php if ( ! empty( $hc_excerpt ) ) : ?>
                                    <p class="lunara-poster-card-excerpt"><?php echo esc_html( $hc_excerpt ); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach; wp_reset_postdata(); ?>
            </div>
        </div>
        <div class="lunara-home-hero-actions">
            <a class="lunara-btn" href="<?php echo esc_url( $primary_cta_url ); ?>"><?php echo esc_html( $primary_cta_label ); ?></a>
            <a class="lunara-btn lunara-btn-secondary" href="<?php echo esc_url( $secondary_cta_url ); ?>"><?php echo esc_html( $secondary_cta_label ); ?></a>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $show_latest_reviews && ! empty( $archive_review_posts ) ) : ?>
    <section class="lunara-home-section lunara-home-slot-latest-reviews lunara-latest-reviews-section" aria-label="Reviews">
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker">Lunara Film</p>
                <h2 class="lunara-home-section-title"><?php echo esc_html( get_theme_mod( 'lunara_home_latest_reviews_heading', 'Reviews' ) ); ?></h2>
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

    <?php
    // Removed 2026-04-19: duplicate Journal lane that queried `post_type => 'post'`.
    // The single source of truth is now the Dispatches section below, which is
    // configured to query the `journal` CPT via lunara_home_dispatches_query().
    ?>

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
                    $featured_excerpt = function_exists( 'lunara_card_excerpt' ) ? lunara_card_excerpt( $featured_post_id, 13 ) : wp_trim_words( get_the_excerpt( $featured_post_id ), 13 );
                    ?>
                    <article class="lunara-poster-card lunara-poster-card-featured">
                        <a class="lunara-poster-card-link" href="<?php the_permalink(); ?>">
                            <div class="lunara-poster-card-image-wrap">
                                <span class="lunara-poster-card-title-overlay" aria-hidden="true"><?php the_title(); ?></span>
                                <?php if ( has_post_thumbnail() ) : ?>
                                    <?php the_post_thumbnail( 'large', array( 'class' => 'lunara-poster-card-image', 'loading' => 'lazy' ) ); ?>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-poster-card-copy">
                                <p class="lunara-poster-card-kicker">LUNARA FILM REVIEW</p>
                                <h3 class="lunara-screen-reader-text"><?php the_title(); ?></h3>
                                <p class="lunara-poster-card-meta"><?php echo esc_html( $featured_year ); ?><?php if ( $featured_score ) : ?> <span class="lunara-inline-score"><?php echo wp_kses_post( lunara_render_stars( $featured_score ) ); ?></span><?php endif; ?></p>
                                <?php if ( ! empty( $featured_excerpt ) ) : ?>
                                    <p class="lunara-poster-card-excerpt"><?php echo esc_html( $featured_excerpt ); ?></p>
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
            $lead_type_label = $normalize_journal_lane_label( $lead_type_label );
            if ( ! empty( $lead_type_label ) ) {
                $dispatch_mix[ sanitize_title( $lead_type_label ) ] = $lead_type_label;
            }
        }

        foreach ( $dispatch_posts as $dispatch_mix_post ) {
            if ( ! ( $dispatch_mix_post instanceof WP_Post ) ) {
                continue;
            }

            $dispatch_type_label = function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $dispatch_mix_post->ID ) : 'Dispatch';
            $dispatch_type_label = $normalize_journal_lane_label( $dispatch_type_label );
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
    <section class="lunara-home-section lunara-home-slot-dispatch lunara-dispatches-section" aria-label="Journal">
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
            <?php if ( $dispatch_lead instanceof WP_Post ) :
                $lead_post_id   = $dispatch_lead->ID;
                // Try to split the lead journal post into per-H2 item cards.
                // Falls back to the classic single-card lead if the post has
                // no H2 sections (or split helper unavailable).
                $journal_split_html = function_exists( 'lunara_render_journal_split_cards' )
                    ? lunara_render_journal_split_cards( $lead_post_id, 6 )
                    : '';
                ?>
            <?php if ( '' !== $journal_split_html ) : ?>
                    <article class="lunara-dispatch-lead lunara-dispatch-lead--split">
                        <header class="lunara-dispatch-split-header">
                            <p class="lunara-dispatch-lead-kicker">Signal Lead</p>
                            <p class="lunara-dispatch-type"><?php echo esc_html( $normalize_journal_lane_label( function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $lead_post_id ) : 'Dispatch' ) ); ?></p>
                            <h3 class="lunara-dispatch-lead-title">
                                <a href="<?php echo esc_url( get_permalink( $lead_post_id ) ); ?>"><?php echo esc_html( get_the_title( $lead_post_id ) ); ?></a>
                            </h3>
                            <div class="lunara-dispatch-lead-meta">
                                <span><?php echo esc_html( get_the_date( 'F j, Y', $lead_post_id ) ); ?></span>
                                <a class="lunara-dispatch-meta-link" href="<?php echo esc_url( get_permalink( $lead_post_id ) ); ?>">Open full entry →</a>
                            </div>
                        </header>
                        <?php echo $journal_split_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </article>
                <?php else :
                    setup_postdata( $dispatch_lead );
                    ?>
                    <article class="lunara-dispatch-lead">
                        <a class="lunara-dispatch-lead-link" href="<?php echo esc_url( get_permalink( $lead_post_id ) ); ?>">
                            <div class="lunara-dispatch-lead-media">
                                <?php
                                // Use the journal card-image helper which respects the __card override.
                                $lead_card_img_html = function_exists( 'lunara_journal_card_image_html' )
                                    ? lunara_journal_card_image_html( $lead_post_id, 'large', 'lunara-dispatch-lead-image' )
                                    : '';
                                if ( '' !== $lead_card_img_html ) :
                                    echo $lead_card_img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                else : ?>
                                    <div class="lunara-dispatch-lead-placeholder">
                                        <span><?php echo esc_html( $normalize_journal_lane_label( function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $lead_post_id ) : 'Dispatch' ) ); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-dispatch-lead-copy">
                                <p class="lunara-dispatch-lead-kicker">Signal Lead</p>
                                <p class="lunara-dispatch-type"><?php echo esc_html( $normalize_journal_lane_label( function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $lead_post_id ) : 'Dispatch' ) ); ?></p>
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
                                <?php
                                $rail_card_img_html = function_exists( 'lunara_journal_card_image_html' )
                                    ? lunara_journal_card_image_html( $dispatch_post_id, 'medium_large', 'lunara-dispatch-rail-thumb' )
                                    : '';
                                if ( '' !== $rail_card_img_html ) :
                                    echo $rail_card_img_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                else :
                                ?>
                                    <div class="lunara-dispatch-rail-thumb-placeholder"></div>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-dispatch-rail-copy">
                                <p class="lunara-dispatch-type"><?php echo esc_html( $normalize_journal_lane_label( function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $dispatch_post_id ) : 'Dispatch' ) ); ?></p>
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

    <?php
    /* ── Ceremony Winners Strip ──────────────────────────────────────
       12 prestige categories from the latest ceremony, poster-led.  */
    $winners_categories = array(
        'BEST PICTURE',
        'DIRECTING',
        'ACTOR IN A LEADING ROLE',
        'ACTRESS IN A LEADING ROLE',
        'ACTOR IN A SUPPORTING ROLE',
        'ACTRESS IN A SUPPORTING ROLE',
        'WRITING (Original Screenplay)',
        'WRITING (Adapted Screenplay)',
        'VISUAL EFFECTS',
        'CINEMATOGRAPHY',
        'FILM EDITING',
        'MUSIC (Original Score)',
    );
    $aat_instance   = class_exists( 'Academy_Awards_Table' ) ? Academy_Awards_Table::get_instance() : null;
    $ceremony_label = ! empty( $snapshot['ceremony_label'] ) ? $snapshot['ceremony_label'] : '';
    $ceremony_url   = ! empty( $snapshot['ceremony_url'] ) ? $snapshot['ceremony_url'] : home_url( '/oscars/' );
    $winner_cards   = array();

    // Winner photo overrides — curated photos from media library (HappyFiles "oscar related photos").
    // Maps category keywords to WordPress attachment IDs.
    $winner_photo_map = array(
        'BEST PICTURE'                    => 30253,
        'DIRECTING'                       => 30249,
        'ACTOR IN A LEADING ROLE'         => 30247,
        'ACTRESS IN A LEADING ROLE'       => 30251,
        'ACTOR IN A SUPPORTING ROLE'      => 30255,
        'ACTRESS IN A SUPPORTING ROLE'    => 30256,
        'WRITING (Original Screenplay)'   => 30252,
        'WRITING (Adapted Screenplay)'    => 28520,
        'CINEMATOGRAPHY'                  => 30248,
        'FILM EDITING'                    => 30250,
        'MUSIC (Original Score)'          => 30254,
    );

    if ( ! empty( $snapshot['winner_map'] ) && is_array( $snapshot['winner_map'] ) ) {
        foreach ( $winners_categories as $wcat ) {
            $wcat_upper = strtoupper( $wcat );
            $entry = null;
            foreach ( $snapshot['winner_map'] as $key => $val ) {
                if ( strtoupper( $key ) === $wcat_upper ) {
                    $entry = $val;
                    break;
                }
            }
            if ( $entry ) {
                $w_visual = array();

                // Prefer curated winner photo over TMDB poster.
                $photo_id = isset( $winner_photo_map[ $wcat ] ) ? $winner_photo_map[ $wcat ] : 0;
                if ( $photo_id > 0 ) {
                    $photo_url = wp_get_attachment_image_url( $photo_id, 'medium_large' );
                    if ( $photo_url ) {
                        $photo_alt = ! empty( $entry['name'] ) ? $entry['name'] : ( $entry['film'] ?? '' );
                        $w_visual['poster_url'] = $photo_url;
                        $w_visual['poster_html'] = sprintf(
                            '<img src="%s" alt="%s" loading="lazy" class="lunara-winner-photo" />',
                            esc_url( $photo_url ),
                            esc_attr( $photo_alt )
                        );
                    }
                }

                // Fall back to TMDB poster if no curated photo.
                if ( empty( $w_visual['poster_url'] ) && $aat_instance && method_exists( $aat_instance, 'get_title_visual_package' ) && ! empty( $entry['film_id'] ) ) {
                    $w_visual = $aat_instance->get_title_visual_package( $entry['film_id'], 'medium' );
                }

                $entry['_visual'] = $w_visual;
                $winner_cards[]   = $entry;
            }
        }
    }
    ?>
    <?php if ( ! empty( $winner_cards ) ) : ?>
    <section class="lunara-home-section lunara-home-slot-database lunara-ceremony-winners-section" aria-label="Ceremony Winners">
        <div class="lunara-home-section-head">
            <div>
                <p class="lunara-home-section-kicker">Oscar Ledger</p>
                <h2 class="lunara-home-section-title"><?php echo esc_html( $ceremony_label ?: 'Latest Ceremony Winners' ); ?></h2>
            </div>
            <a class="lunara-section-link" href="<?php echo esc_url( $ceremony_url ); ?>">Full Ceremony</a>
        </div>
        <div class="lunara-ceremony-winners-grid">
            <?php foreach ( $winner_cards as $wcard ) :
                $w_vis = $wcard['_visual'];
            ?>
                <article class="lunara-ceremony-winner-card<?php echo ! empty( $w_vis['poster_url'] ) || ! empty( $w_vis['poster_html'] ) ? ' has-poster' : ''; ?>">
                    <a class="lunara-ceremony-winner-link" href="<?php echo esc_url( ! empty( $wcard['film_url'] ) ? $wcard['film_url'] : $ceremony_url ); ?>">
                        <?php if ( ! empty( $w_vis['poster_html'] ) ) : ?>
                            <div class="lunara-ceremony-winner-poster"><?php echo $w_vis['poster_html']; // phpcs:ignore ?></div>
                        <?php elseif ( ! empty( $w_vis['poster_url'] ) ) : ?>
                            <div class="lunara-ceremony-winner-poster"><img src="<?php echo esc_url( $w_vis['poster_url'] ); ?>" alt="<?php echo esc_attr( $wcard['film'] ?? '' ); ?> poster" loading="lazy" /></div>
                        <?php endif; ?>
                        <div class="lunara-ceremony-winner-copy">
                            <p class="lunara-ceremony-winner-category"><?php echo esc_html( $wcard['category_label'] ?? $wcard['canonical_category'] ?? '' ); ?></p>
                            <h3 class="lunara-ceremony-winner-name"><?php echo esc_html( ! empty( $wcard['name'] ) ? $wcard['name'] : $wcard['film'] ); ?></h3>
                            <?php if ( ! empty( $wcard['name'] ) && ! empty( $wcard['film'] ) ) : ?>
                                <p class="lunara-ceremony-winner-film"><?php echo esc_html( $wcard['film'] ); ?></p>
                            <?php endif; ?>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if ( $show_home_oscar_story_lane && ! empty( $home_oscar_story_cards ) ) : ?>
    <section class="lunara-home-section lunara-home-slot-deep-cuts lunara-deep-cut-section lunara-home-oscar-story-section" aria-label="Oscar Lore"<?php echo ! empty( $home_oscar_story_cards ) ? ' data-lunara-carousel data-lunara-carousel-autoplay="' . absint( get_theme_mod( 'lunara_home_lore_autoplay', 7600 ) ) . '"' : ''; ?>>
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
                        <?php
                        $visual       = ! empty( $card['visual'] ) && is_array( $card['visual'] ) ? $card['visual'] : array();
                        $lore_backdrop = trim( (string) ( $visual['backdrop_url'] ?? $visual['backdrop_full'] ?? '' ) );
                        ?>
                        <article class="lunara-ledger-story-card lunara-lore-card"
                                 data-lunara-lore-backdrop="<?php echo esc_url( $lore_backdrop ); ?>">
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
