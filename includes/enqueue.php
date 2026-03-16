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
 * Enqueue dark mode CSS and inject color token overrides as inline CSS.
 * Uses a hash of the stored color values as the cache-busting version.
 */
add_action( 'admin_enqueue_scripts', function () {
	if ( ! adm_is_dark_mode_active() ) {
		return;
	}

	$c = wp_parse_args(
		(array) get_option( 'adm_colors', [] ),
		adm_default_colors()
	);

	$preset  = get_option( 'adm_preset', 'default' );
	$css_file = adm_preset_css_file( $preset );

	// Cache-busting: combine plugin version + hash of current color values.
	$color_hash = substr( md5( serialize( $c ) ), 0, 8 );
	$ver        = ADM_VERSION . '-' . $color_hash;

	$sc = static fn( string $k, string $fb ) => sanitize_hex_color( $c[ $k ] ?? '' ) ?: $fb;

	// Fallback values match the Modern preset (WP 7.0 design language).
	$vars = ':root{'                                                          .
		"--adm-bg:{$sc('bg','#1e1e1e')};"                                 .
		"--adm-bg-bar:{$sc('bg_bar','#0c0c0c')};"                         .
		"--adm-bg-deep:{$sc('bg_deep','#0c0c0c')};"                       .
		"--adm-bg-darker:{$sc('bg_darker','#080808')};"                   .
		"--adm-surface-1:{$sc('surface1','#2a2a2a')};"                    .
		"--adm-surface-2:{$sc('surface2','#333333')};"                    .
		"--adm-surface-3:{$sc('surface3','#3d3d3d')};"                    .
		"--adm-table-alt:{$sc('table_alt','#242424')};"                   .
		"--adm-plugin-inactive:{$sc('plugin_inactive','#202020')};"       .
		"--adm-border:{$sc('border','#3a3a3a')};"                         .
		"--adm-border-focus:{$sc('border_focus','#3858e9')};"             .
		"--adm-border-hover:{$sc('border_hover','#6b7aee')};"             .
		"--adm-text:{$sc('text','#f0f0f0')};"                             .
		"--adm-text-muted:{$sc('text_muted','#a0a0a0')};"                 .
		"--adm-text-soft:{$sc('text_soft','#666666')};"                   .
		"--adm-text-on-primary:{$sc('text_on_primary','#ffffff')};"       .
		"--adm-link:{$sc('link','#7b96f5')};"                             .
		"--adm-link-hover:{$sc('link_hover','#a5b8fa')};"                 .
		"--adm-primary:{$sc('primary','#3858e9')};"                       .
		"--adm-primary-hover:{$sc('primary_hover','#2145d4')};"           .
		"--adm-success:{$sc('success','#00ba37')};"                       .
		"--adm-warning:{$sc('warning','#dba617')};"                       .
		"--adm-danger:{$sc('danger','#d63638')};"                         .
		"--adm-sidebar-bg:#1e1e1e;"                                        .
		"--adm-sidebar-active:#3858e9;"                                    .
		"--adm-sidebar-text:#c8c8c8;"                                      .
		"--adm-cm-keyword:{$sc('cm_keyword','#c792ea')};"                 .
		"--adm-cm-operator:{$sc('cm_operator','#89ddff')};"               .
		"--adm-cm-variable2:{$sc('cm_variable2','#82aaff')};"             .
		"--adm-cm-property:{$sc('cm_property','#b2ccd6')};"               .
		"--adm-cm-number:{$sc('cm_number','#f78c6c')};"                   .
		"--adm-cm-string:{$sc('cm_string','#c3e88d')};"                   .
		"--adm-cm-string2:{$sc('cm_string2','#f07178')};"                 .
		"--adm-cm-comment:{$sc('cm_comment','#546e7a')};"                 .
		"--adm-cm-tag:{$sc('cm_tag','#f07178')};"                         .
		"--adm-cm-attribute:{$sc('cm_attribute','#ffcb6b')};"             .
		"--adm-cm-bracket:{$sc('cm_bracket','#89ddff')};"                 .
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
