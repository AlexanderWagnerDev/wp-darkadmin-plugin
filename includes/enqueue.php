<?php
/**
 * Settings registration, asset enqueueing, and admin menu for DarkAdmin.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize callback for boolean settings.
 *
 * Treats only the string '1' and the integer 1 as true, matching the value
 * emitted by WordPress checkbox inputs (value="1"). Everything else is false.
 *
 * @param mixed $v Raw input value.
 * @return bool
 */
function darkadmin_sanitize_bool( $v ): bool {
	return is_scalar( $v ) && 1 === absint( $v );
}

/**
 * Sanitize callback for the allowed-users array setting.
 *
 * @param mixed $v Raw input value.
 * @return int[]
 */
function darkadmin_sanitize_user_ids( $v ): array {
	$values = array_filter( (array) $v, 'is_scalar' );

	return array_values( array_filter( array_map( 'absint', $values ) ) );
}

add_action(
	'admin_init',
	function () {
		register_setting(
			'darkadmin_settings',
			'darkadmin_dark_mode_enabled',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'darkadmin_sanitize_bool',
				'default'           => false,
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_auto_darken',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'darkadmin_sanitize_bool',
				'default'           => false,
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_block_editor',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => 'darkadmin_sanitize_bool',
				'default'           => false,
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_colors',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'darkadmin_sanitize_colors',
				'default'           => darkadmin_preset_colors()['modern'],
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_layout',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'darkadmin_sanitize_layout',
				'default'           => darkadmin_default_layout(),
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_custom_css',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'darkadmin_sanitize_custom_css',
				'default'           => '',
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_allowed_users',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'darkadmin_sanitize_user_ids',
				'default'           => array(),
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_user_access_mode',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'darkadmin_sanitize_user_access_mode',
				'default'           => 'all',
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_preset',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'darkadmin_sanitize_preset',
				'default'           => DARKADMIN_DEFAULT_PRESET,
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_excluded_pages',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'default'           => '',
			)
		);
		register_setting(
			'darkadmin_settings',
			'darkadmin_plugins',
			array(
				'type'              => 'array',
				'sanitize_callback' => 'darkadmin_sanitize_plugins',
				'default'           => array(),
			)
		);
	}
);

/**
 * Sanitize callback for the preset setting.
 *
 * @param mixed $v Raw input value.
 * @return string
 */
function darkadmin_sanitize_preset( $v ): string {
	$value = is_scalar( $v ) ? (string) $v : '';

	return darkadmin_normalize_preset_slug( sanitize_key( $value ) );
}

/**
 * Parses the raw excluded-pages option into a clean list of entries.
 *
 * @param mixed $raw Raw textarea value.
 * @return string[]
 */
