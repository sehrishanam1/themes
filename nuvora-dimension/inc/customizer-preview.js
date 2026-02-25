/**
 * Nuvora Theme Customizer Live Preview
 */
( function ( $ ) {
	wp.customize( 'nuvora_site_title', function ( value ) {
		value.bind( function ( newval ) {
			$( '#header .inner h1' ).text( newval );
		} );
	} );

	wp.customize( 'nuvora_bg_color', function ( value ) {
		value.bind( function ( newval ) {
			$( '#bg' ).css( 'background-color', newval );
			$( '#nuvora-dynamic-css' ).text(
				'#bg { background-color: ' + newval + '; }'
			);
		} );
	} );
} )( jQuery );
