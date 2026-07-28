<?php
/**
 * Runs a set of local diagnostic checks and renders them as a
 * status table. No external network requests are made here.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! function_exists( 'csd_health_status' ) ) {
    function csd_health_status( $level, $label, $message ) {
        return [
            'level'   => $level, // 'good', 'warning', 'critical', 'info'
            'label'   => $label,
            'message' => $message,
        ];
    }
}

$csd_health_checks = [];

// PHP version
$csd_php_version = phpversion();
if ( version_compare( $csd_php_version, '7.4', '<' ) ) {
    /* translators: %s: PHP version number */
    $csd_health_checks[] = csd_health_status( 'critical', __( 'PHP Version', 'check-system-details' ), sprintf( __( 'Running PHP %s. This version no longer receives security updates - upgrade as soon as possible.', 'check-system-details' ), $csd_php_version ) );
} elseif ( version_compare( $csd_php_version, '8.1', '<' ) ) {
    /* translators: %s: PHP version number */
    $csd_health_checks[] = csd_health_status( 'warning', __( 'PHP Version', 'check-system-details' ), sprintf( __( 'Running PHP %s. Consider upgrading to PHP 8.1 or newer for better performance and longer support.', 'check-system-details' ), $csd_php_version ) );
} else {
    /* translators: %s: PHP version number */
    $csd_health_checks[] = csd_health_status( 'good', __( 'PHP Version', 'check-system-details' ), sprintf( __( 'Running PHP %s.', 'check-system-details' ), $csd_php_version ) );
}

// PHP extensions commonly needed by WordPress plugins/themes
$csd_required_extensions = [
    'curl'     => __( 'Used for outbound HTTP requests (updates, APIs, webhooks).', 'check-system-details' ),
    'mbstring' => __( 'Used for correct handling of multibyte text.', 'check-system-details' ),
    'gd'       => __( 'Used for image processing (thumbnails, resizing). Imagick can substitute for this.', 'check-system-details' ),
    'zip'      => __( 'Used for plugin/theme install and export/import.', 'check-system-details' ),
    'openssl'  => __( 'Used for secure connections (HTTPS, encryption).', 'check-system-details' ),
    'dom'      => __( 'Used for XML/HTML parsing (feeds, sitemaps, importers).', 'check-system-details' ),
];

foreach ( $csd_required_extensions as $csd_ext => $csd_ext_reason ) {
    $csd_ext_loaded = extension_loaded( $csd_ext ) || ( 'gd' === $csd_ext && extension_loaded( 'imagick' ) );
    /* translators: %s: PHP extension name */
    $csd_ext_label = sprintf( __( 'PHP Extension: %s', 'check-system-details' ), $csd_ext );
    if ( $csd_ext_loaded ) {
        $csd_health_checks[] = csd_health_status( 'good', $csd_ext_label, __( 'Loaded.', 'check-system-details' ) );
    } else {
        $csd_health_checks[] = csd_health_status( 'warning', $csd_ext_label, __( 'Not loaded.', 'check-system-details' ) . ' ' . $csd_ext_reason );
    }
}

// OPcache
if ( extension_loaded( 'Zend OPcache' ) && function_exists( 'opcache_get_status' ) ) {
    $csd_opcache_status = @opcache_get_status( false );
    if ( $csd_opcache_status && ! empty( $csd_opcache_status['opcache_enabled'] ) ) {
        $csd_health_checks[] = csd_health_status( 'good', __( 'OPcache', 'check-system-details' ), __( 'Enabled - PHP bytecode is cached for faster execution.', 'check-system-details' ) );
    } else {
        $csd_health_checks[] = csd_health_status( 'warning', __( 'OPcache', 'check-system-details' ), __( 'Installed but not enabled. Enabling it can meaningfully speed up PHP execution.', 'check-system-details' ) );
    }
} else {
    $csd_health_checks[] = csd_health_status( 'warning', __( 'OPcache', 'check-system-details' ), __( 'Not available on this server. Ask your host to enable the OPcache extension for better performance.', 'check-system-details' ) );
}

// WP_DEBUG display risk
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    $csd_display_errors = defined( 'WP_DEBUG_DISPLAY' ) ? WP_DEBUG_DISPLAY : true;
    if ( $csd_display_errors ) {
        $csd_health_checks[] = csd_health_status( 'critical', __( 'Debug Display', 'check-system-details' ), __( 'WP_DEBUG is on and WP_DEBUG_DISPLAY is not disabled - PHP errors/warnings may be visible to site visitors. Set WP_DEBUG_DISPLAY to false and log to file instead.', 'check-system-details' ) );
    } else {
        $csd_health_checks[] = csd_health_status( 'good', __( 'Debug Display', 'check-system-details' ), __( 'WP_DEBUG is on, but errors are not displayed to visitors.', 'check-system-details' ) );
    }
} else {
    $csd_health_checks[] = csd_health_status( 'good', __( 'Debug Display', 'check-system-details' ), __( 'WP_DEBUG is off.', 'check-system-details' ) );
}

