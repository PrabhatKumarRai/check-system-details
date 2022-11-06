<?php 

    // Get debug.log - last 100 lines

    if( false !== WP_DEBUG ){
       $path = ABSPATH.'wp-content/debug.log';
        if( false !== file_exists( $path ) ){
            $debug_log = file($path);
            // Get last 100 lines of debug.log file and reverse it to print the content bottom to top
            $debug_log = array_reverse(array_splice($debug_log, count($debug_log) - 100));
            $debug_log = implode('<br><br>', $debug_log);
        }
        else{
            $debug_log = 'Debugging is enabled but debug.log file not found under: '.$path;
        }
    }
    else{
        $debug_log = 'WP_DEBUG is disabled! Enable debugging by following <a href="https://wordpress.org/support/article/debugging-in-wordpress/" target="_blank">this guide</a>.';
    }
?>

<!-- debug.log -->
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">Debug.log - last 100 lines</h2>
    <div class="check-system-details-section-body">
        <p><?php echo $debug_log; ?></p>
    </div>
</div>