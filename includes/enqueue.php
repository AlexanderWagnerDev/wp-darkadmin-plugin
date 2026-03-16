<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register plugin settings.
 */
add_action( 'admin_init', function () {
	register_setting( 'adm_settings', 'adm_dark_mode_enabled', [
		'type'              => 'boolean',
		'sanitize_callback' => fn( $v ) => (bool) $v,
		'default'           => false,
	] );
	register_setting( 'adm_settings', 'adm_auto_darken', [
		'type'              => 'boolean',
		'sanitize_callback' => fn( $v ) => (bool) $v,
		'default'           => false,
	] );
	register_setting( 'adm_settings', 'adm_colors', [
		'type'              => 'array',
		'sanitize_callback' => 'adm_sanitize_colors',
		'default'           => adm_default_colors(),
	] );
	register_setting( 'adm_settings', 'adm_custom_css', [
		'type'              => 'string',
		'sanitize_callback' => 'adm_sanitize_custom_css',
		'default'           => '',
	] );
	register_setting( 'adm_settings', 'adm_allowed_users', [
		'type'              => 'array',
		'sanitize_callback' => fn( $v ) => array_map( 'absint', (array) $v ),
		'default'           => [],
	] );
	register_setting( 'adm_settings', 'adm_preset', [
		'type'              => 'string',
		'sanitize_callback' => function ( $v ) {
			$allowed = array_keys( adm_preset_colors() );
			return in_array( $v, $allowed, true ) ? $v : 'default';
		},
		'default'           => 'default',
	] );
} );

/**
 * Returns the base fallback colors for a given preset.
 * These are used when the user has not customized a specific token.
 * Each preset defines its own baseline so the inline style block
 * always reflects the correct starting point.
 *
 * @param string $preset Preset key ('default', 'modern', …).
 * @return array<string,string> Map of color key => hex value.
 */
function adm_preset_fallbacks( string $preset ): array {
	$presets = [
		'default' => [
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
		'modern' => [
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

	return $presets[ $preset ] ?? $presets['default'];
}

/**
 * Enqueue dark mode CSS and inject color token overrides as inline CSS.
 * Fallback colors are chosen based on the active preset so each preset
 * starts from its own baseline before any user customization is applied.
 */
add_action( 'admin_enqueue_scripts', function () {
	if ( ! adm_is_dark_mode_active() ) {
		return;
	}

	$preset   = get_option( 'adm_preset', 'default' );
	$css_file = adm_preset_css_file( $preset );

	// Merge user-saved colors on top of the preset-specific fallbacks.
	$fallbacks = adm_preset_fallbacks( $preset );
	$c         = wp_parse_args(
		(array) get_option( 'adm_colors', [] ),
		$fallbacks
	);

	// Cache-busting: combine plugin version + hash of current color values.
	$color_hash = substr( md5( serialize( $c ) ), 0, 8 );
	$ver        = ADM_VERSION . '-' . $color_hash;

	$sc = static fn( string $k ) => sanitize_hex_color( $c[ $k ] ?? '' ) ?: $fallbacks[ $k ];

	$vars = ':root{'                                          .
		"--adm-bg:{$sc('bg')};"                           .
		"--adm-bg-bar:{$sc('bg_bar')};"                   .
		"--adm-bg-deep:{$sc('bg_deep')};"                 .
		"--adm-bg-darker:{$sc('bg_darker')};"             .
		"--adm-surface-1:{$sc('surface1')};"              .
		"--adm-surface-2:{$sc('surface2')};"              .
		"--adm-surface-3:{$sc('surface3')};"              .
		"--adm-table-alt:{$sc('table_alt')};"             .
		"--adm-plugin-inactive:{$sc('plugin_inactive')};" .
		"--adm-border:{$sc('border')};"                   .
		"--adm-border-focus:{$sc('border_focus')};"       .
		"--adm-border-hover:{$sc('border_hover')};"       .
		"--adm-text:{$sc('text')};"                       .
		"--adm-text-muted:{$sc('text_muted')};"           .
		"--adm-text-soft:{$sc('text_soft')};"             .
		"--adm-text-on-primary:{$sc('text_on_primary')};" .
		"--adm-link:{$sc('link')};"                       .
		"--adm-link-hover:{$sc('link_hover')};"           .
		"--adm-primary:{$sc('primary')};"                 .
		"--adm-primary-hover:{$sc('primary_hover')};"     .
		"--adm-success:{$sc('success')};"                 .
		"--adm-warning:{$sc('warning')};"                 .
		"--adm-danger:{$sc('danger')};"                   .
		"--adm-cm-keyword:{$sc('cm_keyword')};"           .
		"--adm-cm-operator:{$sc('cm_operator')};"         .
		"--adm-cm-variable2:{$sc('cm_variable2')};"       .
		"--adm-cm-property:{$sc('cm_property')};"         .
		"--adm-cm-number:{$sc('cm_number')};"             .
		"--adm-cm-string:{$sc('cm_string')};"             .
		"--adm-cm-string2:{$sc('cm_string2')};"           .
		"--adm-cm-comment:{$sc('cm_comment')};"           .
		"--adm-cm-tag:{$sc('cm_tag')};"                   .
		"--adm-cm-attribute:{$sc('cm_attribute')};"       .
		"--adm-cm-bracket:{$sc('cm_bracket')};"           .
		'}';

	wp_enqueue_style(
		'adm-darkmode',
		ADM_URL . 'assets/css/' . $css_file,
		[],
		$ver
	);
	wp_add_inline_style( 'adm-darkmode', $vars );

	$custom = get_option( 'adm_custom_css', '' );
	if ( ! empty( $custom ) ) {
		wp_add_inline_style( 'adm-darkmode', adm_sanitize_custom_css( $custom ) );
	}

	if ( get_option( 'adm_auto_darken', false ) ) {
		wp_enqueue_script(
			'adm-auto-darken',
			ADM_URL . 'assets/js/auto-darken.js',
			[],
			ADM_VERSION,
			false
		);
	}
} );

/**
 * Enqueue settings page assets (color picker + settings CSS/JS).
 */
add_action( 'admin_enqueue_scripts', function ( $hook ) {
	if ( $hook !== 'settings_page_darkadmin' ) {
		return;
	}
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style(
		'adm-settings-css',
		ADM_URL . 'assets/css/settings.css',
		[ 'wp-color-picker' ],
		ADM_VERSION
	);
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_script(
		'adm-settings-js',
		ADM_URL . 'assets/js/settings.js',
		[ 'wp-color-picker' ],
		ADM_VERSION,
		true
	);

	// Pass default colors, CSS var map and presets to JS.
	wp_localize_script( 'adm-settings-js', 'admData', [
		'defaults' => adm_default_colors(),
		'varMap'   => array_map( fn( $v ) => $v['var'], adm_css_variable_map() ),
		'presets'  => adm_preset_colors(),
	] );
} );

/**
 * Register the settings menu page.
 */
add_action( 'admin_menu', function () {
	add_options_page(
		__( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ),
		__( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ),
		'manage_options',
		'darkadmin',
		'adm_settings_page'
	);
} );
