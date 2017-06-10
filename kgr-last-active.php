<?php

/*
 * Plugin Name: KGR Last Active
 * Plugin URI: https://github.com/constracti/wp-last-active
 * Description: Adds a custom column to the users table with the time interval for which each user has been inactive.
 * Author: constracti
 * Version: 1.3
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kgr-last-active
 * Domain Path: /languages
 */

if ( !defined( 'ABSPATH' ) )
	exit;

require_once plugin_dir_path( __FILE__ ) . 'column.php';
require_once plugin_dir_path( __FILE__ ) . 'reset.php';

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'kgr-last-active', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

add_action( 'init', function() {
	$user_id = get_current_user_id();
	if ( $user_id === 0 )
		return;
	update_user_meta( $user_id, 'kgr-last-active', time() );
} );
