<?php
/**
 * Frontend — footer, navigation, content filters, search, and animations.
 *
 * @package Lunara_Film
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Footer fallback.
 */
function lunara_footer_menu_fallback() {
    echo '<ul class="lunara-footer-fallback">';
    echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">Home</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/reviews/' ) ) . '">Reviews</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/oscars/' ) ) . '">Oscar Ledger</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">About</a></li>';
    echo '</ul>';
}

/**
 * Optional legacy Lunara footer output.
 *
 * Blocksy should own the live footer shell by default. This renderer remains as
 * a fallback path that can be re-enabled through a filter if needed during the
 * transition.
 */
function lunara_render_custom_footer() {
    $show_logo  = get_theme_mod( 'lunara_footer_show_logo', true );
    $tagline    = get_theme_mod( 'lunara_footer_tagline', 'Film criticism and a living Oscar ledger.' );
    $col1_head  = get_theme_mod( 'lunara_footer_col1_heading', 'Editorial' );
    $col2_head  = get_theme_mod( 'lunara_footer_col2_heading', 'Oscar Ledger' );
    $col3_head  = get_theme_mod( 'lunara_footer_col3_heading', 'Utility' );
    $copyright  = get_theme_mod( 'lunara_footer_copyright', 'Lunara Film' );
    ?>
    <footer class="lunara-site-footer" role="contentinfo">
        <div class="lunara-footer-inner">
            <!-- Zone 1: Branded close -->
            <div class="lunara-footer-brand">
                <?php if ( $show_logo ) :
                    $custom_logo_id = get_theme_mod( 'custom_logo' );
                    if ( $custom_logo_id ) :
                        echo wp_get_attachment_image( $custom_logo_id, 'medium', false, array(
                            'class'   => 'lunara-footer-logo',
                            'loading' => 'lazy',
                            'alt'     => get_bloginfo( 'name' ) . ' logo',
                        ) );
                    else : ?>
                        <span class="lunara-footer-wordmark"><?php bloginfo( 'name' ); ?></span>
                    <?php endif;
                endif; ?>
                <?php if ( $tagline ) : ?>
                    <p class="lunara-footer-tagline"><?php echo esc_html( $tagline ); ?></p>
                <?php endif; ?>
            </div>

            <!-- Zone 2: Navigation columns -->
            <nav class="lunara-footer-nav-grid" aria-label="<?php esc_attr_e( 'Footer navigation', 'lunara-film' ); ?>">
                <div class="lunara-footer-nav-col">
                    <?php if ( $col1_head ) : ?>
                        <h4 class="lunara-footer-col-heading"><?php echo esc_html( $col1_head ); ?></h4>
                    <?php endif; ?>
                    <?php wp_nav_menu( array(
                        'theme_location' => 'footer-editorial',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => 'lunara_footer_editorial_fallback',
                    ) ); ?>
                </div>
                <div class="lunara-footer-nav-col">
                    <?php if ( $col2_head ) : ?>
                        <h4 class="lunara-footer-col-heading"><?php echo esc_html( $col2_head ); ?></h4>
                    <?php endif; ?>
                    <?php wp_nav_menu( array(
                        'theme_location' => 'footer-oscars',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => 'lunara_footer_oscars_fallback',
                    ) ); ?>
                </div>
                <div class="lunara-footer-nav-col">
                    <?php if ( $col3_head ) : ?>
                        <h4 class="lunara-footer-col-heading"><?php echo esc_html( $col3_head ); ?></h4>
                    <?php endif; ?>
                    <?php wp_nav_menu( array(
                        'theme_location' => 'footer-utility',
                        'container'      => false,
                        'depth'          => 1,
                        'fallback_cb'    => 'lunara_footer_utility_fallback',
                    ) ); ?>
                </div>
            </nav>

            <!-- Zone 3: Utility row -->
            <div class="lunara-footer-utility">
                <span class="lunara-footer-copyright">&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php echo esc_html( $copyright ); ?></span>
                <?php $privacy_url = get_privacy_policy_url(); ?>
                <?php if ( $privacy_url ) : ?>
                    <span class="lunara-footer-legal">
                        <a href="<?php echo esc_url( $privacy_url ); ?>"><?php esc_html_e( 'Privacy', 'lunara-film' ); ?></a>
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </footer>
    <?php
}

if ( apply_filters( 'lunara_use_custom_footer', false ) ) {
    add_action( 'wp_footer', 'lunara_render_custom_footer', 1 );
}

/* Footer menu fallbacks */
function lunara_footer_editorial_fallback() {
    $journal_url   = lunara_home_dispatch_archive_url();
    $journal_label = 'Journal';
    $posts_page_id = absint( get_option( 'page_for_posts' ) );
    $news_url      = home_url( '/news/' );

    if ( $posts_page_id > 0 ) {
        $posts_page_title = trim( wp_strip_all_tags( get_the_title( $posts_page_id ) ) );
        if ( '' !== $posts_page_title ) {
            $journal_label = $posts_page_title;
        }
    }

    echo '<ul class="menu">';
    echo '<li><a href="' . esc_url( home_url( '/reviews/' ) ) . '">Reviews</a></li>';
    echo '<li><a href="' . esc_url( $journal_url ) . '">' . esc_html( $journal_label ) . '</a></li>';
    if ( untrailingslashit( $journal_url ) !== untrailingslashit( $news_url ) && 'Journal' !== $journal_label ) {
        echo '<li><a href="' . esc_url( $news_url ) . '">Journal</a></li>';
    }
    echo '<li><a href="' . esc_url( home_url( '/about/' ) ) . '">About</a></li>';
    echo '</ul>';
}

function lunara_footer_oscars_fallback() {
    echo '<ul class="menu">';
    echo '<li><a href="' . esc_url( home_url( '/oscars/' ) ) . '">Ledger</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/oscars/categories/' ) ) . '">Categories</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/oscars/ceremonies/' ) ) . '">Ceremonies</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/oscars/about/' ) ) . '">About the Ledger</a></li>';
    echo '</ul>';
}

function lunara_footer_utility_fallback() {
    echo '<ul class="menu">';
    echo '<li><a href="' . esc_url( home_url( '/?s=' ) ) . '">Search</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/contact/' ) ) . '">Contact</a></li>';
    echo '<li><a href="' . esc_url( get_feed_link() ) . '">RSS</a></li>';
    echo '</ul>';
}

/**
 * Map primary-nav utility paths to reliable fallback labels.
 */
function lunara_primary_menu_fallback_label_for_path( $path ) {
    $label_map = array(
        '/oscars/categories-page'         => 'Categories',
        '/oscars/categories'              => 'Categories',
        '/oscars/about-this-database-page' => 'About the Ledger',
        '/oscars/about'                   => 'About the Ledger',
        '/awards-tracker'                 => 'Awards Tracker',
        '/search'                         => 'Search',
    );

    if ( isset( $label_map[ $path ] ) ) {
        return $label_map[ $path ];
    }

    return '';
}

/**
 * Supply readable labels when a primary-nav item is configured as icon-only.
 */
function lunara_primary_menu_item_title_fallback( $title, $item, $args, $depth ) {
    if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
        return $title;
    }

    $plain_title = trim( wp_strip_all_tags( html_entity_decode( (string) $title, ENT_QUOTES, 'UTF-8' ) ) );
    if ( '' !== $plain_title ) {
        return $title;
    }

    $item_url = isset( $item->url ) ? (string) $item->url : '';
    if ( '' === $item_url ) {
        return $title;
    }

    $path  = wp_parse_url( $item_url, PHP_URL_PATH );
    $path  = is_string( $path ) ? untrailingslashit( $path ) : '';
    $label = lunara_primary_menu_fallback_label_for_path( $path );

    if ( '' !== $label ) {
        return esc_html( $label );
    }

    return $title;
}
add_filter( 'nav_menu_item_title', 'lunara_primary_menu_item_title_fallback', 10, 4 );

