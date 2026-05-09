<?php
/**
 * Blocksy-powered header shell for the Lunara child theme.
 *
 * This keeps the child theme in the template hierarchy while handing header
 * structure and builder controls back to the Blocksy parent theme.
 *
 * @package Lunara_Film
 */

?><!doctype html>
<html <?php language_attributes(); ?><?php echo blocksy_html_attr(); ?>>
<head>
    <?php do_action( 'blocksy:head:start' ); ?>

    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover">
    <link rel="profile" href="https://gmpg.org/xfn/11">

    <?php wp_head(); ?>
    <?php do_action( 'blocksy:head:end' ); ?>
</head>

<?php
ob_start();
blocksy_output_header();
$global_header = ob_get_clean();
?>

<body <?php body_class(); ?> <?php echo blocksy_body_attr(); ?>>

<?php
if ( function_exists( 'wp_body_open' ) ) {
    wp_body_open();
}
?>

<div id="main-container">
    <?php
    do_action( 'blocksy:header:before' );

    echo $global_header;

    do_action( 'blocksy:header:after' );
    do_action( 'blocksy:content:before' );
    ?>

    <main <?php echo blocksy_main_attr(); ?>>
        <?php
        do_action( 'blocksy:content:top' );
        blocksy_before_current_template();
        ?>
