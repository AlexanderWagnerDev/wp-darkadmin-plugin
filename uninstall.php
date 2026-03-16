<?php
/**
 * Fired when the plugin is uninstalled.
 * Removes all plugin options from wp_options.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'adm_dark_mode_enabled' );
delete_option( 'adm_auto_darken' );
delete_option( 'adm_colors' );
delete_option( 'adm_custom_css' );
delete_option( 'adm_allowed_users' );
