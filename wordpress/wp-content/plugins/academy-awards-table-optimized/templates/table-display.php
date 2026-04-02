<?php
/**
 * Academy Awards Table - Frontend Display Template
 * This template renders the interactive awards table or, on the main database page,
 * a lightweight landing view that defers the heavy explorer until requested.
 */

if (!defined('ABSPATH')) {
    exit;
}

$layout = isset($atts['layout']) ? (string) $atts['layout'] : 'full';
$layout = in_array($layout, array('full', 'embedded'), true) ? $layout : 'full';

$autoload_attr = isset($atts['autoload']) ? strtolower(trim((string) $atts['autoload'])) : '';
$table_view_requested = isset($_GET['view']) && sanitize_key(wp_unslash($_GET['view'])) === 'table';
$autoload_table = ($layout === 'embedded') || $table_view_requested || in_array($autoload_attr, array('true', '1', 'yes'), true);

$aat_instance = Academy_Awards_Table::get_instance();
global $wpdb;
$aat_table = $wpdb->prefix . 'academy_awards';
$aat_ceremony_count = intval($wpdb->get_var("SELECT COUNT(DISTINCT ceremony) FROM $aat_table"));
$aat_min_ceremony = intval($wpdb->get_var("SELECT MIN(ceremony) FROM $aat_table"));
$aat_max_ceremony = intval($wpdb->get_var("SELECT MAX(ceremony) FROM $aat_table"));
$aat_record_count = intval($wpdb->get_var("SELECT COUNT(*) FROM $aat_table"));
$aat_winner_count = intval($wpdb->get_var("SELECT COUNT(*) FROM $aat_table WHERE winner = 1"));
$aat_category_count = intval($wpdb->get_var("SELECT COUNT(DISTINCT canonical_category) FROM $aat_table WHERE canonical_category != ''"));
$aat_span = '';
if ($aat_min_ceremony > 0 && $aat_max_ceremony > 0) {
    $first_year = $aat_instance->get_ceremony_year($aat_min_ceremony);
    $last_year = $aat_instance->get_ceremony_year($aat_max_ceremony);
    if ($first_year && $last_year) {
        $aat_span = $first_year . ' - ' . $last_year;
    }
}
?>

<div
    class="aat-container<?php echo ($layout === 'embedded') ? ' aat-embedded' : ''; ?><?php echo (!$autoload_table && $layout === 'full') ? ' aat-database-landing-shell' : ''; ?>"
    data-initial-category="<?php echo esc_attr($atts['category'] ?? ''); ?>"
    data-initial-class="<?php echo esc_attr($atts['class'] ?? ''); ?>"
    data-initial-year="<?php echo esc_attr($atts['year'] ?? ''); ?>"
    data-initial-ceremony="<?php echo esc_attr($atts['ceremony'] ?? ''); ?>"
    data-initial-winners-only="<?php echo esc_attr($atts['winners_only'] ?? 'false'); ?>"