// File editor
if ( defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT ) {
    $csd_health_checks[] = csd_health_status( 'good', __( 'Plugin/Theme File Editor', 'check-system-details' ), __( 'Disabled via DISALLOW_FILE_EDIT.', 'check-system-details' ) );
} else {
    $csd_health_checks[] = csd_health_status( 'warning', __( 'Plugin/Theme File Editor', 'check-system-details' ), __( "Enabled. If an admin account is compromised, the built-in editor lets an attacker modify plugin/theme files directly. Consider adding define('DISALLOW_FILE_EDIT', true); to wp-config.php.", 'check-system-details' ) );
}

// HTTPS
if ( is_ssl() ) {
    $csd_health_checks[] = csd_health_status( 'good', __( 'HTTPS', 'check-system-details' ), __( 'Site is being served over HTTPS.', 'check-system-details' ) );
} else {
    $csd_health_checks[] = csd_health_status( 'warning', __( 'HTTPS', 'check-system-details' ), __( 'Site is not being served over HTTPS. Browsers may flag it as not secure.', 'check-system-details' ) );
}

// PHP memory limit
$csd_memory_limit = ini_get( 'memory_limit' );
$csd_memory_bytes = wp_convert_hr_to_bytes( $csd_memory_limit );
if ( $csd_memory_bytes > 0 && $csd_memory_bytes < 67108864 ) {
    /* translators: %s: PHP memory_limit value, e.g. 32M */
    $csd_health_checks[] = csd_health_status( 'warning', __( 'PHP Memory Limit', 'check-system-details' ), sprintf( __( 'Set to %s. Some plugins/themes expect at least 64M.', 'check-system-details' ), $csd_memory_limit ) );
} else {
    /* translators: %s: PHP memory_limit value, e.g. 256M */
    $csd_health_checks[] = csd_health_status( 'good', __( 'PHP Memory Limit', 'check-system-details' ), sprintf( __( 'Set to %s.', 'check-system-details' ), $csd_memory_limit ) );
}

// WP-Cron enabled/disabled
if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) {
    $csd_health_checks[] = csd_health_status( 'warning', __( 'WP-Cron', 'check-system-details' ), __( 'Disabled via DISABLE_WP_CRON. Make sure a real system cron job calls wp-cron.php, otherwise scheduled tasks will not run.', 'check-system-details' ) );
} else {
    $csd_health_checks[] = csd_health_status( 'good', __( 'WP-Cron', 'check-system-details' ), __( "WordPress's built-in pseudo-cron is active.", 'check-system-details' ) );
}

// wp-content writability
if ( wp_is_writable( WP_CONTENT_DIR ) ) {
    $csd_health_checks[] = csd_health_status( 'good', __( 'wp-content Directory', 'check-system-details' ), __( 'Writable - plugin/theme/core updates can be installed directly.', 'check-system-details' ) );
} else {
    $csd_health_checks[] = csd_health_status( 'warning', __( 'wp-content Directory', 'check-system-details' ), __( 'Not writable by PHP. Updates may prompt for FTP credentials.', 'check-system-details' ) );
}

// wp-config.php permissions
$csd_wp_config_path = file_exists( ABSPATH . 'wp-config.php' ) ? ABSPATH . 'wp-config.php' : dirname( ABSPATH ) . '/wp-config.php';
if ( file_exists( $csd_wp_config_path ) ) {
    $csd_wp_config_perms = fileperms( $csd_wp_config_path ) & 0777;
    if ( $csd_wp_config_perms & 0002 ) {
        /* translators: %o: octal file permissions, e.g. 0666 */
        $csd_health_checks[] = csd_health_status( 'critical', __( 'wp-config.php Permissions', 'check-system-details' ), sprintf( __( 'File is world-writable (permissions %o). This is a significant security risk - restrict to 600 or 644.', 'check-system-details' ), $csd_wp_config_perms ) );
    } elseif ( $csd_wp_config_perms & 0022 ) {
        /* translators: %o: octal file permissions, e.g. 0664 */
        $csd_health_checks[] = csd_health_status( 'warning', __( 'wp-config.php Permissions', 'check-system-details' ), sprintf( __( 'Permissions are %o. Consider restricting to 600 or 640.', 'check-system-details' ), $csd_wp_config_perms ) );
    } else {
        /* translators: %o: octal file permissions, e.g. 0644 */
        $csd_health_checks[] = csd_health_status( 'good', __( 'wp-config.php Permissions', 'check-system-details' ), sprintf( __( 'Permissions are %o.', 'check-system-details' ), $csd_wp_config_perms ) );
    }
} else {
    $csd_health_checks[] = csd_health_status( 'warning', __( 'wp-config.php Permissions', 'check-system-details' ), __( 'Could not locate wp-config.php to check its permissions.', 'check-system-details' ) );
}

