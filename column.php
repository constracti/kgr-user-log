<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'manage_users_columns', function( array $columns ): array {
	$columns['kgr-user-log-reg'] = esc_html__( 'Registration', 'kgr-user-log' );
	$columns['kgr-user-log-act'] = esc_html__( 'Action', 'kgr-user-log' );
	return $columns;
} );

add_action( 'manage_users_custom_column', function( string $output, string $column_name, int $user_id ): string {
	switch ( $column_name ) {
		case 'kgr-user-log-reg':
			$user = get_user_by( 'id', $user_id );
			$dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $user->user_registered, new DateTimeZone( 'UTC' ) );
			$dt = $dt->getTimestamp();
			return $output . sprintf( '%s %s',
				wp_date( get_option( 'date_format' ), $dt ),
				wp_date( get_option( 'time_format' ), $dt ),
			) . "\n";
		case 'kgr-user-log-act':
			$meta = intval( get_user_meta( $user_id, 'kgr-user-log-act', TRUE ) );
			if ( $meta === 0 )
				return $output . esc_html__( 'never', 'kgr-user-log' ) . "\n";
			return $output .= sprintf( '%s %s',
				esc_html( human_time_diff( $meta ), $_SERVER['REQUEST_TIME'] ),
				esc_html__( 'ago', 'kgr-user-log' )
			) . "\n";
		default:
			return $output;
	}
}, 10, 3 );

add_action( 'admin_enqueue_scripts', function( string $hook_suffix ): void {
	if ( $hook_suffix !== 'users.php' )
		return;
	wp_enqueue_style( 'kgr-user-log-column', KGR_USER_LOG_URL . 'column.css', [], kgr_user_log_version() );
} );

add_filter( 'manage_users_sortable_columns', function( array $columns ): array {
	$columns['kgr-user-log-reg'] = [ 'kgr-user-log-reg', TRUE ];
	$columns['kgr-user-log-act'] = [ 'kgr-user-log-act', TRUE ];
	return $columns;
} );

add_action( 'pre_get_users', function( WP_User_Query $query ): void {
	if ( !current_user_can( 'list_users' ) )
		return;
	$orderby = $query->get( 'orderby' );
	switch ( $orderby ) {
		case 'kgr-user-log-reg':
			$query->set( 'orderby', 'user_registered' );
			break;
		case 'kgr-user-log-act':
			$query->set( 'meta_key', 'kgr-user-log-act' );
			$query->set( 'orderby', 'meta_value_num' );
			break;
	}
} );
