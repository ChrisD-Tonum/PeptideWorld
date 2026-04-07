<?php
/**
 * Peptide World — footer.php
 *
 * Closes the #main-content and #page-wrapper divs opened in header.php,
 * then renders the site footer with brand info, a medical disclaimer,
 * and copyright notice.
 *
 * @package PeptideWorld
 */
?>

    </main><!-- /#main-content -->
</div><!-- /#page-wrapper -->

<!-- =========================================================
     SITE FOOTER
     ========================================================= -->
<footer id="site-footer" class="site-footer" role="contentinfo">
    <div class="container">
        <div class="footer-inner">

            <!-- Brand block -->
            <div class="footer-brand">
                <p class="site-title">
                    <?php bloginfo( 'name' ); ?>
                </p>
                <p class="footer-tagline">
                    <?php esc_html_e( 'Science-backed peptide education.', 'peptide-world' ); ?>
                </p>
            </div>

            <!-- Medical / legal disclaimer
                 IMPORTANT: Review this text with a qualified professional
                 before launch to ensure it meets applicable legal standards. -->
            <p class="footer-disclaimer">
                <?php esc_html_e(
                    'The information on this site is for educational purposes only and is not intended as medical advice. Consult a licensed healthcare provider before starting any peptide or hormone therapy protocol.',
                    'peptide-world'
                ); ?>
            </p>

        </div><!-- /.footer-inner -->

        <!-- Copyright -->
        <div class="footer-copyright">
            <p>
                &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
                <?php bloginfo( 'name' ); ?>.
                <?php esc_html_e( 'All rights reserved.', 'peptide-world' ); ?>
            </p>
        </div>

    </div><!-- /.container -->
</footer>
<!-- /site-footer -->

<?php wp_footer(); ?>

</body>
</html>