/**
 * Normalize icon-only primary-menu items before the walker renders them.
 */
function lunara_primary_menu_object_title_fallback( $sorted_menu_items, $args ) {
    if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location || ! is_array( $sorted_menu_items ) ) {
        return $sorted_menu_items;
    }

    foreach ( $sorted_menu_items as $item ) {
        if ( ! is_object( $item ) ) {
            continue;
        }

        $current_title = isset( $item->title ) ? trim( wp_strip_all_tags( html_entity_decode( (string) $item->title, ENT_QUOTES, 'UTF-8' ) ) ) : '';
        if ( '' !== $current_title ) {
            continue;
        }

        $item_url = isset( $item->url ) ? (string) $item->url : '';
        if ( '' === $item_url ) {
            continue;
        }

        $path  = wp_parse_url( $item_url, PHP_URL_PATH );
        $path  = is_string( $path ) ? untrailingslashit( $path ) : '';
        $label = lunara_primary_menu_fallback_label_for_path( $path );

        if ( '' === $label ) {
            continue;
        }

        $item->title = $label;

        if ( isset( $item->post_title ) && '' === trim( (string) $item->post_title ) ) {
            $item->post_title = $label;
        }
    }

    return $sorted_menu_items;
}
add_filter( 'wp_nav_menu_objects', 'lunara_primary_menu_object_title_fallback', 10, 2 );

/**
 * Ensure icon-only primary menu items still output a visible text label.
 */
function lunara_primary_menu_start_el_fallback( $item_output, $item, $depth, $args ) {
    if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
        return $item_output;
    }

    $item_url = isset( $item->url ) ? (string) $item->url : '';
    if ( '' === $item_url ) {
        return $item_output;
    }

    $path  = wp_parse_url( $item_url, PHP_URL_PATH );
    $path  = is_string( $path ) ? untrailingslashit( $path ) : '';
    $label = lunara_primary_menu_fallback_label_for_path( $path );
    if ( '' === $label || false !== strpos( $item_output, $label ) ) {
        return $item_output;
    }

    if ( ! preg_match( '/(<a\b[^>]*>)(.*?)(<\/a>)/is', $item_output, $matches ) ) {
        return $item_output;
    }

    $inner_html = preg_replace( '/<!--.*?-->/s', '', $matches[2] );
    $inner_html = preg_replace( '/<svg\b.*?<\/svg>/is', '', $inner_html );
    $plain_html = trim( wp_strip_all_tags( $inner_html ) );
    if ( '' !== $plain_html ) {
        return $item_output;
    }

    $fallback_markup = '<span class="lunara-menu-fallback-label">' . esc_html( $label ) . '</span>';
    return $matches[1] . $matches[2] . $fallback_markup . $matches[3];
}
add_filter( 'walker_nav_menu_start_el', 'lunara_primary_menu_start_el_fallback', 10, 4 );

/**
 * Review metadata prepended above single review content.
 */
