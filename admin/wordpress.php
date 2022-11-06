<?php 
    // Fetch and store WordPress Details
    global $wp_version;
    $csd_wordpress_details = [
        'WordPress address' => get_option('home'),
        'Site address' => get_option('siteurl'),
        'WordPress version' => $wp_version,
        'WordPress multisite' => is_multisite()? 'true': 'false',
        'WordPress memory limit' => WP_MEMORY_LIMIT,
        'WordPress debug mode' => WP_DEBUG? 'true': 'false',
        'WordPress debug log' => WP_DEBUG_LOG? 'true': 'false',
        'WordPress cron' => get_option('cron')? 'true': 'false',
        'WordPress Language' => get_locale()
    ];

    // Fetch and store Active Theme details
    $csd_theme = wp_get_theme();
    $csd_authorURI = (string)(new SimpleXMLElement($csd_theme->Author))['href'];
    $csd_authorURI = ( true !== empty($csd_authorURI) )? $csd_authorURI: $csd_theme->AuthorURI;
    $csd_theme_details = [
        'Name' => $csd_theme->Name,
        'Version' => $csd_theme->Version,
        'Description' => $csd_theme->Description,
        'Author' => strip_tags($csd_theme->Author),
        'AuthorURI' => $csd_authorURI
    ];

    // Fetch Installed Plugin details
   $csd_plugin_details = get_plugins();
?>

<!-- WordPress Details -->
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">WordPress Details</h2>
    <div class="check-system-details-section-body">
        <table>
            <?php     
                foreach($csd_wordpress_details as $csd_field => $csd_value):
                    ?>
                    <tr>
                        <td><?php echo esc_html( $csd_field ); ?></td>
                        <td><?php echo esc_html( $csd_value ); ?></td>
                    </tr>
                    <?php
                endforeach;
            ?>
        </table>
    </div>
</div>

<!-- Active Theme Details -->
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">Current Theme</h2>
    <div class="check-system-details-section-body">
        <table>
            <?php     
                foreach($csd_theme_details as $csd_field => $csd_value):
                    if( $csd_field !== 'Author' && $csd_field !== 'AuthorURI' ):
                    ?>
                    <tr>
                        <td><?php echo esc_html( $csd_field ); ?></td>
                        <td><?php echo esc_html( $csd_value ); ?></td>
                    </tr>
                    <?php
                    endif;
                endforeach;                    
                ?>
                <tr>
                    <td>Author</td>
                    <td>
                        <a href="<?php echo esc_url($csd_theme_details['AuthorURI']); ?>" target="_blank"><?php echo esc_html( $csd_theme_details['Author'] ); ?></a>
                    </td>
                </tr>
                <?php
            ?>
        </table>
    </div>
</div>

<!-- Installed Plugin Details -->
<div class="check-system-details-section check-system-details-plugin-details">
    <h2 class="check-system-details-section-head">Installed Plugins</h2>
    <div class="check-system-details-section-body">
        <table>
            <?php                
                foreach( $csd_plugin_details as $csd_field => $csd_value):
                    ?>
                    <tr>
                        <td><a href="<?php echo esc_url($csd_value['PluginURI']); ?>" target="_blank"><?php echo esc_html( $csd_value['Name'] ); ?></a></td>
                        <td>version <?php echo esc_html( $csd_value['Version'] ); ?></td>
                        <td>by <a href="<?php echo esc_url($csd_value['AuthorURI']); ?>" target="_blank"><?php echo esc_html( $csd_value['Author'] ); ?></a></td>
                    </tr>
                    <?php
                endforeach;
            ?>
        </table>
    </div>
</div>