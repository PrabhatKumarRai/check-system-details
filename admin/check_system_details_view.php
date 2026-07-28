<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

    $csd_report_filename = 'system-details-' . sanitize_title( get_bloginfo( 'name' ) ) . '-' . gmdate( 'Y-m-d' ) . '.txt';
?>
<div class="wrap csd-wrap">

    <div class="csd-header">
        <h1><?php esc_html_e( 'Check System Details', 'check-system-details' ); ?></h1>
        <div class="csd-toolbar">
            <button type="button" class="button button-secondary csd-copy-report">
                <span class="dashicons dashicons-clipboard"></span> <?php esc_html_e( 'Copy Report', 'check-system-details' ); ?>
            </button>
            <button type="button" class="button button-primary csd-download-report" data-filename="<?php echo esc_attr( $csd_report_filename ); ?>">
                <span class="dashicons dashicons-download"></span> <?php esc_html_e( 'Download Report', 'check-system-details' ); ?>
            </button>
        </div>
    </div>

    <h2 class="nav-tab-wrapper">
        <a href="#" class="nav-tab nav-tab-active" data-tab="health-check"><?php esc_html_e( 'Health Check', 'check-system-details' ); ?></a>
        <a href="#" class="nav-tab" data-tab="wordpress"><?php esc_html_e( 'WordPress', 'check-system-details' ); ?></a>
        <a href="#" class="nav-tab" data-tab="server-database"><?php esc_html_e( 'Server & Database', 'check-system-details' ); ?></a>
        <a href="#" class="nav-tab" data-tab="files-logs"><?php esc_html_e( 'Files & Logs', 'check-system-details' ); ?></a>
        <a href="#" class="nav-tab" data-tab="tools"><?php esc_html_e( 'Utilities', 'check-system-details' ); ?></a>
        <a href="#" class="nav-tab" data-tab="docs"><?php esc_html_e( 'Docs', 'check-system-details' ); ?></a>
    </h2>

    <div class="csd-tab-panel is-active" data-tab="health-check">
        <?php include_once __DIR__ . '/health_check.php'; ?>
    </div>

    <div class="csd-tab-panel" data-tab="wordpress">
        <?php include_once __DIR__ . '/wordpress.php'; ?>
    </div>

    <div class="csd-tab-panel" data-tab="server-database">
        <?php include_once __DIR__ . '/server_and_database.php'; ?>
    </div>

    <div class="csd-tab-panel" data-tab="files-logs">
        <?php
            include_once __DIR__ . '/htaccess.php';
            include_once __DIR__ . '/robots_txt.php';
            include_once __DIR__ . '/debug_log.php';
        ?>
    </div>

    <div class="csd-tab-panel" data-tab="tools">
        <?php include_once __DIR__ . '/tools.php'; ?>
    </div>

    <div class="csd-tab-panel" data-tab="docs">
        <?php include_once __DIR__ . '/docs.php'; ?>
    </div>

    <?php
        require_once __DIR__ . '/report_text.php';
        $csd_report_text = csd_build_report_text( csd_report_context_from_current_scope( get_defined_vars() ) );
    ?>
    <textarea class="csd-report-textarea csd-full-report-textarea" readonly><?php echo esc_textarea( $csd_report_text ); ?></textarea>

    <div class="csd-toast"></div>

    <p class="csd-footer">
        <?php
            printf(
                /* translators: %s: linked author name */
                esc_html__( 'Developed by %s', 'check-system-details' ),
                '<a href="https://prabhatrai.in/" target="_blank" rel="noopener noreferrer"><i>' . esc_html__( 'Prabhat Rai', 'check-system-details' ) . '</i></a>'
            );
        ?>
    </p>
</div>
