<?php
/**
 * Academy Awards Table - Entity Page Template
 * Richer Film / Person / Company pages for Lunara Film
 */

if (!defined('ABSPATH')) {
    exit;
}

$aat = Academy_Awards_Table::get_instance();

$entity = sanitize_text_field(get_query_var('aat_entity'));
$id = sanitize_text_field(get_query_var('aat_entity_id'));

$rows = $aat->get_entity_rows($entity, $id);
$label = sanitize_text_field($aat->get_entity_display_name($entity, $id));
$label = trim((string) $label);

if (empty($rows)) {
    global $wp_query;
    if (is_object($wp_query)) {
        $wp_query->set_404();
    }
    status_header(404);
    nocache_headers();
}

$ordinal = function($n) {
    $n = intval($n);
    if ($n <= 0) return '';
    $s = array('th', 'st', 'nd', 'rd');
    $v = $n % 100;
    return $n . ($s[($v - 20) % 10] ?? $s[$v] ?? $s[0]);
};

$format_category = function($cat) {
    $cat = trim((string) $cat);
    if ($cat === '') return '';
    $map = array(
        'ACTOR IN A LEADING ROLE' => 'Best Actor',
        'ACTRESS IN A LEADING ROLE' => 'Best Actress',
        'ACTOR IN A SUPPORTING ROLE' => 'Best Supporting Actor',
        'ACTRESS IN A SUPPORTING ROLE' => 'Best Supporting Actress',
        'BEST PICTURE' => 'Best Picture',
        'DIRECTING' => 'Best Director',
        'WRITING (ORIGINAL SCREENPLAY)' => 'Original Screenplay',
        'WRITING (ADAPTED SCREENPLAY)' => 'Adapted Screenplay',
    );
    return $map[$cat] ?? ucwords(strtolower($cat));
};

$build_entity_url = function($id) use ($aat) {
    $id = trim((string) $id);
    if ($id === '') return '';
    $base = trailingslashit($aat->get_entity_base_url());
    if (preg_match('/^tt\d+$/', $id)) return esc_url($base . 'title/' . $id . '/');
    if (preg_match('/^nm\d+$/', $id)) return esc_url($base . 'name/' . $id . '/');
    if (preg_match('/^co\d+$/', $id)) return esc_url($base . 'company/' . $id . '/');
    return '';
};

$build_imdb_url = function($id) {
    $id = trim((string) $id);
    if ($id === '') return '';
    if (preg_match('/^tt\d+$/', $id)) return 'https://www.imdb.com/title/' . $id . '/';
    if (preg_match('/^nm\d+$/', $id)) return 'https://www.imdb.com/name/' . $id . '/';
    if (preg_match('/^co\d+$/', $id)) return 'https://www.imdb.com/company/' . $id . '/';
    return '';
};

$pipe_separator_html = '<span class="aat-sep"> &middot; </span>';

$render_linked_pipe = function($value_list, $id_list) use ($build_entity_url, $pipe_separator_html) {
    $values = array_values(array_filter(array_map('trim', explode('|', (string) $value_list)), 'strlen'));
    $ids = array_values(array_filter(array_map('trim', explode('|', (string) $id_list)), 'strlen'));

    if (empty($values)) {
        return '<span class="aat-no-film">&mdash;</span>';
    }

    if (!empty($ids) && count($ids) === count($values)) {
        $out = array();
        foreach ($values as $i => $value) {
            $url = $build_entity_url($ids[$i] ?? '');
            if ($url) {
                $out[] = '<a class="aat-entity-link" href="' . esc_url($url) . '">' . esc_html($value) . '</a>';
            } else {
                $out[] = '<span class="aat-entity-text">' . esc_html($value) . '</span>';
            }
        }

        return implode($pipe_separator_html, $out);
    }

    return implode($pipe_separator_html, array_map(function($value) {
        return '<span class="aat-entity-text">' . esc_html($value) . '</span>';
    }, $values));
};

$normalize_comparable_name = function($value) {
    $value = trim((string) $value);
    if ($value === '') {
        return '';
    }

    if (function_exists('remove_accents')) {
        $value = remove_accents($value);
    }

    $value = strtolower($value);
    $value = str_replace('&', ' and ', $value);
    $value = preg_replace('/[^a-z0-9]+/', ' ', $value);
    $value = preg_replace('/\s+/', ' ', (string) $value);

    return trim((string) $value);
};

