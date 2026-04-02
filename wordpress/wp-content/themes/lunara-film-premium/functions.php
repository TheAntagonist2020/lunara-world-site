<?php
/**
 * Lunara Film Child Theme Functions
 * 
 * @package Lunara_Film
 * @version 2.2.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function lunara_enqueue_styles() {
    // Parent styles (Blocksy)
    wp_enqueue_style(
        'blocksy-style',
        get_template_directory_uri() . '/style.css',
        array(),
        wp_get_theme( get_template() )->get( 'Version' )
    );
    
    wp_enqueue_style(
        'lunara-style',
        get_stylesheet_uri(),
        array( 'blocksy-style' ),
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

/**
 * Canonical homepage section slugs used for ordering and visibility.
 */
function lunara_get_home_section_slugs() {
    return array(
        'hero',
        'featured',
        'dispatch',
        'oscar-spotlight',
        'database',
        'ledger',
        'deep-cuts',
        'latest-reviews',
    );
}

/**
 * Normalize a homepage section slug so editorial controls can use friendly aliases.
 */
function lunara_normalize_home_section_slug( $slug ) {
    $slug    = sanitize_title( (string) $slug );
    $aliases = array(
        'featured-reviews'  => 'featured',
        'featured-review'   => 'featured',
        'dispatches'        => 'dispatch',
        'journal'           => 'dispatch',
        'oscar-spotlight'   => 'oscar-spotlight',
        'oscar-spotlights'  => 'oscar-spotlight',
        'spotlight'         => 'oscar-spotlight',
        'database-spotlight'=> 'database',
        'deep-cuts'         => 'deep-cuts',
        'deep-cut'          => 'deep-cuts',
        'deepcut'           => 'deep-cuts',
        'latest'            => 'latest-reviews',
        'latest-reviews'    => 'latest-reviews',
        'reviews'           => 'latest-reviews',
    );

    return isset( $aliases[ $slug ] ) ? $aliases[ $slug ] : $slug;
}

/**
 * Sanitize a comma-separated homepage section order list.
 */
function lunara_sanitize_home_section_order( $value ) {
    $recognized = lunara_get_home_section_slugs();
    $tokens     = preg_split( '/[\s,\r\n]+/', strtolower( (string) $value ) );
    $ordered    = array();

    if ( is_array( $tokens ) ) {
        foreach ( $tokens as $token ) {
            $token = lunara_normalize_home_section_slug( $token );
            if ( '' === $token || ! in_array( $token, $recognized, true ) || in_array( $token, $ordered, true ) ) {
                continue;
            }

            $ordered[] = $token;
        }
    }

    foreach ( $recognized as $slug ) {
        if ( ! in_array( $slug, $ordered, true ) ) {
            $ordered[] = $slug;
        }
    }

    return implode( ',', $ordered );
}

/**
 * Resolve homepage section order into a slug => order map.
 */
function lunara_get_home_section_order_map() {
    $defaults = lunara_get_home_section_slugs();
    $raw      = (string) get_theme_mod( 'lunara_home_section_order', implode( ',', $defaults ) );
    $ordered  = explode( ',', lunara_sanitize_home_section_order( $raw ) );
    $map      = array();

    foreach ( $ordered as $index => $slug ) {
        $map[ $slug ] = $index + 1;
    }

    return $map;
}

/**
 * Determine whether a homepage section should render.
 */
function lunara_home_section_is_enabled( $slug, $default = true ) {
    $settings = array(
        'hero'            => 'lunara_home_show_hero',
        'featured'        => 'lunara_home_show_featured',
        'dispatch'        => 'lunara_home_show_dispatch',
        'oscar-spotlight' => 'lunara_home_show_oscar_spotlight',
        'database'        => 'lunara_home_show_database',
        'ledger'          => 'lunara_home_show_ledger',
        'deep-cuts'       => 'lunara_home_show_deep_cuts',
        'latest-reviews'  => 'lunara_home_show_latest_reviews',
    );

    $slug = lunara_normalize_home_section_slug( $slug );

    if ( ! isset( $settings[ $slug ] ) ) {
        return (bool) $default;
    }

    return (bool) get_theme_mod( $settings[ $slug ], $default );
}

/**
 * Customizer options
 */
