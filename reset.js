jQuery( document ).ready( function( $ ) {

$( 'tr[data-slug="kgr-last-active"] span.reset>a' ).click( function() {
	if ( $( this ).data( 'busy' ) === 'on' )
		return false;
	if ( ! confirm( $( this ).data( 'confirm' ) ) )
		return false;
	$( this ).data( 'busy', 'on' );
	$.get( $( this ).prop( 'href' ), function( data ) {
		if ( data !== '' )
			alert( data );
		$( this ).data( 'busy', 'off' );
	} );
	return false;
} );

} );
