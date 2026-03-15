/* global wp */
( function ( $ ) {
	'use strict';

	// Initialize all color pickers.
	$( '.adm-color-picker' ).wpColorPicker();

	// Reset all color pickers to their default values.
	$( '#adm-reset-colors' ).on( 'click', function () {
		$( '.adm-color-picker' ).each( function () {
			const $input   = $( this );
			const defColor = $input.data( 'default-color' );
			if ( defColor ) {
				$input.wpColorPicker( 'color', defColor );
			}
		} );
	} );

	// Copy CSS variable name to clipboard on click.
	$( document ).on( 'click', '.adm-var-copy', function () {
		const varName = $( this ).data( 'var' );
		if ( ! varName ) {
			return;
		}
		navigator.clipboard.writeText( 'var(' + varName + ')' ).then( function () {
			const $btn  = $( '.adm-var-copy[data-var="' + varName + '"]' );
			const $code = $btn.find( 'code' );
			const orig  = $code.text();
			$code.text( '✓ Copied!' );
			setTimeout( function () {
				$code.text( orig );
			}, 1500 );
		} );
	} );

} )( jQuery );