function lunara_prepend_review_metadata( $content ) {
    if ( ! is_singular( 'review' ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    $director = get_post_meta( get_the_ID(), '_lunara_director', true );
    $year     = get_post_meta( get_the_ID(), '_lunara_year', true );
    $runtime  = get_post_meta( get_the_ID(), '_lunara_runtime', true );
    $studio   = get_post_meta( get_the_ID(), '_lunara_studio', true );

    $items = array();
    if ( $director ) $items[] = '<span><strong>Director:</strong> ' . esc_html( $director ) . '</span>';
    if ( $year )     $items[] = '<span><strong>Year:</strong> ' . esc_html( $year ) . '</span>';
    if ( $runtime )  $items[] = '<span><strong>Runtime:</strong> ' . esc_html( $runtime ) . '</span>';
    if ( $studio )   $items[] = '<span><strong>Studio:</strong> ' . esc_html( $studio ) . '</span>';

    if ( empty( $items ) ) {
        return $content;
    }

    $bar = '<div class="lunara-review-metadata">' . implode( '', $items ) . '</div>';
    return $bar . $content;
}
add_filter( 'the_content', 'lunara_prepend_review_metadata', 5 );

/**
 * Drop malformed srcset candidates injected by CDN/image optimizers.
 *
 * Some homepage poster images receive an extra candidate like:
 *   "...&_jb=custom 1440.00"
 * which is missing a valid width or density descriptor. Browsers then emit
 * warnings and may ignore the whole srcset. We keep only candidates with a
 * standard trailing descriptor.
 */
if ( ! function_exists( 'lunara_sanitize_srcset_value' ) ) {
    function lunara_sanitize_srcset_value( $srcset ) {
        $srcset = is_string( $srcset ) ? trim( $srcset ) : '';
        if ( '' === $srcset || false === strpos( $srcset, ',' ) ) {
            return $srcset;
        }

        $candidates = preg_split( '/,\s*(?=(?:https?:)?\/\/|\/)/', $srcset );
        if ( ! is_array( $candidates ) || empty( $candidates ) ) {
            return $srcset;
        }

        $valid = array();
        foreach ( $candidates as $candidate ) {
            $candidate = trim( (string) $candidate );
            if ( '' === $candidate ) {
                continue;
            }

            if ( preg_match( '/\s+\d+w$/', $candidate ) || preg_match( '/\s+\d+(?:\.\d+)?x$/', $candidate ) ) {
                $valid[] = $candidate;
            }
        }

        if ( empty( $valid ) ) {
            return '';
        }

        return implode( ', ', $valid );
    }
}

/**
 * Sanitize attachment image attributes after WordPress/CDN filters run.
 */
if ( ! function_exists( 'lunara_sanitize_attachment_image_attributes' ) ) {
    function lunara_sanitize_attachment_image_attributes( $attr ) {
        if ( empty( $attr['srcset'] ) ) {
            return $attr;
        }

        $sanitized = lunara_sanitize_srcset_value( (string) $attr['srcset'] );
        if ( '' === $sanitized ) {
            unset( $attr['srcset'], $attr['sizes'] );
            return $attr;
        }

        $attr['srcset'] = $sanitized;
        if ( false === strpos( $sanitized, ',' ) ) {
            unset( $attr['sizes'] );
        }

        return $attr;
    }
}
add_filter( 'wp_get_attachment_image_attributes', 'lunara_sanitize_attachment_image_attributes', 999 );

/**
 * Sanitize content image tags that may bypass wp_get_attachment_image().
 */
if ( ! function_exists( 'lunara_sanitize_content_image_tag' ) ) {
    function lunara_sanitize_content_image_tag( $filtered_image ) {
        $filtered_image = is_string( $filtered_image ) ? $filtered_image : '';
        if ( '' === $filtered_image || false === strpos( $filtered_image, 'srcset=' ) ) {
            return $filtered_image;
        }

        return preg_replace_callback(
            '/\s(srcset)=("|\')(.*?)\2/i',
            static function ( $matches ) {
                $sanitized = lunara_sanitize_srcset_value( html_entity_decode( (string) $matches[3], ENT_QUOTES, 'UTF-8' ) );
                if ( '' === $sanitized ) {
                    return '';
                }

                return ' ' . $matches[1] . '=' . $matches[2] . esc_attr( $sanitized ) . $matches[2];
            },
            $filtered_image
        );
    }
}
add_filter( 'wp_content_img_tag', 'lunara_sanitize_content_image_tag', 999 );

/**
 * Make search reflect the real Lunara content universe.
 */
if ( ! function_exists( 'lunara_configure_main_search_query' ) ) {
    function lunara_configure_main_search_query( $query ) {
        if ( ! ( $query instanceof WP_Query ) || is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
            return;
        }

        $query->set( 'post_type', array( 'review', 'post', 'page' ) );
        $query->set( 'post_status', 'publish' );
        $query->set( 'ignore_sticky_posts', true );
        $query->set( 'posts_per_page', 12 );
    }
}
add_action( 'pre_get_posts', 'lunara_configure_main_search_query' );

/**
 * Push exact and title-based matches higher in Lunara search results.
 */
if ( ! function_exists( 'lunara_boost_search_orderby' ) ) {
    function lunara_boost_search_orderby( $orderby, $query ) {
        if ( ! ( $query instanceof WP_Query ) || is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
            return $orderby;
        }

        global $wpdb;

        $search = trim( (string) $query->get( 's' ) );
        if ( '' === $search || ! ( $wpdb instanceof wpdb ) ) {
            return $orderby;
        }

        $like_any   = '%' . $wpdb->esc_like( $search ) . '%';
        $like_start = $wpdb->esc_like( $search ) . '%';
        $quoted_any = "'" . esc_sql( $like_any ) . "'";
        $quoted_start = "'" . esc_sql( $like_start ) . "'";
        $quoted_exact = "'" . esc_sql( $search ) . "'";

        $posts_table = $wpdb->posts;

        return "
            CASE
                WHEN {$posts_table}.post_title = {$quoted_exact} THEN 0
                WHEN {$posts_table}.post_title LIKE {$quoted_start} THEN 1
                WHEN {$posts_table}.post_title LIKE {$quoted_any} THEN 2
                WHEN {$posts_table}.post_excerpt LIKE {$quoted_any} THEN 3
                WHEN {$posts_table}.post_content LIKE {$quoted_any} THEN 4
                ELSE 5
            END ASC,
            CASE
                WHEN {$posts_table}.post_type = 'review' THEN 0
                WHEN {$posts_table}.post_type = 'post' THEN 1
                WHEN {$posts_table}.post_type = 'page' THEN 2
                ELSE 3
            END ASC,
            {$posts_table}.post_date DESC
        ";
    }
}
add_filter( 'posts_orderby', 'lunara_boost_search_orderby', 20, 2 );

/**
 * Build fast front-end search suggestions from posts/pages/reviews.
 */
if ( ! function_exists( 'lunara_get_post_search_suggestions' ) ) {
    function lunara_get_post_search_suggestions( $query_text, $limit = 6 ) {
        global $wpdb;

        $query_text = trim( (string) $query_text );
        $limit      = max( 1, intval( $limit ) );

        if ( '' === $query_text || ! ( $wpdb instanceof wpdb ) ) {
            return array();
        }

        $posts_table  = $wpdb->posts;
        $like_any     = '%' . $wpdb->esc_like( $query_text ) . '%';
        $like_start   = $wpdb->esc_like( $query_text ) . '%';
        $quoted_any   = "'" . esc_sql( $like_any ) . "'";
        $quoted_start = "'" . esc_sql( $like_start ) . "'";
        $quoted_exact = "'" . esc_sql( $query_text ) . "'";

        $sql = $wpdb->prepare(
              "SELECT ID, post_title, post_type, post_date
               FROM {$posts_table}
               WHERE post_status = 'publish'
                 AND post_type IN ('review','post','page')
                 AND post_title LIKE %s
               ORDER BY
                  CASE
                      WHEN post_title = {$quoted_exact} THEN 0
                      WHEN post_title LIKE {$quoted_start} THEN 1
                      WHEN post_title LIKE {$quoted_any} THEN 2
                      ELSE 3
                  END ASC,
                  CASE
                      WHEN post_type = 'review' THEN 0
                      WHEN post_type = 'post' THEN 1
                      WHEN post_type = 'page' THEN 2
                    ELSE 3
                END ASC,
                post_date DESC
             LIMIT %d",
            $like_any,
            $limit
        );

        $rows = $wpdb->get_results( $sql, ARRAY_A );
        if ( ! is_array( $rows ) || empty( $rows ) ) {
            return array();
        }

        $results = array();
        foreach ( $rows as $row ) {
            $post_id   = intval( $row['ID'] ?? 0 );
            $post_type = (string) ( $row['post_type'] ?? '' );
            $title     = trim( (string) ( $row['post_title'] ?? '' ) );
            if ( $post_id <= 0 ) {
                continue;
            }

            $score = function_exists( 'lunara_search_text_match_score' )
                ? lunara_search_text_match_score( $title, $query_text )
                : 0;

            if ( $score <= 0 ) {
                continue;
            }

            if ( 'review' === $post_type ) {
                $kicker = __( 'Review', 'lunara-film' );
            } elseif ( 'page' === $post_type ) {
                $kicker = __( 'Page', 'lunara-film' );
            } else {
                $kicker = function_exists( 'lunara_get_dispatch_type_label' ) ? lunara_get_dispatch_type_label( $post_id ) : __( 'Dispatch', 'lunara-film' );
            }

            $results[] = array(
                'kicker' => $kicker,
                'title'  => $title,
                'url'    => get_permalink( $post_id ),
                'score'  => $score,
            );
        }

        usort(
            $results,
            static function ( $left, $right ) {
                return intval( $right['score'] ?? 0 ) <=> intval( $left['score'] ?? 0 );
            }
        );

        return $results;
    }
}

/**
 * Normalize a label for typo-tolerant search recovery checks.
 */
if ( ! function_exists( 'lunara_normalize_search_recovery_label' ) ) {
    function lunara_normalize_search_recovery_label( $label ) {
        $label = strtolower( trim( (string) $label ) );
        $label = preg_replace( '/\(\d{4}\)/', '', $label );
        $label = preg_replace( '/[^a-z0-9]+/i', ' ', $label );
        $label = trim( preg_replace( '/\s+/', ' ', $label ) );

        return is_string( $label ) ? $label : '';
    }
}

/**
 * Pull typo-tolerant fallback routes when a search is weak or empty.
 */
if ( ! function_exists( 'lunara_get_search_recovery_routes' ) ) {
    function lunara_get_search_recovery_routes( $query_text, $limit = 6 ) {
        global $wpdb;

        $query_text = trim( (string) $query_text );
        $limit      = max( 1, intval( $limit ) );

        if ( '' === $query_text || ! ( $wpdb instanceof wpdb ) ) {
            return array();
        }

        $normalized_query = lunara_normalize_search_recovery_label( $query_text );
        if ( '' === $normalized_query ) {
            return array();
        }

        $seed = substr( str_replace( ' ', '', $normalized_query ), 0, 3 );
        if ( '' === $seed ) {
            return array();
        }

        $seed_like = '%' . $wpdb->esc_like( $seed ) . '%';
        $matches   = array();

        $push_match = static function ( $key, $match ) use ( &$matches ) {
            if ( empty( $match['score'] ) ) {
                return;
            }

            if ( ! isset( $matches[ $key ] ) || intval( $match['score'] ) > intval( $matches[ $key ]['score'] ) ) {
                $matches[ $key ] = $match;
            }
        };

        $score_label = static function ( $label ) use ( $normalized_query ) {
            $normalized_label = lunara_normalize_search_recovery_label( $label );
            if ( '' === $normalized_label ) {
                return 0;
            }

            if ( $normalized_label === $normalized_query ) {
                return 100;
            }

            if ( str_starts_with( $normalized_label, $normalized_query ) ) {
                return 94;
            }

            if ( str_contains( $normalized_label, $normalized_query ) ) {
                return 88;
            }

            $distance = levenshtein( $normalized_query, $normalized_label );
            $length   = max( strlen( $normalized_query ), strlen( $normalized_label ) );

            if ( $length <= 0 ) {
                return 0;
            }

            if ( $distance <= 2 ) {
                return 82 - ( $distance * 6 );
            }

            similar_text( $normalized_query, $normalized_label, $percent );
            if ( $percent >= 72 ) {
                return intval( round( $percent ) );
            }

            $query_tokens = array_values( array_filter( explode( ' ', $normalized_query ) ) );
            if ( count( $query_tokens ) > 1 ) {
                $all_tokens_near = true;
                foreach ( $query_tokens as $token ) {
                    if ( ! str_contains( $normalized_label, $token ) ) {
                        $all_tokens_near = false;
                        break;
                    }
                }
                if ( $all_tokens_near ) {
                    return 74;
                }
            }

            return 0;
        };

        $posts_table = $wpdb->posts;
        $post_sql    = $wpdb->prepare(
            "SELECT ID, post_title, post_type
             FROM {$posts_table}
             WHERE post_status = 'publish'
               AND post_type IN ('review','post','page')
               AND post_title LIKE %s
             ORDER BY post_date DESC
             LIMIT 30",
            $seed_like
        );
        $post_rows   = $wpdb->get_results( $post_sql, ARRAY_A );

        if ( is_array( $post_rows ) ) {
            foreach ( $post_rows as $row ) {
                $post_id   = intval( $row['ID'] ?? 0 );
                $post_type = (string) ( $row['post_type'] ?? '' );
                $title     = trim( (string) ( $row['post_title'] ?? '' ) );
                $score     = $score_label( $title );

                if ( $post_id <= 0 || $score < 72 ) {
                    continue;
                }

                if ( 'review' === $post_type ) {
                    $kicker = __( 'Review Route', 'lunara-film' );
                    $score += 6;
                } elseif ( 'page' === $post_type ) {
                    $kicker = __( 'Page Route', 'lunara-film' );
                } else {
                    $kicker = __( 'Dispatch Route', 'lunara-film' );
                    $score += 2;
                }

                $push_match(
                    'post:' . $post_id,
                    array(
                        'kicker' => $kicker,
                        'title'  => $title,
                        'meta'   => __( 'Closest Lunara route', 'lunara-film' ),
                        'url'    => get_permalink( $post_id ),
                        'score'  => $score,
                    )
                );
            }
        }

        $table_name   = $wpdb->prefix . 'academy_awards';
        $table_like   = $wpdb->esc_like( $table_name );
        $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_like ) );

        if ( $table_exists === $table_name ) {
            $oscars_sql = $wpdb->prepare(
                "SELECT film, film_id, nominees, nominee_ids, category, canonical_category, ceremony, year, winner
                 FROM {$table_name}
                 WHERE film LIKE %s
                    OR nominees LIKE %s
                 ORDER BY winner DESC, ceremony DESC, id DESC
                 LIMIT 60",
                $seed_like,
                $seed_like
            );
            $rows = $wpdb->get_results( $oscars_sql, ARRAY_A );

            if ( is_array( $rows ) ) {
                $base_url = home_url( '/oscars/' );
                if ( class_exists( 'Academy_Awards_Table' ) ) {
                    $aat = Academy_Awards_Table::get_instance();
                    if ( $aat && method_exists( $aat, 'get_entity_base_url' ) ) {
                        $base_url = $aat->get_entity_base_url();
                    }
                }
                $base_url = trailingslashit( $base_url );

                foreach ( $rows as $row ) {
                    $film    = trim( (string) ( $row['film'] ?? '' ) );
                    $film_id = strtolower( trim( (string) ( $row['film_id'] ?? '' ) ) );
                    $score   = $score_label( $film );

                    if ( '' !== $film && preg_match( '/^tt\d+$/', $film_id ) && $score >= 72 ) {
                        if ( intval( $row['winner'] ?? 0 ) > 0 ) {
                            $score += 2;
                        }

                        $push_match(
                            'title:' . $film_id,
                            array(
                                'kicker' => __( 'Closest Ledger Title', 'lunara-film' ),
                                'title'  => $film,
                                'meta'   => sprintf(
                                    /* translators: 1: ceremony number, 2: year */
                                    __( '%1$s Ceremony / %2$s', 'lunara-film' ),
                                    intval( $row['ceremony'] ?? 0 ),
                                    trim( (string) ( $row['year'] ?? '' ) )
                                ),
                                'url'    => $base_url . 'title/' . rawurlencode( $film_id ) . '/',
                                'score'  => $score,
                            )
                        );
                    }
                }
            }
        }

        uasort(
            $matches,
            static function ( $left, $right ) {
                return intval( $right['score'] ?? 0 ) <=> intval( $left['score'] ?? 0 );
            }
        );

        return array_slice( array_values( $matches ), 0, $limit );
    }
}

