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
	register_setting( 'adm_settings', 'adm_user_access_mode', [
		'type'              => 'string',
		'sanitize_callback' => 'adm_sanitize_user_access_mode',
		'default'           => 'all',
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
 * Used when the user has not customized a specific token.
 * Each preset defines its own baseline so the inline style block
 * always reflects the correct starting point.
 *
 * IMPORTANT: These values must be identical to adm_preset_colors()
 * in defaults.php. Keep both in sync when adding or changing presets.
 *
 * @param string $preset Preset key ('default', 'modern', ...).
 * @return array<string,string> Map of color key => hex value.
 */
function adm_preset_fallbacks( string $preset ): array {
	// Delegate directly to adm_preset_colors() so there is a single
	// source of truth and both arrays can never drift apart.
	$presets = adm_preset_colors();
	return $presets[ $preset ] ?? $presets['default'];
}

/**
 * Enqueue dark mode CSS and inject color token overrides as inline CSS.
 * Fallback colors are chosen based on the active preset so each preset
 * starts from its own baseline before any user customization is applied.
 *
 * Excluded pages:
 *   - site-editor.php  (Full Site Editor)
 *   - post-new.php     (Block Editor / new post)
 *   - post.php         (Block Editor / existing post)
 * All three ship their own color scheme and conflict with the dark mode styles.
 */
add_action( 'admin_enqueue_scripts', function () {
	if ( ! adm_is_dark_mode_active() ) {
		return;
	}

	// Exclude specific admin pages.
	global $pagenow;
	$excluded = [ 'site-editor.php', 'post-new.php', 'post.php' ];
	if ( in_array( $pagenow, $excluded, true ) ) {
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
	$color_hash = substr( md5( wp_json_encode( $c ) ), 0, 8 );
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
		// Custom CSS is already sanitized on save via adm_sanitize_custom_css().
		wp_add_inline_style( 'adm-darkmode', $custom );
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
