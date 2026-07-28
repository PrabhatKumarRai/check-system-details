<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Get htaccess content

$path = ABSPATH.'.htaccess';
if( false !== file_exists($path) ){
    $htaccess = trim(file_get_contents($path));
}
else{
    $htaccess = __( 'File not found!', 'check-system-details' );
}
?>

<!-- .htaccess -->
<div class="csd-section">
    <h2 class="csd-section-head csd-section-head-flex">
        <span><?php esc_html_e( 'htaccess file Content', 'check-system-details' ); ?><?php echo csd_tooltip( __( 'A configuration file used by Apache web servers to control URL rewriting, redirects, and access rules for your site.', 'check-system-details' ) ); ?></span>
        <button type="button" class="button button-small csd-copy-file"><?php esc_html_e( 'Copy', 'check-system-details' ); ?></button>
    </h2>
    <div class="csd-section-body">
        <pre><?php echo esc_html( $htaccess ); ?></pre>
        <textarea class="csd-report-textarea csd-file-copy-source" readonly><?php echo esc_textarea( $htaccess ); ?></textarea>
    </div>
</div>