/**
 * AJAX suggestions endpoint for front-end search boxes.
 */
if ( ! function_exists( 'lunara_ajax_search_suggestions' ) ) {
    function lunara_ajax_search_suggestions() {
        $query_text = isset( $_REQUEST['q'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['q'] ) ) : '';
        $query_text = trim( $query_text );

        if ( '' === $query_text || strlen( $query_text ) < 2 ) {
            wp_send_json_success(
                array(
                    'items' => array(),
                )
            );
        }

        $items = array();
        $seen  = array();

        foreach ( lunara_get_post_search_suggestions( $query_text, 6 ) as $item ) {
            $url = isset( $item['url'] ) ? (string) $item['url'] : '';
            if ( '' === $url || isset( $seen[ $url ] ) ) {
                continue;
            }
            $seen[ $url ] = true;
            $score        = intval( $item['score'] ?? 0 );
            $kicker       = isset( $item['kicker'] ) ? (string) $item['kicker'] : '';
            if ( 'Review' === $kicker ) {
                $score += 8;
            } elseif ( 'Page' === $kicker ) {
                $score += 1;
            } else {
                $score += 4;
            }
            $items[]      = array(
                'kicker' => $kicker,
                'title'  => $item['title'] ?? '',
                'meta'   => $item['meta'] ?? '',
                'url'    => $url,
                'score'  => $score,
            );
        }

        foreach ( lunara_get_oscars_search_matches( $query_text, 4 ) as $item ) {
            $url = isset( $item['url'] ) ? (string) $item['url'] : '';
            if ( '' === $url || isset( $seen[ $url ] ) ) {
                continue;
            }
            $seen[ $url ] = true;
            $items[]      = array(
                'kicker' => $item['kicker'] ?? __( 'Oscar Match', 'lunara-film' ),
                'title'  => $item['title'] ?? '',
                'meta'   => $item['meta'] ?? '',
                'url'    => $url,
                'score'  => intval( $item['score'] ?? 0 ),
            );
        }

        usort(
            $items,
            static function ( $left, $right ) {
                return intval( $right['score'] ?? 0 ) <=> intval( $left['score'] ?? 0 );
            }
        );

        if ( empty( $items ) ) {
            foreach ( lunara_get_search_recovery_routes( $query_text, 6 ) as $item ) {
                $items[] = array(
                    'kicker' => $item['kicker'] ?? __( 'Closest Route', 'lunara-film' ),
                    'title'  => $item['title'] ?? '',
                    'meta'   => $item['meta'] ?? '',
                    'url'    => $item['url'] ?? '',
                    'score'  => intval( $item['score'] ?? 0 ),
                );
            }
        }

        wp_send_json_success(
            array(
                'items' => array_slice( $items, 0, 8 ),
            )
        );
    }
}
add_action( 'wp_ajax_lunara_search_suggestions', 'lunara_ajax_search_suggestions' );
add_action( 'wp_ajax_nopriv_lunara_search_suggestions', 'lunara_ajax_search_suggestions' );

