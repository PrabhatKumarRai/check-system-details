<?php 

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
            $debug_log = 'Debugging is enabled but debug.log file not found under: '.$path;
            $csd_debug_log_plain = $debug_log;
        }
    }
    else{
        $debug_log = 'WP_DEBUG is disabled! Enable debugging by following <a href="https://wordpress.org/support/article/debugging-in-wordpress/" target="_blank">this guide</a>.';
        $debug_log_is_html = true;
        $csd_debug_log_plain = 'WP_DEBUG is disabled.';
    }
?>

<!-- debug.log -->
<div class="csd-section">
    <h2 class="csd-section-head">Debug.log - last 100 lines</h2>
    <div class="csd-section-body">
        <p><?php echo $debug_log_is_html ? $debug_log : esc_html( $debug_log ); ?></p>
    </div>
</div>