$resolve_title_nominee_display = function($row) use ($normalize_comparable_name, $aat) {
    $explicit_name = trim((string) ($row['name'] ?? ''));
    $nominee_value = trim((string) ($row['nominees'] ?? ''));
    $nominee_ids = trim((string) ($row['nominee_ids'] ?? ''));
    $nominee_parts = array_values(array_filter(array_map('trim', explode('|', $nominee_value)), 'strlen'));
    $id_parts = array_values(array_filter(array_map('trim', explode('|', $nominee_ids)), 'strlen'));
    $single_nominee_id = count($id_parts) === 1 ? strtolower((string) $id_parts[0]) : '';

    if ($explicit_name === '') {
        return array(
            'label' => $nominee_value,
            'ids' => $nominee_ids,
            'is_plural' => strpos($nominee_value, '|') !== false,
        );
    }

    $matched_ids = $nominee_ids;

    if (!empty($nominee_parts) && count($nominee_parts) === count($id_parts) && count($id_parts) > 1) {
        $target = $normalize_comparable_name($explicit_name);
        $matches = array();

        foreach ($nominee_parts as $index => $nominee_part) {
            if ($normalize_comparable_name($nominee_part) === $target && isset($id_parts[$index])) {
                $matches[] = $id_parts[$index];
            }
        }

        if (count($matches) === 1) {
            $matched_ids = $matches[0];
        } else {
            return array(
                'label' => implode('|', $nominee_parts),
                'ids' => implode('|', $id_parts),
                'is_plural' => true,
            );
        }
    }

    if ($explicit_name !== '' && $single_nominee_id !== '' && preg_match('/^(nm|co)\d+$/', $single_nominee_id)) {
        $entity_type = strpos($single_nominee_id, 'co') === 0 ? 'company' : 'name';
        $preferred_label = trim((string) $aat->get_entity_display_name($entity_type, $single_nominee_id));
        if ($preferred_label !== '') {
            $explicit_name = $preferred_label;
        }
    }

    return array(
        'label' => $explicit_name,
        'ids' => $matched_ids,
        'is_plural' => false,
    );
};

$total_nominations = is_array($rows) ? count($rows) : 0;
$total_wins = 0;
$categories_set = array();
$ceremonies_set = array();
$ceremony_year_map = array();
$distinct_films = array();
$timeline = array();
$latest_year = '';
$latest_ceremony = 0;

if (is_array($rows)) {
    foreach ($rows as $r) {
        $winner = (!empty($r['winner']) && (int) $r['winner'] === 1);
        if ($winner) $total_wins++;

        $cat = (string) ($r['canonical_category'] ?? $r['category'] ?? '');
        if ($cat !== '') {
            $categories_set[$cat] = true;
        }

        $cer = intval($r['ceremony'] ?? 0);
        $year = (string) ($r['year'] ?? '');
        if ($cer > 0) {
            $ceremonies_set[$cer] = true;
            $ceremony_year_map[$cer] = $year;
            if ($cer > $latest_ceremony) {
                $latest_ceremony = $cer;
                $latest_year = $year;
            }

            if (!isset($timeline[$cer])) {
                $timeline[$cer] = array(
                    'year' => $year,
                    'rows' => array(),
                );
            }
            $timeline[$cer]['rows'][] = $r;
        }

        if ($entity !== 'title') {
            $film_ids = array_filter(array_map('trim', explode('|', (string) ($r['film_id'] ?? ''))), 'strlen');
            foreach ($film_ids as $fid) {
                $distinct_films[$fid] = true;
            }
        }
    }
}

krsort($timeline, SORT_NUMERIC);

$total_categories = count($categories_set);
$total_ceremonies = count($ceremonies_set);
$span = '';
if (!empty($ceremony_year_map)) {
    $years = array_values(array_filter($ceremony_year_map, 'strlen'));
    sort($years);
    if (!empty($years)) {
        $first_year = reset($years);
        $last_year = end($years);
        $span = $first_year === $last_year ? $first_year : ($first_year . '-' . $last_year);
    }
}

if ($span !== '') {
    $span = str_replace(array('-', '&#8211;'), '&ndash;', $span);
}

$years = array_values(array_filter($ceremony_year_map, 'strlen'));
if (!empty($years)) {
    sort($years);
    $first_year = reset($years);
    $last_year = end($years);
    $span = $first_year === $last_year ? $first_year : ($first_year . '&ndash;' . $last_year);
}

