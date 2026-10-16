/**
 * WP Admin Dark Mode - Auto Darken
 *
 * Scans all elements after page load and dynamically darkens any bright
 * backgrounds or dark text colours not covered by the main stylesheet.
 * Only active when the "Auto Dark Mode" option is enabled in settings.
 *
 * A MutationObserver watches for dynamically injected nodes (AJAX, React etc.)
 * and applies the same logic to new elements.
 */
( function () {
	'use strict';

	/**
	 * Parse an rgb/rgba string into { r, g, b, a } or null.
	 * Handles both 'rgb(r, g, b)' and 'rgba(r, g, b, a)' including
	 * values without spaces between components.
	 * @param {string} color
	 * @returns {{ r: number, g: number, b: number, a: number }|null}
	 */
	function parseRgb( color ) {
		var m = color.match( /rgba?\(\s*(\d+)[,\s]+\s*(\d+)[,\s]+\s*(\d+)(?:[,\s\/]+\s*([\d.]+))?\s*\)/ );
		if ( ! m ) return null;
		return {
			r: parseInt( m[1], 10 ),
			g: parseInt( m[2], 10 ),
			b: parseInt( m[3], 10 ),
			a: m[4] !== undefined ? parseFloat( m[4] ) : 1,
		};
	}

	/**
	 * Relative luminance (0 = black, 1 = white) per WCAG 2.1.
	 * @param {{ r: number, g: number, b: number }} rgb
	 * @returns {number}
	 */
	function luminance( rgb ) {
		var c = [ rgb.r, rgb.g, rgb.b ].map( function ( v ) {
			v /= 255;
			return v <= 0.03928 ? v / 12.92 : Math.pow( ( v + 0.055 ) / 1.055, 2.4 );
		} );
		return 0.2126 * c[0] + 0.7152 * c[1] + 0.0722 * c[2];
	}

	/**
	 * Darken an rgb object by a factor (0 = black, 1 = unchanged).
	 * @param {{ r: number, g: number, b: number }} rgb
	 * @param {number} factor
	 * @returns {string}
	 */
	function darkenRgb( rgb, factor ) {
		var r = Math.round( rgb.r * factor );
		var g = Math.round( rgb.g * factor );
		var b = Math.round( rgb.b * factor );

		// Preserve the source alpha so translucent scrims/overlays stay translucent.
		if ( rgb.a < 1 ) {
			return 'rgba(' + r + ',' + g + ',' + b + ',' + rgb.a + ')';
		}

		return 'rgb(' + r + ',' + g + ',' + b + ')';
	}

	/**
	 * Lighten an rgb object towards white by a blend factor
	 * (0 = unchanged, 1 = white).
	 * @param {{ r: number, g: number, b: number }} rgb
	 * @param {number} t
	 * @returns {string}
	 */
	function lightenRgb( rgb, t ) {
		function mix( v ) {
			return Math.round( v + ( 255 - v ) * t );
		}

		var r = mix( rgb.r );
		var g = mix( rgb.g );
		var b = mix( rgb.b );

		// Preserve the source alpha so translucent text stays translucent.
		if ( rgb.a < 1 ) {
			return 'rgba(' + r + ',' + g + ',' + b + ',' + rgb.a + ')';
		}

		return 'rgb(' + r + ',' + g + ',' + b + ')';
	}

	var SKIP_TAGS = new Set( [
		'IMG', 'VIDEO', 'CANVAS', 'IFRAME', 'SCRIPT', 'STYLE', 'SVG', 'PATH',
		'CIRCLE', 'RECT', 'LINE', 'POLYLINE', 'POLYGON', 'DEFS', 'USE', 'SYMBOL',
	] );

	var PROCESSED_ATTR = 'data-adm-darkened';

	/**
	 * Apply auto-dark logic to a single element.
	 * @param {Element} el
	 */
	function processElement( el ) {
		if ( ! ( el instanceof HTMLElement ) )    return;
		if ( SKIP_TAGS.has( el.tagName ) )        return;
		if ( el.hasAttribute( PROCESSED_ATTR ) )  return;

		el.setAttribute( PROCESSED_ATTR, '1' );

		if ( el.closest && el.closest( '.adm-var-swatch, .wp-color-result, .wp-picker-container, .wp-picker-input-wrap' ) ) {
			return;
		}

		var style = window.getComputedStyle( el );

		var bg = parseRgb( style.backgroundColor );
		if ( bg ) {
			var bgLum = luminance( bg );
			if ( bg.a >= 0.1 && bgLum > 0.15 ) {
				el.style.backgroundColor = darkenRgb( bg, 0.15 );
			}
		}

		var fg = parseRgb( style.color );
		if ( fg && luminance( fg ) < 0.20 ) {
			el.style.color = lightenRgb( fg, 0.85 );
		}

		var hasBorder = [ 'Top', 'Right', 'Bottom', 'Left' ].some( function ( side ) {
			return 'none' !== style[ 'border' + side + 'Style' ] && parseFloat( style[ 'border' + side + 'Width' ] ) > 0;
		} );

		var bc = parseRgb( style.borderColor );
		if ( hasBorder && bc && luminance( bc ) > 0.40 ) {
			el.style.borderColor = darkenRgb( bc, 0.25 );
		}
	}

	/**
	 * Walk every element inside a root node.
	 * @param {Element|Document} root
	 */
	function processTree( root ) {
		var els = ( root === document )
			? document.querySelectorAll( '*' )
			: root.querySelectorAll ? root.querySelectorAll( '*' ) : [];

		if ( root instanceof HTMLElement ) processElement( root );

		for ( var i = 0; i < els.length; i++ ) {
			processElement( els[i] );
		}
	}

	function init() {
		processTree( document );

		var observer = new MutationObserver( function ( mutations ) {
			mutations.forEach( function ( mutation ) {
				mutation.addedNodes.forEach( function ( node ) {
					if ( node.nodeType !== 1 ) return;
					processTree( /** @type {Element} */ ( node ) );
				} );
			} );
		} );

		observer.observe( document.body, {
			childList: true,
			subtree:   true,
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}

}() );
