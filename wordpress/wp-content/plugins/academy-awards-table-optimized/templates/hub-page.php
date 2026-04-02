<?php
/**
 * Academy Awards Table - Hub Page Template
 *
 * Routes:
 *   /{base}/ceremonies/
 *   /{base}/categories/
 *   /{base}/about/
 *   /{base}/ceremony/{N}/
 *   /{base}/category/{slug}/
 */

if (!defined('ABSPATH')) {
    exit;
}

$aat = Academy_Awards_Table::get_instance();
$hub = sanitize_text_field(get_query_var('aat_hub'));
$hub_id = sanitize_text_field(get_query_var('aat_hub_id'));

global $wpdb;
$table_name = $wpdb->prefix . 'academy_awards';

// Common dynamic scope values
$total_records = intval($wpdb->get_var("SELECT COUNT(*) FROM $table_name"));
$total_winners = intval($wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE winner = 1"));
$total_categories = intval($wpdb->get_var("SELECT COUNT(DISTINCT canonical_category) FROM $table_name WHERE canonical_category != ''"));
$total_ceremonies = intval($wpdb->get_var("SELECT COUNT(DISTINCT ceremony) FROM $table_name"));
$min_ceremony = intval($wpdb->get_var("SELECT MIN(ceremony) FROM $table_name"));
$max_ceremony = intval($wpdb->get_var("SELECT MAX(ceremony) FROM $table_name"));
$span = '';
if ($min_ceremony > 0 && $max_ceremony > 0) {
    $first_year = $aat->get_ceremony_year($min_ceremony);
    $last_year = $aat->get_ceremony_year($max_ceremony);
    if ($first_year && $last_year) {
        $span = $first_year . '-' . $last_year;
    }
}

// Helper: mark 404 and show friendly page
$mark_404 = function() {
    global $wp_query;
    if (is_object($wp_query)) {
        $wp_query->set_404();
    }
    status_header(404);
    nocache_headers();
};

$db_url = $aat->get_database_url();
$table_view_requested = ( isset($_GET['view']) && sanitize_key(wp_unslash($_GET['view'])) === 'table' );

$aat_pipe_display = function($value) {
    $parts = array_values(array_filter(array_map('trim', explode('|', (string) $value)), 'strlen'));
    return implode(' | ', $parts);
};

$aat_clean_nominee_label = function($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    $patterns = array(
        '/^Written by\s+/i',
        '/^Music and Lyric by\s+/i',
        '/^Music by\s+/i',
        '/^Lyric by\s+/i',
        '/^Produced by\s+/i',
        '/^Directed by\s+/i',
    );

    return trim((string) preg_replace($patterns, '', $value));
};

$aat_join_meta = function($parts) {
    $parts = array_values(array_filter(array_map('trim', (array) $parts), 'strlen'));
    if (empty($parts)) {
        return '';
    }

    $out = array();
    foreach ($parts as $part) {
        $out[] = '<span>' . esc_html($part) . '</span>';
    }

    return implode('<span class="aat-meta-sep" aria-hidden="true">&middot;</span>', $out);
};

$aat_winner_primary = function($entry) use ($aat_pipe_display, $aat_clean_nominee_label) {
    $category = strtoupper(trim((string) ($entry['canonical_category'] ?? '')));
    $film = trim((string) ($entry['film'] ?? ''));
    $name = $aat_clean_nominee_label($entry['name'] ?? '');
    $nominees = $aat_clean_nominee_label($aat_pipe_display($entry['nominees'] ?? ''));

    if (in_array($category, array('BEST PICTURE', 'ANIMATED FEATURE FILM', 'DOCUMENTARY (Feature)', 'INTERNATIONAL FEATURE FILM', 'SHORT FILM (Animated)', 'SHORT FILM (Live Action)'), true) && $film !== '') {
        return $film;
    }

    if ($name !== '') {
        return $name;
    }

    if ($nominees !== '') {
        return $nominees;
    }

    return $film;
};

$aat_winner_secondary = function($entry) use ($aat_pipe_display, $aat_winner_primary, $aat_clean_nominee_label) {
    $primary = $aat_winner_primary($entry);
    $film = trim((string) ($entry['film'] ?? ''));
    $detail = trim((string) ($entry['detail'] ?? ''));
    $nominees = $aat_clean_nominee_label($aat_pipe_display($entry['nominees'] ?? ''));

    if ($film !== '' && $film !== $primary) {
        return $film;
    }

    if ($detail !== '' && $detail !== $primary) {
        return $detail;
    }

    if ($nominees !== '' && $nominees !== $primary) {
        return $nominees;
    }

    return '';
};

$aat_build_hub_review_cards = function($title_entries, $limit = 6) use ($aat) {
    $cards = array();
    $seen_reviews = array();

    foreach ((array) $title_entries as $entry) {
        $film_id = strtolower(trim((string) ($entry['film_id'] ?? '')));
        if (!preg_match('/^tt\d+$/', $film_id)) {
            continue;
        }

        $review_ids = $aat->get_review_ids_for_title_id($film_id, 1);
        if (empty($review_ids[0])) {
            continue;
        }

        $review_id = intval($review_ids[0]);
        if ($review_id <= 0 || isset($seen_reviews[$review_id])) {
            continue;
        }

        $review_url = get_permalink($review_id);
        if (!is_string($review_url) || $review_url === '') {
            continue;
        }

        $film_label = trim((string) ($entry['film'] ?? ''));
        if ($film_label === '') {
            $film_label = $aat->lookup_title_label($film_id);
        }

        $visual = method_exists($aat, 'get_title_visual_package') ? $aat->get_title_visual_package($film_id, 'medium_large') : array();
        $sort_date = get_post_time('U', true, $review_id);
        if (!$sort_date) {
            $sort_date = 0;
        }

        $cards[] = array(
            'review_id' => $review_id,
            'review_url' => $review_url,
            'review_title' => get_the_title($review_id),
            'review_excerpt' => get_the_excerpt($review_id),
            'review_thumb' => get_the_post_thumbnail($review_id, 'large', array(
                'class' => 'aat-related-review-image',
                'loading' => 'lazy',
                'decoding' => 'async',
            )),
            'film_id' => $film_id,
            'film_label' => $film_label,
            'film_url' => $aat->get_entity_url($film_id),
            'film_year' => trim((string) ($entry['year'] ?? '')),
            'fallback_html' => !empty($visual['card_fallback_html']) ? $visual['card_fallback_html'] : '',
            'sort_date' => intval($sort_date),
        );

        $seen_reviews[$review_id] = true;
    }

    usort($cards, function($a, $b) {
        $cmp = intval($b['sort_date'] ?? 0) <=> intval($a['sort_date'] ?? 0);
        if ($cmp !== 0) {
            return $cmp;
        }

        return strcasecmp((string) ($a['review_title'] ?? ''), (string) ($b['review_title'] ?? ''));
    });

    if ($limit > 0) {
        $cards = array_slice($cards, 0, $limit);
    }

    return $cards;
};

$aat_build_title_spotlight = function($film_id, $fallback_label = '', $meta_lines = array(), $badge_label = '') use ($aat, $db_url) {
    $film_id = strtolower(trim((string) $film_id));
    if (!preg_match('/^tt\d+$/', $film_id)) {
        return array();
    }

    $visual = method_exists($aat, 'get_title_visual_package') ? $aat->get_title_visual_package($film_id, 'large') : array();
    $film_label = trim((string) $fallback_label);
    if ($film_label === '') {
        $film_label = $aat->lookup_title_label($film_id);
    }
    if ($film_label === '' && !empty($visual['title'])) {
        $film_label = (string) $visual['title'];
    }
    if ($film_label === '') {
        $film_label = strtoupper($film_id);
    }

    $film_url = $aat->get_entity_url($film_id);
    $meta_lines = array_values(array_filter(array_map('trim', (array) $meta_lines), 'strlen'));

    return array(
        'film_id' => $film_id,
        'film_label' => $film_label,
        'film_url' => $film_url ? $film_url : $db_url,
        'poster_html' => !empty($visual['poster_html']) ? $visual['poster_html'] : '',
        'poster_url' => !empty($visual['poster_url']) ? $visual['poster_url'] : '',
        'fallback_html' => !empty($visual['fallback_html']) ? $visual['fallback_html'] : '',
        'backdrop_url' => !empty($visual['backdrop_url']) ? $visual['backdrop_url'] : '',
        'meta_lines' => $meta_lines,
        'badge_label' => trim((string) $badge_label),
    );
};

// Optional: if the site owner created WordPress pages for these hubs (as recommended),
// pull their editor content in as the intro copy so they can control tone/voice.
$wp_hub_page = null;
$wp_hub_content = '';
if (in_array($hub, array('ceremonies','categories','about'), true)) {
    $wp_hub_page = $aat->get_hub_page_post($hub);
    if ($wp_hub_page instanceof WP_Post) {
        $wp_hub_content = trim((string) $wp_hub_page->post_content);
    }
}

get_header();
?>

<div class="aat-container aat-hub-page">

    <p class="aat-hub-breadcrumbs">
        <a href="<?php echo esc_url($db_url); ?>"><?php echo esc_html__('Oscar Ledger', 'academy-awards-table'); ?></a>
        <span class="aat-footer-sep">&rsaquo;</span>
        <?php echo esc_html(ucfirst($hub)); ?>
        <?php if (!empty($hub_id)) : ?>
            <span class="aat-footer-sep">&rsaquo;</span>
            <?php echo esc_html($hub_id); ?>
        <?php endif; ?>
    </p>

    <?php
        // CEREMONIES INDEX
        if ($hub === 'ceremonies') :
            $rows = $wpdb->get_results(
                "SELECT ceremony, MIN(year) AS year_label FROM $table_name GROUP BY ceremony ORDER BY ceremony DESC",
                ARRAY_A
            );
    ?>
        <div class="aat-hub-header">
            <h1 class="aat-hub-title"><?php echo esc_html__('Ceremonies', 'academy-awards-table'); ?></h1>

            <?php if ($wp_hub_page instanceof WP_Post && $wp_hub_content !== '') : ?>
                <div class="aat-hub-wp-content">
                    <?php echo apply_filters('the_content', $wp_hub_page->post_content); ?>
                </div>
            <?php endif; ?>

            <div class="aat-hub-actions">
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($aat->get_categories_index_url()); ?>"><?php echo esc_html__('Browse Categories', 'academy-awards-table'); ?></a>
                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($db_url); ?>"><?php echo esc_html__('Open Ledger', 'academy-awards-table'); ?></a>
            </div>
        </div>

        <div class="aat-stats-bar aat-entity-stats">
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_ceremonies)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Ceremonies', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_records)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Nominations', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_winners)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Wins', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_categories)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Categories', 'academy-awards-table'); ?></span></div>
        </div>

        <div class="aat-hub-grid">
            <?php if (!empty($rows)) : foreach ($rows as $r) :
                $c = intval($r['ceremony'] ?? 0);
                if ($c <= 0) continue;
                $year_label = (string) ($r['year_label'] ?? '');
                $url = $aat->get_ceremony_url($c);
            ?>
                <a class="aat-hub-card" href="<?php echo esc_url($url); ?>">
                    <h3 class="aat-hub-card-title"><?php echo esc_html($aat->ordinal($c)); ?> <?php echo esc_html__('Academy Awards', 'academy-awards-table'); ?></h3>
                    <p class="aat-hub-card-meta"><?php echo esc_html($year_label); ?></p>
                </a>
            <?php endforeach; endif; ?>
        </div>

    <?php
        // CATEGORIES INDEX
        elseif ($hub === 'categories') :
            $cats = $wpdb->get_results(
                "SELECT canonical_category, MIN(class) AS class_label FROM $table_name WHERE canonical_category != '' GROUP BY canonical_category ORDER BY MIN(class) ASC, canonical_category ASC",
                ARRAY_A
            );
            $grouped = array();
            if (is_array($cats)) {
                foreach ($cats as $r) {
                    $cat = (string) ($r['canonical_category'] ?? '');
                    if ($cat === '') continue;
                    $cls = (string) ($r['class_label'] ?? '');
                    if ($cls === '') $cls = 'Other';
                    if (!isset($grouped[$cls])) $grouped[$cls] = array();
                    $grouped[$cls][] = $cat;
                }
            }
            ksort($grouped);
    ?>
        <div class="aat-hub-header">
            <h1 class="aat-hub-title"><?php echo esc_html__('Categories', 'academy-awards-table'); ?></h1>

            <?php if ($wp_hub_page instanceof WP_Post && $wp_hub_content !== '') : ?>
                <div class="aat-hub-wp-content">
                    <?php echo apply_filters('the_content', $wp_hub_page->post_content); ?>
                </div>
            <?php endif; ?>

            <div class="aat-hub-actions">
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($aat->get_ceremonies_index_url()); ?>"><?php echo esc_html__('Browse Ceremonies', 'academy-awards-table'); ?></a>
                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($db_url); ?>"><?php echo esc_html__('Open Ledger', 'academy-awards-table'); ?></a>
            </div>
        </div>

        <div class="aat-stats-bar aat-entity-stats">
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_categories)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Categories', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_records)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Nominations', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_winners)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Wins', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_ceremonies)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Ceremonies', 'academy-awards-table'); ?></span></div>
        </div>

        <?php foreach ($grouped as $cls => $list) : ?>
            <div class="aat-hub-section">
                <h2><?php echo esc_html($cls); ?></h2>
                <div class="aat-hub-grid">
                    <?php foreach ($list as $cat) :
                        $url = $aat->get_category_url($cat);
                        $label = $aat->format_category_display($cat);
                    ?>
                        <a class="aat-hub-card" href="<?php echo esc_url($url); ?>">
                            <h3 class="aat-hub-card-title"><?php echo esc_html($label); ?></h3>
                            <p class="aat-hub-card-meta"><?php echo esc_html($cat); ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

    <?php
        // ABOUT
        elseif ($hub === 'about') :
    ?>
        <div class="aat-hub-header">
            <h1 class="aat-hub-title"><?php echo esc_html__('About the Oscar Ledger', 'academy-awards-table'); ?></h1>

            <?php if ($wp_hub_page instanceof WP_Post && $wp_hub_content !== '') : ?>
                <div class="aat-hub-wp-content">
                    <?php echo apply_filters('the_content', $wp_hub_page->post_content); ?>
                </div>
            <?php endif; ?>

            <div class="aat-hub-actions">
                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($db_url); ?>"><?php echo esc_html__('Open Ledger', 'academy-awards-table'); ?></a>
            </div>
        </div>

        <div class="aat-stats-bar aat-entity-stats">
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_records)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Nominations', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_winners)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Wins', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_categories)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Categories', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_ceremonies)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Ceremonies', 'academy-awards-table'); ?></span></div>
        </div>

        <?php if ($span) : ?>
            <div class="aat-hub-section">
                <h2><?php echo esc_html__('Scope', 'academy-awards-table'); ?></h2>
                <p class="aat-hub-copy"><?php echo esc_html(sprintf(__('Coverage: %s.', 'academy-awards-table'), $span)); ?></p>
            </div>
        <?php endif; ?>

        <div class="aat-hub-section">
            <p class="aat-hub-copy"><?php echo esc_html__('Source: Academy of Motion Picture Arts and Sciences.', 'academy-awards-table'); ?></p>
        </div>

        <div class="aat-hub-section">
            <h2><?php echo esc_html__('Explore', 'academy-awards-table'); ?></h2>
            <div class="aat-hub-chips">
                <a class="aat-hub-chip" href="<?php echo esc_url($aat->get_ceremonies_index_url()); ?>"><?php echo esc_html__('Ceremonies', 'academy-awards-table'); ?></a>
                <a class="aat-hub-chip" href="<?php echo esc_url($aat->get_categories_index_url()); ?>"><?php echo esc_html__('Categories', 'academy-awards-table'); ?></a>
            </div>
        </div>

    <?php
        // CEREMONY PAGE
        elseif ($hub === 'ceremony') :
            $ceremony = intval($hub_id);
            if ($ceremony <= 0) {
                $mark_404();
            }
            $year_label = $aat->get_ceremony_year($ceremony);
            $noms = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE ceremony = %d", $ceremony)));
            $wins = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE ceremony = %d AND winner = 1", $ceremony)));
            $cats_count = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT canonical_category) FROM $table_name WHERE ceremony = %d AND canonical_category != ''", $ceremony)));
            $cats = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT canonical_category FROM $table_name WHERE ceremony = %d AND canonical_category != '' ORDER BY canonical_category ASC", $ceremony));
            $ceremony_rollup = method_exists($aat, 'get_ceremony_rollup') ? $aat->get_ceremony_rollup($ceremony) : array();
            $is_latest_ceremony = ($ceremony === intval($aat->get_max_ceremony()));
            $ceremony_spotlight = array();
    ?>
        <div class="aat-hub-header">
            <h1 class="aat-hub-title"><?php echo esc_html($aat->ordinal($ceremony)); ?> <?php echo esc_html__('Academy Awards', 'academy-awards-table'); ?></h1>
            <p class="aat-hub-subtitle"><?php echo esc_html($year_label); ?></p>

            <div class="aat-hub-actions">
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($aat->get_ceremonies_index_url()); ?>"><?php echo esc_html__('All Ceremonies', 'academy-awards-table'); ?></a>
                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($db_url); ?>"><?php echo esc_html__('Open Ledger', 'academy-awards-table'); ?></a>
            </div>
        </div>

        <?php if (!empty($ceremony_rollup)) :
            $best_picture = !empty($ceremony_rollup['best_picture']) ? $ceremony_rollup['best_picture'] : array();
            $most_wins = !empty($ceremony_rollup['most_wins']) ? $ceremony_rollup['most_wins'] : array();
            $most_nominated = !empty($ceremony_rollup['most_nominated']) ? $ceremony_rollup['most_nominated'] : array();
            $spotlight_film_id = '';
            $spotlight_film_label = '';
            $spotlight_meta = array();
            $spotlight_badge = '';

            if (!empty($best_picture['film_id'])) {
                $spotlight_film_id = (string) $best_picture['film_id'];
                $spotlight_film_label = (string) ($best_picture['film'] ?? '');
                $spotlight_meta[] = __('Best Picture winner', 'academy-awards-table');
                if (!empty($year_label)) {
                    $spotlight_meta[] = (string) $year_label;
                }
                $spotlight_badge = __('Best Picture', 'academy-awards-table');
            } elseif (!empty($most_wins['film_id'])) {
                $spotlight_film_id = (string) $most_wins['film_id'];
                $spotlight_film_label = (string) ($most_wins['film'] ?? '');
                if (!empty($most_wins['wins'])) {
                    $spotlight_meta[] = sprintf(__('%s wins', 'academy-awards-table'), number_format_i18n(intval($most_wins['wins'])));
                }
                if (!empty($year_label)) {
                    $spotlight_meta[] = (string) $year_label;
                }
                $spotlight_badge = __('Ceremony leader', 'academy-awards-table');
            }

            if ($spotlight_film_id !== '') {
                $ceremony_spotlight = $aat_build_title_spotlight($spotlight_film_id, $spotlight_film_label, $spotlight_meta, $spotlight_badge);
            }
        ?>
            <section class="aat-hub-section aat-ceremony-marquee">
                <div class="aat-ceremony-marquee-copy">
                    <p class="aat-hub-kicker"><?php echo esc_html($is_latest_ceremony ? __('Winners Now Live', 'academy-awards-table') : __('Ceremony Snapshot', 'academy-awards-table')); ?></p>
                    <h2><?php echo esc_html($is_latest_ceremony && !empty($ceremony_rollup['has_full_winners']) ? __('The latest ceremony is fully updated in the Lunara Oscar Ledger.', 'academy-awards-table') : __('A live, winner-driven snapshot of this ceremony.', 'academy-awards-table')); ?></h2>
                    <p class="aat-hub-copy">
                        <?php if (!empty($best_picture['film'])) : ?>
                            <?php echo esc_html(sprintf(__('Best Picture went to %s.', 'academy-awards-table'), $best_picture['film'])); ?>
                        <?php endif; ?>
                        <?php if (!empty($most_wins['film']) && !empty($most_wins['wins'])) : ?>
                            <?php echo esc_html(sprintf(__('The winning leader is %1$s with %2$s win%3$s.', 'academy-awards-table'), $most_wins['film'], number_format_i18n(intval($most_wins['wins'])), intval($most_wins['wins']) === 1 ? '' : 's')); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="aat-ceremony-marquee-stack">
                    <?php if (!empty($ceremony_spotlight)) : ?>
                        <a class="aat-hub-spotlight-card" href="<?php echo esc_url($ceremony_spotlight['film_url']); ?>">
                            <div class="aat-hub-spotlight-media"<?php if (!empty($ceremony_spotlight['backdrop_url'])) : ?> style="background-image: linear-gradient(180deg, rgba(3,10,22,.1), rgba(3,10,22,.78)), url('<?php echo esc_url($ceremony_spotlight['backdrop_url']); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
                                <?php if (!empty($ceremony_spotlight['poster_html'])) : ?>
                                    <?php echo $ceremony_spotlight['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif (!empty($ceremony_spotlight['poster_url'])) : ?>
                                    <img class="aat-hub-spotlight-poster" src="<?php echo esc_url($ceremony_spotlight['poster_url']); ?>" alt="<?php echo esc_attr($ceremony_spotlight['film_label']); ?> poster" loading="lazy" decoding="async" />
                                <?php elseif (!empty($ceremony_spotlight['fallback_html'])) : ?>
                                    <?php echo $ceremony_spotlight['fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($ceremony_spotlight['film_label']); ?></span></div>
                                <?php endif; ?>
                                <?php if (!empty($ceremony_spotlight['badge_label'])) : ?><span class="aat-winner-badge aat-card-badge"><?php echo esc_html($ceremony_spotlight['badge_label']); ?></span><?php endif; ?>
                            </div>
                            <div class="aat-hub-spotlight-body">
                                <h3 class="aat-hub-spotlight-title"><?php echo esc_html($ceremony_spotlight['film_label']); ?></h3>
                                <?php if (!empty($ceremony_spotlight['meta_lines'])) : ?>
                                    <p class="aat-hub-spotlight-meta"><?php echo $aat_join_meta($ceremony_spotlight['meta_lines']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endif; ?>
                    <?php if (!empty($ceremony_rollup['top_titles'])) : ?>
                        <div class="aat-hub-chip-stack">
                            <?php foreach ($ceremony_rollup['top_titles'] as $title_entry) : ?>
                                <a class="aat-hub-chip aat-hub-chip-rich" href="<?php echo esc_url(!empty($title_entry['film_url']) ? $title_entry['film_url'] : $db_url); ?>">
                                    <strong><?php echo esc_html($title_entry['film']); ?></strong>
                                    <span><?php echo esc_html(number_format_i18n(intval($title_entry['wins']))); ?> <?php echo esc_html__('wins', 'academy-awards-table'); ?> | <?php echo esc_html(number_format_i18n(intval($title_entry['nominations']))); ?> <?php echo esc_html__('nominations', 'academy-awards-table'); ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>

            <div class="aat-hub-metric-grid">
                <article class="aat-hub-metric-card">
                    <span class="aat-hub-metric-label"><?php echo esc_html__('Winner Record', 'academy-awards-table'); ?></span>
                    <strong class="aat-hub-metric-value"><?php echo esc_html(number_format_i18n(intval($ceremony_rollup['winner_categories'] ?? 0))); ?>/<?php echo esc_html(number_format_i18n(intval($ceremony_rollup['categories_total'] ?? 0))); ?></strong>
                    <p class="aat-hub-metric-copy"><?php echo esc_html__('Categories settled and marked as winners in the ledger.', 'academy-awards-table'); ?></p>
                </article>
                <article class="aat-hub-metric-card">
                    <span class="aat-hub-metric-label"><?php echo esc_html__('Best Picture', 'academy-awards-table'); ?></span>
                    <strong class="aat-hub-metric-value"><?php echo esc_html(!empty($best_picture['film']) ? $best_picture['film'] : '-'); ?></strong>
                    <p class="aat-hub-metric-copy"><?php echo esc_html__("The ceremony's top prize, updated dynamically from the winner row.", 'academy-awards-table'); ?></p>
                </article>
                <article class="aat-hub-metric-card">
                    <span class="aat-hub-metric-label"><?php echo esc_html__('Most Wins', 'academy-awards-table'); ?></span>
                    <strong class="aat-hub-metric-value"><?php echo esc_html(!empty($most_wins['film']) ? $most_wins['film'] : '-'); ?></strong>
                    <p class="aat-hub-metric-copy"><?php echo !empty($most_wins['wins']) ? esc_html(sprintf(__('%s wins across the ceremony.', 'academy-awards-table'), number_format_i18n(intval($most_wins['wins'])))) : esc_html__('Awaiting winner data.', 'academy-awards-table'); ?></p>
                </article>
                <article class="aat-hub-metric-card">
                    <span class="aat-hub-metric-label"><?php echo esc_html__('Most Nominated', 'academy-awards-table'); ?></span>
                    <strong class="aat-hub-metric-value"><?php echo esc_html(!empty($most_nominated['film']) ? $most_nominated['film'] : '-'); ?></strong>
                    <p class="aat-hub-metric-copy"><?php echo !empty($most_nominated['nominations']) ? esc_html(sprintf(__('%s nominations in this ceremony.', 'academy-awards-table'), number_format_i18n(intval($most_nominated['nominations'])))) : esc_html__('Awaiting nomination data.', 'academy-awards-table'); ?></p>
                </article>
            </div>
        <?php endif; ?>

        <div class="aat-stats-bar aat-entity-stats">
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($noms)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Nominations', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($wins)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Wins', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($cats_count)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Categories', 'academy-awards-table'); ?></span></div>
                    <div class="aat-stat"><span class="aat-stat-number"><?php echo $span ? esc_html($span) : '-'; ?></span><span class="aat-stat-label"><?php echo esc_html__('Ledger span', 'academy-awards-table'); ?></span></div>
        </div>

        <?php $ceremony_titles = method_exists($aat, 'get_ceremony_title_highlights') ? $aat->get_ceremony_title_highlights($ceremony, 18) : array(); ?>
        <?php $ceremony_review_cards = !empty($ceremony_titles) ? $aat_build_hub_review_cards($ceremony_titles, 6) : array(); ?>
        <?php if (!empty($ceremony_titles)) : ?>
            <div class="aat-hub-section aat-ceremony-gallery-section">
                <h2><?php echo esc_html__('Ceremony Highlights', 'academy-awards-table'); ?></h2>
                <div class="aat-filmography-grid aat-hub-film-grid">
                    <?php foreach ($ceremony_titles as $entry) :
                        $fid = strtolower(trim((string) ($entry['film_id'] ?? '')));
                        if (!$fid) { continue; }
                        $visual = method_exists($aat, 'get_title_visual_package') ? $aat->get_title_visual_package($fid, 'medium_large') : array();
                        $film_label = !empty($entry['film']) ? (string) $entry['film'] : $aat->lookup_title_label($fid);
                        $film_url = $aat->get_entity_url($fid);
                    ?>
                        <article class="aat-filmography-card aat-hub-film-card<?php echo !empty($entry['winner']) ? ' is-winner' : ''; ?>">
                            <a class="aat-filmography-link" href="<?php echo esc_url($film_url ? $film_url : $db_url); ?>">
                                <div class="aat-filmography-poster-wrap">
                                    <?php if (!empty($visual['poster_html'])) : ?>
                                        <?php echo $visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php elseif (!empty($visual['poster_url'])) : ?>
                                        <img class="aat-filmography-poster" src="<?php echo esc_url($visual['poster_url']); ?>" alt="<?php echo esc_attr($film_label); ?> poster" loading="lazy" decoding="async" />
                                    <?php elseif (!empty($visual['card_fallback_html'])) : ?>
                                        <?php echo $visual['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php else : ?>
                                        <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($film_label); ?></span></div>
                                    <?php endif; ?>
                                    <?php if (!empty($entry['winner'])) : ?><span class="aat-winner-badge aat-card-badge">Winner</span><?php endif; ?>
                                </div>
                                <h3 class="aat-filmography-title"><?php echo esc_html($film_label); ?></h3>
                                <p class="aat-filmography-meta"><?php echo esc_html($aat->format_category_display($entry['canonical_category'] ?? '')); ?></p>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($ceremony_review_cards)) : ?>
            <div class="aat-hub-section">
                <h2><?php echo esc_html__('On Lunara', 'academy-awards-table'); ?></h2>
                <div class="aat-related-reviews-grid">
                    <?php foreach ($ceremony_review_cards as $card) : ?>
                        <?php $card_review_excerpt = trim(wp_strip_all_tags((string) ($card['review_excerpt'] ?? ''))); ?>
                        <article class="aat-related-review-card">
                            <a class="aat-related-review-media" href="<?php echo esc_url($card['review_url']); ?>">
                                <?php if (!empty($card['review_thumb'])) : ?>
                                    <?php echo $card['review_thumb']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif (!empty($card['fallback_html'])) : ?>
                                    <?php echo $card['fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($card['film_label']); ?></span></div>
                                <?php endif; ?>
                            </a>
                            <div class="aat-related-review-body">
                                <div class="aat-related-review-kicker"><?php echo esc_html__('Lunara Film Review', 'academy-awards-table'); ?></div>
                                <h3 class="aat-related-review-title"><a href="<?php echo esc_url($card['review_url']); ?>"><?php echo esc_html($card['review_title']); ?></a></h3>
                                <p class="aat-related-review-meta">
                                    <?php if (!empty($card['film_url'])) : ?>
                                        <a href="<?php echo esc_url($card['film_url']); ?>"><?php echo esc_html($card['film_label']); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($card['film_label']); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($card['film_year'])) : ?>
                                        <span class="aat-meta-sep" aria-hidden="true">&middot;</span><span><?php echo esc_html($card['film_year']); ?></span>
                                    <?php endif; ?>
                                </p>
                                <?php if ($card_review_excerpt !== '') : ?>
                                    <p class="aat-related-review-excerpt"><?php echo esc_html(wp_trim_words($card_review_excerpt, 24, '…')); ?></p>
                                <?php endif; ?>
                                <div class="aat-related-review-actions">
                                    <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($card['review_url']); ?>"><?php echo esc_html__('Read Review', 'academy-awards-table'); ?></a>
                                    <?php if (!empty($card['film_url'])) : ?>
                                        <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($card['film_url']); ?>"><?php echo esc_html__('Title Profile', 'academy-awards-table'); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($cats)) : ?>
            <div class="aat-hub-section">
                <h2><?php echo esc_html__('Categories in this ceremony', 'academy-awards-table'); ?></h2>
                <div class="aat-hub-chips">
                    <?php foreach ($cats as $cat) :
                        $url = $aat->get_category_url($cat);
                        $label = $aat->format_category_display($cat);
                    ?>
                        <a class="aat-hub-chip" href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($ceremony_rollup['winner_rows'])) : ?>
            <div class="aat-hub-section aat-winner-circle-section">
                <h2><?php echo esc_html__('Winner Circle', 'academy-awards-table'); ?></h2>
                <div class="aat-winner-circle-grid">
                    <?php foreach ($ceremony_rollup['winner_rows'] as $winner_entry) :
                        $primary_label = $aat_winner_primary($winner_entry);
                        $secondary_label = $aat_winner_secondary($winner_entry);
                        $category_url = $aat->get_category_url($winner_entry['canonical_category'] ?? '');
                    ?>
                        <article class="aat-winner-circle-card">
                            <div class="aat-winner-circle-top">
                                <?php if ($category_url) : ?>
                                    <a class="aat-winner-circle-category" href="<?php echo esc_url($category_url); ?>"><?php echo esc_html($winner_entry['category_label']); ?></a>
                                <?php else : ?>
                                    <span class="aat-winner-circle-category"><?php echo esc_html($winner_entry['category_label']); ?></span>
                                <?php endif; ?>
                                <span class="aat-winner-badge"><?php echo esc_html__('Winner', 'academy-awards-table'); ?></span>
                            </div>
                            <h3 class="aat-winner-circle-title">
                                <?php if (!empty($winner_entry['film_url']) && !empty($winner_entry['film']) && $primary_label === $winner_entry['film']) : ?>
                                    <a href="<?php echo esc_url($winner_entry['film_url']); ?>"><?php echo esc_html($primary_label); ?></a>
                                <?php else : ?>
                                    <?php echo esc_html($primary_label); ?>
                                <?php endif; ?>
                            </h3>
                            <?php if ($secondary_label !== '') : ?><p class="aat-winner-circle-meta"><?php echo esc_html($secondary_label); ?></p><?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
            $table_view_url = add_query_arg('view', 'table');
            $poster_view_url = remove_query_arg('view');
        ?>
        <div class="aat-hub-section aat-explorer-callout">
            <div class="aat-explorer-shell">
                <div class="aat-explorer-copy">
                    <h2><?php echo esc_html__('Table View', 'academy-awards-table'); ?></h2>
                </div>
                <div class="aat-hub-actions aat-view-toggle">
                    <a class="aat-btn aat-btn-secondary<?php echo !$table_view_requested ? ' is-active' : ''; ?>" href="<?php echo esc_url($poster_view_url); ?>"><?php echo esc_html__('Poster View', 'academy-awards-table'); ?></a>
                    <a class="aat-btn aat-btn-primary<?php echo $table_view_requested ? ' is-active' : ''; ?>" href="<?php echo esc_url($table_view_url); ?>"><?php echo esc_html__('Table View', 'academy-awards-table'); ?></a>
                </div>
            </div>
        </div>

        <?php if ($table_view_requested) : ?>
            <div class="aat-hub-section aat-table-shell">
                <?php
                    echo $aat->render_shortcode(array(
                        'ceremony' => (string) $ceremony,
                        'layout' => 'embedded',
                    ));
                ?>
            </div>
        <?php endif; ?>

    <?php
        // CATEGORY PAGE
        elseif ($hub === 'category') :
            $canonical = $aat->resolve_category_slug($hub_id);
            if (empty($canonical)) {
                $mark_404();
            }

            $label = $aat->format_category_display($canonical);
            $noms = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE canonical_category = %s", $canonical)));
            $wins = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE canonical_category = %s AND winner = 1", $canonical)));
            $cers = intval($wpdb->get_var($wpdb->prepare("SELECT COUNT(DISTINCT ceremony) FROM $table_name WHERE canonical_category = %s", $canonical)));
            $first_cer = intval($wpdb->get_var($wpdb->prepare("SELECT MIN(ceremony) FROM $table_name WHERE canonical_category = %s", $canonical)));
            $last_cer = intval($wpdb->get_var($wpdb->prepare("SELECT MAX(ceremony) FROM $table_name WHERE canonical_category = %s", $canonical)));
            $first_year = $first_cer ? $aat->get_ceremony_year($first_cer) : '';
            $last_year = $last_cer ? $aat->get_ceremony_year($last_cer) : '';
            $latest_winner = method_exists($aat, 'get_category_latest_winner') ? $aat->get_category_latest_winner($canonical) : array();
            $category_spotlight = array();
    ?>
        <div class="aat-hub-header">
            <h1 class="aat-hub-title"><?php echo esc_html($label); ?></h1>
            <p class="aat-hub-subtitle"><?php echo esc_html($canonical); ?></p>

            <div class="aat-hub-actions">
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($aat->get_categories_index_url()); ?>"><?php echo esc_html__('All Categories', 'academy-awards-table'); ?></a>
                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($db_url); ?>"><?php echo esc_html__('Open Ledger', 'academy-awards-table'); ?></a>
            </div>
        </div>

        <?php if (!empty($latest_winner)) : ?>
            <?php
                $category_spotlight_meta = array();
                if (!empty($latest_winner['year'])) {
                    $category_spotlight_meta[] = (string) $latest_winner['year'];
                }
                if (!empty($latest_winner['name']) && !empty($latest_winner['film']) && $latest_winner['name'] !== $latest_winner['film']) {
                    $category_spotlight_meta[] = (string) $latest_winner['name'];
                } elseif (!empty($latest_winner['detail'])) {
                    $category_spotlight_meta[] = (string) $latest_winner['detail'];
                }
                if (!empty($latest_winner['film_id'])) {
                    $category_spotlight = $aat_build_title_spotlight((string) $latest_winner['film_id'], (string) ($latest_winner['film'] ?? ''), $category_spotlight_meta, __('Latest winner', 'academy-awards-table'));
                }
            ?>
            <section class="aat-hub-section aat-category-latest-winner">
                <div class="aat-ceremony-marquee-copy">
                    <p class="aat-hub-kicker"><?php echo esc_html__('Latest Winner', 'academy-awards-table'); ?></p>
                    <h2><?php echo esc_html(!empty($latest_winner['film']) ? $latest_winner['film'] : ($latest_winner['name'] ?? '')); ?></h2>
                    <p class="aat-hub-copy">
                        <?php echo esc_html(sprintf(__('Most recent winning record: %1$s ceremony (%2$s).', 'academy-awards-table'), $aat->ordinal(intval($latest_winner['ceremony'] ?? 0)), (string) ($latest_winner['year'] ?? ''))); ?>
                        <?php if (!empty($latest_winner['detail'])) : ?>
                            <?php echo esc_html(' ' . $latest_winner['detail']); ?>
                        <?php elseif (!empty($latest_winner['name']) && !empty($latest_winner['film']) && $latest_winner['name'] !== $latest_winner['film']) : ?>
                            <?php echo esc_html(' ' . $latest_winner['name']); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="aat-ceremony-marquee-stack">
                    <?php if (!empty($category_spotlight)) : ?>
                        <a class="aat-hub-spotlight-card" href="<?php echo esc_url($category_spotlight['film_url']); ?>">
                            <div class="aat-hub-spotlight-media"<?php if (!empty($category_spotlight['backdrop_url'])) : ?> style="background-image: linear-gradient(180deg, rgba(3,10,22,.1), rgba(3,10,22,.78)), url('<?php echo esc_url($category_spotlight['backdrop_url']); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
                                <?php if (!empty($category_spotlight['poster_html'])) : ?>
                                    <?php echo $category_spotlight['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif (!empty($category_spotlight['poster_url'])) : ?>
                                    <img class="aat-hub-spotlight-poster" src="<?php echo esc_url($category_spotlight['poster_url']); ?>" alt="<?php echo esc_attr($category_spotlight['film_label']); ?> poster" loading="lazy" decoding="async" />
                                <?php elseif (!empty($category_spotlight['fallback_html'])) : ?>
                                    <?php echo $category_spotlight['fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($category_spotlight['film_label']); ?></span></div>
                                <?php endif; ?>
                                <?php if (!empty($category_spotlight['badge_label'])) : ?><span class="aat-winner-badge aat-card-badge"><?php echo esc_html($category_spotlight['badge_label']); ?></span><?php endif; ?>
                            </div>
                            <div class="aat-hub-spotlight-body">
                                <h3 class="aat-hub-spotlight-title"><?php echo esc_html($category_spotlight['film_label']); ?></h3>
                                <?php if (!empty($category_spotlight['meta_lines'])) : ?>
                                    <p class="aat-hub-spotlight-meta"><?php echo $aat_join_meta($category_spotlight['meta_lines']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php elseif (!empty($latest_winner['film_url'])) : ?>
                        <a class="aat-hub-chip aat-hub-chip-rich" href="<?php echo esc_url($latest_winner['film_url']); ?>">
                            <strong><?php echo esc_html__('Open Film Page', 'academy-awards-table'); ?></strong>
                            <span><?php echo esc_html($latest_winner['film']); ?></span>
                        </a>
                    <?php endif; ?>
                    <div class="aat-hub-chip-stack">
                        <a class="aat-hub-chip aat-hub-chip-rich" href="<?php echo esc_url($aat->get_ceremony_url(intval($latest_winner['ceremony'] ?? 0))); ?>">
                            <strong><?php echo esc_html__('Open Ceremony', 'academy-awards-table'); ?></strong>
                            <span><?php echo esc_html($aat->ordinal(intval($latest_winner['ceremony'] ?? 0))); ?> <?php echo esc_html__('Academy Awards', 'academy-awards-table'); ?></span>
                        </a>
                        <a class="aat-hub-chip aat-hub-chip-rich" href="<?php echo esc_url($db_url); ?>">
                            <strong><?php echo esc_html__('Open Ledger', 'academy-awards-table'); ?></strong>
                            <span><?php echo esc_html($label); ?></span>
                        </a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <div class="aat-stats-bar aat-entity-stats">
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($noms)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Nominations', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($wins)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Wins', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($cers)); ?></span><span class="aat-stat-label"><?php echo esc_html__('Ceremonies', 'academy-awards-table'); ?></span></div>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html($first_year && $last_year ? ($first_year . '-' . $last_year) : '-'); ?></span><span class="aat-stat-label"><?php echo esc_html__('Span', 'academy-awards-table'); ?></span></div>
        </div>

        <?php $category_titles = method_exists($aat, 'get_category_title_highlights') ? $aat->get_category_title_highlights($canonical, 18) : array(); ?>
        <?php $category_review_cards = !empty($category_titles) ? $aat_build_hub_review_cards($category_titles, 6) : array(); ?>
        <?php if (!empty($category_titles)) : ?>
            <div class="aat-hub-section aat-category-gallery-section">
                <h2><?php echo esc_html__('Category Highlights', 'academy-awards-table'); ?></h2>
                <div class="aat-filmography-grid aat-hub-film-grid">
                    <?php foreach ($category_titles as $entry) :
                        $fid = strtolower(trim((string) ($entry['film_id'] ?? '')));
                        if (!$fid) { continue; }
                        $visual = method_exists($aat, 'get_title_visual_package') ? $aat->get_title_visual_package($fid, 'medium_large') : array();
                        $film_label = !empty($entry['film']) ? (string) $entry['film'] : $aat->lookup_title_label($fid);
                        $film_url = $aat->get_entity_url($fid);
                    ?>
                        <article class="aat-filmography-card aat-hub-film-card<?php echo !empty($entry['winner']) ? ' is-winner' : ''; ?>">
                            <a class="aat-filmography-link" href="<?php echo esc_url($film_url ? $film_url : $db_url); ?>">
                                <div class="aat-filmography-poster-wrap">
                                    <?php if (!empty($visual['poster_html'])) : ?>
                                        <?php echo $visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php elseif (!empty($visual['poster_url'])) : ?>
                                        <img class="aat-filmography-poster" src="<?php echo esc_url($visual['poster_url']); ?>" alt="<?php echo esc_attr($film_label); ?> poster" loading="lazy" decoding="async" />
                                    <?php elseif (!empty($visual['card_fallback_html'])) : ?>
                                        <?php echo $visual['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php else : ?>
                                        <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($film_label); ?></span></div>
                                    <?php endif; ?>
                                    <?php if (!empty($entry['winner'])) : ?><span class="aat-winner-badge aat-card-badge">Winner</span><?php endif; ?>
                                </div>
                                <h3 class="aat-filmography-title"><?php echo esc_html($film_label); ?></h3>
                                <?php if (!empty($entry['year'])) : ?><p class="aat-filmography-meta"><?php echo esc_html($entry['year']); ?></p><?php endif; ?>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($category_review_cards)) : ?>
            <div class="aat-hub-section">
                <h2><?php echo esc_html__('On Lunara', 'academy-awards-table'); ?></h2>
                <div class="aat-related-reviews-grid">
                    <?php foreach ($category_review_cards as $card) : ?>
                        <?php $card_review_excerpt = trim(wp_strip_all_tags((string) ($card['review_excerpt'] ?? ''))); ?>
                        <article class="aat-related-review-card">
                            <a class="aat-related-review-media" href="<?php echo esc_url($card['review_url']); ?>">
                                <?php if (!empty($card['review_thumb'])) : ?>
                                    <?php echo $card['review_thumb']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif (!empty($card['fallback_html'])) : ?>
                                    <?php echo $card['fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($card['film_label']); ?></span></div>
                                <?php endif; ?>
                            </a>
                            <div class="aat-related-review-body">
                                <div class="aat-related-review-kicker"><?php echo esc_html__('Lunara Film Review', 'academy-awards-table'); ?></div>
                                <h3 class="aat-related-review-title"><a href="<?php echo esc_url($card['review_url']); ?>"><?php echo esc_html($card['review_title']); ?></a></h3>
                                <p class="aat-related-review-meta">
                                    <?php if (!empty($card['film_url'])) : ?>
                                        <a href="<?php echo esc_url($card['film_url']); ?>"><?php echo esc_html($card['film_label']); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($card['film_label']); ?>
                                    <?php endif; ?>
                                    <?php if (!empty($card['film_year'])) : ?>
                                        <span class="aat-meta-sep" aria-hidden="true">&middot;</span><span><?php echo esc_html($card['film_year']); ?></span>
                                    <?php endif; ?>
                                </p>
                                <?php if ($card_review_excerpt !== '') : ?>
                                    <p class="aat-related-review-excerpt"><?php echo esc_html(wp_trim_words($card_review_excerpt, 24, '…')); ?></p>
                                <?php endif; ?>
                                <div class="aat-related-review-actions">
                                    <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($card['review_url']); ?>"><?php echo esc_html__('Read Review', 'academy-awards-table'); ?></a>
                                    <?php if (!empty($card['film_url'])) : ?>
                                        <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($card['film_url']); ?>"><?php echo esc_html__('Title Profile', 'academy-awards-table'); ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php
            $table_view_url = add_query_arg('view', 'table');
            $poster_view_url = remove_query_arg('view');
        ?>
        <div class="aat-hub-section aat-explorer-callout">
            <div class="aat-explorer-shell">
                <div class="aat-explorer-copy">
                    <h2><?php echo esc_html__('Table View', 'academy-awards-table'); ?></h2>
                </div>
                <div class="aat-hub-actions aat-view-toggle">
                    <a class="aat-btn aat-btn-secondary<?php echo !$table_view_requested ? ' is-active' : ''; ?>" href="<?php echo esc_url($poster_view_url); ?>"><?php echo esc_html__('Poster View', 'academy-awards-table'); ?></a>
                    <a class="aat-btn aat-btn-primary<?php echo $table_view_requested ? ' is-active' : ''; ?>" href="<?php echo esc_url($table_view_url); ?>"><?php echo esc_html__('Table View', 'academy-awards-table'); ?></a>
                </div>
            </div>
        </div>

        <?php if ($table_view_requested) : ?>
            <div class="aat-hub-section aat-table-shell">
                <?php
                    echo $aat->render_shortcode(array(
                        'category' => $canonical,
                        'layout' => 'embedded',
                    ));
                ?>
            </div>
        <?php endif; ?>

    <?php
        // Unknown hub
        else :
            $mark_404();
    ?>
        <div class="aat-hub-header">
            <h1 class="aat-hub-title"><?php echo esc_html__('Not Found', 'academy-awards-table'); ?></h1>
            <p class="aat-hub-subtitle"><?php echo esc_html__('This page does not exist in the Lunara Oscar Ledger.', 'academy-awards-table'); ?></p>
            <div class="aat-hub-actions">
                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($db_url); ?>"><?php echo esc_html__('Open Ledger', 'academy-awards-table'); ?></a>
            </div>
        </div>
    <?php endif; ?>

</div>


<style>
.aat-hub-film-grid .aat-filmography-card{position:relative}
.aat-hub-film-card.is-winner .aat-filmography-poster-wrap{box-shadow:0 0 0 1px rgba(212,175,55,.45),0 18px 40px rgba(0,0,0,.35)}
.aat-card-badge{position:absolute;top:10px;right:10px;z-index:2}
.aat-ceremony-gallery-section .aat-filmography-title,.aat-category-gallery-section .aat-filmography-title{font-size:1rem;line-height:1.2}
</style>

<?php
get_footer();
?>
