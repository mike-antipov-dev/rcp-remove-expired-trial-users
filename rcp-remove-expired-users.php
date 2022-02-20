<?php
/**
 * Plugin Name:     Restrict Content Pro - Remove expired trial users
 * Description:     Removes expired RCP users except ones that are affiliates
 * Version:         1.0
 * Author:          Mike Antipov
 * Author URI:      https://cv.antipov-design.ru/
 */

// Exit if accessed directly
if(!defined('ABSPATH')) {
	exit;
}

/**
 * Create or customer ID table upon install
 */
function activate_rcp_remove_expired_users() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-remove-expired-users-activation.php';
	RCP_Remove_Expired_Users_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_rcp_remove_expired_users' );

/**
 * Load and instantiate main classes
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-remove-expired-users.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-find-affiliates.php';

$affiliates = new Find_Affiliates;
$affiliates->wa_api_args = 0;
$expired_users = new Remove_Expired_Users;