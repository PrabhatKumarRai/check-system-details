<?php
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
        $robots = "Error fetching robots.txt!\n";
        foreach($get_robots["errors"] as $error => $value){
            $robots .= "Error: {$error} {$value[0]}\n";
        }
    }
    else{
        $robots = $get_robots['body'];
    }
?>

<!-- Robots.txt -->
<div class="csd-section">
    <h2 class="csd-section-head">Robots.txt Content</h2>
    <div class="csd-section-body">
        <pre><?php echo esc_html( $robots ); ?></pre>
    </div>
</div>