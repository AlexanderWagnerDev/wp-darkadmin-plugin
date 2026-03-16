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
		loadPresetColors( admData.defaults );
		$( '#adm_preset' ).val( 'default' );
		updatePresetTiles( 'default' );
	} );

	// Load a preset: set all pickers + live preview + update hidden input.
	function loadPresetColors( colors ) {
		$( '.adm-color-picker' ).each( function () {
			const $input = $( this );
			const key    = $input.data( 'key' );
			const color  = colors[ key ];
			if ( key && color ) {
				$input.wpColorPicker( 'color', color );
				const cssVar = admData.varMap[ key ];
				if ( cssVar ) {
					root.style.setProperty( cssVar, color );
				}
			}
		} );
	}

	function updatePresetTiles( activeSlug ) {
		$( '.adm-preset-tile' ).each( function () {
			const slug = $( this ).data( 'preset' );
			$( this ).toggleClass( 'adm-preset-active', slug === activeSlug );
			$( this ).find( '.adm-preset-load-btn' ).text(
				slug === activeSlug ? '\u2713 Active' : 'Load Preset'
			);
		} );
	}

	// Handle preset load button clicks.
	$( document ).on( 'click', '.adm-preset-load-btn', function () {
		const slug   = $( this ).data( 'preset' );
		const colors = admData.presets[ slug ];
		if ( ! colors ) {
			return;
		}
		loadPresetColors( colors );
		$( '#adm_preset' ).val( slug );
		updatePresetTiles( slug );
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
				$status.text( '\u2713 ' + applied + ' colors imported' ).addClass( 'adm-import-ok' ).removeClass( 'adm-import-err' );
			} catch ( err ) {
				$status.text( '\u2717 Invalid palette file' ).addClass( 'adm-import-err' ).removeClass( 'adm-import-ok' );
			}
		};
		reader.readAsText( file );
		// Reset so the same file can be re-imported.
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
			$code.text( '\u2713 Copied!' );
			setTimeout( function () { $code.text( orig ); }, 1500 );
		} );
	} );

} )( jQuery );
