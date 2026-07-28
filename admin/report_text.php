<?php
/**
 * Defines the reusable report-text-building functions only - it does not
 * execute anything on its own. The admin view calls these after gathering
 * data for its own render; includes/report-generator.php does the same
 * headlessly for WP-CLI, cron, snapshot, and email-report features.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'csd_report_section' ) ) {
    function csd_report_section( $title, $rows ) {
        $lines = [ "### {$title} ###" ];

        foreach ( $rows as $label => $value ) {
            $lines[] = "{$label}: {$value}";
        }

        return implode( "\n", $lines );
    }
}

if ( ! function_exists( 'csd_build_report_text' ) ) {
    /**
     * @param array $ctx Associative array with keys: health_checks, health_summary,
     *                    cron_events, multisite_sites, multisite_capped, wordpress_details,
     *                    theme_details, plugin_details, server_details, database_details,
     *                    tables, htaccess, robots, debug_log_plain.
     */
    function csd_build_report_text( $ctx ) {
        $csd_redact = (bool) get_option( 'csd_redact_sensitive_data', true );

        if ( $csd_redact ) {
            if ( isset( $ctx['database_details']['Database Name'] ) ) {
                $ctx['database_details']['Database Name'] = '[redacted]';
            }
            if ( isset( $ctx['database_details']['Database Host'] ) ) {
                $ctx['database_details']['Database Host'] = '[redacted]';
            }
        }

        $csd_report_parts = [];

        $csd_health_lines = [ "### Health Check ({$ctx['health_summary']['good']} good, {$ctx['health_summary']['warning']} warning, {$ctx['health_summary']['critical']} critical) ###" ];
        foreach ( $ctx['health_checks'] as $csd_health_check ) {
            $csd_health_lines[] = "[{$csd_health_check['level']}] {$csd_health_check['label']}: {$csd_health_check['message']}";
        }
        $csd_report_parts[] = implode( "\n", $csd_health_lines );

        if ( ! empty( $ctx['cron_events'] ) ) {
            $csd_cron_lines = [ '### Scheduled Cron Events ###' ];
            foreach ( $ctx['cron_events'] as $csd_cron_event ) {
                $csd_cron_lines[] = "{$csd_cron_event['hook']} - next run " . gmdate( 'Y-m-d H:i:s', $csd_cron_event['next_run'] ) . " GMT ({$csd_cron_event['recurrence']})";
            }
            $csd_report_parts[] = implode( "\n", $csd_cron_lines );
        }

        if ( is_multisite() ) {
            $csd_ms_lines = [ '### Multisite Network (' . count( $ctx['multisite_sites'] ) . ( $ctx['multisite_capped'] ? '+' : '' ) . ' sites) ###' ];
            foreach ( $ctx['multisite_sites'] as $csd_ms_site ) {
                $csd_ms_lines[] = "Site {$csd_ms_site['blog_id']}: {$csd_ms_site['domain']}{$csd_ms_site['path']}";
            }
            $csd_report_parts[] = implode( "\n", $csd_ms_lines );
        }

        $csd_report_parts[] = csd_report_section( 'WordPress', $ctx['wordpress_details'] );

        $csd_report_parts[] = csd_report_section(
            'Active Theme',
            [
                'Name'        => $ctx['theme_details']['Name'],
                'Version'     => $ctx['theme_details']['Version'],
                'Author'      => $ctx['theme_details']['Author'],
                'Description' => $ctx['theme_details']['Description'],
            ]
        );

        $csd_plugin_lines = [ '### Installed Plugins ###' ];
        foreach ( $ctx['plugin_details'] as $csd_plugin ) {
            $csd_plugin_lines[] = "{$csd_plugin['Name']} - version {$csd_plugin['Version']} by {$csd_plugin['Author']}";
        }
        $csd_report_parts[] = implode( "\n", $csd_plugin_lines );

        $csd_report_parts[] = csd_report_section( 'Server', $ctx['server_details'] );

        $csd_report_parts[] = csd_report_section( 'Database', $ctx['database_details'] );

        $csd_table_lines = [ '### Database Tables ###' ];
        foreach ( $ctx['tables'] as $csd_table ) {
            foreach ( $csd_table as $csd_t ) {
                $csd_table_lines[] = $csd_t;
            }
        }
        $csd_report_parts[] = implode( "\n", $csd_table_lines );

        $csd_report_parts[] = "### .htaccess ###\n" . $ctx['htaccess'];
        $csd_report_parts[] = "### Robots.txt ###\n" . $ctx['robots'];
        $csd_report_parts[] = "### Debug Log (last 100 lines) ###\n" . $ctx['debug_log_plain'];

        $csd_report_text = implode( "\n\n", $csd_report_parts );

        if ( $csd_redact ) {
            // Debug log lines in particular often contain absolute server paths
            // in stack traces - mask them so a pasted report doesn't leak the
            // server username/directory layout.
            $csd_report_text = str_replace( wp_normalize_path( ABSPATH ), '[SITE_ROOT]/', $csd_report_text );
            $csd_report_text = str_replace( ABSPATH, '[SITE_ROOT]/', $csd_report_text );
        }

        return $csd_report_text;
    }
}

if ( ! function_exists( 'csd_report_context_from_current_scope' ) ) {
    /**
     * Builds the report context array from the variables already populated
     * by the section includes pulled in above this file (health_check.php,
     * wordpress.php, server_and_database.php, htaccess.php, robots_txt.php,
     * debug_log.php), whether that happened via the admin view or via the
     * headless gatherer in includes/report-generator.php.
     */
    function csd_report_context_from_current_scope( $vars ) {
        return [
            'health_checks'     => $vars['csd_health_checks'],
            'health_summary'    => $vars['csd_health_summary'],
            'cron_events'       => $vars['csd_cron_events'],
            'multisite_sites'   => $vars['csd_multisite_sites'],
            'multisite_capped'  => $vars['csd_multisite_capped'],
            'wordpress_details' => $vars['csd_wordpress_details'],
            'theme_details'     => $vars['csd_theme_details'],
            'plugin_details'    => $vars['csd_plugin_details'],
            'server_details'    => $vars['csd_server_details'],
            'database_details'  => $vars['csd_database_details'],
            'tables'            => $vars['csd_tables'],
            'htaccess'          => $vars['htaccess'],
            'robots'            => $vars['robots'],
            'debug_log_plain'   => $vars['csd_debug_log_plain'],
        ];
    }
}
