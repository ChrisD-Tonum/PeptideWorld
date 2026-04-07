<?php
/**
 * Peptide World — page.php
 *
 * Template for static WordPress pages. Elementor takes over the content
 * area via the_content() — this template simply provides the wrapper so
 * Elementor output renders inside the correct header/footer context.
 *
 * Pages using this template: Home, Hormone Therapy, Educational Guides,
 * Consultation. The Peptides page uses templates/peptide-database.php.
 *
 * @package PeptideWorld
 */

get_header();

while ( have_posts() ) :
    the_post();
    the_content(); // Elementor intercepts this and renders its page builder content.
endwhile;

get_footer();
