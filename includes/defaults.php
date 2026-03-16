<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default color palette.
 * Keys map 1:1 to CSS custom properties in darkadmin-dark.css (:root).
 */
function adm_default_colors(): array {
	return [
		// Backgrounds
		'bg'               => '#1d2327',
		'bg_bar'           => '#1a1f24',
		'bg_deep'          => '#101517',
		'bg_darker'        => '#161b1f',
		// Surfaces
		'surface1'         => '#2c3338',
		'surface2'         => '#32393f',
		'surface3'         => '#3c434a',
		'table_alt'        => '#272e35',
		'plugin_inactive'  => '#252c32',
		// Borders
		'border'           => '#3c434a',
		'border_focus'     => '#2271b1',
		'border_hover'     => '#5a6470',
		// Text
		'text'             => '#dcdcde',
		'text_muted'       => '#a7aaad',
		'text_soft'        => '#787c82',
		'text_on_primary'  => '#ffffff',
		// Links
		'link'             => '#72aee6',
		'link_hover'       => '#93c5fd',
		// Brand
		'primary'          => '#2271b1',
		'primary_hover'    => '#135e96',
		'success'          => '#00a32a',
		'warning'          => '#dba617',
		'danger'           => '#d63638',
		// CodeMirror syntax tokens
		'cm_keyword'       => '#c792ea',
		'cm_operator'      => '#89ddff',
		'cm_variable2'     => '#82aaff',
		'cm_property'      => '#b2ccd6',
		'cm_number'        => '#f78c6c',
		'cm_string'        => '#c3e88d',
		'cm_string2'       => '#f07178',
		'cm_comment'       => '#546e7a',
		'cm_tag'           => '#f07178',
		'cm_attribute'     => '#ffcb6b',
		'cm_bracket'       => '#89ddff',
	];
}

/**
 * Preset color palettes.
 * 'default' mirrors adm_default_colors() (classic WP 6.x dark).
 * 'modern'  faithful dark port of the WP Modern (7.0) design language:
 *           deep navy base, sky-blue accent, high contrast.
 *
 * IMPORTANT: These values must stay in sync with:
 *   - adm_preset_fallbacks() in enqueue.php
 *   - $preset_meta in settings-page.php (preview swatches)
 */
function adm_preset_colors(): array {
	return [
		'default' => adm_default_colors(),
		'modern'  => [
			// Backgrounds
			'bg'               => '#1e1e1e',
			'bg_bar'           => '#0c0c0c',
			'bg_deep'          => '#0c0c0c',
			'bg_darker'        => '#080808',
			// Surfaces
			'surface1'         => '#2a2a2a',
			'surface2'         => '#333333',
			'surface3'         => '#3d3d3d',
			'table_alt'        => '#242424',
			'plugin_inactive'  => '#202020',
			// Borders
			'border'           => '#3a3a3a',
			'border_focus'     => '#3858e9',
			'border_hover'     => '#6b7aee',
			// Text
			'text'             => '#f0f0f0',
			'text_muted'       => '#a0a0a0',
			'text_soft'        => '#666666',
			'text_on_primary'  => '#ffffff',
			// Links
			'link'             => '#7b96f5',
			'link_hover'       => '#a5b8fa',
			// Brand
			'primary'          => '#3858e9',
			'primary_hover'    => '#2145d4',
			'success'          => '#00ba37',
			'warning'          => '#dba617',
			'danger'           => '#d63638',
			// CodeMirror syntax tokens
			'cm_keyword'       => '#c792ea',
			'cm_operator'      => '#89ddff',
			'cm_variable2'     => '#82aaff',
			'cm_property'      => '#b2ccd6',
			'cm_number'        => '#f78c6c',
			'cm_string'        => '#c3e88d',
			'cm_string2'       => '#f07178',
			'cm_comment'       => '#546e7a',
			'cm_tag'           => '#f07178',
			'cm_attribute'     => '#ffcb6b',
			'cm_bracket'       => '#89ddff',
		],
	];
}

/**
 * Returns the CSS filename for a given preset slug.
 */
function adm_preset_css_file( string $preset ): string {
	$map = [
		'default' => 'darkadmin-dark.css',
		'modern'  => 'darkadmin-wp-modern.css',
	];
	return $map[ $preset ] ?? 'darkadmin-dark.css';
}

/**
 * Maps each color key to its CSS custom property name, label and group.
 */
