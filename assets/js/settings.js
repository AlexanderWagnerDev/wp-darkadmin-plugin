/* DarkAdmin - Settings Page JS */
( function () {
	'use strict';

	const DEFAULT_PRESET = 'modern';

	function i18n( key ) {
		return ( window.darkadminI18n && window.darkadminI18n[ key ] ) ? window.darkadminI18n[ key ] : '';
	}

	function getVarMap() {
		return ( window.darkadminData && window.darkadminData.varMap ) || {};
	}

	function cssVarForKey( key ) {
		const map = getVarMap();
		return map[ key ] || ( '--adm-' + key.replace( /_/g, '-' ) );
	}

	function setCssVar( key, value ) {
		document.documentElement.style.setProperty( cssVarForKey( key ), value );
	}

	function syncPresetHiddenFields( slug ) {
		const presetInput = document.getElementById( 'darkadmin_preset' );
		const colorsPreset = document.getElementById( 'darkadmin_colors_preset' );
		const layoutPreset = document.getElementById( 'darkadmin_layout_preset' );
		if ( presetInput ) presetInput.value = slug;
		if ( colorsPreset ) colorsPreset.value = slug;
		if ( layoutPreset ) layoutPreset.value = slug;
	}

	function activePresetSlug() {
		const presetInput = document.getElementById( 'darkadmin_preset' );
		return presetInput ? presetInput.value : DEFAULT_PRESET;
	}

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
						setCssVar( key, ui.color.toString() );
					}
				},
				clear: function () {
					const key = input.dataset.key;
					const def = input.dataset.defaultColor;
					if ( key && def ) {
						setCssVar( key, def );
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
					setCssVar( key, input.value );
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

		const presetInput            = document.getElementById( 'darkadmin_preset' );
		const previewPanel           = document.getElementById( 'adm-preset-preview' );
		const previewName            = document.getElementById( 'adm-preview-name' );
		const tiles                  = document.querySelectorAll( '.adm-preset-tile' );
		const loadBtns               = document.querySelectorAll( '.adm-preset-load-btn' );
		const darkadminPresets       = ( window.darkadminData && window.darkadminData.presets )       || {};
		const darkadminLayoutPresets = ( window.darkadminData && window.darkadminData.layoutPresets ) || {};

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
				btn.textContent = isThis ? i18n( 'active' ) : i18n( 'loadPreset' );
			} );
			syncPresetHiddenFields( slug );
		}

		tiles.forEach( function ( tile ) {
			tile.addEventListener( 'mouseenter', function () {
				updatePreview( tile.dataset.preset );
			} );
			tile.addEventListener( 'mouseleave', function () {
				updatePreview( presetInput ? presetInput.value : DEFAULT_PRESET );
			} );
		} );

		loadBtns.forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				const slug   = btn.dataset.preset;
				const colors = darkadminPresets[ slug ];
				if ( colors ) {
					loadPresetColors( colors );
				}
				const layout = darkadminLayoutPresets[ slug ];
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
			setCssVar( key, val );
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
			setCssVar( key, val );
		} );
	}

	/* -----------------------------------------------------------------------
	 * Reset colors / layout to active preset
	 * --------------------------------------------------------------------- */
	function initReset() {
		const btnColors = document.getElementById( 'adm-reset-colors' );
		if ( btnColors ) {
			const darkadminPresets = ( window.darkadminData && window.darkadminData.presets ) || {};
			btnColors.addEventListener( 'click', function () {
				const slug   = activePresetSlug();
				const colors = darkadminPresets[ slug ] || darkadminPresets[ DEFAULT_PRESET ] || {};
				loadPresetColors( colors );
			} );
		}

		const btnLayout = document.getElementById( 'adm-reset-layout' );
		if ( btnLayout ) {
			const layoutPresets = ( window.darkadminData && window.darkadminData.layoutPresets ) || {};
			btnLayout.addEventListener( 'click', function () {
				const slug   = activePresetSlug();
				const layout = layoutPresets[ slug ] || layoutPresets[ DEFAULT_PRESET ] || {};
				loadPresetLayout( layout );
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
							setCssVar( key, data[ key ] );
						} );
						if ( statusEl ) {
							statusEl.textContent = i18n( 'importOk' );
							statusEl.className = 'adm-import-status adm-import-ok';
						}
					} catch ( err ) {
						if ( statusEl ) {
							statusEl.textContent = i18n( 'importErr' );
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
	/**
	 * Briefly replace a copy button's variable name with a "copied" label.
	 *
	 * The original label is captured once by the caller so that repeated
	 * clicks always restore the variable name and never the temporary label.
	 *
	 * @param {HTMLElement} btn      Copy button.
	 * @param {string}      original Original variable text to restore.
	 */
	function flashCopiedLabel( btn, original ) {
		const code = btn.querySelector( 'code' );
		if ( ! code ) return;

		clearTimeout( btn.admCopyTimer );
		code.textContent = i18n( 'copied' );
		btn.admCopyTimer = setTimeout( function () {
			code.textContent = original;
		}, 1400 );
	}

	/**
	 * Copy a button's CSS variable reference to the clipboard.
	 *
	 * @param {HTMLElement} btn      Copy button.
	 * @param {string}      original Original variable text.
	 */
	function copyVarName( btn, original ) {
		navigator.clipboard.writeText( 'var(' + btn.dataset.var + ')' ).then( function () {
			flashCopiedLabel( btn, original );
		} ).catch( function () {} );
	}

	/**
	 * Wire up a single variable-name copy button.
	 *
	 * @param {HTMLElement} btn Copy button.
	 */
	function bindCopyButton( btn ) {
		const code     = btn.querySelector( 'code' );
		const original = code ? code.textContent : '';

		btn.addEventListener( 'click', function () {
			if ( ! navigator.clipboard || typeof navigator.clipboard.writeText !== 'function' ) {
				return;
			}
			copyVarName( btn, original );
		} );
	}

	function initVarCopy() {
		document.querySelectorAll( '.adm-var-copy' ).forEach( bindCopyButton );
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
