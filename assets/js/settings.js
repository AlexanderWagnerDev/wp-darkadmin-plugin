/* global wp, admData */
( function ( $ ) {
	'use strict';

	const root = document.documentElement;

	// Initialize all color pickers with live preview on change.
	$( '.adm-color-picker' ).wpColorPicker( {
		change: function ( event, ui ) {
			const key    = $( this ).data( 'key' );
			const cssVar = admData.varMap[ key ];
			if ( cssVar ) {
				root.style.setProperty( cssVar, ui.color.toString() );
			}
		},
		clear: function () {
			const key    = $( this ).data( 'key' );
			const cssVar = admData.varMap[ key ];
			const def    = admData.defaults[ key ];
			if ( cssVar && def ) {
				root.style.setProperty( cssVar, def );
			}
		},
	} );

	// Reset all color pickers to their default values.
	$( '#adm-reset-colors' ).on( 'click', function () {
		$( '.adm-color-picker' ).each( function () {
			const $input   = $( this );
			const key      = $input.data( 'key' );
			const defColor = $input.data( 'default-color' );
			if ( defColor ) {
				$input.wpColorPicker( 'color', defColor );
				const cssVar = admData.varMap[ key ];
				if ( cssVar ) {
					root.style.setProperty( cssVar, defColor );
				}
			}
		} );
	} );

	// Export current palette as JSON file.
	$( '#adm-export-colors' ).on( 'click', function () {
		const palette = {};
		$( '.adm-color-picker' ).each( function () {
			const key = $( this ).data( 'key' );
			if ( key ) {
				palette[ key ] = $( this ).val();
			}
		} );
		const blob = new Blob(
			[ JSON.stringify( { darkadmin_palette: palette }, null, 2 ) ],
			{ type: 'application/json' }
		);
		const url  = URL.createObjectURL( blob );
		const a    = document.createElement( 'a' );
		a.href     = url;
		a.download = 'darkadmin-palette.json';
		a.click();
		URL.revokeObjectURL( url );
	} );

	// Import palette from JSON file.
	$( '#adm-import-file' ).on( 'change', function () {
		const file = this.files[ 0 ];
		if ( ! file ) {
			return;
		}
		const $status = $( '#adm-import-status' );
		const reader  = new FileReader();
		reader.onload = function ( e ) {
			try {
				const data    = JSON.parse( e.target.result );
				const palette = data.darkadmin_palette;
				if ( ! palette || typeof palette !== 'object' ) {
					throw new Error( 'Invalid format' );
				}
				let applied = 0;
				$( '.adm-color-picker' ).each( function () {
					const key   = $( this ).data( 'key' );
					const color = palette[ key ];
					if ( key && color && /^#[0-9a-fA-F]{3,8}$/.test( color ) ) {
						$( this ).wpColorPicker( 'color', color );
						const cssVar = admData.varMap[ key ];
						if ( cssVar ) {
							root.style.setProperty( cssVar, color );
						}
						applied++;
					}
				} );
				$status.text( '✓ ' + applied + ' colors imported' ).addClass( 'adm-import-ok' ).removeClass( 'adm-import-err' );
			} catch ( err ) {
				$status.text( '✗ Invalid palette file' ).addClass( 'adm-import-err' ).removeClass( 'adm-import-ok' );
			}
		};
		reader.readAsText( file );
		// Reset so the same file can be re-imported
		this.value = '';
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
			setTimeout( function () { $code.text( orig ); }, 1500 );
		} );
	} );

} )( jQuery );