function adm_css_variable_map(): array {
	return [
		'bg'              => [ 'var' => '--adm-bg',              'label' => 'Background (Base)',                    'group' => 'Backgrounds' ],
		'bg_bar'          => [ 'var' => '--adm-bg-bar',          'label' => 'Background (Admin Bar)',               'group' => 'Backgrounds' ],
		'bg_deep'         => [ 'var' => '--adm-bg-deep',         'label' => 'Background (Deep / Submenu)',          'group' => 'Backgrounds' ],
		'bg_darker'       => [ 'var' => '--adm-bg-darker',       'label' => 'Background (Darker / Editor Gutter)',  'group' => 'Backgrounds' ],
		'surface1'        => [ 'var' => '--adm-surface-1',       'label' => 'Surface 1 (Cards / Tables)',           'group' => 'Surfaces' ],
		'surface2'        => [ 'var' => '--adm-surface-2',       'label' => 'Surface 2 (Inputs)',                   'group' => 'Surfaces' ],
		'surface3'        => [ 'var' => '--adm-surface-3',       'label' => 'Surface 3 (Hover)',                    'group' => 'Surfaces' ],
		'table_alt'       => [ 'var' => '--adm-table-alt',       'label' => 'Table Row Alternate',                  'group' => 'Surfaces' ],
		'plugin_inactive' => [ 'var' => '--adm-plugin-inactive', 'label' => 'Plugin Inactive Row',                  'group' => 'Surfaces' ],
		'border'          => [ 'var' => '--adm-border',          'label' => 'Border',                               'group' => 'Borders' ],
		'border_focus'    => [ 'var' => '--adm-border-focus',    'label' => 'Border (Focus)',                       'group' => 'Borders' ],
		'border_hover'    => [ 'var' => '--adm-border-hover',    'label' => 'Border (Hover)',                       'group' => 'Borders' ],
		'text'            => [ 'var' => '--adm-text',            'label' => 'Primary Text',                         'group' => 'Text' ],
		'text_muted'      => [ 'var' => '--adm-text-muted',      'label' => 'Muted Text',                           'group' => 'Text' ],
		'text_soft'       => [ 'var' => '--adm-text-soft',       'label' => 'Soft Text (Row Actions)',              'group' => 'Text' ],
		'text_on_primary' => [ 'var' => '--adm-text-on-primary', 'label' => 'Text on Primary / White',             'group' => 'Text' ],
		'link'            => [ 'var' => '--adm-link',            'label' => 'Link Color',                           'group' => 'Links' ],
		'link_hover'      => [ 'var' => '--adm-link-hover',      'label' => 'Link Hover',                           'group' => 'Links' ],
		'primary'         => [ 'var' => '--adm-primary',         'label' => 'Primary / Buttons',                   'group' => 'Brand' ],
		'primary_hover'   => [ 'var' => '--adm-primary-hover',   'label' => 'Primary Hover',                       'group' => 'Brand' ],
		'success'         => [ 'var' => '--adm-success',         'label' => 'Success',                             'group' => 'Brand' ],
		'warning'         => [ 'var' => '--adm-warning',         'label' => 'Warning',                             'group' => 'Brand' ],
		'danger'          => [ 'var' => '--adm-danger',          'label' => 'Danger / Error',                      'group' => 'Brand' ],
		'cm_keyword'      => [ 'var' => '--adm-cm-keyword',      'label' => 'Code: Keyword',                       'group' => 'CodeMirror' ],
		'cm_operator'     => [ 'var' => '--adm-cm-operator',     'label' => 'Code: Operator / Bracket',            'group' => 'CodeMirror' ],
		'cm_variable2'    => [ 'var' => '--adm-cm-variable2',    'label' => 'Code: Variable / Def',                'group' => 'CodeMirror' ],
		'cm_property'     => [ 'var' => '--adm-cm-property',     'label' => 'Code: Property',                      'group' => 'CodeMirror' ],
		'cm_number'       => [ 'var' => '--adm-cm-number',       'label' => 'Code: Number / Atom',                 'group' => 'CodeMirror' ],
		'cm_string'       => [ 'var' => '--adm-cm-string',       'label' => 'Code: String',                        'group' => 'CodeMirror' ],
		'cm_string2'      => [ 'var' => '--adm-cm-string2',      'label' => 'Code: String (alt)',                  'group' => 'CodeMirror' ],
		'cm_comment'      => [ 'var' => '--adm-cm-comment',      'label' => 'Code: Comment',                       'group' => 'CodeMirror' ],
		'cm_tag'          => [ 'var' => '--adm-cm-tag',          'label' => 'Code: Tag',                           'group' => 'CodeMirror' ],
		'cm_attribute'    => [ 'var' => '--adm-cm-attribute',    'label' => 'Code: Attribute / Qualifier / Builtin', 'group' => 'CodeMirror' ],
		'cm_bracket'      => [ 'var' => '--adm-cm-bracket',      'label' => 'Code: Bracket',                       'group' => 'CodeMirror' ],
	];
}

/**
 * Sanitize and validate the color palette input.
 */
function adm_sanitize_colors( $input ): array {
	$defaults = adm_default_colors();
	$output   = [];
	foreach ( $defaults as $key => $default ) {
		$raw            = $input[ $key ] ?? $default;
		$output[ $key ] = sanitize_hex_color( $raw ) ?: $default;
	}
	return $output;
}

/**
 * Sanitize custom CSS input.
 * Strips HTML tags but preserves valid CSS content.
 */
function adm_sanitize_custom_css( string $css ): string {
	$css = preg_replace( '/<\/?(?:script|style|iframe|object|embed|form|input|link|meta|base)[^>]*>/i', '', $css );
	$css = preg_replace( '/<\?(?:php)?.*?\?>/is', '', $css );
	$css = strip_tags( $css );
	return $css;
}