function darkadmin_parse_excluded_pages( $raw ): array {
	$raw   = is_string( $raw ) ? $raw : '';
	$lines = explode( "\n", $raw );
	$out   = array();
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
 * Checks whether the current admin page matches any of the excluded entries.
 *
 * @param string[] $entries     Parsed exclusion list.
 * @param string   $pagenow     Current $pagenow value (e.g. 'admin.php').
 * @param string   $hook_suffix Current hook suffix from admin_enqueue_scripts.
 * @param string   $page_slug   Current page slug resolved from get_current_screen().
 * @return bool
 */
function darkadmin_is_page_excluded( array $entries, string $pagenow, string $hook_suffix, string $page_slug = '' ): bool {
	foreach ( $entries as $entry ) {
		if ( str_contains( $entry, '?' ) ) {
			parse_str( (string) wp_parse_url( $entry, PHP_URL_QUERY ), $params );
			$entry_slug = isset( $params['page'] ) ? sanitize_key( $params['page'] ) : '';
			if ( '' !== $entry_slug && $entry_slug === $page_slug ) {
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

/**
 * Resolves the current admin page slug using get_current_screen().
 *
 * Extracts a slug for screens using WordPress' plugin page ID pattern
 * (settings_page_, toplevel_page_, tools_page_, {parent}_page_, ...). For all
 * other screen IDs an empty string is returned so that slug-based exclusion
 * never fires unexpectedly on built-in screens like edit.php (edit_posts -> 'posts').
 *
 * @return string Sanitized page slug, or empty string.
 */
function darkadmin_current_page_slug(): string {
	$screen = get_current_screen();
	if ( ! $screen instanceof WP_Screen ) {
		return '';
	}
	$id = $screen->id;
	// Only match explicit plugin page patterns to avoid false positives.
	$prefixes = array(
		'settings_page_',
		'toplevel_page_',
		'admin_page_',
	);
	foreach ( $prefixes as $prefix ) {
		if ( str_starts_with( $id, $prefix ) ) {
			return sanitize_key( substr( $id, strlen( $prefix ) ) );
		}
	}
	// Generic fallback for other plugin pages (tools_page_*, {parent}_page_*),
	// skipping post-type/taxonomy parent screens (edit-*) to avoid slug collisions.
	$slug = '';
	if ( ! str_starts_with( $id, 'edit-' ) && preg_match( '/(?:^|_)page_(.+)$/', $id, $matches ) ) {
		$slug = sanitize_key( $matches[1] );
	}
	return $slug;
}

// phpcs:disable WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
/**
 * Determines whether DarkAdmin must leave an editor screen untouched.
 *
 * The site editor always keeps its own color scheme, because its canvas is a
 * preview of the theme design. The block post editor (post.php, post-new.php)
 * is only styled when the "Dark Block Editor" option is enabled. The classic
 * editor is always supported by the main stylesheet and must not be excluded
 * merely because it also uses post.php or post-new.php.
 *
 * @param string         $pagenow Current admin filename.
 * @param WP_Screen|null $screen  Current screen, when available.
 * @return bool
 */
function darkadminIsEditorScreenExcluded( string $pagenow, $screen = null ): bool {
	if ( 'site-editor.php' === $pagenow ) {
		return true;
	}

	if ( ! darkadminIsBlockPostEditor( $pagenow, $screen ) ) {
		return false;
	}

	return ! darkadminIsBlockEditorStylingEnabled();
}

/**
 * Whether the current screen is the block editor for a post (post.php, post-new.php).
 *
 * @param string         $pagenow Current admin filename.
 * @param WP_Screen|null $screen  Current screen, when available.
 * @return bool
 */
function darkadminIsBlockPostEditor( string $pagenow, $screen = null ): bool {
	if ( ! in_array( $pagenow, array( 'post.php', 'post-new.php' ), true ) || ! $screen instanceof WP_Screen ) {
		return false;
	}

	return method_exists( $screen, 'is_block_editor' ) && $screen->is_block_editor();
}

/**
 * Whether the "Dark Block Editor" option is enabled.
 *
 * @return bool
 */
function darkadminIsBlockEditorStylingEnabled(): bool {
	return darkadmin_sanitize_bool( get_option( 'darkadmin_block_editor', false ) );
}
// phpcs:enable WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid

add_action(
	'admin_enqueue_scripts',
	function ( string $hook_suffix ) {
		if ( ! darkadmin_is_dark_mode_active() ) {
			return;
		}

		global $pagenow;
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( darkadminIsEditorScreenExcluded( (string) $pagenow, $screen ) ) {
			return;
		}

		if ( darkadmin_is_current_page_user_excluded( (string) $pagenow, $hook_suffix ) ) {
			return;
		}

		$preset   = darkadmin_normalize_preset_slug( darkadmin_read_string_option( 'darkadmin_preset', DARKADMIN_DEFAULT_PRESET ) );
		$css_file = darkadmin_preset_css_file( $preset );

		// darkadmin_preset_fallbacks() and darkadmin_preset_layout_fallbacks() are
		// defined in defaults.php and both delegate to darkadmin_get_preset_fallbacks().
		$fallbacks = darkadmin_preset_fallbacks( $preset );
		$c         = wp_parse_args(
			(array) get_option( 'darkadmin_colors', array() ),
			$fallbacks
		);

		$layout_fallbacks = darkadmin_preset_layout_fallbacks( $preset );
		$l                = wp_parse_args(
			(array) get_option( 'darkadmin_layout', array() ),
			$layout_fallbacks
		);

		$color_hash = substr( md5( wp_json_encode( $c ) . wp_json_encode( $l ) ), 0, 8 );
		$ver        = DARKADMIN_VERSION . '-' . $color_hash;

		$sc = static function ( string $k ) use ( $c, $fallbacks ) {
			return darkadmin_read_color_value( $c[ $k ] ?? null, $fallbacks[ $k ] );
		};
		$sl = static function ( string $k ) use ( $l, $layout_fallbacks ) {
			return darkadmin_read_layout_value( $l[ $k ] ?? null, $layout_fallbacks[ $k ] );
		};

		$vars = ':root{'
			. "--adm-bg:{$sc('bg')};"
			. "--adm-bg-bar:{$sc('bg_bar')};"
			. "--adm-bg-deep:{$sc('bg_deep')};"
			. "--adm-bg-darker:{$sc('bg_darker')};"
			. "--adm-surface-1:{$sc('surface1')};"
			. "--adm-surface-2:{$sc('surface2')};"
			. "--adm-surface-3:{$sc('surface3')};"
			. "--adm-table-alt:{$sc('table_alt')};"
			. "--adm-plugin-inactive:{$sc('plugin_inactive')};"
			. "--adm-border:{$sc('border')};"
			. "--adm-border-focus:{$sc('border_focus')};"
			. "--adm-border-hover:{$sc('border_hover')};"
			. "--adm-text:{$sc('text')};"
			. "--adm-text-muted:{$sc('text_muted')};"
			. "--adm-text-soft:{$sc('text_soft')};"
			. "--adm-text-on-primary:{$sc('text_on_primary')};"
			. "--adm-link:{$sc('link')};"
			. "--adm-link-hover:{$sc('link_hover')};"
			. "--adm-primary:{$sc('primary')};"
			. "--adm-primary-hover:{$sc('primary_hover')};"
			. "--adm-success:{$sc('success')};"
			. "--adm-warning:{$sc('warning')};"
			. "--adm-danger:{$sc('danger')};"
			. "--adm-sidebar-bg:{$sc('sidebar_bg')};"
			. "--adm-sidebar-active:{$sc('sidebar_active')};"
			. "--adm-sidebar-text:{$sc('sidebar_text')};"
			. "--adm-cm-keyword:{$sc('cm_keyword')};"
			. "--adm-cm-operator:{$sc('cm_operator')};"
			. "--adm-cm-variable2:{$sc('cm_variable2')};"
			. "--adm-cm-property:{$sc('cm_property')};"
			. "--adm-cm-number:{$sc('cm_number')};"
			. "--adm-cm-string:{$sc('cm_string')};"
			. "--adm-cm-string2:{$sc('cm_string2')};"
			. "--adm-cm-comment:{$sc('cm_comment')};"
			. "--adm-cm-tag:{$sc('cm_tag')};"
			. "--adm-cm-attribute:{$sc('cm_attribute')};"
			. "--adm-cm-bracket:{$sc('cm_bracket')};"
			. "--adm-space-2:{$sl('space_2')};"
			. "--adm-space-3:{$sl('space_3')};"
			. "--adm-btn-h:{$sl('btn_h')};"
			. "--adm-input-h:{$sl('input_h')};"
			. "--adm-radius-sm:{$sl('radius_sm')};"
			. "--adm-radius-md:{$sl('radius_md')};"
			. "--adm-radius-lg:{$sl('radius_lg')};"
			. "--adm-shadow-md:{$sl('shadow_md')};"
			. '}';

		wp_enqueue_style(
			'darkadmin-darkmode',
			DARKADMIN_URL . 'assets/css/' . $css_file,
			array(),
			$ver
		);

		// All values in $vars are already sanitized at the source:
		// $sc() runs sanitize_hex_color(), $sl() runs sanitize_text_field()
		// plus numeric cast / regex whitelist for shadow. No further wrapping needed.
		wp_add_inline_style( 'darkadmin-darkmode', $vars );

		// Sanitize again on read: options can be written outside the Settings API
		// (WP-CLI, importers, hooks before register_setting()), so the output path
		// must not rely on the save-time callback alone.
		$custom = darkadmin_sanitize_custom_css( darkadmin_read_string_option( 'darkadmin_custom_css' ) );
		if ( '' !== $custom ) {
			wp_add_inline_style( 'darkadmin-darkmode', $custom );
		}

		if ( get_option( 'darkadmin_auto_darken', false ) ) {
			wp_enqueue_script(
				'darkadmin-auto-darken',
				DARKADMIN_URL . 'assets/js/auto-darken.js',
				array(),
				DARKADMIN_VERSION,
				array(
					'in_footer' => true,
					'strategy'  => 'defer',
				)
			);
		}

		darkadmin_enqueue_plugin_styles();
		darkadmin_enqueue_boot_wpds_styles();
		darkadmin_enqueue_wordpress_ai_styles();

		if ( darkadminIsBlockPostEditor( (string) $pagenow, $screen ) ) {
			darkadmin_enqueue_block_editor_styles();
		}
	}
);

/**
 * Whether the user's "Excluded Pages" list matches the current admin page.
 *
 * @param string $pagenow     Current $pagenow value (e.g. 'admin.php').
 * @param string $hook_suffix Current admin page hook suffix.
 * @return bool
 */
function darkadmin_is_current_page_user_excluded( string $pagenow, string $hook_suffix ): bool {
	$raw_exclusions = darkadmin_read_string_option( 'darkadmin_excluded_pages' );
	if ( '' === $raw_exclusions ) {
		return false;
	}

	return darkadmin_is_page_excluded(
		darkadmin_parse_excluded_pages( $raw_exclusions ),
		$pagenow,
		$hook_suffix,
		darkadmin_current_page_slug()
	);
}

/**
 * Enqueues the dark styles for the block editor interface (header, sidebars,
 * inserter, popovers, modals). The editor canvas is styled separately through
 * darkadmin_block_editor_canvas_settings(), because it renders in an iframe.
 *
 * @return void
 */
function darkadmin_enqueue_block_editor_styles(): void {
	darkadmin_enqueue_boot_wpds_styles( true );

	wp_enqueue_style(
		'darkadmin-block-editor',
		DARKADMIN_URL . 'assets/css/darkadmin-block-editor.css',
		array( 'darkadmin-darkmode', 'darkadmin-wpds', 'wp-components', 'wp-block-editor', 'wp-editor', 'wp-edit-post' ),
		DARKADMIN_VERSION
	);
}

/**
 * Builds the dark stylesheet for the block editor canvas.
 *
 * The canvas is an iframe that does not inherit the admin :root variables, so
 * the resolved color values are written directly into the CSS. Selectors are
 * relative to the canvas body; the editor scopes them to .editor-styles-wrapper.
 *
 * @return string
 */
function darkadmin_block_editor_canvas_css(): string {
	$preset    = darkadmin_normalize_preset_slug( darkadmin_read_string_option( 'darkadmin_preset', DARKADMIN_DEFAULT_PRESET ) );
	$fallbacks = darkadmin_preset_fallbacks( $preset );
	$c         = wp_parse_args( (array) get_option( 'darkadmin_colors', array() ), $fallbacks );

	$sc = static function ( string $k ) use ( $c, $fallbacks ) {
		return darkadmin_read_color_value( $c[ $k ] ?? null, $fallbacks[ $k ] );
	};

	// All values come from darkadmin_read_color_value(), which runs sanitize_hex_color().
	// Block themes keep their global styles in the canvas even with "Use theme
	// styles" off, so body colors use !important and element rules are prefixed
	// with :root to outrank the theme's :root :where() rules. Colors chosen per
	// block (preset classes with !important, inline styles) still take priority.
	return 'body{'
		. "background-color:{$sc('bg')} !important;"
		. "color:{$sc('text')} !important;"
		. '}'
		. ':root :is(h1,h2,h3,h4,h5,h6){color:inherit;}'
		. ":root a:where(:not(.wp-element-button)){color:{$sc('link')};}"
		. ":root a:where(:not(.wp-element-button)):hover{color:{$sc('link_hover')};}"
		. ":root :is(code,kbd,pre,.wp-block-code){background-color:{$sc('surface1')};color:inherit;}"
		. ":root :is(figcaption,.wp-element-caption){color:{$sc('text_muted')};}"
		. ":root :is(hr,.wp-block-separator){border-color:{$sc('border')};}"
		. ":root ::selection{background-color:{$sc('primary')};color:{$sc('text_on_primary')};}"
		. ":root [data-rich-text-placeholder]::after{color:{$sc('text_muted')};opacity:1;}"
		. ":root :is(.components-placeholder,.block-editor-media-placeholder){background-color:{$sc('surface1')};color:{$sc('text')};box-shadow:inset 0 0 0 1px {$sc('border')};}"
		. ":root .components-placeholder :is(.components-placeholder__label,.components-placeholder__instructions){color:{$sc('text')};}";
}

/**
 * Adds the dark canvas stylesheet to the block editor's default editor styles.
 *
 * The block editor uses "defaultEditorStyles" when "Use theme styles" is turned
 * off or when the theme has no editor styles of its own. Themes that style the
 * editor keep their WYSIWYG preview until the user turns their styles off.
 *
 * @param array<string, mixed>    $settings Block editor settings.
 * @param WP_Block_Editor_Context $context  Block editor context.
 * @return array<string, mixed>
 */
function darkadmin_block_editor_canvas_settings( $settings, $context ) {
	if ( ! is_array( $settings ) || ! $context instanceof WP_Block_Editor_Context || 'core/edit-post' !== $context->name ) {
		return $settings;
	}

	if ( ! is_admin() || ! darkadmin_is_dark_mode_active() || ! darkadminIsBlockEditorStylingEnabled() ) {
		return $settings;
	}

	global $pagenow, $hook_suffix;
	$page = is_string( $pagenow ) ? $pagenow : '';
	if ( darkadmin_is_current_page_user_excluded( $page, is_string( $hook_suffix ) ? $hook_suffix : $page ) ) {
		return $settings;
	}

	$styles = isset( $settings['defaultEditorStyles'] ) && is_array( $settings['defaultEditorStyles'] ) ? $settings['defaultEditorStyles'] : array();

	$styles[] = array( 'css' => darkadmin_block_editor_canvas_css() );

	$settings['defaultEditorStyles'] = $styles;

	return $settings;
}
add_filter( 'block_editor_settings_all', 'darkadmin_block_editor_canvas_settings', 10, 2 );

add_action(
	'admin_enqueue_scripts',
	function ( string $hook_suffix ) {
		if ( 'settings_page_darkadmin' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'darkadmin-settings-css',
			DARKADMIN_URL . 'assets/css/settings.css',
			array( 'wp-color-picker' ),
			DARKADMIN_VERSION
		);
		wp_enqueue_script(
			'darkadmin-settings-js',
			DARKADMIN_URL . 'assets/js/settings.js',
			array( 'jquery', 'wp-color-picker' ),
			DARKADMIN_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_localize_script(
			'darkadmin-settings-js',
			'darkadminData',
			array(
				'defaults'       => darkadmin_preset_colors()['modern'],
				'varMap'         => array_map( fn( $v ) => $v['var'], darkadmin_css_variable_map() ),
				'presets'        => darkadmin_preset_colors(),
				'layoutDefaults' => darkadmin_default_layout(),
				'layoutPresets'  => darkadmin_preset_layout(),
			)
		);
		wp_localize_script(
			'darkadmin-settings-js',
			'darkadminI18n',
			array(
				'active'     => __( '✓ Active', 'darkadmin-dark-mode-for-adminpanel' ),
				'loadPreset' => __( 'Load Preset', 'darkadmin-dark-mode-for-adminpanel' ),
				'copied'     => __( 'Copied!', 'darkadmin-dark-mode-for-adminpanel' ),
				'importOk'   => __( 'Palette imported successfully.', 'darkadmin-dark-mode-for-adminpanel' ),
				'importErr'  => __( 'Invalid JSON or color value.', 'darkadmin-dark-mode-for-adminpanel' ),
			)
		);

		// Only mark the body as dark when the dark stylesheet and its :root
		// variables were actually enqueued for this page. Check the direct queue
		// rather than wp_style_is(), because a dependent stylesheet can keep a
		// manually dequeued handle "enqueued" through dependency traversal.
		if ( in_array( 'darkadmin-darkmode', wp_styles()->queue, true ) ) {
			wp_add_inline_script( 'darkadmin-settings-js', 'document.body.classList.add("adm-dark-active");', 'before' );
		}
	}
);

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ),
			__( 'DarkAdmin', 'darkadmin-dark-mode-for-adminpanel' ),
			'manage_options',
			'darkadmin',
			'darkadmin_settings_page'
		);
	}
);
