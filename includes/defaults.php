<?php
/**
 * Default color and layout values for the DarkAdmin plugin.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns the default color palette.
 *
 * @return array<string, string>
 */
function darkadmin_default_colors(): array {
	return array(
		'bg'              => '#1d2327',
		'bg_bar'          => '#1a1f24',
		'bg_deep'         => '#101517',
		'bg_darker'       => '#161b1f',
		'surface1'        => '#2c3338',
		'surface2'        => '#32393f',
		'surface3'        => '#3c434a',
		'table_alt'       => '#272e35',
		'plugin_inactive' => '#252c32',
		'border'          => '#3c434a',
		'border_focus'    => '#2271b1',
		'border_hover'    => '#5a6470',
		'text'            => '#dcdcde',
		'text_muted'      => '#a7aaad',
		'text_soft'       => '#787c82',
		'text_on_primary' => '#ffffff',
		'link'            => '#72aee6',
		'link_hover'      => '#93c5fd',
		'primary'         => '#2271b1',
		'primary_hover'   => '#135e96',
		'success'         => '#00a32a',
		'warning'         => '#dba617',
		'danger'          => '#d63638',
		'sidebar_bg'      => '#1d2327',
		'sidebar_active'  => '#2c3338',
		'sidebar_text'    => '#a7aaad',
		'cm_keyword'      => '#c792ea',
		'cm_operator'     => '#89ddff',
		'cm_variable2'    => '#82aaff',
		'cm_property'     => '#b2ccd6',
		'cm_number'       => '#f78c6c',
		'cm_string'       => '#c3e88d',
		'cm_string2'      => '#f07178',
		'cm_comment'      => '#546e7a',
		'cm_tag'          => '#f07178',
		'cm_attribute'    => '#ffcb6b',
		'cm_bracket'      => '#89ddff',
	);
}

/**
 * Returns all built-in color presets, keyed by preset slug.
 *
 * Results are cached in a static variable to avoid re-building the array on
 * repeated calls within the same request.
 *
 * @return array<string, array<string, string>>
 */
function darkadmin_preset_colors(): array {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = array(
		'default' => darkadmin_default_colors(),
		'modern'  => array(
			'bg'              => '#1e1e1e',
			'bg_bar'          => '#0c0c0c',
			'bg_deep'         => '#0c0c0c',
			'bg_darker'       => '#080808',
			'surface1'        => '#2a2a2a',
			'surface2'        => '#333333',
			'surface3'        => '#3d3d3d',
			'table_alt'       => '#242424',
			'plugin_inactive' => '#202020',
			'border'          => '#3a3a3a',
			'border_focus'    => '#3858e9',
			'border_hover'    => '#6b7aee',
			'text'            => '#f0f0f0',
			'text_muted'      => '#a0a0a0',
			'text_soft'       => '#666666',
			'text_on_primary' => '#ffffff',
			'link'            => '#7b96f5',
			'link_hover'      => '#a5b8fa',
			'primary'         => '#3858e9',
			'primary_hover'   => '#2145d4',
			'success'         => '#00ba37',
			'warning'         => '#dba617',
			'danger'          => '#d63638',
			'sidebar_bg'      => '#1e1e1e',
			'sidebar_active'  => '#3858e9',
			'sidebar_text'    => '#c8c8c8',
			'cm_keyword'      => '#c792ea',
			'cm_operator'     => '#89ddff',
			'cm_variable2'    => '#82aaff',
			'cm_property'     => '#b2ccd6',
			'cm_number'       => '#f78c6c',
			'cm_string'       => '#c3e88d',
			'cm_string2'      => '#f07178',
			'cm_comment'      => '#546e7a',
			'cm_tag'          => '#f07178',
			'cm_attribute'    => '#ffcb6b',
			'cm_bracket'      => '#89ddff',
		),
	);
	return $cache;
}

