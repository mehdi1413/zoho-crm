<?php
/*
Plugin Name: Zoho CRM Integration
Description: a simple project use zoho crm data to show and change data.
Version: 1.0.2
Author: mehdi fani
*/

defined( 'ABSPATH' ) || exit;

define( 'ZOHO_INC_PATH', plugin_dir_path( __FILE__ ) . 'inc/' );
define( 'ZOHO_ADMIN_PATH', plugin_dir_path( __FILE__ ) . 'inc/admin/' );
define( 'ZOHO_VIEW_PATH', plugin_dir_path( __FILE__ ) . 'inc/admin/views/' );
define( 'ZOHO_TABLE_PATH', plugin_dir_path( __FILE__ ) . 'inc/admin/tables/' );


if ( is_admin() ) {
	include_once ZOHO_ADMIN_PATH . 'page/admin-menu.php';
	include_once ZOHO_INC_PATH . 'token.php';
}