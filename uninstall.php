<?php
/**
 * Fired when the plugin is uninstalled.
 * Removes all plugin options from wp_options.
 *
 * @package DarkAdmin
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'darkadmin_dark_mode_enabled' );
delete_option( 'darkadmin_auto_darken' );
delete_option( 'darkadmin_colors' );
delete_option( 'darkadmin_layout' );
delete_option( 'darkadmin_custom_css' );
delete_option( 'darkadmin_allowed_users' );
delete_option( 'darkadmin_user_access_mode' );
delete_option( 'darkadmin_preset' );
delete_option( 'darkadmin_excluded_pages' );
