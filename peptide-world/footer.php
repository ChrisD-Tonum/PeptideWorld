<?php
/**
 * footer.php
 * Outputs the Elementor Pro footer template then closes </body> and </html>.
 * wp_footer() is always called so scripts enqueued in the footer still load.
 *
 * Backup of original custom footer saved in /backup-pre-elementor/footer.php
 *
 * @package PeptideWorld
 */
?>
<?php
// Elementor Pro Theme Builder injects the visual footer here.
// Falls through silently if Elementor Pro is inactive or no footer template exists.
if ( function_exists( 'elementor_theme_do_location' ) ) {
    elementor_theme_do_location( 'footer' );
}
?>
<?php wp_footer(); ?>
</body>
</html>
