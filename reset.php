<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'plugin_action_links_kgr-last-active/kgr-last-active.php', function( array $links ): array {
	$action = KGR_LAST_ACTIVE_KEY . '-reset';
	$links['reset'] = sprintf( '<a href="%s?action=%s&nonce=%s" data-confirm="%s">%s</a>',
		admin_url( '/admin-ajax.php' ),
		$action,
		wp_create_nonce( $action ),
		esc_attr__( 'Reset users metadata?', 'kgr-last-active' ),
		esc_html__( 'Reset', 'kgr-last-active' )
	);
	return $links;
} );

# TODO user meta will be set on deactivation
add_action( 'wp_ajax_kgr-last-active-reset', function() {
	if ( !current_user_can( 'administrator' ) )
		exit( 'role' );
	if ( !wp_verify_nonce( $_GET['nonce'], $_GET['action'] ) )
		exit( 'nonce' );
	$users = get_users( [
		'meta_key' => KGR_LAST_ACTIVE_KEY,
	] );
	foreach( $users as $user )
		delete_user_meta( $user->ID, KGR_LAST_ACTIVE_KEY );
	exit( __( 'Users metadata reset complete.', 'kgr-last-active' ) );
} );

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	if ( $hook !== 'plugins.php' )
		return;
	wp_enqueue_script( 'kgr-last-active-reset', plugins_url( 'reset.js', __FILE__ ), [ 'jquery' ], NULL );
} );
