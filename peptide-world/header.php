<?php
/**
 * header.php
 * Minimal shell — outputs <html>, <head>, and wp_head() only.
 * Elementor Pro Theme Builder injects the visual header automatically
 * via wp_body_open hooks. No explicit calls needed here.
 *
 * Backup of original saved in /backup-pre-elementor/header.php
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
if ( function_exists( 'elementor_theme_do_location' ) ) {
    elementor_theme_do_location( 'header' );
}
?>