/**
 * Returns the CSS filename for a given preset slug.
 *
 * @param string $preset Preset slug (e.g. 'default', 'modern').
 * @return string CSS filename.
 */
function darkadmin_preset_css_file( string $preset ): string {
	$map = array(
		'default' => 'darkadmin-dark.css',
		'modern'  => 'darkadmin-wp-modern.css',
	);
	return isset( $map[ $preset ] ) ? $map[ $preset ] : 'darkadmin-dark.css';
}

/**
 * Returns the CSS variable map for all color tokens.
 *
 * Each entry maps a color key to its CSS variable name, human-readable label,
 * and display group used on the settings page.
 *
 * @return array<string, array{var: string, label: string, group: string}>
 */
function darkadmin_css_variable_map(): array {
	return array(
		'bg'              => array(
			'var'   => '--adm-bg',
			'label' => __( 'Background (Base)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'bg_bar'          => array(
			'var'   => '--adm-bg-bar',
			'label' => __( 'Background (Admin Bar)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'bg_deep'         => array(
			'var'   => '--adm-bg-deep',
			'label' => __( 'Background (Deep / Submenu)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'bg_darker'       => array(
			'var'   => '--adm-bg-darker',
			'label' => __( 'Background (Darker / Editor Gutter)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'surface1'        => array(
			'var'   => '--adm-surface-1',
			'label' => __( 'Surface 1 (Cards / Tables)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Surfaces', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'surface2'        => array(
			'var'   => '--adm-surface-2',
			'label' => __( 'Surface 2 (Inputs)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Surfaces', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'surface3'        => array(
			'var'   => '--adm-surface-3',
			'label' => __( 'Surface 3 (Hover)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Surfaces', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'table_alt'       => array(
			'var'   => '--adm-table-alt',
			'label' => __( 'Table Row Alternate', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Surfaces', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'plugin_inactive' => array(
			'var'   => '--adm-plugin-inactive',
			'label' => __( 'Plugin Inactive Row', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Surfaces', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'border'          => array(
			'var'   => '--adm-border',
			'label' => __( 'Border', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Borders', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'border_focus'    => array(
			'var'   => '--adm-border-focus',
			'label' => __( 'Border (Focus)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Borders', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'border_hover'    => array(
			'var'   => '--adm-border-hover',
			'label' => __( 'Border (Hover)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Borders', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'text'            => array(
			'var'   => '--adm-text',
			'label' => __( 'Primary Text', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Text', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'text_muted'      => array(
			'var'   => '--adm-text-muted',
			'label' => __( 'Muted Text', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Text', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'text_soft'       => array(
			'var'   => '--adm-text-soft',
			'label' => __( 'Soft Text (Row Actions)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Text', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'text_on_primary' => array(
			'var'   => '--adm-text-on-primary',
			'label' => __( 'Text on Primary / White', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Text', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'link'            => array(
			'var'   => '--adm-link',
			'label' => __( 'Link Color', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Links', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'link_hover'      => array(
			'var'   => '--adm-link-hover',
			'label' => __( 'Link Hover', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Links', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'primary'         => array(
			'var'   => '--adm-primary',
			'label' => __( 'Primary / Buttons', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Brand', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'primary_hover'   => array(
			'var'   => '--adm-primary-hover',
			'label' => __( 'Primary Hover', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Brand', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'success'         => array(
			'var'   => '--adm-success',
			'label' => __( 'Success', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Brand', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'warning'         => array(
			'var'   => '--adm-warning',
			'label' => __( 'Warning', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Brand', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'danger'          => array(
			'var'   => '--adm-danger',
			'label' => __( 'Danger / Error', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Brand', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'sidebar_bg'      => array(
			'var'   => '--adm-sidebar-bg',
			'label' => __( 'Sidebar Background', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Sidebar', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'sidebar_active'  => array(
			'var'   => '--adm-sidebar-active',
			'label' => __( 'Sidebar Active Item', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Sidebar', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'sidebar_text'    => array(
			'var'   => '--adm-sidebar-text',
			'label' => __( 'Sidebar Text', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'Sidebar', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_keyword'      => array(
			'var'   => '--adm-cm-keyword',
			'label' => __( 'Code: Keyword', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_operator'     => array(
			'var'   => '--adm-cm-operator',
			'label' => __( 'Code: Operator / Bracket', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_variable2'    => array(
			'var'   => '--adm-cm-variable2',
			'label' => __( 'Code: Variable / Def', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_property'     => array(
			'var'   => '--adm-cm-property',
			'label' => __( 'Code: Property', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_number'       => array(
			'var'   => '--adm-cm-number',
			'label' => __( 'Code: Number / Atom', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_string'       => array(
			'var'   => '--adm-cm-string',
			'label' => __( 'Code: String', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_string2'      => array(
			'var'   => '--adm-cm-string2',
			'label' => __( 'Code: String (alt)', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_comment'      => array(
			'var'   => '--adm-cm-comment',
			'label' => __( 'Code: Comment', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_tag'          => array(
			'var'   => '--adm-cm-tag',
			'label' => __( 'Code: Tag', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_attribute'    => array(
			'var'   => '--adm-cm-attribute',
			'label' => __( 'Code: Attribute / Qualifier / Builtin', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
		'cm_bracket'      => array(
			'var'   => '--adm-cm-bracket',
			'label' => __( 'Code: Bracket', 'darkadmin-dark-mode-for-adminpanel' ),
			'group' => __( 'CodeMirror', 'darkadmin-dark-mode-for-adminpanel' ),
		),
	);
}

/**
 * Shared layout defaults used by all presets.
 * Both presets use the same values so users start from an identical base.
 *
 * @return array<string, string>
 */
function darkadmin_default_layout(): array {
	return array(
		'space_2'   => '8px',
		'space_3'   => '12px',
		'btn_h'     => '30px',
		'input_h'   => '30px',
		'radius_sm' => '4px',
		'radius_md' => '6px',
		'radius_lg' => '8px',
		'shadow_md' => '0 2px 8px rgba(0,0,0,.35)',
	);
}

/**
 * Returns layout presets keyed by preset slug.
 *
 * Currently all presets share the same layout defaults.
 *
 * @return array<string, array<string, string>>
 */
function darkadmin_preset_layout(): array {
	$shared = darkadmin_default_layout();
	return array(
		'default' => $shared,
		'modern'  => $shared,
	);
}

/**
 * Returns the CSS variable map for all layout tokens.
 *
 * Each entry maps a layout key to its CSS variable name, human-readable label,
 * and the CSS unit used for sanitization.
 *
 * @return array<string, array{var: string, label: string, unit: string}>
 */
function darkadmin_layout_variable_map(): array {
	return array(
		'space_2'   => array(
			'var'   => '--adm-space-2',
			'label' => __( 'Spacing (Small)', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => 'px',
		),
		'space_3'   => array(
			'var'   => '--adm-space-3',
			'label' => __( 'Spacing (Medium)', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => 'px',
		),
		'btn_h'     => array(
			'var'   => '--adm-btn-h',
			'label' => __( 'Button Height', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => 'px',
		),
		'input_h'   => array(
			'var'   => '--adm-input-h',
			'label' => __( 'Input Height', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => 'px',
		),
		'radius_sm' => array(
			'var'   => '--adm-radius-sm',
			'label' => __( 'Border Radius (Small)', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => 'px',
		),
		'radius_md' => array(
			'var'   => '--adm-radius-md',
			'label' => __( 'Border Radius (Medium)', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => 'px',
		),
		'radius_lg' => array(
			'var'   => '--adm-radius-lg',
			'label' => __( 'Border Radius (Large)', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => 'px',
		),
		'shadow_md' => array(
			'var'   => '--adm-shadow-md',
			'label' => __( 'Shadow', 'darkadmin-dark-mode-for-adminpanel' ),
			'unit'  => '',
		),
	);
}

/**
 * Sanitize callback for the darkadmin_colors setting.
 *
 * Receives the submitted array from the Settings API (nonce already verified
 * by options.php via settings_fields()). No direct $_POST access is performed.
 *
 * @param mixed $input Raw input from the Settings API.
 * @return array Sanitized color values.
 */
function darkadmin_sanitize_colors( $input ): array {
	$input  = is_array( $input ) ? $input : array();
	$preset = isset( $input['_preset'] )
		? sanitize_key( (string) $input['_preset'] )
		: 'default';

	$allowed_presets = array_keys( darkadmin_preset_colors() );
	if ( ! in_array( $preset, $allowed_presets, true ) ) {
		$preset = 'default';
	}

	$defaults = darkadmin_preset_colors()[ $preset ];
	$output   = array();
	foreach ( $defaults as $key => $default ) {
		$raw            = isset( $input[ $key ] ) ? (string) $input[ $key ] : $default;
		$sanitized      = sanitize_hex_color( $raw );
		$output[ $key ] = ( false !== $sanitized && '' !== $sanitized ) ? $sanitized : $default;
	}
	return $output;
}

/**
 * Sanitize callback for the darkadmin_layout setting.
 *
 * Receives the submitted array from the Settings API (nonce already verified
 * by options.php via settings_fields()). No direct $_POST access is performed.
 *
 * For fields with a px unit the value is cast to a non-negative float.
 * For the shadow_md field (empty unit) the value is validated against a safe
 * CSS box-shadow pattern to prevent CSS injection; invalid values fall back
 * to the preset default.
 *
 * @param mixed $input Raw input from the Settings API.
 * @return array Sanitized layout values.
 */
function darkadmin_sanitize_layout( $input ): array {
	$input  = is_array( $input ) ? $input : array();
	$preset = isset( $input['_preset'] )
		? sanitize_key( (string) $input['_preset'] )
		: 'default';

	$presets = darkadmin_preset_layout();
	if ( ! isset( $presets[ $preset ] ) ) {
		$preset = 'default';
	}

	$defaults = $presets[ $preset ];
	$var_map  = darkadmin_layout_variable_map();
	$output   = array();

	foreach ( $defaults as $key => $default ) {
		$raw = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( (string) $input[ $key ] ) ) : $default;
		if ( '' === trim( $raw ) ) {
			$raw = $default;
		}
		if ( '' !== $var_map[ $key ]['unit'] ) {
			$numeric = (float) $raw;
			if ( $numeric < 0 ) {
				$numeric = 0;
			}
			$raw = $numeric . $var_map[ $key ]['unit'];
		} elseif ( ! preg_match( '/^[a-zA-Z0-9\s\-.,%#()\/:]$/', $raw ) ) {
			// shadow_md: allow only safe CSS box-shadow values.
			// Pattern permits: lengths (px/em/rem/%), colors (hex, rgb, rgba, hsl, hsla),
			// keywords (inset, none), digits, spaces, commas, dots, slashes and parentheses.
			$raw = $default;
		}
		$output[ $key ] = $raw;
	}
	return $output;
}

/**
 * Sanitizes the user access mode option value.
 *
 * @param string $value Raw value to sanitize.
 * @return string One of 'all', 'include', or 'exclude'.
 */
function darkadmin_sanitize_user_access_mode( string $value ): string {
	$allowed = array( 'all', 'include', 'exclude' );
	return in_array( $value, $allowed, true ) ? $value : 'all';
}

/**
 * Sanitizes the custom CSS option value.
 *
 * @param string $css Raw CSS string.
 * @return string Sanitized CSS with all HTML tags stripped.
 */
function darkadmin_sanitize_custom_css( string $css ): string {
	return wp_strip_all_tags( $css );
}