// Persistent object cache (Redis/Memcached/etc.)
if ( function_exists( 'wp_using_ext_object_cache' ) && wp_using_ext_object_cache() ) {
    $csd_health_checks[] = csd_health_status( 'good', __( 'Persistent Object Cache', 'check-system-details' ), __( 'An external object cache (e.g. Redis/Memcached) is active.', 'check-system-details' ) );
} else {
    $csd_health_checks[] = csd_health_status( 'info', __( 'Persistent Object Cache', 'check-system-details' ), __( 'Not detected. WordPress is using its default non-persistent object cache, which resets every request. Optional, but a common upgrade for high-traffic sites.', 'check-system-details' ) );
}

// Page caching plugin detection
$csd_page_cache_signatures = [
    'WP_ROCKET_VERSION'     => 'WP Rocket',
    'W3TC_VERSION'          => 'W3 Total Cache',
    'WPFC_WP_CONTENT_DIR'   => 'WP Fastest Cache',
    'LSCWP_V'               => 'LiteSpeed Cache',
    'CACHE_ENABLER_VERSION' => 'Cache Enabler',
    'WPCACHEHOME'           => 'WP Super Cache',
    'SG_CACHEPRESS_VERSION' => 'SiteGround Optimizer',
];
$csd_detected_cache_plugin = null;
foreach ( $csd_page_cache_signatures as $csd_const => $csd_name ) {
    if ( defined( $csd_const ) ) {
        $csd_detected_cache_plugin = $csd_name;
        break;
    }
}
if ( $csd_detected_cache_plugin ) {
    /* translators: %s: detected caching plugin name */
    $csd_health_checks[] = csd_health_status( 'good', __( 'Page Caching', 'check-system-details' ), sprintf( __( '%s detected.', 'check-system-details' ), $csd_detected_cache_plugin ) );
} elseif ( defined( 'WP_CACHE' ) && WP_CACHE ) {
    $csd_health_checks[] = csd_health_status( 'good', __( 'Page Caching', 'check-system-details' ), __( 'WP_CACHE is enabled by a caching plugin.', 'check-system-details' ) );
} else {
    $csd_health_checks[] = csd_health_status( 'info', __( 'Page Caching', 'check-system-details' ), __( 'No page caching plugin detected. A caching plugin or host-level caching can meaningfully improve load times under traffic.', 'check-system-details' ) );
}

// Scheduled cron events (real data, not just on/off) + overdue detection
$csd_cron_array  = _get_cron_array();
$csd_cron_events = [];

if ( is_array( $csd_cron_array ) ) {
    foreach ( $csd_cron_array as $csd_timestamp => $csd_hooks ) {
        foreach ( $csd_hooks as $csd_hook => $csd_events ) {
            foreach ( $csd_events as $csd_event ) {
                $csd_cron_events[] = [
                    'hook'       => $csd_hook,
                    'next_run'   => $csd_timestamp,
                    'recurrence' => isset( $csd_event['schedule'] ) ? $csd_event['schedule'] : __( 'single event', 'check-system-details' ),
                ];
            }
        }
    }
    usort(
        $csd_cron_events,
        function ( $a, $b ) {
            return $a['next_run'] <=> $b['next_run'];
        }
    );
}

$csd_overdue_cron_count = 0;
foreach ( $csd_cron_events as $csd_event ) {
    if ( $csd_event['next_run'] < ( time() - 300 ) ) {
        $csd_overdue_cron_count++;
    }
}
if ( $csd_overdue_cron_count > 0 ) {
    /* translators: %d: number of overdue scheduled cron events */
    $csd_health_checks[] = csd_health_status( 'warning', __( 'Cron Health', 'check-system-details' ), sprintf( __( '%d scheduled event(s) are overdue by more than 5 minutes. This can happen on low-traffic sites (WP-Cron runs on page load) or when DISABLE_WP_CRON is set without a real system cron in place.', 'check-system-details' ), $csd_overdue_cron_count ) );
} elseif ( ! empty( $csd_cron_events ) ) {
    $csd_health_checks[] = csd_health_status( 'good', __( 'Cron Health', 'check-system-details' ), __( 'No overdue scheduled events detected.', 'check-system-details' ) );
}

