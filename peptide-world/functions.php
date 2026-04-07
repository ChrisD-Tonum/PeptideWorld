<?php
/**
 * Peptide World — functions.php
 *
 * Theme setup, feature declarations, script/style enqueueing,
 * and navigation menu registration.
 *
 * @package PeptideWorld
 */

defined( 'ABSPATH' ) || exit;

// ============================================================================
// THEME CONSTANTS
// ============================================================================

define( 'PEPTIDE_WORLD_VERSION', '1.0.0' );
define( 'PEPTIDE_WORLD_DIR', get_template_directory() );
define( 'PEPTIDE_WORLD_URI', get_template_directory_uri() );

// ============================================================================
// THEME SETUP
// ============================================================================

/**
 * Register all theme supports and navigation menus.
 * Hooked to after_setup_theme so it runs before WordPress init.
 */
function peptide_world_setup() {

    // Let WordPress manage the document <title> tag.
    add_theme_support( 'title-tag' );

    // Enable support for post thumbnails (featured images).
    add_theme_support( 'post-thumbnails' );

    // Navigation menus removed — Elementor Pro Theme Builder manages header/nav.
    // register_nav_menus() was here; backed up in /backup-pre-elementor/

    // Gutenberg block styles — outputs block CSS on the frontend.
    add_theme_support( 'wp-block-styles' );

    // Allow wide and full-width block alignments.
    add_theme_support( 'align-wide' );

    // Responsive embeds (iframes, videos).
    add_theme_support( 'responsive-embeds' );

    // HTML5 markup for core elements.
    add_theme_support( 'html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ] );

    // Elementor compatibility — declares the theme as Elementor-compatible.
    // This suppresses the "Hello Elementor" theme recommendation notice.
    add_theme_support( 'elementor' );

    // Custom logo support.
    add_theme_support( 'custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ] );

    // Load theme text domain for translations.
    load_theme_textdomain( 'peptide-world', PEPTIDE_WORLD_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'peptide_world_setup' );

// ============================================================================
// ENQUEUE STYLES & SCRIPTS
// ============================================================================

/**
 * Enqueue theme stylesheets and scripts.
 */
function peptide_world_enqueue_assets() {

    // Main stylesheet (style.css — contains base reset + layout).
    wp_enqueue_style(
        'peptide-world-style',
        get_stylesheet_uri(),
        [],
        PEPTIDE_WORLD_VERSION
    );

    // Additional component styles.
    wp_enqueue_style(
        'peptide-world-main',
        PEPTIDE_WORLD_URI . '/assets/css/main.css',
        [ 'peptide-world-style' ],
        PEPTIDE_WORLD_VERSION
    );

    // Google Fonts removed — Elementor Pro handles global typography.

    // Main JavaScript — handles peptide card filter/search.
    // Loaded in the footer (true) to avoid render blocking.
    wp_enqueue_script(
        'peptide-world-main',
        PEPTIDE_WORLD_URI . '/assets/js/main.js',
        [],               // no jQuery dependency — vanilla JS
        PEPTIDE_WORLD_VERSION,
        true              // load in footer
    );
}
add_action( 'wp_enqueue_scripts', 'peptide_world_enqueue_assets' );

// ============================================================================
// CONTENT WIDTH
// ============================================================================

/**
 * Set the content width in pixels.
 * Used by WordPress to constrain embedded media.
 */
function peptide_world_content_width() {
    $GLOBALS['content_width'] = apply_filters( 'peptide_world_content_width', 1200 );
}
add_action( 'after_setup_theme', 'peptide_world_content_width', 0 );

// ============================================================================
// CUSTOM PAGE TEMPLATES
// ============================================================================

/**
 * Register the peptide-database.php file as a selectable page template
 * from the WordPress admin Page Attributes panel.
 *
 * The template header comment in templates/peptide-database.php handles
 * most of this automatically; this filter ensures it appears in the list.
 */
function peptide_world_add_page_templates( $templates ) {
    $templates['templates/peptide-database.php'] = __( 'Peptide Database', 'peptide-world' );
    return $templates;
}
add_filter( 'theme_page_templates', 'peptide_world_add_page_templates' );

// ============================================================================
// EXCERPT
// ============================================================================

/**
 * Trim excerpt length for archive/search cards.
 */
function peptide_world_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'peptide_world_excerpt_length' );

/**
 * Replace default "[...]" excerpt suffix with a cleaner ellipsis.
 */
function peptide_world_excerpt_more( $more ) {
    return '&hellip;';
}
add_filter( 'excerpt_more', 'peptide_world_excerpt_more' );

// ============================================================================
// SECURITY: REMOVE WORDPRESS VERSION FROM HEAD
// ============================================================================

remove_action( 'wp_head', 'wp_generator' );
