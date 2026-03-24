<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

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
	register_setting( 'darkadmin_settings', 'darkadmin_layout', [
		'type'              => 'array',
		'sanitize_callback' => 'darkadmin_sanitize_layout',
		'default'           => darkadmin_default_layout(),
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

function darkadmin_preset_fallbacks( string $preset ): array {
	$presets = darkadmin_preset_colors();
	return $presets[ $preset ] ?? $presets['default'];
}

function darkadmin_preset_layout_fallbacks( string $preset ): array {
	$presets = darkadmin_preset_layout();
	return $presets[ $preset ] ?? $presets['default'];
}

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

function darkadmin_is_page_excluded( array $entries, string $pagenow, string $hook_suffix ): bool {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$current_page_slug = isset( $_GET['page'] ) ? sanitize_key( $_GET['page'] ) : '';

	foreach ( $entries as $entry ) {
		if ( str_contains( $entry, '?' ) ) {
			parse_str( (string) wp_parse_url( $entry, PHP_URL_QUERY ), $params );
			$entry_slug = isset( $params['page'] ) ? sanitize_key( $params['page'] ) : '';
			if ( '' !== $entry_slug && $entry_slug === $current_page_slug ) {
				return true;
			}
			continue;
		}
		if ( $entry === $pagenow || $entry === $hook_suffix ) {
			return true;
		}
	}
	return false;
}

add_action( 'admin_enqueue_scripts', function ( string $hook_suffix ) {
	if ( ! darkadmin_is_dark_mode_active() ) {
		return;
	}

	global $pagenow;
	$excluded = [ 'site-editor.php', 'post-new.php', 'post.php' ];
	if ( in_array( $pagenow, $excluded, true ) ) {
		return;
	}

	$raw_exclusions = get_option( 'darkadmin_excluded_pages', '' );
	if ( '' !== $raw_exclusions ) {
		$user_excluded = darkadmin_parse_excluded_pages( $raw_exclusions );
		if ( darkadmin_is_page_excluded( $user_excluded, $pagenow, $hook_suffix ) ) {
			return;
		}
	}

	$preset   = get_option( 'darkadmin_preset', 'default' );
	$css_file = darkadmin_preset_css_file( $preset );

	$fallbacks = darkadmin_preset_fallbacks( $preset );
	$c         = wp_parse_args(
		(array) get_option( 'darkadmin_colors', [] ),
		$fallbacks
	);

	$layout_fallbacks = darkadmin_preset_layout_fallbacks( $preset );
	$l                = wp_parse_args(
		(array) get_option( 'darkadmin_layout', [] ),
		$layout_fallbacks
	);

	$color_hash = substr( md5( wp_json_encode( $c ) . wp_json_encode( $l ) ), 0, 8 );
	$ver        = DARKADMIN_VERSION . '-' . $color_hash;

	$sc = static fn( string $k ) => sanitize_hex_color( $c[ $k ] ?? '' ) ?: $fallbacks[ $k ];
	$sl = static fn( string $k ) => sanitize_text_field( $l[ $k ] ?? $layout_fallbacks[ $k ] );

	$vars = ':root{'                                                   .
		"--adm-bg:{$sc('bg')};"                                    .
		"--adm-bg-bar:{$sc('bg_bar')};"                            .
		"--adm-bg-deep:{$sc('bg_deep')};"                          .
		"--adm-bg-darker:{$sc('bg_darker')};"                      .
		"--adm-surface-1:{$sc('surface1')};"                       .
		"--adm-surface-2:{$sc('surface2')};"                       .
		"--adm-surface-3:{$sc('surface3')};"                       .
		"--adm-table-alt:{$sc('table_alt')};"                      .
		"--adm-plugin-inactive:{$sc('plugin_inactive')};"          .
		"--adm-border:{$sc('border')};"                            .
		"--adm-border-focus:{$sc('border_focus')};"                .
		"--adm-border-hover:{$sc('border_hover')};"                .
		"--adm-text:{$sc('text')};"                                .
		"--adm-text-muted:{$sc('text_muted')};"                    .
		"--adm-text-soft:{$sc('text_soft')};"                      .
		"--adm-text-on-primary:{$sc('text_on_primary')};"          .
		"--adm-link:{$sc('link')};"                                .
		"--adm-link-hover:{$sc('link_hover')};"                    .
		"--adm-primary:{$sc('primary')};"                          .
		"--adm-primary-hover:{$sc('primary_hover')};"              .
		"--adm-success:{$sc('success')};"                          .
		"--adm-warning:{$sc('warning')};"                          .
		"--adm-danger:{$sc('danger')};"                            .
		"--adm-sidebar-bg:{$sc('sidebar_bg')};"                    .
		"--adm-sidebar-active:{$sc('sidebar_active')};"            .
		"--adm-sidebar-text:{$sc('sidebar_text')};"                .
		"--adm-cm-keyword:{$sc('cm_keyword')};"                    .
		"--adm-cm-operator:{$sc('cm_operator')};"                  .
		"--adm-cm-variable2:{$sc('cm_variable2')};"                .
		"--adm-cm-property:{$sc('cm_property')};"                  .
		"--adm-cm-number:{$sc('cm_number')};"                      .
		"--adm-cm-string:{$sc('cm_string')};"                      .
		"--adm-cm-string2:{$sc('cm_string2')};"                    .
		"--adm-cm-comment:{$sc('cm_comment')};"                    .
		"--adm-cm-tag:{$sc('cm_tag')};"                            .
		"--adm-cm-attribute:{$sc('cm_attribute')};"                .
		"--adm-cm-bracket:{$sc('cm_bracket')};"                    .
		"--adm-space-2:{$sl('space_2')};"                          .
		"--adm-space-3:{$sl('space_3')};"                          .
		"--adm-btn-h:{$sl('btn_h')};"                              .
		"--adm-input-h:{$sl('input_h')};"                          .
		"--adm-radius-sm:{$sl('radius_sm')};"                      .
		"--adm-radius-md:{$sl('radius_md')};"                      .
		"--adm-radius-lg:{$sl('radius_lg')};"                      .
		"--adm-shadow-md:{$sl('shadow_md')};"                      .
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
		wp_add_inline_style( 'darkadmin-darkmode', $custom );
	}

	if ( get_option( 'darkadmin_auto_darken', false ) ) {
		wp_enqueue_script(
			'darkadmin-auto-darken',
			DARKADMIN_URL . 'assets/js/auto-darken.js',
			[],
			DARKADMIN_VERSION,
			true
		);
	}
} );

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

	wp_localize_script( 'darkadmin-settings-js', 'admData', [
		'defaults'       => darkadmin_default_colors(),
		'varMap'         => array_map( fn( $v ) => $v['var'], darkadmin_css_variable_map() ),
		'presets'        => darkadmin_preset_colors(),
		'layoutDefaults' => darkadmin_default_layout(),
		'layoutPresets'  => darkadmin_preset_layout(),
	] );
	wp_localize_script( 'darkadmin-settings-js', 'admI18n', [
		'active'     => __( 'Active', 'darkadmin-dark-mode-for-adminpanel' ),
		'loadPreset' => __( 'Load Preset', 'darkadmin-dark-mode-for-adminpanel' ),
	] );
} );

add_action( 'admin_menu', function () {
	add_options_page(
		__( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ),
		__( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ),
		'manage_options',
		'darkadmin',
		'darkadmin_settings_page'
	);
} );