// Multisite network sites (capped for very large networks)
$csd_multisite_sites   = [];
$csd_multisite_capped  = false;
if ( is_multisite() ) {
    $csd_sites = get_sites( [ 'number' => 100 ] );
    foreach ( $csd_sites as $csd_site ) {
        $csd_multisite_sites[] = [
            'blog_id' => $csd_site->blog_id,
            'domain'  => $csd_site->domain,
            'path'    => $csd_site->path,
        ];
    }
    $csd_multisite_capped = count( $csd_multisite_sites ) >= 100;
}

$csd_health_summary = [
    'good'     => 0,
    'warning'  => 0,
    'critical' => 0,
];
foreach ( $csd_health_checks as $csd_check ) {
    if ( isset( $csd_health_summary[ $csd_check['level'] ] ) ) {
        $csd_health_summary[ $csd_check['level'] ]++;
    }
}

$csd_level_icons = [
    'good'     => 'yes-alt',
    'warning'  => 'warning',
    'critical' => 'dismiss',
    'info'     => 'info',
];

$csd_level_labels = [
    'good'     => __( 'Good', 'check-system-details' ),
    'warning'  => __( 'Warning', 'check-system-details' ),
    'critical' => __( 'Critical', 'check-system-details' ),
    'info'     => __( 'Info', 'check-system-details' ),
];
?>

<!-- Health Check -->
<div class="csd-section">
    <h2 class="csd-section-head csd-section-head-flex">
        <span><?php esc_html_e( 'Health Check', 'check-system-details' ); ?></span>
        <span class="csd-summary">
            <?php
                /* translators: %d: count of health checks at this level */
                printf( '<span class="csd-pill csd-pill-good">' . esc_html__( '%d Good', 'check-system-details' ) . '</span>', (int) $csd_health_summary['good'] );
                printf( '<span class="csd-pill csd-pill-warning">' . esc_html__( '%d Warning', 'check-system-details' ) . '</span>', (int) $csd_health_summary['warning'] );
                printf( '<span class="csd-pill csd-pill-critical">' . esc_html__( '%d Critical', 'check-system-details' ) . '</span>', (int) $csd_health_summary['critical'] );
            ?>
        </span>
    </h2>
    <div class="csd-section-body">
        <table>
            <?php foreach ( $csd_health_checks as $csd_check ) : ?>
                <tr>
                    <td>
                        <span class="dashicons dashicons-<?php echo esc_attr( $csd_level_icons[ $csd_check['level'] ] ); ?> csd-health-icon csd-health-icon-<?php echo esc_attr( $csd_check['level'] ); ?>" aria-hidden="true"></span>
                        <span class="screen-reader-text"><?php echo esc_html( $csd_level_labels[ $csd_check['level'] ] ); ?>: </span>
                        <?php echo esc_html( $csd_check['label'] ); ?>
                    </td>
                    <td><?php echo esc_html( $csd_check['message'] ); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php if ( is_multisite() ) : ?>
<!-- Multisite Network -->
<div class="csd-section">
    <h2 class="csd-section-head">
        <?php
            if ( $csd_multisite_capped ) {
                /* translators: %d: number of sites shown (capped) */
                printf( esc_html__( 'Multisite Network (%d+ sites, showing first 100)', 'check-system-details' ), (int) count( $csd_multisite_sites ) );
            } else {
                /* translators: %d: number of sites in the network */
                printf( esc_html__( 'Multisite Network (%d sites)', 'check-system-details' ), (int) count( $csd_multisite_sites ) );
            }
        ?>
    </h2>
    <div class="csd-section-body">
        <table>
            <tr>
                <td><strong><?php esc_html_e( 'Site ID', 'check-system-details' ); ?></strong></td>
                <td><strong><?php esc_html_e( 'Domain', 'check-system-details' ); ?></strong></td>
                <td><strong><?php esc_html_e( 'Path', 'check-system-details' ); ?></strong></td>
            </tr>
            <?php foreach ( $csd_multisite_sites as $csd_ms_site ) : ?>
                <tr>
                    <td><?php echo esc_html( $csd_ms_site['blog_id'] ); ?></td>
                    <td><?php echo esc_html( $csd_ms_site['domain'] ); ?></td>
                    <td><?php echo esc_html( $csd_ms_site['path'] ); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php endif; ?>
