<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function darkadmin_default_colors(): array {
	return [
		'bg'               => '#1d2327',
		'bg_bar'           => '#1a1f24',
		'bg_deep'          => '#101517',
		'bg_darker'        => '#161b1f',
		'surface1'         => '#2c3338',
		'surface2'         => '#32393f',
		'surface3'         => '#3c434a',
		'table_alt'        => '#272e35',
		'plugin_inactive'  => '#252c32',
		'border'           => '#3c434a',
		'border_focus'     => '#2271b1',
		'border_hover'     => '#5a6470',
		'text'             => '#dcdcde',
		'text_muted'       => '#a7aaad',
		'text_soft'        => '#787c82',
		'text_on_primary'  => '#ffffff',
		'link'             => '#72aee6',
		'link_hover'       => '#93c5fd',
		'primary'          => '#2271b1',
		'primary_hover'    => '#135e96',
		'success'          => '#00a32a',
		'warning'          => '#dba617',
		'danger'           => '#d63638',
		'sidebar_bg'       => '#1d2327',
		'sidebar_active'   => '#2c3338',
		'sidebar_text'     => '#a7aaad',
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

function darkadmin_preset_colors(): array {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$cache = [
		'default' => darkadmin_default_colors(),
		'modern'  => [
			'bg'               => '#1e1e1e',
			'bg_bar'           => '#0c0c0c',
			'bg_deep'          => '#0c0c0c',
			'bg_darker'        => '#080808',
			'surface1'         => '#2a2a2a',
			'surface2'         => '#333333',
			'surface3'         => '#3d3d3d',
			'table_alt'        => '#242424',
			'plugin_inactive'  => '#202020',
			'border'           => '#3a3a3a',
			'border_focus'     => '#3858e9',
			'border_hover'     => '#6b7aee',
			'text'             => '#f0f0f0',
			'text_muted'       => '#a0a0a0',
			'text_soft'        => '#666666',
			'text_on_primary'  => '#ffffff',
			'link'             => '#7b96f5',
			'link_hover'       => '#a5b8fa',
			'primary'          => '#3858e9',
			'primary_hover'    => '#2145d4',
			'success'          => '#00ba37',
			'warning'          => '#dba617',
			'danger'           => '#d63638',
			'sidebar_bg'       => '#1e1e1e',
			'sidebar_active'   => '#3858e9',
			'sidebar_text'     => '#c8c8c8',
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
	return $cache;
}

function darkadmin_preset_css_file( string $preset ): string {
	$map = [
		'default' => 'darkadmin-dark.css',
		'modern'  => 'darkadmin-wp-modern.css',
	];
	return $map[ $preset ] ?? 'darkadmin-dark.css';
}

function darkadmin_css_variable_map(): array {
	return [
		'bg'              => [ 'var' => '--adm-bg',              'label' => __( 'Background (Base)',                     'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ) ],
		'bg_bar'          => [ 'var' => '--adm-bg-bar',          'label' => __( 'Background (Admin Bar)',                'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ) ],
		'bg_deep'         => [ 'var' => '--adm-bg-deep',         'label' => __( 'Background (Deep / Submenu)',           'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ) ],
		'bg_darker'       => [ 'var' => '--adm-bg-darker',       'label' => __( 'Background (Darker / Editor Gutter)',   'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Backgrounds', 'darkadmin-dark-mode-for-adminpanel' ) ],
		'surface1'        => [ 'var' => '--adm-surface-1',       'label' => __( 'Surface 1 (Cards / Tables)',            'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Surfaces',    'darkadmin-dark-mode-for-adminpanel' ) ],
		'surface2'        => [ 'var' => '--adm-surface-2',       'label' => __( 'Surface 2 (Inputs)',                    'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Surfaces',    'darkadmin-dark-mode-for-adminpanel' ) ],
		'surface3'        => [ 'var' => '--adm-surface-3',       'label' => __( 'Surface 3 (Hover)',                     'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Surfaces',    'darkadmin-dark-mode-for-adminpanel' ) ],
		'table_alt'       => [ 'var' => '--adm-table-alt',       'label' => __( 'Table Row Alternate',                   'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Surfaces',    'darkadmin-dark-mode-for-adminpanel' ) ],
		'plugin_inactive' => [ 'var' => '--adm-plugin-inactive', 'label' => __( 'Plugin Inactive Row',                   'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Surfaces',    'darkadmin-dark-mode-for-adminpanel' ) ],
		'border'          => [ 'var' => '--adm-border',          'label' => __( 'Border',                                'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Borders',     'darkadmin-dark-mode-for-adminpanel' ) ],
		'border_focus'    => [ 'var' => '--adm-border-focus',    'label' => __( 'Border (Focus)',                        'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Borders',     'darkadmin-dark-mode-for-adminpanel' ) ],
		'border_hover'    => [ 'var' => '--adm-border-hover',    'label' => __( 'Border (Hover)',                        'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Borders',     'darkadmin-dark-mode-for-adminpanel' ) ],
		'text'            => [ 'var' => '--adm-text',            'label' => __( 'Primary Text',                          'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Text',        'darkadmin-dark-mode-for-adminpanel' ) ],
		'text_muted'      => [ 'var' => '--adm-text-muted',      'label' => __( 'Muted Text',                            'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Text',        'darkadmin-dark-mode-for-adminpanel' ) ],
		'text_soft'       => [ 'var' => '--adm-text-soft',       'label' => __( 'Soft Text (Row Actions)',                'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Text',        'darkadmin-dark-mode-for-adminpanel' ) ],
		'text_on_primary' => [ 'var' => '--adm-text-on-primary', 'label' => __( 'Text on Primary / White',               'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Text',        'darkadmin-dark-mode-for-adminpanel' ) ],
		'link'            => [ 'var' => '--adm-link',            'label' => __( 'Link Color',                            'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Links',       'darkadmin-dark-mode-for-adminpanel' ) ],
		'link_hover'      => [ 'var' => '--adm-link-hover',      'label' => __( 'Link Hover',                            'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Links',       'darkadmin-dark-mode-for-adminpanel' ) ],
		'primary'         => [ 'var' => '--adm-primary',         'label' => __( 'Primary / Buttons',                     'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Brand',       'darkadmin-dark-mode-for-adminpanel' ) ],
		'primary_hover'   => [ 'var' => '--adm-primary-hover',   'label' => __( 'Primary Hover',                         'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Brand',       'darkadmin-dark-mode-for-adminpanel' ) ],
		'success'         => [ 'var' => '--adm-success',         'label' => __( 'Success',                               'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Brand',       'darkadmin-dark-mode-for-adminpanel' ) ],
		'warning'         => [ 'var' => '--adm-warning',         'label' => __( 'Warning',                               'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Brand',       'darkadmin-dark-mode-for-adminpanel' ) ],
		'danger'          => [ 'var' => '--adm-danger',          'label' => __( 'Danger / Error',                        'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Brand',       'darkadmin-dark-mode-for-adminpanel' ) ],
		'sidebar_bg'      => [ 'var' => '--adm-sidebar-bg',      'label' => __( 'Sidebar Background',                    'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Sidebar',     'darkadmin-dark-mode-for-adminpanel' ) ],
		'sidebar_active'  => [ 'var' => '--adm-sidebar-active',  'label' => __( 'Sidebar Active Item',                   'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Sidebar',     'darkadmin-dark-mode-for-adminpanel' ) ],
		'sidebar_text'    => [ 'var' => '--adm-sidebar-text',    'label' => __( 'Sidebar Text',                          'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'Sidebar',     'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_keyword'      => [ 'var' => '--adm-cm-keyword',      'label' => __( 'Code: Keyword',                         'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_operator'     => [ 'var' => '--adm-cm-operator',     'label' => __( 'Code: Operator / Bracket',              'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_variable2'    => [ 'var' => '--adm-cm-variable2',    'label' => __( 'Code: Variable / Def',                  'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_property'     => [ 'var' => '--adm-cm-property',     'label' => __( 'Code: Property',                        'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_number'       => [ 'var' => '--adm-cm-number',       'label' => __( 'Code: Number / Atom',                   'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_string'       => [ 'var' => '--adm-cm-string',       'label' => __( 'Code: String',                          'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_string2'      => [ 'var' => '--adm-cm-string2',      'label' => __( 'Code: String (alt)',                    'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_comment'      => [ 'var' => '--adm-cm-comment',      'label' => __( 'Code: Comment',                         'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_tag'          => [ 'var' => '--adm-cm-tag',          'label' => __( 'Code: Tag',                             'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_attribute'    => [ 'var' => '--adm-cm-attribute',    'label' => __( 'Code: Attribute / Qualifier / Builtin', 'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
		'cm_bracket'      => [ 'var' => '--adm-cm-bracket',      'label' => __( 'Code: Bracket',                         'darkadmin-dark-mode-for-adminpanel' ), 'group' => __( 'CodeMirror',  'darkadmin-dark-mode-for-adminpanel' ) ],
	];
}

/**
 * Shared layout defaults used by all presets.
 * Both presets use the same values so users start from an identical base.
 */
function darkadmin_default_layout(): array {
	return [
		'space_2'   => '8px',
		'space_3'   => '12px',
		'btn_h'     => '30px',
		'input_h'   => '30px',
		'radius_sm' => '4px',
		'radius_md' => '6px',
		'radius_lg' => '8px',
		'shadow_md' => '0 2px 8px rgba(0,0,0,.35)',
	];
}

function darkadmin_preset_layout(): array {
	$shared = darkadmin_default_layout();
	return [
		'default' => $shared,
		'modern'  => $shared,
	];
}

function darkadmin_layout_variable_map(): array {
	return [
		'space_2'   => [ 'var' => '--adm-space-2',   'label' => __( 'Spacing (Small)',         'darkadmin-dark-mode-for-adminpanel' ), 'unit' => 'px' ],
		'space_3'   => [ 'var' => '--adm-space-3',   'label' => __( 'Spacing (Medium)',        'darkadmin-dark-mode-for-adminpanel' ), 'unit' => 'px' ],
		'btn_h'     => [ 'var' => '--adm-btn-h',     'label' => __( 'Button Height',           'darkadmin-dark-mode-for-adminpanel' ), 'unit' => 'px' ],
		'input_h'   => [ 'var' => '--adm-input-h',   'label' => __( 'Input Height',            'darkadmin-dark-mode-for-adminpanel' ), 'unit' => 'px' ],
		'radius_sm' => [ 'var' => '--adm-radius-sm', 'label' => __( 'Border Radius (Small)',   'darkadmin-dark-mode-for-adminpanel' ), 'unit' => 'px' ],
		'radius_md' => [ 'var' => '--adm-radius-md', 'label' => __( 'Border Radius (Medium)',  'darkadmin-dark-mode-for-adminpanel' ), 'unit' => 'px' ],
		'radius_lg' => [ 'var' => '--adm-radius-lg', 'label' => __( 'Border Radius (Large)',   'darkadmin-dark-mode-for-adminpanel' ), 'unit' => 'px' ],
		'shadow_md' => [ 'var' => '--adm-shadow-md', 'label' => __( 'Shadow',                  'darkadmin-dark-mode-for-adminpanel' ), 'unit' => '' ],
	];
}

function darkadmin_sanitize_colors( $input ): array {
	$submitted_preset = isset( $_POST['darkadmin_preset'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		? sanitize_key( wp_unslash( $_POST['darkadmin_preset'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		: 'default';

	$allowed_presets = array_keys( darkadmin_preset_colors() );
	if ( ! in_array( $submitted_preset, $allowed_presets, true ) ) {
		$submitted_preset = 'default';
	}

	$defaults = darkadmin_preset_fallbacks( $submitted_preset );
	$output   = [];
	foreach ( $defaults as $key => $default ) {
		$raw            = $input[ $key ] ?? $default;
		$output[ $key ] = sanitize_hex_color( $raw ) ?: $default;
	}
	return $output;
}

function darkadmin_sanitize_layout( $input ): array {
	$submitted_preset = isset( $_POST['darkadmin_preset'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		? sanitize_key( wp_unslash( $_POST['darkadmin_preset'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
		: 'default';

	$presets = darkadmin_preset_layout();
	if ( ! isset( $presets[ $submitted_preset ] ) ) {
		$submitted_preset = 'default';
	}

	$defaults = $presets[ $submitted_preset ];
	$var_map  = darkadmin_layout_variable_map();
	$output   = [];

	foreach ( $defaults as $key => $default ) {
		$raw = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : $default;
		if ( '' === trim( $raw ) ) {
			$raw = $default;
		}
		if ( '' !== $var_map[ $key ]['unit'] ) {
			$numeric = (float) $raw;
			if ( $numeric < 0 ) {
				$numeric = 0;
			}
			$raw = $numeric . $var_map[ $key ]['unit'];
		}
		$output[ $key ] = $raw;
	}
	return $output;
}

function darkadmin_sanitize_user_access_mode( string $value ): string {
	$allowed = [ 'all', 'include', 'exclude' ];
	return in_array( $value, $allowed, true ) ? $value : 'all';
}

function darkadmin_sanitize_custom_css( string $css ): string {
	return wp_strip_all_tags( $css );
}