/**
 * Lightweight live-search suggestions for front-end search inputs.
 */
if ( ! function_exists( 'lunara_render_live_search_script' ) ) {
    function lunara_render_live_search_script() {
        if ( is_admin() ) {
            return;
        }
        ?>
        <script id="lunara-live-search-script">
        document.addEventListener('DOMContentLoaded', function () {
            const forms = Array.from(document.querySelectorAll('form[role="search"], .search-form')).filter(function (form) {
                return form.querySelector('input[name="s"]');
            });
            if (!forms.length) return;

            const endpoint = <?php echo wp_json_encode( admin_url( 'admin-ajax.php?action=lunara_search_suggestions' ) ); ?>;

            forms.forEach(function (form) {
                const input = form.querySelector('input[name="s"]');
                if (!input || input.dataset.lunaraSuggestionsReady === '1') return;
                input.dataset.lunaraSuggestionsReady = '1';

                form.classList.add('lunara-live-search-form');
                let panel = form.querySelector('.lunara-live-search-panel');
                if (!panel) {
                    panel = document.createElement('div');
                    panel.className = 'lunara-live-search-panel';
                    panel.hidden = true;
                    form.appendChild(panel);
                }

                let controller = null;
                let activeIndex = -1;
                let currentItems = [];

                const closePanel = function () {
                    panel.hidden = true;
                    panel.innerHTML = '';
                    activeIndex = -1;
                    currentItems = [];
                };

                const renderPanel = function (items) {
                    currentItems = items.slice();
                    activeIndex = -1;

                    if (!items.length) {
                        closePanel();
                        return;
                    }

                    panel.innerHTML = items.map(function (item, index) {
                        const meta = item.meta ? '<span class="lunara-live-search-meta">' + item.meta + '</span>' : '';
                        return '<a class="lunara-live-search-item" href="' + item.url + '" data-index="' + index + '">' +
                            '<span class="lunara-live-search-kicker">' + item.kicker + '</span>' +
                            '<span class="lunara-live-search-title">' + item.title + '</span>' +
                            meta +
                        '</a>';
                    }).join('') +
                    '<a class="lunara-live-search-all-results" href="' + form.action + '?s=' + encodeURIComponent(input.value.trim()) + '">' +
                        '<span class="lunara-live-search-kicker"><?php echo esc_js( __( 'Search Desk', 'lunara-film' ) ); ?></span>' +
                        '<span class="lunara-live-search-title"><?php echo esc_js( __( 'See all results on the record', 'lunara-film' ) ); ?></span>' +
                    '</a>';
                    panel.hidden = false;
                };

                const updateActiveItem = function () {
                    const links = panel.querySelectorAll('.lunara-live-search-item');
                    links.forEach(function (link, index) {
                        link.classList.toggle('is-active', index === activeIndex);
                    });
                };

                const fetchSuggestions = function (value) {
                    if (controller) controller.abort();
                    controller = new AbortController();
                    const url = endpoint + '&q=' + encodeURIComponent(value);

                    fetch(url, {
                        credentials: 'same-origin',
                        signal: controller.signal
                    })
                    .then(function (response) { return response.json(); })
                    .then(function (payload) {
                        if (!payload || payload.success !== true || !payload.data || !Array.isArray(payload.data.items)) {
                            closePanel();
                            return;
                        }
                        renderPanel(payload.data.items);
                    })
                    .catch(function (error) {
                        if (error && error.name === 'AbortError') return;
                        closePanel();
                    });
                };

                let debounceTimer = null;
                input.addEventListener('input', function () {
                    const value = input.value.trim();
                    window.clearTimeout(debounceTimer);
                    if (value.length < 2) {
                        closePanel();
                        return;
                    }
                    debounceTimer = window.setTimeout(function () {
                        fetchSuggestions(value);
                    }, 140);
                });

                input.addEventListener('keydown', function (event) {
                    if (panel.hidden || !currentItems.length) return;

                    if (event.key === 'ArrowDown') {
                        event.preventDefault();
                        activeIndex = Math.min(activeIndex + 1, currentItems.length - 1);
                        updateActiveItem();
                    } else if (event.key === 'ArrowUp') {
                        event.preventDefault();
                        activeIndex = Math.max(activeIndex - 1, 0);
                        updateActiveItem();
                    } else if (event.key === 'Enter' && activeIndex >= 0) {
                        const link = panel.querySelector('.lunara-live-search-item[data-index="' + activeIndex + '"]');
                        if (link) {
                            event.preventDefault();
                            window.location.href = link.href;
                        }
                    } else if (event.key === 'Escape') {
                        closePanel();
                    }
                });

                form.addEventListener('focusout', function () {
                    window.setTimeout(function () {
                        if (!form.contains(document.activeElement)) {
                            closePanel();
                        }
                    }, 120);
                });

                document.addEventListener('click', function (event) {
                    if (!form.contains(event.target)) {
                        closePanel();
                    }
                });
            });
        });
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'lunara_render_live_search_script', 120 );

/**
 * Pull direct Oscars entity matches for the front-end search desk.
 */
if ( ! function_exists( 'lunara_get_oscars_search_matches' ) ) {
    function lunara_get_oscars_search_matches( $query_text, $limit = 6 ) {
        global $wpdb;

        $query_text = trim( (string) $query_text );
        $limit      = max( 1, intval( $limit ) );

        if ( '' === $query_text || ! ( $wpdb instanceof wpdb ) ) {
            return array();
        }

        $table_name = $wpdb->prefix . 'academy_awards';
        $table_like = $wpdb->esc_like( $table_name );
        $table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_like ) );

        if ( $table_exists !== $table_name ) {
            return array();
        }

        $search_term = '%' . $wpdb->esc_like( $query_text ) . '%';
        $sql         = $wpdb->prepare(
            "SELECT film, film_id, name, nominees, nominee_ids, canonical_category, category, ceremony, year, winner
             FROM {$table_name}
             WHERE film LIKE %s
                OR name LIKE %s
                OR nominees LIKE %s
                OR canonical_category LIKE %s
                OR category LIKE %s
             ORDER BY winner DESC, ceremony DESC, id DESC
             LIMIT 80",
            $search_term,
            $search_term,
            $search_term,
            $search_term,
            $search_term
        );
        $rows        = $wpdb->get_results( $sql, ARRAY_A );

        if ( ! is_array( $rows ) || empty( $rows ) ) {
            return array();
        }

        $base_url = home_url( '/oscars/' );
        if ( class_exists( 'Academy_Awards_Table' ) ) {
            $aat = Academy_Awards_Table::get_instance();
            if ( $aat && method_exists( $aat, 'get_entity_base_url' ) ) {
                $base_url = $aat->get_entity_base_url();
            }
        }
        $base_url = trailingslashit( $base_url );

        $normalized_query = strtolower( $query_text );
        $matches          = array();

        $push_match = static function ( $key, $match ) use ( &$matches ) {
            if ( ! isset( $match['score'] ) ) {
                return;
            }

            if ( ! isset( $matches[ $key ] ) || intval( $match['score'] ) > intval( $matches[ $key ]['score'] ) ) {
                $matches[ $key ] = $match;
            }
        };

        $map_pipe_values = static function ( $values, $ids ) {
            $value_parts = array_values( array_filter( array_map( 'trim', explode( '|', (string) $values ) ), 'strlen' ) );
            $id_parts    = array_values( array_filter( array_map( 'trim', explode( '|', (string) $ids ) ), 'strlen' ) );

            if ( empty( $value_parts ) || count( $value_parts ) !== count( $id_parts ) ) {
                return array();
            }

            return array_combine( $id_parts, $value_parts );
        };

        foreach ( $rows as $row ) {
            $film    = trim( (string) ( $row['film'] ?? '' ) );
            $film_id = strtolower( trim( (string) ( $row['film_id'] ?? '' ) ) );

            if ( '' !== $film && preg_match( '/^tt\d+$/', $film_id ) ) {
                $film_score = function_exists( 'lunara_search_text_match_score' )
                    ? lunara_search_text_match_score( $film, $query_text )
                    : 0;
                if ( $film_score > 0 && intval( $row['winner'] ?? 0 ) > 0 ) {
                    $film_score += 4;
                }
            } else {
                $film_score = 0;
            }

            if ( $film_score > 0 ) {
                $push_match(
                    'title:' . $film_id,
                    array(
                        'kicker' => __( 'Oscar Title Match', 'lunara-film' ),
                        'title'  => $film,
                        'meta'   => sprintf(
                            /* translators: 1: ceremony number, 2: year */
                            __( '%1$s Ceremony / %2$s', 'lunara-film' ),
                            intval( $row['ceremony'] ?? 0 ),
                            trim( (string) ( $row['year'] ?? '' ) )
                        ),
                        'url'    => $base_url . 'title/' . rawurlencode( $film_id ) . '/',
                        'score'  => $film_score,
                    )
                );
            }

            $nominee_map = $map_pipe_values( $row['nominees'] ?? '', $row['nominee_ids'] ?? '' );
            foreach ( $nominee_map as $entity_id => $entity_label ) {
                $entity_id    = strtolower( trim( (string) $entity_id ) );
                $entity_label = trim( (string) $entity_label );
                $entity_score = function_exists( 'lunara_search_text_match_score' )
                    ? lunara_search_text_match_score( $entity_label, $query_text )
                    : 0;
                if ( $entity_score <= 0 ) {
                    continue;
                }

                if ( preg_match( '/^nm\d+$/', $entity_id ) ) {
                    $entity_type   = 'name';
                    $entity_kicker = __( 'Oscar Person Match', 'lunara-film' );
                } elseif ( preg_match( '/^co\d+$/', $entity_id ) ) {
                    $entity_type   = 'company';
                    $entity_kicker = __( 'Oscar Company Match', 'lunara-film' );
                } else {
                    continue;
                }

                $push_match(
                    $entity_type . ':' . $entity_id,
                    array(
                        'kicker' => $entity_kicker,
                        'title'  => $entity_label,
                        'meta'   => trim( (string) ( $row['category'] ?? $row['canonical_category'] ?? '' ) ),
                        'url'    => $base_url . $entity_type . '/' . rawurlencode( $entity_id ) . '/',
                        'score'  => $entity_score + ( intval( $row['winner'] ?? 0 ) > 0 ? 2 : 0 ),
                    )
                );
            }
        }

        uasort(
            $matches,
            static function ( $left, $right ) {
                return intval( $right['score'] ?? 0 ) <=> intval( $left['score'] ?? 0 );
            }
        );

        return array_slice( array_values( $matches ), 0, $limit );
    }
}

