<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determines whether dark mode should be active for the current user.
 *
 * Logic:
 *  1. Global dark mode must be enabled (darkadmin_dark_mode_enabled).
 *  2. Administrators always get dark mode when it's globally enabled.
 *  3. For non-admins the darkadmin_user_access_mode option controls behaviour:
 *     - 'all'     : dark mode applies to everyone (default).
 *     - 'include' : dark mode only for users listed in darkadmin_allowed_users.
 *     - 'exclude' : dark mode for everyone EXCEPT users listed in darkadmin_allowed_users.
 *
 * @return bool
 */
function darkadmin_is_dark_mode_active(): bool {
	if ( ! get_option( 'darkadmin_dark_mode_enabled', false ) ) {
		return false;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}

	// Administrators always get dark mode when globally enabled.
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	$mode    = get_option( 'darkadmin_user_access_mode', 'all' );
	$listed  = array_map( 'strval', (array) get_option( 'darkadmin_allowed_users', [] ) );
	$user_id_str = (string) $user_id;

	switch ( $mode ) {
		case 'include':
			return in_array( $user_id_str, $listed, true );
		case 'exclude':
			return ! in_array( $user_id_str, $listed, true );
		case 'all':
		default:
			return true;
	}
}

/**
 * Returns all non-admin users for the user selection UI.
 *
 * @return WP_User[]
 */
function darkadmin_get_selectable_users(): array {
	return get_users( [
		'orderby'      => 'display_name',
		'order'        => 'ASC',
		'role__not_in' => [ 'administrator' ],
	] );
}
