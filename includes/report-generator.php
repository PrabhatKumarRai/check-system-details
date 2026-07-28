<?php
/**
 * Headless report generation, for contexts with no admin page rendering
 * a chance to run (WP-CLI, cron, AJAX handlers). Reuses the exact same
 * data-gathering code as the admin page by including those files with
 * output buffering, then reuses the same text-building function.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'csd_generate_full_report_text' ) ) {
    function csd_generate_full_report_text() {
        // get_plugins() lives here and isn't autoloaded outside wp-admin.
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        require_once CSD_PLUGIN_DIR . 'admin/report_text.php';

        $csd_admin_dir = CSD_PLUGIN_DIR . 'admin/';

        ob_start();
        include $csd_admin_dir . 'health_check.php';
        include $csd_admin_dir . 'wordpress.php';
        include $csd_admin_dir . 'server_and_database.php';
        include $csd_admin_dir . 'htaccess.php';
        include $csd_admin_dir . 'robots_txt.php';
        include $csd_admin_dir . 'debug_log.php';
        ob_end_clean();

        return csd_build_report_text( csd_report_context_from_current_scope( get_defined_vars() ) );
    }
}
