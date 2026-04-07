<?php
/**
 * Template Name: Peptide Database
 *
 * Fetches all peptides from Supabase and renders a filterable card grid.
 * Assign this template to the "Peptides" page in:
 *   WP Admin → Pages → Peptides → Page Attributes → Template
 *
 * REQUIRES in wp-config.php (before "That's all, stop editing!"):
 *   define( 'SUPABASE_URL',      'https://ffvozsgmyxxfkdpvnpxv.supabase.co' );
 *   define( 'SUPABASE_ANON_KEY', 'your-anon-key-here' );
 *
 * Data source: peptides table joined with peptide_enrichments via Supabase
 * resource embedding. One API call returns both sets of data.
 *
 * @package PeptideWorld
 */

defined( 'ABSPATH' ) || exit;

get_header();

// ============================================================================
// SUPABASE FETCH
// ============================================================================

/**
 * Validate that the required constants are defined in wp-config.php.
 * Show an admin-only notice if they're missing instead of a broken page.
 */
if ( ! defined( 'SUPABASE_URL' ) || ! defined( 'SUPABASE_ANON_KEY' ) ) {
    if ( current_user_can( 'manage_options' ) ) {
        echo '<div class="container"><div class="peptide-error">';
        echo '<strong>Admin notice:</strong> SUPABASE_URL and SUPABASE_ANON_KEY are not defined in wp-config.php.';
        echo '</div></div>';
    }
    get_footer();
    return;
}

/**
 * Fetch peptides with their enrichment data in a single request.
 *
 * Supabase resource embedding syntax embeds the related peptide_enrichments
 * row for each peptide using the peptide_id foreign key relationship.
 *
 * Columns fetched from peptides:
 *   id, name, category_function, confidence_score
 *
 * Columns fetched from peptide_enrichments (embedded):
 *   phase_2_data (JSONB), phase_2_success
 *
 * Only include enrichments where phase_2_success = true so incomplete
 * research data is never shown to end users.
 */
$endpoint = SUPABASE_URL . '/rest/v1/peptides'
    . '?select=id,name,category_function,confidence_score,'
    . 'peptide_enrichments!peptide_enrichments_peptide_id_fkey(phase_2_data,phase_2_success)'
    . '&order=name.asc';

$response = wp_remote_get( $endpoint, [
    'headers' => [
        'apikey'        => SUPABASE_ANON_KEY,
        'Authorization' => 'Bearer ' . SUPABASE_ANON_KEY,
        'Content-Type'  => 'application/json',
    ],
    'timeout' => 15,
] );

// ---- Error handling ----
$fetch_error = false;
$peptides    = [];

if ( is_wp_error( $response ) ) {
    $fetch_error = $response->get_error_message();
} else {
    $status_code = wp_remote_retrieve_response_code( $response );
    $body        = wp_remote_retrieve_body( $response );
    $decoded     = json_decode( $body, true );

    if ( $status_code !== 200 || ! is_array( $decoded ) ) {
        // Surface the Supabase error message to admins only.
        $fetch_error = current_user_can( 'manage_options' )
            ? ( isset( $decoded['message'] ) ? $decoded['message'] : 'Unexpected API response (HTTP ' . $status_code . ').' )
            : true; // non-admins just see the generic error UI
    } else {
        $peptides = $decoded;
    }
}

// ============================================================================
// BUILD CATEGORY FILTER LIST
// ============================================================================

/**
 * Collect unique categories from phase_2_data.research_categories across
 * all enriched peptides. Each peptide can belong to multiple categories.
 * Sorted alphabetically and capitalised for display.
 */
$categories = [];

foreach ( $peptides as $peptide ) {
    $enrichments = isset( $peptide['peptide_enrichments'] ) ? $peptide['peptide_enrichments'] : [];
    foreach ( $enrichments as $e ) {
        if ( empty( $e['phase_2_success'] ) || empty( $e['phase_2_data'] ) ) {
            continue;
        }
        $research_cats = $e['phase_2_data']['research_categories'] ?? [];
        foreach ( (array) $research_cats as $cat ) {
            $cat = trim( $cat );
            if ( $cat !== '' && ! in_array( $cat, $categories, true ) ) {
                $categories[] = $cat;
            }
        }
    }
}

