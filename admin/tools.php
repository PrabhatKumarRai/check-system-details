<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$csd_snapshot        = get_option( 'csd_report_snapshot', [] );
$csd_has_snapshot    = ! empty( $csd_snapshot['time'] );
$csd_recurring       = get_option(
    'csd_recurring_report_settings',
    [
        'enabled'   => false,
        'recipient' => get_option( 'admin_email' ),
        'frequency' => 'csd_weekly',
    ]
);
$csd_redact_enabled = (bool) get_option( 'csd_redact_sensitive_data', true );
?>

<!-- Report Privacy -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Report Privacy', 'check-system-details' ); ?><?php echo csd_tooltip( __( 'Controls what is included when you copy, download, or email this report - not what you see on this page.', 'check-system-details' ) ); ?></h2>
    <div class="csd-section-body">
        <p>
            <label>
                <input type="checkbox" id="csd-redact-sensitive" <?php checked( $csd_redact_enabled ); ?> />
                <?php esc_html_e( 'Redact sensitive data (database name/host, server file paths) in Copy Report, Download Report, and Email Report', 'check-system-details' ); ?>
            </label>
        </p>
        <p class="description"><?php esc_html_e( "This does not affect what you see on this page - only what leaves the site when a report is copied, downloaded, or emailed. Turn it off if you're sending the report somewhere you trust with the full detail (e.g. your own developer).", 'check-system-details' ); ?></p>
        <span class="csd-tools-result csd-redact-result"></span>
    </div>
</div>

<!-- Test Email -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Test Email', 'check-system-details' ); ?><?php echo csd_tooltip( __( "Confirms your site's mail sending actually works - a common cause of missed password resets, notifications, and form submissions.", 'check-system-details' ) ); ?></h2>
    <div class="csd-section-body">
        <p>
            <?php
                /* translators: %s: site admin email address */
                printf( esc_html__( 'Send a test email to %s to confirm this site can actually deliver mail.', 'check-system-details' ), esc_html( get_option( 'admin_email' ) ) );
            ?>
        </p>
        <button type="button" class="button button-secondary csd-send-test-email"><?php esc_html_e( 'Send Test Email', 'check-system-details' ); ?></button>
        <span class="csd-tools-result csd-test-email-result"></span>
    </div>
</div>

<!-- Snapshot & Compare -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Snapshot & Compare', 'check-system-details' ); ?><?php echo csd_tooltip( __( 'Useful when something breaks after a plugin/theme update - save a snapshot before, then compare after to see exactly what changed.', 'check-system-details' ) ); ?></h2>
    <div class="csd-section-body">
        <p><?php esc_html_e( 'Save a snapshot of the current report, then compare it against a later report to see exactly what changed after something broke.', 'check-system-details' ); ?></p>
        <p class="csd-snapshot-status">
            <?php if ( $csd_has_snapshot ) : ?>
                <?php
                    printf(
                        /* translators: %s: date and time the snapshot was saved */
                        esc_html__( 'Last snapshot saved: %s', 'check-system-details' ),
                        '<strong>' . esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $csd_snapshot['time'] ) ) . '</strong>'
                    );
                ?>
            <?php else : ?>
                <?php esc_html_e( 'No snapshot saved yet.', 'check-system-details' ); ?>
            <?php endif; ?>
        </p>
        <button type="button" class="button button-secondary csd-save-snapshot"><?php esc_html_e( 'Save Snapshot Now', 'check-system-details' ); ?></button>
        <button type="button" class="button button-secondary csd-compare-snapshot" <?php echo $csd_has_snapshot ? '' : 'disabled'; ?>><?php esc_html_e( 'Compare Current vs Snapshot', 'check-system-details' ); ?></button>
        <span class="csd-tools-result csd-snapshot-result"></span>
        <div class="csd-snapshot-diff"></div>
    </div>
</div>

<!-- Email Report -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Email Report', 'check-system-details' ); ?><?php echo csd_tooltip( __( 'Send this report immediately to any address, or schedule automatic delivery to keep an eye on the site over time.', 'check-system-details' ) ); ?></h2>
    <div class="csd-section-body">
        <p>
            <label for="csd-report-email"><?php esc_html_e( 'Send the current report to:', 'check-system-details' ); ?></label><br />
            <input type="email" id="csd-report-email" class="regular-text" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" />
            <button type="button" class="button button-secondary csd-send-report-now"><?php esc_html_e( 'Send Now', 'check-system-details' ); ?></button>
        </p>
        <span class="csd-tools-result csd-send-report-result"></span>

        <hr />

        <p>
            <label>
                <input type="checkbox" id="csd-recurring-enabled" <?php checked( ! empty( $csd_recurring['enabled'] ) ); ?> />
                <?php esc_html_e( 'Automatically email this report on a schedule', 'check-system-details' ); ?>
            </label>
        </p>
        <p>
            <label for="csd-recurring-recipient"><?php esc_html_e( 'Recipient:', 'check-system-details' ); ?></label><br />
            <input type="email" id="csd-recurring-recipient" class="regular-text" value="<?php echo esc_attr( $csd_recurring['recipient'] ); ?>" />
        </p>
        <p>
            <label for="csd-recurring-frequency"><?php esc_html_e( 'Frequency:', 'check-system-details' ); ?></label><br />
            <select id="csd-recurring-frequency">
                <option value="csd_weekly" <?php selected( $csd_recurring['frequency'], 'csd_weekly' ); ?>><?php esc_html_e( 'Weekly', 'check-system-details' ); ?></option>
                <option value="csd_monthly" <?php selected( $csd_recurring['frequency'], 'csd_monthly' ); ?>><?php esc_html_e( 'Monthly', 'check-system-details' ); ?></option>
            </select>
        </p>
        <button type="button" class="button button-primary csd-save-recurring-settings"><?php esc_html_e( 'Save Schedule', 'check-system-details' ); ?></button>
        <span class="csd-tools-result csd-recurring-result"></span>
    </div>
</div>

<!-- WP-CLI -->
<div class="csd-section">
    <h2 class="csd-section-head">WP-CLI<?php echo csd_tooltip( __( 'For developers/agencies with server access - pulls the same report from the command line without logging into wp-admin.', 'check-system-details' ) ); ?></h2>
    <div class="csd-section-body">
        <p><?php esc_html_e( 'If WP-CLI is available on this server, the same report can be pulled from the terminal:', 'check-system-details' ); ?></p>
        <pre>wp check-system-details</pre>
    </div>
</div>
