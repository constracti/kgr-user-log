<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'plugin_action_links_kgr-last-active/kgr-last-active.php', function( array $links ): array {
	$class = ['kgr-last-active-reset'];
	$url = admin_url( '/admin-ajax.php?action=kgr-last-active-reset' );
	$confirm = esc_attr__( 'Reset users metadata?', 'kgr-last-active' );
	$text = esc_html__( 'Reset', 'kgr-last-active' );
	$links[] = sprintf( '<a class="%s" href="%s" data-kgr-last-active-reset-confirm="%s">%s</a>', implode( ' ', $class ), $url, $confirm, $text );
	return $links;
} );

# TODO user meta will be set on deactivation
add_action( 'wp_ajax_kgr-last-active-reset', function() {
	if ( ! current_user_can( 'list_users' ) )
		exit;
	$users = get_users( [
		'meta_key' => 'kgr-last-active',
	] );
	foreach( $users as $user )
		delete_user_meta( $user->ID, 'kgr-last-active' );
	exit( __( 'Users metadata reset complete.', 'kgr-last-active' ) );
} );

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	if ( $hook !== 'plugins.php' )
		return;
	wp_enqueue_script( 'kgr-last-active-reset', plugins_url( 'reset.js', __FILE__ ), ['jquery'] );
} );
