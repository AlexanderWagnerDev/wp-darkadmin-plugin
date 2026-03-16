<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Determines whether dark mode should be active for the current user.
 *
 * Logic:
 *  1. Global dark mode must be enabled (adm_dark_mode_enabled).
 *  2. If the current user is a super-admin / administrator:
 *     - Dark mode is always active for them.
 *  3. For non-admins:
 *     - Check if their user ID is in the allowed-users list (adm_allowed_users).
 *     - If adm_allowed_users is empty, dark mode applies to ALL users (default).
 *
 * @return bool
 */
function adm_is_dark_mode_active(): bool {
	if ( ! get_option( 'adm_dark_mode_enabled', false ) ) {
		return false;
	}

	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}

	// Administrators always get dark mode when it's globally enabled.
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}

	$allowed = (array) get_option( 'adm_allowed_users', [] );

	// Empty list = applies to all users.
	if ( empty( $allowed ) ) {
		return true;
	}

	return in_array( (string) $user_id, array_map( 'strval', $allowed ), true );
}

/**
 * Returns all non-admin users for the user selection UI.
 *
 * @return WP_User[]
 */
function adm_get_selectable_users(): array {
	return get_users( [
		'orderby'      => 'display_name',
		'order'        => 'ASC',
		'exclude'      => [],
		'role__not_in' => [ 'administrator' ],
	] );
}
