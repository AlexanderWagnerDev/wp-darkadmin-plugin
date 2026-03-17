<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register plugin settings.
 */
add_action( 'admin_init', function () {
	register_setting( 'darkadmin_settings', 'darkadmin_dark_mode_enabled', [
		'type'              => 'boolean',
		'sanitize_callback' => fn( $v ) => (bool) $v,
		'default'           => false,
	] );
	register_setting( 'darkadmin_settings', 'darkadmin_auto_darken', [
		'type'              => 'boolean',
		'sanitize_callback' => fn( $v ) => (bool) $v,
		'default'           => false,
	] );
	register_setting( 'darkadmin_settings', 'darkadmin_colors', [
		'type'              => 'array',
		'sanitize_callback' => 'darkadmin_sanitize_colors',
		'default'           => darkadmin_default_colors(),
	] );
	register_setting( 'darkadmin_settings', 'darkadmin_custom_css', [
		'type'              => 'string',
		'sanitize_callback' => 'darkadmin_sanitize_custom_css',
		'default'           => '',
	] );
	register_setting( 'darkadmin_settings', 'darkadmin_allowed_users', [
		'type'              => 'array',
		'sanitize_callback' => fn( $v ) => array_map( 'absint', (array) $v ),
		'default'           => [],
	] );
	register_setting( 'darkadmin_settings', 'darkadmin_user_access_mode', [
		'type'              => 'string',
		'sanitize_callback' => 'darkadmin_sanitize_user_access_mode',
		'default'           => 'all',
	] );
	register_setting( 'darkadmin_settings', 'darkadmin_preset', [
		'type'              => 'string',
		'sanitize_callback' => function ( $v ) {
			$allowed = array_keys( darkadmin_preset_colors() );
			return in_array( $v, $allowed, true ) ? $v : 'default';
		},
		'default'           => 'default',
	] );
	register_setting( 'darkadmin_settings', 'darkadmin_excluded_pages', [
		'type'              => 'string',
		'sanitize_callback' => 'sanitize_textarea_field',
		'default'           => '',
	] );
} );

/**
 * Returns the base fallback colors for a given preset.
 * Used when the user has not customized a specific token.
 * Each preset defines its own baseline so the inline style block
 * always reflects the correct starting point.
 *
 * IMPORTANT: These values must be identical to darkadmin_preset_colors()
 * in defaults.php. Keep both in sync when adding or changing presets.
 *
 * @param string $preset Preset key ('default', 'modern', ...).
 * @return array<string,string> Map of color key => hex value.
 */
function darkadmin_preset_fallbacks( string $preset ): array {
	// Delegate directly to darkadmin_preset_colors() so there is a single
	// source of truth and both arrays can never drift apart.
	$presets = darkadmin_preset_colors();
	return $presets[ $preset ] ?? $presets['default'];
}

/**
 * Parse the darkadmin_excluded_pages textarea value into a clean array.
 *
 * Rules:
 *   - One entry per line.
 *   - Lines starting with # are treated as comments and ignored.
 *   - Blank / whitespace-only lines are ignored.
 *   - Values are trimmed; duplicates are removed.
 *   - Comparison is intentionally case-sensitive (WordPress slugs are exact).
 *
 * @param string $raw Raw option value from the DB.
 * @return string[] Deduplicated list of exclusion entries.
 */
function darkadmin_parse_excluded_pages( string $raw ): array {
	$lines = explode( "\n", $raw );
	$out   = [];
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( '' === $line || str_starts_with( $line, '#' ) ) {
			continue;
		}
		$out[] = $line;
	}
	return array_unique( $out );
}

/**
 * Check whether the current admin page matches a user-supplied exclusion entry.
 *
 * Supported formats:
 *   - Plain filename : plugins.php, edit.php
 *   - Query-string  : admin.php?page=woocommerce  (page param compared exactly)
 *
 * @param string[] $entries      Parsed exclusion list from darkadmin_parse_excluded_pages().
 * @param string   $pagenow      Global $pagenow value.
 * @param string   $hook_suffix  Hook suffix passed to admin_enqueue_scripts.
 * @return bool True when the current page should be excluded.
 */
