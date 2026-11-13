<?php
/**
 * Excluding whole plugins from dark mode.
 *
 * Instead of listing every admin.php?page=... URL by hand, the user picks a
 * plugin and DarkAdmin finds its admin pages on its own: every page added with
 * add_menu_page()/add_submenu_page() has a page hook whose callback lives in
 * the file of the plugin that registered it. Subpages also belong to the
 * plugin that owns their top-level menu, so add-ons that hook into another
 * plugin's menu (for example MainWP extensions) are covered by excluding the
 * main plugin.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sanitize callback for the excluded plugins setting.
 *
 * Keeps only plugin basenames (folder/file.php or file.php) of installed plugins.
 *
 * @param mixed $value Raw input value.
 * @return string[]
 */
function darkadmin_sanitize_plugin_exclusions( $value ): array {
	$installed = darkadmin_installed_plugin_basenames();
	$out       = array();

	foreach ( (array) $value as $basename ) {
		if ( ! is_string( $basename ) ) {
			continue;
		}
		$basename = wp_unslash( trim( $basename ) );
		if ( in_array( $basename, $installed, true ) ) {
			$out[] = $basename;
		}
	}

	return array_values( array_unique( $out ) );
}

/**
 * Basenames of all installed plugins.
 *
 * @return string[]
 */
function darkadmin_installed_plugin_basenames(): array {
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	return array_map( 'strval', array_keys( get_plugins() ) );
}

/**
 * Basenames of the plugins excluded from dark mode.
 *
 * Read without the save-time sanitizer, so stored values are filtered again.
 *
 * @return string[]
 */
function darkadmin_excluded_plugins(): array {
	$value = get_option( 'darkadmin_plugin_exclusions', array() );

	return array_values( array_filter( (array) $value, 'is_string' ) );
}

/**
 * Basenames of all active plugins, including network-activated ones.
 *
 * @return string[]
 */
function darkadmin_active_plugin_basenames(): array {
	$active = array_filter( (array) get_option( 'active_plugins', array() ), 'is_string' );

	if ( is_multisite() ) {
		$network = array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) );
		$active  = array_merge( $active, array_filter( $network, 'is_string' ) );
	}

	return array_values( array_unique( $active ) );
}

/**
 * Maps a PHP file to the basename of the active plugin it belongs to.
 *
 * @param string   $file   Absolute path of a PHP file.
 * @param string[] $active Active plugin basenames.
 * @return string Plugin basename, or empty string when the file is not part of an active plugin.
 */
function darkadmin_plugin_for_file( string $file, array $active ): string {
	if ( '' === $file ) {
		return '';
	}

	// plugin_basename() resolves symlinked plugins and strips the plugins directory.
	$relative = plugin_basename( $file );
	if ( $relative === $file || str_starts_with( $relative, '/' ) ) {
		return '';
	}

	foreach ( $active as $basename ) {
		$dir = dirname( $basename );
		if ( '.' === $dir ? $relative === $basename : str_starts_with( $relative, $dir . '/' ) ) {
			return $basename;
		}
	}

	return '';
}

/**
 * Returns the file a hook callback is defined in.
 *
 * @param mixed $callback Hook callback as stored in WP_Hook.
 * @return string Absolute file path, or empty string when it cannot be resolved.
 */
function darkadmin_callback_file( $callback ): string {
	try {
		if ( is_string( $callback ) && str_contains( $callback, '::' ) ) {
			$callback = explode( '::', $callback, 2 );
		}

		if ( is_array( $callback ) && 2 === count( $callback ) && is_string( $callback[1] ) && ( is_object( $callback[0] ) || is_string( $callback[0] ) ) ) {
			$reflection = new ReflectionMethod( $callback[0], $callback[1] );
		} elseif ( $callback instanceof Closure || ( is_string( $callback ) && function_exists( $callback ) ) ) {
			$reflection = new ReflectionFunction( $callback );
		} elseif ( is_object( $callback ) && method_exists( $callback, '__invoke' ) ) {
			$reflection = new ReflectionMethod( $callback, '__invoke' );
		} else {
			return '';
		}
	} catch ( ReflectionException $e ) {
		return '';
	}

	$file = $reflection->getFileName();

	return is_string( $file ) ? wp_normalize_path( $file ) : '';
}

