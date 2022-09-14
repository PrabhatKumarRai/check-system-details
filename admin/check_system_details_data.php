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

    // Fetch and store Server Details
    $csd_ini_get_all = ini_get_all();
    $csd_server_details = [
        'Max input timeout' => $csd_ini_get_all['max_input_time']['local_value'],
        'Max input vars' => $csd_ini_get_all['max_input_vars']['local_value'],
        'Max file upload' => $csd_ini_get_all['max_file_uploads']['local_value'],
        'Max file upload size' => $csd_ini_get_all['upload_max_filesize']['local_value']
    ];

    //Store Database Details
    global $table_prefix;
    $csd_database_details = [
        'Database Name' => DB_NAME,        
        'Database Host' => DB_HOST,        
        'Database Charset' => DB_CHARSET,        
        'Database Prefix' => $table_prefix
    ];    

    // Gets all existing Database Tables
    global $wpdb;
    $csd_tables=$wpdb->get_results("SHOW TABLES");    

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

<div class="check-system-details-container">
    <div class="check-system-details-head">
        <h1 class="check-system-details-title">Check System Details</h1>
    </div>
    <div class="check-system-details-body">        
        <!-- WordPress Details -->
        <div class="check-system-details-section">
            <h2>WordPress Details</h2>
            <table>
                <?php     
                    foreach($csd_wordpress_details as $csd_field => $csd_value):
                        ?>
                        <tr>
                            <td><?= esc_html( $csd_field ); ?></td>
                            <td><?= esc_html( $csd_value ); ?></td>
                        </tr>
                        <?php
                    endforeach;
                ?>
            </table>
        </div>
        <!-- Server Details -->
        <div class="check-system-details-section">
            <h2>Server Details</h2>
            <table>
                <?php     
                    foreach($csd_server_details as $csd_field => $csd_value):
                        ?>
                        <tr>
                            <td><?= esc_html( $csd_field ); ?></td>
                            <td><?= esc_html( $csd_value ); ?></td>
                        </tr>
                        <?php
                    endforeach;
                ?>
            </table>
        </div>
        <!-- Database Details -->
        <div class="check-system-details-section">
            <h2>Database Details</h2>
            <table>
                <?php
                    foreach($csd_database_details as $csd_field => $csd_value):
                        ?>
                        <tr>
                            <td><?= esc_html( $csd_field ); ?></td>
                            <td><?= esc_html( $csd_value ); ?></td>
                        </tr>
                        <?php
                    endforeach;
                ?>
            </table>
        </div>
        <!-- Database Tables -->
        <div class="check-system-details-section">
            <h2>Database Tables</h2>
            <table>
                <?php
                    foreach ($csd_tables as $csd_table):
                        foreach ($csd_table as $csd_t):
                            ?>
                            <tr>
                                <td><?= esc_html( $csd_t ); ?></td>
                            </tr>
                            <?php
                        endforeach;
                    endforeach;
                ?>
            </table>
        </div>
        <!-- Active Theme Details -->
        <div class="check-system-details-section">
            <h2>Current Theme</h2>
            <table>
                <?php     
                    foreach($csd_theme_details as $csd_field => $csd_value):
                        if( $csd_field !== 'Author' && $csd_field !== 'AuthorURI' ):
                        ?>
                        <tr>
                            <td><?= esc_html( $csd_field ); ?></td>
                            <td>
                                <?= esc_html( $csd_value ); ?>
                            </td>
                        </tr>
                        <?php
                        endif;
                    endforeach;                    
                    ?>
                    <tr>
                        <td>Author</td>
                        <td>
                            <a href="<?= esc_url($csd_theme_details['AuthorURI']) ?>"><?= esc_html( $csd_theme_details['Author'] ); ?></a>
                        </td>
                    </tr>
                    <?php
                ?>
            </table>
        </div>
        <!-- Installed Plugin Details -->
        <div class="check-system-details-section check-system-details-plugin-details">
            <h2>Installed Plugins</h2>
            <table>
                <?php                
                    foreach( $csd_plugin_details as $csd_field => $csd_value):
                        ?>
                        <tr>
                            <td><a href="<?= $csd_value['PluginURI'] ?>"><?= esc_html( $csd_value['Name'] ); ?></a></td>
                            <td>version <?= esc_html( $csd_value['Version'] ); ?></td>
                            <td>by <a href="<?= esc_url($csd_value['AuthorURI']); ?>" target="_blank"><?= esc_html( $csd_value['Author'] ); ?></a></td>
                        </tr>
                        <?php
                    endforeach;
                ?>
            </table>
        </div>
    </div>
    <div class="check-system-details-footer">
        <p class="check-system-details-title">Developed by <i>Prabhat Rai</i></p>
    </div>
</div>