/**
 * Score a text label against a search query for title-first suggestion ranking.
 */
if ( ! function_exists( 'lunara_search_text_match_score' ) ) {
    function lunara_search_text_match_score( $label, $query_text ) {
        $label      = strtolower( trim( (string) $label ) );
        $query_text = strtolower( trim( (string) $query_text ) );

        if ( '' === $label || '' === $query_text ) {
            return 0;
        }

        if ( $label === $query_text ) {
            return 120;
        }

        if ( str_starts_with( $label, $query_text ) ) {
            return 102;
        }

        $query_length = function_exists( 'mb_strlen' ) ? mb_strlen( $query_text ) : strlen( $query_text );
        $label_words  = preg_split( '/\s+/', $label );
        $word_count   = is_array( $label_words ) ? count( array_filter( $label_words ) ) : 0;

        $tokens = preg_split( '/\s+/', $query_text );
        $tokens = is_array( $tokens ) ? array_values( array_filter( $tokens ) ) : array();

        if ( preg_match( '/(^|[^a-z0-9])' . preg_quote( $query_text, '/' ) . '([^a-z0-9]|$)/i', $label ) ) {
            if ( count( $tokens ) > 1 || $word_count <= 5 ) {
                return 88;
            }

            return 0;
        }

        if ( count( $tokens ) > 1 ) {
            $all_tokens_present = true;
            foreach ( $tokens as $token ) {
                if ( false === strpos( $label, $token ) ) {
                    $all_tokens_present = false;
                    break;
                }
            }

            if ( $all_tokens_present ) {
                return 82;
            }
        }

        if ( $query_length < 3 ) {
            return 0;
        }

        if ( false !== strpos( $label, $query_text ) && $word_count <= 5 ) {
            return 70;
        }

        return 0;
    }
}


/**
 * Poster carousel controls.
 */
function lunara_output_carousel_controls_js() {
    if ( ! is_front_page() && ! is_page( 'oscars' ) && ! is_page_template( 'page-oscars.php' ) ) {
        return;
    }
    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        document.querySelectorAll('[data-lunara-carousel]').forEach(function(section) {
            const track = section.querySelector('[data-lunara-carousel-track]');
            const prev = section.querySelector('[data-lunara-carousel-prev]');
            const next = section.querySelector('[data-lunara-carousel-next]');
            if (!track) return;
            function amount() {
                const card = track.children[0];
                const styles = window.getComputedStyle(track);
                const gap = parseInt(styles.columnGap || styles.gap || 24, 10);
                return card ? card.offsetWidth + gap : 360;
            }
            function step(direction) {
                const distance = amount() * direction;
                const maxScroll = Math.max(0, track.scrollWidth - track.clientWidth);
                if (direction > 0 && track.scrollLeft + distance >= maxScroll - 6) {
                    track.scrollTo({ left: 0, behavior: 'smooth' });
                    return;
                }
                if (direction < 0 && track.scrollLeft <= 6) {
                    track.scrollTo({ left: maxScroll, behavior: 'smooth' });
                    return;
                }
                track.scrollBy({ left: distance, behavior: 'smooth' });
            }
            if (prev) {
                prev.addEventListener('click', function () {
                    step(-1);
                });
            }
            if (next) {
                next.addEventListener('click', function () {
                    step(1);
                });
            }

            const autoplay = parseInt(section.getAttribute('data-lunara-carousel-autoplay') || '0', 10);
            if (!reduceMotion && autoplay > 0 && window.innerWidth > 900) {
                let timer = null;
                const stop = function () {
                    if (timer) {
                        window.clearInterval(timer);
                        timer = null;
                    }
                };
                const start = function () {
                    stop();
                    timer = window.setInterval(function () {
                        step(1);
                    }, autoplay);
                };
                section.addEventListener('mouseenter', stop);
                section.addEventListener('mouseleave', start);
                section.addEventListener('focusin', stop);
                section.addEventListener('focusout', start);
                document.addEventListener('visibilitychange', function () {
                    if (document.hidden) {
                        stop();
                    } else {
                        start();
                    }
                });
                start();
            }
        });
    });
    </script>
    <?php
}
add_action( 'wp_footer', 'lunara_output_carousel_controls_js', 99 );

/**
 * Wave 2: Image fade-in on load.
 */
