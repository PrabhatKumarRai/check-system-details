<?php
/**
 * AJAX endpoints for the Tools tab: test email, snapshot save/compare,
 * send report now, and recurring report settings. All require the
 * manage_options capability and a valid nonce.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'csd_ajax_guard' ) ) {
    function csd_ajax_guard() {
        check_ajax_referer( 'csd_tools_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => __( 'Insufficient permissions.', 'check-system-details' ) ], 403 );
        }
    }
}

if ( ! function_exists( 'csd_ajax_send_test_email' ) ) {
    function csd_ajax_send_test_email() {
        csd_ajax_guard();

        $csd_mail_error   = null;
        $csd_capture_error = function ( $wp_error ) use ( &$csd_mail_error ) {
            $csd_mail_error = $wp_error;
        };
        add_action( 'wp_mail_failed', $csd_capture_error );

        $csd_to   = get_option( 'admin_email' );
        $csd_sent = wp_mail(
            $csd_to,
            /* translators: %s: site name */
            sprintf( __( '[%s] Check System Details - Test Email', 'check-system-details' ), get_bloginfo( 'name' ) ),
            sprintf(
                /* translators: 1: site URL, 2: date/time sent */
                __( "This is a test email sent from the Check System Details plugin.\n\nSite: %1\$s\nSent: %2\$s", 'check-system-details' ),
                home_url(),
                current_time( 'mysql' )
            )
        );

        remove_action( 'wp_mail_failed', $csd_capture_error );

        if ( $csd_sent ) {
            /* translators: %s: email address the test message was sent to */
            wp_send_json_success( [ 'message' => sprintf( __( 'Test email sent to %s. Check your inbox (and spam folder).', 'check-system-details' ), $csd_to ) ] );
        } else {
            wp_send_json_error( [ 'message' => $csd_mail_error ? $csd_mail_error->get_error_message() : __( 'wp_mail() returned false. Your server may not be configured to send email - consider an SMTP plugin.', 'check-system-details' ) ] );
        }
    }
}
add_action( 'wp_ajax_csd_send_test_email', 'csd_ajax_send_test_email' );

if ( ! function_exists( 'csd_ajax_save_snapshot' ) ) {
    function csd_ajax_save_snapshot() {
        csd_ajax_guard();

        require_once CSD_PLUGIN_DIR . 'includes/report-generator.php';

        $csd_time = time();
        update_option(
            'csd_report_snapshot',
            [
                'text' => csd_generate_full_report_text(),
                'time' => $csd_time,
            ],
            false
        );

        wp_send_json_success(
            [
                'message' => __( 'Snapshot saved.', 'check-system-details' ),
                'date'    => esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $csd_time ) ),
            ]
        );
    }
}
add_action( 'wp_ajax_csd_save_snapshot', 'csd_ajax_save_snapshot' );

if ( ! function_exists( 'csd_ajax_compare_snapshot' ) ) {
    function csd_ajax_compare_snapshot() {
        csd_ajax_guard();

        $csd_snapshot = get_option( 'csd_report_snapshot', [] );

        if ( empty( $csd_snapshot['text'] ) ) {
            wp_send_json_error( [ 'message' => __( 'No snapshot saved yet.', 'check-system-details' ) ] );
        }

        if ( ! function_exists( 'wp_text_diff' ) ) {
            require_once ABSPATH . 'wp-includes/wp-diff.php';
        }

        require_once CSD_PLUGIN_DIR . 'includes/report-generator.php';

        $csd_current_text = csd_generate_full_report_text();
        $csd_diff_html     = wp_text_diff(
            $csd_snapshot['text'],
            $csd_current_text,
            [
                'title_left'  => __( 'Snapshot', 'check-system-details' ),
                'title_right' => __( 'Current', 'check-system-details' ),
            ]
        );

        if ( empty( $csd_diff_html ) ) {
            wp_send_json_success( [ 'html' => '<p>' . esc_html__( 'No differences since the snapshot was saved.', 'check-system-details' ) . '</p>' ] );
        }

        wp_send_json_success( [ 'html' => $csd_diff_html ] );
    }
}
add_action( 'wp_ajax_csd_compare_snapshot', 'csd_ajax_compare_snapshot' );

if ( ! function_exists( 'csd_ajax_send_report_now' ) ) {
    function csd_ajax_send_report_now() {
        csd_ajax_guard();

        $csd_email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

        if ( ! is_email( $csd_email ) ) {
            wp_send_json_error( [ 'message' => __( 'Please enter a valid email address.', 'check-system-details' ) ] );
        }

        require_once CSD_PLUGIN_DIR . 'includes/report-generator.php';

        $csd_sent = wp_mail(
            $csd_email,
            /* translators: %s: site name */
            sprintf( __( '[%s] Check System Details Report', 'check-system-details' ), get_bloginfo( 'name' ) ),
            csd_generate_full_report_text()
        );

        if ( $csd_sent ) {
            /* translators: %s: recipient email address */
            wp_send_json_success( [ 'message' => sprintf( __( 'Report sent to %s.', 'check-system-details' ), $csd_email ) ] );
        } else {
            wp_send_json_error( [ 'message' => __( 'wp_mail() returned false. The report could not be sent.', 'check-system-details' ) ] );
        }
    }
}
add_action( 'wp_ajax_csd_send_report_now', 'csd_ajax_send_report_now' );

if ( ! function_exists( 'csd_ajax_save_recurring_settings' ) ) {
    function csd_ajax_save_recurring_settings() {
        csd_ajax_guard();

        $csd_enabled   = ! empty( $_POST['enabled'] );
        $csd_recipient = isset( $_POST['recipient'] ) ? sanitize_email( wp_unslash( $_POST['recipient'] ) ) : '';
        $csd_frequency = isset( $_POST['frequency'] ) && 'csd_monthly' === $_POST['frequency'] ? 'csd_monthly' : 'csd_weekly';

        if ( $csd_enabled && ! is_email( $csd_recipient ) ) {
            wp_send_json_error( [ 'message' => __( 'Please enter a valid recipient email address.', 'check-system-details' ) ] );
        }

        update_option(
            'csd_recurring_report_settings',
            [
                'enabled'   => $csd_enabled,
                'recipient' => $csd_recipient,
                'frequency' => $csd_frequency,
            ]
        );

        csd_reschedule_recurring_report( $csd_enabled ? $csd_frequency : false );

        wp_send_json_success( [ 'message' => $csd_enabled ? __( 'Recurring report scheduled.', 'check-system-details' ) : __( 'Recurring report disabled.', 'check-system-details' ) ] );
    }
}
add_action( 'wp_ajax_csd_save_recurring_settings', 'csd_ajax_save_recurring_settings' );

if ( ! function_exists( 'csd_ajax_save_redact_setting' ) ) {
    function csd_ajax_save_redact_setting() {
        csd_ajax_guard();

        $csd_enabled = ! empty( $_POST['enabled'] );
        csd_update_bool_option( 'csd_redact_sensitive_data', $csd_enabled );

        wp_send_json_success( [ 'message' => $csd_enabled ? __( 'Sensitive data will be redacted in exported reports.', 'check-system-details' ) : __( 'Sensitive data will be included in exported reports.', 'check-system-details' ) ] );
    }
}
add_action( 'wp_ajax_csd_save_redact_setting', 'csd_ajax_save_redact_setting' );
