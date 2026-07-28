<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

    // Get debug.log - last 100 lines

    $debug_log_is_html = false;
    $csd_debug_log_plain = '';

    if( false !== WP_DEBUG ){
       $path = ABSPATH.'wp-content/debug.log';
        if( false !== file_exists( $path ) ){
            $debug_log = file($path);
            // Get last 100 lines of debug.log file and reverse it to print the content bottom to top
            $debug_log = array_reverse(array_splice($debug_log, count($debug_log) - 100));
            $csd_debug_log_plain = implode( "\n", $debug_log );
            $debug_log = implode('<br><br>', array_map('esc_html', $debug_log));
            $debug_log_is_html = true;
        }
        else{
            /* translators: %s: path to the missing debug.log file */
            $debug_log = sprintf( __( 'Debugging is enabled but debug.log file not found under: %s', 'check-system-details' ), $path );
            $csd_debug_log_plain = $debug_log;
        }
    }
    else{
        $debug_log = sprintf(
            /* translators: %s: link to the WordPress debugging guide */
            __( 'WP_DEBUG is disabled! Enable debugging by following %s.', 'check-system-details' ),
            '<a href="https://wordpress.org/support/article/debugging-in-wordpress/" target="_blank">' . esc_html__( 'this guide', 'check-system-details' ) . '</a>'
        );
        $debug_log_is_html = true;
        $csd_debug_log_plain = __( 'WP_DEBUG is disabled.', 'check-system-details' );
    }
?>

<!-- debug.log -->
<div class="csd-section">
    <h2 class="csd-section-head csd-section-head-flex">
        <span><?php esc_html_e( 'Debug.log - last 100 lines', 'check-system-details' ); ?><?php echo csd_tooltip( __( 'A log file WordPress writes PHP errors, warnings, and notices to when WP_DEBUG_LOG is enabled - useful for diagnosing what is going wrong on the site.', 'check-system-details' ) ); ?></span>
        <button type="button" class="button button-small csd-copy-file"><?php esc_html_e( 'Copy', 'check-system-details' ); ?></button>
    </h2>
    <div class="csd-section-body">
        <p><?php echo $debug_log_is_html ? $debug_log : esc_html( $debug_log ); ?></p>
        <textarea class="csd-report-textarea csd-file-copy-source" readonly><?php echo esc_textarea( $csd_debug_log_plain ); ?></textarea>
    </div>
</div>