>
    <?php if ($layout === 'full' && !$autoload_table) : ?>
        <?php
            $rollup = method_exists($aat_instance, 'get_ceremony_rollup') ? $aat_instance->get_ceremony_rollup($aat_max_ceremony) : array();
            $best_picture = !empty($rollup['best_picture']) ? $rollup['best_picture'] : array();
            $top_titles = method_exists($aat_instance, 'get_ceremony_title_highlights') ? $aat_instance->get_ceremony_title_highlights($aat_max_ceremony, 6) : array();
            $winner_rows = !empty($rollup['winner_rows']) && is_array($rollup['winner_rows']) ? array_slice($rollup['winner_rows'], 0, 6) : array();
            $table_view_url = add_query_arg('view', 'table');
        ?>
        <div class="aat-header aat-database-landing-header">
            <img
                class="aat-oscar-icon"
                src="<?php echo esc_url(AAT_PLUGIN_URL . 'assets/img/oscar.png'); ?>"
                alt="<?php echo esc_attr__('Oscar statuette', 'academy-awards-table'); ?>"
                loading="lazy"
            />
            <h2><?php esc_html_e('The Lunara Oscar Ledger', 'academy-awards-table'); ?></h2>
            <div class="aat-hub-actions aat-database-landing-actions">
                <a class="aat-btn aat-btn-primary" href="<?php echo esc_url($table_view_url); ?>"><?php esc_html_e('Open Table View', 'academy-awards-table'); ?></a>
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($aat_instance->get_ceremonies_index_url()); ?>"><?php esc_html_e('Browse Ceremonies', 'academy-awards-table'); ?></a>
                <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($aat_instance->get_categories_index_url()); ?>"><?php esc_html_e('Browse Categories', 'academy-awards-table'); ?></a>
            </div>
        </div>

        <div class="aat-hub-metric-grid aat-database-landing-metrics">
            <article class="aat-hub-metric-card">
                <span class="aat-hub-metric-label"><?php esc_html_e('Records', 'academy-awards-table'); ?></span>
                <strong class="aat-hub-metric-value"><?php echo esc_html(number_format_i18n($aat_record_count)); ?></strong>
            </article>
            <article class="aat-hub-metric-card">
                <span class="aat-hub-metric-label"><?php esc_html_e('Winners', 'academy-awards-table'); ?></span>
                <strong class="aat-hub-metric-value"><?php echo esc_html(number_format_i18n($aat_winner_count)); ?></strong>
            </article>
            <article class="aat-hub-metric-card">
                <span class="aat-hub-metric-label"><?php esc_html_e('Categories', 'academy-awards-table'); ?></span>
                <strong class="aat-hub-metric-value"><?php echo esc_html(number_format_i18n($aat_category_count)); ?></strong>
            </article>
            <article class="aat-hub-metric-card">
                <span class="aat-hub-metric-label"><?php esc_html_e('Span', 'academy-awards-table'); ?></span>
                <strong class="aat-hub-metric-value"><?php echo esc_html($aat_span ? $aat_span : '-'); ?></strong>
            </article>
        </div>

        <?php if (!empty($rollup)) : ?>
            <section class="aat-hub-section aat-ceremony-marquee">
                <div class="aat-ceremony-marquee-copy">
                    <p class="aat-hub-kicker"><?php echo esc_html__('Latest Ceremony', 'academy-awards-table'); ?></p>
                    <h2>
                        <?php
                            echo esc_html(
                                sprintf(
                                    __('%1$s Academy Awards', 'academy-awards-table'),
                                    $aat_instance->ordinal($aat_max_ceremony)
                                )
                            );
                        ?>
                    </h2>
                    <p class="aat-hub-copy">
                        <?php if (!empty($best_picture['film'])) : ?>
                            <?php echo esc_html(sprintf(__('Best Picture: %s.', 'academy-awards-table'), $best_picture['film'])); ?>
                        <?php endif; ?>
                        <?php if (!empty($rollup['most_wins']['film']) && !empty($rollup['most_wins']['wins'])) : ?>
                            <?php echo esc_html(sprintf(__('The biggest winner was %1$s with %2$s win%3$s.', 'academy-awards-table'), $rollup['most_wins']['film'], number_format_i18n(intval($rollup['most_wins']['wins'])), intval($rollup['most_wins']['wins']) === 1 ? '' : 's')); ?>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="aat-ceremony-marquee-stack">
                    <a class="aat-hub-chip aat-hub-chip-rich" href="<?php echo esc_url($aat_instance->get_ceremony_url($aat_max_ceremony)); ?>">
                        <strong><?php esc_html_e('Open Ceremony Page', 'academy-awards-table'); ?></strong>
                        <span><?php echo esc_html($aat_instance->get_ceremony_year($aat_max_ceremony)); ?></span>
                    </a>
                    <a class="aat-hub-chip aat-hub-chip-rich" href="<?php echo esc_url($table_view_url); ?>">
                        <strong><?php esc_html_e('Open Table View', 'academy-awards-table'); ?></strong>
                        <span><?php esc_html_e('Sort and filter entries', 'academy-awards-table'); ?></span>
                    </a>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($top_titles)) : ?>
            <div class="aat-hub-section aat-ceremony-gallery-section">
                <h2><?php esc_html_e('Poster Highlights', 'academy-awards-table'); ?></h2>
                <div class="aat-filmography-grid aat-hub-film-grid">
                    <?php foreach ($top_titles as $entry) :
                        $fid = strtolower(trim((string) ($entry['film_id'] ?? '')));
                        if (!$fid) { continue; }
                        $visual = method_exists($aat_instance, 'get_title_visual_package') ? $aat_instance->get_title_visual_package($fid, 'medium_large') : array();
                        $film_label = !empty($entry['film']) ? (string) $entry['film'] : $aat_instance->lookup_title_label($fid);
                        $film_url = $aat_instance->get_entity_url($fid);
                    ?>
                        <article class="aat-filmography-card aat-hub-film-card<?php echo !empty($entry['winner']) ? ' is-winner' : ''; ?>">
                            <a class="aat-filmography-link" href="<?php echo esc_url($film_url ? $film_url : $table_view_url); ?>">
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
                                    <?php if (!empty($entry['winner'])) : ?><span class="aat-winner-badge aat-card-badge"><?php esc_html_e('Winner', 'academy-awards-table'); ?></span><?php endif; ?>
                                </div>
                                <h3 class="aat-filmography-title"><?php echo esc_html($film_label); ?></h3>
                                <?php if (!empty($entry['year'])) : ?><p class="aat-filmography-meta"><?php echo esc_html($entry['year']); ?></p><?php endif; ?>
                            </a>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if (!empty($winner_rows)) : ?>
            <div class="aat-hub-section aat-winner-circle-section">
                <h2><?php esc_html_e('Latest Winner Circle', 'academy-awards-table'); ?></h2>
                <div class="aat-winner-circle-grid">
                    <?php foreach ($winner_rows as $winner_entry) :
                        $primary_label = trim((string) ($winner_entry['name'] ?? ''));
                        if ($primary_label === '') {
                            $primary_label = trim((string) ($winner_entry['film'] ?? ''));
                        }
                        $secondary_bits = array();
                        if (!empty($winner_entry['film']) && $winner_entry['film'] !== $primary_label) {
                            $secondary_bits[] = $winner_entry['film'];
                        }
                        if (!empty($winner_entry['detail']) && $winner_entry['detail'] !== $primary_label) {
                            $secondary_bits[] = $winner_entry['detail'];
                        }
                        $secondary_label = implode(' | ', array_slice($secondary_bits, 0, 2));
                        $category_url = $aat_instance->get_category_url($winner_entry['canonical_category'] ?? '');
                    ?>
                        <article class="aat-winner-circle-card">
                            <div class="aat-winner-circle-top">
                                <?php if ($category_url) : ?>
                                    <a class="aat-winner-circle-category" href="<?php echo esc_url($category_url); ?>"><?php echo esc_html($winner_entry['category_label']); ?></a>
                                <?php else : ?>
                                    <span class="aat-winner-circle-category"><?php echo esc_html($winner_entry['category_label']); ?></span>
                                <?php endif; ?>
                                <span class="aat-winner-badge"><?php esc_html_e('Winner', 'academy-awards-table'); ?></span>
                            </div>
                            <h3 class="aat-winner-circle-title"><?php echo esc_html($primary_label); ?></h3>
                            <?php if ($secondary_label !== '') : ?><p class="aat-winner-circle-meta"><?php echo esc_html($secondary_label); ?></p><?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <?php if ($layout === 'full') : ?>
            <?php $poster_view_url = remove_query_arg('view'); ?>
            <div class="aat-explorer-shell">
                <div class="aat-explorer-copy">
                    <h2><?php esc_html_e('Table View', 'academy-awards-table'); ?></h2>
                </div>
                <div class="aat-hub-actions aat-view-toggle">
                    <a class="aat-btn aat-btn-secondary" href="<?php echo esc_url($poster_view_url); ?>"><?php esc_html_e('Poster View', 'academy-awards-table'); ?></a>
                    <span class="aat-btn aat-btn-primary is-active"><?php esc_html_e('Table View', 'academy-awards-table'); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($layout === 'full') : ?>
        <div class="aat-header">
            <img
                class="aat-oscar-icon"
                src="<?php echo esc_url(AAT_PLUGIN_URL . 'assets/img/oscar.png'); ?>"
                alt="<?php echo esc_attr__('Oscar statuette', 'academy-awards-table'); ?>"
                loading="lazy"
            />
            <h2><?php esc_html_e('The Lunara Oscar Ledger', 'academy-awards-table'); ?></h2>
        </div>

        <div class="aat-stats-bar">
            <div class="aat-stat">
                <span class="aat-stat-number" id="aat-stat-total">&mdash;</span>
                <span class="aat-stat-label"><?php esc_html_e('Total Nominations', 'academy-awards-table'); ?></span>
            </div>
            <div class="aat-stat">
                <span class="aat-stat-number" id="aat-stat-winners">&mdash;</span>
                <span class="aat-stat-label"><?php esc_html_e('Winners', 'academy-awards-table'); ?></span>
            </div>
            <div class="aat-stat">
                <span class="aat-stat-number" id="aat-stat-categories">&mdash;</span>
                <span class="aat-stat-label"><?php esc_html_e('Categories', 'academy-awards-table'); ?></span>
            </div>
            <div class="aat-stat">
                <span class="aat-stat-number" id="aat-stat-ceremonies">&mdash;</span>
                <span class="aat-stat-label"><?php esc_html_e('Ceremonies', 'academy-awards-table'); ?></span>
            </div>
        </div>

        <div class="aat-quick-filters">
            <!-- Quick filters populated by JavaScript -->
        </div>
        <?php endif; ?>

        <details class="aat-filters-disclosure" open>
            <summary><?php esc_html_e('Filters', 'academy-awards-table'); ?></summary>
            <div class="aat-filters">
                <div class="aat-filter-group">
                    <label for="aat-filter-category"><?php esc_html_e('Category', 'academy-awards-table'); ?></label>
                    <select id="aat-filter-category">
                        <option value=""><?php esc_html_e('All Categories', 'academy-awards-table'); ?></option>
                    </select>
                </div>

                <div class="aat-filter-group">
                    <label for="aat-filter-class"><?php esc_html_e('Type', 'academy-awards-table'); ?></label>
                    <select id="aat-filter-class">
                        <option value=""><?php esc_html_e('All Types', 'academy-awards-table'); ?></option>
                    </select>
                </div>

                <div class="aat-filter-group">
                    <label for="aat-filter-year"><?php esc_html_e('Year', 'academy-awards-table'); ?></label>
                    <select id="aat-filter-year">
                        <option value=""><?php esc_html_e('All Years', 'academy-awards-table'); ?></option>
                    </select>
                </div>

                <div class="aat-filter-group">
                    <label for="aat-filter-ceremony"><?php esc_html_e('Ceremony', 'academy-awards-table'); ?></label>
                    <select id="aat-filter-ceremony">
                        <option value=""><?php esc_html_e('All Ceremonies', 'academy-awards-table'); ?></option>
                    </select>
                </div>

                <div class="aat-filter-group aat-checkbox-group">
                    <input type="checkbox" id="aat-filter-winners">
                    <label for="aat-filter-winners"><?php esc_html_e('Winners Only', 'academy-awards-table'); ?></label>
                </div>

                <div class="aat-filter-group aat-filter-actions">
                    <button type="button" class="aat-btn aat-btn-secondary aat-btn-reset">
                        <?php esc_html_e('Reset', 'academy-awards-table'); ?>
                    </button>
                </div>
            </div>
        </details>

        <div class="aat-table-wrapper">
            <div class="aat-loading">
                <div class="aat-loading-spinner"></div>
                <span class="aat-loading-text"><?php esc_html_e('Loading Academy Awards data...', 'academy-awards-table'); ?></span>
            </div>
        </div>

        <div class="aat-footer">
            <p class="aat-footer-line">
                <span class="aat-footer-sep"><?php echo esc_html(number_format_i18n($aat_ceremony_count)); ?> ceremonies<?php if ($aat_span) : ?> (<?php echo esc_html($aat_span); ?>)<?php endif; ?></span>
            </p>
            <p class="aat-footer-links">
                <a href="<?php echo esc_url(Academy_Awards_Table::get_instance()->get_ceremonies_index_url()); ?>"><?php esc_html_e('Ceremonies', 'academy-awards-table'); ?></a>
                <span class="aat-footer-sep">&middot;</span>
                <a href="<?php echo esc_url(Academy_Awards_Table::get_instance()->get_categories_index_url()); ?>"><?php esc_html_e('Categories', 'academy-awards-table'); ?></a>
                <span class="aat-footer-sep">&middot;</span>
                <a href="<?php echo esc_url(Academy_Awards_Table::get_instance()->get_about_url()); ?>"><?php esc_html_e('About the ledger', 'academy-awards-table'); ?></a>
            </p>
        </div>
    <?php endif; ?>
</div>
