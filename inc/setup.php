<?php
/**
 * Theme setup, enqueue, and utility helpers.
 *
 * @package Lunara_Film
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue theme styles.
 */
function lunara_enqueue_styles() {
    // Base reset (replaces Blocksy parent base styles).
    $base_css = lunara_resolve_theme_asset( 'assets/css/lunara-base.css' );
    if ( ! empty( $base_css['uri'] ) ) {
        wp_enqueue_style(
            'lunara-base',
            $base_css['uri'],
            array(),
            lunara_theme_asset_version( $base_css['path'] )
        );
    }

    wp_enqueue_style(
        'lunara-style',
        get_stylesheet_uri(),
        array( 'lunara-base' ),
        filemtime( get_stylesheet_directory() . '/style.css' )
    );

    if ( is_page( 'oscars' ) || is_page_template( 'page-oscars.php' ) ) {
        $oscars_css = lunara_resolve_theme_asset(
            'assets/css/oscars.css',
            array( 'oscars/oscars.css' )
        );

        if ( ! empty( $oscars_css['uri'] ) ) {
            wp_enqueue_style(
                'lunara-oscars-shell',
                $oscars_css['uri'],
                array( 'lunara-style' ),
                lunara_theme_asset_version( $oscars_css['path'] )
            );
        }
    }
}
add_action( 'wp_enqueue_scripts', 'lunara_enqueue_styles' );

/**
 * Theme setup
 */
function lunara_theme_setup() {
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ) );

    add_theme_support( 'html5', array(
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'script',
        'style',
    ) );

    // Register navigation menu
    register_nav_menus( array(
        'primary'          => __( 'Primary Menu', 'lunara-film' ),
        'footer'           => __( 'Footer Menu', 'lunara-film' ),
        'footer-editorial' => __( 'Footer Editorial', 'lunara-film' ),
        'footer-oscars'    => __( 'Footer Oscars', 'lunara-film' ),
        'footer-utility'   => __( 'Footer Utility', 'lunara-film' ),
    ) );
}
add_action( 'after_setup_theme', 'lunara_theme_setup' );

/**
 * Ensure the custom logo always ships with meaningful accessibility text.
 */
function lunara_filter_custom_logo_image_attributes( $attr, $custom_logo_id, $blog_id ) {
    unset( $custom_logo_id, $blog_id );

    $alt = isset( $attr['alt'] ) ? trim( (string) $attr['alt'] ) : '';

    if ( '' === $alt ) {
        $attr['alt'] = sprintf( __( '%s logo', 'lunara-film' ), get_bloginfo( 'name' ) );
    }

    return $attr;
}
add_filter( 'get_custom_logo_image_attributes', 'lunara_filter_custom_logo_image_attributes', 10, 3 );

/**
 * Optional escape hatch for forcing the legacy Lunara shell.
 *
 * By default we now allow Blocksy to render its native header builder again so
 * the parent theme regains control over the shell UI. A filter remains in place
 * in case we need to temporarily restore the old child-theme header path during
 * follow-up work.
 */
if ( apply_filters( 'lunara_disable_blocksy_header', false ) ) {
    add_filter( 'blocksy_has_header', '__return_false' );
}

/**
 * Resolve a child-theme asset across the canonical assets tree and older flat-file layouts.
 */
function lunara_resolve_theme_asset( $preferred, $fallbacks = array() ) {
    $candidates = array_merge( array( $preferred ), (array) $fallbacks );

    foreach ( $candidates as $relative ) {
        $relative = ltrim( str_replace( '\\', '/', (string) $relative ), '/' );
        if ( $relative === '' ) {
            continue;
        }

        $path = trailingslashit( get_stylesheet_directory() ) . $relative;
        if ( file_exists( $path ) ) {
            return array(
                'path' => $path,
                'uri'  => trailingslashit( get_stylesheet_directory_uri() ) . $relative,
            );
        }
    }

    return array(
        'path' => '',
        'uri'  => '',
    );
}

/**
 * Use file modification time when possible so asset changes bust cache automatically.
 */
function lunara_theme_asset_version( $path ) {
    if ( $path && file_exists( $path ) ) {
        return (string) filemtime( $path );
    }

    return wp_get_theme()->get( 'Version' );
}

/**
 * Return a non-empty theme mod string or a fallback.
 */
if ( ! function_exists( 'lunara_theme_mod_text' ) ) {
    function lunara_theme_mod_text( $setting, $default = '' ) {
        $value = trim( (string) get_theme_mod( $setting, '' ) );

        return $value !== '' ? $value : $default;
    }
}

/**
 * Return a non-empty theme mod URL or a fallback.
 */
if ( ! function_exists( 'lunara_theme_mod_url' ) ) {
    function lunara_theme_mod_url( $setting, $default = '' ) {
        $value = esc_url_raw( (string) get_theme_mod( $setting, '' ) );

        return $value !== '' ? $value : $default;
    }
}

/**
 * Keep a stable QR URL for festival outreach while allowing the destination to change.
 */
function lunara_handle_festival_qr_redirect() {
    $qr_key = isset( $_GET['lunara_qr'] ) ? sanitize_key( wp_unslash( $_GET['lunara_qr'] ) ) : '';

    if ( 'festival' !== $qr_key ) {
        return;
    }

    $target = lunara_theme_mod_url( 'lunara_festival_qr_target_url', home_url( '/' ) );
    if ( '' === $target ) {
        $target = home_url( '/' );
    }

    $target_host = wp_parse_url( $target, PHP_URL_HOST );
    $home_host   = wp_parse_url( home_url( '/' ), PHP_URL_HOST );

    if ( $target_host && $home_host && strtolower( $target_host ) === strtolower( $home_host ) ) {
        $target = add_query_arg(
            array(
                'utm_source'   => 'festival_qr',
                'utm_medium'   => 'qr',
                'utm_campaign' => 'festival_outreach',
            ),
            $target
        );
    }

    wp_safe_redirect( $target, 302, 'Lunara Festival QR' );
    exit;
}
add_action( 'template_redirect', 'lunara_handle_festival_qr_redirect', 0 );