function lunara_customize_register( $wp_customize ) {
    // Lunara Header Section
    $wp_customize->add_section( 'lunara_header_options', array(
        'title'    => __( 'Lunara Header', 'lunara-film' ),
        'priority' => 30,
    ) );
    
    // Site Title Text
    $wp_customize->add_setting( 'lunara_site_title', array(
        'default'           => 'LUNARA FILM',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    
    $wp_customize->add_control( 'lunara_site_title', array(
        'label'    => __( 'Site Title Text', 'lunara-film' ),
        'section'  => 'lunara_header_options',
        'type'     => 'text',
    ) );
    
    // Show/Hide Site Title
    $wp_customize->add_setting( 'lunara_show_site_title', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    
    $wp_customize->add_control( 'lunara_show_site_title', array(
        'label'    => __( 'Show Site Title', 'lunara-film' ),
        'section'  => 'lunara_header_options',
        'type'     => 'checkbox',
    ) );
    
    // Show/Hide Logo
    $wp_customize->add_setting( 'lunara_show_logo', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ) );
    
    $wp_customize->add_control( 'lunara_show_logo', array(
        'label'    => __( 'Show Logo (set in Site Identity)', 'lunara-film' ),
        'section'  => 'lunara_header_options',
        'type'     => 'checkbox',
    ) );

    // Header Spacing (fully editable without touching CSS)
    $wp_customize->add_setting( 'lunara_header_padding_y', array(
        'default'           => 24,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'lunara_header_padding_y', array(
        'label'       => __( 'Header Vertical Padding (px)', 'lunara-film' ),
        'section'     => 'lunara_header_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 8,
            'max'  => 48,
            'step' => 1,
        ),
    ) );

    $wp_customize->add_setting( 'lunara_logo_max_height', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'lunara_logo_max_height', array(
        'label'       => __( 'Logo Max Height (px)', 'lunara-film' ),
        'section'     => 'lunara_header_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 24,
            'max'  => 110,
            'step' => 1,
        ),
    ) );

    $header_number_controls = array(
        'lunara_header_max_width' => array(
            'label'   => __( 'Header Max Width (px)', 'lunara-film' ),
            'default' => 1480,
            'min'     => 960,
            'max'     => 1800,
            'step'    => 10,
        ),
        'lunara_header_side_padding' => array(
            'label'   => __( 'Header Side Padding (px)', 'lunara-film' ),
            'default' => 48,
            'min'     => 12,
            'max'     => 96,
            'step'    => 1,
        ),
        'lunara_header_nav_gap' => array(
            'label'   => __( 'Navigation Gap (px)', 'lunara-film' ),
            'default' => 26,
            'min'     => 8,
            'max'     => 56,
            'step'    => 1,
        ),
        'lunara_header_nav_size' => array(
            'label'   => __( 'Navigation Font Size (px)', 'lunara-film' ),
            'default' => 14,
            'min'     => 11,
            'max'     => 22,
            'step'    => 1,
        ),
        'lunara_header_title_size' => array(
            'label'   => __( 'Site Title Size (px)', 'lunara-film' ),
            'default' => 19,
            'min'     => 12,
            'max'     => 34,
            'step'    => 1,
        ),
    );

    foreach ( $header_number_controls as $setting => $control ) {
        $wp_customize->add_setting( $setting, array(
            'default'           => $control['default'],
            'sanitize_callback' => 'absint',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( $setting, array(
            'label'       => $control['label'],
            'section'     => 'lunara_header_options',
            'type'        => 'number',
            'input_attrs' => array(
                'min'  => $control['min'],
                'max'  => $control['max'],
                'step' => $control['step'],
            ),
        ) );
    }

    $wp_customize->add_setting( 'lunara_header_nav_tracking', array(
        'default'           => 0.12,
        'sanitize_callback' => 'lunara_sanitize_decimal',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'lunara_header_nav_tracking', array(
        'label'       => __( 'Navigation Letter Spacing (em)', 'lunara-film' ),
        'section'     => 'lunara_header_options',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 0,
            'max'  => 0.4,
            'step' => 0.01,
        ),
    ) );

    if ( class_exists( 'WP_Customize_Color_Control' ) ) {
        $header_color_controls = array(
            'lunara_header_background' => array(
                'label'   => __( 'Header Background', 'lunara-film' ),
                'default' => '#0a1520',
            ),
            'lunara_header_border_color' => array(
                'label'   => __( 'Header Border', 'lunara-film' ),
                'default' => '#223142',
            ),
            'lunara_header_link_color' => array(
                'label'   => __( 'Header Link Color', 'lunara-film' ),
                'default' => '#d4d4d4',
            ),
            'lunara_header_link_hover_color' => array(
                'label'   => __( 'Header Link Hover Color', 'lunara-film' ),
                'default' => '#e0c481',
            ),
        );

        foreach ( $header_color_controls as $setting => $control ) {
            $wp_customize->add_setting( $setting, array(
                'default'           => $control['default'],
                'sanitize_callback' => 'sanitize_hex_color',
                'transport'         => 'refresh',
            ) );

            $wp_customize->add_control(
                new WP_Customize_Color_Control(
                    $wp_customize,
                    $setting,
                    array(
                        'label'   => $control['label'],
                        'section' => 'lunara_header_options',
                    )
                )
            );
        }
    }

    $wp_customize->add_section(
        'lunara_global_design_options',
        array(
            'title'       => __( 'Lunara Global Design', 'lunara-film' ),
            'priority'    => 31,
            'description' => __( 'Control the sitewide palette, typography, shell width, and card styling without editing CSS.', 'lunara-film' ),
        )
    );

    $global_design_controls = array(
        array(
            'setting'  => 'lunara_shell_content_width',
            'default'  => 1360,
            'label'    => __( 'Content Width (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 960, 'max' => 1800, 'step' => 10 ),
        ),
        array(
            'setting'  => 'lunara_shell_side_padding',
            'default'  => 28,
            'label'    => __( 'Content Side Padding (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 12, 'max' => 96, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_surface_radius',
            'default'  => 28,
            'label'    => __( 'Card Corner Radius (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 0, 'max' => 48, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_body_font_size',
            'default'  => 17,
            'label'    => __( 'Body Font Size (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 14, 'max' => 24, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_body_line_height',
            'default'  => 1.7,
            'label'    => __( 'Body Line Height', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'lunara_sanitize_decimal',
            'attrs'    => array( 'min' => 1.3, 'max' => 2.2, 'step' => 0.05 ),
        ),
        array(
            'setting'  => 'lunara_section_title_size',
            'default'  => 34,
            'label'    => __( 'Section Heading Size (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 24, 'max' => 64, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_hero_title_size',
            'default'  => 72,
            'label'    => __( 'Hero Title Size (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 40, 'max' => 120, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_hero_copy_size',
            'default'  => 19,
            'label'    => __( 'Hero Copy Size (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 15, 'max' => 30, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_kicker_size',
            'default'  => 12,
            'label'    => __( 'Kicker Size (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 10, 'max' => 18, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_kicker_tracking',
            'default'  => 0.16,
            'label'    => __( 'Kicker Letter Spacing (em)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'lunara_sanitize_decimal',
            'attrs'    => array( 'min' => 0.02, 'max' => 0.4, 'step' => 0.01 ),
        ),
        array(
            'setting'     => 'lunara_heading_font_family',
            'default'     => '',
            'label'       => __( 'Heading Font Stack', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'lunara_sanitize_font_stack',
            'description' => __( 'Enter a loaded font stack or leave blank to keep the current theme font.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_body_font_family',
            'default'     => '',
            'label'       => __( 'Body Font Stack', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'lunara_sanitize_font_stack',
            'description' => __( 'Enter a loaded font stack or leave blank to keep the current theme font.', 'lunara-film' ),
        ),
    );

    foreach ( $global_design_controls as $control ) {
        $wp_customize->add_setting(
            $control['setting'],
            array(
                'default'           => $control['default'],
                'sanitize_callback' => $control['sanitize'],
                'transport'         => 'refresh',
            )
        );

        $args = array(
            'label'       => $control['label'],
            'section'     => 'lunara_global_design_options',
            'type'        => $control['type'],
            'description' => ! empty( $control['description'] ) ? $control['description'] : '',
        );

        if ( ! empty( $control['attrs'] ) ) {
            $args['input_attrs'] = $control['attrs'];
        }

        $wp_customize->add_control( $control['setting'], $args );
    }

    if ( class_exists( 'WP_Customize_Color_Control' ) ) {
        $global_color_controls = array(
            'lunara_bg_primary' => array(
                'label'   => __( 'Primary Background', 'lunara-film' ),
                'default' => '#0a1520',
            ),
            'lunara_bg_secondary' => array(
                'label'   => __( 'Secondary Background', 'lunara-film' ),
                'default' => '#0f1d2e',
            ),
            'lunara_bg_card' => array(
                'label'   => __( 'Card Background', 'lunara-film' ),
                'default' => '#1a2938',
            ),
            'lunara_accent_color' => array(
                'label'   => __( 'Accent Color', 'lunara-film' ),
                'default' => '#c9a961',
            ),
            'lunara_accent_soft_color' => array(
                'label'   => __( 'Accent Highlight', 'lunara-film' ),
                'default' => '#e0c481',
            ),
            'lunara_text_color' => array(
                'label'   => __( 'Body Text Color', 'lunara-film' ),
                'default' => '#d4d4d4',
            ),
            'lunara_muted_text_color' => array(
                'label'   => __( 'Muted Text Color', 'lunara-film' ),
                'default' => '#888888',
            ),
            'lunara_border_color' => array(
                'label'   => __( 'Border Color', 'lunara-film' ),
                'default' => '#c9a961',
            ),
        );

        foreach ( $global_color_controls as $setting => $control ) {
            $wp_customize->add_setting(
                $setting,
                array(
                    'default'           => $control['default'],
                    'sanitize_callback' => 'sanitize_hex_color',
                    'transport'         => 'refresh',
                )
            );

            $wp_customize->add_control(
                new WP_Customize_Color_Control(
                    $wp_customize,
                    $setting,
                    array(
                        'label'   => $control['label'],
                        'section' => 'lunara_global_design_options',
                    )
                )
            );
        }
    }

    // Lunara Debrief Section (signature controls)
    $wp_customize->add_section( 'lunara_debrief_options', array(
        'title'    => __( 'Lunara Debrief', 'lunara-film' ),
        'priority' => 32,
    ) );

        $wp_customize->add_setting( 'lunara_debrief_kicker_text', array(
        'default'           => 'A LUNARA FILM SIGNATURE',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );

    $wp_customize->add_control( 'lunara_debrief_kicker_text', array(
        'label'   => __( 'Kicker Text', 'lunara-film' ),
        'section' => 'lunara_debrief_options',
        'type'    => 'text',
    ) );

    $wp_customize->add_section( 'lunara_homepage_pulse_options', array(
        'title'    => __( 'Lunara Homepage Pulse', 'lunara-film' ),
        'priority' => 33,
    ) );

    $homepage_controls = array(
        array(
            'setting'  => 'lunara_home_hero_kicker',
            'default'  => 'LUNARA FILM',
            'label'    => __( 'Hero Kicker', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_home_hero_title',
            'default'  => get_bloginfo( 'name' ),
            'label'    => __( 'Hero Title', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_home_hero_copy',
            'default'  => 'Film criticism and a living Oscar ledger for readers who want cinema, and the record around it, taken seriously.',
            'label'    => __( 'Hero Sentence', 'lunara-film' ),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
        array(
            'setting'  => 'lunara_home_primary_cta_label',
            'default'  => 'Browse Reviews',
            'label'    => __( 'Primary Button Label', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_home_primary_cta_url',
            'default'  => home_url( '/reviews/' ),
            'label'    => __( 'Primary Button URL', 'lunara-film' ),
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        array(
            'setting'  => 'lunara_home_secondary_cta_label',
            'default'  => 'Explore the Oscar Ledger',
            'label'    => __( 'Secondary Button Label', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_home_secondary_cta_url',
            'default'  => home_url( '/oscars/' ),
            'label'    => __( 'Secondary Button URL', 'lunara-film' ),
            'type'     => 'url',
            'sanitize' => 'esc_url_raw',
        ),
        array(
            'setting'  => 'lunara_home_database_heading',
            'default'  => 'The Lunara Oscar Ledger',
            'label'    => __( 'Oscar Spotlight Heading', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_home_database_copy',
            'default'  => 'This is not just a review blog. The Lunara Oscar Ledger is a research-driven archive of Academy Awards history, structured so readers can move from iconic films to categories, people, companies, and ceremony context without getting lost in a dead wall of data.',
            'label'    => __( 'Oscar Spotlight Copy', 'lunara-film' ),
            'type'     => 'textarea',
            'sanitize' => 'sanitize_textarea_field',
        ),
    );

    foreach ( $homepage_controls as $control ) {
        $wp_customize->add_setting(
            $control['setting'],
            array(
                'default'           => $control['default'],
                'sanitize_callback' => $control['sanitize'],
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            $control['setting'],
            array(
                'label'       => $control['label'],
                'section'     => 'lunara_homepage_pulse_options',
                'type'        => $control['type'],
                'description' => ! empty( $control['description'] ) ? $control['description'] : '',
            )
        );
    }

    for ( $index = 1; $index <= 3; $index++ ) {
        $fields = array(
            'kicker' => array(
                'label'       => sprintf( __( 'Pulse Card %d Kicker', 'lunara-film' ), $index ),
                'type'        => 'text',
                'sanitize'    => 'sanitize_text_field',
                'description' => __( 'Leave blank to use the live database-driven card.', 'lunara-film' ),
            ),
            'title' => array(
                'label'       => sprintf( __( 'Pulse Card %d Title', 'lunara-film' ), $index ),
                'type'        => 'text',
                'sanitize'    => 'sanitize_text_field',
                'description' => __( 'Leave blank to use the live database-driven card.', 'lunara-film' ),
            ),
            'copy' => array(
                'label'       => sprintf( __( 'Pulse Card %d Copy', 'lunara-film' ), $index ),
                'type'        => 'textarea',
                'sanitize'    => 'sanitize_textarea_field',
                'description' => __( 'Use this when you want to foreground a first, milestone, or sharper editorial angle.', 'lunara-film' ),
            ),
            'link_label' => array(
                'label'       => sprintf( __( 'Pulse Card %d Button Label', 'lunara-film' ), $index ),
                'type'        => 'text',
                'sanitize'    => 'sanitize_text_field',
                'description' => __( 'Optional override for the card button label.', 'lunara-film' ),
            ),
            'link_url' => array(
                'label'       => sprintf( __( 'Pulse Card %d Button URL', 'lunara-film' ), $index ),
                'type'        => 'url',
                'sanitize'    => 'esc_url_raw',
                'description' => __( 'Optional override for where the card should go.', 'lunara-film' ),
            ),
        );

        foreach ( $fields as $suffix => $field ) {
            $setting = sprintf( 'lunara_home_pulse_card_%d_%s', $index, $suffix );

            $wp_customize->add_setting(
                $setting,
                array(
                    'default'           => '',
                    'sanitize_callback' => $field['sanitize'],
                    'transport'         => 'refresh',
                )
            );

            $wp_customize->add_control(
                $setting,
                array(
                    'label'       => $field['label'],
                    'section'     => 'lunara_homepage_pulse_options',
                    'type'        => $field['type'],
                    'description' => $field['description'],
                )
            );
        }
    }

    $wp_customize->add_section(
        'lunara_homepage_layout_options',
        array(
            'title'       => __( 'Lunara Homepage Layout', 'lunara-film' ),
            'priority'    => 34,
            'description' => __( 'Control the homepage width, spacing, section visibility, and section order without editing templates.', 'lunara-film' ),
        )
    );

    $homepage_layout_controls = array(
        array(
            'setting'  => 'lunara_home_max_width',
            'default'  => 1400,
            'label'    => __( 'Homepage Max Width (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 960, 'max' => 1800, 'step' => 10 ),
        ),
        array(
            'setting'  => 'lunara_home_side_padding',
            'default'  => 40,
            'label'    => __( 'Homepage Side Padding (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 12, 'max' => 96, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_home_section_gap',
            'default'  => 72,
            'label'    => __( 'Homepage Section Gap (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 24, 'max' => 140, 'step' => 1 ),
        ),
        array(
            'setting'  => 'lunara_home_hero_top_padding',
            'default'  => 92,
            'label'    => __( 'Hero Top Padding (px)', 'lunara-film' ),
            'type'     => 'number',
            'sanitize' => 'absint',
            'attrs'    => array( 'min' => 24, 'max' => 180, 'step' => 1 ),
        ),
        array(
            'setting'     => 'lunara_home_section_order',
            'default'     => implode( ',', lunara_get_home_section_slugs() ),
            'label'       => __( 'Section Order', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'lunara_sanitize_home_section_order',
            'description' => __( 'Comma-separated slugs: hero, featured, dispatch, oscar-spotlight, database, ledger, deep-cuts, latest-reviews.', 'lunara-film' ),
        ),
    );

    foreach ( $homepage_layout_controls as $control ) {
        $wp_customize->add_setting(
            $control['setting'],
            array(
                'default'           => $control['default'],
                'sanitize_callback' => $control['sanitize'],
                'transport'         => 'refresh',
            )
        );

        $args = array(
            'label'       => $control['label'],
            'section'     => 'lunara_homepage_layout_options',
            'type'        => $control['type'],
            'description' => ! empty( $control['description'] ) ? $control['description'] : '',
        );

        if ( ! empty( $control['attrs'] ) ) {
            $args['input_attrs'] = $control['attrs'];
        }

        $wp_customize->add_control( $control['setting'], $args );
    }

    $homepage_toggle_controls = array(
        'lunara_home_show_hero'            => __( 'Show Hero', 'lunara-film' ),
        'lunara_home_show_featured'        => __( 'Show Featured Reviews', 'lunara-film' ),
        'lunara_home_show_dispatch'        => __( 'Show Dispatches & Audio', 'lunara-film' ),
        'lunara_home_show_oscar_spotlight' => __( 'Show Oscar Spotlight', 'lunara-film' ),
        'lunara_home_show_database'        => __( 'Show Database Spotlight', 'lunara-film' ),
        'lunara_home_show_ledger'          => __( 'Show From the Ledger', 'lunara-film' ),
        'lunara_home_show_deep_cuts'       => __( 'Show Deep Cut Stats', 'lunara-film' ),
        'lunara_home_show_latest_reviews'  => __( 'Show Latest Reviews', 'lunara-film' ),
    );

    foreach ( $homepage_toggle_controls as $setting => $label ) {
        $wp_customize->add_setting( $setting, array(
            'default'           => true,
            'sanitize_callback' => 'wp_validate_boolean',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( $setting, array(
            'label'   => $label,
            'section' => 'lunara_homepage_layout_options',
            'type'    => 'checkbox',
        ) );
    }

    $wp_customize->add_section(
        'lunara_homepage_editorial_options',
        array(
            'title'    => __( 'Lunara Homepage Curation', 'lunara-film' ),
            'priority' => 35,
        )
    );

    $homepage_editorial_controls = array(
        array(
            'setting'     => 'lunara_home_featured_reviews_kicker',
            'default'     => 'Featured Reviews',
            'label'       => __( 'Featured Reviews Kicker', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Controls the small label above the homepage review carousel.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_featured_reviews_heading',
            'default'     => 'Poster-Driven Criticism',
            'label'       => __( 'Featured Reviews Heading', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Use this to rename the homepage review showcase without editing templates.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_featured_review_ids',
            'default'     => '',
            'label'       => __( 'Featured Review IDs or URLs', 'lunara-film' ),
            'type'        => 'textarea',
            'sanitize'    => 'sanitize_textarea_field',
            'description' => __( 'Paste published review IDs or full URLs, one per line. Leave blank to fall back to the featured tag.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_ledger_kicker',
            'default'     => 'From the Ledger',
            'label'       => __( 'Deep Cuts Kicker', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Controls the label above the Oscar history carousel.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_ledger_heading',
            'default'     => 'Oscar Ledger Highlights',
            'label'       => __( 'Deep Cuts Heading', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Rename the historical Oscar spotlight lane from the Customizer.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_dispatch_kicker',
            'default'     => 'Dispatches & Audio',
            'label'       => __( 'Dispatches Kicker', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Use this for the mixed editorial lane for news, reactions, essays, and podcast posts.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_dispatch_heading',
            'default'     => 'News, Reactions, and the Lunara Journal',
            'label'       => __( 'Dispatches Heading', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'This is the homepage headline for your non-review editorial stream.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_dispatch_copy',
            'default'     => 'Use this lane for reported news, quick reactions, larger think pieces, and podcast episodes without flattening everything into review coverage.',
            'label'       => __( 'Dispatches Intro Copy', 'lunara-film' ),
            'type'        => 'textarea',
            'sanitize'    => 'sanitize_textarea_field',
            'description' => __( 'A short statement that tells readers what kinds of writing and audio live here.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_dispatch_category_slugs',
            'default'     => 'news,think-pieces,reactions,podcast',
            'label'       => __( 'Dispatch Category Slugs', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Comma-separated category slugs to pull into the homepage dispatches lane.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_dispatch_post_ids',
            'default'     => '',
            'label'       => __( 'Dispatch Post IDs or URLs', 'lunara-film' ),
            'type'        => 'textarea',
            'sanitize'    => 'sanitize_textarea_field',
            'description' => __( 'Paste published post IDs or URLs, one per line, when you want full manual control over the dispatches lane.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_dispatch_button_label',
            'default'     => 'Open the Journal',
            'label'       => __( 'Dispatches Button Label', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'The call-to-action beside the dispatches section heading.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_dispatch_button_url',
            'default'     => '',
            'label'       => __( 'Dispatches Button URL', 'lunara-film' ),
            'type'        => 'url',
            'sanitize'    => 'esc_url_raw',
            'description' => __( 'Optional override. Leave blank to link to the first matching category archive or the blog index.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_latest_reviews_heading',
            'default'     => 'New Writing',
            'label'       => __( 'Latest Reviews Heading', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Rename the lower review grid without touching the template.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_home_latest_reviews_button_label',
            'default'     => 'View All',
            'label'       => __( 'Latest Reviews Button Label', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Controls the archive button for the latest reviews section.', 'lunara-film' ),
        ),
    );

    foreach ( $homepage_editorial_controls as $control ) {
        $wp_customize->add_setting(
            $control['setting'],
            array(
                'default'           => $control['default'],
                'sanitize_callback' => $control['sanitize'],
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            $control['setting'],
            array(
                'label'       => $control['label'],
                'section'     => 'lunara_homepage_editorial_options',
                'type'        => $control['type'],
                'description' => $control['description'],
            )
        );
    }

    $wp_customize->add_section(
        'lunara_editorial_archive_options',
        array(
            'title'       => __( 'Lunara Editorial Archives', 'lunara-film' ),
            'priority'    => 36,
            'description' => __( 'Shape the review archive and the journal/news archive without editing templates.', 'lunara-film' ),
        )
    );

    $editorial_archive_controls = array(
        array(
            'setting'  => 'lunara_reviews_archive_kicker',
            'default'  => 'Review Archive',
            'label'    => __( 'Reviews Archive Kicker', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_reviews_archive_title',
            'default'  => 'The Review Archive',
            'label'    => __( 'Reviews Archive Title', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'     => 'lunara_reviews_archive_copy',
            'default'     => 'Poster-led criticism, cataloged so readers can move through the writing as an evolving record instead of a pile of disconnected posts.',
            'label'       => __( 'Reviews Archive Intro', 'lunara-film' ),
            'type'        => 'textarea',
            'sanitize'    => 'sanitize_textarea_field',
            'description' => __( 'This appears on the main reviews archive page.', 'lunara-film' ),
        ),
        array(
            'setting'  => 'lunara_journal_archive_kicker',
            'default'  => 'The Journal',
            'label'    => __( 'Journal Archive Kicker', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_journal_archive_title',
            'default'  => 'News, Reactions, Essays, and Audio',
            'label'    => __( 'Journal Archive Title', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'     => 'lunara_journal_archive_copy',
            'default'     => 'This is the live editorial lane for news, quick reactions, longer think pieces, interviews, and podcast writing that should stand beside the reviews without being mistaken for them.',
            'label'       => __( 'Journal Archive Intro', 'lunara-film' ),
            'type'        => 'textarea',
            'sanitize'    => 'sanitize_textarea_field',
            'description' => __( 'This appears on the blog/posts archive when no posts page intro is available.', 'lunara-film' ),
        ),
    );

    foreach ( $editorial_archive_controls as $control ) {
        $wp_customize->add_setting(
            $control['setting'],
            array(
                'default'           => $control['default'],
                'sanitize_callback' => $control['sanitize'],
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            $control['setting'],
            array(
                'label'       => $control['label'],
                'section'     => 'lunara_editorial_archive_options',
                'type'        => $control['type'],
                'description' => ! empty( $control['description'] ) ? $control['description'] : '',
            )
        );
    }

    $wp_customize->add_section(
        'lunara_oscars_portal_options',
        array(
            'title'       => __( 'Lunara Oscars Portal', 'lunara-film' ),
            'priority'    => 37,
            'description' => __( 'Control the dedicated /oscars/ landing page without editing templates.', 'lunara-film' ),
        )
    );

    $oscars_portal_controls = array(
        array(
            'setting'     => 'lunara_oscars_portal_kicker',
            'default'     => 'The Lunara Oscar Ledger',
            'label'       => __( 'Portal Kicker', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Small label above the Oscars portal headline.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_oscars_portal_title',
            'default'     => 'Academy Awards history, treated like a living editorial system.',
            'label'       => __( 'Portal Title', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Main headline for the /oscars/ front door.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_oscars_portal_copy',
            'default'     => 'Move from a winning film to the people behind it, from one category to the ceremony around it, and from the ledger straight into Lunara criticism without ever hitting a dead wall of data.',
            'label'       => __( 'Portal Intro Copy', 'lunara-film' ),
            'type'        => 'textarea',
            'sanitize'    => 'sanitize_textarea_field',
            'description' => __( 'Intro paragraph for the Oscars portal hero.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_oscars_portal_explore_heading',
            'default'     => 'Start anywhere in the ledger.',
            'label'       => __( 'Explore Section Heading', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Headline for the section that links deeper into ceremonies, categories, and the database.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_oscars_portal_reviews_heading',
            'default'     => 'Reviews Inside the Ledger',
            'label'       => __( 'Linked Reviews Heading', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Headline above the latest reviews connected to Oscar film pages.', 'lunara-film' ),
        ),
        array(
            'setting'     => 'lunara_oscars_portal_deep_cuts_heading',
            'default'     => 'Oscar Deep Cuts',
            'label'       => __( 'Deep Cuts Heading', 'lunara-film' ),
            'type'        => 'text',
            'sanitize'    => 'sanitize_text_field',
            'description' => __( 'Headline above the rotating stats and historical facts grid.', 'lunara-film' ),
        ),
    );

    foreach ( $oscars_portal_controls as $control ) {
        $wp_customize->add_setting(
            $control['setting'],
            array(
                'default'           => $control['default'],
                'sanitize_callback' => $control['sanitize'],
                'transport'         => 'refresh',
            )
        );

        $wp_customize->add_control(
            $control['setting'],
            array(
                'label'       => $control['label'],
                'section'     => 'lunara_oscars_portal_options',
                'type'        => $control['type'],
                'description' => ! empty( $control['description'] ) ? $control['description'] : '',
            )
        );
    }

    // Ledger Stories IMDb ID overrides (Feature 3).
    $wp_customize->add_section( 'lunara_homepage_ledger_options', array(
        'title'       => __( 'Lunara Ledger Stories', 'lunara-film' ),
        'priority'    => 38,
        'description' => __( 'Override the "From the Ledger" homepage cards with specific IMDb title IDs. Leave blank to use automatic rotation from the database.', 'lunara-film' ),
    ) );

    for ( $slot = 1; $slot <= 4; $slot++ ) {
        $setting_key = 'lunara_home_ledger_card_' . $slot . '_imdb_id';

        $wp_customize->add_setting( $setting_key, array(
            'default'           => '',
            'sanitize_callback' => 'sanitize_text_field',
            'transport'         => 'refresh',
        ) );

        $wp_customize->add_control( $setting_key, array(
            'label'       => sprintf( __( 'Ledger Card %d IMDb ID', 'lunara-film' ), $slot ),
            'section'     => 'lunara_homepage_ledger_options',
            'type'        => 'text',
            'description' => __( 'e.g. tt0111161. Leave blank for automatic rotation.', 'lunara-film' ),
        ) );
    }

    // ── Footer Design Controls ──
    $wp_customize->add_section( 'lunara_footer_options', array(
        'title'    => __( 'Lunara Footer', 'lunara-film' ),
        'priority' => 34,
    ) );

    $footer_controls = array(
        array(
            'setting'  => 'lunara_footer_tagline',
            'default'  => 'Film criticism and a living Oscar ledger.',
            'label'    => __( 'Footer Tagline', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_footer_show_logo',
            'default'  => true,
            'label'    => __( 'Show Logo in Footer', 'lunara-film' ),
            'type'     => 'checkbox',
            'sanitize' => 'absint',
        ),
        array(
            'setting'  => 'lunara_footer_col1_heading',
            'default'  => 'Editorial',
            'label'    => __( 'Column 1 Heading', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_footer_col2_heading',
            'default'  => 'Oscar Ledger',
            'label'    => __( 'Column 2 Heading', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_footer_col3_heading',
            'default'  => 'Utility',
            'label'    => __( 'Column 3 Heading', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
        array(
            'setting'  => 'lunara_footer_copyright',
            'default'  => 'Lunara Film',
            'label'    => __( 'Copyright Name', 'lunara-film' ),
            'type'     => 'text',
            'sanitize' => 'sanitize_text_field',
        ),
    );

    foreach ( $footer_controls as $fc ) {
        $wp_customize->add_setting( $fc['setting'], array(
            'default'           => $fc['default'],
            'sanitize_callback' => $fc['sanitize'],
            'transport'         => 'refresh',
        ) );
        $args = array(
            'label'   => $fc['label'],
            'section' => 'lunara_footer_options',
            'type'    => $fc['type'],
        );
        $wp_customize->add_control( $fc['setting'], $args );
    }
}
add_action( 'customize_register', 'lunara_customize_register' );

/**
 * Print runtime CSS for Lunara design controls.
 */
function lunara_output_runtime_customizer_css() {
    $header_padding_y = max( 8, min( 48, absint( get_theme_mod( 'lunara_header_padding_y', 24 ) ) ) );
    $logo_max_height  = max( 24, min( 110, absint( get_theme_mod( 'lunara_logo_max_height', 56 ) ) ) );
    $header_max_width = max( 960, min( 1800, absint( get_theme_mod( 'lunara_header_max_width', 1480 ) ) ) );
    $header_side_pad  = max( 12, min( 96, absint( get_theme_mod( 'lunara_header_side_padding', 48 ) ) ) );
    $nav_gap          = max( 8, min( 56, absint( get_theme_mod( 'lunara_header_nav_gap', 26 ) ) ) );
    $nav_size         = max( 11, min( 22, absint( get_theme_mod( 'lunara_header_nav_size', 14 ) ) ) );
    $title_size       = max( 12, min( 34, absint( get_theme_mod( 'lunara_header_title_size', 19 ) ) ) );
    $nav_tracking     = max( 0, min( 0.4, (float) get_theme_mod( 'lunara_header_nav_tracking', 0.12 ) ) );
    $header_bg        = sanitize_hex_color( get_theme_mod( 'lunara_header_background', '#0a1520' ) ) ?: '#0a1520';
    $header_border    = sanitize_hex_color( get_theme_mod( 'lunara_header_border_color', '#223142' ) ) ?: '#223142';
    $header_link      = sanitize_hex_color( get_theme_mod( 'lunara_header_link_color', '#d4d4d4' ) ) ?: '#d4d4d4';
    $header_hover     = sanitize_hex_color( get_theme_mod( 'lunara_header_link_hover_color', '#e0c481' ) ) ?: '#e0c481';
    $bg_primary       = sanitize_hex_color( get_theme_mod( 'lunara_bg_primary', '#0a1520' ) ) ?: '#0a1520';
    $bg_secondary     = sanitize_hex_color( get_theme_mod( 'lunara_bg_secondary', '#0f1d2e' ) ) ?: '#0f1d2e';
    $bg_card          = sanitize_hex_color( get_theme_mod( 'lunara_bg_card', '#1a2938' ) ) ?: '#1a2938';
    $accent           = sanitize_hex_color( get_theme_mod( 'lunara_accent_color', '#c9a961' ) ) ?: '#c9a961';
    $accent_soft      = sanitize_hex_color( get_theme_mod( 'lunara_accent_soft_color', '#e0c481' ) ) ?: '#e0c481';
    $text_color       = sanitize_hex_color( get_theme_mod( 'lunara_text_color', '#d4d4d4' ) ) ?: '#d4d4d4';
    $muted_text       = sanitize_hex_color( get_theme_mod( 'lunara_muted_text_color', '#888888' ) ) ?: '#888888';
    $border_color     = sanitize_hex_color( get_theme_mod( 'lunara_border_color', '#c9a961' ) ) ?: '#c9a961';
    $shell_max        = max( 960, min( 1800, absint( get_theme_mod( 'lunara_shell_content_width', 1360 ) ) ) );
    $shell_pad        = max( 12, min( 96, absint( get_theme_mod( 'lunara_shell_side_padding', 28 ) ) ) );
    $surface_radius   = max( 0, min( 48, absint( get_theme_mod( 'lunara_surface_radius', 28 ) ) ) );
    $body_size        = max( 14, min( 24, absint( get_theme_mod( 'lunara_body_font_size', 17 ) ) ) );
    $body_line_height = max( 1.3, min( 2.2, (float) get_theme_mod( 'lunara_body_line_height', 1.7 ) ) );
    $section_title    = max( 24, min( 64, absint( get_theme_mod( 'lunara_section_title_size', 34 ) ) ) );
    $hero_title       = max( 40, min( 120, absint( get_theme_mod( 'lunara_hero_title_size', 72 ) ) ) );
    $hero_copy        = max( 15, min( 30, absint( get_theme_mod( 'lunara_hero_copy_size', 19 ) ) ) );
    $kicker_size      = max( 10, min( 18, absint( get_theme_mod( 'lunara_kicker_size', 12 ) ) ) );
    $kicker_track     = max( 0.02, min( 0.4, (float) get_theme_mod( 'lunara_kicker_tracking', 0.16 ) ) );
    $heading_font     = lunara_sanitize_font_stack( get_theme_mod( 'lunara_heading_font_family', '' ) );
    $body_font        = lunara_sanitize_font_stack( get_theme_mod( 'lunara_body_font_family', '' ) );
    $border_alpha     = lunara_hex_to_rgba( $border_color, 0.24 );

    $home_max_width   = max( 960, min( 1800, absint( get_theme_mod( 'lunara_home_max_width', 1400 ) ) ) );
    $home_side_pad    = max( 12, min( 96, absint( get_theme_mod( 'lunara_home_side_padding', 40 ) ) ) );
    $home_gap         = max( 24, min( 140, absint( get_theme_mod( 'lunara_home_section_gap', 72 ) ) ) );
    $hero_top_padding = max( 24, min( 180, absint( get_theme_mod( 'lunara_home_hero_top_padding', 92 ) ) ) );
    $mobile_header_pad = min( $header_side_pad, 20 );
    $mobile_home_pad   = min( $home_side_pad, 24 );
    $mobile_shell_pad  = min( $shell_pad, 24 );
    $section_order     = lunara_get_home_section_order_map();
    $css               = '';

    $css .= ':root{';
    $css .= '--lunara-bg-primary:' . $bg_primary . ';';
    $css .= '--lunara-bg-secondary:' . $bg_secondary . ';';
    $css .= '--lunara-bg-card:' . $bg_card . ';';
    $css .= '--lunara-gold:' . $accent . ';';
    $css .= '--lunara-gold-light:' . $accent_soft . ';';
    $css .= '--lunara-text:' . $text_color . ';';
    $css .= '--lunara-text-muted:' . $muted_text . ';';
    $css .= '--lunara-border:' . $border_alpha . ';';
    $css .= '--lunara-border-solid:' . $border_color . ';';
    $css .= '--lunara-shell-max:' . $shell_max . 'px;';
    $css .= '--lunara-shell-pad:' . $shell_pad . 'px;';
    $css .= '--lunara-surface-radius:' . $surface_radius . 'px;';
    $css .= '--lunara-body-size:' . $body_size . 'px;';
    $css .= '--lunara-body-line-height:' . $body_line_height . ';';
    $css .= '--lunara-section-title-size:' . $section_title . 'px;';
    $css .= '--lunara-hero-title-size:' . $hero_title . 'px;';
    $css .= '--lunara-hero-copy-size:' . $hero_copy . 'px;';
    $css .= '--lunara-kicker-size:' . $kicker_size . 'px;';
    $css .= '--lunara-kicker-track:' . $kicker_track . 'em;';
    $css .= '--lunara-header-pad:' . $header_padding_y . 'px;';
    $css .= '--lunara-logo-max:' . $logo_max_height . 'px;';
    $css .= '--lunara-header-max:' . $header_max_width . 'px;';
    $css .= '--lunara-header-side-pad:' . $header_side_pad . 'px;';
    $css .= '--lunara-header-nav-gap:' . $nav_gap . 'px;';
    $css .= '--lunara-header-nav-size:' . $nav_size . 'px;';
    $css .= '--lunara-header-title-size:' . $title_size . 'px;';
    $css .= '--lunara-header-nav-track:' . $nav_tracking . 'em;';
    $css .= '--lunara-home-max:' . $home_max_width . 'px;';
    $css .= '--lunara-home-pad:' . $home_side_pad . 'px;';
    $css .= '--lunara-home-gap:' . $home_gap . 'px;';
    $css .= '--lunara-home-hero-top:' . $hero_top_padding . 'px;';
    $css .= '--lunara-section-gap:' . $home_gap . 'px;';
    $css .= '}';

    $css .= 'body{background-color:var(--lunara-bg-primary)!important;color:var(--lunara-text)!important;font-size:var(--lunara-body-size);line-height:var(--lunara-body-line-height);}';
    $css .= '.lunara-section,.lunara-archive-page > .lunara-home-section,.lunara-editorial-single-page > .lunara-home-section,.lunara-oscars-portal > .lunara-home-section{max-width:var(--lunara-shell-max);margin-left:auto;margin-right:auto;padding-left:var(--lunara-shell-pad);padding-right:var(--lunara-shell-pad);}';
    $css .= '.lunara-home-section-title,.lunara-section-title,.lunara-home-pulse-title,.lunara-home-pulse-feature-heading,.lunara-poster-card-title,.lunara-dispatch-lead-title,.lunara-dispatch-rail-title,.lunara-home-winner-title,.lunara-home-pulse-note-title,.lunara-review-grid-title,.lunara-oscar-spotlight-text-panel h3{font-size:var(--lunara-section-title-size);}';
    $css .= '.lunara-home-hero-title{font-size:var(--lunara-hero-title-size);}';
    $css .= '.lunara-home-hero-copy{font-size:var(--lunara-hero-copy-size);line-height:var(--lunara-body-line-height);}';
    $css .= '.lunara-home-hero-kicker,.lunara-home-section-kicker,.lunara-poster-card-kicker,.lunara-home-pulse-kicker,.lunara-dispatch-type,.lunara-home-pulse-note-kicker{font-size:var(--lunara-kicker-size);letter-spacing:var(--lunara-kicker-track);}';
    $css .= '.lunara-home-section-summary,.lunara-poster-card-excerpt,.lunara-dispatch-lead-excerpt,.lunara-dispatch-rail-excerpt,.lunara-home-pulse-summary,.lunara-home-pulse-feature-copy,.lunara-home-pulse-note-copy,.lunara-oscar-spotlight-copy,.lunara-review-grid-meta,.lunara-poster-card-meta{font-size:var(--lunara-body-size);line-height:var(--lunara-body-line-height);}';
    $css .= '.lunara-home-pulse-card,.lunara-poster-card,.lunara-dispatch-lead,.lunara-dispatch-rail-card,.lunara-home-winner-card,.lunara-home-pulse-note,.lunara-review-grid-card,.lunara-oscar-spotlight-pill,.lunara-home-pulse-feature-card{border-color:var(--lunara-border);border-radius:var(--lunara-surface-radius);}';
    $css .= '.lunara-dispatch-lead,.lunara-dispatch-rail-card,.lunara-home-winner-card,.lunara-home-pulse-note,.lunara-review-grid-card,.lunara-home-pulse-feature-card{background:' . lunara_hex_to_rgba( $bg_card, 0.88 ) . ';}';
    $css .= '.lunara-home-pulse-card{background:linear-gradient(180deg,' . lunara_hex_to_rgba( $bg_card, 0.96 ) . ',' . lunara_hex_to_rgba( $bg_primary, 0.92 ) . ');}';
    $css .= '.lunara-home-pulse-card-top,.lunara-oscar-spotlight-layout,.lunara-home-pulse-feature-card{background:linear-gradient(135deg,' . lunara_hex_to_rgba( $bg_secondary, 0.96 ) . ',' . lunara_hex_to_rgba( $bg_primary, 0.98 ) . ');}';

    if ( '' !== $body_font ) {
        $css .= 'body,.lunara-front-page,.lunara-archive-page{font-family:' . $body_font . ';}';
    }

    if ( '' !== $heading_font ) {
        $css .= '.lunara-home-hero-title,.lunara-home-section-title,.lunara-section-title,.lunara-home-pulse-title,.lunara-home-pulse-feature-heading,.lunara-poster-card-title,.lunara-dispatch-lead-title,.lunara-dispatch-rail-title,.lunara-home-winner-title,.lunara-home-pulse-note-title,.lunara-review-grid-title,.lunara-oscar-spotlight-text-panel h3{font-family:' . $heading_font . ';}';
    }

    /* Header: only color-dependent rules here — layout handled by style.css via CSS variables */
    $css .= '.ct-header [data-row*="middle"]{background:' . $header_bg . ';border-bottom:1px solid ' . $header_border . ';}';
    $css .= '.ct-header [data-row*="middle"] .ct-container{max-width:var(--lunara-header-max);padding-left:var(--lunara-header-side-pad);padding-right:var(--lunara-header-side-pad);}';
    $css .= '.ct-header .site-title,.ct-header .site-title a{font-size:var(--lunara-header-title-size);}';
    $css .= '.ct-header [data-id="menu"] .ct-menu,.ct-header [data-id="menu"] .menu{column-gap:var(--lunara-header-nav-gap);}';
    $css .= '.ct-header [data-id="menu"] .ct-menu > li > a,.ct-header [data-id="menu"] .menu > li > a,.ct-header button,.ct-header [data-id="search"]{color:' . $header_link . ';font-size:var(--lunara-header-nav-size);letter-spacing:var(--lunara-header-nav-track);text-transform:uppercase;}';
    $css .= '.ct-header [data-id="menu"] .ct-menu > li > a:hover,.ct-header [data-id="menu"] .menu > li > a:hover,.ct-header button:hover,.ct-header [data-id="search"]:hover{color:' . $header_hover . ';}';
    $css .= '.ct-footer,footer.site-footer{background:linear-gradient(180deg,' . lunara_hex_to_rgba( $bg_secondary, 0.92 ) . ',' . lunara_hex_to_rgba( $bg_primary, 0.98 ) . ');border-top:1px solid ' . $border_alpha . ';}';
    $css .= '.ct-footer .ct-container,footer.site-footer .ct-container{max-width:var(--lunara-shell-max);padding-left:var(--lunara-shell-pad);padding-right:var(--lunara-shell-pad);}';
    $css .= '.ct-footer a,footer.site-footer a{color:' . $text_color . ';}';
    $css .= '.ct-footer a:hover,footer.site-footer a:hover{color:' . $accent_soft . ';}';
    $css .= '.lunara-front-page,.lunara-archive-page,.lunara-editorial-single-page,.lunara-oscars-portal{max-width:var(--lunara-home-max);padding-left:var(--lunara-home-pad);padding-right:var(--lunara-home-pad);}';
    $css .= '.lunara-front-page,.lunara-editorial-single-page,.lunara-oscars-portal{gap:var(--lunara-home-gap);}';
    $css .= '.lunara-home-hero.is-minimal,.lunara-archive-hero,.lunara-journal-single-hero,.lunara-oscars-portal-hero{padding-top:var(--lunara-home-hero-top);}';

    foreach ( lunara_get_home_section_slugs() as $slug ) {
        $order = isset( $section_order[ $slug ] ) ? intval( $section_order[ $slug ] ) : 99;
        $css  .= '.lunara-front-page > .lunara-home-slot-' . $slug . '{order:' . $order . ';}';
    }

    if ( ! get_theme_mod( 'lunara_show_logo', true ) ) {
        $css .= '.ct-header .site-logo-container{display:none !important;}';
    }

    if ( ! get_theme_mod( 'lunara_show_site_title', true ) ) {
        $css .= '.ct-header .site-title{display:none !important;}';
    }

    /* Mobile responsive rules now handled entirely by style.css breakpoints (900px / 640px) */

    if ( '' === $css ) {
        return;
    }

    echo '<style id="lunara-runtime-customizer-css">' . $css . '</style>' . "\n";
}
add_action( 'wp_head', 'lunara_output_runtime_customizer_css', 99 );

/**
 * Register Reviews Custom Post Type
 */
if ( ! defined( 'LUNARA_CORE_VERSION' ) ) {
    function lunara_register_reviews_cpt() {
        $args = array(
            'labels' => array(
                'name'          => 'Reviews',
                'singular_name' => 'Review',
                'add_new'       => 'Add New Review',
                'add_new_item'  => 'Add New Review',
                'edit_item'     => 'Edit Review',
                'menu_name'     => 'Reviews',
            ),
            'public'            => true,
            'has_archive'       => true,
            'rewrite'           => array( 'slug' => 'reviews' ),
            'menu_icon'         => 'dashicons-star-filled',
            'supports'          => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
            'taxonomies'        => array( 'category', 'post_tag' ),
            'show_in_rest'      => true,
        );
        register_post_type( 'review', $args );
    }
    add_action( 'init', 'lunara_register_reviews_cpt' );

    function lunara_register_review_single_rewrite() {
        add_rewrite_rule(
            '^reviews/([^/]+)/?$',
            'index.php?post_type=review&name=$matches[1]',
            'top'
        );
    }
    add_action( 'init', 'lunara_register_review_single_rewrite', 20 );

    function lunara_preserve_review_canonical( $redirect_url ) {
        if ( is_singular( 'review' ) ) {
            return false;
        }

        return $redirect_url;
    }
    add_filter( 'redirect_canonical', 'lunara_preserve_review_canonical', 10, 1 );

    /**
     * Flush rewrite rules on activation
     */
    function lunara_flush_rewrites() {
        lunara_register_reviews_cpt();
        flush_rewrite_rules();
    }
    add_action( 'after_switch_theme', 'lunara_flush_rewrites' );
}

/**
 * Blocksy controls the site header natively.
 */

/**
 * Shortcode: Homepage Content
 */
function lunara_home_shortcode() {
    ob_start();
    ?>
    <?php echo do_shortcode('[lunara_carousel set="homepage"]'); ?>

    <div class="lunara-tagline">
        <p class="lunara-tagline-text">Film criticism and the living record of the Oscars.</p>
    </div>

    <section class="lunara-section">
        <div class="lunara-section-header">
            <h2 class="lunara-section-title">Latest Reviews</h2>
        </div>
        <?php echo do_shortcode('[lunara_reviews count="3"]'); ?>
        <div class="text-center" style="margin-top: 30px;">
            <a href="<?php echo esc_url( home_url( '/reviews/' ) ); ?>" class="lunara-btn">View All Reviews</a>
        </div>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'lunara_home', 'lunara_home_shortcode' );

/**
 * Shortcode: Display Reviews
 */
function lunara_reviews_shortcode( $atts ) {
    $atts = shortcode_atts( array( 'count' => 6 ), $atts );
    $count = intval( $atts['count'] );
    if ( $count === 0 ) { $count = 6; }

    $query = new WP_Query( array(
        'post_type'      => 'review',
        'posts_per_page' => $count < 0 ? -1 : $count,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'ignore_sticky_posts' => true,
    ) );

    if ( ! $query->have_posts() ) {
        return '<p style="text-align:center;color:#888;">No reviews yet.</p>';
    }

    ob_start();
    echo '<div class="lunara-review-grid lunara-review-archive-grid">';
    while ( $query->have_posts() ) {
        $query->the_post();
        $score = get_post_meta( get_the_ID(), '_lunara_score', true );
        $year  = get_post_meta( get_the_ID(), '_lunara_year', true );
        $director = get_post_meta( get_the_ID(), '_lunara_director', true );
        ?>
        <article class="lunara-review-grid-card lunara-review-archive-card">
            <a class="lunara-review-grid-link" href="<?php the_permalink(); ?>">
                <div class="lunara-review-grid-poster-wrap">
                    <?php if ( has_post_thumbnail() ) : ?>
                        <?php the_post_thumbnail( 'medium_large', array( 'class' => 'lunara-review-grid-poster', 'loading' => 'lazy' ) ); ?>
                    <?php endif; ?>
                    <?php if ( $score ) : ?><span class="lunara-score-badge"><?php echo wp_kses_post( lunara_render_stars( $score ) ); ?></span><?php endif; ?>
                </div>
                <div class="lunara-review-grid-copy">
                    <h3 class="lunara-review-grid-title"><?php the_title(); ?></h3>
                    <p class="lunara-review-grid-meta"><?php echo esc_html( $year ); ?><?php if ( $director ) : ?> · <?php echo esc_html( $director ); ?><?php endif; ?></p>
                </div>
            </a>
        </article>
        <?php
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'lunara_reviews', 'lunara_reviews_shortcode' );

/**
 * Shortcode: Display Posts by Category
 */
function lunara_posts_shortcode( $atts ) {
    $atts = shortcode_atts( array( 
        'category' => '', 
        'count'    => 6 
    ), $atts );
    
    $query = new WP_Query( array(
        'post_type'      => 'post',
        'category_name'  => sanitize_text_field( $atts['category'] ),
        'posts_per_page' => intval( $atts['count'] ),
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
        'ignore_sticky_posts' => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ) );
    
    if ( ! $query->have_posts() ) {
        return '<p style="text-align:center;color:#888;">No posts found.</p>';
    }
    
    ob_start();
    echo '<div class="lunara-grid">';
    while ( $query->have_posts() ) {
        $query->the_post();
        ?>
        <article class="lunara-card">
            <?php if ( has_post_thumbnail() ) : ?>
                <a href="<?php the_permalink(); ?>">
                    <?php the_post_thumbnail( 'medium', array( 'class' => 'lunara-card-thumb' ) ); ?>
                </a>
            <?php endif; ?>
            <h3 class="lunara-card-title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h3>
            <div class="lunara-card-meta"><?php echo get_the_date( 'F j, Y' ); ?></div>
            <div class="lunara-card-excerpt"><?php the_excerpt(); ?></div>
            <a href="<?php the_permalink(); ?>" class="lunara-btn">Read More</a>
        </article>
        <?php
    }
    echo '</div>';
    wp_reset_postdata();
    return ob_get_clean();
}
add_shortcode( 'lunara_posts', 'lunara_posts_shortcode' );

/* ========================================
   LUNARA DEBRIEF - REVIEW METADATA
   ======================================== */

/**
 * Render star rating
 */
if ( ! function_exists( 'lunara_render_stars' ) ) {
    function lunara_render_stars( $score ) {
        if ( empty( $score ) ) {
            return '';
        }
        
        $score = floatval( $score );
        $full_stars = floor( $score );
        $half_star = ( $score - $full_stars ) >= 0.5;
        
        $output = '<span class="lunara-stars">';
        for ( $i = 0; $i < $full_stars; $i++ ) {
            $output .= '★';
        }
        if ( $half_star ) {
            $output .= '½';
        }
        $output .= '</span>';
        
        return $output;
    }
}

/**
 * Add Lunara Debrief meta box to Reviews
 */
if ( ! defined( 'LUNARA_CORE_VERSION' ) ) {
    function lunara_add_debrief_meta_box() {
        add_meta_box(
            'lunara_debrief_meta',
            'Lunara Debrief',
            'lunara_debrief_meta_callback',
            'review',
            'normal',
            'high'
        );
    }
    add_action( 'add_meta_boxes', 'lunara_add_debrief_meta_box' );

    /**
     * Debrief meta box callback
     */
    function lunara_debrief_meta_callback( $post ) {
        wp_nonce_field( 'lunara_debrief_nonce', 'lunara_debrief_nonce' );
        
        $score = get_post_meta( $post->ID, '_lunara_score', true );
        $year = get_post_meta( $post->ID, '_lunara_year', true );
        $imdb_review_id = get_post_meta( $post->ID, '_lunara_imdb_title_id', true );
        $where = get_post_meta( $post->ID, '_lunara_where', true );
        $theme_echo = get_post_meta( $post->ID, '_lunara_theme_echo', true );
        $counter = get_post_meta( $post->ID, '_lunara_counter_program', true );
        $craft = get_post_meta( $post->ID, '_lunara_craft_mirror', true );
        ?>
        <style>
            .lunara-meta-field { margin-bottom: 15px; }
            .lunara-meta-field label { display: block; font-weight: 600; margin-bottom: 5px; }
            .lunara-meta-field input, .lunara-meta-field select { width: 100%; }
            .lunara-meta-field .description { font-style: italic; color: #666; font-size: 12px; margin-top: 4px; }
            .lunara-meta-section { margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd; }
            .lunara-meta-section h4 { margin: 0 0 15px; color: #c9a961; }
            .lunara-meta-row { display: flex; gap: 20px; }
            .lunara-meta-row .lunara-meta-field { flex: 1; }
        </style>
        
        <div class="lunara-meta-row">
            <div class="lunara-meta-field">
                <label for="lunara_score">Score (0-5, use .5 for half stars)</label>
                <input type="text" id="lunara_score" name="lunara_score" value="<?php echo esc_attr( $score ); ?>" placeholder="4.5">
                <p class="description">Examples: 4, 4.5, 5 → ★★★★, ★★★★½, ★★★★★</p>
            </div>
            
            <div class="lunara-meta-field">
                <label for="lunara_year">Year Released</label>
                <select id="lunara_year" name="lunara_year">
                    <option value="">— Select Year —</option>
                    <?php 
                    $current_year = (int) date('Y') + 2; // Allow 2 years ahead for upcoming films
                    for ( $y = $current_year; $y >= 1920; $y-- ) : 
                    ?>
                        <option value="<?php echo $y; ?>" <?php selected( $year, $y ); ?>><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        
    </div>

        <div class="lunara-meta-field">
            <label for="lunara_imdb_title_id">IMDb Title ID (for this review)</label>
            <input type="text" id="lunara_imdb_title_id" name="lunara_imdb_title_id" value="<?php echo esc_attr( $imdb_review_id ); ?>" placeholder="tt1234567">
            <p class="description">Connects this review to the Oscars database film page (shows a “Lunara Review” module on /oscars/title/tt…/).</p>
        </div>

        <div class="lunara-meta-field">
            <label for="lunara_where">Where to Watch</label>
            <input type="text" id="lunara_where" name="lunara_where" value="<?php echo esc_attr( $where ); ?>" placeholder="Netflix, Max, Theaters">
        </div>
        
        <div class="lunara-meta-section">
            <h4>PAIR IT WITH</h4>
            
            <div class="lunara-meta-field">
                <label for="lunara_theme_echo">Theme Echo</label>
                <input type="text" id="lunara_theme_echo" name="lunara_theme_echo" value="<?php echo esc_attr( $theme_echo ); ?>" placeholder="Film that shares thematic DNA">
                <p class="description">Tip: for clickable internal + IMDb links, you can append <code>| tt1234567</code> or paste a full IMDb URL anywhere in the line.</p>
            </div>
            
            <div class="lunara-meta-field">
                <label for="lunara_counter_program">Counter-Program</label>
                <input type="text" id="lunara_counter_program" name="lunara_counter_program" value="<?php echo esc_attr( $counter ); ?>" placeholder="Film that offers opposing perspective">
                <p class="description">Tip: optionally add <code>| tt1234567</code> (or an IMDb URL) to enable direct links.</p>
            </div>
            
            <div class="lunara-meta-field">
                <label for="lunara_craft_mirror">Career Context (Optional)</label>
                <input type="text" id="lunara_craft_mirror" name="lunara_craft_mirror" value="<?php echo esc_attr( $craft ); ?>" placeholder="Film with similar technical approach">
                <p class="description">Tip: optionally add <code>| tt1234567</code> (or an IMDb URL) to enable direct links.</p>
            </div>
        </div>
        <?php
    }

    /**
     * Save Debrief meta
     */
    function lunara_save_debrief_meta( $post_id ) {
        if ( ! isset( $_POST['lunara_debrief_nonce'] ) ) return;
        if ( ! wp_verify_nonce( $_POST['lunara_debrief_nonce'], 'lunara_debrief_nonce' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        
        $fields = array( 'lunara_score', 'lunara_year', 'lunara_imdb_title_id', 'lunara_where', 'lunara_theme_echo', 'lunara_counter_program', 'lunara_craft_mirror' );
        
        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( $_POST[ $field ] ) );
            }
        }
    }
    add_action( 'save_post_review', 'lunara_save_debrief_meta' );
}

/**
 * Load the bundled IMDb title map (title|year -> ttID).
 * This lets Debrief lines link directly to IMDb (and Lunara Oscars film pages)
 * without requiring you to paste a tt-id every time.
 *
 * File: /assets/data/imdb-title-map.json
 *
 * Key format: "<normalized_title>|<year>" => "tt1234567"
 */

/**
 * Resolve the active Academy Awards DB table name (supports multiple plugin variants).
 */
function lunara_awards_table_name() {
    static $resolved_table = null;

    if ( $resolved_table !== null ) {
        return $resolved_table;
    }

    $cached_table = get_transient( 'lunara_awards_table_name_v1' );
    if ( is_string( $cached_table ) ) {
        $resolved_table = $cached_table;
        return $resolved_table;
    }

    global $wpdb;
    $candidates = array(
        $wpdb->prefix . 'academy_awards',
        $wpdb->prefix . 'academy_awards_table',
        $wpdb->prefix . 'aat_awards',
        $wpdb->prefix . 'lunara_academy_awards',
        $wpdb->prefix . 'lunara_awards',
    );
    foreach ( $candidates as $t ) {
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $found = $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $t ) );
        if ( $found ) {
            $resolved_table = $t;
            set_transient( 'lunara_awards_table_name_v1', $resolved_table, 12 * HOUR_IN_SECONDS );
            return $resolved_table;
        }
    }

    $resolved_table = '';
    set_transient( 'lunara_awards_table_name_v1', $resolved_table, HOUR_IN_SECONDS );
    return $resolved_table;
}

/**
 * Get Oscar nominations/wins counts for a film by IMDb title id (tt...).
 * Returns array( 'noms' => int, 'wins' => int ).
 */
function lunara_get_oscar_ledger_counts( $tt ) {
    $tt = strtolower( trim( (string) $tt ) );
    if ( $tt === '' || ! preg_match( '/^tt\d{7,8}$/', $tt ) ) {
        return array( 'noms' => 0, 'wins' => 0 );
    }

    $cache_key = 'lunara_oscar_ledger_' . $tt;
    $cached = get_transient( $cache_key );
    if ( is_array( $cached ) && isset( $cached['noms'], $cached['wins'] ) ) {
        return $cached;
    }

    $table = lunara_awards_table_name();
    if ( $table === '' ) {
        return array( 'noms' => 0, 'wins' => 0 );
    }

    global $wpdb;

    // Fetch both totals in one pass so single-review pages do less database work.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT COUNT(*) AS noms, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS wins FROM {$table} WHERE film_id = %s",
            $tt
        ),
        ARRAY_A
    );

    $out = array(
        'noms' => isset( $row['noms'] ) ? (int) $row['noms'] : 0,
        'wins' => isset( $row['wins'] ) ? (int) $row['wins'] : 0,
    );
    set_transient( $cache_key, $out, 6 * HOUR_IN_SECONDS );
    return $out;
}

/**
 * Render the Oscar Ledger pill (clicks into Lunara's Oscars film page).
 */
function lunara_render_oscar_ledger_pill( $tt, $counts = null ) {
    $tt = strtolower( trim( (string) $tt ) );
    if ( $tt === '' || ! preg_match( '/^tt\d{7,8}$/', $tt ) ) {
        return '';
    }

    if ( ! is_array( $counts ) ) {
        $counts = lunara_get_oscar_ledger_counts( $tt );
    }

    $noms = (int) ( $counts['noms'] ?? 0 );
    $wins = (int) ( $counts['wins'] ?? 0 );

    // Keep it classy: only show when the film actually has Oscar presence.
    if ( $noms <= 0 ) {
        return '';
    }

    $href = home_url( '/oscars/title/' . $tt . '/' );
    $label = sprintf( '%d nominations • %d wins', $noms, $wins );

    return '<a class="lunara-oscar-ledger" href="' . esc_url( $href ) . '">'
        . '<span class="lunara-oscar-ledger-pill">Oscar Ledger</span>'
        . '<span class="lunara-oscar-ledger-counts">' . esc_html( $label ) . '</span>'
        . '</a>';
}


function lunara_imdb_title_map() {
    static $map = null;
    if ( $map !== null ) {
        return $map;
    }

    $map = array();
    $asset = lunara_resolve_theme_asset(
        'assets/data/imdb-title-map.json',
        array( 'imdb-title-map.json' )
    );
    $file = $asset['path'];

    if ( $file && file_exists( $file ) ) {
        $json = file_get_contents( $file );
        $data = json_decode( $json, true );
        if ( is_array( $data ) ) {
            $map = $data;
        }
    }

    return $map;
}

/**
 * Normalize a title to a stable lookup key.
 */
function lunara_normalize_title_key( $title ) {
    $t = strtolower( remove_accents( (string) $title ) );
    $t = str_replace( '&', 'and', $t );
    $t = preg_replace( '/[^a-z0-9]+/', ' ', $t );
    $t = trim( preg_replace( '/\s+/', ' ', $t ) );
    return $t;
}



/**
 * Shortcode: The Lunara Debrief Block
 */
function lunara_debrief_shortcode( $atts ) {
    $post_id = get_the_ID();
    
    $score = get_post_meta( $post_id, '_lunara_score', true );
    $year = get_post_meta( $post_id, '_lunara_year', true );
    $where = get_post_meta( $post_id, '_lunara_where', true );
    $theme_echo = get_post_meta( $post_id, '_lunara_theme_echo', true );
    $counter = get_post_meta( $post_id, '_lunara_counter_program', true );
    $craft = get_post_meta( $post_id, '_lunara_craft_mirror', true );
    $review_tt = get_post_meta( $post_id, '_lunara_imdb_title_id', true );
    if ( is_string( $review_tt ) && preg_match( '#imdb\.com/title/(tt\d{7,8})#i', $review_tt, $mtt ) ) { $review_tt = $mtt[1]; }
    $review_tt = strtolower( trim( (string) $review_tt ) );
    
    if ( empty( $score ) && empty( $where ) && empty( $theme_echo ) && empty( $year ) ) {
        return '';
    }

    // Local helper: render a "Pair It With" line.
    // Supports optional IMDb title ID / URL embedded anywhere in the field.
    // Examples you can paste into the meta field:
    //   "There Will Be Blood (2007) — ... | tt0469494"
    //   "There Will Be Blood (2007) — ... https://www.imdb.com/title/tt0469494/"
    // If a tt-id is present, the title links to the internal Oscars film page (/oscars/title/tt.../)
    // and an "IMDb" reference chip is shown.
    $format_pairing = function( $value ) {
    $raw = trim( (string) $value );
    if ( $raw === '' ) {
        return '';
    }

    // 1) Extract a tt-id if present anywhere (either bare tt123... or full IMDb URL).
    //    Also optionally extract a Letterboxd film URL (for clickable title).
    $tt = '';
    $lb = '';
    if ( preg_match( '/\btt\d{7,8}\b/i', $raw, $m ) ) {
        $tt = strtolower( $m[0] );
    } elseif ( preg_match( '#imdb\.com/title/(tt\d{7,8})#i', $raw, $m ) ) {
        $tt = strtolower( $m[1] );
    }

    // Letterboxd film URL (optional). Supports:
    //   - https://letterboxd.com/film/<slug>/
    //   - | lb:https://letterboxd.com/film/<slug>/
    //   - | https://letterboxd.com/film/<slug>/
    if ( preg_match( '#letterboxd\.com/film/[^\s\|\)\]]+/?#i', $raw, $m ) ) {
        $lb = $m[0];
        // Ensure scheme.
        if ( stripos( $lb, 'http' ) !== 0 ) {
            $lb = 'https://' . ltrim( $lb, '/' );
        }
    }

    // 2) Remove the tt-id / IMDb URL / Letterboxd URL from the display string so the line stays clean.
    $clean = $raw;
    if ( $tt !== '' ) {
        $clean = preg_replace( '/\[\s*' . preg_quote( $tt, '/' ) . '\s*\]/i', '', $clean );
        $clean = preg_replace( '/\(\s*' . preg_quote( $tt, '/' ) . '\s*\)/i', '', $clean );
        $clean = preg_replace( '/\s*\|\s*\b' . preg_quote( $tt, '/' ) . '\b\s*$/i', '', $clean );
        $clean = preg_replace( '#\s*\|\s*https?://(www\.)?imdb\.com/title/' . preg_quote( $tt, '#' ) . '/?\s*$#i', '', $clean );
        $clean = preg_replace( '#\s*https?://(www\.)?imdb\.com/title/' . preg_quote( $tt, '#' ) . '/?\s*#i', ' ', $clean );
        $clean = preg_replace( '/\s*\b' . preg_quote( $tt, '/' ) . '\b\s*/i', ' ', $clean );
        $clean = trim( preg_replace( '/\s{2,}/', ' ', $clean ) );
    }
    if ( $lb !== '' ) {
        $clean = preg_replace( '#\s*\|\s*lb:\s*' . preg_quote( $lb, '#' ) . '\s*$#i', '', $clean );
        $clean = preg_replace( '#\s*\|\s*' . preg_quote( $lb, '#' ) . '\s*$#i', '', $clean );
        $clean = preg_replace( '#\s*' . preg_quote( $lb, '#' ) . '\s*#i', ' ', $clean );
        $clean = trim( preg_replace( '/\s{2,}/', ' ', $clean ) );
    }

    // 3) Split into title + note (prefer em dash).
    $parts = preg_split( '/\s+—\s+/u', $clean, 2 );
    if ( count( $parts ) < 2 ) {
        $parts = preg_split( '/\s+-\s+/', $clean, 2 );
    }

    $title = trim( $parts[0] ?? '' );
    $note  = trim( $parts[1] ?? '' );

    // 4) Pull year out of "Title (YYYY)" for smarter lookups & cleaner IMDb search queries.
    $title_base = $title;
    $year = '';
    if ( preg_match( '/^(.*?)(?:\s*\((\d{4})\))\s*$/', $title, $m2 ) ) {
        $title_base = trim( $m2[1] );
        $year = trim( $m2[2] );
    }

    // 5) If no explicit tt-id was provided, try to resolve via the bundled IMDb title map.
    if ( $tt === '' && $title_base !== '' ) {
        $map = lunara_imdb_title_map();
        if ( $year !== '' ) {
            $key = lunara_normalize_title_key( $title_base ) . '|' . $year;
            if ( isset( $map[ $key ] ) ) {
                $tt = strtolower( $map[ $key ] );
            }
        } else {
            // Only use a title-only lookup if it's unambiguous.
            $prefix = lunara_normalize_title_key( $title_base ) . '|';
            $matches = array();
            foreach ( $map as $k => $val ) {
                if ( strpos( $k, $prefix ) === 0 ) {
                    $matches[] = $val;
                }
            }
            $matches = array_values( array_unique( $matches ) );
            if ( count( $matches ) === 1 ) {
                $tt = strtolower( $matches[0] );
            }
        }
    }

    // 6) Build title + links.
    //    Title click goes to Letterboxd (film URL if provided; otherwise Letterboxd search).
    $lb_href = '';
    if ( $lb !== '' ) {
        $lb_href = $lb;
    } else {
        $q = $title_base !== '' ? $title_base : $title;
        if ( $year !== '' ) {
            $q .= ' ' . $year;
        }
        $lb_href = 'https://letterboxd.com/search/' . rawurlencode( $q ) . '/';
    }
    $title_html = '<a class="lunara-pair-title" href="' . esc_url( $lb_href ) . '" target="_blank" rel="noopener noreferrer nofollow"><em>' . esc_html( $title ) . '</em></a>';
    $chips_html = '';

    if ( $tt !== '' ) {
            $imdb = 'https://www.imdb.com/title/' . $tt . '/';

            // IMDb is the external reference. Oscar Ledger pill drives visitors into Lunara's database.
            $chips_html = ' <a class="lunara-debrief-chip lunara-debrief-chip-imdb" href="' . esc_url( $imdb ) . '" target="_blank" rel="noopener noreferrer nofollow">IMDb</a>';
            $chips_html .= ' ' . lunara_render_oscar_ledger_pill( $tt );
        } else {
        // Fallback: IMDb search. Use "Title YYYY" (no parentheses) for better results.
        $q = $title_base !== '' ? $title_base : $title;
        if ( $year !== '' ) {
            $q .= ' ' . $year;
        }
        $imdb_search = 'https://www.imdb.com/find/?q=' . rawurlencode( $q ) . '&s=tt';
        $chips_html  = ' <a class="lunara-debrief-chip lunara-debrief-chip-imdb" href="' . esc_url( $imdb_search ) . '" target="_blank" rel="noopener noreferrer nofollow">IMDb</a>';
    }

    // Optional poster thumbnail (pulled from the Academy Awards Database poster library when available).
    // If no poster is found, we fall back to the original text-only layout.
    $poster_html = '';
    if ( $tt !== '' && class_exists( 'Academy_Awards_Table' ) ) {
        $aat = Academy_Awards_Table::get_instance();
        if ( $aat && method_exists( $aat, 'get_poster_img_html_for_title' ) ) {
            // Use a non-cropped size so posters keep their aspect ratio (we size down via CSS).
            $poster_html = (string) $aat->get_poster_img_html_for_title(
                $tt,
                'medium',
                array(
                    'class'    => 'lunara-debrief-thumb',
                    'loading'  => 'lazy',
                    'decoding' => 'async',
                )
            );
        }
    }

    // IMPORTANT UX FIX:
    // The descriptive sentence (note) must not trail AFTER the IMDb chip.
    // Render a clean first line (title + chips), then the note on its own line below.
    $line1 = '<span class="lunara-debrief-line1">' . $title_html . $chips_html . '</span>';
    $line2 = '';
    if ( $note !== '' ) {
        $line2 = '<span class="lunara-debrief-note">' . esc_html( $note ) . '</span>';
    }

    $text_html = '<span class="lunara-debrief-pairing-text">' . $line1 . $line2 . '</span>';

    if ( $poster_html === '' ) {
        return $text_html;
    }

    return '<span class="lunara-debrief-pairing">'
        . '<span class="lunara-debrief-thumb-wrap">' . $poster_html . '</span>'
        . $text_html
        . '</span>';
};
    
    ob_start();
    ?>
    <section class="lunara-debrief-block">
        <h3 class="lunara-debrief-heading">LUNARA DEBRIEF</h3>
        <?php $kicker = trim( (string) get_theme_mod( 'lunara_debrief_kicker_text', 'A LUNARA FILM SIGNATURE' ) ); ?>
        <?php if ( $kicker !== '' ) : ?>
            <div class="lunara-debrief-kicker"><?php echo esc_html( $kicker ); ?></div>
        <?php endif; ?>
<ul class="lunara-debrief-list">
            <?php if ( $score ) : ?>
                <li><strong>Score:</strong><span class="lunara-debrief-value"><?php echo lunara_render_stars( $score ); ?></span></li>
            <?php endif; ?>

            <?php if ( $review_tt ) : ?>
                <?php $ledger = lunara_render_oscar_ledger_pill( $review_tt ); ?>
                <?php if ( $ledger !== '' ) : ?>
                    <li class="lunara-debrief-ledger-row"><strong>&nbsp;</strong><span class="lunara-debrief-value"><?php echo $ledger; ?></span></li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if ( $year ) : ?>
                <li><strong>Year:</strong><span class="lunara-debrief-value"><?php echo esc_html( $year ); ?></span></li>
            <?php endif; ?>
            
            <?php if ( $where ) : ?>
                <li><strong>Where to Watch:</strong><span class="lunara-debrief-value"><?php echo esc_html( $where ); ?></span></li>
            <?php endif; ?>
            
            <?php if ( $theme_echo || $counter || $craft ) : ?>
                <li class="lunara-debrief-pair-header">Pair It With</li>
                
                <?php if ( $theme_echo ) : ?>
                    <li><strong>Theme Echo:</strong><span class="lunara-debrief-value"><?php echo $format_pairing( $theme_echo ); ?></span></li>
                <?php endif; ?>
                
                <?php if ( $counter ) : ?>
                    <li><strong>Counter-Program:</strong><span class="lunara-debrief-value"><?php echo $format_pairing( $counter ); ?></span></li>
                <?php endif; ?>
                
                <?php if ( $craft ) : ?>
                    <li><strong>Career Context:</strong><span class="lunara-debrief-value"><?php echo $format_pairing( $craft ); ?></span></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </section>
    <?php
    return ob_get_clean();
}
add_shortcode( 'lunara_debrief', 'lunara_debrief_shortcode' );

/**
 * Auto-append Lunara Debrief to single review content
 */
function lunara_append_debrief_to_review( $content ) {
    if ( is_singular( 'review' ) && in_the_loop() && is_main_query() ) {
        // Avoid shortcode parsing overhead.
        $content .= lunara_debrief_shortcode( array() );
    }
    return $content;
}
add_filter( 'the_content', 'lunara_append_debrief_to_review' );

/* ========================================
   SLIDE SETS - CURATED CAROUSELS
   ======================================== */

/**
 * Register Slide Sets taxonomy for Media
 * In WP Admin: Media Library → click an image → edit → assign to a Slide Set (e.g., "homepage")
 * Then use: [lunara_carousel set="homepage"]
 */
if ( ! defined( 'LUNARA_CORE_VERSION' ) ) {
add_action( 'init', function() {
    register_taxonomy( 'lunara_slide_set', array( 'attachment' ), array(
        'labels' => array(
            'name'          => __( 'Slide Sets', 'lunara-film' ),
            'singular_name' => __( 'Slide Set', 'lunara-film' ),
            'search_items'  => __( 'Search Slide Sets', 'lunara-film' ),
            'all_items'     => __( 'All Slide Sets', 'lunara-film' ),
            'edit_item'     => __( 'Edit Slide Set', 'lunara-film' ),
            'update_item'   => __( 'Update Slide Set', 'lunara-film' ),
            'add_new_item'  => __( 'Add New Slide Set', 'lunara-film' ),
            'new_item_name' => __( 'New Slide Set Name', 'lunara-film' ),
            'menu_name'     => __( 'Slide Sets', 'lunara-film' ),
        ),
        'public'             => false,
        'show_ui'            => true,
        'show_admin_column'  => true,
        'show_in_quick_edit' => true,
        'show_in_rest'       => true,
        'hierarchical'       => false,
        'rewrite'            => false,
        'query_var'          => false,
    ) );
} );


/**
 * Attachment field: Carousel Link URL (stored as _lunara_slide_link).
 * Falls back to Alt Text for backward compatibility.
 */
add_filter('attachment_fields_to_edit', function($form_fields, $post) {
    // Show for all media items; harmless if not used.
    $form_fields['lunara_slide_link'] = array(
        'label' => 'Carousel Link URL',
        'input' => 'text',
        'value' => get_post_meta($post->ID, '_lunara_slide_link', true),
        'helps' => 'Optional. If set, the carousel slide will link here. If empty, the theme falls back to using Alt Text as the link.',
    );
    return $form_fields;
}, 10, 2);

add_filter('attachment_fields_to_save', function($post, $attachment) {
    if (isset($attachment['lunara_slide_link'])) {
        $url = trim((string) $attachment['lunara_slide_link']);
        if ($url === '') {
            delete_post_meta($post['ID'], '_lunara_slide_link');
        } else {
            update_post_meta($post['ID'], '_lunara_slide_link', esc_url_raw($url));
        }
    }
    return $post;
}, 10, 2);

/**
 * Admin: Carousel Manager (drag & drop ordering per Slide Set).
 */
add_action('admin_menu', function() {
    add_theme_page(
        'Lunara Carousel',
        'Lunara Carousel',
        'manage_options',
        'lunara-carousel-manager',
        'lunara_render_carousel_manager_page'
    );
});

add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'appearance_page_lunara-carousel-manager') {
        return;
    }

    $admin_css = lunara_resolve_theme_asset(
        'assets/css/lunara-carousel-admin.css',
        array(
            'lunara-carousel-admin.css',
            'lunara-carousel-admis.css',
        )
    );
    $admin_js = lunara_resolve_theme_asset(
        'assets/js/lunara-carousel-admin.js',
        array(
            'lunara-carousel-admin.js',
            'lunara-carousel-admis.js',
        )
    );

    if ( $admin_css['uri'] ) {
        wp_enqueue_style(
            'lunara-carousel-admin',
            $admin_css['uri'],
            array(),
            lunara_theme_asset_version( $admin_css['path'] )
        );
    }

    wp_enqueue_script('jquery-ui-sortable');

    if ( $admin_js['uri'] ) {
        wp_enqueue_script(
            'lunara-carousel-admin',
            $admin_js['uri'],
            array('jquery', 'jquery-ui-sortable'),
            lunara_theme_asset_version( $admin_js['path'] ),
            true
        );
    }

    if ( $admin_js['uri'] ) {
        wp_localize_script('lunara-carousel-admin', 'LUNARA_CAROUSEL_ADMIN', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('lunara_carousel_admin'),
        ));
    }
});

function lunara_render_carousel_manager_page() {
    if (!current_user_can('manage_options')) {
        wp_die('You do not have permission to access this page.');
    }

    $taxonomy = 'lunara_slide_set';
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));

    $selected = isset($_GET['set']) ? sanitize_text_field(wp_unslash($_GET['set'])) : '';
    if ($selected === '' && !empty($terms) && !is_wp_error($terms)) {
        $selected = $terms[0]->slug;
    }

    echo '<div class="wrap">';
    echo '<h1>Lunara Carousel</h1>';
    echo '<p><strong>How to update the carousel:</strong> Upload (or select) images in <em>Media → Library</em>, then assign them to a <em>Slide Set</em>. Use this page to drag & drop reorder slides. To add a link per slide, edit the media item and fill in <em>Carousel Link URL</em>.</p>';

    echo '<form method="get" action="">';
    echo '<input type="hidden" name="page" value="lunara-carousel-manager" />';
    echo '<label for="lunara-slide-set"><strong>Slide Set:</strong></label> ';
    echo '<select id="lunara-slide-set" name="set">';
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $t) {
            $sel = selected($selected, $t->slug, false);
            echo '<option value="' . esc_attr($t->slug) . '" ' . $sel . '>' . esc_html($t->name) . '</option>';
        }
    }
    echo '</select> ';
    submit_button('Load', 'secondary', '', false);
    echo '</form>';

    if ($selected) {
        $attachments = get_posts(array(
            'post_type' => 'attachment',
            'post_status' => 'inherit',
            'posts_per_page' => -1,
            'orderby' => array('menu_order' => 'ASC', 'date' => 'DESC'),
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field' => 'slug',
                    'terms' => $selected,
                ),
            ),
        ));

        echo '<hr />';
        echo '<h2>Slides in: ' . esc_html($selected) . '</h2>';

        if (empty($attachments)) {
            echo '<p>No slides found in this set yet.</p>';
        } else {
            echo '<p class="description">Drag & drop to reorder. Then click <strong>Save Order</strong>.</p>';
            echo '<ul id="lunara-carousel-sortable" class="lunara-carousel-sortable" data-slide-set="' . esc_attr($selected) . '">';
            foreach ($attachments as $att) {
                $thumb = wp_get_attachment_image($att->ID, array(120, 120), true);
                $link = get_post_meta($att->ID, '_lunara_slide_link', true);
                echo '<li class="lunara-carousel-item" data-id="' . esc_attr($att->ID) . '">';
                echo '<div class="lunara-carousel-thumb">' . $thumb . '</div>';
                echo '<div class="lunara-carousel-meta">';
                echo '<div class="lunara-carousel-title"><strong>' . esc_html(get_the_title($att->ID)) . '</strong></div>';
                if ($link) {
                    echo '<div class="lunara-carousel-link"><code>' . esc_html($link) . '</code></div>';
                }
                echo '<div class="lunara-carousel-actions"><a href="' . esc_url(get_edit_post_link($att->ID)) . '">Edit</a></div>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
            echo '<button type="button" class="button button-primary" id="lunara-carousel-save-order">Save Order</button> ';
            echo '<span id="lunara-carousel-save-status" style="margin-left:10px;"></span>';
        }
    }

    echo '</div>';
}

add_action('wp_ajax_lunara_save_carousel_order', function() {
    if (!current_user_can('manage_options')) {
        wp_send_json_error(array('message' => 'Permission denied.'));
    }

    check_ajax_referer('lunara_carousel_admin', 'nonce');

    $order = isset($_POST['order']) ? (array) $_POST['order'] : array();
    $order = array_values(array_filter(array_map('intval', $order)));

    if (empty($order)) {
        wp_send_json_error(array('message' => 'No order received.'));
    }

    $menu_order = 0;
    foreach ($order as $id) {
        wp_update_post(array(
            'ID' => $id,
            'menu_order' => $menu_order,
        ));
        $menu_order++;
    }

    wp_send_json_success(array(
        'message' => 'Order saved.',
        'count' => count($order),
    ));
});
}


/**
 * Shortcode: Curated Carousel
 * Usage: [lunara_carousel set="homepage"]
 * 
 * Each image can have:
 * - Title: Used as slide title
 * - Caption: Used as slide subtitle
 * - Carousel Link URL: Used as link URL (optional)
 *   (set it on the Media item; Alt Text is left for actual alt text)
 */
function lunara_carousel_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'set'   => 'homepage',
        'limit' => -1,  // -1 = unlimited
    ), $atts );

    // Enqueue carousel JS only when the shortcode is used.
    $carousel_js = lunara_resolve_theme_asset(
        'assets/js/lunara-carousel.js',
        array( 'lunara-carousel.js' )
    );
    if ( $carousel_js['path'] ) {
        wp_enqueue_script(
            'lunara-carousel',
            $carousel_js['uri'],
            array(),
            lunara_theme_asset_version( $carousel_js['path'] ),
            true
        );
    }

    $set_slug = sanitize_title( $atts['set'] );
    $limit    = (int) $atts['limit'];

    // Query slides for this set. (No object-cache here: we want updates to appear immediately after you assign images.)
    $images = get_posts( array(
        'post_type'              => 'attachment',
        'post_mime_type'         => 'image',
        'posts_per_page'         => $limit,
        'post_status'            => 'inherit',
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'tax_query'              => array(
            array(
                'taxonomy' => 'lunara_slide_set',
                'field'    => 'slug',
                'terms'    => $set_slug,
            ),
        ),
        'orderby'                => 'menu_order',
        'order'                  => 'ASC',
    ) );
    // Fallback if no images in set
    if ( empty( $images ) ) {
        return '<div class="lunara-carousel-empty" style="background:#0f1d2e;padding:100px 40px;text-align:center;color:#888;">
            <p>No images in slide set "' . esc_html( $atts['set'] ) . '"</p>
            <p style="font-size:0.9em;">Go to Media Library → Edit an image → Assign to Slide Set</p>
        </div>';
    }
    
    ob_start();
    ?>
    <div class="lunara-carousel" id="lunara-carousel-<?php echo esc_attr( $set_slug ); ?>" data-autoplay="5000">
        <?php foreach ( $images as $index => $image ) : 
            $img_url = wp_get_attachment_image_url( $image->ID, 'full' );
            $title = $image->post_title;
            $caption = wp_get_attachment_caption( $image->ID );
            $link = (string) get_post_meta( $image->ID, '_lunara_slide_link', true );
            if ( empty( $link ) ) {
                // Back-compat: if Alt Text was previously used to store a URL, accept it only when it looks like a URL.
                $alt = (string) get_post_meta( $image->ID, '_wp_attachment_image_alt', true );
                if ( $alt && preg_match( '~^https?://~i', $alt ) ) {
                    $link = $alt;
                }
            }
            $link = ( $link && filter_var( $link, FILTER_VALIDATE_URL ) ) ? $link : '';
        ?>
            <div class="lunara-carousel-slide <?php echo $index === 0 ? 'active' : ''; ?>" style="background-image: url('<?php echo esc_url( $img_url ); ?>');">
                <div class="lunara-carousel-overlay">
                    <?php if ( $link ) : ?>
                        <a href="<?php echo esc_url( $link ); ?>" class="lunara-carousel-link">
                    <?php endif; ?>
                    
                    <?php if ( $title ) : ?>
                        <h2 class="lunara-carousel-title"><?php echo esc_html( $title ); ?></h2>
                    <?php endif; ?>
                    
                    <?php if ( $caption ) : ?>
                        <p class="lunara-carousel-subtitle"><?php echo esc_html( $caption ); ?></p>
                    <?php endif; ?>
                    
                    <?php if ( $link ) : ?>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        
        <?php if ( count( $images ) > 1 ) : ?>
            <div class="lunara-carousel-dots">
                <?php foreach ( $images as $index => $image ) : ?>
                    <button class="lunara-carousel-dot <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>"></button>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'lunara_carousel', 'lunara_carousel_shortcode' );


/* ========================================
   LUNARA 2.0 - REVIEW ARCHIVES / LEDGER HIGHLIGHTS / METADATA
   ======================================== */

/**
 * Register archive taxonomies for director and review year.
 */
if ( ! defined( 'LUNARA_CORE_VERSION' ) ) {
    function lunara_register_review_taxonomies() {
        register_taxonomy( 'lunara_director', array( 'review' ), array(
            'labels' => array(
                'name'          => __( 'Directors', 'lunara-film' ),
                'singular_name' => __( 'Director', 'lunara-film' ),
            ),
            'public'       => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'rewrite'      => array( 'slug' => 'director' ),
        ) );

        register_taxonomy( 'lunara_review_year', array( 'review' ), array(
            'labels' => array(
                'name'          => __( 'Review Years', 'lunara-film' ),
                'singular_name' => __( 'Review Year', 'lunara-film' ),
            ),
            'public'       => true,
            'hierarchical' => false,
            'show_in_rest' => true,
            'rewrite'      => array( 'slug' => 'review-year' ),
        ) );
    }
    add_action( 'init', 'lunara_register_review_taxonomies', 20 );

    /**
     * Review detail meta box.
     */
    function lunara_add_review_details_meta_box() {
        add_meta_box(
            'lunara_review_details_meta',
            'Review Details',
            'lunara_review_details_meta_callback',
            'review',
            'side',
            'default'
        );
    }
    add_action( 'add_meta_boxes', 'lunara_add_review_details_meta_box' );

    function lunara_review_details_meta_callback( $post ) {
        wp_nonce_field( 'lunara_review_details_nonce', 'lunara_review_details_nonce' );
        $director = get_post_meta( $post->ID, '_lunara_director', true );
        $runtime  = get_post_meta( $post->ID, '_lunara_runtime', true );
        $studio   = get_post_meta( $post->ID, '_lunara_studio', true );
        ?>
        <p><label for="lunara_director"><strong>Director</strong></label><br>
        <input type="text" name="lunara_director" id="lunara_director" value="<?php echo esc_attr( $director ); ?>" style="width:100%;"></p>

        <p><label for="lunara_runtime"><strong>Runtime</strong></label><br>
        <input type="text" name="lunara_runtime" id="lunara_runtime" value="<?php echo esc_attr( $runtime ); ?>" placeholder="142 min" style="width:100%;"></p>

        <p><label for="lunara_studio"><strong>Studio / Distributor</strong></label><br>
        <input type="text" name="lunara_studio" id="lunara_studio" value="<?php echo esc_attr( $studio ); ?>" style="width:100%;"></p>
        <?php
    }

    function lunara_save_review_details_meta( $post_id ) {
        if ( ! isset( $_POST['lunara_review_details_nonce'] ) ) return;
        if ( ! wp_verify_nonce( $_POST['lunara_review_details_nonce'], 'lunara_review_details_nonce' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;

        foreach ( array( 'lunara_director', 'lunara_runtime', 'lunara_studio' ) as $field ) {
            if ( isset( $_POST[ $field ] ) ) {
                update_post_meta( $post_id, '_' . $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
            }
        }
    }
    add_action( 'save_post_review', 'lunara_save_review_details_meta' );

    /**
     * Keep archive taxonomies synchronized with review meta.
     */
    function lunara_sync_review_archive_terms( $post_id ) {
        if ( wp_is_post_revision( $post_id ) || 'review' !== get_post_type( $post_id ) ) {
            return;
        }

        $director = trim( (string) get_post_meta( $post_id, '_lunara_director', true ) );
        $year     = trim( (string) get_post_meta( $post_id, '_lunara_year', true ) );

        if ( $director !== '' ) {
            wp_set_object_terms( $post_id, array( $director ), 'lunara_director', false );
        }

        if ( $year !== '' ) {
            wp_set_object_terms( $post_id, array( $year ), 'lunara_review_year', false );
        }
    }
    add_action( 'save_post_review', 'lunara_sync_review_archive_terms', 30 );
}

/**
 * Helper for card excerpt.
 */
function lunara_card_excerpt( $post_id, $words = 22 ) {
    if ( has_excerpt( $post_id ) ) {
        return wp_trim_words( get_the_excerpt( $post_id ), $words );
    }
    return wp_trim_words( wp_strip_all_tags( get_post_field( 'post_content', $post_id ) ), $words );
}

/**
 * Cached home section review IDs.
 */
function lunara_cached_review_ids( $cache_group, $count, $query_args ) {
    $count = max( 1, (int) $count );
    $cache_key = sprintf( 'lunara_%s_%d_v1', sanitize_key( $cache_group ), $count );
    $post_ids = get_transient( $cache_key );

    if ( ! is_array( $post_ids ) ) {
        $query_args = wp_parse_args(
            $query_args,
            array(
                'post_type'              => 'review',
                'posts_per_page'         => $count,
                'post_status'            => 'publish',
                'ignore_sticky_posts'    => true,
                'no_found_rows'          => true,
                'fields'                 => 'ids',
                'orderby'                => 'date',
                'order'                  => 'DESC',
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,
            )
        );

        $query_args['posts_per_page'] = $count;
        $post_ids = get_posts( $query_args );
        $post_ids = array_values( array_map( 'intval', is_array( $post_ids ) ? $post_ids : array() ) );

        set_transient( $cache_key, $post_ids, 15 * MINUTE_IN_SECONDS );
    }

    return $post_ids;
}

/**
 * Prime post caches used by front-page cards before rendering.
 */
function lunara_prime_review_card_caches( $post_ids ) {
    $post_ids = array_values( array_filter( array_map( 'intval', (array) $post_ids ) ) );
    if ( empty( $post_ids ) ) {
        return;
    }

    update_meta_cache( 'post', $post_ids );
    update_object_term_cache( $post_ids, 'post' );
}

/**
 * Build a review query from cached IDs.
 */
function lunara_reviews_query_from_ids( $post_ids ) {
    $post_ids = array_values( array_filter( array_map( 'intval', (array) $post_ids ) ) );

    if ( empty( $post_ids ) ) {
        return new WP_Query(
            array(
                'post_type'      => 'review',
                'post__in'       => array( 0 ),
                'posts_per_page' => 0,
                'no_found_rows'  => true,
            )
        );
    }

    lunara_prime_review_card_caches( $post_ids );

    return new WP_Query(
        array(
            'post_type'              => 'review',
            'post__in'               => $post_ids,
            'posts_per_page'         => count( $post_ids ),
            'orderby'                => 'post__in',
            'post_status'            => 'publish',
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        )
    );
}

/**
 * Build a standard post query from a curated list of IDs.
 */
function lunara_posts_query_from_ids( $post_ids ) {
    $post_ids = array_values( array_filter( array_map( 'intval', (array) $post_ids ) ) );

    if ( empty( $post_ids ) ) {
        return new WP_Query(
            array(
                'post_type'      => 'post',
                'post__in'       => array( 0 ),
                'posts_per_page' => 0,
                'no_found_rows'  => true,
            )
        );
    }

    lunara_prime_review_card_caches( $post_ids );

    return new WP_Query(
        array(
            'post_type'              => 'post',
            'post__in'               => $post_ids,
            'posts_per_page'         => count( $post_ids ),
            'orderby'                => 'post__in',
            'post_status'            => 'publish',
            'ignore_sticky_posts'    => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => true,
            'update_post_term_cache' => true,
        )
    );
}

/**
 * Resolve the editorial label for a standard post card.
 */
function lunara_get_dispatch_type_label( $post_id ) {
    $priority_labels = array(
        'podcast'      => __( 'Podcast', 'lunara-film' ),
        'audio'        => __( 'Podcast', 'lunara-film' ),
        'news'         => __( 'News', 'lunara-film' ),
        'reaction'     => __( 'Reaction', 'lunara-film' ),
        'reactions'    => __( 'Reaction', 'lunara-film' ),
        'think-piece'  => __( 'Think Piece', 'lunara-film' ),
        'think-pieces' => __( 'Think Piece', 'lunara-film' ),
        'essay'        => __( 'Essay', 'lunara-film' ),
        'essays'       => __( 'Essay', 'lunara-film' ),
        'ink'          => __( 'Ink', 'lunara-film' ),
        'interview'    => __( 'Interview', 'lunara-film' ),
    );

    $terms = get_the_terms( $post_id, 'category' );
    if ( ! is_array( $terms ) ) {
        return __( 'Dispatch', 'lunara-film' );
    }

    foreach ( $priority_labels as $slug => $label ) {
        foreach ( $terms as $term ) {
            if ( $term instanceof WP_Term && $term->slug === $slug ) {
                return $label;
            }
        }
    }

    foreach ( $terms as $term ) {
        if ( ! ( $term instanceof WP_Term ) ) {
            continue;
        }

        if ( 'uncategorized' === $term->slug ) {
            continue;
        }

        if ( '' !== trim( (string) $term->name ) ) {
            return (string) $term->name;
        }
    }

    return __( 'Dispatch', 'lunara-film' );
}

/**
 * Resolve a stable editorial type slug for styling and layout accents.
 */
function lunara_get_dispatch_type_slug( $post_id ) {
    $priority_slugs = array(
        'podcast'      => 'podcast',
        'audio'        => 'podcast',
        'news'         => 'news',
        'reaction'     => 'reaction',
        'reactions'    => 'reaction',
        'think-piece'  => 'essay',
        'think-pieces' => 'essay',
        'essay'        => 'essay',
        'essays'       => 'essay',
        'ink'          => 'ink',
        'interview'    => 'interview',
    );

    $terms = get_the_terms( $post_id, 'category' );
    if ( ! is_array( $terms ) ) {
        return 'dispatch';
    }

    foreach ( $priority_slugs as $slug => $resolved_slug ) {
        foreach ( $terms as $term ) {
            if ( $term instanceof WP_Term && $term->slug === $slug ) {
                return $resolved_slug;
            }
        }
    }

    foreach ( $terms as $term ) {
        if ( ! ( $term instanceof WP_Term ) || 'uncategorized' === $term->slug ) {
            continue;
        }

        $fallback_slug = sanitize_title( (string) $term->slug );
        if ( '' !== $fallback_slug ) {
            return $fallback_slug;
        }
    }

    return 'dispatch';
}

/**
 * Return the editorial category slugs configured for the journal lane.
 */
function lunara_get_dispatch_category_slugs() {
    $raw_slugs = lunara_theme_mod_text( 'lunara_home_dispatch_category_slugs', 'news,think-pieces,reactions,podcast' );
    return array_values( array_filter( array_map( 'sanitize_title', array_map( 'trim', explode( ',', $raw_slugs ) ) ) ) );
}

/**
 * Determine whether a category term belongs to the editorial dispatch lane.
 */
function lunara_is_editorial_category_term( $term ) {
    if ( ! ( $term instanceof WP_Term ) || 'category' !== $term->taxonomy ) {
        return false;
    }

    return in_array( $term->slug, lunara_get_dispatch_category_slugs(), true );
}

/**
 * Resolve the fallback archive URL for the homepage dispatches section.
 */
function lunara_home_dispatch_archive_url() {
    $custom_url = lunara_theme_mod_url( 'lunara_home_dispatch_button_url', '' );
    if ( '' !== $custom_url ) {
        return $custom_url;
    }

    $slugs = lunara_get_dispatch_category_slugs();

    foreach ( $slugs as $slug ) {
        $term = get_category_by_slug( $slug );
        if ( $term instanceof WP_Term && intval( $term->count ) > 0 ) {
            $term_link = get_term_link( $term );
            if ( ! is_wp_error( $term_link ) ) {
                return $term_link;
            }
        }
    }

    $posts_page_id = absint( get_option( 'page_for_posts' ) );
    if ( $posts_page_id > 0 ) {
        $posts_page_url = get_permalink( $posts_page_id );
        if ( is_string( $posts_page_url ) && '' !== $posts_page_url ) {
            return $posts_page_url;
        }
    }

    foreach ( array( 'news', 'journal', 'blog' ) as $path ) {
        $page = get_page_by_path( $path );
        if ( $page instanceof WP_Post ) {
            $page_url = get_permalink( $page );
            if ( is_string( $page_url ) && '' !== $page_url ) {
                return $page_url;
            }
        }
    }

    $latest_post = get_posts(
        array(
            'post_type'              => 'post',
            'post_status'            => 'publish',
            'posts_per_page'         => 1,
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'ignore_sticky_posts'    => true,
            'fields'                 => 'ids',
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        )
    );

    if ( ! empty( $latest_post[0] ) ) {
        return get_permalink( intval( $latest_post[0] ) );
    }

    return home_url( '/news/' );
}

/**
 * Build the year/director line used on review cards.
 */
function lunara_get_review_card_meta( $post_id ) {
    $post_id  = intval( $post_id );
    $year     = trim( (string) get_post_meta( $post_id, '_lunara_year', true ) );
    $director = trim( (string) get_post_meta( $post_id, '_lunara_director', true ) );
    $parts    = array();

    if ( '' !== $year ) {
        $parts[] = $year;
    }

    if ( '' !== $director ) {
        $parts[] = $director;
    }

    return implode( ' / ', $parts );
}

/**
 * Provide one uniform teaser line for review cards.
 */
function lunara_get_review_card_teaser() {
    return __( 'Open the review and enter the full argument.', 'lunara-film' );
}

/**
 * Build a stable Oscars title URL from an IMDb title id.
 */
if ( ! function_exists( 'lunara_get_oscars_title_url' ) ) {
    function lunara_get_oscars_title_url( $imdb_title_id ) {
        $imdb_title_id = strtolower( trim( (string) $imdb_title_id ) );
        if ( ! preg_match( '/^tt\d{7,8}$/', $imdb_title_id ) ) {
            return '';
        }

        return home_url( '/oscars/title/' . rawurlencode( $imdb_title_id ) . '/' );
    }
}

/**
 * Render the filtered review content so it can be re-sectioned inside the template.
 */
if ( ! function_exists( 'lunara_get_review_rendered_content' ) ) {
    function lunara_get_review_rendered_content( $post_id ) {
        $post_id = intval( $post_id );
        if ( $post_id <= 0 ) {
            return '';
        }

        $content = get_post_field( 'post_content', $post_id );
        if ( ! is_string( $content ) || '' === trim( $content ) ) {
            return '';
        }

        return (string) apply_filters( 'the_content', $content );
    }
}

/**
 * Add Lunara-owned links and Oscar pills to paired-film lines inside the debrief.
 */
if ( ! function_exists( 'lunara_enhance_review_debrief_html' ) ) {
    function lunara_enhance_review_debrief_html( $html ) {
        $html = trim( (string) $html );
        if ( '' === $html ) {
            return '';
        }

        return preg_replace_callback(
            '~(<strong>[^<]+:</strong>\s*)([^<]+?)\s*\|\s*IMDB:\s*(tt\d{7,8})(?:\s*(?:—|-)\s*([^<]+)|\s*<em>\s*(?:—|-)\s*([^<]+)\s*</em>)?~iu',
            static function( $matches ) {
                $label = $matches[1];
                $title = trim( wp_strip_all_tags( $matches[2] ) );
                $tt_id = strtolower( trim( $matches[3] ) );
                $note  = isset( $matches[4] ) && '' !== trim( (string) $matches[4] )
                    ? trim( wp_strip_all_tags( $matches[4] ) )
                    : ( isset( $matches[5] ) ? trim( wp_strip_all_tags( $matches[5] ) ) : '' );

                if ( '' !== $note ) {
                    $note = preg_replace( '/^\s*[—-]\s*/u', '', $note );
                }

                $letterbox_url = 'https://letterboxd.com/search/' . rawurlencode( $title ) . '/';
                $title_html    = sprintf(
                    '<a class="lunara-pair-title-link" href="%s" target="_blank" rel="noopener noreferrer nofollow"><em>%s</em></a>',
                    esc_url( $letterbox_url ),
                    esc_html( $title )
                );

                $imdb_chip = sprintf(
                    '<a class="lunara-debrief-chip lunara-debrief-chip-imdb" href="%s" target="_blank" rel="noopener noreferrer nofollow">IMDb</a>',
                    esc_url( 'https://www.imdb.com/title/' . rawurlencode( $tt_id ) . '/' )
                );

                $pill = '';
                if ( function_exists( 'lunara_get_oscar_ledger_counts' ) && function_exists( 'lunara_render_oscar_ledger_pill' ) ) {
                    $pill = lunara_render_oscar_ledger_pill( $tt_id, lunara_get_oscar_ledger_counts( $tt_id ) );
                }

                $poster_html = '';
                if ( class_exists( 'Academy_Awards_Table' ) ) {
                    $aat = Academy_Awards_Table::get_instance();
                    if ( $aat && method_exists( $aat, 'get_poster_img_html_for_title' ) ) {
                        $poster_html = (string) $aat->get_poster_img_html_for_title(
                            $tt_id,
                            'medium',
                            array(
                                'class'    => 'lunara-debrief-thumb',
                                'loading'  => 'lazy',
                                'decoding' => 'async',
                            )
                        );
                    }
                }

                $line_1    = '<span class="lunara-debrief-line1">' . $title_html . ' ' . $imdb_chip . ( '' !== $pill ? ' ' . $pill : '' ) . '</span>';
                $line_2    = '' !== $note ? '<span class="lunara-debrief-note">' . esc_html( $note ) . '</span>' : '';
                $text_html = '<span class="lunara-debrief-pairing-text">' . $line_1 . $line_2 . '</span>';

                if ( '' === $poster_html ) {
                    return $label . $text_html;
                }

                return $label
                    . '<span class="lunara-debrief-pairing">'
                    . '<span class="lunara-debrief-thumb-wrap">' . $poster_html . '</span>'
                    . $text_html
                    . '</span>';
            },
            $html
        );
    }
}

/**
 * Split a rendered review into the main essay, the Lunara Debrief, and any postscript/share blocks.
 */
if ( ! function_exists( 'lunara_extract_review_content_sections' ) ) {
    function lunara_extract_review_content_sections( $content_html ) {
        $content_html = trim( (string) $content_html );

        $sections = array(
            'body'     => $content_html,
            'debrief'  => '',
            'postscript' => '',
        );

        if ( '' === $content_html ) {
            return $sections;
        }

        $marker_pattern = '~<p>\s*(?:<strong>)?\s*LUNARA\s+DEBRIEF\s*(?:</strong>)?\s*</p>~i';

        if ( ! preg_match( $marker_pattern, $content_html, $marker_match, PREG_OFFSET_CAPTURE ) ) {
            return $sections;
        }

        $start_offset = intval( $marker_match[0][1] );
        $before       = trim( substr( $content_html, 0, $start_offset ) );
        $tail         = substr( $content_html, $start_offset );
        $debrief_end  = null;

        if ( preg_match( '~<p>\s*<strong>\s*Pair\s+It\s+With\s*</strong>\s*</p>.*?(</ul>)~is', $tail, $pair_match, PREG_OFFSET_CAPTURE ) ) {
            $debrief_end = intval( $pair_match[1][1] ) + strlen( $pair_match[1][0] );
        } elseif ( preg_match( '~</ul>~i', $tail, $list_end_match, PREG_OFFSET_CAPTURE ) ) {
            $debrief_end = intval( $list_end_match[0][1] ) + strlen( $list_end_match[0][0] );
        }

        if ( null === $debrief_end ) {
            return $sections;
        }

        $debrief_html   = trim( substr( $tail, 0, $debrief_end ) );
        $postscript_html = trim( substr( $tail, $debrief_end ) );

        $debrief_html = preg_replace( $marker_pattern, '', $debrief_html, 1 );
        $debrief_html = lunara_enhance_review_debrief_html( $debrief_html );

        $sections['body']       = '' !== $before ? $before : $content_html;
        $sections['debrief']    = trim( (string) $debrief_html );
        $sections['postscript'] = trim( (string) $postscript_html );

        return $sections;
    }
}

/**
 * Build a longer excerpt for hero/feature review cards.
 */
if ( ! function_exists( 'lunara_get_review_archive_excerpt' ) ) {
function lunara_get_review_archive_excerpt( $post_id, $words = 28 ) {
    $post_id = intval( $post_id );
    $words   = max( 12, intval( $words ) );

    if ( $post_id <= 0 ) {
        return '';
    }

    if ( function_exists( 'lunara_get_review_card_teaser' ) ) {
        return lunara_get_review_card_teaser();
    }

    return __( 'Open the review and enter the full argument.', 'lunara-film' );
}
}

/**
 * Render a lead or supporting review card for the archive shell.
 */
if ( ! function_exists( 'lunara_render_review_feature_card' ) ) {
    function lunara_render_review_feature_card( $post_id, $args = array() ) {
        $post_id = intval( $post_id );
        if ( $post_id <= 0 ) {
            return '';
        }

        $args = wp_parse_args(
            $args,
            array(
                'variant' => 'lead',
                'excerpt_words' => 30,
            )
        );

        $variant      = 'compact' === $args['variant'] ? 'compact' : 'lead';
        $score        = trim( (string) get_post_meta( $post_id, '_lunara_score', true ) );
        $meta         = lunara_get_review_card_meta( $post_id );
        $excerpt      = lunara_get_review_archive_excerpt( $post_id, intval( $args['excerpt_words'] ) );
        $review_tt    = function_exists( 'lunara_get_review_imdb_title_id' ) ? lunara_get_review_imdb_title_id( $post_id ) : '';
        $ledger_pill  = '';

        if ( '' !== $review_tt && function_exists( 'lunara_get_oscar_ledger_counts' ) && function_exists( 'lunara_render_oscar_ledger_pill' ) ) {
            $ledger_pill = lunara_render_oscar_ledger_pill( $review_tt, lunara_get_oscar_ledger_counts( $review_tt ) );
        }

        ob_start();
        ?>
        <article class="lunara-review-feature-card is-<?php echo esc_attr( $variant ); ?>">
            <a class="lunara-review-feature-link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
                <div class="lunara-review-feature-media">
                    <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                        <?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'lunara-review-feature-image', 'loading' => 'lazy' ) ); ?>
                    <?php else : ?>
                        <div class="lunara-review-feature-placeholder"><?php echo esc_html( get_the_title( $post_id ) ); ?></div>
                    <?php endif; ?>
                    <?php if ( '' !== $score ) : ?>
                        <span class="lunara-score-badge"><?php echo wp_kses_post( lunara_render_stars( $score ) ); ?></span>
                    <?php endif; ?>
                </div>
                <div class="lunara-review-feature-copy">
                    <p class="lunara-home-section-kicker"><?php esc_html_e( 'Lunara Review', 'lunara-film' ); ?></p>
                    <h2 class="lunara-review-feature-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
                    <?php if ( '' !== $meta ) : ?>
                        <p class="lunara-review-feature-meta"><?php echo esc_html( $meta ); ?></p>
                    <?php endif; ?>
                    <?php if ( '' !== $excerpt ) : ?>
                        <p class="lunara-review-feature-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <?php endif; ?>
                    <div class="lunara-review-feature-footer">
                        <?php if ( '' !== $ledger_pill ) : ?>
                            <div class="lunara-review-feature-ledger"><?php echo wp_kses_post( $ledger_pill ); ?></div>
                        <?php endif; ?>
                        <span class="lunara-section-link"><?php esc_html_e( 'Read Review', 'lunara-film' ); ?></span>
                    </div>
                </div>
            </a>
        </article>
        <?php

        return ob_get_clean();
    }
}

/**
 * Normalize the IMDb title id attached to a review.
 */
if ( ! function_exists( 'lunara_get_review_imdb_title_id' ) ) {
    function lunara_get_review_imdb_title_id( $post_id ) {
        $post_id = intval( $post_id );
        if ( $post_id <= 0 ) {
            return '';
        }

        $raw = trim( (string) get_post_meta( $post_id, '_lunara_imdb_title_id', true ) );
        if ( '' === $raw ) {
            return '';
        }

        if ( preg_match( '/\btt\d{7,8}\b/i', $raw, $matches ) ) {
            return strtolower( $matches[0] );
        }

        if ( preg_match( '#imdb\.com/title/(tt\d{7,8})#i', $raw, $matches ) ) {
            return strtolower( $matches[1] );
        }

        return '';
    }
}

/**
 * Query related reviews using director/year affinity first, then recent fallback.
 */
if ( ! function_exists( 'lunara_get_related_review_posts' ) ) {
    function lunara_get_related_review_posts( $post_id, $count = 4 ) {
        $post_id = intval( $post_id );
        $count   = max( 1, intval( $count ) );

        if ( $post_id <= 0 ) {
            return lunara_reviews_query_from_ids( array() );
        }

        $director = trim( (string) get_post_meta( $post_id, '_lunara_director', true ) );
        $year     = trim( (string) get_post_meta( $post_id, '_lunara_year', true ) );
        $ids      = array();

        $collect_ids = static function( $query_args ) use ( &$ids, $count, $post_id ) {
            if ( count( $ids ) >= $count ) {
                return;
            }

            $query_args = wp_parse_args(
                $query_args,
                array(
                    'post_type'              => 'review',
                    'post_status'            => 'publish',
                    'posts_per_page'         => max( 1, $count - count( $ids ) ),
                    'post__not_in'           => array_merge( array( $post_id ), $ids ),
                    'ignore_sticky_posts'    => true,
                    'fields'                 => 'ids',
                    'orderby'                => 'date',
                    'order'                  => 'DESC',
                    'no_found_rows'          => true,
                    'update_post_meta_cache' => false,
                    'update_post_term_cache' => false,
                )
            );

            $found_ids = get_posts( $query_args );
            foreach ( array_map( 'intval', is_array( $found_ids ) ? $found_ids : array() ) as $found_id ) {
                if ( $found_id > 0 && ! in_array( $found_id, $ids, true ) && $found_id !== $post_id ) {
                    $ids[] = $found_id;
                    if ( count( $ids ) >= $count ) {
                        break;
                    }
                }
            }
        };

        if ( '' !== $director ) {
            $collect_ids(
                array(
                    'meta_query' => array(
                        array(
                            'key'     => '_lunara_director',
                            'value'   => $director,
                            'compare' => '=',
                        ),
                    ),
                )
            );
        }

        if ( count( $ids ) < $count && '' !== $year ) {
            $collect_ids(
                array(
                    'meta_query' => array(
                        array(
                            'key'     => '_lunara_year',
                            'value'   => $year,
                            'compare' => '=',
                        ),
                    ),
                )
            );
        }

        if ( count( $ids ) < $count ) {
            $collect_ids( array() );
        }

        return lunara_reviews_query_from_ids( array_slice( $ids, 0, $count ) );
    }
}

/**
 * Render a review archive card.
 */
function lunara_render_review_grid_card( $post_id ) {
    $post_id = intval( $post_id );
    if ( $post_id <= 0 ) {
        return '';
    }

    $score       = get_post_meta( $post_id, '_lunara_score', true );
    $meta        = lunara_get_review_card_meta( $post_id );
    $teaser      = lunara_get_review_card_teaser();
    $review_tt   = function_exists( 'lunara_get_review_imdb_title_id' ) ? lunara_get_review_imdb_title_id( $post_id ) : '';
    $ledger_pill = '';

    if ( '' !== $review_tt && function_exists( 'lunara_get_oscar_ledger_counts' ) && function_exists( 'lunara_render_oscar_ledger_pill' ) ) {
        $ledger_pill = lunara_render_oscar_ledger_pill( $review_tt, lunara_get_oscar_ledger_counts( $review_tt ) );
    }

    ob_start();
    ?>
    <article class="lunara-review-grid-card lunara-review-archive-card">
        <a class="lunara-review-grid-link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
            <div class="lunara-review-grid-poster-wrap">
                <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                    <?php echo get_the_post_thumbnail( $post_id, 'medium_large', array( 'class' => 'lunara-review-grid-poster', 'loading' => 'lazy' ) ); ?>
                <?php else : ?>
                    <div class="lunara-review-grid-poster-placeholder"><?php echo esc_html( get_the_title( $post_id ) ); ?></div>
                <?php endif; ?>
                <?php if ( $score ) : ?>
                    <span class="lunara-score-badge"><?php echo wp_kses_post( lunara_render_stars( $score ) ); ?></span>
                <?php endif; ?>
            </div>
            <div class="lunara-review-grid-copy">
                <p class="lunara-review-grid-kicker"><?php esc_html_e( 'Lunara Review', 'lunara-film' ); ?></p>
                <h3 class="lunara-review-grid-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
                <?php if ( '' !== $meta ) : ?>
                    <p class="lunara-review-grid-meta"><?php echo esc_html( $meta ); ?></p>
                <?php endif; ?>
                <?php if ( '' !== trim( $teaser ) ) : ?>
                    <p class="lunara-review-grid-excerpt"><?php echo esc_html( $teaser ); ?></p>
                <?php endif; ?>
                <?php if ( '' !== $ledger_pill ) : ?>
                    <div class="lunara-review-grid-footer">
                        <div class="lunara-review-grid-ledger"><?php echo wp_kses_post( $ledger_pill ); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </a>
    </article>
    <?php

    return ob_get_clean();
}

/**
 * Build a short taxonomy line for standard post cards.
 */
function lunara_get_dispatch_category_line( $post_id ) {
    $terms = get_the_terms( $post_id, 'category' );
    if ( ! is_array( $terms ) ) {
        return '';
    }

    $labels = array();

    foreach ( $terms as $term ) {
        if ( ! ( $term instanceof WP_Term ) || 'uncategorized' === $term->slug ) {
            continue;
        }

        $labels[] = trim( (string) $term->name );
    }

    $labels = array_values( array_filter( array_unique( $labels ) ) );

    return implode( ' / ', array_slice( $labels, 0, 2 ) );
}

/**
 * Estimate reading time for a standard editorial post.
 */
function lunara_get_post_reading_time( $post_id ) {
    $post_id = intval( $post_id );
    if ( $post_id <= 0 ) {
        return '';
    }

    $content    = wp_strip_all_tags( (string) get_post_field( 'post_content', $post_id ) );
    $word_count = str_word_count( $content );
    if ( $word_count <= 0 ) {
        return '';
    }

    $minutes = max( 1, (int) ceil( $word_count / 225 ) );

    return sprintf(
        /* translators: %d: Reading time in minutes. */
        _n( '%d min read', '%d mins read', $minutes, 'lunara-film' ),
        $minutes
    );
}

/**
 * Build a compact label list for a post's tags.
 */
function lunara_get_post_tag_line( $post_id, $limit = 4 ) {
    $terms = get_the_terms( $post_id, 'post_tag' );
    if ( ! is_array( $terms ) ) {
        return '';
    }

    $labels = array();

    foreach ( $terms as $term ) {
        if ( ! ( $term instanceof WP_Term ) ) {
            continue;
        }

        $labels[] = trim( (string) $term->name );
    }

    $labels = array_values( array_filter( array_unique( $labels ) ) );

    return implode( ' / ', array_slice( $labels, 0, max( 1, intval( $limit ) ) ) );
}

/**
 * Query related editorial posts by shared categories.
 */
function lunara_get_related_dispatch_posts( $post_id, $count = 3 ) {
    $post_id = intval( $post_id );
    $count   = max( 1, intval( $count ) );
    $cat_ids = wp_get_post_categories( $post_id, array( 'fields' => 'ids' ) );

    $query_args = array(
        'post_type'              => 'post',
        'post_status'            => 'publish',
        'posts_per_page'         => $count,
        'post__not_in'           => array( $post_id ),
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
        'orderby'                => 'date',
        'order'                  => 'DESC',
    );

    if ( ! empty( $cat_ids ) ) {
        $query_args['category__in'] = array_map( 'intval', $cat_ids );
    } else {
        $dispatch_slugs = lunara_get_dispatch_category_slugs();
        if ( ! empty( $dispatch_slugs ) ) {
            $query_args['tax_query'] = array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => $dispatch_slugs,
                    'operator' => 'IN',
                ),
            );
        }
    }

    return new WP_Query( $query_args );
}

/**
 * Render a standard post card for the editorial archive.
 */
function lunara_render_dispatch_archive_card( $post_id, $featured = false ) {
    $post_id        = intval( $post_id );
    $featured       = (bool) $featured;
    $type_label     = lunara_get_dispatch_type_label( $post_id );
    $type_slug      = lunara_get_dispatch_type_slug( $post_id );
    $category_line  = lunara_get_dispatch_category_line( $post_id );
    $excerpt_length = $featured ? 40 : 22;
    $excerpt        = function_exists( 'lunara_card_excerpt' )
        ? lunara_card_excerpt( $post_id, $excerpt_length )
        : wp_trim_words( get_the_excerpt( $post_id ), $excerpt_length );

    ob_start();

    if ( $featured ) :
        ?>
        <article class="<?php echo esc_attr( 'lunara-dispatch-lead lunara-archive-lead-card lunara-dispatch-type-card is-' . sanitize_html_class( $type_slug ) ); ?>">
            <a class="lunara-dispatch-lead-link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
                <div class="lunara-dispatch-lead-media">
                    <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                        <?php echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'lunara-dispatch-lead-image', 'loading' => 'lazy' ) ); ?>
                    <?php else : ?>
                        <div class="lunara-dispatch-lead-placeholder"><?php echo esc_html( $type_label ); ?></div>
                    <?php endif; ?>
                </div>
                <div class="lunara-dispatch-lead-copy">
                    <p class="lunara-dispatch-type"><?php echo esc_html( $type_label ); ?></p>
                    <h2 class="lunara-dispatch-lead-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h2>
                    <?php if ( '' !== $excerpt ) : ?>
                        <p class="lunara-dispatch-lead-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <?php endif; ?>
                    <div class="lunara-dispatch-lead-meta">
                        <span><?php echo esc_html( get_the_date( 'F j, Y', $post_id ) ); ?></span>
                        <?php if ( '' !== $category_line ) : ?>
                            <span><?php echo esc_html( $category_line ); ?></span>
                        <?php endif; ?>
                        <span class="lunara-dispatch-meta-link"><?php esc_html_e( 'Read or listen', 'lunara-film' ); ?></span>
                    </div>
                </div>
            </a>
        </article>
        <?php
    else :
        ?>
        <article class="<?php echo esc_attr( 'lunara-dispatch-archive-card lunara-dispatch-type-card is-' . sanitize_html_class( $type_slug ) ); ?>">
            <a class="lunara-dispatch-archive-link" href="<?php echo esc_url( get_permalink( $post_id ) ); ?>">
                <div class="lunara-dispatch-archive-thumb-wrap">
                    <?php if ( has_post_thumbnail( $post_id ) ) : ?>
                        <?php echo get_the_post_thumbnail( $post_id, 'medium_large', array( 'class' => 'lunara-dispatch-archive-thumb', 'loading' => 'lazy' ) ); ?>
                    <?php else : ?>
                        <div class="lunara-dispatch-rail-thumb-placeholder"><?php echo esc_html( $type_label ); ?></div>
                    <?php endif; ?>
                </div>
                <div class="lunara-dispatch-archive-copy">
                    <p class="lunara-dispatch-type"><?php echo esc_html( $type_label ); ?></p>
                    <h3 class="lunara-dispatch-archive-title"><?php echo esc_html( get_the_title( $post_id ) ); ?></h3>
                    <?php if ( '' !== $excerpt ) : ?>
                        <p class="lunara-dispatch-archive-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                    <?php endif; ?>
                    <p class="lunara-dispatch-archive-meta">
                        <span><?php echo esc_html( get_the_date( 'F j, Y', $post_id ) ); ?></span>
                        <?php if ( '' !== $category_line ) : ?>
                            <span><?php echo esc_html( $category_line ); ?></span>
                        <?php endif; ?>
                    </p>
                </div>
            </a>
        </article>
        <?php
    endif;

    return ob_get_clean();
}

/**
 * Normalize posts pulled from either the global loop or an explicit query.
 */
if ( ! function_exists( 'lunara_get_loop_posts' ) ) {
    function lunara_get_loop_posts( $query = null ) {
        if ( $query instanceof WP_Query ) {
            return is_array( $query->posts ) ? $query->posts : array();
        }

        global $wp_query;

        return ( isset( $wp_query ) && $wp_query instanceof WP_Query && is_array( $wp_query->posts ) )
            ? $wp_query->posts
            : array();
    }
}

/**
 * Shared editorial archive shell used by the posts index and editorial tax archives.
 */
if ( ! function_exists( 'lunara_render_editorial_archive_shell' ) ) {
    function lunara_render_editorial_archive_shell( $args = array() ) {
        $defaults = array(
            'classes'           => 'lunara-editorial-archive-page',
            'kicker'            => __( 'Archive', 'lunara-film' ),
            'title'             => __( 'Archive', 'lunara-film' ),
            'copy'              => '',
            'posts'             => array(),
            'empty_title'       => __( 'Nothing has been filed in this archive yet.', 'lunara-film' ),
            'empty_copy'        => '',
            'copy_words'        => 42,
            'pagination'        => paginate_links(),
            'overview_kicker'   => __( 'At A Glance', 'lunara-film' ),
            'overview_label'    => __( 'Coverage Focus', 'lunara-film' ),
            'source_label'      => __( 'Editorial lane', 'lunara-film' ),
            'overview_lines'    => array(),
            'lead_rail_kicker'  => __( 'In Rotation', 'lunara-film' ),
            'lead_rail_title'   => __( 'What The Archive Is Holding Beside The Lead', 'lunara-film' ),
            'lead_rail_copy'    => __( 'A tighter supporting stack so this page reads like a live Lunara lane instead of a generic archive.', 'lunara-film' ),
            'run_kicker'        => __( 'Archive Run', 'lunara-film' ),
            'run_title'         => __( 'More From The Archive', 'lunara-film' ),
            'run_copy'          => __( 'The broader run stays browseable and poster-led, but now lives inside the same deliberate editorial grammar as the rest of Lunara.', 'lunara-film' ),
            'empty_note_kicker' => __( 'What Lives Here', 'lunara-film' ),
            'empty_note_title'  => __( 'Dispatches, reactions, essays, and signal worth following.', 'lunara-film' ),
            'empty_note_copy'   => __( 'This lane is for the part of Lunara that moves with the moment: news, reactions, interviews, longer arguments, and the pieces that keep the publication alive between the review tentpoles.', 'lunara-film' ),
        );
        $args = wp_parse_args( $args, $defaults );

        $posts          = array_values( array_filter( (array) $args['posts'], static function ( $post_item ) {
            return $post_item instanceof WP_Post;
        } ) );
        $copy            = trim( wp_strip_all_tags( (string) $args['copy'] ) );
        $classes         = trim( 'site-main lunara-archive-page ' . (string) $args['classes'] );
        $lead_post       = ! empty( $posts ) ? array_shift( $posts ) : null;
        $support_posts   = array_slice( $posts, 0, 2 );
        $remaining_posts = array_slice( $posts, 2 );
        $visible_count   = count( $posts ) + ( $lead_post instanceof WP_Post ? 1 : 0 );
        $archive_mode    = $lead_post instanceof WP_Post
            ? ( ! empty( $remaining_posts ) ? __( 'Spotlight / Supporting / Archive Run', 'lunara-film' ) : __( 'Spotlight / Supporting', 'lunara-film' ) )
            : __( 'Standby', 'lunara-film' );
        $total_count     = 0;

        global $wp_query;

        if ( isset( $wp_query ) && $wp_query instanceof WP_Query ) {
            $total_count = intval( $wp_query->found_posts );
        }

        if ( $total_count <= 0 ) {
            $total_count = $visible_count;
        }

        $overview_lines = array_values( array_filter( (array) $args['overview_lines'], static function ( $line ) {
            return is_array( $line ) && ! empty( $line['label'] ) && isset( $line['value'] );
        } ) );

        if ( empty( $overview_lines ) ) {
            $overview_lines = array(
                array(
                    'label' => __( 'Total Filed', 'lunara-film' ),
                    'value' => number_format_i18n( $total_count ),
                ),
                array(
                    'label' => __( 'Visible Now', 'lunara-film' ),
                    'value' => number_format_i18n( $visible_count ),
                ),
                array(
                    'label' => __( 'Page Shape', 'lunara-film' ),
                    'value' => $archive_mode,
                ),
                array(
                    'label' => (string) $args['overview_label'],
                    'value' => (string) $args['source_label'],
                ),
            );
        }

        ob_start();
        ?>
        <main id="primary" class="<?php echo esc_attr( $classes ); ?>">
            <section class="lunara-home-section lunara-archive-hero">
                <div class="lunara-editorial-archive-hero-shell">
                    <div class="lunara-editorial-archive-hero-copy-wrap">
                        <p class="lunara-archive-hero-kicker"><?php echo esc_html( $args['kicker'] ); ?></p>
                        <h1 class="lunara-archive-hero-title"><?php echo esc_html( $args['title'] ); ?></h1>
                        <?php if ( '' !== $copy ) : ?>
                            <p class="lunara-archive-hero-copy"><?php echo esc_html( wp_trim_words( $copy, max( 12, intval( $args['copy_words'] ) ) ) ); ?></p>
                        <?php endif; ?>
                    </div>
                    <aside class="lunara-editorial-archive-debrief" aria-label="<?php esc_attr_e( 'Editorial archive summary', 'lunara-film' ); ?>">
                        <p class="lunara-editorial-archive-debrief-kicker"><?php echo esc_html( $args['overview_kicker'] ); ?></p>
                        <ul class="lunara-editorial-archive-debrief-list">
                            <?php foreach ( $overview_lines as $line ) : ?>
                                <li>
                                    <strong><?php echo esc_html( (string) $line['label'] ); ?></strong>
                                    <span><?php echo esc_html( (string) $line['value'] ); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </aside>
                </div>
            </section>

            <section class="lunara-home-section lunara-editorial-archive-shell">
                <?php if ( $lead_post instanceof WP_Post ) : ?>
                    <div class="lunara-editorial-archive-spotlight">
                        <?php echo lunara_render_dispatch_archive_card( $lead_post->ID, true ); ?>

                        <?php if ( ! empty( $support_posts ) ) : ?>
                            <div class="lunara-editorial-archive-rail">
                                <div class="lunara-editorial-archive-rail-shell">
                                    <p class="lunara-home-section-kicker"><?php echo esc_html( $args['lead_rail_kicker'] ); ?></p>
                                    <h2 class="lunara-section-title"><?php echo esc_html( $args['lead_rail_title'] ); ?></h2>
                                    <p class="lunara-editorial-archive-rail-copy"><?php echo esc_html( $args['lead_rail_copy'] ); ?></p>
                                </div>
                                <?php foreach ( $support_posts as $post_item ) : ?>
                                    <?php echo lunara_render_dispatch_archive_card( $post_item->ID ); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $remaining_posts ) ) : ?>
                        <div class="lunara-home-section-head lunara-editorial-archive-run-head">
                            <div>
                                <p class="lunara-home-section-kicker"><?php echo esc_html( $args['run_kicker'] ); ?></p>
                                <h2 class="lunara-section-title"><?php echo esc_html( $args['run_title'] ); ?></h2>
                                <p class="lunara-editorial-archive-run-copy"><?php echo esc_html( $args['run_copy'] ); ?></p>
                            </div>
                        </div>

                        <div class="lunara-dispatch-archive-grid lunara-editorial-archive-grid">
                            <?php foreach ( $remaining_posts as $post_item ) : ?>
                                <?php echo lunara_render_dispatch_archive_card( $post_item->ID ); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $args['pagination'] ) ) : ?>
                        <div class="lunara-archive-pagination">
                            <?php echo wp_kses_post( $args['pagination'] ); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="lunara-editorial-archive-empty-shell">
                        <div class="lunara-archive-empty lunara-editorial-archive-empty">
                            <h2><?php echo esc_html( $args['empty_title'] ); ?></h2>
                            <?php if ( '' !== trim( (string) $args['empty_copy'] ) ) : ?>
                                <p><?php echo esc_html( $args['empty_copy'] ); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="lunara-editorial-archive-empty-note">
                            <p class="lunara-home-section-kicker"><?php echo esc_html( $args['empty_note_kicker'] ); ?></p>
                            <h2 class="lunara-section-title"><?php echo esc_html( $args['empty_note_title'] ); ?></h2>
                            <p class="lunara-editorial-archive-empty-copy"><?php echo esc_html( $args['empty_note_copy'] ); ?></p>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </main>
        <?php

        return ob_get_clean();
    }
}

/**
 * Shared review archive shell used by review and director archives.
 */
if ( ! function_exists( 'lunara_render_review_archive_shell' ) ) {
    function lunara_render_review_archive_shell( $args = array() ) {
        $defaults = array(
            'classes'      => 'lunara-review-archive-page',
            'kicker'       => __( 'Review Archive', 'lunara-film' ),
            'title'        => __( 'The Review Archive', 'lunara-film' ),
            'copy'         => '',
            'posts'        => array(),
            'empty_title'  => __( 'No reviews yet.', 'lunara-film' ),
            'empty_copy'   => __( 'When new criticism is published, it will appear here automatically.', 'lunara-film' ),
            'copy_words'   => 42,
            'pagination'   => paginate_links(),
        );
        $args = wp_parse_args( $args, $defaults );

        $posts = array_values( array_filter( (array) $args['posts'], static function ( $post_item ) {
            return $post_item instanceof WP_Post;
        } ) );
        $copy          = trim( wp_strip_all_tags( (string) $args['copy'] ) );
        $classes       = trim( 'site-main lunara-archive-page ' . (string) $args['classes'] );
        $total_reviews = wp_count_posts( 'review' );
        $total_reviews = isset( $total_reviews->publish ) ? intval( $total_reviews->publish ) : 0;
        $visible_count = count( $posts );

        ob_start();
        ?>
        <main id="primary" class="<?php echo esc_attr( $classes ); ?>">
            <section class="lunara-home-section lunara-archive-hero lunara-review-archive-hero">
                <div class="lunara-review-archive-hero-shell">
                    <div class="lunara-review-archive-hero-copy-wrap">
                        <p class="lunara-archive-hero-kicker"><?php echo esc_html( $args['kicker'] ); ?></p>
                        <h1 class="lunara-archive-hero-title"><?php echo esc_html( $args['title'] ); ?></h1>
                        <?php if ( '' !== $copy ) : ?>
                            <p class="lunara-archive-hero-copy"><?php echo esc_html( wp_trim_words( $copy, max( 12, intval( $args['copy_words'] ) ) ) ); ?></p>
                        <?php endif; ?>
                    </div>
                    <aside class="lunara-review-archive-debrief" aria-label="<?php esc_attr_e( 'Review archive summary', 'lunara-film' ); ?>">
                        <p class="lunara-review-archive-debrief-kicker"><?php esc_html_e( 'At A Glance', 'lunara-film' ); ?></p>
                        <ul class="lunara-review-archive-debrief-list">
                            <li>
                                <strong><?php esc_html_e( 'Total Reviews', 'lunara-film' ); ?></strong>
                                <span><?php echo esc_html( number_format_i18n( $total_reviews ) ); ?></span>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Visible Now', 'lunara-film' ); ?></strong>
                                <span><?php echo esc_html( number_format_i18n( $visible_count ) ); ?></span>
                            </li>
                        </ul>
                    </aside>
                </div>
            </section>

            <section class="lunara-home-section lunara-review-archive-shell">
                <?php if ( ! empty( $posts ) ) : ?>
                    <div class="lunara-review-grid lunara-review-archive-grid lunara-review-archive-uniform">
                        <?php foreach ( $posts as $review_post ) : ?>
                            <?php echo lunara_render_review_grid_card( $review_post->ID ); ?>
                        <?php endforeach; ?>
                    </div>

                    <?php if ( ! empty( $args['pagination'] ) ) : ?>
                        <div class="lunara-archive-pagination">
                            <?php echo wp_kses_post( $args['pagination'] ); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="lunara-archive-empty">
                        <h2><?php echo esc_html( $args['empty_title'] ); ?></h2>
                        <?php if ( '' !== trim( (string) $args['empty_copy'] ) ) : ?>
                            <p><?php echo esc_html( $args['empty_copy'] ); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </section>
        </main>
        <?php

        return ob_get_clean();
    }
}

/**
 * Dedicated news archive shell so the /news/ route feels authored, not generic.
 */
if ( ! function_exists( 'lunara_render_news_archive_shell' ) ) {
    function lunara_render_news_archive_shell( $args = array() ) {
        $defaults = array(
            'classes'      => 'lunara-editorial-archive-page lunara-news-archive-page',
            'kicker'       => __( 'Lunara Journal', 'lunara-film' ),
            'title'        => __( 'News', 'lunara-film' ),
            'copy'         => '',
            'posts'        => array(),
            'empty_title'  => __( 'No news posts have been filed yet.', 'lunara-film' ),
            'empty_copy'   => __( 'Published news coverage will appear here automatically.', 'lunara-film' ),
            'copy_words'   => 42,
            'pagination'   => paginate_links(),
            'source_label' => __( 'Editorial lane', 'lunara-film' ),
        );
        $args = wp_parse_args( $args, $defaults );

        $posts = array_values( array_filter( (array) $args['posts'], static function ( $post_item ) {
            return $post_item instanceof WP_Post;
        } ) );

        $copy            = trim( wp_strip_all_tags( (string) $args['copy'] ) );
        $classes         = trim( 'site-main lunara-archive-page ' . (string) $args['classes'] );
        $lead_post       = ! empty( $posts ) ? array_shift( $posts ) : null;
        $support_posts   = array_slice( $posts, 0, 2 );
        $remaining_posts = array_slice( $posts, 2 );
        $visible_count   = count( $posts ) + ( $lead_post instanceof WP_Post ? 1 : 0 );
        $archive_mode    = $lead_post instanceof WP_Post
            ? ( ! empty( $remaining_posts ) ? __( 'Spotlight / Supporting / News Run', 'lunara-film' ) : __( 'Spotlight / Supporting', 'lunara-film' ) )
            : __( 'Standby', 'lunara-film' );

        $news_total = 0;
        $news_term  = get_category_by_slug( 'news' );
        if ( $news_term instanceof WP_Term ) {
            $news_total = intval( $news_term->count );
        }

        ob_start();
        ?>
        <main id="primary" class="<?php echo esc_attr( $classes ); ?>">
            <section class="lunara-home-section lunara-archive-hero lunara-news-archive-hero">
                <div class="lunara-news-archive-hero-shell">
                    <div class="lunara-news-archive-hero-copy-wrap">
                        <p class="lunara-archive-hero-kicker"><?php echo esc_html( $args['kicker'] ); ?></p>
                        <h1 class="lunara-archive-hero-title"><?php echo esc_html( $args['title'] ); ?></h1>
                        <?php if ( '' !== $copy ) : ?>
                            <p class="lunara-archive-hero-copy"><?php echo esc_html( wp_trim_words( $copy, max( 12, intval( $args['copy_words'] ) ) ) ); ?></p>
                        <?php endif; ?>
                    </div>
                    <aside class="lunara-news-archive-debrief" aria-label="<?php esc_attr_e( 'News archive summary', 'lunara-film' ); ?>">
                        <p class="lunara-news-archive-debrief-kicker"><?php esc_html_e( 'At A Glance', 'lunara-film' ); ?></p>
                        <ul class="lunara-news-archive-debrief-list">
                            <li>
                                <strong><?php esc_html_e( 'Total Filed', 'lunara-film' ); ?></strong>
                                <span><?php echo esc_html( number_format_i18n( $news_total ) ); ?></span>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Visible Now', 'lunara-film' ); ?></strong>
                                <span><?php echo esc_html( number_format_i18n( $visible_count ) ); ?></span>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Desk State', 'lunara-film' ); ?></strong>
                                <span><?php echo esc_html( $archive_mode ); ?></span>
                            </li>
                            <li>
                                <strong><?php esc_html_e( 'Coverage Focus', 'lunara-film' ); ?></strong>
                                <span><?php echo esc_html( $args['source_label'] ); ?></span>
                            </li>
                        </ul>
                    </aside>
                </div>
            </section>

            <section class="lunara-home-section lunara-editorial-archive-shell lunara-news-archive-shell">
                <?php if ( $lead_post instanceof WP_Post ) : ?>
                    <div class="lunara-news-archive-spotlight">
                        <?php echo lunara_render_dispatch_archive_card( $lead_post->ID, true ); ?>

                        <?php if ( ! empty( $support_posts ) ) : ?>
                            <div class="lunara-news-archive-rail">
                                <div class="lunara-news-archive-rail-shell">
                                    <p class="lunara-home-section-kicker"><?php esc_html_e( 'In Rotation', 'lunara-film' ); ?></p>
                                    <h2 class="lunara-section-title"><?php esc_html_e( 'What The Signal Is Holding Beside The Lead', 'lunara-film' ); ?></h2>
                                    <p class="lunara-news-archive-rail-copy"><?php esc_html_e( 'A tighter support rail so the news archive feels like a live editorial desk, not a generic feed.', 'lunara-film' ); ?></p>
                                </div>
                                <?php foreach ( $support_posts as $post_item ) : ?>
                                    <?php echo lunara_render_dispatch_archive_card( $post_item->ID ); ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <?php if ( ! empty( $remaining_posts ) ) : ?>
                        <div class="lunara-home-section-head lunara-news-archive-run-head">
                            <div>
                                <p class="lunara-home-section-kicker"><?php esc_html_e( 'Archive Run', 'lunara-film' ); ?></p>
                                <h2 class="lunara-section-title"><?php esc_html_e( 'More Lunara Dispatches', 'lunara-film' ); ?></h2>
                                <p class="lunara-news-archive-run-copy"><?php esc_html_e( 'The broader run stays browseable and poster-led, but now lives inside the same deliberate editorial grammar as the rest of Lunara.', 'lunara-film' ); ?></p>
                            </div>
                        </div>

                        <div class="lunara-dispatch-archive-grid lunara-news-archive-grid">
                            <?php foreach ( $remaining_posts as $post_item ) : ?>
                                <?php echo lunara_render_dispatch_archive_card( $post_item->ID ); ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $args['pagination'] ) ) : ?>
                        <div class="lunara-archive-pagination">
                            <?php echo wp_kses_post( $args['pagination'] ); ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="lunara-news-archive-empty-shell">
                        <div class="lunara-archive-empty lunara-news-archive-empty">
                            <p class="lunara-home-section-kicker"><?php esc_html_e( 'Desk Standby', 'lunara-film' ); ?></p>
                            <h2><?php echo esc_html( $args['empty_title'] ); ?></h2>
                            <?php if ( '' !== trim( (string) $args['empty_copy'] ) ) : ?>
                                <p><?php echo esc_html( $args['empty_copy'] ); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="lunara-news-archive-empty-note">
                            <p class="lunara-home-section-kicker"><?php esc_html_e( 'What Lives Here', 'lunara-film' ); ?></p>
                            <h2 class="lunara-section-title"><?php esc_html_e( 'Breaking items, industry shifts, and the stories worth moving on quickly.', 'lunara-film' ); ?></h2>
                            <p class="lunara-news-archive-empty-copy"><?php esc_html_e( 'This lane is for fresh movement across the film landscape: production turns, box office signals, festival currents, awards tremors, and the kinds of developments that keep Lunara alive between the longer critical pieces.', 'lunara-film' ); ?></p>
                        </div>
                    </div>
                    <div class="lunara-news-archive-standby-shell">
                        <div class="lunara-home-section-head lunara-news-archive-standby-head">
                            <div>
                                <p class="lunara-home-section-kicker"><?php esc_html_e( 'Stay On Signal', 'lunara-film' ); ?></p>
                                <h2 class="lunara-section-title"><?php esc_html_e( 'The publication is still alive around the dispatch desk.', 'lunara-film' ); ?></h2>
                                <p class="lunara-news-archive-empty-copy"><?php esc_html_e( 'If the news lane is waiting on the next movement, the criticism, ledger, and front door are still fully in motion.', 'lunara-film' ); ?></p>
                            </div>
                        </div>
                        <div class="lunara-news-archive-standby-grid">
                            <a class="lunara-news-archive-standby-card" href="<?php echo esc_url( get_post_type_archive_link( 'review' ) ?: home_url( '/reviews/' ) ); ?>">
                                <p class="lunara-home-section-kicker"><?php esc_html_e( 'Criticism', 'lunara-film' ); ?></p>
                                <h3><?php esc_html_e( 'Browse The Review Archive', 'lunara-film' ); ?></h3>
                                <p><?php esc_html_e( 'Move through the poster-led criticism system while the news desk waits for the next live item.', 'lunara-film' ); ?></p>
                                <span class="lunara-section-link"><?php esc_html_e( 'Enter The Reviews', 'lunara-film' ); ?></span>
                            </a>
                            <a class="lunara-news-archive-standby-card" href="<?php echo esc_url( home_url( '/oscars/' ) ); ?>">
                                <p class="lunara-home-section-kicker"><?php esc_html_e( 'Ledger', 'lunara-film' ); ?></p>
                                <h3><?php esc_html_e( 'Step Into The Oscar Ledger', 'lunara-film' ); ?></h3>
                                <p><?php esc_html_e( 'Follow categories, ceremonies, records, and title profiles without leaving the Lunara world.', 'lunara-film' ); ?></p>
                                <span class="lunara-section-link"><?php esc_html_e( 'Open The Ledger', 'lunara-film' ); ?></span>
                            </a>
                            <a class="lunara-news-archive-standby-card" href="<?php echo esc_url( home_url( '/' ) ); ?>">
                                <p class="lunara-home-section-kicker"><?php esc_html_e( 'Front Door', 'lunara-film' ); ?></p>
                                <h3><?php esc_html_e( 'Return To The Live Homepage', 'lunara-film' ); ?></h3>
                                <p><?php esc_html_e( 'Jump back into the main signal mix: featured criticism, the current pulse, and the latest Oscar movement.', 'lunara-film' ); ?></p>
                                <span class="lunara-section-link"><?php esc_html_e( 'Go To Lunara', 'lunara-film' ); ?></span>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </section>
        </main>
        <?php

        return ob_get_clean();
    }
}

/**
 * Homepage featured reviews query with manual curation override.
 */
function lunara_home_featured_reviews_query( $count = 8 ) {
    $manual_ids = lunara_parse_manual_post_ids(
        lunara_theme_mod_text( 'lunara_home_featured_review_ids', '' ),
        'review'
    );

    if ( ! empty( $manual_ids ) ) {
        return lunara_reviews_query_from_ids( array_slice( $manual_ids, 0, max( 1, intval( $count ) ) ) );
    }

    return lunara_featured_reviews_query( $count );
}

/**
 * Homepage dispatches query for news, essays, reactions, and podcast posts.
 */
function lunara_home_dispatches_query( $count = 4 ) {
    $count      = max( 1, intval( $count ) );
    $manual_ids = lunara_parse_manual_post_ids(
        lunara_theme_mod_text( 'lunara_home_dispatch_post_ids', '' ),
        'post'
    );

    if ( ! empty( $manual_ids ) ) {
        return lunara_posts_query_from_ids( array_slice( $manual_ids, 0, $count ) );
    }

    $slugs = lunara_get_dispatch_category_slugs();

    $query_args = array(
        'post_type'              => 'post',
        'posts_per_page'         => $count,
        'post_status'            => 'publish',
        'ignore_sticky_posts'    => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    );

    if ( ! empty( $slugs ) ) {
        $query_args['tax_query'] = array(
            array(
                'taxonomy' => 'category',
                'field'    => 'slug',
                'terms'    => $slugs,
                'operator' => 'IN',
            ),
        );
    }

    $query = new WP_Query( $query_args );
    if ( $query->have_posts() ) {
        return $query;
    }

    return new WP_Query(
        array(
            'post_type'      => 'post',
            'post__in'       => array( 0 ),
            'posts_per_page' => 0,
            'no_found_rows'  => true,
        )
    );
}

/**
 * Clear short-lived front-page query caches whenever review content changes.
 */
function lunara_get_oscars_plugin() {
    if ( ! class_exists( 'Academy_Awards_Table' ) || ! method_exists( 'Academy_Awards_Table', 'get_instance' ) ) {
        return null;
    }

    return Academy_Awards_Table::get_instance();
}

/**
 * Resolve the most readable homepage label for an Oscar winner entry.
 */
function lunara_home_winner_primary_label( $entry ) {
    $category = trim( (string) ( $entry['canonical_category'] ?? '' ) );
    $film     = trim( (string) ( $entry['film'] ?? '' ) );
    $name     = trim( (string) ( $entry['name'] ?? '' ) );
    $detail   = trim( (string) ( $entry['detail'] ?? '' ) );
    $nominees = trim( (string) ( $entry['nominees'] ?? '' ) );

    $title_forward_categories = array(
        'BEST PICTURE',
        'ANIMATED FEATURE FILM',
        'DOCUMENTARY (Feature)',
        'DOCUMENTARY (Short Subject)',
        'INTERNATIONAL FEATURE FILM',
        'SHORT FILM (Animated)',
        'SHORT FILM (Live Action)',
    );

    if ( in_array( $category, $title_forward_categories, true ) && $film !== '' ) {
        return $film;
    }

    if ( $name !== '' ) {
        return $name;
    }

    if ( $film !== '' ) {
        return $film;
    }

    if ( $detail !== '' ) {
        return $detail;
    }

    return $nominees;
}

/**
 * Build a compact secondary line for homepage winner cards.
 */
function lunara_home_winner_secondary_label( $entry ) {
    $primary = lunara_home_winner_primary_label( $entry );
    $bits    = array();

    foreach ( array( 'film', 'name', 'detail', 'note' ) as $key ) {
        $value = trim( (string) ( $entry[ $key ] ?? '' ) );
        if ( $value === '' ) {
            continue;
        }

        if ( strcasecmp( $value, $primary ) === 0 ) {
            continue;
        }

        if ( ! in_array( $value, $bits, true ) ) {
            $bits[] = $value;
        }
    }

    return implode( ' · ', array_slice( $bits, 0, 2 ) );
}

/**
 * Build a dynamic Oscars snapshot for the homepage from the active database plugin.
 */
function lunara_get_home_oscars_snapshot() {
    $cache_key = 'lunara_home_oscars_snapshot_v2'; // v2: includes visual package on spotlight entries
    $cached    = get_transient( $cache_key );

    if ( is_array( $cached ) && ! empty( $cached ) ) {
        return $cached;
    }

    $aat = lunara_get_oscars_plugin();
    if ( ! $aat || ! method_exists( $aat, 'get_max_ceremony' ) || ! method_exists( $aat, 'get_ceremony_rollup' ) ) {
        return array();
    }

    $ceremony = intval( $aat->get_max_ceremony() );
    if ( $ceremony <= 0 ) {
        return array();
    }

    $rollup = $aat->get_ceremony_rollup( $ceremony );
    if ( empty( $rollup ) || ! is_array( $rollup ) ) {
        return array();
    }

    $winner_rows = ! empty( $rollup['winner_rows'] ) && is_array( $rollup['winner_rows'] ) ? $rollup['winner_rows'] : array();
    $winner_map  = array();
    foreach ( $winner_rows as $winner_entry ) {
        $canonical = trim( (string) ( $winner_entry['canonical_category'] ?? '' ) );
        if ( $canonical !== '' && ! isset( $winner_map[ $canonical ] ) ) {
            $winner_map[ $canonical ] = $winner_entry;
        }
    }

    $spotlight_categories = array(
        'BEST PICTURE',
        'DIRECTING',
        'ACTOR IN A LEADING ROLE',
        'ACTRESS IN A LEADING ROLE',
        'ACTOR IN A SUPPORTING ROLE',
        'ACTRESS IN A SUPPORTING ROLE',
        'CINEMATOGRAPHY',
        'WRITING (Original Screenplay)',
    );

    $spotlights = array();
    foreach ( $spotlight_categories as $canonical ) {
        if ( empty( $winner_map[ $canonical ] ) ) {
            continue;
        }

        $entry = $winner_map[ $canonical ];
        $entry['category_label'] = method_exists( $aat, 'format_category_display' ) ? $aat->format_category_display( $canonical ) : $canonical;
        $entry['category_url']   = method_exists( $aat, 'get_category_url' ) ? $aat->get_category_url( $canonical ) : home_url( '/oscars/category/' . sanitize_title( $canonical ) . '/' );
        $entry['primary_label']  = lunara_home_winner_primary_label( $entry );
        $entry['secondary_label'] = lunara_home_winner_secondary_label( $entry );
        $entry['url']            = ! empty( $entry['film_url'] ) ? $entry['film_url'] : $entry['category_url'];
        $spotlight_film_id       = trim( (string) ( $entry['film_id'] ?? '' ) );
        $entry['visual']         = ( $spotlight_film_id !== '' && method_exists( $aat, 'get_title_visual_package' ) )
            ? $aat->get_title_visual_package( $spotlight_film_id, 'medium_large' )
            : array();
        $spotlights[]            = $entry;
    }

    $top_titles = array();
    foreach ( array_slice( (array) ( $rollup['top_titles'] ?? array() ), 0, 4 ) as $title_entry ) {
        $film_id = trim( (string) ( $title_entry['film_id'] ?? '' ) );
        $visual  = ( $film_id !== '' && method_exists( $aat, 'get_title_visual_package' ) ) ? $aat->get_title_visual_package( $film_id, 'large' ) : array();

        $title_entry['visual'] = $visual;
        $title_entry['url']    = ! empty( $title_entry['film_url'] )
            ? $title_entry['film_url']
            : ( ( $film_id !== '' && method_exists( $aat, 'build_entity_url_from_id' ) ) ? $aat->build_entity_url_from_id( $film_id ) : home_url( '/oscars/title/' . $film_id . '/' ) );
        $title_entry['winning_categories_line'] = ! empty( $title_entry['winning_categories'] ) && is_array( $title_entry['winning_categories'] )
            ? implode( ' · ', array_slice( array_values( $title_entry['winning_categories'] ), 0, 2 ) )
            : '';

        $top_titles[] = $title_entry;
    }

    $best_picture = ! empty( $rollup['best_picture'] ) && is_array( $rollup['best_picture'] ) ? $rollup['best_picture'] : array();
    if ( ! empty( $best_picture['film_id'] ) && method_exists( $aat, 'get_title_visual_package' ) ) {
        $best_picture['visual'] = $aat->get_title_visual_package( $best_picture['film_id'], 'large' );
    } else {
        $best_picture['visual'] = array();
    }

    $winner_rows_total = count( $winner_rows );
    $categories_total  = intval( $rollup['categories_total'] ?? 0 );
    $summary_bits      = array();

    if ( ! empty( $best_picture['film'] ) ) {
        $summary_bits[] = sprintf( 'Best Picture went to %s', $best_picture['film'] );
    }

    if ( ! empty( $rollup['most_wins']['film'] ) && ! empty( $rollup['most_wins']['wins'] ) ) {
        $summary_bits[] = sprintf( '%1$s led the ceremony with %2$s win%3$s', $rollup['most_wins']['film'], number_format_i18n( intval( $rollup['most_wins']['wins'] ) ), intval( $rollup['most_wins']['wins'] ) === 1 ? '' : 's' );
    }

    $snapshot = array(
        'ceremony'          => $ceremony,
        'ceremony_label'    => method_exists( $aat, 'ordinal' ) ? $aat->ordinal( $ceremony ) . ' Academy Awards' : sprintf( 'Ceremony %d', $ceremony ),
        'year_label'        => method_exists( $aat, 'get_ceremony_year' ) ? $aat->get_ceremony_year( $ceremony ) : (string) ( $rollup['year'] ?? '' ),
        'ceremony_url'      => method_exists( $aat, 'get_ceremony_url' ) ? $aat->get_ceremony_url( $ceremony ) : home_url( '/oscars/ceremony/' . $ceremony . '/' ),
        'database_url'      => method_exists( $aat, 'get_database_url' ) ? $aat->get_database_url() : home_url( '/oscars/' ),
        'categories_url'    => method_exists( $aat, 'get_categories_index_url' ) ? $aat->get_categories_index_url() : home_url( '/oscars/categories/' ),
        'rollup'            => $rollup,
        'best_picture'      => $best_picture,
        'spotlights'        => $spotlights,
        'top_titles'        => $top_titles,
        'winner_rows_total' => $winner_rows_total,
        'categories_total'  => $categories_total,
        'summary'           => ! empty( $summary_bits ) ? implode( '. ', $summary_bits ) . '.' : '',
        'winner_record'     => sprintf(
            '%1$s winner row%2$s across %3$s categor%4$s%5$s',
            number_format_i18n( $winner_rows_total ),
            $winner_rows_total === 1 ? '' : 's',
            number_format_i18n( $categories_total ),
            $categories_total === 1 ? 'y' : 'ies',
            $winner_rows_total > $categories_total ? ', including ties' : ''
        ),
    );

    set_transient( $cache_key, $snapshot, 5 * MINUTE_IN_SECONDS );

    return $snapshot;
}

/**
 * Find a spotlight winner entry in the homepage snapshot by canonical category.
 */
function lunara_get_home_snapshot_spotlight( $snapshot, $canonical_category ) {
    foreach ( (array) ( $snapshot['spotlights'] ?? array() ) as $spotlight ) {
        if ( strtoupper( trim( (string) ( $spotlight['canonical_category'] ?? '' ) ) ) === strtoupper( trim( (string) $canonical_category ) ) ) {
            return $spotlight;
        }
    }

    return array();
}

/**
 * Determine whether a category debuts in the supplied ceremony.
 */
function lunara_home_category_debuts_in_ceremony( $canonical_category, $ceremony ) {
    global $wpdb;

    $canonical_category = trim( (string) $canonical_category );
    $ceremony           = intval( $ceremony );

    if ( $canonical_category === '' || $ceremony <= 0 ) {
        return false;
    }

    $table_name = $wpdb->prefix . 'academy_awards';
    $first_seen = intval(
        $wpdb->get_var(
            $wpdb->prepare(
                "SELECT MIN(ceremony) FROM $table_name WHERE canonical_category = %s",
                $canonical_category
            )
        )
    );

    return $first_seen > 0 && $first_seen === $ceremony;
}

/**
 * Build editable homepage Oscar pulse notes.
 */
function lunara_get_home_pulse_editorial_cards( $snapshot = array() ) {
    if ( empty( $snapshot ) ) {
        $snapshot = lunara_get_home_oscars_snapshot();
    }

    $ceremony          = intval( $snapshot['ceremony'] ?? 0 );
    $ceremony_label    = trim( (string) ( $snapshot['ceremony_label'] ?? 'Latest Oscar Pulse' ) );
    $database_url      = trim( (string) ( $snapshot['database_url'] ?? home_url( '/oscars/' ) ) );
    $ceremony_url      = trim( (string) ( $snapshot['ceremony_url'] ?? $database_url ) );
    $best_picture      = (array) ( $snapshot['best_picture'] ?? array() );
    $most_wins         = (array) ( $snapshot['rollup']['most_wins'] ?? array() );
    $casting_spotlight = lunara_get_home_snapshot_spotlight( $snapshot, 'CASTING' );
    $cinema_spotlight  = lunara_get_home_snapshot_spotlight( $snapshot, 'CINEMATOGRAPHY' );

    $cards = array(
        array(
            'kicker'     => 'Latest Best Picture',
            'title'      => ! empty( $best_picture['film'] ) ? sprintf( '%s is now the front door into the latest ceremony', $best_picture['film'] ) : 'The latest Best Picture winner is now live',
            'copy'       => ! empty( $most_wins['film'] ) && ! empty( $most_wins['wins'] )
                ? sprintf( '%1$s now sits inside the latest Academy Awards hub alongside %2$s, which led the night with %3$s win%4$s.', $ceremony_label, $most_wins['film'], number_format_i18n( intval( $most_wins['wins'] ) ), intval( $most_wins['wins'] ) === 1 ? '' : 's' )
                : 'The newest winners, ceremony rollup, and film pages are already stitched into the Lunara Oscar Ledger.',
            'link_label' => 'Open Ceremony Hub',
            'url'        => $ceremony_url,
        ),
        array(
            'kicker'     => lunara_home_category_debuts_in_ceremony( 'CASTING', $ceremony ) ? 'New Oscar Category' : 'Category Spotlight',
            'title'      => ! empty( $casting_spotlight['primary_label'] )
                ? sprintf( '%s keeps the newest category from feeling abstract', $casting_spotlight['primary_label'] )
                : 'Every category in the Ledger leads somewhere real',
            'copy'       => ! empty( $casting_spotlight['film'] )
                ? sprintf( '%s gives you a live example of how the Ledger moves from a category win into a film page, people pages, and broader ceremony context.', $casting_spotlight['film'] )
                : 'Browse any category to see the full lineage of winners and nominees, then follow each name into the wider Lunara record.',
            'link_label' => ! empty( $casting_spotlight['category_url'] ) ? 'See the Category' : 'Explore the Ledger',
            'url'        => ! empty( $casting_spotlight['category_url'] ) ? $casting_spotlight['category_url'] : $database_url,
        ),
        array(
            'kicker'     => ! empty( $cinema_spotlight['category_label'] ) ? $cinema_spotlight['category_label'] : 'Editorial Spotlight',
            'title'      => ! empty( $cinema_spotlight['primary_label'] )
                ? sprintf( '%s won in a category that rewards the image itself', $cinema_spotlight['primary_label'] )
                : 'The craft categories reveal the ceremony from a different angle',
            'copy'       => ! empty( $cinema_spotlight['film'] )
                ? sprintf( 'Follow %s through the Ledger to see how the craft side of this ceremony connects to the broader competition.', $cinema_spotlight['film'] )
                : 'Cinematography, editing, sound, and design winners sit alongside every other category inside the same Lunara record.',
            'link_label' => ! empty( $cinema_spotlight['url'] ) ? 'Open the Winner Page' : 'Browse Categories',
            'url'        => ! empty( $cinema_spotlight['url'] ) ? $cinema_spotlight['url'] : $database_url,
        ),
    );

    foreach ( $cards as $index => $card ) {
        $card_number = $index + 1;
        foreach ( array( 'kicker', 'title', 'copy', 'link_label', 'url' ) as $field ) {
            $theme_mod_key = sprintf( 'lunara_home_pulse_card_%d_%s', $card_number, $field === 'url' ? 'link_url' : $field );
            $override      = 'url' === $field
                ? lunara_theme_mod_url( $theme_mod_key, '' )
                : lunara_theme_mod_text( $theme_mod_key, '' );

            if ( '' !== $override ) {
                $cards[ $index ][ $field ] = $override;
            }
        }
    }

    return $cards;
}

/**
 * Build a poster-first story card for a specific Oscar-recognized title.
 */
function lunara_build_home_title_story( $imdb_id, $args = array() ) {
    $aat = lunara_get_oscars_plugin();
    if ( ! $aat || ! method_exists( $aat, 'get_title_visual_package' ) ) {
        return array();
    }

    $defaults = array(
        'preferred_categories' => array(),
        'max_categories'       => 4,
    );
    $args = wp_parse_args( $args, $defaults );

    global $wpdb;
    $table_name = $wpdb->prefix . 'academy_awards';
    $imdb_id    = strtolower( trim( (string) $imdb_id ) );

    if ( $imdb_id === '' ) {
        return array();
    }

    $like = '%' . $wpdb->esc_like( $imdb_id ) . '%';
    $rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT ceremony, year, canonical_category, film, winner FROM $table_name WHERE (film_id = %s OR film_id LIKE %s) AND canonical_category != '' ORDER BY ceremony DESC, winner DESC, canonical_category ASC",
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
            static function( $row ) {
                return ! empty( $row['winner'] );
            }
        )
    );

    $raw_categories = array();
    foreach ( $winner_rows as $row ) {
        $canonical = trim( (string) ( $row['canonical_category'] ?? '' ) );
        if ( $canonical !== '' && ! in_array( $canonical, $raw_categories, true ) ) {
            $raw_categories[] = $canonical;
        }
    }

    $ordered_categories = array();
    foreach ( (array) $args['preferred_categories'] as $preferred ) {
        if ( in_array( $preferred, $raw_categories, true ) ) {
            $ordered_categories[] = $preferred;
        }
    }
    foreach ( $raw_categories as $canonical ) {
        if ( ! in_array( $canonical, $ordered_categories, true ) ) {
            $ordered_categories[] = $canonical;
        }
    }

    $display_categories = array();
    foreach ( array_slice( $ordered_categories, 0, intval( $args['max_categories'] ) ) as $canonical ) {
        $display_categories[] = method_exists( $aat, 'format_category_display' ) ? $aat->format_category_display( $canonical ) : $canonical;
    }

    $first_row = $rows[0];
    $title     = trim( (string) ( $first_row['film'] ?? '' ) );
    if ( $title === '' && method_exists( $aat, 'lookup_title_label' ) ) {
        $title = $aat->lookup_title_label( $imdb_id );
    }

    return array(
        'imdb_id'         => $imdb_id,
        'title'           => $title,
        'year'            => trim( (string) ( $first_row['year'] ?? '' ) ),
        'url'             => method_exists( $aat, 'build_entity_url_from_id' ) ? $aat->build_entity_url_from_id( $imdb_id ) : home_url( '/oscars/title/' . $imdb_id . '/' ),
        'visual'          => $aat->get_title_visual_package( $imdb_id, 'medium_large' ),
        'wins'            => count( $winner_rows ),
        'nominations'     => count( $rows ),
        'categories'      => $display_categories,
        'categories_line' => implode( ' / ', $display_categories ),
    );
}

/**
 * Build the homepage Oscar Database spotlight section.
 */
function lunara_get_home_database_spotlight() {
    $cache_key = 'lunara_home_database_spotlight_v1';
    $cached    = get_transient( $cache_key );

    if ( is_array( $cached ) && ! empty( $cached ) ) {
        return $cached;
    }

    $aat = lunara_get_oscars_plugin();
    if ( ! $aat ) {
        return array();
    }

    global $wpdb;
    $table_name = $wpdb->prefix . 'academy_awards';
    $snapshot   = lunara_get_home_oscars_snapshot();

    $cards = array();
    foreach ( array_slice( (array) ( $snapshot['top_titles'] ?? array() ), 0, 5 ) as $title_entry ) {
        $card = array(
            'title'           => trim( (string) ( $title_entry['film'] ?? '' ) ),
            'year'            => trim( (string) ( $title_entry['year'] ?? '' ) ),
            'url'             => trim( (string) ( $title_entry['url'] ?? '' ) ),
            'visual'          => is_array( $title_entry['visual'] ?? null ) ? $title_entry['visual'] : array(),
            'categories_line' => trim( (string) ( $title_entry['winning_categories_line'] ?? '' ) ),
        );

        if ( '' !== $card['title'] && '' !== $card['url'] ) {
            $cards[] = $card;
        }
    }

    if ( count( $cards ) < 5 ) {
        // Fill remaining slots from the latest ceremony's most-nominated films
        $max_ceremony = intval( $wpdb->get_var( "SELECT MAX(ceremony) FROM $table_name" ) );
        if ( $max_ceremony > 0 ) {
            $existing_urls = array();
            foreach ( $cards as $c ) {
                $existing_urls[ strtolower( trim( (string) ( $c['url'] ?? '' ) ) ) ] = true;
            }

            $fill_rows = $wpdb->get_results(
                $wpdb->prepare(
                    "SELECT film_id, film, COUNT(*) as nom_count FROM $table_name WHERE ceremony = %d AND film_id != '' AND canonical_category != '' GROUP BY film_id, film ORDER BY nom_count DESC LIMIT 10",
                    $max_ceremony
                ),
                ARRAY_A
            );

            foreach ( $fill_rows as $fill_row ) {
                if ( count( $cards ) >= 5 ) {
                    break;
                }

                $fill_card = lunara_build_home_title_story( $fill_row['film_id'], array( 'max_categories' => 3 ) );
                if ( ! empty( $fill_card ) && ! empty( $fill_card['url'] ) ) {
                    $fill_url = strtolower( trim( (string) $fill_card['url'] ) );
                    if ( ! isset( $existing_urls[ $fill_url ] ) ) {
                        $cards[]                    = $fill_card;
                        $existing_urls[ $fill_url ] = true;
                    }
                }
            }
        }
    }

    $spotlight = array(
        'database_url'      => method_exists( $aat, 'get_database_url' ) ? $aat->get_database_url() : home_url( '/oscars/' ),
        'categories_url'    => method_exists( $aat, 'get_categories_index_url' ) ? $aat->get_categories_index_url() : home_url( '/oscars/categories/' ),
        'records_total'     => intval( $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" ) ),
        'winners_total'     => intval( $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE winner = 1" ) ),
        'categories_total'  => intval( $wpdb->get_var( "SELECT COUNT(DISTINCT canonical_category) FROM $table_name WHERE canonical_category != ''" ) ),
        'ceremonies_total'  => intval( $wpdb->get_var( "SELECT COUNT(DISTINCT ceremony) FROM $table_name" ) ),
        'cards'             => $cards,
    );

    set_transient( $cache_key, $spotlight, 15 * MINUTE_IN_SECONDS );

    return $spotlight;
}

/**
 * Build the homepage "From the Ledger" story cards.
 *
 * Feature 3: Dynamic + admin-controllable.
 * - If the Customizer override IDs are set, use those.
 * - Otherwise, query the database for a rotating mix of Best Picture winners
 *   and high-nomination films, refreshed every 6 hours.
 */
function lunara_get_home_ledger_story_cards() {
    $cache_key = 'lunara_home_ledger_story_cards_v2';
    $cached    = get_transient( $cache_key );

    if ( is_array( $cached ) && ! empty( $cached ) ) {
        return $cached;
    }

    // Check for Customizer overrides first.
    $override_ids = array();
    for ( $i = 1; $i <= 4; $i++ ) {
        $id = trim( (string) get_theme_mod( 'lunara_home_ledger_card_' . $i . '_imdb_id', '' ) );
        if ( preg_match( '/^tt\d{7,8}$/i', $id ) ) {
            $override_ids[] = strtolower( $id );
        }
    }

    if ( count( $override_ids ) >= 4 ) {
        // Use the admin-selected IDs.
        $selected_ids = array_slice( $override_ids, 0, 4 );
    } else {
        // Dynamic rotation: query the database.
        $table = lunara_awards_table_name();
        if ( $table === '' ) {
            // Fallback to curated classics.
            $selected_ids = array( 'tt0070047', 'tt0074958', 'tt0477348', 'tt6751668' );
        } else {
            global $wpdb;
            $day_of_year = intval( date( 'z' ) );

            $bp_categories = "'BEST PICTURE','BEST MOTION PICTURE','OUTSTANDING PICTURE','OUTSTANDING PRODUCTION','OUTSTANDING MOTION PICTURE','UNIQUE AND ARTISTIC PICTURE'";

            // Pool 1: Random Best Picture winners (seeded by day).
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $bp_winners = $wpdb->get_col(
                "SELECT DISTINCT film_id FROM {$table} WHERE category IN ({$bp_categories}) AND winner = 1 AND film_id != '' ORDER BY RAND({$day_of_year}) LIMIT 8"
            );

            // Pool 2: High-nomination films (10+ noms).
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
            $high_nom = $wpdb->get_col(
                "SELECT film_id FROM {$table} WHERE film_id != '' GROUP BY film_id HAVING COUNT(*) >= 10 ORDER BY RAND({$day_of_year}) LIMIT 8"
            );

            // Pool 3: Recent ceremony highlights (latest 3 ceremonies).
            $aat = lunara_get_oscars_plugin();
            $max_ceremony = ( $aat && method_exists( $aat, 'get_max_ceremony' ) ) ? intval( $aat->get_max_ceremony() ) : 0;
            $recent_ids = array();
            if ( $max_ceremony > 0 ) {
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
                $recent_ids = $wpdb->get_col(
                    $wpdb->prepare(
                        "SELECT DISTINCT film_id FROM {$table} WHERE ceremony >= %d AND winner = 1 AND film_id != '' ORDER BY RAND({$day_of_year}) LIMIT 6",
                        max( 1, $max_ceremony - 2 )
                    )
                );
            }

            // Merge pools, deduplicate, pick 4.
            $all_pools = array_merge(
                array_slice( (array) $bp_winners, 0, 3 ),
                array_slice( (array) $high_nom, 0, 3 ),
                array_slice( (array) $recent_ids, 0, 3 )
            );
            $all_pools = array_values( array_unique( array_filter( $all_pools ) ) );

            // Fill in any Customizer overrides at the front.
            $selected_ids = array();
            foreach ( $override_ids as $oid ) {
                $selected_ids[] = $oid;
            }
            foreach ( $all_pools as $pool_id ) {
                if ( ! in_array( $pool_id, $selected_ids, true ) ) {
                    $selected_ids[] = $pool_id;
                }
                if ( count( $selected_ids ) >= 4 ) {
                    break;
                }
            }

            // Final fallback if database is sparse.
            $fallback_ids = array( 'tt0070047', 'tt0074958', 'tt0477348', 'tt6751668' );
            foreach ( $fallback_ids as $fid ) {
                if ( count( $selected_ids ) >= 4 ) {
                    break;
                }
                if ( ! in_array( $fid, $selected_ids, true ) ) {
                    $selected_ids[] = $fid;
                }
            }
        }
    }

    $cards = array();
    foreach ( array_slice( $selected_ids, 0, 4 ) as $imdb_id ) {
        $card = lunara_build_home_title_story(
            $imdb_id,
            array(
                'preferred_categories' => array( 'BEST PICTURE', 'DIRECTING', 'ACTOR IN A LEADING ROLE', 'ACTRESS IN A LEADING ROLE' ),
                'max_categories'       => 4,
            )
        );

        if ( ! empty( $card ) ) {
            $cards[] = $card;
        }
    }

    // Cache for 6 hours.
    set_transient( $cache_key, $cards, 6 * HOUR_IN_SECONDS );

    return $cards;
}

/* ========================================
   FEATURE 1: OSCAR SPOTLIGHT (date-based daily rotation)
   ======================================== */

/**
 * Build the homepage Oscar Spotlight section.
 * Uses date('z') (day of year 0-365) to rotate through different spotlight types.
 * Cached as a 12-hour transient.
 */
function lunara_get_home_oscar_spotlight() {
    $cache_key = 'lunara_home_oscar_spotlight_v1';
    $cached    = get_transient( $cache_key );

    if ( is_array( $cached ) && ! empty( $cached ) ) {
        return $cached;
    }

    $table = lunara_awards_table_name();
    if ( $table === '' ) {
        return array();
    }

    global $wpdb;
    $aat       = lunara_get_oscars_plugin();
    $day       = intval( date( 'z' ) );
    $month     = intval( date( 'n' ) );
    $result    = array();

    $bp_categories = "'BEST PICTURE','BEST MOTION PICTURE','OUTSTANDING PICTURE','OUTSTANDING PRODUCTION','OUTSTANDING MOTION PICTURE','UNIQUE AND ARTISTIC PICTURE'";

    if ( $day <= 72 ) {
        // --- "On This Day" — ceremonies/wins from the current month across all years ---
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $ceremony_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ceremony, year, film, film_id FROM {$table} WHERE category IN ({$bp_categories}) AND winner = 1 ORDER BY RAND(%d) LIMIT 1",
                $day + $month * 100
            ),
            ARRAY_A
        );

        if ( empty( $ceremony_row ) ) {
            return array();
        }

        $ceremony_num  = intval( $ceremony_row['ceremony'] );
        $film          = trim( (string) ( $ceremony_row['film'] ?? '' ) );
        $film_id       = trim( (string) ( $ceremony_row['film_id'] ?? '' ) );

        // Get stats for this ceremony.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $ceremony_stats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) AS total_noms, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS total_wins, COUNT(DISTINCT canonical_category) AS categories FROM {$table} WHERE ceremony = %d",
                $ceremony_num
            ),
            ARRAY_A
        );

        $ceremony_label = ( $aat && method_exists( $aat, 'ordinal' ) ) ? $aat->ordinal( $ceremony_num ) . ' Academy Awards' : sprintf( 'Ceremony %d', $ceremony_num );
        $ceremony_url   = ( $aat && method_exists( $aat, 'get_ceremony_url' ) ) ? $aat->get_ceremony_url( $ceremony_num ) : home_url( '/oscars/ceremony/' . $ceremony_num . '/' );

        $visual = array();
        if ( $film_id !== '' && $aat && method_exists( $aat, 'get_title_visual_package' ) ) {
            $visual = $aat->get_title_visual_package( $film_id, 'medium_large' );
        }

        $result = array(
            'kicker'        => 'On This Day in Oscar History',
            'title'         => sprintf( '%s won Best Picture at the %s', $film, $ceremony_label ),
            'copy'          => sprintf( 'The %s featured %s categories, %s nominees, and %s winners. Explore the full ceremony breakdown.',
                $ceremony_label,
                number_format_i18n( intval( $ceremony_stats['categories'] ?? 0 ) ),
                number_format_i18n( intval( $ceremony_stats['total_noms'] ?? 0 ) ),
                number_format_i18n( intval( $ceremony_stats['total_wins'] ?? 0 ) )
            ),
            'stats'         => array(
                array( 'label' => 'Ceremony', 'value' => $ceremony_label ),
                array( 'label' => 'Year', 'value' => trim( (string) ( $ceremony_row['year'] ?? '' ) ) ),
                array( 'label' => 'Categories', 'value' => number_format_i18n( intval( $ceremony_stats['categories'] ?? 0 ) ) ),
                array( 'label' => 'Winners', 'value' => number_format_i18n( intval( $ceremony_stats['total_wins'] ?? 0 ) ) ),
            ),
            'featured_film' => array(
                'title'   => $film,
                'film_id' => $film_id,
                'visual'  => $visual,
                'url'     => $film_id !== '' ? home_url( '/oscars/title/' . $film_id . '/' ) : $ceremony_url,
            ),
            'url'           => $ceremony_url,
        );

    } elseif ( $day <= 145 ) {
        // --- "Category Deep Dive" — spotlight a random category with its most decorated nominee ---
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $category_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT canonical_category, COUNT(*) AS total, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS wins FROM {$table} WHERE canonical_category != '' GROUP BY canonical_category HAVING total >= 20 ORDER BY RAND(%d) LIMIT 1",
                $day
            ),
            ARRAY_A
        );

        if ( empty( $category_row ) ) {
            return array();
        }

        $canonical = trim( (string) $category_row['canonical_category'] );
        $category_label = ( $aat && method_exists( $aat, 'format_category_display' ) ) ? $aat->format_category_display( $canonical ) : $canonical;
        $category_url   = ( $aat && method_exists( $aat, 'get_category_url' ) ) ? $aat->get_category_url( $canonical ) : home_url( '/oscars/category/' . sanitize_title( $canonical ) . '/' );

        // Most decorated person/film in this category.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $top_nominee = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT name, film, film_id, COUNT(*) AS noms, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS wins FROM {$table} WHERE canonical_category = %s AND name != '' GROUP BY name ORDER BY noms DESC, wins DESC LIMIT 1",
                $canonical
            ),
            ARRAY_A
        );

        $film_id = trim( (string) ( $top_nominee['film_id'] ?? '' ) );
        $visual  = array();
        if ( $film_id !== '' && $aat && method_exists( $aat, 'get_title_visual_package' ) ) {
            $visual = $aat->get_title_visual_package( $film_id, 'medium_large' );
        }

        $result = array(
            'kicker'        => 'Category Deep Dive',
            'title'         => $category_label,
            'copy'          => sprintf( '%s has %s total nomination rows and %s winners in the ledger. %s leads this category with %s nominations and %s wins.',
                $category_label,
                number_format_i18n( intval( $category_row['total'] ) ),
                number_format_i18n( intval( $category_row['wins'] ) ),
                ! empty( $top_nominee['name'] ) ? $top_nominee['name'] : 'The most decorated nominee',
                number_format_i18n( intval( $top_nominee['noms'] ?? 0 ) ),
                number_format_i18n( intval( $top_nominee['wins'] ?? 0 ) )
            ),
            'stats'         => array(
                array( 'label' => 'Nominations', 'value' => number_format_i18n( intval( $category_row['total'] ) ) ),
                array( 'label' => 'Winners', 'value' => number_format_i18n( intval( $category_row['wins'] ) ) ),
                array( 'label' => 'Top Nominee', 'value' => trim( (string) ( $top_nominee['name'] ?? 'N/A' ) ) ),
                array( 'label' => 'Their Noms', 'value' => number_format_i18n( intval( $top_nominee['noms'] ?? 0 ) ) ),
            ),
            'featured_film' => array(
                'title'   => trim( (string) ( $top_nominee['film'] ?? $category_label ) ),
                'film_id' => $film_id,
                'visual'  => $visual,
                'url'     => $category_url,
            ),
            'url'           => $category_url,
        );

    } elseif ( $day <= 218 ) {
        // --- "The Record Holders" — most wins/nominations for a rotating category ---
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $record_row = $wpdb->get_row(
            "SELECT film, film_id, COUNT(*) AS noms, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS wins FROM {$table} WHERE film_id != '' AND film != '' GROUP BY film_id ORDER BY wins DESC, noms DESC LIMIT 1",
            ARRAY_A
        );

        if ( empty( $record_row ) ) {
            return array();
        }

        $film    = trim( (string) $record_row['film'] );
        $film_id = trim( (string) $record_row['film_id'] );
        $visual  = array();
        if ( $film_id !== '' && $aat && method_exists( $aat, 'get_title_visual_package' ) ) {
            $visual = $aat->get_title_visual_package( $film_id, 'medium_large' );
        }

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $total_unique_films = intval( $wpdb->get_var( "SELECT COUNT(DISTINCT film_id) FROM {$table} WHERE film_id != ''" ) );

        $result = array(
            'kicker'        => 'The Record Holders',
            'title'         => sprintf( '%s holds the most Oscar wins', $film ),
            'copy'          => sprintf( 'With %s wins from %s nominations, %s stands at the top of the all-time Oscar leaderboard across %s unique films in the database.',
                number_format_i18n( intval( $record_row['wins'] ) ),
                number_format_i18n( intval( $record_row['noms'] ) ),
                $film,
                number_format_i18n( $total_unique_films )
            ),
            'stats'         => array(
                array( 'label' => 'Wins', 'value' => number_format_i18n( intval( $record_row['wins'] ) ) ),
                array( 'label' => 'Nominations', 'value' => number_format_i18n( intval( $record_row['noms'] ) ) ),
                array( 'label' => 'Unique Films', 'value' => number_format_i18n( $total_unique_films ) ),
            ),
            'featured_film' => array(
                'title'   => $film,
                'film_id' => $film_id,
                'visual'  => $visual,
                'url'     => $film_id !== '' ? home_url( '/oscars/title/' . $film_id . '/' ) : home_url( '/oscars/' ),
            ),
            'url'           => $film_id !== '' ? home_url( '/oscars/title/' . $film_id . '/' ) : home_url( '/oscars/' ),
        );

    } elseif ( $day <= 291 ) {
        // --- "Oscar Rivalries" — ceremonies where multiple films had 10+ nominations ---
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $rivalry_ceremony = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT ceremony, year FROM (
                    SELECT ceremony, year, film_id, COUNT(*) AS noms
                    FROM {$table}
                    WHERE film_id != ''
                    GROUP BY ceremony, film_id
                    HAVING noms >= 10
                ) AS high_nom_films
                GROUP BY ceremony
                HAVING COUNT(*) >= 2
                ORDER BY RAND(%d) LIMIT 1",
                $day
            ),
            ARRAY_A
        );

        if ( empty( $rivalry_ceremony ) ) {
            // Fallback: just pick the ceremony with the most total nominations.
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
            $rivalry_ceremony = $wpdb->get_row(
                "SELECT ceremony, year FROM {$table} GROUP BY ceremony ORDER BY COUNT(*) DESC LIMIT 1",
                ARRAY_A
            );
        }

        if ( empty( $rivalry_ceremony ) ) {
            return array();
        }

        $ceremony_num = intval( $rivalry_ceremony['ceremony'] );
        $ceremony_label = ( $aat && method_exists( $aat, 'ordinal' ) ) ? $aat->ordinal( $ceremony_num ) . ' Academy Awards' : sprintf( 'Ceremony %d', $ceremony_num );
        $ceremony_url   = ( $aat && method_exists( $aat, 'get_ceremony_url' ) ) ? $aat->get_ceremony_url( $ceremony_num ) : home_url( '/oscars/ceremony/' . $ceremony_num . '/' );

        // Top two films at this ceremony by nomination count.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $rivals = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT film, film_id, COUNT(*) AS noms, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS wins FROM {$table} WHERE ceremony = %d AND film_id != '' GROUP BY film_id ORDER BY noms DESC LIMIT 2",
                $ceremony_num
            ),
            ARRAY_A
        );

        $first  = ! empty( $rivals[0] ) ? $rivals[0] : array();
        $second = ! empty( $rivals[1] ) ? $rivals[1] : array();
        $film_id = trim( (string) ( $first['film_id'] ?? '' ) );

        $visual = array();
        if ( $film_id !== '' && $aat && method_exists( $aat, 'get_title_visual_package' ) ) {
            $visual = $aat->get_title_visual_package( $film_id, 'medium_large' );
        }

        $result = array(
            'kicker'        => 'Oscar Rivalries',
            'title'         => sprintf( '%s vs. %s at the %s',
                trim( (string) ( $first['film'] ?? 'Film A' ) ),
                trim( (string) ( $second['film'] ?? 'Film B' ) ),
                $ceremony_label
            ),
            'copy'          => sprintf( '%s led with %s nominations (%s wins) while %s earned %s nominations (%s wins). A ceremony worth revisiting.',
                trim( (string) ( $first['film'] ?? 'The front-runner' ) ),
                number_format_i18n( intval( $first['noms'] ?? 0 ) ),
                number_format_i18n( intval( $first['wins'] ?? 0 ) ),
                trim( (string) ( $second['film'] ?? 'the challenger' ) ),
                number_format_i18n( intval( $second['noms'] ?? 0 ) ),
                number_format_i18n( intval( $second['wins'] ?? 0 ) )
            ),
            'stats'         => array(
                array( 'label' => trim( (string) ( $first['film'] ?? 'Film A' ) ), 'value' => sprintf( '%s noms / %s wins', number_format_i18n( intval( $first['noms'] ?? 0 ) ), number_format_i18n( intval( $first['wins'] ?? 0 ) ) ) ),
                array( 'label' => trim( (string) ( $second['film'] ?? 'Film B' ) ), 'value' => sprintf( '%s noms / %s wins', number_format_i18n( intval( $second['noms'] ?? 0 ) ), number_format_i18n( intval( $second['wins'] ?? 0 ) ) ) ),
                array( 'label' => 'Ceremony', 'value' => $ceremony_label ),
            ),
            'featured_film' => array(
                'title'   => trim( (string) ( $first['film'] ?? '' ) ),
                'film_id' => $film_id,
                'visual'  => $visual,
                'url'     => $ceremony_url,
            ),
            'url'           => $ceremony_url,
        );

    } else {
        // --- "Ceremony Spotlight" — random historical ceremony + BP winner + stats ---
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $random_ceremony = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT DISTINCT ceremony, year FROM {$table} ORDER BY RAND(%d) LIMIT 1",
                $day
            ),
            ARRAY_A
        );

        if ( empty( $random_ceremony ) ) {
            return array();
        }

        $ceremony_num   = intval( $random_ceremony['ceremony'] );
        $ceremony_label = ( $aat && method_exists( $aat, 'ordinal' ) ) ? $aat->ordinal( $ceremony_num ) . ' Academy Awards' : sprintf( 'Ceremony %d', $ceremony_num );
        $ceremony_url   = ( $aat && method_exists( $aat, 'get_ceremony_url' ) ) ? $aat->get_ceremony_url( $ceremony_num ) : home_url( '/oscars/ceremony/' . $ceremony_num . '/' );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $bp_winner = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT film, film_id FROM {$table} WHERE ceremony = %d AND category IN ({$bp_categories}) AND winner = 1 LIMIT 1",
                $ceremony_num
            ),
            ARRAY_A
        );

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $ceremony_stats = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT COUNT(*) AS total_noms, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS total_wins, COUNT(DISTINCT canonical_category) AS categories FROM {$table} WHERE ceremony = %d",
                $ceremony_num
            ),
            ARRAY_A
        );

        $film    = trim( (string) ( $bp_winner['film'] ?? 'Unknown' ) );
        $film_id = trim( (string) ( $bp_winner['film_id'] ?? '' ) );
        $visual  = array();
        if ( $film_id !== '' && $aat && method_exists( $aat, 'get_title_visual_package' ) ) {
            $visual = $aat->get_title_visual_package( $film_id, 'medium_large' );
        }

        $result = array(
            'kicker'        => 'Ceremony Spotlight',
            'title'         => $ceremony_label,
            'copy'          => sprintf( '%s won Best Picture at the %s, a ceremony with %s categories, %s nominees, and %s winners.',
                $film,
                $ceremony_label,
                number_format_i18n( intval( $ceremony_stats['categories'] ?? 0 ) ),
                number_format_i18n( intval( $ceremony_stats['total_noms'] ?? 0 ) ),
                number_format_i18n( intval( $ceremony_stats['total_wins'] ?? 0 ) )
            ),
            'stats'         => array(
                array( 'label' => 'Best Picture', 'value' => $film ),
                array( 'label' => 'Year', 'value' => trim( (string) ( $random_ceremony['year'] ?? '' ) ) ),
                array( 'label' => 'Categories', 'value' => number_format_i18n( intval( $ceremony_stats['categories'] ?? 0 ) ) ),
                array( 'label' => 'Winners', 'value' => number_format_i18n( intval( $ceremony_stats['total_wins'] ?? 0 ) ) ),
            ),
            'featured_film' => array(
                'title'   => $film,
                'film_id' => $film_id,
                'visual'  => $visual,
                'url'     => $film_id !== '' ? home_url( '/oscars/title/' . $film_id . '/' ) : $ceremony_url,
            ),
            'url'           => $ceremony_url,
        );
    }

    if ( ! empty( $result ) ) {
        set_transient( $cache_key, $result, 12 * HOUR_IN_SECONDS );
    }

    return $result;
}

/* ========================================
   FEATURE 2: DEEP CUT STATS
   ======================================== */

/**
 * Build the homepage Deep Cut Stats section.
 * Queries the database for 4 interesting stats, rotating daily using date('z').
 * Cached as a 24-hour transient.
 */
function lunara_get_home_deep_cuts() {
    $cache_key = 'lunara_home_deep_cuts_v1';
    $cached    = get_transient( $cache_key );

    if ( is_array( $cached ) && ! empty( $cached ) ) {
        return $cached;
    }

    $table = lunara_awards_table_name();
    if ( $table === '' ) {
        return array();
    }

    global $wpdb;
    $aat = lunara_get_oscars_plugin();
    $day = intval( date( 'z' ) );

    // Build a pool of stats, then pick 4 based on the day.
    $pool = array();

    // 1. Most wins by a single film.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $most_wins_film = $wpdb->get_row(
        "SELECT film, film_id, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS wins FROM {$table} WHERE film_id != '' GROUP BY film_id ORDER BY wins DESC LIMIT 1",
        ARRAY_A
    );
    if ( ! empty( $most_wins_film ) ) {
        $pool[] = array(
            'label'   => 'Most Wins by a Single Film',
            'value'   => number_format_i18n( intval( $most_wins_film['wins'] ) ),
            'context' => trim( (string) $most_wins_film['film'] ),
            'url'     => ! empty( $most_wins_film['film_id'] ) ? home_url( '/oscars/title/' . $most_wins_film['film_id'] . '/' ) : home_url( '/oscars/' ),
        );
    }

    // 2. Total unique films in database.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $unique_films = intval( $wpdb->get_var( "SELECT COUNT(DISTINCT film_id) FROM {$table} WHERE film_id != ''" ) );
    if ( $unique_films > 0 ) {
        $pool[] = array(
            'label'   => 'Unique Films in the Ledger',
            'value'   => number_format_i18n( $unique_films ),
            'context' => 'Every nominated and winning film, tracked',
            'url'     => home_url( '/oscars/' ),
        );
    }

    // 3. Total unique people in database.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $unique_people = intval( $wpdb->get_var( "SELECT COUNT(DISTINCT name) FROM {$table} WHERE name != ''" ) );
    if ( $unique_people > 0 ) {
        $pool[] = array(
            'label'   => 'Unique People Nominated',
            'value'   => number_format_i18n( $unique_people ),
            'context' => 'Actors, directors, writers, and craftspeople',
            'url'     => home_url( '/oscars/' ),
        );
    }

    // 4. Most nominated film without winning Best Picture.
    $bp_categories = "'BEST PICTURE','BEST MOTION PICTURE','OUTSTANDING PICTURE','OUTSTANDING PRODUCTION','OUTSTANDING MOTION PICTURE','UNIQUE AND ARTISTIC PICTURE'";
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $most_nom_no_bp = $wpdb->get_row(
        "SELECT film, film_id, COUNT(*) AS noms FROM {$table}
         WHERE film_id != '' AND film_id NOT IN (
             SELECT film_id FROM {$table} WHERE category IN ({$bp_categories}) AND winner = 1 AND film_id != ''
         )
         GROUP BY film_id ORDER BY noms DESC LIMIT 1",
        ARRAY_A
    );
    if ( ! empty( $most_nom_no_bp ) ) {
        $pool[] = array(
            'label'   => 'Most Nominated Without Best Picture',
            'value'   => number_format_i18n( intval( $most_nom_no_bp['noms'] ) ) . ' noms',
            'context' => trim( (string) $most_nom_no_bp['film'] ),
            'url'     => ! empty( $most_nom_no_bp['film_id'] ) ? home_url( '/oscars/title/' . $most_nom_no_bp['film_id'] . '/' ) : home_url( '/oscars/' ),
        );
    }

    // 5. Most nominated person without a win.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $most_nom_no_win = $wpdb->get_row(
        "SELECT name, COUNT(*) AS noms FROM {$table} WHERE name != '' GROUP BY name HAVING SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) = 0 ORDER BY noms DESC LIMIT 1",
        ARRAY_A
    );
    if ( ! empty( $most_nom_no_win ) ) {
        $pool[] = array(
            'label'   => 'Most Nominated Without a Win',
            'value'   => number_format_i18n( intval( $most_nom_no_win['noms'] ) ) . ' noms',
            'context' => trim( (string) $most_nom_no_win['name'] ),
            'url'     => home_url( '/oscars/' ),
        );
    }

    // 6. Director with most Best Picture wins.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
    $top_director = $wpdb->get_row(
        "SELECT name, COUNT(*) AS wins FROM {$table} WHERE canonical_category = 'DIRECTING' AND winner = 1 AND name != '' GROUP BY name ORDER BY wins DESC LIMIT 1",
        ARRAY_A
    );
    if ( ! empty( $top_director ) ) {
        $pool[] = array(
            'label'   => 'Most Best Director Wins',
            'value'   => number_format_i18n( intval( $top_director['wins'] ) ),
            'context' => trim( (string) $top_director['name'] ),
            'url'     => home_url( '/oscars/' ),
        );
    }

    // 7. Total ceremonies.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $total_ceremonies = intval( $wpdb->get_var( "SELECT COUNT(DISTINCT ceremony) FROM {$table}" ) );
    if ( $total_ceremonies > 0 ) {
        $pool[] = array(
            'label'   => 'Ceremonies in the Ledger',
            'value'   => number_format_i18n( $total_ceremonies ),
            'context' => 'From the 1st Academy Awards to the latest',
            'url'     => home_url( '/oscars/' ),
        );
    }

    // 8. Most competitive ceremony (highest ratio of nominees to winners).
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $competitive = $wpdb->get_row(
        "SELECT ceremony, COUNT(*) AS total, SUM(CASE WHEN winner = 1 THEN 1 ELSE 0 END) AS wins FROM {$table} GROUP BY ceremony HAVING wins > 0 ORDER BY (total / wins) DESC LIMIT 1",
        ARRAY_A
    );
    if ( ! empty( $competitive ) ) {
        $comp_ceremony = intval( $competitive['ceremony'] );
        $comp_label    = ( $aat && method_exists( $aat, 'ordinal' ) ) ? $aat->ordinal( $comp_ceremony ) : (string) $comp_ceremony;
        $pool[] = array(
            'label'   => 'Most Competitive Ceremony',
            'value'   => sprintf( '%s:%s', number_format_i18n( intval( $competitive['total'] ) ), number_format_i18n( intval( $competitive['wins'] ) ) ),
            'context' => sprintf( '%s ceremony (nominees to winners)', $comp_label ),
            'url'     => ( $aat && method_exists( $aat, 'get_ceremony_url' ) ) ? $aat->get_ceremony_url( $comp_ceremony ) : home_url( '/oscars/ceremony/' . $comp_ceremony . '/' ),
        );
    }

    // 9. Person with longest span between first and last nomination.
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $longest_span = $wpdb->get_row(
        "SELECT name, MIN(ceremony) AS first_nom, MAX(ceremony) AS last_nom, (MAX(ceremony) - MIN(ceremony)) AS span FROM {$table} WHERE name != '' GROUP BY name HAVING COUNT(*) >= 2 ORDER BY span DESC LIMIT 1",
        ARRAY_A
    );
    if ( ! empty( $longest_span ) && intval( $longest_span['span'] ) > 0 ) {
        $pool[] = array(
            'label'   => 'Longest Career Span',
            'value'   => number_format_i18n( intval( $longest_span['span'] ) ) . ' ceremonies',
            'context' => trim( (string) $longest_span['name'] ),
            'url'     => home_url( '/oscars/' ),
        );
    }

    // 10. Category with most nominees per year (on average).
    // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
    $busiest_cat = $wpdb->get_row(
        "SELECT canonical_category, COUNT(*) AS total, COUNT(DISTINCT ceremony) AS ceremonies FROM {$table} WHERE canonical_category != '' GROUP BY canonical_category HAVING ceremonies >= 5 ORDER BY (total / ceremonies) DESC LIMIT 1",
        ARRAY_A
    );
    if ( ! empty( $busiest_cat ) ) {
        $cat_label = ( $aat && method_exists( $aat, 'format_category_display' ) ) ? $aat->format_category_display( $busiest_cat['canonical_category'] ) : $busiest_cat['canonical_category'];
        $avg = round( intval( $busiest_cat['total'] ) / max( 1, intval( $busiest_cat['ceremonies'] ) ), 1 );
        $pool[] = array(
            'label'   => 'Most Nominees Per Year (Avg)',
            'value'   => number_format( $avg, 1 ),
            'context' => $cat_label,
            'url'     => ( $aat && method_exists( $aat, 'get_category_url' ) ) ? $aat->get_category_url( $busiest_cat['canonical_category'] ) : home_url( '/oscars/' ),
        );
    }

    if ( count( $pool ) < 4 ) {
        return array();
    }

    // Deterministic daily rotation: pick 4 stats based on day of year.
    $pool_count = count( $pool );
    $start      = $day % $pool_count;
    $selected   = array();
    for ( $i = 0; $i < 4; $i++ ) {
        $selected[] = $pool[ ( $start + $i ) % $pool_count ];
    }

    set_transient( $cache_key, $selected, DAY_IN_SECONDS );

    return $selected;
}

function lunara_invalidate_review_query_caches( $post_id = 0 ) {
    if ( $post_id && get_post_type( $post_id ) !== 'review' ) {
        return;
    }

    delete_transient( 'lunara_home_oscars_snapshot_v1' );
    delete_transient( 'lunara_home_oscars_snapshot_v2' );
    delete_transient( 'lunara_home_database_spotlight_v1' );
    delete_transient( 'lunara_home_ledger_story_cards_v1' );
    delete_transient( 'lunara_home_ledger_story_cards_v2' );
    delete_transient( 'lunara_home_oscar_spotlight_v1' );
    delete_transient( 'lunara_home_deep_cuts_v1' );

    $counts = array( 6, 8, 9 );
    $groups = array( 'featured_reviews', 'ledger_highlights', 'latest_reviews' );

    foreach ( $groups as $group ) {
        foreach ( $counts as $count ) {
            delete_transient( sprintf( 'lunara_%s_%d_v1', $group, $count ) );
        }
    }
}

/**
 * Featured review query.
 */
function lunara_featured_reviews_query( $count = 8 ) {
    $post_ids = lunara_cached_review_ids(
        'featured_reviews',
        $count,
        array(
            'tag' => 'featured',
        )
    );

    return lunara_reviews_query_from_ids( $post_ids );
}

/**
 * Ledger highlights query.
 */
function lunara_ledger_highlights_query( $count = 6 ) {
    $post_ids = lunara_cached_review_ids(
        'ledger_highlights',
        $count,
        array(
            'tag' => 'oscar-ledger',
        )
    );

    return lunara_reviews_query_from_ids( $post_ids );
}

/**
 * Latest review query.
 */
function lunara_latest_reviews_query( $count = 9 ) {
    $post_ids = lunara_cached_review_ids( 'latest_reviews', $count, array() );

    return lunara_reviews_query_from_ids( $post_ids );
}

/**
 * Latest reviews that are explicitly connected to an Oscars title page.
 */
function lunara_oscars_linked_reviews_query( $count = 4 ) {
    $count = max( 1, intval( $count ) );

    // First priority: published reviews whose IMDb ID appears in the Academy Awards table.
    global $wpdb;
    $awards_table = $wpdb->prefix . 'academy_awards';
    $oscar_ids    = array();

    if ( $wpdb->get_var( "SHOW TABLES LIKE '{$awards_table}'" ) === $awards_table ) {
        // Get IMDb IDs of reviewed Oscar-nominated films (rotate daily via OFFSET).
        $day_offset = intval( date( 'z' ) ) % 20; // rotate through the pool
        $oscar_imdb = $wpdb->get_col( $wpdb->prepare(
            "SELECT DISTINCT pm.post_id
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id AND p.post_type = 'review' AND p.post_status = 'publish'
             INNER JOIN {$awards_table} aa ON aa.film_id = pm.meta_value AND aa.film_id != ''
             WHERE pm.meta_key = '_lunara_imdb_title_id' AND pm.meta_value != ''
             GROUP BY pm.post_id
             ORDER BY p.post_date DESC
             LIMIT %d OFFSET %d",
            $count * 3, // fetch extra so rotation works
            $day_offset
        ) );

        if ( ! empty( $oscar_imdb ) ) {
            $oscar_ids = array_map( 'intval', array_slice( $oscar_imdb, 0, $count ) );
        }
    }

    if ( ! empty( $oscar_ids ) ) {
        $query = new WP_Query(
            array(
                'post_type'              => 'review',
                'post_status'            => 'publish',
                'post__in'               => $oscar_ids,
                'posts_per_page'         => $count,
                'orderby'                => 'post__in',
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_meta_cache' => true,
                'update_post_term_cache' => true,
            )
        );
    } else {
        // Fallback: any published review with an IMDb ID.
        $query = new WP_Query(
            array(
                'post_type'              => 'review',
                'post_status'            => 'publish',
                'posts_per_page'         => $count,
                'no_found_rows'          => true,
                'ignore_sticky_posts'    => true,
                'update_post_meta_cache' => true,
                'update_post_term_cache' => true,
                'meta_query'             => array(
                    'relation' => 'AND',
                    array(
                        'key'     => '_lunara_imdb_title_id',
                        'compare' => 'EXISTS',
                    ),
                    array(
                        'key'     => '_lunara_imdb_title_id',
                        'value'   => '',
                        'compare' => '!=',
                    ),
                ),
            )
        );
    }

    if ( function_exists( 'lunara_prime_review_card_caches' ) && ! empty( $query->posts ) ) {
        lunara_prime_review_card_caches( wp_list_pluck( $query->posts, 'ID' ) );
    }

    return $query;
}

/**
 * True when rendering the dedicated Oscars portal front door.
 */
function lunara_is_oscars_portal_page() {
    return ! is_admin() && is_page( 'oscars' );
}

/**
 * Build the dedicated Oscars portal markup for the /oscars/ page.
 */
function lunara_render_oscars_portal_markup() {
    $aat                = function_exists( 'lunara_get_oscars_plugin' ) ? lunara_get_oscars_plugin() : null;
    $snapshot           = function_exists( 'lunara_get_home_oscars_snapshot' ) ? lunara_get_home_oscars_snapshot() : array();
    $database_spotlight = function_exists( 'lunara_get_home_database_spotlight' ) ? lunara_get_home_database_spotlight() : array();
    $deep_cuts          = function_exists( 'lunara_get_home_deep_cuts' ) ? lunara_get_home_deep_cuts() : array();
    $linked_reviews     = function_exists( 'lunara_oscars_linked_reviews_query' ) ? lunara_oscars_linked_reviews_query( 4 ) : new WP_Query();

    $hero_kicker       = lunara_theme_mod_text( 'lunara_oscars_portal_kicker', 'The Lunara Oscar Ledger' );
    $hero_title        = lunara_theme_mod_text( 'lunara_oscars_portal_title', 'Academy Awards history, treated like a living editorial system.' );
    $hero_copy         = lunara_theme_mod_text( 'lunara_oscars_portal_copy', 'Move from a winning film to the people behind it, from one category to the ceremony around it, and from the ledger straight into Lunara criticism without ever hitting a dead wall of data.' );
    $explore_heading   = lunara_theme_mod_text( 'lunara_oscars_portal_explore_heading', 'Start anywhere in the ledger.' );
    $reviews_heading   = lunara_theme_mod_text( 'lunara_oscars_portal_reviews_heading', 'Reviews Inside the Ledger' );
    $deep_cuts_heading = lunara_theme_mod_text( 'lunara_oscars_portal_deep_cuts_heading', 'Oscar Deep Cuts' );

    $best_picture      = is_array( $snapshot['best_picture'] ?? null ) ? $snapshot['best_picture'] : array();
    $best_visual       = is_array( $best_picture['visual'] ?? null ) ? $best_picture['visual'] : array();
    $spotlights        = array_slice( (array) ( $snapshot['spotlights'] ?? array() ), 0, 6 );
    $title_cards       = array_slice( (array) ( $database_spotlight['cards'] ?? array() ), 0, 5 );

    $database_url      = ( $aat && method_exists( $aat, 'get_database_url' ) ) ? $aat->get_database_url() : trim( (string) ( $snapshot['database_url'] ?? ( $database_spotlight['database_url'] ?? home_url( '/oscars/' ) ) ) );
    $categories_url    = ( $aat && method_exists( $aat, 'get_categories_index_url' ) ) ? $aat->get_categories_index_url() : trim( (string) ( $snapshot['categories_url'] ?? ( $database_spotlight['categories_url'] ?? home_url( '/oscars/categories/' ) ) ) );
    $ceremony_url      = ( $aat && method_exists( $aat, 'get_ceremony_url' ) && ! empty( $snapshot['ceremony'] ) ) ? $aat->get_ceremony_url( intval( $snapshot['ceremony'] ) ) : trim( (string) ( $snapshot['ceremony_url'] ?? home_url( '/oscars/ceremony/' ) ) );
    $ceremony_label    = trim( (string) ( $snapshot['ceremony_label'] ?? 'Latest Ceremony' ) );
    $year_label        = trim( (string) ( $snapshot['year_label'] ?? '' ) );
    $about_url         = ( $aat && method_exists( $aat, 'get_about_url' ) ) ? $aat->get_about_url() : home_url( '/oscars/about/' );
    $ceremonies_url    = ( $aat && method_exists( $aat, 'get_ceremonies_index_url' ) ) ? $aat->get_ceremonies_index_url() : home_url( '/oscars/ceremonies/' );
    $hero_backdrop_url = trim( (string) ( $best_visual['backdrop_url'] ?? '' ) );
    $hero_style        = '';

    if ( '' !== $hero_backdrop_url ) {
        $hero_style = "background-image: linear-gradient(120deg, rgba(7,16,27,.92) 0%, rgba(7,16,27,.86) 48%, rgba(7,16,27,.97) 100%), url('" . esc_url( $hero_backdrop_url ) . "'); background-size: cover; background-position: center;";
    }

    $hero_title_card = array();
    if ( ! empty( $best_picture ) ) {
        $hero_title_card = array(
            'title'     => trim( (string) ( $best_picture['film'] ?? '' ) ),
            'url'       => trim( (string) ( $best_picture['film_url'] ?? $ceremony_url ) ),
            'visual'    => $best_visual,
            'eyebrow'   => 'Latest Best Picture',
            'meta_line' => trim( (string) $ceremony_label . ( $year_label !== '' ? ' / ' . $year_label : '' ) ),
            'body'      => trim( (string) ( $snapshot['summary'] ?? $snapshot['winner_record'] ?? '' ) ),
        );
    } elseif ( ! empty( $title_cards[0] ) ) {
        $hero_title_card = array(
            'title'     => trim( (string) ( $title_cards[0]['title'] ?? '' ) ),
            'url'       => trim( (string) ( $title_cards[0]['url'] ?? $database_url ) ),
            'visual'    => is_array( $title_cards[0]['visual'] ?? null ) ? $title_cards[0]['visual'] : array(),
            'eyebrow'   => 'Featured Portal Entry',
            'meta_line' => trim( (string) ( $title_cards[0]['categories_line'] ?? '' ) ),
            'body'      => 'Open a poster-led entry point into the ledger and move outward into categories, ceremonies, and related people pages.',
        );
    }

    $portal_stats = array(
        array(
            'label' => 'Ceremony',
            'value' => $ceremony_label,
        ),
        array(
            'label' => 'Year',
            'value' => $year_label !== '' ? $year_label : 'Live',
        ),
        array(
            'label' => 'Rows',
            'value' => number_format_i18n( intval( $database_spotlight['records_total'] ?? 0 ) ),
        ),
        array(
            'label' => 'Categories',
            'value' => number_format_i18n( intval( $database_spotlight['categories_total'] ?? 0 ) ),
        ),
    );

    // Backdrop images keyed by portal card — iconic Oscar titles.
    $portal_backdrop_map = array(
        'Ceremonies' => 'tt7286456',  // Joker
        'Categories' => 'tt1375666',  // Inception
        'Ledger'     => 'tt0111161',  // The Shawshank Redemption
        'About'      => 'tt0068646',  // The Godfather
    );
    $portal_backdrops = array();
    if ( class_exists( 'Academy_Awards_Table' ) ) {
        $aat_inst = Academy_Awards_Table::get_instance();
        if ( $aat_inst && method_exists( $aat_inst, 'get_title_visual_package' ) ) {
            foreach ( $portal_backdrop_map as $key => $imdb ) {
                $vis = $aat_inst->get_title_visual_package( $imdb, 'large' );
                $portal_backdrops[ $key ] = $vis['backdrop_url'] ?? '';
            }
        }
    }

    $portal_links = array(
        array(
            'kicker'   => 'Ceremonies',
            'title'    => 'Move through the awards one ceremony at a time.',
            'copy'     => 'Jump into the latest winners, older races, and the shape of each year.',
            'url'      => $ceremonies_url,
            'backdrop' => $portal_backdrops['Ceremonies'] ?? '',
        ),
        array(
            'kicker'   => 'Categories',
            'title'    => 'Track the history of every Oscar category.',
            'copy'     => 'Go from Best Picture to supporting races, crafts, and international wins.',
            'url'      => $categories_url,
            'backdrop' => $portal_backdrops['Categories'] ?? '',
        ),
        array(
            'kicker'   => 'Ledger',
            'title'    => 'Search the full ledger directly.',
            'copy'     => 'Use the full ledger when you want filters, tables, and the entire record at once.',
            'url'      => $database_url,
            'backdrop' => $portal_backdrops['Ledger'] ?? '',
        ),
        array(
            'kicker'   => 'About',
            'title'    => 'See how Lunara structures the archive.',
            'copy'     => 'Read the editorial and data philosophy behind the Oscar ledger.',
            'url'      => $about_url,
            'backdrop' => $portal_backdrops['About'] ?? '',
        ),
    );

    ob_start();
    ?>
    <main id="primary" class="site-main lunara-oscars-portal">
        <?php if ( empty( $snapshot ) && empty( $database_spotlight ) ) : ?>
            <section class="lunara-home-section lunara-archive-hero">
                <p class="lunara-archive-hero-kicker"><?php echo esc_html( $hero_kicker ); ?></p>
                <h1 class="lunara-archive-hero-title"><?php echo esc_html( get_the_title() ); ?></h1>
                <p class="lunara-archive-hero-copy"><?php esc_html_e( 'The Oscars portal is ready for data, but the ledger data layer is not currently returning any live material.', 'lunara-film' ); ?></p>
            </section>
        <?php else : ?>
            <section class="lunara-home-section lunara-oscars-portal-hero"<?php if ( '' !== $hero_style ) : ?> style="<?php echo esc_attr( $hero_style ); ?>"<?php endif; ?>>
                <div class="lunara-oscars-portal-hero-grid">
                    <div class="lunara-oscars-portal-copy">
                        <p class="lunara-home-section-kicker"><?php echo esc_html( $hero_kicker ); ?></p>
                        <h1 class="lunara-home-hero-title"><?php echo esc_html( $hero_title ); ?></h1>
                        <p class="lunara-home-hero-copy"><?php echo esc_html( $hero_copy ); ?></p>

                        <div class="lunara-oscars-portal-actions">
                            <a class="lunara-button lunara-button-primary" href="<?php echo esc_url( $ceremony_url ); ?>">Latest Ceremony</a>
                            <a class="lunara-button lunara-button-secondary" href="<?php echo esc_url( $database_url ); ?>">Open Full Ledger</a>
                            <a class="lunara-button-ghost" href="<?php echo esc_url( $categories_url ); ?>">Browse Categories</a>
                        </div>

                        <div class="lunara-oscars-portal-stat-grid">
                            <?php foreach ( $portal_stats as $stat ) : ?>
                                <div class="lunara-oscars-portal-stat">
                                    <span class="lunara-oscars-portal-stat-label"><?php echo esc_html( $stat['label'] ); ?></span>
                                    <strong class="lunara-oscars-portal-stat-value"><?php echo esc_html( $stat['value'] ); ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <?php if ( ! empty( $hero_title_card ) ) : ?>
                        <a class="lunara-oscars-portal-feature-card" href="<?php echo esc_url( $hero_title_card['url'] ?? $database_url ); ?>">
                            <div class="lunara-oscars-portal-feature-poster">
                                <?php if ( ! empty( $hero_title_card['visual']['poster_html'] ) ) : ?>
                                    <?php echo $hero_title_card['visual']['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php elseif ( ! empty( $hero_title_card['visual']['poster_url'] ) ) : ?>
                                    <img src="<?php echo esc_url( $hero_title_card['visual']['poster_url'] ); ?>" alt="<?php echo esc_attr( $hero_title_card['title'] ); ?>" loading="lazy" decoding="async" />
                                <?php elseif ( ! empty( $hero_title_card['visual']['card_fallback_html'] ) ) : ?>
                                    <?php echo $hero_title_card['visual']['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                <?php else : ?>
                                    <div class="aat-filmography-poster-placeholder"><div class="aat-fallback-inner"><div class="aat-fallback-kicker">Oscar Portal</div><div class="aat-fallback-title small"><?php echo esc_html( $hero_title_card['title'] ); ?></div></div></div>
                                <?php endif; ?>
                            </div>
                            <div class="lunara-oscars-portal-feature-copy">
                                <p class="lunara-oscars-portal-feature-kicker"><?php echo esc_html( $hero_title_card['eyebrow'] ); ?></p>
                                <h2><?php echo esc_html( $hero_title_card['title'] ); ?></h2>
                                <?php if ( '' !== trim( (string) $hero_title_card['meta_line'] ) ) : ?>
                                    <p class="lunara-oscars-portal-feature-meta"><?php echo esc_html( $hero_title_card['meta_line'] ); ?></p>
                                <?php endif; ?>
                                <?php if ( '' !== trim( (string) $hero_title_card['body'] ) ) : ?>
                                    <p class="lunara-oscars-portal-feature-body"><?php echo esc_html( $hero_title_card['body'] ); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    <?php endif; ?>
                </div>
            </section>

            <section class="lunara-home-section lunara-oscars-portal-links-section">
                <div class="lunara-home-section-header">
                    <div>
                        <p class="lunara-home-section-kicker">Explore the Portal</p>
                        <h2 class="lunara-home-section-title"><?php echo esc_html( $explore_heading ); ?></h2>
                    </div>
                    <p class="lunara-home-section-summary"><?php echo esc_html( $snapshot['winner_record'] ?? 'The Oscars portal now points readers into ceremonies, categories, and title pages instead of leaving the ledger isolated from the rest of Lunara.' ); ?></p>
                </div>

                <div class="lunara-oscars-portal-link-grid">
                    <?php foreach ( $portal_links as $portal_link ) :
                        $bd_url   = trim( (string) ( $portal_link['backdrop'] ?? '' ) );
                        $bd_style = '' !== $bd_url ? 'background-image:url(' . esc_url( $bd_url ) . ')' : '';
                        $bd_class = '' !== $bd_url ? ' has-backdrop' : '';
                    ?>
                        <a class="lunara-oscars-portal-link-card<?php echo $bd_class; ?>" href="<?php echo esc_url( $portal_link['url'] ); ?>"<?php if ( '' !== $bd_style ) : ?> style="<?php echo esc_attr( $bd_style ); ?>"<?php endif; ?>>
                            <p class="lunara-oscars-portal-link-kicker"><?php echo esc_html( $portal_link['kicker'] ); ?></p>
                            <h3><?php echo esc_html( $portal_link['title'] ); ?></h3>
                            <p><?php echo esc_html( $portal_link['copy'] ); ?></p>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>

            <?php if ( ! empty( $spotlights ) ) : ?>
                <section class="lunara-home-section lunara-oscars-portal-spotlights">
                    <div class="lunara-home-section-header">
                        <div>
                            <p class="lunara-home-section-kicker"><?php echo esc_html( $ceremony_label ); ?></p>
                            <h2 class="lunara-home-section-title">Latest Ceremony, category by category.</h2>
                        </div>
                        <p class="lunara-home-section-summary"><?php echo esc_html( $snapshot['summary'] ?? 'The latest winners now connect directly to film pages, category pages, and the wider Lunara archive.' ); ?></p>
                    </div>

                    <div class="lunara-oscars-portal-spotlight-grid">
                        <?php foreach ( $spotlights as $spotlight ) :
                            $sl_visual = is_array( $spotlight['visual'] ?? null ) ? $spotlight['visual'] : array();
                            $sl_winner = intval( $spotlight['winner'] ?? 0 );
                        ?>
                            <a class="lunara-oscars-portal-spotlight-card<?php echo $sl_winner ? ' is-winner' : ''; ?>" href="<?php echo esc_url( $spotlight['url'] ?? $database_url ); ?>">
                                <?php if ( ! empty( $sl_visual['poster_html'] ) ) : ?>
                                    <div class="lunara-oscars-spotlight-poster">
                                        <?php echo $sl_visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    </div>
                                <?php elseif ( ! empty( $sl_visual['poster_url'] ) ) : ?>
                                    <div class="lunara-oscars-spotlight-poster">
                                        <img src="<?php echo esc_url( $sl_visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $spotlight['primary_label'] ?? '' ); ?>" loading="lazy" decoding="async" />
                                    </div>
                                <?php else : ?>
                                    <div class="lunara-oscars-spotlight-poster lunara-oscars-spotlight-poster--fallback">
                                        <span><?php echo esc_html( $spotlight['primary_label'] ?? '' ); ?></span>
                                    </div>
                                <?php endif; ?>
                                <div class="lunara-oscars-spotlight-card-copy">
                                    <p class="lunara-oscars-portal-spotlight-category"><?php echo esc_html( $spotlight['category_label'] ?? 'Category' ); ?></p>
                                    <h3><?php echo esc_html( $spotlight['primary_label'] ?? $spotlight['film'] ?? '' ); ?></h3>
                                    <?php if ( ! empty( $spotlight['secondary_label'] ) ) : ?>
                                        <p class="lunara-oscars-portal-spotlight-secondary"><?php echo esc_html( $spotlight['secondary_label'] ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $spotlight['year'] ) ) : ?>
                                        <p class="lunara-oscars-portal-spotlight-meta"><?php echo esc_html( $spotlight['year'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ( ! empty( $title_cards ) ) : ?>
                <section class="lunara-home-section lunara-oscars-portal-titles">
                    <div class="lunara-home-section-header">
                        <div>
                            <p class="lunara-home-section-kicker">Poster-Led Entry Points</p>
                            <h2 class="lunara-home-section-title">Open the ledger through the films themselves.</h2>
                        </div>
                        <p class="lunara-home-section-summary">These title pages are where posters, category history, review links, and ceremony context all start to braid together.</p>
                    </div>

                    <div class="lunara-oscars-portal-title-grid">
                        <?php foreach ( $title_cards as $card ) : ?>
                            <?php $card_visual = is_array( $card['visual'] ?? null ) ? $card['visual'] : array(); ?>
                            <a class="lunara-oscars-portal-title-card" href="<?php echo esc_url( $card['url'] ?? $database_url ); ?>">
                                <div class="lunara-oscars-portal-title-media">
                                    <?php if ( ! empty( $card_visual['poster_html'] ) ) : ?>
                                        <?php echo $card_visual['poster_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php elseif ( ! empty( $card_visual['poster_url'] ) ) : ?>
                                        <img src="<?php echo esc_url( $card_visual['poster_url'] ); ?>" alt="<?php echo esc_attr( $card['title'] ?? 'Oscar title' ); ?>" loading="lazy" decoding="async" />
                                    <?php elseif ( ! empty( $card_visual['card_fallback_html'] ) ) : ?>
                                        <?php echo $card_visual['card_fallback_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                                    <?php else : ?>
                                        <div class="aat-filmography-poster-placeholder"><div class="aat-fallback-inner"><div class="aat-fallback-kicker">Oscar Title</div><div class="aat-fallback-title small"><?php echo esc_html( $card['title'] ?? '' ); ?></div></div></div>
                                    <?php endif; ?>
                                </div>
                                <div class="lunara-oscars-portal-title-copy">
                                    <h3><?php echo esc_html( $card['title'] ?? '' ); ?></h3>
                                    <?php if ( ! empty( $card['year'] ) ) : ?>
                                        <p class="lunara-oscars-portal-title-year"><?php echo esc_html( $card['year'] ); ?></p>
                                    <?php endif; ?>
                                    <?php if ( ! empty( $card['categories_line'] ) ) : ?>
                                        <p class="lunara-oscars-portal-title-line"><?php echo esc_html( $card['categories_line'] ); ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <?php if ( $linked_reviews instanceof WP_Query && $linked_reviews->have_posts() ) : ?>
                <section class="lunara-home-section lunara-oscars-portal-reviews">
                    <div class="lunara-home-section-header">
                        <div>
                            <p class="lunara-home-section-kicker">Criticism Meets the Ledger</p>
                            <h2 class="lunara-home-section-title"><?php echo esc_html( $reviews_heading ); ?></h2>
                        </div>
                        <p class="lunara-home-section-summary">These reviews already point back into the Oscars film pages, so the writing and the data are starting to behave like one system.</p>
                    </div>

                    <div class="lunara-review-grid lunara-review-archive-grid">
                        <?php while ( $linked_reviews->have_posts() ) : $linked_reviews->the_post(); ?>
                            <?php echo lunara_render_review_grid_card( get_the_ID() ); ?>
                        <?php endwhile; ?>
                    </div>
                </section>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>

            <?php if ( ! empty( $deep_cuts ) ) : ?>
                <section class="lunara-home-section lunara-oscars-portal-deep-cuts">
                    <div class="lunara-home-section-header">
                        <div>
                            <p class="lunara-home-section-kicker">Rotating Stats</p>
                            <h2 class="lunara-home-section-title"><?php echo esc_html( $deep_cuts_heading ); ?></h2>
                        </div>
                        <p class="lunara-home-section-summary">This section keeps the history moving. It rotates through milestones, outliers, and useful context pulled straight from the ledger.</p>
                    </div>

                    <div class="lunara-oscars-portal-facts-grid">
                        <?php foreach ( $deep_cuts as $cut ) : ?>
                            <a class="lunara-oscars-portal-fact-card" href="<?php echo esc_url( $cut['url'] ?? $database_url ); ?>">
                                <p class="lunara-oscars-portal-fact-label"><?php echo esc_html( $cut['label'] ?? '' ); ?></p>
                                <strong class="lunara-oscars-portal-fact-value"><?php echo esc_html( $cut['value'] ?? '' ); ?></strong>
                                <?php if ( ! empty( $cut['context'] ) ) : ?>
                                    <p class="lunara-oscars-portal-fact-context"><?php echo esc_html( $cut['context'] ); ?></p>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <?php

    return trim( (string) ob_get_clean() );
}

/**
 * Replace the default page content with the custom Oscars portal.
 */
function lunara_replace_oscars_portal_content( $content ) {
    if ( ! lunara_is_oscars_portal_page() || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    return lunara_render_oscars_portal_markup();
}
add_filter( 'the_content', 'lunara_replace_oscars_portal_content', 20 );

/**
 * Force the Oscars portal to render through a dedicated shell.
 *
 * Some theme/plugin combinations can bypass the normal singular content flow
 * and surface archive-style shells on the /oscars/ page. Rendering directly
 * here keeps the front door stable without touching the database layer.
 */
function lunara_render_oscars_portal_direct() {
    if ( ! lunara_is_oscars_portal_page() || is_admin() || is_feed() || is_embed() || is_preview() ) {
        return;
    }

    get_header();
    ?>
    <main id="main" class="site-main">
        <?php echo lunara_render_oscars_portal_markup(); ?>
    </main>
    <?php
    get_footer();
    exit;
}
add_action( 'template_redirect', 'lunara_render_oscars_portal_direct', 1 );

/**
 * Add a body class so the portal can be styled without relying on generic page shells.
 */
function lunara_oscars_portal_body_class( $classes ) {
    if ( lunara_is_oscars_portal_page() ) {
        $classes[] = 'lunara-oscars-portal-page';
    }

    return $classes;
}
add_filter( 'body_class', 'lunara_oscars_portal_body_class' );
add_action( 'save_post_review', 'lunara_invalidate_review_query_caches', 50 );
add_action( 'deleted_post', 'lunara_invalidate_review_query_caches' );

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
 * ── Lunara Custom Footer System ──
 * A three-zone branded footer that replaces Blocksy's native footer.
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
add_action( 'wp_footer', 'lunara_render_custom_footer', 1 );

/* Suppress Blocksy's native footer so ours is the only one. */
add_filter( 'blocksy:footer:has-widgets', '__return_false' );
add_filter( 'blocksy:builder:footer:enabled', '__return_false' );

function lunara_hide_blocksy_footer_css() {
    echo '<style id="lunara-hide-blocksy-footer">.ct-footer,footer.site-footer:not(.lunara-site-footer){display:none!important;}</style>' . "\n";
}
add_action( 'wp_head', 'lunara_hide_blocksy_footer_css', 100 );

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
add_action( 'wp_footer', function() {
    if ( ! is_front_page() ) {
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
}, 99 );

/**
 * Wave 2: Image fade-in on load.
 */
add_action( 'wp_footer', function () {
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
}, 100 );

/**
 * Wave 3: Scroll-triggered reveals.
 */
add_action( 'wp_footer', function () {
    ?>
    <script>
    (function(){
        if(window.matchMedia('(prefers-reduced-motion: reduce)').matches)return;
        // Only run scroll reveals on the front page and archive/single pages — NOT on the Oscars portal or plugin pages
        var isOscarsPortal=document.body.classList.contains('lunara-oscars-portal-page');
        var isPluginPage=document.querySelector('.aat-hub-page,.aat-entity-page');
        var revealSels=[];
        var staggerSels=[];
        if(!isOscarsPortal&&!isPluginPage){
            revealSels=[
                '.lunara-home-section','.lunara-review-grid-card','.lunara-review-feature-card',
                '.lunara-poster-card','.lunara-ledger-card','.lunara-dispatch-archive-card',
                '.lunara-debrief-block','.lunara-review-related',
                '.lunara-review-single-debrief'
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
}, 101 );

/**
 * Wave 5: Oscar stats count-up animation.
 */
add_action( 'wp_footer', function () {
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
}, 102 );
