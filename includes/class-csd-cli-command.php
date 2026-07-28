<?php
/**
 * WP-CLI command: `wp check-system-details`
 * Only loaded when WP-CLI is present (see check_system_details.php).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'CSD_CLI_Command' ) ) {
    class CSD_CLI_Command {
        /**
         * Prints the full system details report.
         *
         * ## EXAMPLES
         *
         *     wp check-system-details
         */
        public function __invoke( $args, $assoc_args ) {
            require_once CSD_PLUGIN_DIR . 'includes/report-generator.php';
            WP_CLI::log( csd_generate_full_report_text() );
        }
    }
}

WP_CLI::add_command( 'check-system-details', 'CSD_CLI_Command' );
