<?php
/**
 * Third-party plugin style registry and helpers.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Returns all built-in plugin style definitions.
 *
 * Each entry:
 *  - label         Human-readable plugin name.
 *  - description   Short note shown on the settings page.
 *  - css           Filename inside assets/css/plugins/.
 *  - is_installed  Callable that returns true when the plugin is installed and active.
 *
 * @return array<string, array{label: string, description: string, css: string, is_installed: callable(): bool}>
 */
function darkadmin_plugin_registry(): array {
	return array(
		'yoast'          => array(
			'label'        => 'Yoast SEO',
			'description'  => __( 'SEO settings, metaboxes and dashboard widgets.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'yoast.css',
			'is_installed' => static function (): bool {
				return defined( 'WPSEO_VERSION' );
			},
		),
		'wordfence'      => array(
			'label'        => 'Wordfence',
			'description'  => __( 'Firewall, scan and security options pages.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'wordfence.css',
			'is_installed' => static function (): bool {
				return defined( 'WORDFENCE_VERSION' );
			},
		),
		'updraftplus'    => array(
			'label'        => 'UpdraftPlus',
			'description'  => __( 'Backup, restore and migration screens.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'updraftplus.css',
			'is_installed' => static function (): bool {
				return defined( 'UPDRAFTPLUS_VERSION' ) || class_exists( 'UpdraftPlus', false );
			},
		),
		'wp-optimize'    => array(
			'label'        => 'WP-Optimize',
			'description'  => __( 'Database, cache and image optimization UI.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'wp-optimize.css',
			'is_installed' => static function (): bool {
				return defined( 'WPO_VERSION' ) || class_exists( 'WP_Optimize', false );
			},
		),
		'contact-form-7' => array(
			'label'        => 'Contact Form 7',
			'description'  => __( 'Form editor, tag generator, integration and list table.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'contact-form-7.css',
			'is_installed' => static function (): bool {
				return defined( 'WPCF7_VERSION' );
			},
		),
		'complianz'      => array(
			'label'        => 'Complianz',
			'description'  => __( 'Cookie consent wizard, dashboard, settings, banner editor and data tables.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'complianz.css',
			'is_installed' => static function (): bool {
				return defined( 'cmplz_free' ) || defined( 'cmplz_premium' ) || defined( 'CMPLZ_PLUGIN' );
			},
		),
		'newsletter'     => array(
			'label'        => 'Newsletter',
			'description'  => __( 'Subscription, forms, newsletters and settings tabs.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'newsletter.css',
			'is_installed' => static function (): bool {
				return defined( 'NEWSLETTER_VERSION' );
			},
		),
		'duplicator'     => array(
			'label'        => 'Duplicator',
			'description'  => __( 'Backups, schedules, storage and settings screens.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'duplicator.css',
			'is_installed' => static function (): bool {
				return defined( 'DUPLICATOR_VERSION' );
			},
		),
		'wordpress-ai'   => array(
			'label'        => __( 'WordPress AI & Connectors', 'darkadmin-dark-mode-for-adminpanel' ),
			'description'  => __( 'Connectors, AI settings, connector approvals and request logs.', 'darkadmin-dark-mode-for-adminpanel' ),
			'css'          => 'wordpress-ai.css',
			'is_installed' => static function (): bool {
				return function_exists( 'wp_options_connectors_wp_admin_render_page' )
					|| defined( 'WPAI_VERSION' );
			},
		),
	);
}

/**
 * Screen IDs for WordPress AI admin pages (Boot/WPDS and Tools screens).
 *
 * @return string[]
 */
function darkadmin_wordpress_ai_screen_ids(): array {
	return array(
		'options-connectors',
		'settings_page_ai-wp-admin',
		'tools_page_ai-connector-approval',
		'tools_page_ai-request-logs',
	);
}

/**
 * Whether the current admin screen is a WordPress AI page covered by wordpress-ai.css.
 *
 * @return bool
 */
function darkadmin_is_wordpress_ai_admin_screen(): bool {
	$screen = get_current_screen();
	if ( ! $screen instanceof WP_Screen ) {
		return false;
	}

	return in_array( $screen->id, darkadmin_wordpress_ai_screen_ids(), true );
}

/**
 * Enqueues WordPress AI dark styles on Connectors, AI settings and Tools screens.
 *
 * Loaded automatically on matching screens when dark mode is active so users
 * do not need to enable the optional plugin stylesheet separately.
 *
 * @return void
 */
function darkadmin_enqueue_wordpress_ai_styles(): void {
	if ( ! darkadmin_is_wordpress_ai_admin_screen() ) {
		return;
	}

	$path = DARKADMIN_PATH . 'assets/css/plugins/wordpress-ai.css';
	if ( ! is_readable( $path ) ) {
		return;
	}

	wp_enqueue_style(
		'darkadmin-wordpress-ai',
		DARKADMIN_URL . 'assets/css/plugins/wordpress-ai.css',
		array( 'darkadmin-darkmode', 'wp-components' ),
		DARKADMIN_VERSION
	);
}

/**
 * Back-compat alias for darkadmin_wordpress_ai_screen_ids().
 *
 * @return string[]
 */
function darkadmin_boot_wpds_screen_ids(): array {
	return darkadmin_wordpress_ai_screen_ids();
}

/**
 * Whether a registry slug refers to an installed, active plugin.
 *
 * @param string $slug Plugin style slug.
 * @return bool
 */
function darkadmin_plugin_is_installed( string $slug ): bool {
	$registry = darkadmin_plugin_registry();
	if ( ! isset( $registry[ $slug ] ) ) {
		return false;
	}
	$callback = $registry[ $slug ]['is_installed'];
	return is_callable( $callback ) && (bool) call_user_func( $callback );
}

/**
 * Returns plugin slugs whose dedicated dark styles are disabled in settings.
 *
 * @return string[]
 */
function darkadmin_plugins_disabled(): array {
	return (array) get_option( 'darkadmin_plugins', array() );
}

/**
 * Whether dedicated dark styles should load for a registry slug.
 *
 * @param string $slug Plugin style slug.
 * @return bool
 */
function darkadmin_plugin_styles_enabled( string $slug ): bool {
	if ( 'wordpress-ai' === $slug ) {
		return false;
	}
	if ( ! darkadmin_plugin_is_installed( $slug ) ) {
		return false;
	}

	return ! in_array( $slug, darkadmin_plugins_disabled(), true );
}

/**
 * Sanitize callback for darkadmin_plugins (stores disabled slugs).
 *
 * The settings form submits checked plugins as enabled; unchecked installed
 * plugins are persisted as disabled.
 *
 * @param mixed $value Raw input (enabled slugs from checkboxes).
 * @return string[]
 */
function darkadmin_sanitize_plugins( $value ): array {
	$allowed = array_keys( darkadmin_plugin_registry() );
	$enabled = array_map( 'sanitize_key', (array) $value );
	$enabled = array_values( array_filter( $enabled ) );
	$enabled = array_values( array_intersect( $enabled, $allowed ) );

	$disabled = array();
	foreach ( darkadmin_plugin_registry() as $slug => $meta ) {
		if ( 'wordpress-ai' === $slug ) {
			continue;
		}
		if ( ! darkadmin_plugin_is_installed( $slug ) ) {
			continue;
		}
		if ( ! in_array( $slug, $enabled, true ) ) {
			$disabled[] = $slug;
		}
	}

	return $disabled;
}

/**
 * Enqueues plugin stylesheets for installed plugins (opt-out via settings).
 *
 * WordPress AI styles are loaded separately on matching admin screens only.
 *
 * @return void
 */
function darkadmin_enqueue_plugin_styles(): void {
	foreach ( darkadmin_plugin_registry() as $slug => $meta ) {
		if ( 'wordpress-ai' === $slug ) {
			continue;
		}
		if ( ! darkadmin_plugin_styles_enabled( $slug ) ) {
			continue;
		}

		$path = DARKADMIN_PATH . 'assets/css/plugins/' . $meta['css'];
		if ( ! is_readable( $path ) ) {
			continue;
		}

		wp_enqueue_style(
			'darkadmin-plugin-' . $slug,
			DARKADMIN_URL . 'assets/css/plugins/' . $meta['css'],
			array( 'darkadmin-darkmode' ),
			DARKADMIN_VERSION
		);
	}
}
