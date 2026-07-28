<?php 

// Get htaccess content

$path = ABSPATH.'.htaccess';
if( false !== file_exists($path) ){
    $htaccess = trim(file_get_contents($path));
}
else{
    $htaccess = "File not found!";
}
?>

<!-- .htaccess -->
<div class="csd-section">
    <h2 class="csd-section-head">htaccess file Content</h2>
    <div class="csd-section-body">
        <pre><?php echo esc_html( $htaccess ); ?></pre>
    </div>
</div>