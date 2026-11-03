<?php
/**
 * Fired when the plugin is uninstalled.
 * Removes all plugin options and the per-user settings notice transients.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Removes DarkAdmin options and notice transients for the current site.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 * @return void
 */
function darkadmin_uninstall_site_data(): void {
	global $wpdb;

	$options = array(
		'darkadmin_dark_mode_enabled',
		'darkadmin_auto_darken',
		'darkadmin_colors',
		'darkadmin_layout',
		'darkadmin_custom_css',
		'darkadmin_allowed_users',
		'darkadmin_user_access_mode',
		'darkadmin_preset',
		'darkadmin_excluded_pages',
		'darkadmin_plugins',
		'darkadmin_db_version',
	);

	foreach ( $options as $option ) {
		delete_option( $option );
	}

	$transient_like = $wpdb->esc_like( '_transient_darkadmin_settings_saved_' ) . '%';
	$timeout_like   = $wpdb->esc_like( '_transient_timeout_darkadmin_settings_saved_' ) . '%';

	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time uninstall cleanup for dynamically named per-user transients.
	$transient_names = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE %s OR option_name LIKE %s",
			$transient_like,
			$timeout_like
		)
	);

	foreach ( $transient_names as $transient_name ) {
		delete_option( $transient_name );
	}
}

if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		darkadmin_uninstall_site_data();
		restore_current_blog();
	}
} else {
	darkadmin_uninstall_site_data();
}
