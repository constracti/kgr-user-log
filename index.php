<?php

/*
 * Plugin Name: KGR User Log
 * Plugin URI: https://github.com/constracti/wp-userlog
 * Description: Adds two custom columns to the users table. The first column contains the registration time of each user. The second column displays the time interval for which each user has been inactive.
 * Author: constracti
 * Version: 1.5
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kgr-userlog
 * Domain Path: /languages
 */

if ( !defined( 'ABSPATH' ) )
	exit;

define( 'KGR_USERLOG_DIR', plugin_dir_path( __FILE__ ) );
define( 'KGR_USERLOG_URL', plugin_dir_url( __FILE__ ) );
define( 'KGR_USERLOG_KEY', 'kgr-userlog' );

require_once KGR_USERLOG_DIR . 'column.php';

add_action( 'plugins_loaded', function() {
	load_plugin_textdomain( 'kgr-userlog', FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
} );

register_activation_hook( __FILE__, function() {
	$users = get_users( [
		'meta_key' => 'kgr-userlog-act',
		'meta_compare' => 'NOT EXISTS',
		'fields' => 'id',
	] );
	foreach ( $users as $user_id )
		update_user_meta( $user_id, 'kgr-userlog-act', 0 );
} );

add_action( 'init', function() {
	$user_id = get_current_user_id();
	if ( $user_id === 0 )
		return;
	update_user_meta( $user_id, 'kgr-userlog-act', $_SERVER['REQUEST_TIME'] );
} );

add_action( 'user_register', function( int $user_id ) {
	update_user_meta( $user_id, 'kgr-userlog-act', 0 );
} );
