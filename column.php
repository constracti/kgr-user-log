<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'manage_users_columns', function( array $columns ): array {
	$columns['kgr-userlog-reg'] = esc_html__( 'Registration', 'kgr-userlog' );
	$columns['kgr-userlog-act'] = esc_html__( 'Action', 'kgr-userlog' );
	return $columns;
} );

add_action( 'manage_users_custom_column', function( string $output, string $column_name, int $user_id ): string {
	switch ( $column_name ) {
		case 'kgr-userlog-reg':
			$user = get_user_by( 'ID', $user_id );
			$dt = DateTime::createFromFormat( 'Y-m-d H:i:s', $user->user_registered );
			$dt = $dt->getTimestamp();
			return $output . sprintf( '%s %s',
				wp_date( get_option( 'date_format' ), $dt ),
				wp_date( get_option( 'time_format' ), $dt ),
			) . "\n";
		case 'kgr-userlog-act':
			$meta = intval( get_user_meta( $user_id, 'kgr-userlog-act', TRUE ) );
			if ( $meta === 0 )
				return $output . esc_html__( 'never', 'kgr-userlog' ) . "\n";
			return $output .= sprintf( '%s %s',
				esc_html( human_time_diff( $meta ), $_SERVER['REQUEST_TIME'] ),
				esc_html__( 'ago', 'kgr-userlog' )
			) . "\n";
		default:
			return $output;
	}
}, 10, 3 );

add_action( 'admin_enqueue_scripts', function( string $hook_suffix ): void {
	if ( $hook_suffix !== 'users.php' )
		return;
	wp_enqueue_style( 'kgr-userlog-column', KGR_USERLOG_URL . 'column.css', [], kgr_userlog_version() );
} );

add_filter( 'manage_users_sortable_columns', function( array $columns ): array {
	$columns['kgr-userlog-reg'] = [ 'kgr-userlog-reg', TRUE ];
	$columns['kgr-userlog-act'] = [ 'kgr-userlog-act', TRUE ];
	return $columns;
} );

add_action( 'pre_get_users', function( WP_User_Query $query ): void {
	if ( !current_user_can( 'list_users' ) )
		return;
	$orderby = $query->get( 'orderby' );
	switch ( $orderby ) {
		case 'kgr-userlog-reg':
			$query->set( 'orderby', 'user_registered' );
			break;
		case 'kgr-userlog-act':
			$query->set( 'meta_key', 'kgr-userlog-act' );
			$query->set( 'orderby', 'meta_value_num' );
			break;
	}
} );
