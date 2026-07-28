<?php
    $csd_report_filename = 'system-details-' . sanitize_title( get_bloginfo( 'name' ) ) . '-' . gmdate( 'Y-m-d' ) . '.txt';
?>
<div class="wrap csd-wrap">

    <div class="csd-header">
        <h1>Check System Details</h1>
        <div class="csd-toolbar">
            <button type="button" class="button button-secondary csd-copy-report">
                <span class="dashicons dashicons-clipboard"></span> Copy Report
            </button>
            <button type="button" class="button button-primary csd-download-report" data-filename="<?php echo esc_attr( $csd_report_filename ); ?>">
                <span class="dashicons dashicons-download"></span> Download Report
            </button>
        </div>
    </div>

    <h2 class="nav-tab-wrapper">
        <a href="#" class="nav-tab nav-tab-active" data-tab="wordpress">WordPress</a>
        <a href="#" class="nav-tab" data-tab="server-database">Server &amp; Database</a>
        <a href="#" class="nav-tab" data-tab="files-logs">Files &amp; Logs</a>
    </h2>

    <div class="csd-tab-panel is-active" data-tab="wordpress">
        <?php include_once 'wordpress.php'; ?>
    </div>

    <div class="csd-tab-panel" data-tab="server-database">
        <?php include_once 'server_and_database.php'; ?>
    </div>

    <div class="csd-tab-panel" data-tab="files-logs">
        <?php
            include_once 'htaccess.php';
            include_once 'robots_txt.php';
            include_once 'debug_log.php';
        ?>
    </div>

    <?php include_once 'report_text.php'; ?>

    <div class="csd-toast"></div>

    <p class="csd-footer">Developed by <a href="https://prabhatrai.in/" target="_blank" rel="noopener noreferrer"><i>Prabhat Rai</i></a></p>
</div>
