<?php
/*
 * Plugin Name:       Check System Details
 * Plugin URI:        https://github.com/PrabhatKumarRai/check-system-details
 * Description:       Puts your WordPress, server, and database details in one place, flags real problems instead of just listing facts, and gives you tools to test, snapshot, and report on your site.
 * Version:           1.2.0
 * Author:            Prabhat Rai
 * Author URI:        https://github.com/PrabhatKumarRai/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 4.9
 * Requires PHP:      7.0
 * Text Domain:       check-system-details
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'CSD_PLUGIN_VERSION', '1.2.0' );
define( 'CSD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'CSD_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once CSD_PLUGIN_DIR . 'includes/helpers.php';
require_once CSD_PLUGIN_DIR . 'includes/cron.php';
require_once CSD_PLUGIN_DIR . 'includes/ajax-handlers.php';

register_deactivation_hook( __FILE__, 'csd_deactivate' );

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    require_once CSD_PLUGIN_DIR . 'includes/class-csd-cli-command.php';
}

// Adds a "Settings" quick link on the Plugins list page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'csd_plugin_action_links' );
function csd_plugin_action_links( $links ) {
    $csd_settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=check_system_details' ) ) . '">' . esc_html__( 'Settings', 'check-system-details' ) . '</a>';
    array_unshift( $links, $csd_settings_link );
    return $links;
}

// Adds the plugin to the WordPress admin menu/sidebar
add_action('admin_menu', 'check_system_details_menu');
function check_system_details_menu(){
    $csd_hook = add_menu_page(
        __( 'Check System Details', 'check-system-details' ),
        __( 'System Details', 'check-system-details' ),
        'manage_options',
        'check_system_details',
        'check_system_details_view',
        'dashicons-admin-settings'
    );

    add_action( 'load-' . $csd_hook, 'check_system_details_assets' );
}

function check_system_details_view(){
    require_once plugin_dir_path(__FILE__) . 'admin/check_system_details_view.php';
}

// Load the plugin's CSS/JS only on its own admin page
function check_system_details_assets(){
    add_action( 'admin_enqueue_scripts', function () {
        wp_enqueue_style(
            'check-system-details-admin',
            CSD_PLUGIN_URL . 'admin/css/check-system-details-admin.css',
            [],
            CSD_PLUGIN_VERSION
        );

        wp_enqueue_script(
            'check-system-details-admin',
            CSD_PLUGIN_URL . 'admin/js/check-system-details-admin.js',
            [],
            CSD_PLUGIN_VERSION,
            true
        );

        wp_localize_script(
            'check-system-details-admin',
            'csdAdmin',
            [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce'   => wp_create_nonce( 'csd_tools_nonce' ),
            ]
        );
    } );
}