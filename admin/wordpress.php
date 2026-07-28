<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

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

    // Pending update checks - reads WordPress's own already-cached update
    // transients (populated by core's own scheduled update checks); this
    // does not make any new outbound request itself.
    if ( ! function_exists( 'get_core_updates' ) ) {
        require_once ABSPATH . 'wp-admin/includes/update.php';
    }

    $csd_core_update_version = null;
    $csd_core_updates        = get_core_updates();
    if ( ! empty( $csd_core_updates ) && isset( $csd_core_updates[0]->response ) && 'upgrade' === $csd_core_updates[0]->response ) {
        $csd_core_update_version = $csd_core_updates[0]->current;
    }

    $csd_plugin_updates   = get_plugin_updates();
    $csd_theme_updates    = get_theme_updates();
    $csd_theme_stylesheet = $csd_theme->get_stylesheet();

    // Internal keys of $csd_wordpress_details stay as stable English identifiers
    // (used for lookups/comparisons above); these two maps translate only what's displayed.
    $csd_wordpress_field_labels = [
        'WordPress address'      => __( 'WordPress address', 'check-system-details' ),
        'Site address'           => __( 'Site address', 'check-system-details' ),
        'WordPress version'      => __( 'WordPress version', 'check-system-details' ),
        'WordPress multisite'    => __( 'WordPress multisite', 'check-system-details' ),
        'WordPress memory limit' => __( 'WordPress memory limit', 'check-system-details' ),
        'WordPress debug mode'   => __( 'WordPress debug mode', 'check-system-details' ),
        'WordPress debug log'    => __( 'WordPress debug log', 'check-system-details' ),
        'WordPress cron'         => __( 'WordPress cron', 'check-system-details' ),
        'WordPress Language'     => __( 'WordPress Language', 'check-system-details' ),
    ];

    $csd_wordpress_field_tooltips = [
        'WordPress address'      => __( 'The WordPress Address (WP_HOME) - where your WordPress core files live.', 'check-system-details' ),
        'Site address'           => __( 'The Site Address (WP_SITEURL) - the URL visitors use to reach your site. Usually the same as WordPress address.', 'check-system-details' ),
        'WordPress version'      => __( 'The version of WordPress core currently installed.', 'check-system-details' ),
        'WordPress multisite'    => __( 'Whether this install is part of a WordPress Multisite network (multiple sites sharing one WordPress install).', 'check-system-details' ),
        'WordPress memory limit' => __( 'The maximum memory PHP will allocate to WordPress (WP_MEMORY_LIMIT). Low values can cause "allowed memory size exhausted" errors.', 'check-system-details' ),
        'WordPress debug mode'   => __( 'Whether WP_DEBUG is enabled, surfacing PHP errors/warnings for troubleshooting. Should generally be off on a live site.', 'check-system-details' ),
        'WordPress debug log'    => __( 'Whether WP_DEBUG_LOG is enabled, writing errors to wp-content/debug.log.', 'check-system-details' ),
        'WordPress cron'         => __( "Whether WordPress's built-in pseudo-cron system has any scheduled tasks stored.", 'check-system-details' ),
        'WordPress Language'     => __( "The site's active language/locale.", 'check-system-details' ),
    ];
?>

<!-- WordPress Details -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'WordPress Details', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <table>
            <?php
                foreach($csd_wordpress_details as $csd_field => $csd_value):
                    ?>
                    <tr>
                        <td>
                            <?php echo esc_html( $csd_wordpress_field_labels[ $csd_field ] ?? $csd_field ); ?>
                            <?php if ( isset( $csd_wordpress_field_tooltips[ $csd_field ] ) ) : ?>
                                <?php echo csd_tooltip( $csd_wordpress_field_tooltips[ $csd_field ] ); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo esc_html( $csd_value ); ?>
                            <?php if ( 'WordPress version' === $csd_field && $csd_core_update_version ) : ?>
                                <a class="csd-update-badge" href="<?php echo esc_url( admin_url( 'update-core.php' ) ); ?>">
                                    <?php
                                        /* translators: %s: available WordPress core version */
                                        printf( esc_html__( 'Update available: %s', 'check-system-details' ), esc_html( $csd_core_update_version ) );
                                    ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            ?>
        </table>
    </div>
</div>