$latest_result = array();
if ($latest_ceremony > 0 && !empty($timeline[$latest_ceremony]['rows'])) {
    $latest_rows = $timeline[$latest_ceremony]['rows'];
    $latest_wins = 0;
    $latest_categories = array();
    foreach ($latest_rows as $latest_row) {
        if (!empty($latest_row['winner']) && (int) $latest_row['winner'] === 1) {
            $latest_wins++;
        }
        $latest_cat = (string) ($latest_row['canonical_category'] ?? $latest_row['category'] ?? '');
        if ($latest_cat !== '') {
            $latest_categories[$latest_cat] = $format_category($latest_cat);
        }
    }

    $latest_result = array(
        'ceremony' => $latest_ceremony,
        'year' => (string) $latest_year,
        'nominations' => count($latest_rows),
        'wins' => $latest_wins,
        'categories' => array_values($latest_categories),
    );
}

$imdb_url = $build_imdb_url($id);
$search_url = home_url('/?s=' . rawurlencode($label ? $label : $id));
$database_url = home_url('/oscars/');
$type_label = $entity === 'title' ? 'Film' : ($entity === 'company' ? 'Company' : 'Person');
$summary = '';
$visual = array();
$entity_anchor_title = '';
if ($entity === 'title' && method_exists($aat, 'get_title_visual_package')) {
    $visual = $aat->get_title_visual_package($id, 'large');
} elseif ($entity === 'name' && method_exists($aat, 'get_person_visual_package')) {
    $visual = $aat->get_person_visual_package($id, 'large');
} elseif ($entity === 'company' && !empty($distinct_films) && method_exists($aat, 'get_title_visual_package')) {
    foreach (array_keys($distinct_films) as $company_film_id) {
        $company_film_id = trim((string) $company_film_id);
        if ($company_film_id === '') {
            continue;
        }

        $company_visual = $aat->get_title_visual_package($company_film_id, 'large');
        $entity_anchor_title = method_exists($aat, 'lookup_title_label') ? $aat->lookup_title_label($company_film_id) : '';
        if ($entity_anchor_title === '' && !empty($company_visual['title'])) {
            $entity_anchor_title = (string) $company_visual['title'];
        }
        if ($entity_anchor_title === '') {
            $entity_anchor_title = strtoupper($company_film_id);
        }

        if (
            !empty($company_visual['poster_html']) ||
            !empty($company_visual['poster_url']) ||
            !empty($company_visual['backdrop_url']) ||
            !empty($company_visual['fallback_html'])
        ) {
            $visual = $company_visual;
            break;
        }
    }
}
$tmdb = !empty($visual['tmdb']) ? $visual['tmdb'] : array();
$hero_classes = array('aat-entity-hero');
if (
    $entity === 'company' &&
    empty($visual['poster_html']) &&
    empty($visual['poster_url']) &&
    empty($visual['fallback_html'])
) {
    $hero_classes[] = 'is-meta-only';
} elseif ($entity === 'title' && empty($visual['poster_html']) && empty($visual['poster_url']) && empty($visual['fallback_html'])) {
    $hero_classes[] = 'is-no-poster';
} elseif ($entity === 'name' && empty($visual['portrait_url']) && empty($visual['fallback_html'])) {
    $hero_classes[] = 'is-no-poster';
}
if ($total_nominations > 0) {
    $summary = sprintf(
        '%s appears in the Lunara Oscar Ledger with %s nomination%s and %s win%s across %s ceremon%s.',
        $label ? $label : strtoupper($id),
        number_format_i18n($total_nominations),
        $total_nominations === 1 ? '' : 's',
        number_format_i18n($total_wins),
        $total_wins === 1 ? '' : 's',
        number_format_i18n($total_ceremonies),
        $total_ceremonies === 1 ? 'y' : 'ies'
    );
}

$aat_review_ids = array();
if ($entity === 'title') {
    $aat_review_ids = $aat->get_review_ids_for_title_id($id, 3);
}

