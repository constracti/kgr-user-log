<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'manage_users_columns', function( array $columns ): array {
	$columns[ KGR_LAST_ACTIVE_KEY ] = esc_html__( 'Active', 'kgr-last-active' );
	return $columns;
} );

add_action( 'manage_users_custom_column', function( string $output, string $column_name, int $user_id ): string {
	if ( $column_name !== KGR_LAST_ACTIVE_KEY )
		return $output;
	$meta = get_user_meta( $user_id, KGR_LAST_ACTIVE_KEY, TRUE );
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
	$columns[ KGR_LAST_ACTIVE_KEY ] = KGR_LAST_ACTIVE_KEY;
	return $columns;
} );

# TODO meta_value not exists
add_action( 'pre_get_users', function( $query ) {
	if ( ! current_user_can( 'list_users' ) )
		return;
	$orderby = $query->get( 'orderby' );
	if ( $orderby !== KGR_LAST_ACTIVE_KEY )
		return;
	$query->set( 'meta_key', KGR_LAST_ACTIVE_KEY );
	$query->set( 'orderby', 'meta_value_num' );
} );