<!-- Scheduled Cron Events -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Scheduled Cron Events', 'check-system-details' ); ?><?php echo csd_tooltip( __( "WordPress's task scheduler. Each row is a task waiting to run - like publishing a scheduled post or checking for updates.", 'check-system-details' ) ); ?></h2>
    <div class="csd-section-body">
        <?php if ( empty( $csd_cron_events ) ) : ?>
            <p><?php esc_html_e( 'No scheduled cron events found.', 'check-system-details' ); ?></p>
        <?php else : ?>
            <table>
                <tr>
                    <td><strong><?php esc_html_e( 'Hook', 'check-system-details' ); ?></strong></td>
                    <td><strong><?php esc_html_e( 'Next Run (GMT)', 'check-system-details' ); ?></strong></td>
                    <td><strong><?php esc_html_e( 'Recurrence', 'check-system-details' ); ?></strong></td>
                </tr>
                <?php foreach ( $csd_cron_events as $csd_cron_event ) : ?>
                    <tr>
                        <td><?php echo esc_html( $csd_cron_event['hook'] ); ?></td>
                        <td><?php echo esc_html( gmdate( 'Y-m-d H:i:s', $csd_cron_event['next_run'] ) ); ?></td>
                        <td><?php echo esc_html( $csd_cron_event['recurrence'] ); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
</div>

<!-- Active Theme Details -->
<div class="csd-section">
    <h2 class="csd-section-head"><?php esc_html_e( 'Current Theme', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <table>
            <?php
                $csd_theme_field_labels = [
                    'Name'        => __( 'Name', 'check-system-details' ),
                    'Version'     => __( 'Version', 'check-system-details' ),
                    'Description' => __( 'Description', 'check-system-details' ),
                ];
                foreach($csd_theme_details as $csd_field => $csd_value):
                    if( $csd_field !== 'Author' && $csd_field !== 'AuthorURI' ):
                    ?>
                    <tr>
                        <td><?php echo esc_html( $csd_theme_field_labels[ $csd_field ] ?? $csd_field ); ?></td>
                        <td>
                            <?php echo esc_html( $csd_value ); ?>
                            <?php if ( 'Version' === $csd_field && isset( $csd_theme_updates[ $csd_theme_stylesheet ]->update['new_version'] ) ) : ?>
                                <a class="csd-update-badge" href="<?php echo esc_url( admin_url( 'themes.php' ) ); ?>">
                                    <?php
                                        /* translators: %s: available theme version */
                                        printf( esc_html__( 'Update available: %s', 'check-system-details' ), esc_html( $csd_theme_updates[ $csd_theme_stylesheet ]->update['new_version'] ) );
                                    ?>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php
                    endif;
                endforeach;
                ?>
                <tr>
                    <td><?php esc_html_e( 'Author', 'check-system-details' ); ?></td>
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
<div class="csd-section csd-plugin-details">
    <h2 class="csd-section-head"><?php esc_html_e( 'Installed Plugins', 'check-system-details' ); ?></h2>
    <div class="csd-section-body">
        <table>
            <?php
                foreach( $csd_plugin_details as $csd_field => $csd_value):
                    ?>
                    <tr>
                        <td><a href="<?php echo esc_url($csd_value['PluginURI']); ?>" target="_blank"><?php echo esc_html( $csd_value['Name'] ); ?></a></td>
                        <td>
                            <?php
                                /* translators: %s: plugin version number */
                                printf( esc_html__( 'version %s', 'check-system-details' ), esc_html( $csd_value['Version'] ) );
                            ?>
                            <?php if ( isset( $csd_plugin_updates[ $csd_field ]->update->new_version ) ) : ?>
                                <a class="csd-update-badge" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>">
                                    <?php
                                        /* translators: %s: available plugin version */
                                        printf( esc_html__( 'Update available: %s', 'check-system-details' ), esc_html( $csd_plugin_updates[ $csd_field ]->update->new_version ) );
                                    ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                                /* translators: %s: linked plugin author name */
                                printf( esc_html__( 'by %s', 'check-system-details' ), '<a href="' . esc_url( $csd_value['AuthorURI'] ) . '" target="_blank">' . esc_html( $csd_value['Author'] ) . '</a>' );
                            ?>
                        </td>
                    </tr>
                    <?php
                endforeach;
            ?>
        </table>
    </div>
</div>