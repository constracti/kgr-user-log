jQuery( function() {

var busy = false;

jQuery( '.kgr-last-active-reset' ).click( function() {
	if ( busy )
		return false;
	busy = true;
	var link = jQuery( this );
	if ( ! confirm( link.data( 'kgr-last-active-reset-confirm' ) ) )
		return false;
	jQuery.get( link.prop( 'href' ), function( data ) {
		if ( data !== '' )
			alert( data );
		busy = false;
	} );
	return false;
} );

} );
