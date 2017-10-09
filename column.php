<?php

if ( !defined( 'ABSPATH' ) )
	exit;

add_filter( 'manage_users_columns', function( array $columns ): array {
	$columns[ 'kgr-userlog-reg' ] = esc_html__( 'Registration', 'kgr-userlog' );
	$columns[ 'kgr-userlog-act' ] = esc_html__( 'Action', 'kgr-userlog' );
	return $columns;
} );

add_action( 'manage_users_custom_column', function( string $output, string $column_name, int $user_id ): string {
	switch ( $column_name ) {
		case 'kgr-userlog-reg':
			$meta = absint( get_user_meta( $user_id, 'kgr-userlog-reg', TRUE ) );
			if ( $meta === 0 )
				return '';
			$meta += get_option( 'gmt_offset' ) * HOUR_IN_SECONDS;
			return sprintf( '%s<br />%s',
				date_i18n( get_option( 'date_format' ), $meta ),
				date_i18n( get_option( 'time_format' ), $meta )
			);
		case 'kgr-userlog-act':
			$meta = absint( get_user_meta( $user_id, 'kgr-userlog-act', TRUE ) );
			if ( $meta === 0 )
				return esc_html__( 'never', 'kgr-userlog' );
			return sprintf( '%s %s',
				esc_html( human_time_diff( $meta ), $_SERVER['REQUEST_TIME'] ),
				esc_html__( 'ago', 'kgr-userlog' )
			);
		default:
			return $output;
	}
}, 10, 3 );

add_action( 'admin_enqueue_scripts', function( string $hook ) {
	if ( $hook !== 'users.php' )
		return;
	wp_enqueue_style( 'kgr-userlog-column', KGR_USERLOG_URL . 'column.css', [], NULL );
} );

add_filter( 'manage_users_sortable_columns', function( array $columns ): array {
	$columns[ 'kgr-userlog-reg' ] = 'kgr-userlog-reg';
	$columns[ 'kgr-userlog-act' ] = 'kgr-userlog-act';
	return $columns;
} );

# TODO meta_value not exists
add_action( 'pre_get_users', function( $query ) {
	if ( ! current_user_can( 'list_users' ) )
		return;
	$orderby = $query->get( 'orderby' );
	switch ( $orderby ) {
		case 'kgr-userlog-reg':
			$query->set( 'meta_key', 'kgr-userlog-reg' );
			$query->set( 'orderby', 'meta_value_num' );
			break;
		case 'kgr-userlog-act':
			$query->set( 'meta_key', 'kgr-userlog-act' );
			$query->set( 'orderby', 'meta_value_num' );
			break;
	}
} );