/**
 * Treat /news/ as a legacy doorway into the Journal surface.
 */
if ( ! function_exists( 'lunara_handle_legacy_news_redirect' ) ) {
    function lunara_handle_legacy_news_redirect() {
        if ( is_admin() || wp_doing_ajax() || is_customize_preview() ) {
            return;
        }

        $request_path = wp_parse_url( home_url( add_query_arg( array() ) ), PHP_URL_PATH );
        $request_path = is_string( $request_path ) ? untrailingslashit( $request_path ) : '';

        if ( '/news' !== $request_path ) {
            return;
        }

        $target = function_exists( 'lunara_home_dispatch_archive_url' )
            ? lunara_home_dispatch_archive_url()
            : home_url( '/journal/' );

        if ( ! is_string( $target ) || '' === $target ) {
            $target = home_url( '/journal/' );
        }

        if ( untrailingslashit( $target ) === untrailingslashit( home_url( '/news/' ) ) ) {
            return;
        }

        wp_safe_redirect( $target, 301, 'Lunara Legacy News' );
        exit;
    }
}
add_action( 'template_redirect', 'lunara_handle_legacy_news_redirect', 1 );

/**
 * Normalize rich text into a clean one-line archive summary.
 */
if ( ! function_exists( 'lunara_clean_archive_summary_text' ) ) {
    function lunara_clean_archive_summary_text( $content ) {
        $content = strip_shortcodes( (string) $content );
        $content = wp_strip_all_tags( $content );
        $content = preg_replace( '/\s+/', ' ', $content );

        return trim( (string) $content );
    }
}

/**
 * Pull a safe archive intro from a supporting page without leaking shortcode scaffolding.
 */
if ( ! function_exists( 'lunara_get_archive_intro_from_post' ) ) {
    function lunara_get_archive_intro_from_post( $post ) {
        if ( ! ( $post instanceof WP_Post ) ) {
            return '';
        }

        $excerpt = lunara_clean_archive_summary_text( $post->post_excerpt );
        if ( '' !== $excerpt ) {
            return $excerpt;
        }

        return lunara_clean_archive_summary_text( $post->post_content );
    }
}

/**
 * Parse comma/newline-separated post IDs or URLs into published post IDs.
 */
if ( ! function_exists( 'lunara_parse_manual_post_ids' ) ) {
    function lunara_parse_manual_post_ids( $raw_value, $allowed_post_type = '' ) {
        $allowed_post_type = sanitize_key( (string) $allowed_post_type );
        $tokens            = preg_split( '/[\r\n,]+/', (string) $raw_value );
        $post_ids          = array();

        if ( ! is_array( $tokens ) ) {
            return array();
        }

        foreach ( $tokens as $token ) {
            $token   = trim( (string) $token );
            $post_id = 0;

            if ( '' === $token ) {
                continue;
            }

            if ( preg_match( '/^\d+$/', $token ) ) {
                $post_id = intval( $token );
            } elseif ( filter_var( $token, FILTER_VALIDATE_URL ) ) {
                $post_id = url_to_postid( $token );
            }

            if ( $post_id <= 0 ) {
                continue;
            }

            $post = get_post( $post_id );
            if ( ! ( $post instanceof WP_Post ) || 'publish' !== $post->post_status ) {
                continue;
            }

            if ( '' !== $allowed_post_type && $post->post_type !== $allowed_post_type ) {
                continue;
            }

            $post_ids[] = $post_id;
        }

        return array_values( array_unique( array_map( 'intval', $post_ids ) ) );
    }
}

/**
 * Sanitize decimal Customizer values.
 */
function lunara_sanitize_decimal( $value ) {
    return is_numeric( $value ) ? (float) $value : 0;
}

/**
 * Sanitize a CSS font stack entered in the Customizer.
 */
function lunara_sanitize_font_stack( $value ) {
    $value = wp_strip_all_tags( (string) $value );
    $value = preg_replace( '/[^A-Za-z0-9,\-_"\'\s]/', '', $value );
    $value = preg_replace( '/\s+/', ' ', (string) $value );

    return trim( (string) $value );
}

/**
 * Sanitize a select/radio Customizer value against its declared choices.
 *
 * @param string               $input   The value to sanitize.
 * @param WP_Customize_Setting $setting The setting instance.
 * @return string Sanitized value or the setting default.
 */
function lunara_sanitize_select( $input, $setting ) {
    $input   = sanitize_text_field( $input );
    $choices = $setting->manager->get_control( $setting->id )->choices;

    return array_key_exists( $input, $choices ) ? $input : $setting->default;
}

/**
 * Convert a hex color into rgba() for runtime CSS output.
 */
function lunara_hex_to_rgba( $value, $opacity = 1 ) {
    $hex = sanitize_hex_color( $value );

    if ( ! $hex ) {
        return 'rgba(201,169,97,0.24)';
    }

    $hex = ltrim( $hex, '#' );

    if ( 3 === strlen( $hex ) ) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }

    $opacity = max( 0, min( 1, (float) $opacity ) );
    $red     = hexdec( substr( $hex, 0, 2 ) );
    $green   = hexdec( substr( $hex, 2, 2 ) );
    $blue    = hexdec( substr( $hex, 4, 2 ) );
    $alpha   = rtrim( rtrim( sprintf( '%.3F', $opacity ), '0' ), '.' );

    return sprintf( 'rgba(%d,%d,%d,%s)', $red, $green, $blue, $alpha );
}
