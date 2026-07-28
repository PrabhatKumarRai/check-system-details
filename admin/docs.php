<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'About Check System Details', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <p><?php esc_html_e( 'This plugin reports your WordPress, server, and database details in one place, flags real problems instead of just listing facts, and gives you a few tools for handing that information off to support or keeping an eye on the site over time.', 'check-system-details' ); ?></p>
    </div>
</div>

<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Health Check', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <p><?php esc_html_e( 'A set of local diagnostic checks - PHP version and extensions, OPcache, debug settings, file permissions, caching, and cron health - each marked Good, Warning, or Critical. Nothing here makes an outbound network request; it only reads settings and files already on this server.', 'check-system-details' ); ?></p>
    </div>
</div>

<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'WordPress, Server & Database, Files & Logs', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <p>
            <?php
                echo wp_kses(
                    __( 'These three tabs are the reference data: WordPress core/theme/plugin details and scheduled cron events, PHP/server limits and database connection details, and the raw content of <code>.htaccess</code>, <code>robots.txt</code>, and the last 100 lines of <code>debug.log</code>.', 'check-system-details' ),
                    [ 'code' => [] ]
                );
                echo ' ';
                printf(
                    /* translators: %s: a help icon shown inline in the sentence */
                    esc_html__( 'Hover the %s icon next to a field for a plain-language explanation of what it means.', 'check-system-details' ),
                    '<span class="dashicons dashicons-editor-help" style="font-size:14px;width:14px;height:14px;vertical-align:text-bottom;"></span>'
                );
            ?>
        </p>
    </div>
</div>

<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Utilities - how to use them', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <p>
            <?php
                printf(
                    /* translators: 1: "Report Privacy" bolded, 2: rest of the explanation */
                    esc_html__( '%1$s - a toggle that controls what leaves the site through Copy Report, Download Report, and Email Report. When enabled (the default), your database name/host and server file paths are masked, since reports are often pasted into public support forums. It does not change anything you see on this admin page.', 'check-system-details' ),
                    '<strong>' . esc_html__( 'Report Privacy', 'check-system-details' ) . '</strong>'
                );
            ?>
        </p>
        <p>
            <?php
                echo wp_kses(
                    sprintf(
                        /* translators: %s: "Test Email" bolded */
                        __( '%s - sends a real email via <code>wp_mail()</code> to your admin address. If it fails, the reason is shown - a fast way to confirm whether mail sending is actually working before troubleshooting missed password-reset or notification emails.', 'check-system-details' ),
                        '<strong>' . esc_html__( 'Test Email', 'check-system-details' ) . '</strong>'
                    ),
                    [
                        'strong' => [],
                        'code'   => [],
                    ]
                );
            ?>
        </p>
        <p>
            <?php
                printf(
                    /* translators: %s: "Snapshot & Compare" bolded */
                    esc_html__( '%s - "Save Snapshot Now" stores the current report. Later - after an update, a migration, or when something looks off - click "Compare Current vs Snapshot" to see a line-by-line diff of exactly what changed.', 'check-system-details' ),
                    '<strong>' . esc_html__( 'Snapshot & Compare', 'check-system-details' ) . '</strong>'
                );
            ?>
        </p>
        <p>
            <?php
                printf(
                    /* translators: %s: "Email Report" bolded */
                    esc_html__( '%s - "Send Now" emails the current report to any address immediately. Below that, you can turn on a recurring weekly or monthly send to a chosen recipient, useful for keeping a periodic record without having to remember to check.', 'check-system-details' ),
                    '<strong>' . esc_html__( 'Email Report', 'check-system-details' ) . '</strong>'
                );
            ?>
        </p>
        <p>
            <?php
                echo wp_kses(
                    sprintf(
                        /* translators: %s: "WP-CLI" bolded */
                        __( '%s - if this server has WP-CLI installed, <code>wp check-system-details</code> prints the same report directly in the terminal, without needing to log into wp-admin.', 'check-system-details' ),
                        '<strong>WP-CLI</strong>'
                    ),
                    [
                        'strong' => [],
                        'code'   => [],
                    ]
                );
            ?>
        </p>
    </div>
</div>
