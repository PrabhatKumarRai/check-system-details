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
        $robots = 'Error fetching robots.txt!';
        foreach($get_robots["errors"] as $error => $value){
            $robots .= '<br>Error: ' . $error . ' ' . $value[0];
        }
    }
    else{
        $robots = $get_robots['body'];
    }
?>

<!-- Robots.txt -->
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">Robots.txt Content</h2>
    <div class="check-system-details-section-body">
        <pre><?php echo $robots; ?></pre>
    </div>
</div>