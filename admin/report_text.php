<?php
/**
 * Builds a plain-text version of the full report for the Copy/Download
 * toolbar. Relies on variables already populated by the section includes
 * pulled in above it in check_system_details_view.php.
 */

if ( ! function_exists( 'csd_report_section' ) ) {
    function csd_report_section( $title, $rows ) {
        $lines = [ "### {$title} ###" ];

        foreach ( $rows as $label => $value ) {
            $lines[] = "{$label}: {$value}";
        }

        return implode( "\n", $lines );
    }
}

$csd_report_parts = [];

$csd_report_parts[] = csd_report_section( 'WordPress', $csd_wordpress_details );

$csd_report_parts[] = csd_report_section( 'Active Theme', [
    'Name'        => $csd_theme_details['Name'],
    'Version'     => $csd_theme_details['Version'],
    'Author'      => $csd_theme_details['Author'],
    'Description' => $csd_theme_details['Description'],
] );

$csd_plugin_lines = [ '### Installed Plugins ###' ];
foreach ( $csd_plugin_details as $csd_plugin ) {
    $csd_plugin_lines[] = "{$csd_plugin['Name']} - version {$csd_plugin['Version']} by {$csd_plugin['Author']}";
}
$csd_report_parts[] = implode( "\n", $csd_plugin_lines );

$csd_report_parts[] = csd_report_section( 'Server', $csd_server_details );

$csd_report_parts[] = csd_report_section( 'Database', $csd_database_details );

$csd_table_lines = [ '### Database Tables ###' ];
foreach ( $csd_tables as $csd_table ) {
    foreach ( $csd_table as $csd_t ) {
        $csd_table_lines[] = $csd_t;
    }
}
$csd_report_parts[] = implode( "\n", $csd_table_lines );

$csd_report_parts[] = "### .htaccess ###\n" . $htaccess;
$csd_report_parts[] = "### Robots.txt ###\n" . $robots;
$csd_report_parts[] = "### Debug Log (last 100 lines) ###\n" . $csd_debug_log_plain;

$csd_report_text = implode( "\n\n", $csd_report_parts );
?>
<textarea class="csd-report-textarea" readonly><?php echo esc_textarea( $csd_report_text ); ?></textarea>
