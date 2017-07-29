<?php

/*
 * Plugin Name: KGR Last Active
 * Plugin URI: https://github.com/constracti/wp-last-active
 * Description: Adds a custom column to the users table with the time interval for which each user has been inactive.
 * Author: constracti
 * Version: 1.3.1
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kgr-last-active
 * Domain Path: /languages
 */

if ( !defined( 'ABSPATH' ) )
	exit;

define( 'KGR_LAST_ACTIVE_DIR', plugin_dir_path( __FILE__ ) );
define( 'KGR_LAST_ACTIVE_URL', plugin_dir_url( __FILE__ ) );
define( 'KGR_LAST_ACTIVE_KEY', 'kgr-last-active' );

require_once KGR_LAST_ACTIVE_DIR . 'column.php';
require_once KGR_LAST_ACTIVE_DIR . 'reset.php';

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'kgr-last-active', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
} );

add_action( 'init', function() {
	$user_id = get_current_user_id();
	if ( $user_id === 0 )
		return;
	update_user_meta( $user_id, KGR_LAST_ACTIVE_KEY, time() );
} );
