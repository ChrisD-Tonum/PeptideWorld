<?php
/**
 * footer.php
 * Minimal shell — calls wp_footer() then closes body and html.
 * Elementor Pro Theme Builder injects the visual footer automatically
 * via wp_footer hooks. No explicit calls needed here.
 *
 * Backup of original saved in /backup-pre-elementor/footer.php
 *
 * @package PeptideWorld
 */
?>
<?php
if ( function_exists( 'elementor_theme_do_location' ) ) {
    elementor_theme_do_location( 'footer' );
}
?>
<?php wp_footer(); ?>
</body>
</html>
