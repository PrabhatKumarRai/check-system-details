<?php
/**
 * Runs only when the plugin is deleted from the Plugins screen (not on
 * deactivation). Removes everything this plugin stored.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'csd_report_snapshot' );
delete_option( 'csd_recurring_report_settings' );
delete_option( 'csd_redact_sensitive_data' );

wp_clear_scheduled_hook( 'csd_send_recurring_report' );
