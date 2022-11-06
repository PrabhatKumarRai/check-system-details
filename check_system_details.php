<?php
/* 
 * Plugin Name:       Check System Details
 * Plugin URI:        https://github.com/PrabhatKumarRai/check-system-details
 * Description:       Easily check your WordPress installation and server details along with database tables, installed plugins, and the active theme.
 * Version:           1.1.0
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
        'check_system_details_view',
        'dashicons-admin-settings'
    );
}

function check_system_details_view(){
    require_once plugin_dir_path(__FILE__) . 'admin/check_system_details_view.php';
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

    .check-system-details-section{
        background-color: #fff;
        border: 1px solid black;
        margin-bottom: 30px;
    }
    .check-system-details-section-head{
        padding: 10px;
        margin: 0;
        border-bottom: 1px solid black;
    }
    .check-system-details-section-body{
        padding: 5px;
    }
    
    .check-system-details-section-body * {
        font-size: 16px;
        font-family: inherit;  
    }
    .check-system-details-section table{
        width: 100%;
        word-break: break-all;
    }
    .check-system-details-section table td{
        padding: 5px;
        width: 50%;
    }
    .check-system-details-plugin-details table td{
        width: unset;
    }
    .check-system-details-section pre,
    .check-system-details-section p{
        margin: 0;
        padding: 6px 5px;      
    }
    </style>";
}
add_action( 'admin_head', 'check_system_details_css' );