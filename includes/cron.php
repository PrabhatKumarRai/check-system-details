<?php
/**
 * Custom cron schedules and the recurring email report.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'csd_add_cron_schedules' ) ) {
    function csd_add_cron_schedules( $schedules ) {
        $schedules['csd_weekly'] = [
            'interval' => WEEK_IN_SECONDS,
            'display'  => __( 'Once Weekly', 'check-system-details' ),
        ];
        $schedules['csd_monthly'] = [
            'interval' => 30 * DAY_IN_SECONDS,
            'display'  => __( 'Once Monthly', 'check-system-details' ),
        ];
        return $schedules;
    }
}
add_filter( 'cron_schedules', 'csd_add_cron_schedules' );

if ( ! function_exists( 'csd_reschedule_recurring_report' ) ) {
    /**
     * @param string|false $frequency One of 'csd_weekly', 'csd_monthly', or false to disable.
     */
    function csd_reschedule_recurring_report( $frequency ) {
        wp_clear_scheduled_hook( 'csd_send_recurring_report' );

        if ( $frequency ) {
            wp_schedule_event( time(), $frequency, 'csd_send_recurring_report' );
        }
    }
}

if ( ! function_exists( 'csd_do_send_recurring_report' ) ) {
    function csd_do_send_recurring_report() {
        $csd_settings = get_option( 'csd_recurring_report_settings', [] );

        if ( empty( $csd_settings['enabled'] ) || empty( $csd_settings['recipient'] ) || ! is_email( $csd_settings['recipient'] ) ) {
            return;
        }

        require_once CSD_PLUGIN_DIR . 'includes/report-generator.php';

        wp_mail(
            $csd_settings['recipient'],
            /* translators: %s: site name */
            sprintf( __( '[%s] Check System Details Report', 'check-system-details' ), get_bloginfo( 'name' ) ),
            csd_generate_full_report_text()
        );
    }
}
add_action( 'csd_send_recurring_report', 'csd_do_send_recurring_report' );

if ( ! function_exists( 'csd_deactivate' ) ) {
    function csd_deactivate() {
        wp_clear_scheduled_hook( 'csd_send_recurring_report' );
    }
}
