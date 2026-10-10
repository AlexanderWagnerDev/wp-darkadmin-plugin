<?php
/**
 * Plugin Name: DarkAdmin - Dark Mode for Adminpanel
 * Plugin URI: https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/
 * Description: Simple, lightweight Dark Mode Plugin for the WordPress Admin Dashboard.
 * Version: 0.4.0
 * Requires at least: 6.3
 * Tested up to: 7.1
 * Requires PHP: 8.0
 * Author: AlexanderWagnerDev
 * Author URI: https://alexanderwagnerdev.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: darkadmin-dark-mode-for-adminpanel
 * Domain Path: /languages
 *
 * @package DarkAdmin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'DARKADMIN_VERSION', '0.4.0' );
define( 'DARKADMIN_URL', plugin_dir_url( __FILE__ ) );
define( 'DARKADMIN_PATH', plugin_dir_path( __FILE__ ) );

/** Default preset slug for new installations. */
define( 'DARKADMIN_DEFAULT_PRESET', 'modern' );

require_once DARKADMIN_PATH . 'includes/defaults.php';
require_once DARKADMIN_PATH . 'includes/user-settings.php';
require_once DARKADMIN_PATH . 'includes/plugins.php';
require_once DARKADMIN_PATH . 'includes/enqueue.php';
require_once DARKADMIN_PATH . 'includes/settings-page.php';

/**
 * One-time migrations for existing installations (preset rename, defaults).
 */
add_action(
	'admin_init',
	function () {
		$db_version = (string) get_option( 'darkadmin_db_version', '0' );

		if ( version_compare( $db_version, '0.3.0', '<' ) ) {
			// Explicit default keeps "no stored row" detectable; the registered
			// default would otherwise be returned for single-argument calls.
			$preset = get_option( 'darkadmin_preset', false );

			if ( 'default' === $preset ) {
				update_option( 'darkadmin_preset', 'classic' );
			} elseif ( false === $preset && darkadmin_is_existing_install() ) {
				// Pre-0.3.0 installs without a saved preset were using the classic palette.
				update_option( 'darkadmin_preset', 'classic' );
			}

			$db_version = '0.3.0';
			update_option( 'darkadmin_db_version', $db_version );
		}

		if ( version_compare( $db_version, '0.3.1', '<' ) ) {
			// Former opt-in list becomes opt-out; default is styles on for all installed plugins.
			// Remove the legacy row instead of writing array() through update_option(), which
			// would run the registered darkadmin_sanitize_plugins() callback and treat the
			// empty value as "no plugin enabled", storing every installed plugin as disabled.
			delete_option( 'darkadmin_plugins' );
			update_option( 'darkadmin_db_version', '0.3.1' );
		}
	}
);

/**
 * Add a settings link in the Plugins list.
 */
add_filter(
	'plugin_action_links_' . plugin_basename( __FILE__ ),
	function ( $actions ) {
		$url                 = admin_url( 'options-general.php?page=darkadmin' );
		$actions['settings'] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'darkadmin-dark-mode-for-adminpanel' ) . '</a>';
		return $actions;
	}
);

/**
 * Transient key for the one-time "settings saved" admin notice.
 *
 * @param int $user_id User ID.
 * @return string
 */
function darkadmin_settings_saved_notice_key( int $user_id ): string {
	return 'darkadmin_settings_saved_' . $user_id;
}

/**
 * Queue a success notice after DarkAdmin options are saved via the settings page.
 *
 * Nonce verification happens in options.php via settings_fields() before this runs.
 *
 * @param string $option Option name.
 * @return void
 */
function darkadmin_queue_settings_saved_notice( string $option ): void {
	if ( ! str_starts_with( $option, 'darkadmin_' ) || 'darkadmin_db_version' === $option ) {
		return;
	}

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce is verified by options.php via settings_fields() before this hook runs.
	$option_page = isset( $_POST['option_page'] ) && is_string( $_POST['option_page'] ) ? sanitize_key( wp_unslash( $_POST['option_page'] ) ) : '';
	$action      = isset( $_POST['action'] ) && is_string( $_POST['action'] ) ? sanitize_key( wp_unslash( $_POST['action'] ) ) : '';
	// phpcs:enable WordPress.Security.NonceVerification.Missing
	if ( 'darkadmin_settings' !== $option_page || 'update' !== $action ) {
		return;
	}

	set_transient( darkadmin_settings_saved_notice_key( get_current_user_id() ), 1, MINUTE_IN_SECONDS );
}

add_action( 'updated_option', 'darkadmin_queue_settings_saved_notice' );
add_action( 'added_option', 'darkadmin_queue_settings_saved_notice' );

/**
 * Show an admin notice after settings are saved.
 */
add_action(
	'admin_notices',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen instanceof WP_Screen || 'settings_page_darkadmin' !== $screen->id ) {
			return;
		}

		$notice_key = darkadmin_settings_saved_notice_key( get_current_user_id() );
		if ( ! get_transient( $notice_key ) ) {
			return;
		}
		delete_transient( $notice_key );

		$enabled = (bool) get_option( 'darkadmin_dark_mode_enabled', false );
		$msg     = $enabled
			? __( '✓ Dark Mode is active. Settings have been saved.', 'darkadmin-dark-mode-for-adminpanel' )
			: __( '✓ Settings saved. Dark Mode is disabled.', 'darkadmin-dark-mode-for-adminpanel' );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
	}
);
