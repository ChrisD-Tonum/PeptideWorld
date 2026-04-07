<?php
/**
 * header.php
 * Outputs <html>, <head>, and wp_head() — always runs on every page.
 * Elementor Pro Theme Builder injects the visual header (nav/logo) via
 * elementor_theme_do_location( 'header' ) below. If Elementor Pro is not
 * active or no header template is published, the block is simply skipped.
 *
 * Backup of original custom header saved in /backup-pre-elementor/header.php
 *
 * @package PeptideWorld
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
// Elementor Pro Theme Builder injects the visual header here (nav, logo, etc.).
// Falls through silently if Elementor Pro is inactive or no header template exists.
if ( function_exists( 'elementor_theme_do_location' ) ) {
    elementor_theme_do_location( 'header' );
}
?>