function lunara_output_image_fadein_js() {
    ?>
    <script>
    (function(){
        function markLoaded(img){img.classList.add('lunara-img-loaded');}
        function processImg(img){
            if(img.complete&&img.naturalWidth>0){markLoaded(img);return;}
            img.addEventListener('load',function(){markLoaded(img);});
            img.addEventListener('error',function(){markLoaded(img);});
        }
        var sels='.lunara-review-grid-poster,.lunara-review-feature-image,.lunara-poster-card-image,.lunara-dispatch-archive-thumb,.lunara-dispatch-lead-image,.lunara-home-pulse-poster,.aat-filmography-poster,.aat-entity-poster';
        document.querySelectorAll(sels).forEach(processImg);
        if(window.MutationObserver){
            new MutationObserver(function(mutations){
                mutations.forEach(function(m){
                    m.addedNodes.forEach(function(n){
                        if(n.nodeType===1){
                            if(n.matches&&n.matches(sels))processImg(n);
                            n.querySelectorAll&&n.querySelectorAll(sels).forEach(processImg);
                        }
                    });
                });
            }).observe(document.body,{childList:true,subtree:true});
        }
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'lunara_output_image_fadein_js', 100 );

/**
 * Wave 3: Scroll-triggered reveals.
 */
function lunara_output_scroll_reveal_js() {
    ?>
    <script>
    (function(){
        if(window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;
        // Only run scroll reveals on the front page — skip portal, plugin, single review, and other pages
        var isFrontPage=document.body.classList.contains('home')||document.querySelector('.lunara-front-page');
        var isPluginPage=document.querySelector('.aat-hub-page,.aat-entity-page');
        var revealSels=[];
        var staggerSels=[];
        if(isFrontPage){
            revealSels=[
                '.lunara-front-page>.lunara-home-section','.lunara-review-grid-card','.lunara-review-feature-card',
                '.lunara-poster-card','.lunara-ledger-card','.lunara-dispatch-archive-card'
            ];
            staggerSels=[
                '.lunara-review-grid','.lunara-review-related-grid'
            ];
        }
        // Entity pages get targeted reveals for stats/timeline only
        if(isPluginPage){
            revealSels=['.aat-entity-status-banner','.aat-stat','.aat-timeline-card'];
            staggerSels=['.aat-stats-bar','.aat-timeline-list'];
        }
        if(!revealSels.length)return;
        revealSels.forEach(function(s){
            document.querySelectorAll(s).forEach(function(el){el.classList.add('lunara-reveal');});
        });
        staggerSels.forEach(function(s){
            document.querySelectorAll(s).forEach(function(el){el.classList.add('lunara-reveal-stagger');});
        });
        var obs=new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
                if(entry.isIntersecting){
                    entry.target.classList.add('is-visible');
                    obs.unobserve(entry.target);
                }
            });
        },{threshold:0.08,rootMargin:'0px 0px -40px 0px'});
        document.querySelectorAll('.lunara-reveal').forEach(function(el){obs.observe(el);});
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'lunara_output_scroll_reveal_js', 101 );

// Sticky sidebar deferred to standalone theme (Tier 4).
// Blocksy's scroll container architecture defeats both CSS sticky and JS fixed positioning.
// The sidebar renders correctly in place; it just doesn't follow the reader yet.

/**
 * Wave 5: Oscar stats count-up animation.
 */
function lunara_output_stats_countup_js() {
    if ( ! is_singular() ) {
        return;
    }
    ?>
    <script>
    (function(){
        var stats=document.querySelectorAll('.aat-stat-number');
        if(!stats.length||window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;
        var obs=new IntersectionObserver(function(entries){
            entries.forEach(function(entry){
                if(!entry.isIntersecting)return;
                obs.unobserve(entry.target);
                var el=entry.target,text=el.textContent.trim();
                var match=text.match(/^([\d,]+)(.*)/);
                if(!match)return;
                var target=parseInt(match[1].replace(/,/g,''),10);
                var suffix=match[2];
                if(isNaN(target)||target===0)return;
                var duration=Math.min(1600,Math.max(600,target*8));
                var start=performance.now();
                function tick(now){
                    var t=Math.min(1,(now-start)/duration);
                    var ease=1-Math.pow(1-t,3);
                    var current=Math.round(target*ease);
                    el.textContent=current.toLocaleString()+suffix;
                    if(t<1)requestAnimationFrame(tick);
                }
                el.textContent='0'+suffix;
                requestAnimationFrame(tick);
            });
        },{threshold:0.3});
        stats.forEach(function(el){obs.observe(el);});
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'lunara_output_stats_countup_js', 102 );

/**
 * Suppress Blocksy's native footer via CSS so ours is the only one.
 */
function lunara_hide_blocksy_footer_css() {
}
add_action( 'wp_head', 'lunara_hide_blocksy_footer_css', 100 );

/**
 * Hero cinema crossfade: auto-rotating poster hero.
 */
function lunara_output_hero_cinema_js() {
    if ( ! is_front_page() ) {
        return;
    }
    ?>
    <script>
    (function(){
        var stage=document.querySelector('.lunara-hero-cinema-stage');
        if(!stage)return;
        var slides=stage.querySelectorAll('.lunara-hero-cinema-slide');
        var pips=stage.querySelectorAll('.lunara-hero-cinema-pip');
        if(slides.length<2)return;
        var current=0;
        var interval=5500;
        var timer=null;
        var paused=false;
        function goTo(idx){
            slides[current].classList.remove('is-active');
            slides[current].setAttribute('aria-hidden','true');
            if(pips[current])pips[current].classList.remove('is-active');
            current=idx%slides.length;
            slides[current].classList.add('is-active');
            slides[current].setAttribute('aria-hidden','false');
            if(pips[current])pips[current].classList.add('is-active');
        }
        function next(){goTo(current+1);}
        function startAuto(){
            if(timer||paused||window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;
            timer=setInterval(next,interval);
        }
        function stopAuto(){if(timer){clearInterval(timer);timer=null;}}
        stage.addEventListener('mouseenter',function(){paused=true;stopAuto();});
        stage.addEventListener('mouseleave',function(){paused=false;startAuto();});
        stage.addEventListener('focusin',function(){paused=true;stopAuto();});
        stage.addEventListener('focusout',function(){paused=false;startAuto();});
        pips.forEach(function(pip){
            pip.addEventListener('click',function(){
                stopAuto();
                goTo(parseInt(pip.getAttribute('data-slide'),10));
                if(!paused)startAuto();
            });
        });
        startAuto();
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'lunara_output_hero_cinema_js', 100 );

/**
 * Review sidebar scroll-follow.
 * Blocksy's #main-container overflow:clip defeats CSS position:sticky.
 * This JS-based approach manually tracks scroll and fixes the sidebar.
 */
function lunara_output_sidebar_scroll_follow_js() {
    if ( ! is_singular( 'review' ) && ! ( is_single() && has_term( '', 'lunara_director' ) ) ) {
        return;
    }
    ?>
    <script>
    (function(){
        var sticky = document.querySelector('.lunara-review-single-rail-sticky');
        var rail   = document.querySelector('.lunara-review-single-rail');
        var grid   = document.querySelector('.lunara-review-single-body-grid');
        if (!sticky || !rail || !grid) return;

        var mq = window.matchMedia('(max-width: 900px)');
        var topGap  = 90;   /* px below viewport top */
        var ticking = false;

        function resetMobileState() {
            sticky.style.transform = '';
            sticky.classList.remove('is-following', 'is-bottomed');
        }

        /*
         * Use transform: translateY() instead of position:fixed.
         * This keeps the element in normal flow, avoiding Blocksy's
         * overflow:clip and ancestor-transform issues entirely.
         */
        function update() {
            ticking = false;

            if (mq.matches) {
                resetMobileState();
                return;
            }

            /* Natural (un-translated) top of the sticky element */
            sticky.style.transform = '';               /* reset to measure natural position */
            var stickyNat  = sticky.getBoundingClientRect();
            var gridRect   = grid.getBoundingClientRect();
            var stickyH    = sticky.offsetHeight;
            var gridBottom = gridRect.bottom;

            /* 1. Sidebar top hasn't scrolled past the gap — stay put */
            if (stickyNat.top >= topGap) {
                sticky.classList.remove('is-following', 'is-bottomed');
                return;
            }

            /* How far we need to shift the element down */
            var shift = topGap - stickyNat.top;

            /* 2. Clamp so it doesn't overflow past the grid bottom */
            var maxShift = gridBottom - stickyNat.top - stickyH - 24;
            if (maxShift < 0) maxShift = 0;
            if (shift > maxShift) {
                shift = maxShift;
                sticky.classList.remove('is-following');
                sticky.classList.add('is-bottomed');
            } else {
                sticky.classList.add('is-following');
                sticky.classList.remove('is-bottomed');
            }

            sticky.style.transform = 'translateY(' + Math.round(shift) + 'px)';
        }

        function onViewportChange() {
            resetMobileState();
            if (!mq.matches) {
                requestAnimationFrame(update);
            }
        }

        function onScroll() {
            if (mq.matches) {
                resetMobileState();
                return;
            }

            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', onScroll, {passive: true});
        window.addEventListener('resize', onViewportChange);

        if (typeof mq.addEventListener === 'function') {
            mq.addEventListener('change', onViewportChange);
        } else if (typeof mq.addListener === 'function') {
            mq.addListener(onViewportChange);
        }

        /* Initial call after layout settles */
        requestAnimationFrame(onViewportChange);
    })();
    </script>
    <?php
}
add_action( 'wp_footer', 'lunara_output_sidebar_scroll_follow_js', 101 );

/**
 * Keep reviews inside the review lane instead of bleeding into standard post archives.
 */
if ( ! function_exists( 'lunara_separate_review_from_editorial_archives' ) ) {
    function lunara_separate_review_from_editorial_archives( $query ) {
        if ( is_admin() || ! ( $query instanceof WP_Query ) || ! $query->is_main_query() ) {
            return;
        }

        if ( $query->is_search() || $query->is_singular( 'review' ) || $query->is_singular( 'journal' ) ) {
            return;
        }

        if ( $query->is_post_type_archive( 'review' ) ) {
            if ( function_exists( 'lunara_apply_review_archive_sort_args' ) ) {
                $query_vars = array(
                    'orderby' => $query->get( 'orderby' ),
                    'order'   => $query->get( 'order' ),
                );
                $query_vars = lunara_apply_review_archive_sort_args( $query_vars );
                $query->set( 'orderby', $query_vars['orderby'] );
                $query->set( 'order', $query_vars['order'] );
            }
            return;
        }

        if ( $query->is_post_type_archive( 'journal' ) || $query->is_tax( 'journal_type' ) ) {
            if ( function_exists( 'lunara_apply_editorial_archive_sort_args' ) ) {
                $query_vars = array(
                    'orderby' => $query->get( 'orderby' ),
                    'order'   => $query->get( 'order' ),
                );
                $query_vars = lunara_apply_editorial_archive_sort_args( $query_vars );
                $query->set( 'orderby', $query_vars['orderby'] );
                $query->set( 'order', $query_vars['order'] );
            }
            return;
        }

        $requested_post_type = $query->get( 'post_type' );
        if ( 'review' === $requested_post_type || ( is_array( $requested_post_type ) && in_array( 'review', $requested_post_type, true ) ) ) {
            return;
        }

        if ( $query->is_home() || $query->is_category() || $query->is_tag() || $query->is_author() || $query->is_date() ) {
            $query->set( 'post_type', 'post' );

            if ( function_exists( 'lunara_apply_editorial_archive_sort_args' ) ) {
                $query_vars = array(
                    'orderby' => $query->get( 'orderby' ),
                    'order'   => $query->get( 'order' ),
                );
                $query_vars = lunara_apply_editorial_archive_sort_args( $query_vars );
                $query->set( 'orderby', $query_vars['orderby'] );
                $query->set( 'order', $query_vars['order'] );
            }
        }
    }
}
add_action( 'pre_get_posts', 'lunara_separate_review_from_editorial_archives', 12 );

/* ========================================
   NEWS GRID — auto-splits multi-story posts into card grids
   Detects H2/H3 headings as story boundaries.
   ======================================== */

/**
 * Split a standard post's content into separate story cards when it has
 * multiple H2 or H3 headings (a "roundup" or "multi-story" post).
 *
 * Requires at least 2 headings to activate. Single-story posts pass through untouched.
 */
function lunara_news_grid_content_filter( $content ) {
    if ( ! is_singular( 'post' ) || is_admin() ) {
        return $content;
    }

    $post_id = get_the_ID();
    if ( ! $post_id ) {
        return $content;
    }

    // Only apply to news-type posts. Skip if post has the _lunara_disable_news_grid meta.
    if ( '1' === (string) get_post_meta( $post_id, '_lunara_disable_news_grid', true ) ) {
        return $content;
    }

    // Split content on H2 and H3 tags.
    $parts = preg_split( '/(<h[23][^>]*>.*?<\/h[23]>)/is', $content, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY );
    if ( ! is_array( $parts ) || count( $parts ) < 4 ) {
        // Fewer than 2 heading+body pairs — not a multi-story post.
        return $content;
    }

    // Build story cards: each card starts with a heading and collects content until the next heading.
    $cards   = array();
    $current = array( 'heading' => '', 'body' => '' );
    $intro   = '';

    foreach ( $parts as $part ) {
        if ( preg_match( '/^<h[23][^>]*>(.*?)<\/h[23]>$/is', $part, $m ) ) {
            // Save previous card if it has a heading.
            if ( '' !== $current['heading'] ) {
                $cards[] = $current;
            } elseif ( '' !== trim( $current['body'] ) ) {
                // Content before the first heading is an intro.
                $intro = $current['body'];
            }
            $current = array( 'heading' => trim( $m[1] ), 'body' => '' );
        } else {
            $current['body'] .= $part;
        }
    }

    // Save the last card.
    if ( '' !== $current['heading'] ) {
        $cards[] = $current;
    }

    if ( count( $cards ) < 2 ) {
        return $content;
    }

    // Extract the first image from each card's body for the card visual.
    $grid_html = '';

    if ( '' !== trim( $intro ) ) {
        $grid_html .= '<div class="lunara-news-grid-intro">' . $intro . '</div>';
    }

    $grid_html .= '<div class="lunara-news-grid">';

    foreach ( $cards as $card ) {
        $image_html = '';
        $card_body  = $card['body'];

        // Pull the first <img> or <figure> from the card body.
        if ( preg_match( '/<figure[^>]*>.*?<\/figure>/is', $card_body, $fig_match ) ) {
            $image_html = $fig_match[0];
            $card_body  = str_replace( $fig_match[0], '', $card_body );
        } elseif ( preg_match( '/<img[^>]+>/is', $card_body, $img_match ) ) {
            $image_html = $img_match[0];
            $card_body  = str_replace( $img_match[0], '', $card_body );
        }

        // Clean up empty paragraphs left after image extraction.
        $card_body = preg_replace( '/<p>\s*<\/p>/is', '', $card_body );
        $card_body = trim( $card_body );

        $grid_html .= '<article class="lunara-news-grid-card">';

        if ( '' !== $image_html ) {
            $grid_html .= '<div class="lunara-news-grid-card-image">' . $image_html . '</div>';
        }

        $grid_html .= '<div class="lunara-news-grid-card-content">';
        $grid_html .= '<h3 class="lunara-news-grid-card-title">' . $card['heading'] . '</h3>';
        $grid_html .= '<div class="lunara-news-grid-card-body">' . $card_body . '</div>';
        $grid_html .= '</div>';
        $grid_html .= '</article>';
    }

    $grid_html .= '</div>';

    return $grid_html;
}
add_filter( 'the_content', 'lunara_news_grid_content_filter', 8 );
