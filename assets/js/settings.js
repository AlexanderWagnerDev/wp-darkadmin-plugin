/* DarkAdmin - Settings Page JS */
( function () {
	'use strict';

	/* -----------------------------------------------------------------------
	 * Color picker live preview
	 * --------------------------------------------------------------------- */
	function initColorPickers() {
		const pickers = document.querySelectorAll( '.adm-color-picker' );
		pickers.forEach( function ( input ) {
			jQuery( input ).wpColorPicker( {
				change: function ( _e, ui ) {
					const key = input.dataset.key;
					if ( key ) {
						document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), ui.color.toString() );
					}
				},
				clear: function () {
					const key = input.dataset.key;
					const def = input.dataset.defaultColor;
					if ( key && def ) {
						document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), def );
					}
				},
			} );
		} );
	}

	/* -----------------------------------------------------------------------
	 * Layout inputs live preview
	 * --------------------------------------------------------------------- */
	function initLayoutInputs() {
		document.querySelectorAll( '.adm-layout-input' ).forEach( function ( input ) {
			input.addEventListener( 'input', function () {
				const key = input.dataset.key;
				if ( key ) {
					document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), input.value );
				}
			} );
		} );
	}

	/* -----------------------------------------------------------------------
	 * Preset tiles + live preview panel
	 * --------------------------------------------------------------------- */
	function initPresets() {
		const metaEl = document.getElementById( 'adm-preset-meta' );
		if ( ! metaEl ) return;
		const meta = JSON.parse( metaEl.textContent || '{}' );

		const presetInput    = document.getElementById( 'darkadmin_preset' );
		const previewPanel   = document.getElementById( 'adm-preset-preview' );
		const previewName    = document.getElementById( 'adm-preview-name' );
		const tiles          = document.querySelectorAll( '.adm-preset-tile' );
		const loadBtns       = document.querySelectorAll( '.adm-preset-load-btn' );
		const admPresets     = ( window.admData && window.admData.presets )     || {};
		const admLayoutPresets = ( window.admData && window.admData.layoutPresets ) || {};

		function updatePreview( slug ) {
			if ( ! previewPanel || ! meta[ slug ] ) return;
			const m = meta[ slug ];
			previewPanel.style.setProperty( '--adm-preview-bg',      m.bg );
			previewPanel.style.setProperty( '--adm-preview-surface', m.surface );
			previewPanel.style.setProperty( '--adm-preview-primary', m.primary );
			previewPanel.style.setProperty( '--adm-preview-text',    m.text );
			previewPanel.style.setProperty( '--adm-preview-bar',     m.bar || m.bg );
			if ( previewName ) previewName.textContent = m.label;
		}

		function setActive( slug ) {
			tiles.forEach( function ( t ) {
				const isThis = t.dataset.preset === slug;
				t.classList.toggle( 'adm-preset-active', isThis );
			} );
			loadBtns.forEach( function ( btn ) {
				const isThis = btn.dataset.preset === slug;
				btn.textContent = isThis
					? ( window.admI18n ? window.admI18n.active    : '\u2713 Active' )
					: ( window.admI18n ? window.admI18n.loadPreset : 'Load Preset' );
			} );
			if ( presetInput ) presetInput.value = slug;
		}

		tiles.forEach( function ( tile ) {
			tile.addEventListener( 'mouseenter', function () {
				updatePreview( tile.dataset.preset );
			} );
			tile.addEventListener( 'mouseleave', function () {
				updatePreview( presetInput ? presetInput.value : 'default' );
			} );
		} );

		loadBtns.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				const slug   = btn.dataset.preset;
				const colors = admPresets[ slug ];
				if ( colors ) {
					loadPresetColors( colors );
				}
				const layout = admLayoutPresets[ slug ];
				if ( layout ) {
					loadPresetLayout( layout );
				}
				setActive( slug );
				updatePreview( slug );
			} );
		} );
	}

	/* -----------------------------------------------------------------------
	 * Load preset colors into color pickers
	 * --------------------------------------------------------------------- */
	function loadPresetColors( colors ) {
		Object.keys( colors ).forEach( function ( key ) {
			const input = document.getElementById( 'adm_color_' + key );
			if ( ! input ) return;
			const val = colors[ key ];
			jQuery( input ).wpColorPicker( 'color', val );
			document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), val );
		} );
	}

	/* -----------------------------------------------------------------------
	 * Load preset layout values into layout inputs
	 * --------------------------------------------------------------------- */
	function loadPresetLayout( layout ) {
		Object.keys( layout ).forEach( function ( key ) {
			const input = document.getElementById( 'adm_layout_' + key );
			if ( ! input ) return;
			const val = layout[ key ];
			input.value = val;
			document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), val );
		} );
	}

	function updatePresetTiles( activeSlug ) {
		document.querySelectorAll( '.adm-preset-tile' ).forEach( function ( tile ) {
			tile.classList.toggle( 'adm-preset-active', tile.dataset.preset === activeSlug );
		} );
		document.querySelectorAll( '.adm-preset-load-btn' ).forEach( function ( btn ) {
			const isActive = btn.dataset.preset === activeSlug;
			btn.textContent = isActive
				? ( window.admI18n ? window.admI18n.active    : '\u2713 Active' )
				: ( window.admI18n ? window.admI18n.loadPreset : 'Load Preset' );
		} );
	}

	/* -----------------------------------------------------------------------
	 * Reset colors
	 * --------------------------------------------------------------------- */
	function initReset() {
		const btnColors = document.getElementById( 'adm-reset-colors' );
		if ( btnColors ) {
			const admDefaults = window.admData.defaults;
			btnColors.addEventListener( 'click', function () {
				Object.keys( admDefaults ).forEach( function ( key ) {
					const input = document.getElementById( 'adm_color_' + key );
					if ( ! input ) return;
					jQuery( input ).wpColorPicker( 'color', admDefaults[ key ] );
					document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), admDefaults[ key ] );
				} );
			} );
		}

		const btnLayout = document.getElementById( 'adm-reset-layout' );
		if ( btnLayout ) {
			const layoutDefaults = ( window.admData && window.admData.layoutDefaults ) || {};
			btnLayout.addEventListener( 'click', function () {
				Object.keys( layoutDefaults ).forEach( function ( key ) {
					const input = document.getElementById( 'adm_layout_' + key );
					if ( ! input ) return;
					input.value = layoutDefaults[ key ];
					document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), layoutDefaults[ key ] );
				} );
			} );
		}
	}

	/* -----------------------------------------------------------------------
	 * Export / Import palette
	 * --------------------------------------------------------------------- */
	function initPaletteIO() {
		const exportBtn  = document.getElementById( 'adm-export-colors' );
		const importFile = document.getElementById( 'adm-import-file' );
		const statusEl   = document.getElementById( 'adm-import-status' );

		if ( exportBtn ) {
			exportBtn.addEventListener( 'click', function () {
				const pickers = document.querySelectorAll( '.adm-color-picker' );
				const palette = {};
				pickers.forEach( function ( p ) { palette[ p.dataset.key ] = p.value; } );
				const blob = new Blob( [ JSON.stringify( palette, null, 2 ) ], { type: 'application/json' } );
				const url  = URL.createObjectURL( blob );
				const a    = document.createElement( 'a' );
				a.href     = url;
				a.download = 'darkadmin-palette.json';
				a.click();
				URL.revokeObjectURL( url );
			} );
		}

		if ( importFile ) {
			importFile.addEventListener( 'change', function () {
				const file = importFile.files[ 0 ];
				if ( ! file ) return;
				const reader = new FileReader();
				reader.onload = function ( e ) {
					try {
						const data = JSON.parse( e.target.result );
						for ( const key in data ) {
							if ( ! /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test( data[ key ] ) ) {
								throw new Error( 'Invalid hex color value for key: ' + key );
							}
						}
						Object.keys( data ).forEach( function ( key ) {
							const input = document.getElementById( 'adm_color_' + key );
							if ( ! input ) return;
							jQuery( input ).wpColorPicker( 'color', data[ key ] );
							document.documentElement.style.setProperty( '--adm-' + key.replace( /_/g, '-' ), data[ key ] );
						} );
						if ( statusEl ) {
							statusEl.textContent = '\u2713 Imported';
							statusEl.className = 'adm-import-status adm-import-ok';
						}
					} catch ( err ) {
						if ( statusEl ) {
							statusEl.textContent = '\u2715 Invalid JSON or Hex color';
							statusEl.className = 'adm-import-status adm-import-err';
						}
					}
					importFile.value = '';
				};
				reader.readAsText( file );
			} );
		}
	}

	/* -----------------------------------------------------------------------
	 * Copy CSS var to clipboard
	 * --------------------------------------------------------------------- */
	function initVarCopy() {
		document.querySelectorAll( '.adm-var-copy' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				navigator.clipboard.writeText( 'var(' + btn.dataset.var + ')' ).then( function () {
					const orig = btn.innerHTML;
					btn.innerHTML = '<code>\u2713 Copied!</code>';
					setTimeout( function () { btn.innerHTML = orig; }, 1400 );
				} ).catch( function () {} );
			} );
		} );
	}

	/* -----------------------------------------------------------------------
	 * User Access Mode -- show/hide user grid + highlight active radio card
	 * --------------------------------------------------------------------- */
	function initUserAccessMode() {
		const radios   = document.querySelectorAll( 'input[name="darkadmin_user_access_mode"]' );
		const userGrid = document.getElementById( 'adm-user-grid' );
		if ( ! radios.length || ! userGrid ) return;

		function update( val ) {
			userGrid.style.display = val === 'all' ? 'none' : '';
			document.querySelectorAll( '.adm-access-mode-option' ).forEach( function ( label ) {
				const radio = label.querySelector( 'input[type="radio"]' );
				label.classList.toggle( 'is-active', radio && radio.value === val );
			} );
		}

		radios.forEach( function ( radio ) {
			radio.addEventListener( 'change', function () {
				update( radio.value );
			} );
		} );
	}

	/* -----------------------------------------------------------------------
	 * Boot
	 * --------------------------------------------------------------------- */
	document.addEventListener( 'DOMContentLoaded', function () {
		initColorPickers();
		initLayoutInputs();
		initPresets();
		initReset();
		initPaletteIO();
		initVarCopy();
		initUserAccessMode();
	} );
} )();