/**
 * Plugins that registered callbacks on an admin page hook.
 *
 * @param string   $hookname Page hook name, e.g. toplevel_page_mainwp_tab.
 * @param string[] $active   Active plugin basenames.
 * @return string[]
 */
function darkadmin_page_hook_owners( string $hookname, array $active ): array {
	global $wp_filter;

	if ( '' === $hookname || ! isset( $wp_filter[ $hookname ] ) || ! $wp_filter[ $hookname ] instanceof WP_Hook ) {
		return array();
	}

	$owners = array();
	foreach ( $wp_filter[ $hookname ]->callbacks as $callbacks ) {
		foreach ( $callbacks as $callback ) {
			$owner = darkadmin_plugin_for_file( darkadmin_callback_file( $callback['function'] ?? null ), $active );
			if ( '' !== $owner ) {
				$owners[] = $owner;
			}
		}
	}

	return $owners;
}

/**
 * Plugins that own an admin page.
 *
 * A page belongs to the plugin whose callback renders it, and to the plugin
 * that owns its top-level menu. Pages registered by a PHP file inside a plugin
 * folder (page=my-plugin/admin.php) belong to that plugin.
 *
 * @param string      $slug        Page slug (the "page" query argument).
 * @param string|null $parent_slug Parent menu slug, or empty for top-level and hidden pages.
 * @param string[]    $active      Active plugin basenames.
 * @return string[]
 */
function darkadmin_admin_page_owners( string $slug, ?string $parent_slug, array $active ): array {
	if ( '' === $slug ) {
		return array();
	}

	$parent_slug = (string) $parent_slug;
	$owners      = darkadmin_page_hook_owners( get_plugin_page_hookname( $slug, $parent_slug ), $active );

	if ( '' !== $parent_slug && $parent_slug !== $slug ) {
		$owners = array_merge( $owners, darkadmin_page_hook_owners( get_plugin_page_hookname( $parent_slug, '' ), $active ) );
	}

	foreach ( array( $slug, $parent_slug ) as $file_slug ) {
		if ( str_ends_with( $file_slug, '.php' ) && str_contains( $file_slug, '/' ) ) {
			$owners[] = darkadmin_plugin_for_file( wp_normalize_path( WP_PLUGIN_DIR . '/' . $file_slug ), $active );
		}
	}

	return array_values( array_unique( array_filter( $owners ) ) );
}

/**
 * Finds the admin pages of every active plugin.
 *
 * Reads the pages registered through the admin menu API, so it must run after
 * the admin_menu action (for example while rendering a settings page).
 *
 * @return array<string, string[]> Plugin basename => page slugs.
 */
function darkadmin_detect_plugin_admin_pages(): array {
	global $_parent_pages;

	$active = darkadmin_active_plugin_basenames();
	$pages  = array();

	foreach ( (array) $_parent_pages as $slug => $parent ) {
		$slug = (string) $slug;
		foreach ( darkadmin_admin_page_owners( $slug, is_string( $parent ) ? $parent : '', $active ) as $owner ) {
			$pages[ $owner ][] = $slug;
		}
	}

	foreach ( $pages as $owner => $slugs ) {
		$slugs = array_values( array_unique( $slugs ) );
		sort( $slugs, SORT_STRING | SORT_FLAG_CASE );
		$pages[ $owner ] = $slugs;
	}

	return $pages;
}

/**
 * Whether the current admin page belongs to a plugin excluded from dark mode.
 *
 * @return bool
 */
function darkadmin_is_current_page_plugin_excluded(): bool {
	global $plugin_page, $_parent_pages;

	$excluded = darkadmin_excluded_plugins();
	if ( empty( $excluded ) || ! is_string( $plugin_page ) || '' === $plugin_page ) {
		return false;
	}

	$parent = is_array( $_parent_pages ) && isset( $_parent_pages[ $plugin_page ] ) && is_string( $_parent_pages[ $plugin_page ] )
		? $_parent_pages[ $plugin_page ]
		: '';

	$owners = darkadmin_admin_page_owners( $plugin_page, $parent, darkadmin_active_plugin_basenames() );

	return ! empty( array_intersect( $owners, $excluded ) );
}