sort( $categories );

?>

<div class="container">

    <?php while ( have_posts() ) : the_post(); ?>
        <header class="entry-header peptide-db-header">
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            <?php if ( get_the_content() ) : ?>
                <div class="entry-content peptide-db-intro">
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>
        </header>
    <?php endwhile; ?>

    <?php if ( $fetch_error ) : ?>

        <!-- ---- API error state ---- -->
        <div class="peptide-error">
            <?php if ( is_string( $fetch_error ) ) : ?>
                <strong><?php esc_html_e( 'Could not load peptide data:', 'peptide-world' ); ?></strong>
                <?php echo esc_html( $fetch_error ); ?>
            <?php else : ?>
                <?php esc_html_e( 'Peptide data is temporarily unavailable. Please try again shortly.', 'peptide-world' ); ?>
            <?php endif; ?>
        </div>

    <?php elseif ( empty( $peptides ) ) : ?>

        <!-- ---- Empty database state ---- -->
        <div class="no-results">
            <p><?php esc_html_e( 'No peptides found.', 'peptide-world' ); ?></p>
        </div>

    <?php else : ?>

        <!-- ==============================================================
             CONTROLS: search bar + category filter buttons
             main.js reads data-peptide-search and .peptide-filter-btn
             ============================================================== -->
        <div class="peptide-controls">

            <div class="peptide-search-wrap">
                <label for="peptide-search" class="screen-reader-text">
                    <?php esc_html_e( 'Search peptides', 'peptide-world' ); ?>
                </label>
                <input
                    id="peptide-search"
                    class="peptide-search"
                    type="search"
                    placeholder="<?php esc_attr_e( 'Search peptides…', 'peptide-world' ); ?>"
                    data-peptide-search
                    autocomplete="off"
                >
            </div>

            <ul class="peptide-filters" role="list" aria-label="<?php esc_attr_e( 'Filter by category', 'peptide-world' ); ?>">
                <!-- "All" button is active by default -->
                <li>
                    <button
                        class="peptide-filter-btn is-active"
                        data-category="All"
                        aria-pressed="true"
                    >
                        <?php esc_html_e( 'All', 'peptide-world' ); ?>
                    </button>
                </li>

                <?php foreach ( $categories as $cat ) : ?>
                    <li>
                        <button
                            class="peptide-filter-btn"
                            data-category="<?php echo esc_attr( $cat ); ?>"
                            aria-pressed="false"
                        >
                            <?php echo esc_html( $cat ); ?>
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>

        </div>
        <!-- /peptide-controls -->

        <!-- ==============================================================
             CARD GRID
             data-* attributes are read by main.js for client-side filtering.
             ============================================================== -->
        <div class="peptide-grid" id="peptide-grid">

            <?php foreach ( $peptides as $peptide ) :

                // ---- Extract base peptide fields ----
                $peptide_name = isset( $peptide['name'] )             ? $peptide['name']             : '';
                $conf_score   = isset( $peptide['confidence_score'] ) ? (float) $peptide['confidence_score'] : null;

                // ---- Extract enrichment data (first successful enrichment) ----
                $enrichments = isset( $peptide['peptide_enrichments'] ) ? $peptide['peptide_enrichments'] : [];
                $enrichment  = null;

                foreach ( $enrichments as $e ) {
                    if ( ! empty( $e['phase_2_success'] ) && ! empty( $e['phase_2_data'] ) ) {
                        $enrichment = $e['phase_2_data'];
                        break;
                    }
                }

                // ---- Pull fields from phase_2_data JSONB ----
                $overview         = $enrichment ? ( $enrichment['overview']            ?? '' ) : '';
                $benefits         = $enrichment ? ( $enrichment['potential_benefits']  ?? [] ) : [];
                $mechanism        = $enrichment ? ( $enrichment['mechanism_of_action'] ?? '' ) : '';
                $research_cats    = $enrichment ? ( $enrichment['research_categories'] ?? [] ) : [];

                // Use enrichment confidence score if available; fall back to peptide-level score.
                if ( $enrichment && isset( $enrichment['confidence_score'] ) ) {
                    $conf_score = (float) $enrichment['confidence_score'];
                }

                // Skip cards with no meaningful data to display.
                if ( $peptide_name === '' && $overview === '' ) {
                    continue;
                }

                // Truncate overview for the card body (full text in data attr for search).
                $overview_short = mb_strlen( $overview ) > 220
                    ? mb_substr( $overview, 0, 220 ) . '…'
                    : $overview;

                // Limit displayed benefits to 5 items.
                $benefits_display = array_slice( (array) $benefits, 0, 5 );

                // Truncate mechanism of action for the card.
                $mechanism_short = mb_strlen( $mechanism ) > 180
                    ? mb_substr( $mechanism, 0, 180 ) . '…'
                    : $mechanism;

                // Primary badge: first research category, capitalised.
                $primary_cat = ! empty( $research_cats ) ? ucfirst( $research_cats[0] ) : '';

                // All categories as JSON for multi-category filtering in main.js.
                $cats_json = wp_json_encode( array_map( 'strtolower', (array) $research_cats ) );

                // Confidence score for display.
                $score_pct   = $conf_score !== null ? round( $conf_score * 100 ) : null;
                $score_label = $score_pct !== null
                    ? ( $score_pct >= 75 ? 'high' : ( $score_pct >= 50 ? 'medium' : 'low' ) )
                    : '';

                // Unique ID ties the card to its modal content.
                $card_id = 'peptide-' . esc_attr( $peptide['id'] ?? uniqid() );

                // Limit card to 3 benefits; modal shows all.
                $benefits_card  = array_slice( (array) $benefits, 0, 3 );
                $benefits_all   = (array) $benefits;

                // Full-length fields for the modal.
                $risks     = $enrichment ? ( $enrichment['risks_and_safety'] ?? [] ) : [];
                $legal     = $enrichment ? ( $enrichment['legal_status']     ?? '' ) : '';

            ?>

                <!-- =====================================================
                     CARD (uniform 4-row grid layout)
                     ===================================================== -->
                <div
                    class="peptide-card"
                    data-name="<?php echo esc_attr( strtolower( $peptide_name ) ); ?>"
                    data-categories="<?php echo esc_attr( $cats_json ); ?>"
                    data-description="<?php echo esc_attr( strtolower( $overview ) ); ?>"
                    data-modal="<?php echo esc_attr( $card_id ); ?>"
                    role="button"
                    tabindex="0"
                    aria-haspopup="dialog"
                >
                    <!-- Row 1: name + badge -->
                    <div class="peptide-card-header">
                        <h2 class="peptide-name"><?php echo esc_html( $peptide_name ); ?></h2>
                        <?php if ( $primary_cat ) : ?>
                            <span class="peptide-category-badge"><?php echo esc_html( $primary_cat ); ?></span>
                        <?php endif; ?>
                    </div>

                    <!-- Row 2: overview (clamped to 3 lines via CSS) -->
                    <p class="peptide-description"><?php echo esc_html( $overview ); ?></p>

                    <!-- Row 3: top 3 benefits -->
                    <?php if ( ! empty( $benefits_card ) ) : ?>
                        <div class="peptide-benefits">
                            <p class="peptide-benefits-title"><?php esc_html_e( 'Key Benefits', 'peptide-world' ); ?></p>
                            <ul class="peptide-benefits-list">
                                <?php foreach ( $benefits_card as $benefit ) : ?>
                                    <li class="peptide-benefit-item"><?php echo esc_html( $benefit ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php else : ?>
                        <div></div><!-- keep grid rows aligned -->
                    <?php endif; ?>

                    <!-- Row 4: footer with button -->
                    <div class="peptide-card-footer">
                        <button
                            class="peptide-details-btn"
                            data-modal="<?php echo esc_attr( $card_id ); ?>"
                            aria-controls="<?php echo esc_attr( $card_id ); ?>"
                        >
                            <?php esc_html_e( 'View Details', 'peptide-world' ); ?>
                        </button>
                    </div>

                </div>
                <!-- /peptide-card -->

                <!-- =====================================================
                     HIDDEN MODAL CONTENT for this card.
                     JS clones this into the shared overlay on open.
                     ===================================================== -->
                <div id="<?php echo esc_attr( $card_id ); ?>" class="peptide-modal-data" hidden>

                    <div class="peptide-modal-header">
                        <h2 class="peptide-modal-name"><?php echo esc_html( $peptide_name ); ?></h2>
                        <?php if ( $primary_cat ) : ?>
                            <span class="peptide-category-badge"><?php echo esc_html( $primary_cat ); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if ( $score_pct !== null ) : ?>
                        <div class="peptide-confidence confidence-<?php echo esc_attr( $score_label ); ?>">
                            <?php printf( esc_html__( 'Research confidence: %d%%', 'peptide-world' ), $score_pct ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( $overview ) : ?>
                        <div class="peptide-modal-section">
                            <p class="peptide-modal-section-title"><?php esc_html_e( 'Overview', 'peptide-world' ); ?></p>
                            <p><?php echo esc_html( $overview ); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $benefits_all ) ) : ?>
                        <div class="peptide-modal-section">
                            <p class="peptide-modal-section-title"><?php esc_html_e( 'Potential Benefits', 'peptide-world' ); ?></p>
                            <ul class="peptide-modal-benefits">
                                <?php foreach ( $benefits_all as $b ) : ?>
                                    <li><?php echo esc_html( $b ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ( $mechanism ) : ?>
                        <div class="peptide-modal-section">
                            <p class="peptide-modal-section-title"><?php esc_html_e( 'Mechanism of Action', 'peptide-world' ); ?></p>
                            <p><?php echo esc_html( $mechanism ); ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $risks ) ) : ?>
                        <div class="peptide-modal-section">
                            <p class="peptide-modal-section-title"><?php esc_html_e( 'Risks & Safety', 'peptide-world' ); ?></p>
                            <ul class="peptide-modal-risks">
                                <?php foreach ( $risks as $risk ) : ?>
                                    <li><?php echo esc_html( $risk ); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ( $legal ) : ?>
                        <div class="peptide-modal-section">
                            <p class="peptide-modal-section-title"><?php esc_html_e( 'Legal Status', 'peptide-world' ); ?></p>
                            <div class="peptide-modal-legal"><?php echo esc_html( $legal ); ?></div>
                        </div>
                    <?php endif; ?>

                </div>
                <!-- /peptide-modal-data -->

            <?php endforeach; ?>

        </div>
        <!-- /peptide-grid -->

        <!-- Shown by main.js when no cards match the current filter/search -->
        <div class="peptide-no-results" aria-live="polite">
            <p><?php esc_html_e( 'No peptides match your search. Try different keywords or clear the filter.', 'peptide-world' ); ?></p>
        </div>

    <?php endif; ?>

</div><!-- /.container -->

<!-- =========================================================
     SHARED MODAL OVERLAY
     JS populates #peptide-modal-body from the hidden
     .peptide-modal-data div that matches the clicked card.
     ========================================================= -->
<div
    class="peptide-modal-overlay"
    id="peptide-modal-overlay"
    hidden
    role="dialog"
    aria-modal="true"
    aria-labelledby="peptide-modal-title"
>
    <div class="peptide-modal">
        <button
            class="peptide-modal-close"
            id="peptide-modal-close"
            aria-label="<?php esc_attr_e( 'Close', 'peptide-world' ); ?>"
        >&times;</button>
        <div id="peptide-modal-body"></div>
    </div>
</div>

<?php get_footer(); ?>
