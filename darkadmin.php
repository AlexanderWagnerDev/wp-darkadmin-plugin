<?php
/**
 * Plugin Name: DarkAdmin - Dark Mode for Adminpanel
 * Plugin URI: https://wordpress.org/plugins/darkadmin-dark-mode-for-adminpanel/
 * Description: Simple, lightweight Dark Mode Plugin for the WordPress Admin Dashboard.
 * Version: 0.0.9
 * Requires at least: 6.3
 * Tested up to: 6.9
 * Requires PHP: 7.4
 * Author: AlexanderWagnerDev
 * Author URI: https://alexanderwagnerdev.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: darkadmin-dark-mode-for-adminpanel
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ADM_VERSION', '0.0.9' );
define( 'ADM_URL', plugin_dir_url( __FILE__ ) );
define( 'ADM_PATH', plugin_dir_path( __FILE__ ) );

require_once ADM_PATH . 'includes/defaults.php';
require_once ADM_PATH . 'includes/user-settings.php';
require_once ADM_PATH . 'includes/enqueue.php';
require_once ADM_PATH . 'includes/settings-page.php';

/**
 * Add a settings link in the Plugins list.
 */
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $actions ) {
	$url = admin_url( 'options-general.php?page=darkadmin' );
	$actions['settings'] = '<a href="' . esc_url( $url ) . '">' . __( 'Settings', 'darkadmin-dark-mode-for-adminpanel' ) . '</a>';
	return $actions;
} );

/**
 * Show an admin notice after settings are saved.
 */
add_action( 'admin_notices', function () {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
	if ( $page !== 'darkadmin' ) {
		return;
	}
	if ( empty( $_GET['settings-updated'] ) ) {
		return;
	}
	$enabled = (bool) get_option( 'adm_dark_mode_enabled', false );
	$msg = $enabled
		? __( '✓ Dark Mode is active. Settings have been saved.', 'darkadmin-dark-mode-for-adminpanel' )
		: __( '✓ Settings saved. Dark Mode is disabled.', 'darkadmin-dark-mode-for-adminpanel' );
	echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $msg ) . '</p></div>';
} );
