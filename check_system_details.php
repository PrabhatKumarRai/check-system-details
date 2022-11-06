<?php
/* 
 * Plugin Name:       Check System Details - V1
 * Plugin URI:        https://github.com/PrabhatKumarRai/check-system-details
 * Description:       Easily check your WordPress installation and server details along with database tables, installed plugins, and the active theme.
 * Version:           1.0.0
 * Author:            Prabhat Rai
 * Author URI:        https://github.com/PrabhatKumarRai/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Requires at least: 4.9
 * Requires PHP:      5.6
*/

// Adds the plugin to the WordPress admin menu/sidebar
add_action('admin_menu', 'check_system_details_menu');
function check_system_details_menu(){
    add_menu_page(
        'Check System Details',
        'System Details',
        'administrator',
        'check_system_details',
        'check_system_details_data',
        'dashicons-admin-settings'
    );
}

function check_system_details_data(){
    include_once plugin_dir_path(__FILE__) . 'admin/check_system_details_data.php';
}

// CSS for the plugin page
function check_system_details_css(){
    echo "<style type='text/css'>
    .check-system-details-container{
    padding: 25px 25px 0 25px;
    margin-left: -25px;
    margin-top: -16px;
    }
    .check-system-details-head{
        position: relative;
    }
    .check-system-details-head h1{
        position: absolute;
        width: 100%;
    }
    .check-system-details-body{
        margin-top: 55px;
    }
    .check-system-details-title,
    .check-system-details-footer{
        text-decoration: underline;
        text-align: center;
    }
    .check-system-details-container h2{
        padding: 10px;
        margin-bottom: 0;
        border: 1px solid black;
        border-bottom: 0;
    }
    .check-system-details-section{
        background-color: #fff;
    }
    .check-system-details-container table{
        border: 1px solid black;
        width: 100%;
        margin-bottom: 30px;
        padding: 5px;
        word-break: break-all;
    }
    .check-system-details-container table td{
        padding: 5px;
        font-size: 16px;
        width: 50%;
    }
    .check-system-details-plugin-details table td{
        width: unset;
    }
    </style>";
}
add_action( 'admin_head', 'check_system_details_css' );