$aat_related_reviews = array();
if ($entity !== 'title' && !empty($distinct_films)) {
    foreach (array_keys($distinct_films) as $related_film_id) {
        $related_film_id = trim((string) $related_film_id);
        if ($related_film_id === '') {
            continue;
        }

        $related_review_ids = $aat->get_review_ids_for_title_id($related_film_id, 1);
        if (empty($related_review_ids[0])) {
            continue;
        }

        $related_review_id = (int) $related_review_ids[0];
        $related_review_url = get_permalink($related_review_id);
        if (!$related_review_url) {
            continue;
        }

        $related_film_label = method_exists($aat, 'lookup_title_label') ? $aat->lookup_title_label($related_film_id) : '';
        if ($related_film_label === '' && method_exists($aat, 'get_entity_display_name')) {
            $related_film_label = (string) $aat->get_entity_display_name('title', $related_film_id);
        }
        if ($related_film_label === '') {
            $related_film_label = strtoupper($related_film_id);
        }

        $related_review_thumb = get_the_post_thumbnail_url($related_review_id, 'medium_large');
        $related_review_excerpt = get_the_excerpt($related_review_id);
        $related_film_visual = method_exists($aat, 'get_title_visual_package') ? $aat->get_title_visual_package($related_film_id, 'medium_large') : array();
        $related_release_year = '';
        if (!empty($related_film_visual['release_year'])) {
            $related_release_year = (string) $related_film_visual['release_year'];
        } elseif (!empty($related_film_visual['tmdb']['release_date'])) {
            $related_release_year = substr((string) $related_film_visual['tmdb']['release_date'], 0, 4);
        }

        $aat_related_reviews[] = array(
            'review_id' => $related_review_id,
            'review_url' => $related_review_url,
            'review_title' => get_the_title($related_review_id),
            'review_excerpt' => $related_review_excerpt,
            'review_thumb' => $related_review_thumb,
            'film_id' => $related_film_id,
            'film_label' => $related_film_label,
            'film_url' => $build_entity_url($related_film_id),
            'film_year' => $related_release_year,
            'fallback_html' => !empty($related_film_visual['card_fallback_html']) ? $related_film_visual['card_fallback_html'] : '',
            'sort_date' => get_post_field('post_date', $related_review_id),
        );
    }

    if (!empty($aat_related_reviews)) {
        usort($aat_related_reviews, function($left, $right) {
            return strcmp((string) ($right['sort_date'] ?? ''), (string) ($left['sort_date'] ?? ''));
        });
        $aat_related_reviews = array_slice($aat_related_reviews, 0, 6);
    }
}

