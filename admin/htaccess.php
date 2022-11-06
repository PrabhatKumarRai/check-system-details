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
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">htaccess file Content</h2>
    <div class="check-system-details-section-body">
        <pre><?php echo $htaccess; ?></pre>
    </div>
</div>