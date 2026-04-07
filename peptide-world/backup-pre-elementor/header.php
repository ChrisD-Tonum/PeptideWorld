<?php
/**
 * Peptide World — header.php
 *
 * Outputs the site <head> and sticky site header with primary navigation.
 * The nav menu (Home, Peptides, Hormone Therapy, Educational Guides, Consultation)
 * must be assigned in WordPress admin → Appearance → Menus → Primary Navigation.
 *
 * @package PeptideWorld
 */
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<a class="skip-link screen-reader-text" href="#main-content">
    <?php esc_html_e( 'Skip to content', 'peptide-world' ); ?>
</a>

<!-- =========================================================
     SITE HEADER
     Sticky header: logo/brand on the left, nav on the right.
     On mobile (≤768px) the nav collapses behind a hamburger.
     ========================================================= -->
<header id="site-header" class="site-header" role="banner">
    <div class="container">
        <div class="header-inner">

            <!-- Brand / Logo -->
            <div class="site-branding">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <p class="site-title">
                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
                            <?php bloginfo( 'name' ); ?>
                        </a>
                    </p>
                <?php endif; ?>
            </div>
            <!-- /site-branding -->

            <!-- Mobile nav toggle — visible only on small screens via CSS -->
            <button
                class="nav-toggle"
                aria-controls="primary-nav"
                aria-expanded="false"
                aria-label="<?php esc_attr_e( 'Toggle navigation menu', 'peptide-world' ); ?>"
            >
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Primary Navigation
                 Assign pages in: WP Admin → Appearance → Menus
                 Recommended menu order:
                   1. Home
                   2. Peptides
                   3. Hormone Therapy
                   4. Educational Guides
                   5. Consultation
            -->
            <nav id="primary-nav" class="primary-nav" role="navigation" aria-label="<?php esc_attr_e( 'Primary Navigation', 'peptide-world' ); ?>">
                <?php
                wp_nav_menu( [
                    'theme_location' => 'primary',
                    'menu_id'        => 'primary-menu',
                    'menu_class'     => 'primary-menu',
                    'container'      => false,
                    'fallback_cb'    => 'peptide_world_fallback_nav',
                    'depth'          => 1, // single-level nav; no dropdowns needed
                ] );
                ?>
            </nav>
            <!-- /primary-nav -->

        </div><!-- /.header-inner -->
    </div><!-- /.container -->
</header>
<!-- /site-header -->

<?php
/**
 * Fallback navigation rendered when no menu has been assigned to
 * the 'primary' location. Outputs placeholder links matching the
 * intended site structure so the header never looks broken during
 * development.
 *
 * IMPORTANT: Replace these hrefs with real page URLs or assign a
 * proper menu via WP Admin → Appearance → Menus.
 */
function peptide_world_fallback_nav() {
    ?>
    <ul class="primary-menu fallback-menu">
        <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'peptide-world' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/peptides/' ) ); ?>"><?php esc_html_e( 'Peptides', 'peptide-world' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/hormone-therapy/' ) ); ?>"><?php esc_html_e( 'Hormone Therapy', 'peptide-world' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/educational-guides/' ) ); ?>"><?php esc_html_e( 'Educational Guides', 'peptide-world' ); ?></a></li>
        <li><a href="<?php echo esc_url( home_url( '/consultation/' ) ); ?>"><?php esc_html_e( 'Consultation', 'peptide-world' ); ?></a></li>
    </ul>
    <?php
}
?>

<div id="page-wrapper" class="site">
    <main id="main-content" class="site-main" role="main">