get_header();
?>
<div class="aat-container aat-entity-page">
    <nav class="aat-breadcrumbs" aria-label="Breadcrumb">
        <a href="<?php echo esc_url(home_url('/')); ?>">Home</a>
        <span class="aat-sep">/</span>
        <a href="<?php echo esc_url($database_url); ?>">Oscars</a>
        <span class="aat-sep">/</span>
        <span><?php echo esc_html($type_label); ?></span>
    </nav>

    <section class="<?php echo esc_attr(implode(' ', $hero_classes)); ?>"<?php if (in_array($entity, array('title', 'name', 'company'), true) && !empty($visual['backdrop_url'])) : ?> style="background-image: linear-gradient(180deg, rgba(3,10,22,.82), rgba(3,10,22,.95)), url('<?php echo esc_url($visual['backdrop_url']); ?>'); background-size: cover; background-position: center;"<?php endif; ?>>
        <?php if (in_array($entity, array('title', 'name', 'company'), true)) : ?>
            <?php $aat_poster_html = ($entity === 'title' && !empty($visual['poster_html'])) ? $visual['poster_html'] : ''; ?>
            <?php $aat_person_portrait_url = ($entity === 'name' && !empty($visual['portrait_url'])) ? $visual['portrait_url'] : ''; ?>
            <?php $aat_company_poster_html = ($entity === 'company' && !empty($visual['poster_html'])) ? $visual['poster_html'] : ''; ?>
            <?php $aat_fallback_html = !empty($visual['fallback_html']) ? $visual['fallback_html'] : ''; ?>
            <?php if (!empty($aat_poster_html) || !empty($aat_company_poster_html) || !empty($visual['poster_url']) || !empty($aat_person_portrait_url) || !empty($aat_fallback_html)) : ?>
                <div class="aat-entity-poster-wrap<?php echo $entity === 'name' ? ' is-person' : ''; ?><?php echo $entity === 'company' ? ' is-company' : ''; ?>">
                    <?php if (!empty($aat_poster_html)) : ?>
                        <?php echo $aat_poster_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php elseif (!empty($aat_company_poster_html)) : ?>
                        <?php echo $aat_company_poster_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php elseif (!empty($visual['poster_url'])) : ?>
                        <img class="aat-entity-poster" src="<?php echo esc_url($visual['poster_url']); ?>" alt="<?php echo esc_attr($label ? $label : strtoupper($id)); ?> poster" loading="lazy" decoding="async" />
                    <?php elseif (!empty($aat_person_portrait_url)) : ?>
                        <img class="aat-entity-portrait" src="<?php echo esc_url($aat_person_portrait_url); ?>" alt="<?php echo esc_attr($label ? $label : strtoupper($id)); ?> portrait" loading="lazy" decoding="async" />
                    <?php elseif (!empty($aat_fallback_html)) : ?>
                        <?php echo $aat_fallback_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="aat-entity-hero-copy">
            <div class="aat-entity-kicker"><?php echo esc_html($type_label); ?> Profile</div>
            <h1 class="aat-entity-title"><?php echo esc_html($label ? $label : strtoupper($id)); ?></h1>
            <p class="aat-entity-subtitle"><?php echo esc_html__('The Lunara Oscar Ledger', 'academy-awards-table'); ?></p>
            <?php if ($summary) : ?>
                <p class="aat-entity-summary"><?php echo esc_html($summary); ?></p>
            <?php endif; ?>
            <?php if ($entity === 'title' && !empty($tmdb)) : ?>
                <div class="aat-entity-meta-line">
                    <?php if (!empty($visual['release_year'])) : ?><span><?php echo esc_html($visual['release_year']); ?></span><?php endif; ?>
                    <?php if (!empty($visual['director'])) : ?><span><?php echo esc_html($visual['director']); ?></span><?php endif; ?>
                    <?php if (!empty($visual['runtime'])) : ?><span><?php echo esc_html($visual['runtime']); ?> min</span><?php endif; ?>
                </div>
                <?php if (!empty($visual['overview'])) : ?><p class="aat-entity-overview"><?php echo esc_html($visual['overview']); ?></p><?php endif; ?>
            <?php elseif ($entity === 'name' && !empty($tmdb)) : ?>
                <?php $person_meta_bits = array_values(array_filter((array) ($visual['meta_bits'] ?? array()), 'strlen')); ?>
                <?php if (!empty($person_meta_bits)) : ?>
                    <div class="aat-entity-meta-line">
                        <?php foreach ($person_meta_bits as $person_meta_bit) : ?>
                            <span><?php echo esc_html($person_meta_bit); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($visual['biography'])) : ?><p class="aat-entity-overview"><?php echo esc_html(wp_trim_words((string) $visual['biography'], 55)); ?></p><?php endif; ?>
            <?php elseif ($entity === 'company') : ?>
                <?php
                $company_meta_bits = array();
                if ($entity_anchor_title !== '') {
                    $company_meta_bits[] = sprintf(__('Representative title: %s', 'academy-awards-table'), $entity_anchor_title);
                }
                if (!empty($visual['release_year'])) {
                    $company_meta_bits[] = (string) $visual['release_year'];
                }
                ?>
                <?php if (!empty($company_meta_bits)) : ?>
                    <div class="aat-entity-meta-line">
                        <?php foreach ($company_meta_bits as $company_meta_bit) : ?>
                            <span><?php echo esc_html($company_meta_bit); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <?php if ($entity_anchor_title !== '') : ?>
                    <p class="aat-entity-overview"><?php echo esc_html(sprintf(__('%1$s is anchored visually through %2$s so company profiles can sit inside the same poster-led Oscars world as titles and people.', 'academy-awards-table'), $label ? $label : strtoupper($id), $entity_anchor_title)); ?></p>
                <?php endif; ?>
            <?php endif; ?>

            <div class="aat-entity-actions">
                <?php if (!empty($imdb_url)) : ?>
                    <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($imdb_url); ?>" target="_blank" rel="noopener noreferrer">IMDb Reference</a>
                <?php endif; ?>
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($search_url); ?>"><?php echo esc_html__('Search Lunara', 'academy-awards-table'); ?></a>
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($database_url); ?>"><?php echo esc_html__('Return to Ledger', 'academy-awards-table'); ?></a>
            </div>
        </div>
    </section>

    <?php if (!empty($latest_result)) : ?>
        <section class="aat-entity-status-banner<?php echo !empty($latest_result['wins']) ? ' is-winner' : ''; ?>">
            <div class="aat-entity-status-copy">
                <p class="aat-entity-status-kicker"><?php echo esc_html__('Latest Oscar Result', 'academy-awards-table'); ?></p>
                <h2 class="aat-entity-status-title">
                    <?php
                    if (!empty($latest_result['wins'])) {
                        echo esc_html(sprintf(__('%1$s win%2$s at the %3$s ceremony', 'academy-awards-table'), number_format_i18n(intval($latest_result['wins'])), intval($latest_result['wins']) === 1 ? '' : 's', $ordinal(intval($latest_result['ceremony']))));
                    } else {
                        echo esc_html(sprintf(__('%1$s nomination%2$s at the %3$s ceremony', 'academy-awards-table'), number_format_i18n(intval($latest_result['nominations'])), intval($latest_result['nominations']) === 1 ? '' : 's', $ordinal(intval($latest_result['ceremony']))));
                    }
                    ?>
                </h2>
                <p class="aat-entity-status-summary">
                    <?php echo esc_html(sprintf(__('Most recent appearance: %1$s. %2$s nomination%3$s and %4$s win%5$s recorded for %6$s.', 'academy-awards-table'), (string) ($latest_result['year'] ?: $ordinal(intval($latest_result['ceremony'])) . ' Academy Awards'), number_format_i18n(intval($latest_result['nominations'])), intval($latest_result['nominations']) === 1 ? '' : 's', number_format_i18n(intval($latest_result['wins'])), intval($latest_result['wins']) === 1 ? '' : 's', $label ? $label : strtoupper($id))); ?>
                </p>
            </div>
            <?php if (!empty($latest_result['categories'])) : ?>
                <div class="aat-entity-status-tags">
                    <?php foreach (array_slice($latest_result['categories'], 0, 6) as $result_category) : ?>
                        <span class="aat-entity-status-tag"><?php echo esc_html($result_category); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <div class="aat-stats-bar aat-entity-stats">
        <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_nominations)); ?></span><span class="aat-stat-label">Nominations</span></div>
        <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_wins)); ?></span><span class="aat-stat-label">Wins</span></div>
        <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_categories)); ?></span><span class="aat-stat-label">Categories</span></div>
        <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n($total_ceremonies)); ?></span><span class="aat-stat-label">Ceremonies</span></div>
        <?php if ($entity !== 'title') : ?>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html(number_format_i18n(count($distinct_films))); ?></span><span class="aat-stat-label">Films</span></div>
        <?php endif; ?>
        <?php if ($span) : ?>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo wp_kses_post($span); ?></span><span class="aat-stat-label">Span</span></div>
        <?php endif; ?>
        <?php if ($latest_year) : ?>
            <div class="aat-stat"><span class="aat-stat-number"><?php echo esc_html($latest_year); ?></span><span class="aat-stat-label">Most Recent</span></div>
        <?php endif; ?>
    </div>

    <?php if ($entity === 'title' && !empty($aat_review_ids)) : ?>
        <?php
        $aat_primary_review_id = (int) $aat_review_ids[0];
        $aat_review_url = get_permalink($aat_primary_review_id);
        $aat_review_title = get_the_title($aat_primary_review_id);
        $aat_review_excerpt = get_the_excerpt($aat_primary_review_id);
        $aat_review_excerpt = trim(wp_strip_all_tags((string) $aat_review_excerpt));
        $aat_review_thumb = get_the_post_thumbnail_url($aat_primary_review_id, 'medium');
        ?>
        <section class="aat-lunara-review-module" aria-label="Lunara Film review">
            <div class="aat-lunara-review-inner">
                <?php if (!empty($aat_review_thumb)) : ?>
                    <a class="aat-lunara-review-poster" href="<?php echo esc_url($aat_review_url); ?>">
                        <img src="<?php echo esc_url($aat_review_thumb); ?>" alt="<?php echo esc_attr($aat_review_title); ?>" loading="lazy" decoding="async" />
                    </a>
                <?php endif; ?>
                <div class="aat-lunara-review-content">
                    <div class="aat-lunara-review-kicker">LUNARA FILM REVIEW</div>
                    <h2 class="aat-lunara-review-title"><a href="<?php echo esc_url($aat_review_url); ?>"><?php echo esc_html($aat_review_title); ?></a></h2>
                    <?php if ($aat_review_excerpt !== '') : ?>
                        <p class="aat-lunara-review-excerpt"><?php echo esc_html(wp_trim_words($aat_review_excerpt, 26, '…')); ?></p>
                    <?php endif; ?>
                    <div class="aat-lunara-review-actions">
                        <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($aat_review_url); ?>">Read the Review</a>
                        <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url(home_url('/reviews/')); ?>">Review Archive</a>
                    </div>
                    <?php if (count($aat_review_ids) > 1) : ?>
                        <div class="aat-lunara-review-more">
                            <span class="aat-lunara-review-more-label">Also on Lunara:</span>
                            <?php
                            $more_links = array();
                            foreach (array_slice($aat_review_ids, 1) as $rid) {
                                $rid = (int) $rid;
                                $more_links[] = '<a href="' . esc_url(get_permalink($rid)) . '">' . esc_html(get_the_title($rid)) . '</a>';
                            }
                            echo implode($pipe_separator_html, $more_links); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

        <section class="aat-entity-section aat-entity-timeline">
        <div class="aat-section-head">
            <h2 class="aat-section-title">Oscar History</h2>
            <p class="aat-section-description">Every ceremony touchpoint for this <?php echo esc_html(strtolower($type_label)); ?>, tracked through the Lunara Oscar ledger.</p>
        </div>

        <?php if (empty($rows)) : ?>
            <div class="aat-no-results">
                <div class="aat-no-results-icon">Awards</div>
                <h3>No records found</h3>
                <p>This profile has not yet been matched to a verified Oscar record in the ledger.</p>
            </div>
        <?php else : ?>
            <div class="aat-timeline-list">
                <?php foreach ($timeline as $cer => $group) : ?>
                    <section class="aat-timeline-card">
                        <div class="aat-timeline-meta">
                            <div class="aat-timeline-ceremony"><?php echo esc_html($ordinal($cer)); ?> Ceremony</div>
                            <div class="aat-timeline-year"><?php echo esc_html($group['year']); ?></div>
                        </div>
                        <div class="aat-timeline-body">
                            <?php foreach ($group['rows'] as $r) :
                                $cat = (string) ($r['canonical_category'] ?? $r['category'] ?? '');
                                $cat_url = $aat->get_category_url($cat);
                                $cat_label = $format_category($cat);
                                $is_winner = (!empty($r['winner']) && (int) $r['winner'] === 1);
                            ?>
                                <article class="aat-history-item <?php echo $is_winner ? 'is-winner' : ''; ?>">
                                    <div class="aat-history-main">
                                        <div class="aat-history-category">
                                            <?php if ($cat_url) : ?>
                                                <a class="aat-hub-link" href="<?php echo esc_url($cat_url); ?>"><span class="aat-category-pill"><?php echo esc_html($cat_label); ?></span></a>
                                            <?php else : ?>
                                                <span class="aat-category-pill"><?php echo esc_html($cat_label); ?></span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="aat-history-detail">
                                            <?php if ($entity === 'title') : ?>
                                                <?php $nominee_display = $resolve_title_nominee_display($r); ?>
                                                <?php if (!empty($nominee_display['label'])) : ?>
                                                    <div class="aat-history-line"><strong>Nominee<?php echo !empty($nominee_display['is_plural']) ? 's' : ''; ?>:</strong> <?php echo $render_linked_pipe($nominee_display['label'], $nominee_display['ids']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                                <?php else : ?>
                                                    <div class="aat-history-line aat-history-line-muted">Nominee data is still being verified for this entry.</div>
                                                <?php endif; ?>
                                            <?php else : ?>
                                                <div class="aat-history-line"><strong>Film:</strong> <?php echo $render_linked_pipe($r['film'] ?? '', $r['film_id'] ?? ''); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
                                            <?php endif; ?>

                                            <?php if (!empty($r['detail'])) : ?>
                                                <div class="aat-history-line"><strong>Detail:</strong> <?php echo esc_html((string) $r['detail']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($r['citation'])) : ?>
                                                <div class="aat-history-line"><strong>Citation:</strong> <?php echo esc_html((string) $r['citation']); ?></div>
                                            <?php endif; ?>
                                            <?php if (!empty($r['note'])) : ?>
                                                <div class="aat-history-line"><strong>Note:</strong> <?php echo esc_html((string) $r['note']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="aat-history-status">
                                        <?php if ($is_winner) : ?>
                                            <span class="aat-winner-badge">Winner</span>
                                        <?php else : ?>
                                            <span class="aat-nominee-badge">Nominee</span>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>


    <?php if ($entity !== 'title' && !empty($distinct_films)) : ?>
        <section class="aat-entity-section aat-filmography-section">
            <div class="aat-section-head">
                <h2 class="aat-section-title">Nominated Films</h2>
                <p class="aat-section-description">Poster-first passage through the films that shape this Oscar trail.</p>
            </div>
            <div class="aat-filmography-grid">
                <?php foreach (array_keys($distinct_films) as $fid) :
                    $fid = trim((string) $fid);
                    if (!$fid) { continue; }
                    $film_label = method_exists($aat, 'lookup_title_label') ? $aat->lookup_title_label($fid) : '';
                    if (!$film_label && method_exists($aat, 'get_entity_display_name')) {
                        $film_label = $aat->get_entity_display_name('title', $fid);
                    }
                    if (!$film_label) { $film_label = strtoupper($fid); }
                    $poster_html = $aat->get_poster_img_html_for_title($fid, 'medium_large', array('class' => 'aat-filmography-poster'));
                    $tmdb_item = method_exists($aat, 'get_tmdb_data_for_imdb_id') ? $aat->get_tmdb_data_for_imdb_id($fid) : array();
                    $film_url = $build_entity_url($fid);
                ?>
                    <article class="aat-filmography-card">
                        <a class="aat-filmography-link" href="<?php echo esc_url($film_url ? $film_url : $database_url); ?>">
                            <div class="aat-filmography-poster-wrap">
                                <?php if ($poster_html) : ?>
                                    <?php echo $poster_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif (!empty($tmdb_item['poster_full'])) : ?>
                                    <img class="aat-filmography-poster" src="<?php echo esc_url($tmdb_item['poster_full']); ?>" alt="<?php echo esc_attr($film_label); ?> poster" loading="lazy" decoding="async" />
                                <?php else : ?>
                                    <?php $film_visual = method_exists($aat, 'get_title_visual_package') ? $aat->get_title_visual_package($fid, 'medium_large') : array(); ?>
                                    <?php if (!empty($film_visual['card_fallback_html'])) : ?>
                                        <?php echo $film_visual['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php else : ?>
                                        <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($film_label); ?></span></div>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            <h3 class="aat-filmography-title"><?php echo esc_html($film_label); ?></h3>
                            <?php if (!empty($tmdb_item['release_date'])) : ?><p class="aat-filmography-meta"><?php echo esc_html(substr($tmdb_item['release_date'], 0, 4)); ?></p><?php endif; ?>
                        </a>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($entity !== 'title' && !empty($aat_related_reviews)) : ?>
        <section class="aat-entity-section aat-related-reviews-section">
            <div class="aat-section-head">
                <h2 class="aat-section-title">On Lunara</h2>
                <p class="aat-section-description">Criticism from the Lunara archive that keeps this Oscar history tied to the writing.</p>
            </div>
            <div class="aat-related-reviews-grid">
                <?php foreach ($aat_related_reviews as $related_review) : ?>
                    <?php $related_review_excerpt = trim(wp_strip_all_tags((string) ($related_review['review_excerpt'] ?? ''))); ?>
                    <article class="aat-related-review-card">
                        <a class="aat-related-review-media" href="<?php echo esc_url($related_review['review_url']); ?>">
                            <?php if (!empty($related_review['review_thumb'])) : ?>
                                <img class="aat-related-review-image" src="<?php echo esc_url($related_review['review_thumb']); ?>" alt="<?php echo esc_attr($related_review['review_title']); ?>" loading="lazy" decoding="async" />
                            <?php elseif (!empty($related_review['fallback_html'])) : ?>
                                <?php echo $related_review['fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                            <?php else : ?>
                                <div class="aat-filmography-poster-placeholder"><span><?php echo esc_html($related_review['film_label']); ?></span></div>
                            <?php endif; ?>
                        </a>
                        <div class="aat-related-review-body">
                            <div class="aat-related-review-kicker">LUNARA FILM REVIEW</div>
                            <h3 class="aat-related-review-title"><a href="<?php echo esc_url($related_review['review_url']); ?>"><?php echo esc_html($related_review['review_title']); ?></a></h3>
                            <p class="aat-related-review-meta">
                                <?php if (!empty($related_review['film_url'])) : ?>
                                    <a href="<?php echo esc_url($related_review['film_url']); ?>"><?php echo esc_html($related_review['film_label']); ?></a>
                                <?php else : ?>
                                    <span><?php echo esc_html($related_review['film_label']); ?></span>
                                <?php endif; ?>
                            <?php if (!empty($related_review['film_year'])) : ?>
                                    <span class="aat-sep"> &middot; </span><span><?php echo esc_html($related_review['film_year']); ?></span>
                                <?php endif; ?>
                            </p>
                            <?php if ($related_review_excerpt !== '') : ?>
                                <p class="aat-related-review-excerpt"><?php echo esc_html(wp_trim_words($related_review_excerpt, 24, '…')); ?></p>
                            <?php endif; ?>
                            <div class="aat-related-review-actions">
                                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($related_review['review_url']); ?>">Read Review</a>
                                <?php if (!empty($related_review['film_url'])) : ?>
                                    <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($related_review['film_url']); ?>"><?php echo esc_html__('Title Profile', 'academy-awards-table'); ?></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <div class="aat-footer">
        <p>Data sourced from the Academy of Motion Picture Arts and Sciences. Structured dataset compiled and maintained by Lunara Film.</p>
        <p>Profiles are generated directly from the Lunara Film Oscars dataset. New nominations and winners appear automatically after each annual import.</p>
    </div>
</div>
<?php get_footer();
