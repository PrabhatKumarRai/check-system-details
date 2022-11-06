<?php 
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

?>

<!-- Server Details -->
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">Server Details</h2>
    <div class="check-system-details-section-body">
        <table>
            <?php     
                foreach($csd_server_details as $csd_field => $csd_value):
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

<!-- Database Details -->
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">Database Details</h2>
    <div class="check-system-details-section-body">
        <table>
            <?php
                foreach($csd_database_details as $csd_field => $csd_value):
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

<!-- Database Tables -->
<div class="check-system-details-section">
    <h2 class="check-system-details-section-head">Database Tables</h2>
    <div class="check-system-details-section-body">
        <table>
            <?php
                foreach ($csd_tables as $csd_table):
                    foreach ($csd_table as $csd_t):
                        ?>
                        <tr>
                            <td><?php echo esc_html( $csd_t ); ?></td>
                        </tr>
                        <?php
                    endforeach;
                endforeach;
            ?>
        </table>
    </div>
</div>