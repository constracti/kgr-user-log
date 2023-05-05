<?php

/*
 * Plugin Name: KGR User Log
 * Plugin URI: https://github.com/constracti/kgr-user-log
 * Description: Displays the registration time and the last active time in two custom columns in the users table.
 * Version: 1.6.2
 * Requires at least: 5.3.0
 * Requires PHP: 7.0
 * Author: constracti
 * Author URI: https://github.com/constracti
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: kgr-user-log
 * Domain Path: /languages
 */

if ( !defined( 'ABSPATH' ) )
	exit;

// define plugin constants
define( 'KGR_USER_LOG_DIR', plugin_dir_path( __FILE__ ) );
define( 'KGR_USER_LOG_URL', plugin_dir_url( __FILE__ ) );

// require php files
$files = glob( KGR_USER_LOG_DIR . '*.php' );
foreach ( $files as $file ) {
        if ( $file !== __FILE__ )
                require_once( $file );
}

// return plugin version
function kgr_user_log_version(): string {
        $plugin_data = get_plugin_data( __FILE__ );
        return $plugin_data['Version'];
}

// load plugin translations
add_action( 'init', function(): void {
        load_plugin_textdomain( 'kgr-user-log', FALSE, basename( __DIR__ ) . '/languages' );
} );

// create meta upon plugin activation
register_activation_hook( __FILE__, function(): void {
	$users = get_users( [
		'meta_key'     => 'kgr-user-log-act',
		'meta_compare' => 'NOT EXISTS',
		'fields'       => 'id',
	] );
	foreach ( $users as $user_id )
		update_user_meta( $user_id, 'kgr-user-log-act', 0 );
} );

// update meta upon user action
add_action( 'init', function(): void {
	$user_id = get_current_user_id();
	if ( $user_id === 0 )
		return;
	update_user_meta( $user_id, 'kgr-user-log-act', $_SERVER['REQUEST_TIME'] );
} );

// create meta upon user registration
add_action( 'user_register', function( int $user_id ): void {
	update_user_meta( $user_id, 'kgr-user-log-act', 0 );
} );
