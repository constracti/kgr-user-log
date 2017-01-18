<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'manage_users_columns', function( array $columns ): array {
	$columns['kgr-last-active'] = esc_html__( 'Active', 'kgr-last-active' );
	return $columns;
} );

add_action( 'manage_users_custom_column', function( string $output, string $column_name, int $user_id ): string {
	if ( $column_name !== 'kgr-last-active' )
		return $output;
	$meta = get_user_meta( $user_id, 'kgr-last-active', TRUE );
	if ( $meta === '' )
		return esc_html__( 'never', 'kgr-last-active' );
	return sprintf( '%s %s', esc_html( human_time_diff( intval( $meta ) ) ), esc_html__( 'ago', 'kgr-last-active' ) );
}, 10, 3 );

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	if ( $hook !== 'users.php' )
		return;
	wp_enqueue_style( 'kgr-last-active-column', plugins_url( 'column.css', __FILE__ ) );
} );

add_filter( 'manage_users_sortable_columns', function( array $columns ): array {
	$columns['kgr-last-active'] = 'kgr-last-active';
	return $columns;
} );

add_action( 'pre_get_users', function( $query ) {
	if ( ! current_user_can( 'list_users' ) )
		return;
	$orderby = $query->get( 'orderby' );
	if ( $orderby !== 'kgr-last-active' )
		return;
	$query->set( 'meta_key', 'kgr-last-active' );
	$query->set( 'orderby', 'meta_value_num' );
} );
