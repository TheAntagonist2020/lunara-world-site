<?php
/**
 * Generic archive template for Lunara editorial archives.
 */

get_header();

$archive_title  = trim( wp_strip_all_tags( get_the_archive_title() ) );
$archive_copy   = trim( wp_strip_all_tags( get_the_archive_description() ) );
$archive_kicker = __( 'Archive', 'lunara-film' );
$source_label   = __( 'Editorial file', 'lunara-film' );
$run_title      = __( 'More From The Archive', 'lunara-film' );
$run_copy       = __( 'The broader run stays browseable and poster-led, but now lives inside the same deliberate editorial grammar as the rest of Lunara.', 'lunara-film' );
$rail_title     = __( 'What The Archive Is Holding Beside The Lead', 'lunara-film' );
$rail_copy      = __( 'A tighter supporting stack so this page reads like a live Lunara lane instead of a generic archive.', 'lunara-film' );
$empty_note_title = __( 'Dispatches, reactions, essays, and signal worth following.', 'lunara-film' );
$empty_note_copy  = __( 'This lane is for the part of Lunara that moves with the moment: news, reactions, interviews, longer arguments, and the pieces that keep the publication alive between the review tentpoles.', 'lunara-film' );

if ( is_category() ) {
    $archive_kicker = __( 'Category Archive', 'lunara-film' );
    $source_label   = __( 'Category file', 'lunara-film' );
} elseif ( is_tag() ) {
    $archive_kicker = __( 'Tagged Signal', 'lunara-film' );
    $source_label   = __( 'Tag focus', 'lunara-film' );
    $run_title      = __( 'More Tagged Signal', 'lunara-film' );
    $run_copy       = __( 'These pieces are linked by the same conversational marker, but they should still read like part of one authored publication.', 'lunara-film' );
    $rail_title     = __( 'What This Tag Is Holding Beside The Lead', 'lunara-film' );
    $empty_note_title = __( 'Tagged routes become useful once the archive starts echoing itself.', 'lunara-film' );
    $empty_note_copy  = __( 'When posts begin sharing this tag, this page will turn into a cleaner trail through a recurring subject, person, or conversation inside Lunara.', 'lunara-film' );
} elseif ( is_author() ) {
    $archive_kicker = __( 'Byline Archive', 'lunara-film' );
    $source_label   = __( 'Byline focus', 'lunara-film' );
    $run_title      = __( 'More From This Byline', 'lunara-film' );
    $run_copy       = __( 'This archive should feel like a coherent voice trail, not a bare author listing.', 'lunara-film' );
    $rail_title     = __( 'What This Byline Is Holding Beside The Lead', 'lunara-film' );
    $empty_note_title = __( 'Byline archives become richer as the publication deepens.', 'lunara-film' );
    $empty_note_copy  = __( 'Once more work is published under this byline, the archive will become a sharper record of voice, emphasis, and editorial range.', 'lunara-film' );
} elseif ( is_date() ) {
    $archive_kicker = __( 'Calendar File', 'lunara-film' );
    $source_label   = __( 'Calendar focus', 'lunara-film' );
    $run_title      = __( 'More From This Filing Window', 'lunara-film' );
    $run_copy       = __( 'This route is for chronology, but it should still feel like Lunara rather than a default date archive.', 'lunara-film' );
    $rail_title     = __( 'What This Filing Window Is Holding Beside The Lead', 'lunara-film' );
    $empty_note_title = __( 'Calendar files matter once they start recording motion.', 'lunara-film' );
    $empty_note_copy  = __( 'As this publication keeps moving, date-based archives become a useful way to revisit specific stretches of coverage without losing the authored shell.', 'lunara-film' );
}

if ( '' === $archive_title ) {
    $archive_title = __( 'Archive', 'lunara-film' );
}

if ( '' === $archive_copy ) {
    $archive_copy = lunara_theme_mod_text(
        'lunara_journal_archive_copy',
        'This archive collects the editorial side of Lunara Film, including news, reactions, essays, and audio-driven pieces.'
    );
}

echo lunara_render_editorial_archive_shell(
    array(
        'classes'     => 'lunara-editorial-archive-page',
        'kicker'      => $archive_kicker,
        'title'       => $archive_title,
        'copy'        => $archive_copy,
        'posts'       => lunara_get_loop_posts(),
        'source_label'=> $source_label,
        'lead_rail_title' => $rail_title,
        'lead_rail_copy'  => $rail_copy,
        'run_title'       => $run_title,
        'run_copy'        => $run_copy,
        'empty_note_title'=> $empty_note_title,
        'empty_note_copy' => $empty_note_copy,
        'empty_title' => __( 'Nothing has been filed in this archive yet.', 'lunara-film' ),
    )
);

get_footer();
