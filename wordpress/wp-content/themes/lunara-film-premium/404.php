<?php
/**
 * 404 template.
 *
 * @package Lunara_Film
 */

get_header();
?>
<main id="primary" class="site-main lunara-archive-page lunara-404-page">
    <section class="lunara-home-section lunara-archive-hero">
        <div class="lunara-editorial-archive-hero-shell">
            <div class="lunara-editorial-archive-hero-copy-wrap">
                <p class="lunara-archive-hero-kicker"><?php esc_html_e( 'Lost Signal', 'lunara-film' ); ?></p>
                <h1 class="lunara-archive-hero-title"><?php esc_html_e( 'This page is not on the record.', 'lunara-film' ); ?></h1>
                <p class="lunara-archive-hero-copy"><?php esc_html_e( 'The route you followed does not currently resolve inside Lunara Film. The publication shell is still intact, and the quickest way back is through the front door, the reviews, or the Oscars ledger.', 'lunara-film' ); ?></p>
            </div>
            <aside class="lunara-editorial-archive-debrief" aria-label="<?php esc_attr_e( 'Recovery options', 'lunara-film' ); ?>">
                <p class="lunara-editorial-archive-debrief-kicker"><?php esc_html_e( 'Recovery Route', 'lunara-film' ); ?></p>
                <ul class="lunara-editorial-archive-debrief-list">
                    <li>
                        <strong><?php esc_html_e( 'Best Reset', 'lunara-film' ); ?></strong>
                        <span><?php esc_html_e( 'Return to the homepage and re-enter through the front door.', 'lunara-film' ); ?></span>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'Fastest Route', 'lunara-film' ); ?></strong>
                        <span><?php esc_html_e( 'Search a title, filmmaker, or Oscar category below.', 'lunara-film' ); ?></span>
                    </li>
                    <li>
                        <strong><?php esc_html_e( 'Stable Hubs', 'lunara-film' ); ?></strong>
                        <span><?php esc_html_e( 'Reviews / Journal / Oscar Ledger', 'lunara-film' ); ?></span>
                    </li>
                </ul>
            </aside>
        </div>
    </section>

    <section class="lunara-home-section lunara-404-shell">
        <div class="lunara-404-panel">
            <div class="lunara-home-section-head">
                <div>
                    <p class="lunara-home-section-kicker"><?php esc_html_e( 'Re-enter The Site', 'lunara-film' ); ?></p>
                    <h2 class="lunara-section-title"><?php esc_html_e( 'Choose the cleanest way back into Lunara World.', 'lunara-film' ); ?></h2>
                    <p class="lunara-editorial-archive-run-copy"><?php esc_html_e( 'The goal here is recovery without friction: get back to criticism, the Journal, or the Oscar ledger in one move.', 'lunara-film' ); ?></p>
                </div>
            </div>

            <div class="lunara-404-actions">
                <a class="lunara-btn lunara-btn-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go Home', 'lunara-film' ); ?></a>
                <a class="lunara-btn lunara-btn-secondary" href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>"><?php esc_html_e( 'Open Reviews', 'lunara-film' ); ?></a>
                <a class="lunara-btn lunara-btn-secondary" href="<?php echo esc_url( home_url( '/oscars/' ) ); ?>"><?php esc_html_e( 'Open The Ledger', 'lunara-film' ); ?></a>
            </div>

            <form role="search" method="get" class="lunara-search-form lunara-search-form-shell" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                <label class="screen-reader-text" for="lunara-404-search-input"><?php esc_html_e( 'Search for:', 'lunara-film' ); ?></label>
                <input id="lunara-404-search-input" type="search" class="lunara-search-input" placeholder="<?php esc_attr_e( 'Search Lunara Film', 'lunara-film' ); ?>" value="" name="s" />
                <button type="submit" class="lunara-btn lunara-btn-primary"><?php esc_html_e( 'Search', 'lunara-film' ); ?></button>
            </form>
        </div>
    </section>
</main>
<?php
get_footer();