function darkadmin_is_page_excluded( array $entries, string $pagenow, string $hook_suffix ): bool {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current_page_slug = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';

	foreach ( $entries as $entry ) {
		// Entry contains a query string — compare only the page= parameter.
		if ( str_contains( $entry, '?' ) ) {
			parse_str( (string) wp_parse_url( $entry, PHP_URL_QUERY ), $params );
			$entry_slug = isset( $params['page'] ) ? sanitize_key( $params['page'] ) : '';
			if ( '' !== $entry_slug && $entry_slug === $current_page_slug ) {
				return true;
			}
			continue;
		}
		// Plain filename — match against pagenow or hook_suffix.
		if ( $entry === $pagenow || $entry === $hook_suffix ) {
			return true;
		}
	}
	return false;
}

/**
 * Enqueue dark mode CSS and inject color token overrides as inline CSS.
 * Fallback colors are chosen based on the active preset so each preset
 * starts from its own baseline before any user customization is applied.
 *
 * Hard-coded excluded pages:
 *   - site-editor.php  (Full Site Editor)
 *   - post-new.php     (Block Editor / new post)
 *   - post.php         (Block Editor / existing post)
 * All three ship their own color scheme and conflict with the dark mode styles.
 *
 * Additional pages can be excluded via Settings > DarkAdmin > Excluded Pages.
 */
add_action( 'admin_enqueue_scripts', function ( string $hook_suffix ) {
	if ( ! darkadmin_is_dark_mode_active() ) {
		return;
	}

	// Hard-coded excludes.
	global $pagenow;
	$excluded = [ 'site-editor.php', 'post-new.php', 'post.php' ];
	if ( in_array( $pagenow, $excluded, true ) ) {
		return;
	}

	// User-defined excludes.
	$raw_exclusions = get_option( 'darkadmin_excluded_pages', '' );
	if ( '' !== $raw_exclusions ) {
		$user_excluded = darkadmin_parse_excluded_pages( $raw_exclusions );
		if ( darkadmin_is_page_excluded( $user_excluded, $pagenow, $hook_suffix ) ) {
			return;
		}
	}

	$preset   = get_option( 'darkadmin_preset', 'default' );
	$css_file = darkadmin_preset_css_file( $preset );

	// Merge user-saved colors on top of the preset-specific fallbacks.
	$fallbacks = darkadmin_preset_fallbacks( $preset );
	$c         = wp_parse_args(
		(array) get_option( 'darkadmin_colors', [] ),
		$fallbacks
	);

	// Cache-busting: combine plugin version + hash of current color values.
	$color_hash = substr( md5( wp_json_encode( $c ) ), 0, 8 );
	$ver        = DARKADMIN_VERSION . '-' . $color_hash;

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
		'darkadmin-darkmode',
		DARKADMIN_URL . 'assets/css/' . $css_file,
		[],
		$ver
	);
	wp_add_inline_style( 'darkadmin-darkmode', $vars );

	$custom = get_option( 'darkadmin_custom_css', '' );
	if ( ! empty( $custom ) ) {
		// Custom CSS is already sanitized on save via darkadmin_sanitize_custom_css().
		wp_add_inline_style( 'darkadmin-darkmode', $custom );
	}

	if ( get_option( 'darkadmin_auto_darken', false ) ) {
		wp_enqueue_script(
			'darkadmin-auto-darken',
			DARKADMIN_URL . 'assets/js/auto-darken.js',
			[],
			DARKADMIN_VERSION,
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
		'darkadmin-settings-css',
		DARKADMIN_URL . 'assets/css/settings.css',
		[ 'wp-color-picker' ],
		DARKADMIN_VERSION
	);
	wp_enqueue_script(
		'darkadmin-settings-js',
		DARKADMIN_URL . 'assets/js/settings.js',
		[ 'jquery', 'wp-color-picker' ],
		DARKADMIN_VERSION,
		true
	);

	// Pass default colors, CSS var map, presets and i18n strings to JS.
	wp_localize_script( 'darkadmin-settings-js', 'admData', [
		'defaults' => darkadmin_default_colors(),
		'varMap'   => array_map( fn( $v ) => $v['var'], darkadmin_css_variable_map() ),
		'presets'  => darkadmin_preset_colors(),
	] );
	wp_localize_script( 'darkadmin-settings-js', 'admI18n', [
		'active'     => __( '✓ Active', 'darkadmin-dark-mode-for-adminpanel' ),
		'loadPreset' => __( 'Load Preset', 'darkadmin-dark-mode-for-adminpanel' ),
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
		'darkadmin_settings_page'
	);
} );
