<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

    // Get Robots.txt

    //Get Robots.txt URL
    $root_url = site_url();
    $url = parse_url( $root_url );
    if( array_key_exists('path', $url) ){
        $url = $url['scheme'] . '://' . $url['host'] . '/robots.txt';
    }
    else{
        $url = $root_url . '/robots.txt';
    }

    // Get Robots.txt content
    $get_robots = (array) wp_remote_get($url);
    $robots = '';

    if( false !== array_key_exists("errors", $get_robots) ){
        $robots = __( 'Error fetching robots.txt!', 'check-system-details' ) . "\n";
        foreach($get_robots["errors"] as $error => $value){
            /* translators: 1: error code, 2: error message */
            $robots .= sprintf( __( 'Error: %1$s %2$s', 'check-system-details' ), $error, $value[0] ) . "\n";
        }
    }
    else{
        $robots = $get_robots['body'];
    }
?>

<!-- Robots.txt -->
<div class="csd-section">
    <h2 class="csd-section-head csd-section-head-flex">
        <span><?php esc_html_e( 'Robots.txt Content', 'check-system-details' ); ?><?php echo csd_tooltip( __( 'A file that tells search engine crawlers which parts of your site they are allowed to access.', 'check-system-details' ) ); ?></span>
        <button type="button" class="button button-small csd-copy-file"><?php esc_html_e( 'Copy', 'check-system-details' ); ?></button>
    </h2>
    <div class="csd-section-body">
        <pre><?php echo esc_html( $robots ); ?></pre>
        <textarea class="csd-report-textarea csd-file-copy-source" readonly><?php echo esc_textarea( $robots ); ?></textarea>
    </div>
</div>