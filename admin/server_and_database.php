<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

    // Internal keys stay stable English identifiers for lookups; these two
    // maps translate only what's displayed.
    $csd_server_field_labels = [
        'Max input timeout'    => __( 'Max input timeout', 'check-system-details' ),
        'Max input vars'       => __( 'Max input vars', 'check-system-details' ),
        'Max file upload'      => __( 'Max file upload', 'check-system-details' ),
        'Max file upload size' => __( 'Max file upload size', 'check-system-details' ),
    ];

    $csd_server_field_tooltips = [
        'Max input timeout'    => __( 'How many seconds PHP spends parsing input data (max_input_time) before giving up. Can affect large form submissions.', 'check-system-details' ),
        'Max input vars'       => __( 'The maximum number of input fields PHP accepts in one request (max_input_vars). Large forms (e.g. big menus) can hit this limit.', 'check-system-details' ),
        'Max file upload'      => __( 'The maximum number of files that can be uploaded in a single request (max_file_uploads).', 'check-system-details' ),
        'Max file upload size' => __( 'The largest file size PHP will accept in an upload (upload_max_filesize).', 'check-system-details' ),
    ];

    $csd_database_field_labels = [
        'Database Name'    => __( 'Database Name', 'check-system-details' ),
        'Database Host'    => __( 'Database Host', 'check-system-details' ),
        'Database Charset' => __( 'Database Charset', 'check-system-details' ),
        'Database Prefix'  => __( 'Database Prefix', 'check-system-details' ),
    ];

    $csd_database_field_tooltips = [
        'Database Name'    => __( 'The name of the MySQL/MariaDB database WordPress stores its data in.', 'check-system-details' ),
        'Database Host'    => __( 'The server address WordPress connects to for its database.', 'check-system-details' ),
        'Database Charset' => __( 'The character encoding used by the database (commonly utf8mb4, which supports full Unicode including emoji).', 'check-system-details' ),
        'Database Prefix'  => __( 'The prefix WordPress adds to its table names (e.g. wp_), letting multiple installs share one database.', 'check-system-details' ),
    ];
?>

<!-- Server Details -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Server Details', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <table>
            <?php
                foreach($csd_server_details as $csd_field => $csd_value):
                    ?>
                    <tr>
                        <td>
                            <?php echo esc_html( $csd_server_field_labels[ $csd_field ] ?? $csd_field ); ?>
                            <?php if ( isset( $csd_server_field_tooltips[ $csd_field ] ) ) : ?>
                                <?php echo csd_tooltip( $csd_server_field_tooltips[ $csd_field ] ); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( $csd_value ); ?></td>
                    </tr>
                    <?php
                endforeach;
            ?>
        </table>
    </div>
</div>

<!-- Database Details -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Database Details', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <table>
            <?php
                foreach($csd_database_details as $csd_field => $csd_value):
                    ?>
                    <tr>
                        <td>
                            <?php echo esc_html( $csd_database_field_labels[ $csd_field ] ?? $csd_field ); ?>
                            <?php if ( isset( $csd_database_field_tooltips[ $csd_field ] ) ) : ?>
                                <?php echo csd_tooltip( $csd_database_field_tooltips[ $csd_field ] ); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( $csd_value ); ?></td>
                    </tr>
                    <?php
                endforeach;
            ?>
        </table>
    </div>
</div>

<!-- Database Tables -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Database Tables', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
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