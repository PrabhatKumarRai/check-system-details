<?php
/*
 * Plugin Name:       Check System Details
 * Plugin URI:        https://github.com/PrabhatKumarRai/check-system-details
 * Description:       Easily check your WordPress installation and server details along with database tables, installed plugins, and the active theme.
 * Version:           1.2.0
 * Author:            Prabhat Rai
 * Author URI:        https://github.com/PrabhatKumarRai/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 4.9
 * Requires PHP:      5.6
*/

define( 'CSD_PLUGIN_VERSION', '1.2.0' );
define( 'CSD_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Adds the plugin to the WordPress admin menu/sidebar
add_action('admin_menu', 'check_system_details_menu');
function check_system_details_menu(){
    $csd_hook = add_menu_page(
        'Check System Details',
        'System Details',
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
    } );
}